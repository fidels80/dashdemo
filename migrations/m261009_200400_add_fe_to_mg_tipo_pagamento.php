<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_tipo_pagamento le condizioni di pagamento SDI
 * (TP01 a rate / TP02 completo / TP03 anticipo), usate nella
 * fattura elettronica dei documenti.
 */
class m261009_200400_add_fe_to_mg_tipo_pagamento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_tipo_pagamento', 'fe_condizioni_pagamento')) {
            $this->addColumn('{{%mg_tipo_pagamento}}', 'fe_condizioni_pagamento', $this->string(4)->null());
        }

        $this->execute("UPDATE {{%mg_tipo_pagamento}} SET fe_condizioni_pagamento='TP02'
            WHERE fe_condizioni_pagamento IS NULL OR fe_condizioni_pagamento=''");
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('mg_tipo_pagamento', 'fe_condizioni_pagamento')) {
            $this->dropColumn('{{%mg_tipo_pagamento}}', 'fe_condizioni_pagamento');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
