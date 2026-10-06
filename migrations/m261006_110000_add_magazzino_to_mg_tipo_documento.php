<?php

use yii\db\Migration;

/**
 * Configurazione del movimento di magazzino sul tipo documento:
 * - id_magazzino_partenza / id_magazzino_arrivo: magazzini coinvolti
 *   (solo partenza, oppure partenza e arrivo)
 * - segno_movimento: segno della giacenza (carico / scarico / nessuno)
 * - varia_impegnato: varia la quantità impegnata (aumenta / diminuisci / nessuno)
 * - varia_ordinato: varia la quantità ordinata (aumenta / diminuisci / nessuno)
 */
class m261006_110000_add_magazzino_to_mg_tipo_documento extends Migration
{
    private $campiMovimento = ['segno_movimento', 'varia_impegnato', 'varia_ordinato'];

    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_tipo_documento', 'id_magazzino_partenza')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'id_magazzino_partenza', $this->integer()->null());
            $this->createIndex('idx-mg_tipo_documento-id_magazzino_partenza', '{{%mg_tipo_documento}}', 'id_magazzino_partenza');
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'id_magazzino_arrivo')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'id_magazzino_arrivo', $this->integer()->null());
            $this->createIndex('idx-mg_tipo_documento-id_magazzino_arrivo', '{{%mg_tipo_documento}}', 'id_magazzino_arrivo');
        }
        foreach ($this->campiMovimento as $c) {
            if (!$this->checkColumnExist('mg_tipo_documento', $c)) {
                $this->addColumn('{{%mg_tipo_documento}}', $c, $this->string(10)->notNull()->defaultValue('nessuno'));
            }
        }
    }

    public function safeDown()
    {
        foreach (['id_magazzino_partenza', 'id_magazzino_arrivo'] as $c) {
            if ($this->checkColumnExist('mg_tipo_documento', $c)) {
                $this->dropColumn('{{%mg_tipo_documento}}', $c);
            }
        }
        foreach ($this->campiMovimento as $c) {
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
