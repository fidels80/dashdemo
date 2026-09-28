<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aliquote IVA su articoli e soggetti:
 * - mg_articolo.id_iva_vendita / id_iva_acquisto
 * - mg_anagrafica.id_aliquota_iva (predefinita del soggetto)
 * Migra i vecchi valori di mg_articolo.iva in aliquote tabellate.
 */
class m260921_230800_add_iva_to_mg extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_articolo', 'id_iva_vendita')) {
            $this->addColumn('{{%mg_articolo}}', 'id_iva_vendita', $this->integer()->null());
            $this->createIndex('idx-mg_articolo-iva_vendita', '{{%mg_articolo}}', 'id_iva_vendita');
        }
        if (!$this->checkColumnExist('mg_articolo', 'id_iva_acquisto')) {
            $this->addColumn('{{%mg_articolo}}', 'id_iva_acquisto', $this->integer()->null());
            $this->createIndex('idx-mg_articolo-iva_acquisto', '{{%mg_articolo}}', 'id_iva_acquisto');
        }
        if (!$this->checkForeignKeyExist('mg_articolo', 'fk-mg_articolo-iva_vendita')) {
            $this->addForeignKey('fk-mg_articolo-iva_vendita', '{{%mg_articolo}}', 'id_iva_vendita',
                '{{%mg_aliquota_iva}}', 'id', 'NO ACTION', 'NO ACTION');
        }
        if (!$this->checkForeignKeyExist('mg_articolo', 'fk-mg_articolo-iva_acquisto')) {
            $this->addForeignKey('fk-mg_articolo-iva_acquisto', '{{%mg_articolo}}', 'id_iva_acquisto',
                '{{%mg_aliquota_iva}}', 'id', 'NO ACTION', 'NO ACTION');
        }

        if (!$this->checkColumnExist('mg_anagrafica', 'id_aliquota_iva')) {
            $this->addColumn('{{%mg_anagrafica}}', 'id_aliquota_iva', $this->integer()->null());
            $this->createIndex('idx-mg_anagrafica-iva', '{{%mg_anagrafica}}', 'id_aliquota_iva');
        }
        if (!$this->checkForeignKeyExist('mg_anagrafica', 'fk-mg_anagrafica-iva')) {
            $this->addForeignKey('fk-mg_anagrafica-iva', '{{%mg_anagrafica}}', 'id_aliquota_iva',
                '{{%mg_aliquota_iva}}', 'id', 'NO ACTION', 'NO ACTION');
        }

        $this->migraVecchiaIva();
    }

    public function safeDown()
    {
        if ($this->checkForeignKeyExist('mg_anagrafica', 'fk-mg_anagrafica-iva')) {
            $this->dropForeignKey('fk-mg_anagrafica-iva', '{{%mg_anagrafica}}');
        }
        if ($this->checkColumnExist('mg_anagrafica', 'id_aliquota_iva')) {
            $this->dropColumn('{{%mg_anagrafica}}', 'id_aliquota_iva');
        }
        foreach (['fk-mg_articolo-iva_vendita' => 'id_iva_vendita', 'fk-mg_articolo-iva_acquisto' => 'id_iva_acquisto'] as $fk => $col) {
            if ($this->checkForeignKeyExist('mg_articolo', $fk)) {
                $this->dropForeignKey($fk, '{{%mg_articolo}}');
            }
            if ($this->checkColumnExist('mg_articolo', $col)) {
                $this->dropColumn('{{%mg_articolo}}', $col);
            }
        }
    }

    /**
     * Converte i valori di mg_articolo.iva in righe di mg_aliquota_iva e li assegna
     * come aliquota di vendita/acquisto predefinita dell'articolo.
     */
    private function migraVecchiaIva()
    {
        if (!$this->checkColumnExist('mg_articolo', 'iva')
            || !$this->checkColumnExist('mg_articolo', 'id_iva_vendita')) {
            return;
        }

        $valori = (new Query())->select(['iva'])->distinct()->from('{{%mg_articolo}}')->column();
        foreach ($valori as $v) {
            $perc = (float) $v;
            $codice = str_pad((string) (int) round($perc), 2, '0', STR_PAD_LEFT);

            $id = (new Query())->select('id')->from('{{%mg_aliquota_iva}}')->where(['codice' => $codice])->scalar();
            if (!$id) {
                $this->insert('{{%mg_aliquota_iva}}', [
                    'codice' => $codice,
                    'descrizione' => 'IVA ' . number_format($perc, 0, ',', '.') . '%',
                    'percentuale' => $perc,
                    'attivo' => 1,
                ]);
                $id = (new Query())->select('id')->from('{{%mg_aliquota_iva}}')
                    ->where(['codice' => $codice])->scalar();
            }

            $this->update('{{%mg_articolo}}',
                ['id_iva_vendita' => $id, 'id_iva_acquisto' => $id],
                ['iva' => $v]);
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
