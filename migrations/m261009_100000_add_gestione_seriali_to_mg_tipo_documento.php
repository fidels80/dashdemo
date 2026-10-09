<?php

use yii\db\Migration;

/**
 * Funzionalita' seriali/matricole e data consegna sul tipo documento.
 * - gestione_seriali: abilita nelle righe documento la gestione dei numeri
 *   di serie / matricole (una per pezzo).
 * - gestione_data_consegna: abilita nelle righe documento la gestione delle
 *   date di consegna, anche diverse all'interno della stessa riga.
 *
 * I campi corrispondenti compaiono nella form documento solo se il flag e'
 * attivo sul tipo documento selezionato.
 */
class m261009_100000_add_gestione_seriali_to_mg_tipo_documento extends Migration
{
    private $flags = ['gestione_seriali', 'gestione_data_consegna'];

    public function safeUp()
    {
        foreach ($this->flags as $c) {
            if (!$this->checkColumnExist('mg_tipo_documento', $c)) {
                $this->addColumn('{{%mg_tipo_documento}}', $c, $this->boolean()->notNull()->defaultValue(0));
            }
        }
    }

    public function safeDown()
    {
        foreach ($this->flags as $c) {
            if ($this->checkColumnExist('mg_tipo_documento', $c)) {
                $this->dropColumn('{{%mg_tipo_documento}}', $c);
            }
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
