<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Tabelle di tabulazione dei pagamenti del microgestionale:
 * - mg_tipo_pagamento          (tipologia: contanti, bonifico, ri.ba., assegno, carta...)
 * - mg_metodo_pagamento        (metodo con partenza e numero rate)
 * - mg_metodo_pagamento_rata   (dettaglio rate: giorni dalla partenza e % importo)
 */
class m260921_230100_create_mg_pagamento_tables extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_tipo_pagamento')) {
            $this->createTable('{{%mg_tipo_pagamento}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(20)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_tipo_pagamento-codice', '{{%mg_tipo_pagamento}}', 'codice', true);
        }

        if (!$this->checkTableExist('mg_metodo_pagamento')) {
            $this->createTable('{{%mg_metodo_pagamento}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(20)->notNull(),
                'descrizione' => $this->string(200)->notNull(),
                'id_tipo_pagamento' => $this->integer()->null(),
                // partenza: emissione | giorni_dopo | inizio_mese | fine_mese
                'partenza' => $this->string(20)->notNull()->defaultValue('emissione'),
                'giorni_partenza' => $this->integer()->notNull()->defaultValue(0),
                'n_rate' => $this->integer()->notNull()->defaultValue(1),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_metodo_pagamento-codice', '{{%mg_metodo_pagamento}}', 'codice', true);
            $this->createIndex('idx-mg_metodo_pagamento-tipo', '{{%mg_metodo_pagamento}}', 'id_tipo_pagamento');

            $this->addForeignKey('fk-mg_metodo_pagamento-tipo', '{{%mg_metodo_pagamento}}', 'id_tipo_pagamento',
                '{{%mg_tipo_pagamento}}', 'id', 'SET NULL', 'SET NULL');
        }

        if (!$this->checkTableExist('mg_metodo_pagamento_rata')) {
            $this->createTable('{{%mg_metodo_pagamento_rata}}', [
                'id' => $this->primaryKey(),
                'id_metodo' => $this->integer()->notNull(),
                'progressivo' => $this->integer()->notNull()->defaultValue(1),
                'giorni' => $this->integer()->notNull()->defaultValue(0),
                'percentuale' => $this->decimal(9, 4)->notNull()->defaultValue(0),
            ]);
            $this->createIndex('idx-mg_metodo_pagamento_rata-metodo', '{{%mg_metodo_pagamento_rata}}', 'id_metodo');
            $this->createIndex('idx-mg_metodo_pagamento_rata-unico', '{{%mg_metodo_pagamento_rata}}',
                ['id_metodo', 'progressivo'], true);

            $this->addForeignKey('fk-mg_metodo_pagamento_rata-metodo', '{{%mg_metodo_pagamento_rata}}', 'id_metodo',
                '{{%mg_metodo_pagamento}}', 'id', 'CASCADE', 'CASCADE');
        }

        $this->seed();
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_metodo_pagamento_rata')) {
            $this->dropForeignKey('fk-mg_metodo_pagamento_rata-metodo', '{{%mg_metodo_pagamento_rata}}');
            $this->dropTable('{{%mg_metodo_pagamento_rata}}');
        }
        if ($this->checkTableExist('mg_metodo_pagamento')) {
            $this->dropForeignKey('fk-mg_metodo_pagamento-tipo', '{{%mg_metodo_pagamento}}');
            $this->dropTable('{{%mg_metodo_pagamento}}');
        }
        if ($this->checkTableExist('mg_tipo_pagamento')) {
            $this->dropTable('{{%mg_tipo_pagamento}}');
        }
    }

    private function seed()
    {
        if (!$this->checkTableExist('mg_tipo_pagamento')) {
            return;
        }

        $count = (new Query())->from('{{%mg_tipo_pagamento}}')->count();
        if ((int) $count === 0) {
            $this->batchInsert('{{%mg_tipo_pagamento}}', ['codice', 'descrizione', 'attivo'], [
                ['CONT', 'Contanti', 1],
                ['BON', 'Bonifico bancario', 1],
                ['RIB', 'Ri.Ba.', 1],
                ['ASS', 'Assegno', 1],
                ['CAR', 'Carta di credito', 1],
            ]);
        }

        $countMetodi = (new Query())->from('{{%mg_metodo_pagamento}}')->count();
        if ((int) $countMetodi > 0) {
            return;
        }

        $tipi = (new Query())->select(['id', 'codice'])->from('{{%mg_tipo_pagamento}}')
            ->indexBy('codice')->column();
        $now = new \yii\db\Expression('GETDATE()');

        $metodi = [
            ['CONT', 'Contanti', $tipi['CONT'] ?? null, 'emissione', 0, 1, $now],
            ['BON30', 'Bonifico 30 gg', $tipi['BON'] ?? null, 'giorni_dopo', 30, 1, $now],
            ['BON30-60', 'Bonifico 30/60 gg', $tipi['BON'] ?? null, 'giorni_dopo', 30, 2, $now],
            ['RIB30FM', 'Ri.Ba. 30 gg fine mese', $tipi['RIB'] ?? null, 'fine_mese', 30, 1, $now],
        ];

        foreach ($metodi as $m) {
            $this->insert('{{%mg_metodo_pagamento}}', [
                'codice' => $m[0],
                'descrizione' => $m[1],
                'id_tipo_pagamento' => $m[2],
                'partenza' => $m[3],
                'giorni_partenza' => $m[4],
                'n_rate' => $m[5],
                'attivo' => 1,
                'created_at' => $m[6],
            ]);
        }

        $map = (new Query())->select(['id', 'codice'])->from('{{%mg_metodo_pagamento}}')
            ->indexBy('codice')->column();

        $rate = [
            ['CONT', 1, 0, 100],
            ['BON30', 1, 0, 100],
            ['BON30-60', 1, 0, 50],
            ['BON30-60', 2, 30, 50],
            ['RIB30FM', 1, 0, 100],
        ];

        foreach ($rate as $r) {
            if (empty($map[$r[0]])) {
                continue;
            }
            $this->insert('{{%mg_metodo_pagamento_rata}}', [
                'id_metodo' => $map[$r[0]],
                'progressivo' => $r[1],
                'giorni' => $r[2],
                'percentuale' => $r[3],
            ]);
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
