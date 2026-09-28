<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_tipo_documento la destinazione del documento:
 * 'cliente' oppure 'fornitore'. In base a questo valore, nella form del
 * documento l'anagrafica intestataria viene filtrata (soli clienti o soli fornitori).
 */
class m260921_230000_add_destinazione_to_mg_tipo_documento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_tipo_documento', 'destinazione')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'destinazione',
                $this->string(20)->notNull()->defaultValue('cliente'));
        }
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('mg_tipo_documento', 'destinazione')) {
            $this->dropColumn('{{%mg_tipo_documento}}', 'destinazione');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
