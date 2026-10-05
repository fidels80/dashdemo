<?php

namespace app\components;

use Yii;
use app\models\ApiToken;
use app\models\DashApiEntita;
use app\models\DashApiRel;

/**
 * Controlli di integrita' referenziale per il servizio REST (tabelle mg_*).
 *
 * Prima di cancellare un record verifica che non esistano righe che lo
 * puntano. Un documento con righe o scadenze non si cancella, cosi' come un
 * articolo citato in righe di documento.
 *
 * La rimozione dei figli e' possibile solo se:
 *   - la regola e' di tipo "figlio" con cascade = 1;
 *   - il client la chiede esplicitamente (body "cascade": true);
 *   - il token ha il permesso di cancellazione anche sull'entita' figlia;
 *   - il record non e' a sua volta referenziato da altri enti.
 *
 * I riferimenti non vengono mai rimossi in automatico: un articolo usato in
 * un documento resta bloccato anche se il token puo' cancellare i documenti.
 */
class ApiIntegrity
{
    /**
     * Conteggio delle righe che impediscono la cancellazione di un id.
     *
     * @param DashApiRel $regola
     * @param int[] $ids
     * @return int
     */
    private static function conteggio(DashApiRel $regola, array $ids)
    {
        return (int) (new \yii\db\Query())
            ->from($regola->tabella)
            ->where([$regola->colonna => $ids])
            ->count();
    }

    /**
     * Verifica se un record puo' essere cancellato, senza effettuarlo.
     *
     * @param DashApiEntita $entita
     * @param int $id
     * @param bool $cascade autorizzare la rimozione dei figli marcati cascade
     * @param ApiToken|null $token usato per i permessi sulle entita' figlie
     * @return string[] elenco motivi di blocco (vuoto = cancellabile)
     */
    public static function blocchiCancellazione(DashApiEntita $entita, $id, $cascade = false, ApiToken $token = null)
    {
        $motivi = [];

        if (!$entita->cancellabile) {
            $motivi[] = sprintf('L\'entità "%s" non è cancellabile tramite API.', $entita->descrizione);
        }

        $ids = [(int) $id];

        foreach (DashApiRel::perEntita($entita->codice) as $regola) {
            $n = self::conteggio($regola, $ids);
            if ($n === 0) {
                continue;
            }

            if ($regola->isFiglio() && $cascade && $regola->cascade) {
                $figlia = DashApiEntita::findOne(['tabella' => $regola->tabella]);
                if ($figlia !== null
                    && $figlia->attiva
                    && ApiAccess::can($figlia->codice, ApiAccess::OP_DELETE, $token)) {
                    continue;   // i figli verranno rimossi dopo il controllo finale
                }
            }

            $motivi[] = $regola->descriviVincolo($n);
        }

        return $motivi;
    }

