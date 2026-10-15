<?php

use yii\db\Migration;

/**
 * Aggiunge la colonna `icona` a dash_report: icona Font Awesome opzionale
 * mostrata nel catalogo report e nel designer.
 */
class m261015_170000_add_report_icona extends Migration
{
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%dash_report}}');
        if ($table && !isset($table->columns['icona'])) {
            $this->addColumn('{{%dash_report}}', 'icona', $this->string(50)->null());
        }
    }

    public function safeDown()
    {
        $table = Yii::$app->db->getTableSchema('{{%dash_report}}');
        if ($table && isset($table->columns['icona'])) {
            $this->dropColumn('{{%dash_report}}', 'icona');
        }
    }
}
