<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_metodo_pagamento la modalità di pagamento SDI
 * (MP01 contanti, MP05 bonifico, MP12 RIBA, ...), usata nella
 * fattura elettronica dei documenti.
 */
class m261009_200500_add_fe_to_mg_metodo_pagamento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_metodo_pagamento', 'fe_modalita_pagamento')) {
            $this->addColumn('{{%mg_metodo_pagamento}}', 'fe_modalita_pagamento', $this->string(4)->null());
        }

        $this->execute("UPDATE {{%mg_metodo_pagamento}} SET fe_modalita_pagamento='MP01' WHERE descrizione LIKE '%contant%'");
        $this->execute("UPDATE {{%mg_metodo_pagamento}} SET fe_modalita_pagamento='MP12' WHERE descrizione LIKE 'ri.ba%' OR codice LIKE 'RIB%'");
        $this->execute("UPDATE {{%mg_metodo_pagamento}} SET fe_modalita_pagamento='MP02' WHERE descrizione LIKE '%assegno%'");
        $this->execute("UPDATE {{%mg_metodo_pagamento}} SET fe_modalita_pagamento='MP08' WHERE descrizione LIKE '%carta%'");
        $this->execute("UPDATE {{%mg_metodo_pagamento}} SET fe_modalita_pagamento='MP05' WHERE descrizione LIKE '%bonifico%'");
        $this->execute("UPDATE {{%mg_metodo_pagamento}} SET fe_modalita_pagamento='MP05'
            WHERE fe_modalita_pagamento IS NULL OR fe_modalita_pagamento=''");
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('mg_metodo_pagamento', 'fe_modalita_pagamento')) {
            $this->dropColumn('{{%mg_metodo_pagamento}}', 'fe_modalita_pagamento');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