    /**
     * Cancella i record verificando l'integrita' referenziale.
     *
     * @param DashApiEntita $entita
     * @param int[] $ids
     * @param bool $cascade autorizzare la rimozione dei figli marcati cascade
     * @param ApiToken|null $token
     * @return array{ok: bool, deleted: int, cascaded: array, skipped: array, error: string|null, vincoli: array}
     */
    public static function cancella(DashApiEntita $entita, array $ids, $cascade = false, ApiToken $token = null)
    {
        $resultato = [
            'ok' => false,
            'deleted' => 0,
            'cascaded' => [],
            'skipped' => [],
            'error' => null,
            'vincoli' => [],
        ];

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), function ($v) {
            return $v > 0;
        })));
        if (empty($ids)) {
            $resultato['error'] = 'Nessun id valido da eliminare.';
            return $resultato;
        }

        if (!$entita->cancellabile) {
            $resultato['error'] = sprintf('L\'entità "%s" non è cancellabile tramite API.', $entita->descrizione);
            return $resultato;
        }

        $model = $entita->getModel();
        if ($model === null) {
            $resultato['error'] = 'Modello non valido per l\'entità "' . $entita->descrizione . '".';
            return $resultato;
        }

        // Esistono? si distingue "non trovato" da "bloccato".
        $esistenti = $model::find()->select('id')->where(['id' => $ids])->column();
        $esistenti = array_map('intval', $esistenti);
        $mancanti = array_values(array_diff($ids, $esistenti));
        if (!empty($mancanti)) {
            $resultato['skipped'] = $mancanti;
        }

        if (empty($esistenti)) {
            $resultato['error'] = 'Record non trovato.';
            return $resultato;
        }

        // Blocco integrita': si raccoglie tutto prima di toccare il database.
        $bloccati = [];
        foreach ($esistenti as $id) {
            $motivi = self::blocchiCancellazione($entita, $id, $cascade, $token);
            if (!empty($motivi)) {
                $bloccati[$id] = $motivi;
            }
        }

        $eliminabili = array_values(array_diff($esistenti, array_keys($bloccati)));
        if (!empty($bloccati)) {
            $resultato['vincoli'] = $bloccati;
        }

        if (empty($eliminabili)) {
            $resultato['error'] = 'Nessun record eliminabile: tutti sono vincolati.';
            return $resultato;
        }

        $db = $model::getDb();
        $trans = $db->beginTransaction();
        try {
            // Figli prima del padre, in profondita' crescente (max 3 livelli).
            if ($cascade) {
                self::cascadifica($entita, $eliminabili, $token, $resultato['cascaded'], 0);
            }

            $cancellati = $model::deleteAll(['id' => $eliminabili]);
            if ((int) $cancellati !== count($eliminabili)) {
                throw new \yii\db\IntegrityException(
                    sprintf('Eliminati %d record su %d richiesti.', $cancellati, count($eliminabili))
                );
            }
            $resultato['deleted'] = (int) $cancellati;

            $trans->commit();
        } catch (\Exception $e) {
            $trans->rollBack();
            $resultato['error'] = 'Cancellazione non riuscita: ' . $e->getMessage();
            return $resultato;
        }

        $resultato['ok'] = true;
        return $resultato;
    }

    /**
     * Rimuove ricorsivamente i figli marcati come cascade, prima del padre.
     *
     * @param DashApiEntita $entita
     * @param int[] $idsPadre
     * @param ApiToken|null $token
     * @param array $registro accumulatore [["entita"=>..,"tabella"=>..,"cancellati"=>n], ...]
     * @param int $livello
     */
    private static function cascadifica(DashApiEntita $entita, array $idsPadre, ApiToken $token, array &$registro, $livello)
    {
        if ($livello >= 3) {
            return;   // guardia: evita cicli sui dati
        }

        foreach (DashApiRel::perEntita($entita->codice) as $regola) {
            if (!$regola->isFiglio() || !$regola->cascade) {
                continue;
            }
            $figlia = DashApiEntita::findOne(['tabella' => $regola->tabella]);
            if ($figlia === null || !$figlia->attiva || !$figlia->cancellabile) {
                continue;
            }
            if (!ApiAccess::can($figlia->codice, ApiAccess::OP_DELETE, $token)) {
                continue;
            }

            $figli = (new \yii\db\Query())
                ->select('id')
                ->from($regola->tabella)
                ->where([$regola->colonna => $idsPadre])
                ->column();
            $figli = array_map('intval', $figli);
            if (empty($figli)) {
                continue;
            }

            // Un figlio non puo' a sua volta essere vincolato da altri enti.
            $bloccati = [];
            foreach ($figli as $idFiglio) {
                $motivi = self::blocchiCancellazione($figlia, $idFiglio, true, $token);
                if (!empty($motivi)) {
                    $bloccati[$idFiglio] = $motivi;
                    continue;
                }
            }
            $eliminabili = array_values(array_diff($figli, array_keys($bloccati)));
            if (empty($eliminabili)) {
                continue;
            }
            foreach ($bloccati as $idFiglio => $motivi) {
                $registro[] = [
                    'entita' => $figlia->codice,
                    'tabella' => $regola->tabella,
                    'cancellati' => 0,
                    'bloccati' => [$idFiglio => $motivi],
                ];
            }

            self::cascadifica($figlia, $eliminabili, $token, $registro, $livello + 1);

            $n = Yii::$app->db->createCommand()
                ->delete($regola->tabella, ['id' => $eliminabili])
                ->execute();
            $registro[] = [
                'entita' => $figlia->codice,
                'tabella' => $regola->tabella,
                'cancellati' => (int) $n,
                'bloccati' => [],
            ];
        }
    }

    /**
     * Riepilogo dei vincoli registrati per un'entita', da mostrare nella UI.
     *
     * @param string $codice
     * @return array<int, array<string, mixed>>
     */
    public static function vincoliEntita($codice)
    {
        $out = [];
        foreach (DashApiRel::perEntita($codice) as $regola) {
            $out[] = [
                'tipo' => $regola->tipo,
                'tabella' => $regola->tabella,
                'colonna' => $regola->colonna,
                'etichetta' => $regola->etichetta,
                'cascade' => (bool) $regola->cascade,
                'entita' => $regola->entita,
            ];
        }
        return $out;
    }
}
