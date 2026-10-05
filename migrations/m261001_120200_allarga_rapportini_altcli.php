<?php

use yii\db\Migration;

/**
 * rapportini.altcli era CHAR(7) (dimensionata sui vecchi codici anacli):
 * si allarga per contenere mg_anagrafica.codice (fino a 20 caratteri),
 * come già fatto per cd_cli.
 */
class m261001_120200_allarga_rapportini_altcli extends Migration
{
    public function safeUp()
    {
        $this->allargaAltcli();
    }

    public function safeDown()
    {
        // Nessuna operazione: il tipo precedente non va ripristinato.
    }

    private function allargaAltcli()
    {
        $schema = Yii::$app->db->schema->getTableSchema('rapportini', true);
        if ($schema === null) {
            return;
        }
        $col = $schema->getColumn('altcli');
        if ($col === null || strtoupper($col->type) !== 'CHAR') {
            return;
        }
        $null = $col->allowNull ? 'NULL' : 'NOT NULL';
        Yii::$app->db->createCommand("ALTER TABLE rapportini ALTER COLUMN altcli NVARCHAR(20) {$null}")->execute();
    }
}
