<?php

namespace app\components;

use app\models\ApiToken;
use app\models\DashApiEntita;

/**
 * Verifica dei permessi del token Bearer.
 *
 * La stringa "scopes" del token e' formata da voci separate da virgola:
 *
 *     entita:operazione[,entita:operazione...]
 *
 * con operazione fra read, insert, update, delete. Sono accettati anche:
 *
 *     *       tutte le entita' e tutte le operazioni (equivalente a "pieno")
 *     ro      sola lettura
 *     rw      inserimento e modifica (niente delete)
 *     pieno   tutte le operazioni sull'entita'
 *     solo    (oppure "no") esclude l'entita' anche se c'e' un jolly
 *
 * Esempi:
 *     documenti:ro,righe:rw,articoli:rw
 *     documenti:read,documenti:insert,articoli:*
 *     *                       -> ogni entita' attiva, ogni operazione
 *
 * Un token senza scope non puo' nulla (fail closed).
 */
class ApiAccess
{
    const OP_READ = 'read';
    const OP_INSERT = 'insert';
    const OP_UPDATE = 'update';
    const OP_DELETE = 'delete';

    /** Wildcard: ogni entita' attiva del registro. */
    const TUTTE = '*';

    /** @var array<string, string[]> cache per token, indicizzata da spl_object_hash */
    private static $cache = [];

    /** @var ApiToken|null token autenticato nella richiesta corrente */
    private static $token = null;

    /**
     * Operazioni valide.
     */
    public static function operazioni()
    {
        return [
            self::OP_READ => 'Lettura',
            self::OP_INSERT => 'Inserimento',
            self::OP_UPDATE => 'Modifica',
            self::OP_DELETE => 'Cancellazione',
        ];
    }

    /**
     * Traduce le scorciatoie nelle operazioni effettive.
     *
     * Accetta sia un elenco (dal form) sia una singola stringa con più
     * operazioni separate da "+", "|", "/" o spazi: è il formato prodotto da
     * ApiToken::composiScopes().
     *
     * @param string|string[] $spec
     * @return string[]
     */
    public static function normalizzaOperazioni($spec)
    {
        $out = [];
        foreach ((array) $spec as $s) {
            $s = strtolower(trim((string) $s));
            if ($s === '') {
                continue;
            }
            foreach (preg_split('/[+,|\/\s]+/', $s) as $pezzo) {
                switch ($pezzo) {
                    case self::TUTTE:
                    case 'pieno':
                    case 'full':
                    case 'all':
                        $out = array_keys(self::operazioni());
                        break;
                    case 'ro':
                    case 'r':
                    case self::OP_READ:
                        $out[] = self::OP_READ;
                        break;
                    case 'rw':
                    case 'w':
                        $out[] = self::OP_INSERT;
                        $out[] = self::OP_UPDATE;
                        break;
                    case self::OP_INSERT:
                        $out[] = self::OP_INSERT;
                        break;
                    case self::OP_UPDATE:
                        $out[] = self::OP_UPDATE;
                        break;
                    case self::OP_DELETE:
                    case 'del':
                        $out[] = self::OP_DELETE;
                        break;
                    case 'solo':
                    case 'no':
                    case 'none':
                        $out[] = 'solo';
                        break;
                    default:
                        break;
                }
            }
        }
        return array_values(array_unique($out));
    }

    /**
     * Espande la stringa degli scope del token.
     *
     * @param string $scopes
     * @return array<string, string[]> codice entita' => lista operazioni
     */
    public static function parse($scopes)
    {
        $scopes = trim((string) $scopes);
        if ($scopes === '') {
            return [];
        }

        $esplicite = [];
        $escludi = [];
        foreach (explode(',', $scopes) as $voce) {
            $voce = strtolower(trim($voce));
            if ($voce === '') {
                continue;
            }
            if ($voce === self::TUTTE || $voce === 'pieno' || $voce === 'full' || $voce === 'all') {
                // jolly: si espande più avanti su tutte le entita' attive
                continue;
            }
            $parts = explode(':', $voce, 2);
            $codice = trim($parts[0]);
            if ($codice === '') {
                continue;
            }
            if (!isset($parts[1]) || trim($parts[1]) === '') {
                // entita' senza operazione esplicita: sola lettura
                $esplicite[$codice] = [self::OP_READ];
                continue;
            }
            $ops = self::normalizzaOperazioni($parts[1]);
            if (in_array('solo', $ops, true)) {
                $escludi[] = $codice;
                continue;
            }
            if (!$ops) {
                // operazioni non riconosciute: nessun permesso (fail closed)
                $escludi[] = $codice;
                continue;
            }
            $esplicite[$codice] = array_values(array_unique(array_merge(
                isset($esplicite[$codice]) ? $esplicite[$codice] : [],
                $ops
            )));
        }

        $jolly = false;
        foreach (preg_split('/[,\s]+/', strtolower($scopes)) as $voce) {
            if ($voce === self::TUTTE || $voce === 'pieno' || $voce === 'full' || $voce === 'all') {
                $jolly = true;
                break;
            }
        }

        $out = $esplicite;
        if ($jolly) {
            foreach (DashApiEntita::attive() as $e) {
                $out[$e->codice] = array_keys(self::operazioni());
            }
        }

        foreach ($escludi as $codice) {
            unset($out[$codice]);
        }

        return $out;
    }

