<?php

use yii\db\Migration;

/**
 * Configurazione visibilità varianti nelle righe documento:
 * - mg_tipo_documento.mostra_varianti (mostra/nascondi colonne Taglia e Colore)
 * - mg_documento_riga.taglia / colore (valori denormalizzati dall'articolo)
 */
class m260922_000600_add_varianti_to_righe_documento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_tipo_documento', 'mostra_varianti')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'mostra_varianti',
                $this->boolean()->notNull()->defaultValue(0));
        }
        if (!$this->checkColumnExist('mg_documento_riga', 'taglia')) {
            $this->addColumn('{{%mg_documento_riga}}', 'taglia', $this->string(50)->null());
        }
        if (!$this->checkColumnExist('mg_documento_riga', 'colore')) {
            $this->addColumn('{{%mg_documento_riga}}', 'colore', $this->string(50)->null());
        }
    }

    public function safeDown()
    {
        foreach (['taglia', 'colore'] as $col) {
            if ($this->checkColumnExist('mg_documento_riga', $col)) {
                $this->dropColumn('{{%mg_documento_riga}}', $col);
            }
        }
        if ($this->checkColumnExist('mg_tipo_documento', 'mostra_varianti')) {
            $this->dropColumn('{{%mg_tipo_documento}}', 'mostra_varianti');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
