<?php

namespace app\components;

use app\models\DashRecordLock;
use Yii;
use yii\db\Expression;
use yii\db\Query;

/**
 * Gestione dei blocchi temporanei "record in uso" tra utenti.
 *
 * Il blocco e' identificato dalla coppia (tabella, id_record) e registra
 * l'utente che sta modificando il record e la scadenza. Alla scadenza il
 * blocco decade automaticamente e il record torna disponibile.
 *
 * NOTA: le date vengono gestite interamente lato SQL Server
 * (GETDATE / DATEADD) e mai come stringhe PHP, per essere indipendenti
 * dal DATEFORMAT della connessione (che su questo server e' italiano/dmy).
 */
class RecordLock
{
    /**
     * Durata predefinita di un blocco, in secondi (param recordLockTimeout).
     */
    public static function defaultTimeout()
    {
        return (int) (Yii::$app->params['recordLockTimeout'] ?? 900);
    }

    /**
     * Username dell'utente autenticato corrente (null se ospite).
     */
    public static function currentUser()
    {
        $identity = Yii::$app->user->identity;
        return $identity !== null ? (string) $identity->username : null;
    }

    /**
     * Riga di blocco per il record indicato (o null).
     */
    public static function find($tabella, $idRecord)
    {
        return DashRecordLock::findOne([
            'tabella' => (string) $tabella,
            'id_record' => (string) $idRecord,
        ]);
    }

    /**
     * True se il record e' bloccato da un utente diverso da quello corrente.
     */
    public static function isHeldByOther($tabella, $idRecord)
    {
        $holder = static::holder($tabella, $idRecord);
        return $holder !== null && $holder !== static::currentUser();
    }

    /**
     * Username dell'utente che detiene un blocco attivo (o null se libero/scaduto).
     * La scadenza e' valutata dalla stessa query con GETDATE().
     */
    public static function holder($tabella, $idRecord)
    {
        return (new Query())
            ->select(['utente'])
            ->from('{{%dash_record_lock}}')
            ->where(['tabella' => (string) $tabella, 'id_record' => (string) $idRecord])
            ->andWhere(['>', 'scadenza', new Expression('GETDATE()')])
            ->scalar();
    }

    /**
     * Prova ad acquisire (o rinnovare) il blocco del record per l'utente corrente.
     *
     * @return bool true se il blocco e' dell'utente corrente, false se e' in uso da altri
     */
    public static function acquire($tabella, $idRecord, $timeout = null)
    {
        if (Yii::$app->user->isGuest) {
            return true;
        }

        $tabella = (string) $tabella;
        $idRecord = (string) $idRecord;
        $timeout = $timeout !== null ? (int) $timeout : static::defaultTimeout();
        $utente = static::currentUser();
        $db = Yii::$app->db;

        $dati = [
            'utente' => $utente,
            'utente_id' => Yii::$app->user->id,
            'creato_il' => new Expression('GETDATE()'),
            'scadenza' => new Expression('DATEADD(SECOND, ' . $timeout . ', GETDATE())'),
        ];

        // 1) Rinnova un blocco esistente se e' dell'utente corrente o e' scaduto.
        $aggiornate = $db->createCommand()->update(
            '{{%dash_record_lock}}',
            $dati,
            [
                'and',
                ['tabella' => $tabella, 'id_record' => $idRecord],
                ['or',
                    ['utente' => $utente],
                    ['<=', 'scadenza', new Expression('GETDATE()')],
                ],
            ]
        )->execute();

        if ($aggiornate > 0) {
            return true;
        }

        // 2) Nessuna riga aggiornata: o e' di un altro utente (non scaduta), o non esiste.
        $esiste = (new Query())
            ->from('{{%dash_record_lock}}')
            ->where(['tabella' => $tabella, 'id_record' => $idRecord])
            ->exists($db);

        if ($esiste) {
            return false;
        }

        // 3) Inserimento: il record era libero.
        try {
            $db->createCommand()->insert('{{%dash_record_lock}}', $dati)->execute();
            return true;
        } catch (\Exception $e) {
            // Un altro utente ha inserito il blocco nello stesso istante.
            return !static::isHeldByOther($tabella, $idRecord);
        }
    }

    /**
     * Rilascia il blocco se appartiene all'utente corrente.
     */
    public static function release($tabella, $idRecord)
    {
        $utente = static::currentUser();
        if ($utente === null) {
            return;
        }
        DashRecordLock::deleteAll([
            'tabella' => (string) $tabella,
            'id_record' => (string) $idRecord,
            'utente' => $utente,
        ]);
    }

    /**
     * Rilascia tutti i blocchi detenuti da un utente (es. al logout).
     */
    public static function releaseAllForUser($utente)
    {
        if ($utente === null || $utente === '') {
            return;
        }
        DashRecordLock::deleteAll(['utente' => (string) $utente]);
    }

    /**
     * Elimina i blocchi scaduti (manutenzione).
     */
    public static function releaseExpired()
    {
        DashRecordLock::deleteAll(['<=', 'scadenza', new Expression('GETDATE()')]);
    }
}