    /**
     * Scope espansi di un token, con cache per richiesta.
     *
     * @param ApiToken|null $token
     * @return array<string, string[]>
     */
    public static function scope(ApiToken $token = null)
    {
        if ($token === null) {
            $token = static::currentToken();
        }
        if ($token === null) {
            return [];
        }
        // Chiave per istanza e non per id: due token non ancora salvati hanno
        // entrambi id = 0 e non devono condividere i permessi calcolati.
        $id = spl_object_hash($token);
        if (!array_key_exists($id, self::$cache)) {
            self::$cache[$id] = self::parse($token->scopes);
        }
        return self::$cache[$id];
    }

    public static function clearCache()
    {
        self::$cache = [];
    }

    /**
     * Imposta il token corrente della richiesta (chiamato da ApiController).
     *
     * @param ApiToken|null $token
     */
    public static function setCurrentToken(ApiToken $token = null)
    {
        self::$token = $token;
        self::clearCache();
    }

    /**
     * @return ApiToken|null
     */
    public static function currentToken()
    {
        return self::$token;
    }

    /**
     * Entita' attive che il token puo' usare, nell'ordine del registro.
     *
     * @param ApiToken|null $token
     * @return DashApiEntita[]
     */
    public static function entitaConcesse(ApiToken $token = null)
    {
        $scope = self::scope($token);
        $out = [];
        foreach (DashApiEntita::attive() as $e) {
            if (!empty($scope[$e->codice])) {
                $out[] = $e;
            }
        }
        return $out;
    }

    /**
     * Verifica che il token possa eseguire l'operazione sull'entita'.
     *
     * @param string $codice codice (o alias) dell'entita'
     * @param string $operazione una delle costanti OP_*
     * @param ApiToken|null $token
     * @return bool
     */
    public static function can($codice, $operazione, ApiToken $token = null)
    {
        $scope = self::scope($token);
        if (!$scope) {
            return false;
        }

        $entita = DashApiEntita::findByName($codice);
        if ($entita === null || !$entita->attiva) {
            return false;
        }

        $ops = isset($scope[$entita->codice]) ? $scope[$entita->codice] : [];
        if (!in_array($operazione, $ops, true)) {
            return false;
        }

        // Le entita' marcate "sola lettura" non ammettono mai scritture,
        // nemmeno se il token ha ricevuto l'entita' con "*".
        if ($operazione !== self::OP_READ && $entita->sola_lettura) {
            return false;
        }

        return true;
    }

    /**
     * Verifica e, in caso di fallimento, prepara il messaggio d'errore.
     *
     * @param DashApiEntita $entita
     * @param string $operazione
     * @param ApiToken|null $token
     * @return string|null messaggio d'errore, null se autorizzato
     */
    public static function errore(DashApiEntita $entita, $operazione, ApiToken $token = null)
    {
        if (self::can($entita->codice, $operazione, $token)) {
            return null;
        }

        $nomeOp = isset(self::operazioni()[$operazione])
            ? mb_strtolower(self::operazioni()[$operazione])
            : $operazione;

        if (!$entita->attiva) {
            return sprintf('L\'entità "%s" non è disponibile su questo servizio.', $entita->descrizione);
        }
        if ($entita->sola_lettura && $operazione !== self::OP_READ) {
            return sprintf('L\'entità "%s" è in sola lettura: la %s non è consentita.',
                $entita->descrizione, $nomeOp);
        }
        return sprintf('Il token non ha il permesso di %s su "%s".',
            $nomeOp, $entita->descrizione);
    }
}
