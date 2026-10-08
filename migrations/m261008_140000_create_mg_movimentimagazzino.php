<?php

use yii\db\Migration;

/**
 * Movimenti di magazzino generati dalle righe documento.
 *
 * Una riga per ogni riga documento salvata, con:
 * - id_documento_riga: riga documento di origine (cascade alla cancellazione);
 * - codice_articolo, qta, id_unita_misura, um: dati della riga;
 * - fattore: fattore di conversione preso da mg_articolo_um per l'articolo
 *   e l'unita di misura della riga (1 se non trovato);
 * - id_magazzino_partenza / id_magazzino_arrivo: magazzini della riga;
 * - segno_movimento, varia_impegnato, varia_ordinato: configurazione presa
 *   dal tipo documento;
 * - qta_movimento = qta * segno_movimento (carico +1, scarico -1, nessuno 0);
 * - qta_impegnato = qta * varia_impegnato (aumenta +1, diminuisci -1, nessuno 0);
 * - qta_ordinato = qta * varia_ordinato (aumenta +1, diminuisci -1, nessuno 0).
 */
class m261008_140000_create_mg_movimentimagazzino extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_movimentimagazzino')) {
            $this->createTable('{{%mg_movimentimagazzino}}', [
                'id' => $this->primaryKey(),
                'id_documento_riga' => $this->integer()->notNull(),
                'codice_articolo' => $this->string(25)->null(),
                'qta' => $this->decimal(18, 4)->null()->defaultValue(0),
                'id_unita_misura' => $this->integer()->null(),
                'um' => $this->string(10)->null(),
                'fattore' => $this->decimal(18, 4)->notNull()->defaultValue(1),
                'id_magazzino_partenza' => $this->integer()->null(),
                'id_magazzino_arrivo' => $this->integer()->null(),
                'segno_movimento' => $this->string(10)->notNull()->defaultValue('nessuno'),
                'varia_impegnato' => $this->string(10)->notNull()->defaultValue('nessuno'),
                'varia_ordinato' => $this->string(10)->notNull()->defaultValue('nessuno'),
                'qta_movimento' => $this->decimal(18, 4)->null()->defaultValue(0),
                'qta_impegnato' => $this->decimal(18, 4)->null()->defaultValue(0),
                'qta_ordinato' => $this->decimal(18, 4)->null()->defaultValue(0),
            ]);
            $this->createIndex('idx-mg_movimentimagazzino-riga', '{{%mg_movimentimagazzino}}', 'id_documento_riga');
        }

        $this->aggiungiForeignKeys();
    }

    public function safeDown()
    {
        $this->rimuoviForeignKeys();
        if ($this->checkTableExist('mg_movimentimagazzino')) {
            $this->dropTable('{{%mg_movimentimagazzino}}');
        }
    }

    private function aggiungiForeignKeys()
    {
        $fks = [
            ['fk-mg_movimentimagazzino-riga', 'id_documento_riga', 'mg_documento_riga', 'CASCADE'],
            ['fk-mg_movimentimagazzino-um', 'id_unita_misura', 'mg_unita_misura', 'NO ACTION'],
            ['fk-mg_movimentimagazzino-magazzino_partenza', 'id_magazzino_partenza', 'mg_magazzino', 'NO ACTION'],
            ['fk-mg_movimentimagazzino-magazzino_arrivo', 'id_magazzino_arrivo', 'mg_magazzino', 'NO ACTION'],
        ];

        foreach ($fks as $fk) {
            list($nome, $colonna, $tabella, $delete) = $fk;
            if ($this->checkForeignKeyExist('mg_movimentimagazzino', $nome)) {
                $this->dropForeignKey($nome, '{{%mg_movimentimagazzino}}');
            }
            $this->addForeignKey($nome, '{{%mg_movimentimagazzino}}', $colonna,
                '{{%' . $tabella . '}}', 'id', $delete, 'NO ACTION');
        }
    }

    private function rimuoviForeignKeys()
    {
        foreach (['fk-mg_movimentimagazzino-riga', 'fk-mg_movimentimagazzino-um',
            'fk-mg_movimentimagazzino-magazzino_partenza', 'fk-mg_movimentimagazzino-magazzino_arrivo'] as $nome) {
            if ($this->checkForeignKeyExist('mg_movimentimagazzino', $nome)) {
                $this->dropForeignKey($nome, '{{%mg_movimentimagazzino}}');
            }
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
