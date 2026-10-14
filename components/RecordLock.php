<?php

namespace app\components;

use app\models\DashRecordLock;
use Yii;

/**
 * Gestione dei blocchi temporanei "record in uso" tra utenti.
 *
 * Il blocco e' identificato dalla coppia (tabella, id_record) e registra
 * l'utente che sta modificando il record e la scadenza. Alla scadenza il
 * blocco decade automaticamente e il record torna disponibile.
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
     * Verifica se un blocco e' scaduto.
     */
    public static function isExpired($lock, $now = null)
    {
        $now = $now === null ? time() : $now;
        $scadenza = strtotime((string) $lock->scadenza);
        return $scadenza === false || $scadenza <= $now;
    }

    /**
     * True se il record e' bloccato da un utente diverso da quello corrente.
     */
    public static function isHeldByOther($tabella, $idRecord)
    {
        $lock = static::find($tabella, $idRecord);
        return $lock !== null
            && $lock->utente !== static::currentUser()
            && !static::isExpired($lock);
    }

    /**
     * Username dell'utente che detiene un blocco attivo (o null se libero/scaduto).
     */
    public static function holder($tabella, $idRecord)
    {
        $lock = static::find($tabella, $idRecord);
        if ($lock === null || static::isExpired($lock)) {
            return null;
        }
        return $lock->utente;
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
        $now = time();
        $timeout = $timeout !== null ? (int) $timeout : static::defaultTimeout();
        $utente = static::currentUser();
        $scadenza = date('Y-m-d H:i:s', $now + $timeout);

        $lock = static::find($tabella, $idRecord);

        if ($lock === null) {
            $lock = new DashRecordLock();
            $lock->tabella = $tabella;
            $lock->id_record = $idRecord;
            $lock->utente = $utente;
            $lock->utente_id = Yii::$app->user->id;
            $lock->creato_il = date('Y-m-d H:i:s', $now);
            $lock->scadenza = $scadenza;
            try {
                return (bool) $lock->save(false);
            } catch (\Exception $e) {
                // Un altro utente ha inserito il blocco nello stesso istante.
                $lock = static::find($tabella, $idRecord);
                return $lock !== null && $lock->utente === $utente;
            }
        }

        if ($lock->utente !== $utente && !static::isExpired($lock, $now)) {
            return false;
        }

        $lock->utente = $utente;
        $lock->utente_id = Yii::$app->user->id;
        $lock->scadenza = $scadenza;
        return (bool) $lock->save(false);
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
        DashRecordLock::deleteAll(['<', 'scadenza', date('Y-m-d H:i:s')]);
    }
}
