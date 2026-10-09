<?php

use yii\db\Migration;

/**
 * Funzionalita' gestione lotti sul tipo documento.
 * - gestione_lotti: abilita nelle righe documento la gestione dei lotti
 *   (codice lotto, descrizione, data scadenza, nota), con selezione dei
 *   lotti esistenti dell'articolo e creazione rapida di nuovi lotti.
 *
 * Il campo corrispondente compare nella form documento solo se il flag e'
 * attivo sul tipo documento selezionato.
 */
class m261009_120000_add_gestione_lotti_to_mg_tipo_documento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_tipo_documento', 'gestione_lotti')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'gestione_lotti', $this->boolean()->notNull()->defaultValue(0));
        }
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('mg_tipo_documento', 'gestione_lotti')) {
            $this->dropColumn('{{%mg_tipo_documento}}', 'gestione_lotti');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
