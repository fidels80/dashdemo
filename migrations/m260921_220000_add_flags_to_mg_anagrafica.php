<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_anagrafica i flag indipendenti cliente/fornitore/agente
 * (un'anagrafica puo' essere tutte e tre contemporaneamente).
 */
class m260921_220000_add_flags_to_mg_anagrafica extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_anagrafica', 'is_cliente')) {
            $this->addColumn('{{%mg_anagrafica}}', 'is_cliente', $this->boolean()->notNull()->defaultValue(0));
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'is_fornitore')) {
            $this->addColumn('{{%mg_anagrafica}}', 'is_fornitore', $this->boolean()->notNull()->defaultValue(0));
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'is_agente')) {
            $this->addColumn('{{%mg_anagrafica}}', 'is_agente', $this->boolean()->notNull()->defaultValue(0));
        }

        // Migra i vecchi valori di "tipo" nei nuovi flag
        if ($this->checkColumnExist('mg_anagrafica', 'tipo')) {
            $this->execute("UPDATE {{%mg_anagrafica}} SET is_cliente = CASE WHEN tipo IN ('cliente','entrambi') THEN 1 ELSE 0 END");
            $this->execute("UPDATE {{%mg_anagrafica}} SET is_fornitore = CASE WHEN tipo IN ('fornitore','entrambi') THEN 1 ELSE 0 END");
        }
    }

    public function safeDown()
    {
        foreach (['is_cliente', 'is_fornitore', 'is_agente'] as $c) {
            if ($this->checkColumnExist('mg_anagrafica', $c)) {
                $this->dropColumn('{{%mg_anagrafica}}', $c);
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
