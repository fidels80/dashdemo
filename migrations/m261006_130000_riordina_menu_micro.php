<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Riordina il menu Microgestionale: crea i gruppi di secondo livello
 * Anagrafiche, Documentale e Prodotti e vi sposta le voci esistenti.
 * La voce duplicata "Sotto Commessa" (mgsottocommessa) viene disattivata.
 */
class m261006_130000_riordina_menu_micro extends Migration
{
    /**
     * codice => [label, icona, ordine]
     */
    private $gruppi = [
        'micro-anagrafiche' => ['Anagrafiche', 'address-book', 1],
        'micro-documentale' => ['Documentale', 'folder-open', 2],
        'micro-prodotti'    => ['Prodotti', 'cubes', 3],
    ];

    /**
     * gruppo => [codice voce => ordine]
     */
    private $figli = [
        'micro-anagrafiche' => [
            'micro-anag'          => 1,
            'micro-contatti'      => 2,
            'micro-tipicontatto'  => 3,
            'micro-commesse'      => 4,
            'micro-sottocommesse' => 5,
        ],
        'micro-documentale' => [
            'micro-doc'           => 1,
            'planning-rapportini' => 2,
            'micro-tipi'          => 3,
            'micro-metodipag'     => 4,
            'micro-tipipag'       => 5,
            'micro-aliquote'      => 6,
        ],
        'micro-prodotti' => [
            'micro-art'        => 1,
            'micro-attributi'  => 2,
            'micro-um'         => 3,
            'micro-modelli'    => 4,
            'micro-wizard'     => 5,
            'micro-magazzini'  => 6,
        ],
    ];

    /**
     * Ordinamento originario delle voci direttamente sotto "micro".
     */
    private $ordineOriginario = [
        'micro-doc'           => 1,
        'micro-tipi'          => 2,
        'micro-anag'          => 3,
        'micro-art'           => 4,
        'micro-metodipag'     => 5,
        'micro-tipipag'       => 6,
        'micro-aliquote'      => 7,
        'planning-rapportini' => 7,
        'micro-um'            => 8,
        'micro-attributi'     => 9,
        'micro-commesse'      => 10,
        'micro-sottocommesse' => 11,
        'micro-wizard'        => 12,
        'micro-tipicontatto'  => 13,
        'micro-modelli'       => 14,
        'micro-contatti'      => 15,
        'micro-magazzini'     => 16,
        'mgsottocommessa'     => 20,
    ];

    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $micro = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        if (!$micro) {
            return;
        }

        $now = new \yii\db\Expression('GETDATE()');

        foreach ($this->gruppi as $codice => $g) {
            $esiste = (new Query())->from('{{%dash_menu}}')->where(['codice' => $codice])->exists();
            if (!$esiste) {
                $this->insert('{{%dash_menu}}', [
                    'codice'      => $codice,
                    'label'       => $g[0],
                    'icona'       => $g[1],
                    'url'         => null,
                    'genitore_id' => $micro,
                    'livello_min' => 0,
                    'ordine'      => $g[2],
                    'per_tutti'   => 1,
                    'attivo'      => 1,
                    'created_at'  => $now,
                ]);
            } else {
                $this->update('{{%dash_menu}}', [
                    'genitore_id' => $micro,
                    'ordine'      => $g[2],
                    'attivo'      => 1,
                ], ['codice' => $codice]);
            }
        }

        foreach ($this->figli as $gruppo => $voci) {
            $genitore = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => $gruppo])->scalar();
            if (!$genitore) {
                continue;
            }
            foreach ($voci as $codiceVoce => $ordine) {
                $this->update('{{%dash_menu}}', [
                    'genitore_id' => $genitore,
                    'ordine'      => $ordine,
                ], ['codice' => $codiceVoce]);
            }
        }

        $this->update('{{%dash_menu}}', ['attivo' => 0], ['codice' => 'mgsottocommessa']);
    }

    public function safeDown()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $micro = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();

        foreach ($this->figli as $voci) {
            foreach ($voci as $codiceVoce => $ordine) {
                $this->update('{{%dash_menu}}', [
                    'genitore_id' => $micro,
                    'ordine'      => isset($this->ordineOriginario[$codiceVoce]) ? $this->ordineOriginario[$codiceVoce] : $ordine,
                ], ['codice' => $codiceVoce]);
            }
        }

        $this->delete('{{%dash_menu}}', ['codice' => array_keys($this->gruppi)]);
        $this->update('{{%dash_menu}}', ['attivo' => 1], ['codice' => 'mgsottocommessa']);
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
