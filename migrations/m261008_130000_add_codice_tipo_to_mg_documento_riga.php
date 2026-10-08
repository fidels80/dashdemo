<?php

use yii\db\Migration;

/**
 * Codice tipo documento denormalizzato sulle righe documento
 * (mg_documento_riga.codice_tipo): copiato da mg_documento.codice_tipo
 * a ogni salvataggio. Le righe esistenti vengono allineate dalle testate.
 */
class m261008_130000_add_codice_tipo_to_mg_documento_riga extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_documento_riga', 'codice_tipo')) {
            $this->addColumn('{{%mg_documento_riga}}', 'codice_tipo', $this->string(20)->null());
        }

        $this->execute(
            "UPDATE r SET r.codice_tipo = d.codice_tipo
             FROM [dbo].[mg_documento_riga] r
             INNER JOIN [dbo].[mg_documento] d ON d.id = r.id_documento
             WHERE r.codice_tipo IS NULL"
        );
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('mg_documento_riga', 'codice_tipo')) {
            $this->dropColumn('{{%mg_documento_riga}}', 'codice_tipo');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
