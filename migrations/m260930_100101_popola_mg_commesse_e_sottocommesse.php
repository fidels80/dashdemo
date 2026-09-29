<?php

use yii\db\Migration;
use yii\db\Query;

class m260930_100101_popola_mg_commesse_e_sottocommesse extends Migration
{
    public function safeUp()
    {
        // 1. Usa dei codici univoci (es. COM001 invece di C001)
        $commesse = [
            ['COM001', 'Commerciale Nord'],
            ['COM002', 'Commerciale Sud'],
            ['COM003', 'Commerciale Centro'],
            ['COM004', 'Progetti Enterprise'],
            ['COM005', 'Servizi Cloud'],
            ['COM006', 'Manutenzione 24/7'],
            ['COM007', 'Consulting IT'],
            ['COM008', 'Implementazioni Custom'],
            ['COM009', 'Outsourcing'],
            ['COM010', 'Supporto Applicativo'],
            ['COM011', 'Licenze Software'],
            ['COM012', 'Formazione Team'],
            ['COM013', 'Audit Sicurezza'],
            ['COM014', 'Backup & Disaster Recovery'],
            ['COM015', 'Migrazione Sistemi'],
            ['COM016', 'Integrazione API'],
            ['COM017', 'Monitoraggio Performance'],
            ['COM018', 'Data Analytics'],
            ['COM019', 'Automazione Processi'],
            ['COM020', 'Sviluppo Mobile'],
        ];

        foreach ($commesse as $commessa) {
            $this->insert('mg_commessa', [
                'codice' => $commessa[0],
                'descrizione' => $commessa[1],
                'data_inizio' => null,
                'data_fine' => null,
                'id_anagrafica' => null,
                'attivo' => 1,
            ]);
        }

        // 2. Recupera solo gli ID delle commesse appena inserite (filtrando per codice)
        $codici = array_column($commesse, 0);
        $allCommesse = (new Query())
            ->select(['id', 'codice'])
            ->from('mg_commessa')
            ->where(['codice' => $codici])
            ->orderBy(['codice' => SORT_ASC])
            ->all();

        // Mappa codice => id
        $commessaMap = array_column($allCommesse, 'id', 'codice');

        // 3. Popola mg_sottocommessa
        $sottocommesse = [
            ['SC001-N', 'Sottocommessa Nord - Fase A', $commessaMap['COM001'] ?? null],
            ['SC002-N', 'Sottocommessa Nord - Fase B', $commessaMap['COM001'] ?? null],
            ['SC003-S', 'Sottocommessa Sud - Fase A', $commessaMap['COM002'] ?? null],
            ['SC004-S', 'Sottocommessa Sud - Fase B', $commessaMap['COM002'] ?? null],
            ['SC005-C', 'Sottocommessa Centro - Fase A', $commessaMap['COM003'] ?? null],
            ['SC006-C', 'Sottocommessa Centro - Fase B', $commessaMap['COM003'] ?? null],
            ['SC007-E', 'Progetti Enterprise - Core', $commessaMap['COM004'] ?? null],
            ['SC008-E', 'Progetti Enterprise - Extension', $commessaMap['COM004'] ?? null],
            ['SC009-CL', 'Servizi Cloud - Platform', $commessaMap['COM005'] ?? null],
            ['SC010-CL', 'Servizi Cloud - Infrastructure', $commessaMap['COM005'] ?? null],
            ['SC011-M', 'Manutenzione 24/7 - Monitoraggio', $commessaMap['COM006'] ?? null],
            ['SC012-M', 'Manutenzione 24/7 - Risoluzione', $commessaMap['COM006'] ?? null],
            ['SC013-CI', 'Consulting IT - Strategico', $commessaMap['COM007'] ?? null],
            ['SC014-CI', 'Consulting IT - Operativo', $commessaMap['COM007'] ?? null],
            ['SC015-IC', 'Implementazioni Custom - Core', $commessaMap['COM008'] ?? null],
            ['SC016-IC', 'Implementazioni Custom - Extensions', $commessaMap['COM008'] ?? null],
            ['SC017-O', 'Outsourcing - Livello 1', $commessaMap['COM009'] ?? null],
            ['SC018-O', 'Outsourcing - Livello 2', $commessaMap['COM009'] ?? null],
            ['SC019-SA', 'Supporto Applicativo - User', $commessaMap['COM010'] ?? null],
            ['SC020-SA', 'Supporto Applicativo - Admin', $commessaMap['COM010'] ?? null],
            ['SC021-L', 'Licenze Software - Renewal', $commessaMap['COM011'] ?? null],
            ['SC022-L', 'Licenze Software - Expansion', $commessaMap['COM011'] ?? null],
            ['SC023-F', 'Formazione Team - Base', $commessaMap['COM012'] ?? null],
            ['SC024-F', 'Formazione Team - Advanced', $commessaMap['COM012'] ?? null],
            ['SC025-A', 'Audit Sicurezza - Assessment', $commessaMap['COM013'] ?? null],
            ['SC026-A', 'Audit Sicurezza - Remediation', $commessaMap['COM013'] ?? null],
            ['SC027-BD', 'Backup & DR - Setup', $commessaMap['COM014'] ?? null],
            ['SC028-BD', 'Backup & DR - Testing', $commessaMap['COM014'] ?? null],
            ['SC029-MI', 'Migrazione Sistemi - Data', $commessaMap['COM015'] ?? null],
            ['SC030-MI', 'Migrazione Sistemi - Configurazione', $commessaMap['COM015'] ?? null],
            ['SC031-API', 'Integrazione API - REST', $commessaMap['COM016'] ?? null],
            ['SC032-API', 'Integrazione API - SOAP', $commessaMap['COM016'] ?? null],
            ['SC033-MP', 'Monitoraggio Performance - Realtime', $commessaMap['COM017'] ?? null],
            ['SC034-MP', 'Monitoraggio Performance - Historical', $commessaMap['COM017'] ?? null],
            ['SC035-DA', 'Data Analytics - Reporting', $commessaMap['COM018'] ?? null],
            ['SC036-DA', 'Data Analytics - BI', $commessaMap['COM018'] ?? null],
            ['SC037-Auto', 'Automazione Processi - Workflow', $commessaMap['COM019'] ?? null],
            ['SC038-Auto', 'Automazione Processi - Automation', $commessaMap['COM019'] ?? null],
            ['SC039-Mo', 'Sviluppo Mobile - iOS', $commessaMap['COM020'] ?? null],
            ['SC040-Mo', 'Sviluppo Mobile - Android', $commessaMap['COM020'] ?? null],
        ];

        foreach ($sottocommesse as $sottocommessa) {
            if ($sottocommessa[2] !== null) {
                $this->insert('mg_sottocommessa', [
                    'id_commessa' => $sottocommessa[2],
                    'codice' => $sottocommessa[0],
                    'descrizione' => $sottocommessa[1],
                    'data_inizio' => null,
                    'data_fine' => null,
                    'id_anagrafica' => null,
                    'attivo' => 1,
                ]);
            }
        }
    }

    public function safeDown()
    {
        $this->delete('mg_sottocommessa');
        $this->delete('mg_commessa', ['LIKE', 'codice', 'COM%']);
    }
}
