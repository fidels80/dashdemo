<?php

use yii\db\Migration;

/**
 * Opzioni operative sul tipo documento:
 * - preleva_rapportini: abilita il prelievo rapportini nella form documento
 * - crea_articoli: abilita la creazione rapida articoli
 * - crea_anagrafiche: abilita la creazione rapida clienti/fornitori
 * - mostra_matrice: abilita la matrice taglie
 */
class m261001_110000_add_opzioni_to_mg_tipo_documento extends Migration
{
    private $flags = ['preleva_rapportini', 'crea_articoli', 'crea_anagrafiche', 'mostra_matrice'];

    public function safeUp()
    {
        foreach ($this->flags as $c) {
            if (!$this->checkColumnExist('mg_tipo_documento', $c)) {
                $this->addColumn('{{%mg_tipo_documento}}', $c, $this->boolean()->notNull()->defaultValue(1));
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
