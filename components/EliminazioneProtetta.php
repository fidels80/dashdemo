<?php

namespace app\components;

/**
 * Controllo di congruità dei dati prima dell'eliminazione.
 *
 * I model che usano il trait implementano riferimentiBloccanti() restituendo
 * l'elenco delle relazioni logiche che impediscono la cancellazione. In quel
 * caso beforeDelete() annulla l'operazione e l'errore viene mostrato
 * all'utente (via SweetAlert) dal controller.
 */
trait EliminazioneProtetta
{
    /**
     * Elenco (etichette) delle relazioni che bloccano l'eliminazione.
     *
     * @return string[]
     */
    abstract protected function riferimentiBloccanti();

    /**
     * True se il record puo' essere eliminato. Se false, $motivo contiene
     * la spiegazione da mostrare all'utente.
     *
     * @param string|null $motivo
     * @return bool
     */
    public function puoEliminare(&$motivo = null)
    {
        $motivo = null;
        $rif = $this->riferimentiBloccanti();
        if (!empty($rif)) {
            $motivo = 'Impossibile eliminare: il record è utilizzato in ' . implode(', ', $rif) . '.';
            return false;
        }
        return true;
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $motivo = null;
        return $this->puoEliminare($motivo);
    }

    /**
     * Helper: true se esiste almeno una riga nella tabella $table con $condition.
     */
    protected function esisteRiferimento($table, $condition)
    {
        try {
            return (new \yii\db\Query())->from($table)->where($condition)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}
