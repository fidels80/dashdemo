<?php

use yii\db\Migration;

/**
 * Dettaglio seriali / date consegna delle righe documento.
 *
 * Una riga per ogni pezzo (o gruppo di pezzi) della riga documento:
 * - id_documento_riga: riga documento di origine (cascade alla cancellazione);
 * - seriale: numero di serie / matricola del pezzo (opzionale);
 * - data_consegna: data di consegna del pezzo (opzionale);
 * - qta: quantita' riferita alla data (1 se si gestiscono i seriali);
 * - ordine: ordinamento del dettaglio nella riga.
 *
 * La tabella serve sia la gestione seriali sia la gestione date consegna:
 * i campi effettivamente compilati dipendono dai flag del tipo documento.
 */
class m261009_110000_create_mg_documento_riga_dettaglio extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_documento_riga_dettaglio')) {
            $this->createTable('{{%mg_documento_riga_dettaglio}}', [
                'id' => $this->primaryKey(),
                'id_documento_riga' => $this->integer()->notNull(),
                'seriale' => $this->string(100)->null(),
                'data_consegna' => $this->date()->null(),
                'qta' => $this->decimal(18, 4)->null()->defaultValue(1),
                'ordine' => $this->integer()->null()->defaultValue(0),
            ]);
            $this->createIndex('idx-mg_documento_riga_dettaglio-riga', '{{%mg_documento_riga_dettaglio}}', 'id_documento_riga');
        }

        $fk = 'fk-mg_documento_riga_dettaglio-riga';
        if ($this->checkForeignKeyExist('mg_documento_riga_dettaglio', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_documento_riga_dettaglio}}');
        }
        $this->addForeignKey($fk, '{{%mg_documento_riga_dettaglio}}', 'id_documento_riga',
            '{{%mg_documento_riga}}', 'id', 'CASCADE', 'NO ACTION');
    }

    public function safeDown()
    {
        $fk = 'fk-mg_documento_riga_dettaglio-riga';
        if ($this->checkForeignKeyExist('mg_documento_riga_dettaglio', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_documento_riga_dettaglio}}');
        }
        if ($this->checkTableExist('mg_documento_riga_dettaglio')) {
            $this->dropTable('{{%mg_documento_riga_dettaglio}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
