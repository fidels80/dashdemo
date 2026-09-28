<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_anagrafica:
 * - perc_provvigione     (percentuale provvigione, usata per gli agenti)
 * - id_metodo_pagamento  (metodo di pagamento predefinito: cliente, fornitore o agente)
 */
class m260921_230200_add_pagamento_to_mg_anagrafica extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_anagrafica', 'perc_provvigione')) {
            $this->addColumn('{{%mg_anagrafica}}', 'perc_provvigione',
                $this->decimal(9, 2)->notNull()->defaultValue(0));
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'id_metodo_pagamento')) {
            $this->addColumn('{{%mg_anagrafica}}', 'id_metodo_pagamento', $this->integer()->null());
            $this->createIndex('idx-mg_anagrafica-metodo', '{{%mg_anagrafica}}', 'id_metodo_pagamento');
        }
        if (!$this->checkForeignKeyExist('mg_anagrafica', 'fk-mg_anagrafica-metodo')) {
            $this->addForeignKey('fk-mg_anagrafica-metodo', '{{%mg_anagrafica}}', 'id_metodo_pagamento',
                '{{%mg_metodo_pagamento}}', 'id', 'SET NULL', 'SET NULL');
        }
    }

    public function safeDown()
    {
        if ($this->checkForeignKeyExist('mg_anagrafica', 'fk-mg_anagrafica-metodo')) {
            $this->dropForeignKey('fk-mg_anagrafica-metodo', '{{%mg_anagrafica}}');
        }
        if ($this->checkColumnExist('mg_anagrafica', 'id_metodo_pagamento')) {
            $this->dropColumn('{{%mg_anagrafica}}', 'id_metodo_pagamento');
        }
        if ($this->checkColumnExist('mg_anagrafica', 'perc_provvigione')) {
            $this->dropColumn('{{%mg_anagrafica}}', 'perc_provvigione');
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
