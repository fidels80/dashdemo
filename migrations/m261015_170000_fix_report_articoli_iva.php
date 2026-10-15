<?php

use yii\db\Migration;
use app\models\DashReport;

/**
 * Imposta il flag "totale: false" sulla colonna IVA % del report articoli:
 * la percentuale non va sommata nella riga dei totali.
 */
class m261015_170000_fix_report_articoli_iva extends Migration
{
    public function safeUp()
    {
        $this->setFlag(false);
    }

    public function safeDown()
    {
        $this->setFlag(true);
    }

    private function setFlag($totale)
    {
        if (!$this->tableExists('dash_report')) {
            return;
        }

        $report = DashReport::findOne(['codice' => 'rep_articoli']);
        if (!$report) {
            return;
        }

        $def = $report->getDefinizioneArray();
        if (empty($def['colonne']) || !is_array($def['colonne'])) {
            return;
        }

        foreach ($def['colonne'] as &$c) {
            if (($c['alias'] ?? '') === 'iva') {
                $c['totale'] = $totale;
            }
        }
        unset($c);

        $report->definizione = json_encode($def, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $report->updated_at = date('Ymd H:i:s');
        $report->save(false);
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
