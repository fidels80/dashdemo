<?php

namespace app\commands;

use app\models\DashRecordLock;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Diagnostica e manutenzione dei blocchi "record in uso" (tabella dash_record_lock).
 *
 * Uso:
 *   php yii record-lock                              elenca tutti i blocchi
 *   php yii record-lock/index mg_documento           elenca i blocchi di una tabella
 *   php yii record-lock/clear                        elimina TUTTI i blocchi
 *   php yii record-lock/clear mg_documento 257       elimina il blocco di un singolo record
 */
class RecordLockController extends Controller
{
    public $defaultAction = 'index';

    /**
     * Elenca i blocchi presenti e il loro stato.
     */
    public function actionIndex($tabella = null)
    {
        $query = DashRecordLock::find()->orderBy(['scadenza' => SORT_ASC]);
        if ($tabella !== null) {
            $query->andWhere(['tabella' => $tabella]);
        }

        $rows = $query->all();
        if (empty($rows)) {
            Console::output('Nessun blocco presente.');
            return ExitCode::OK;
        }

        $now = time();
        Console::output(sprintf('%-30s %-12s %-32s %-20s %s', 'TABELLA', 'ID_RECORD', 'UTENTE', 'SCADENZA', 'STATO'));
        foreach ($rows as $r) {
            $scadenza = strtotime((string) $r->scadenza);
            $stato = ($scadenza !== false && $scadenza > $now) ? 'ATTIVO' : 'scaduto';
            Console::output(sprintf(
                '%-30s %-12s %-32s %-20s %s',
                $r->tabella,
                $r->id_record,
                $r->utente,
                $r->scadenza,
                $stato
            ));
        }
        Console::output('Totale: ' . count($rows));

        return ExitCode::OK;
    }

    /**
     * Elimina i blocchi (tutti, oppure di una tabella o di un singolo record).
     */
    public function actionClear($tabella = null, $id = null)
    {
        if ($tabella === null) {
            $eliminati = DashRecordLock::deleteAll();
        } else {
            $condizione = ['tabella' => $tabella];
            if ($id !== null) {
                $condizione['id_record'] = (string) $id;
            }
            $eliminati = DashRecordLock::deleteAll($condizione);
        }

        Console::output("Blocchi eliminati: {$eliminati}");
        return ExitCode::OK;
    }
}
