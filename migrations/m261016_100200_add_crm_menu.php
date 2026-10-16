<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Menu e permessi del CRM:
 * - gruppo di menu "CRM" (operativo interno) con Ticket, Nuova richiesta e
 *   Configurazione; visibile ai supervisori e assegnabile per operatore.
 * - gruppo "Portale cliente" (area riservata agli utenti esterni).
 * - permessi ACL crmticket / crmconfig.
 */
class m261016_100200_add_crm_menu extends Migration
{
    public function safeUp()
    {
        $this->creaPermessi();
        $this->creaMenu();
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => [
                'crm', 'crm-ticket', 'crm-ticket-nuovo', 'crm-config',
                'portale', 'portale-richieste', 'portale-nuova', 'portale-documenti', 'portale-scadenze',
            ]]);
        }
        if ($this->tableExists('dash_permesso')) {
            $this->delete('{{%dash_permesso}}', ['codice' => ['crmticket', 'crmconfig']]);
        }
    }

    private function creaPermessi()
    {
        if (!$this->tableExists('dash_permesso')) {
            return;
        }
        $now = new \yii\db\Expression('GETDATE()');
        $permessi = [
            ['crmticket', 'Ticket CRM', 'CRM', 100],
            ['crmconfig', 'Configurazione CRM', 'CRM', 101],
        ];
        foreach ($permessi as $p) {
            if ((new Query())->from('{{%dash_permesso}}')->where(['codice' => $p[0]])->exists()) {
                continue;
            }
            $this->insert('{{%dash_permesso}}', [
                'codice' => $p[0],
                'descrizione' => $p[1],
                'gruppo' => $p[2],
                'ordine' => $p[3],
                'attivo' => 1,
                'created_at' => $now,
            ]);
        }
    }

    private function creaMenu()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $now = new \yii\db\Expression('GETDATE()');

        if (!(new Query())->from('{{%dash_menu}}')->where(['codice' => 'crm'])->exists()) {
            $this->insert('{{%dash_menu}}', [
                'codice' => 'crm',
                'label' => 'CRM',
                'icona' => 'headset',
                'url' => null,
                'genitore_id' => null,
                'livello_min' => 0,
                'ordine' => 35,
                'per_tutti' => 0,
                'attivo' => 1,
                'created_at' => $now,
            ]);
        }

        if (!(new Query())->from('{{%dash_menu}}')->where(['codice' => 'portale'])->exists()) {
            $this->insert('{{%dash_menu}}', [
                'codice' => 'portale',
                'label' => 'Portale cliente',
                'icona' => 'user-circle',
                'url' => null,
                'genitore_id' => null,
                'livello_min' => 0,
                'ordine' => 36,
                'per_tutti' => 1,
                'attivo' => 1,
                'created_at' => $now,
            ]);
        }

        $crm = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'crm'])->scalar();
        $portale = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'portale'])->scalar();

        $voci = [
            ['crm-ticket', 'Ticket', 'list', 'crmticket/index', $crm, 0, 1, 0],
            ['crm-ticket-nuovo', 'Nuova richiesta', 'plus', 'crmticket/create', $crm, 0, 2, 0],
            ['crm-config', 'Configurazione', 'tags', 'crmconfig/index', $crm, 0, 3, 0],
            ['portale-richieste', 'Le mie richieste', 'life-ring', 'portale/index', $portale, 0, 1, 1],
            ['portale-nuova', 'Nuova richiesta', 'plus', 'portale/richiesta-create', $portale, 0, 2, 1],
            ['portale-documenti', 'Documenti', 'file-invoice', 'portale/documenti', $portale, 0, 3, 1],
            ['portale-scadenze', 'Scadenze', 'calendar-check', 'portale/scadenze', $portale, 0, 4, 1],
        ];

        foreach ($voci as $v) {
            if ((new Query())->from('{{%dash_menu}}')->where(['codice' => $v[0]])->exists()) {
                continue;
            }
            $this->insert('{{%dash_menu}}', [
                'codice' => $v[0],
                'label' => $v[1],
                'icona' => $v[2],
                'url' => $v[3],
                'genitore_id' => $v[4] ?: null,
                'livello_min' => $v[5],
                'ordine' => $v[6],
                'per_tutti' => $v[7],
                'attivo' => 1,
                'created_at' => $now,
            ]);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
