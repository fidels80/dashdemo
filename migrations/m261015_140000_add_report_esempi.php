<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Report di esempio per il report designer (fonti dati + report) e relativi
 * permesso e voce di menu. Servono da modello e per provare il motore:
 *
 * 1. Presenze e costi per dipendente    (db)   - raggruppato, parametri, calcolato
 * 2. Attivita' flotta per periodo       (cross-db db + db5) - tabella, filtri
 * 3. Consuntivo ore per ditta esterna   (db)   - raggruppato, join multipli
 * 4. Anagrafica clienti e fornitori     (db5)  - tabella, filtro like
 */
class m261015_140000_add_report_esempi extends Migration
{
    public function safeUp()
    {
        $this->creaFonti();
        $this->creaReport();
        $this->creaPermesso();
        $this->creaMenu();
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_report')) {
            $this->delete('{{%dash_report}}', ['codice' => [
                'rep_presenze_costi', 'rep_attivita_flotta',
                'rep_consuntivo_ditte', 'rep_clienti',
            ]]);
        }
        if ($this->tableExists('dash_report_fonte')) {
            $this->delete('{{%dash_report_fonte}}', ['codice' => [
                'flt_presenze_dip', 'flt_attivita', 'flt_consuntivo_ditte', 'flt_clienti',
            ]]);
        }
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'report-catalogo']);
        }
        if ($this->tableExists('dash_permesso')) {
            $this->delete('{{%dash_permesso}}', ['codice' => 'report']);
        }
    }

    private function creaFonti()
    {
        if (!$this->tableExists('dash_report_fonte')) {
            return;
        }

        $fonti = [
            [
                'codice' => 'flt_presenze_dip',
                'nome' => 'Presenze per dipendente',
                'descrizione' => 'Presenze con dipendente e tipologia assenza.',
                'tipo' => 'strutturata',
                'connessione' => 'db',
                'base_tabella' => 'presenze',
                'definizione' => [
                    'base' => ['db' => 'db', 'tabella' => 'presenze', 'alias' => 'p'],
                    'join' => [
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'personale', 'alias' => 'dip',
                            'on' => [['left' => 'dip.id', 'right' => 'p.personale_id']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'tipologia_presenza', 'alias' => 'tp',
                            'on' => [['left' => 'tp.codice', 'right' => 'p.tipo_assenza']]],
                    ],
                ],
            ],
            [
                'codice' => 'flt_attivita',
                'nome' => 'Attivita\' flotta (cross-db)',
                'descrizione' => 'Planning con ditta esterna e descrizione cliente da ADB_UFFICIO2000.',
                'tipo' => 'strutturata',
                'connessione' => 'db',
                'base_tabella' => 'planning',
                'definizione' => [
                    'base' => ['db' => 'db', 'tabella' => 'planning', 'alias' => 'pl'],
                    'join' => [
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'dittaesterna', 'alias' => 'de',
                            'on' => [['left' => 'de.codice', 'right' => 'pl.ditta_esterna']]],
                        ['tipo' => 'left', 'db' => 'db5', 'tabella' => 'cf', 'alias' => 'cli',
                            'on' => [['left' => 'cli.Cd_CF', 'right' => 'pl.cd_cf']]],
                    ],
                ],
            ],
            [
                'codice' => 'flt_consuntivo_ditte',
                'nome' => 'Ore e costi per ditta esterna',
                'descrizione' => 'Incrocio presenze/planning per il consuntivo per ditta esterna.',
                'tipo' => 'strutturata',
                'connessione' => 'db',
                'base_tabella' => 'presenze',
                'definizione' => [
                    'base' => ['db' => 'db', 'tabella' => 'presenze', 'alias' => 'p'],
                    'join' => [
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'personale', 'alias' => 'dip',
                            'on' => [['left' => 'dip.id', 'right' => 'p.personale_id']]],
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'planning_personale', 'alias' => 'pp',
                            'on' => [['left' => 'pp.personale_id', 'right' => 'dip.id']]],
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'planning', 'alias' => 'pl',
                            'on' => [['left' => 'pl.id', 'right' => 'pp.planning_id'],
                                ['left' => 'pl.data_attivita', 'right' => 'p.data_presenza']]],
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'dittaesterna', 'alias' => 'de',
                            'on' => [['left' => 'de.codice', 'right' => 'pl.ditta_esterna']]],
                    ],
                ],
            ],
            [
                'codice' => 'flt_clienti',
                'nome' => 'Anagrafica clienti/fornitori (db5)',
                'descrizione' => 'Tabella cf di ADB_UFFICIO2000.',
                'tipo' => 'strutturata',
                'connessione' => 'db5',
                'base_tabella' => 'cf',
                'definizione' => [
                    'base' => ['db' => 'db5', 'tabella' => 'cf', 'alias' => 'cf'],
                ],
            ],
        ];

        foreach ($fonti as $f) {
            if ((new Query())->from('{{%dash_report_fonte}}')->where(['codice' => $f['codice']])->exists()) {
                continue;
            }
            $this->insert('{{%dash_report_fonte}}', [
                'codice' => $f['codice'],
                'nome' => $f['nome'],
                'descrizione' => $f['descrizione'],
                'tipo' => $f['tipo'],
                'connessione' => $f['connessione'],
                'base_tabella' => $f['base_tabella'],
                'definizione' => json_encode($f['definizione'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'attivo' => 1,
                'created_at' => new \yii\db\Expression('GETDATE()'),
                'updated_at' => new \yii\db\Expression('GETDATE()'),
            ]);
        }
    }

    private function creaReport()
    {
        if (!$this->tableExists('dash_report')) {
            return;
        }

        $fonteId = function ($codice) {
            return (new Query())->select('id')->from('{{%dash_report_fonte}}')
                ->where(['codice' => $codice])->scalar() ?: null;
        };

        $reports = [
            [
                'codice' => 'rep_presenze_costi',
                'nome' => 'Presenze e costi per dipendente',
                'descrizione' => 'Ore lavorate e costo per dipendente nel periodo selezionato.',
                'categoria' => 'Personale',
                'fonte' => 'flt_presenze_dip',
                'tipo' => 'raggruppato',
                'ordine' => 10,
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'dal', 'label' => 'Dal', 'tipo' => 'date', 'default' => 'first-day-month'],
                        ['nome' => 'al', 'label' => 'Al', 'tipo' => 'date', 'default' => 'today'],
                    ],
                    'filtri' => [
                        ['campo' => 'p.data_presenza', 'operatore' => 'between', 'param1' => 'dal', 'param2' => 'al'],
                    ],
                    'raggruppamenti' => [
                        ['campo' => 'dip.cognome', 'alias' => 'cognome', 'label' => 'Cognome'],
                        ['campo' => 'dip.nome', 'alias' => 'nome', 'label' => 'Nome'],
                    ],
                    'aggregazioni' => [
                        ['campo' => 'p.ore_lavorate', 'funzione' => 'SUM', 'alias' => 'tot_ore',
                            'label' => 'Ore', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'dip.tariffa_oraria', 'funzione' => 'MAX', 'alias' => 'tariffa',
                            'label' => 'Tariffa', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'calcolati' => [
                        ['nome' => 'costo', 'label' => 'Costo', 'formula' => 'tot_ore * tariffa',
                            'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'cognome', 'dir' => 'ASC'],
                        ['campo' => 'nome', 'dir' => 'ASC'],
                    ],
                ],
            ],
            [
                'codice' => 'rep_attivita_flotta',
                'nome' => 'Attivita\' flotta per periodo',
                'descrizione' => 'Elenco delle attivita\' pianificate con ditta esterna e cliente.',
                'categoria' => 'Operativo',
                'fonte' => 'flt_attivita',
                'tipo' => 'tabella',
                'ordine' => 20,
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'dal', 'label' => 'Dal', 'tipo' => 'date', 'default' => 'first-day-month'],
                        ['nome' => 'al', 'label' => 'Al', 'tipo' => 'date', 'default' => 'today'],
                        ['nome' => 'ditta', 'label' => 'Ditta esterna', 'tipo' => 'testo'],
                    ],
                    'filtri' => [
                        ['campo' => 'pl.data_attivita', 'operatore' => 'between', 'param1' => 'dal', 'param2' => 'al'],
                        ['campo' => 'de.descrizione', 'operatore' => 'like', 'param1' => 'ditta', 'opzionale' => true],
                    ],
                    'colonne' => [
                        ['campo' => 'pl.data_attivita', 'alias' => 'data', 'label' => 'Data', 'tipo' => 'data', 'formato' => 'd/m/Y'],
                        ['campo' => 'de.descrizione', 'alias' => 'ditta', 'label' => 'Ditta esterna'],
                        ['campo' => 'cli.Descrizione', 'alias' => 'cliente', 'label' => 'Cliente'],
                        ['campo' => 'pl.indirizzo', 'alias' => 'indirizzo', 'label' => 'Indirizzo'],
                        ['campo' => 'pl.descrizione', 'alias' => 'note', 'label' => 'Note'],
                        ['campo' => 'pl.qta_operai', 'alias' => 'operai', 'label' => 'Operai', 'tipo' => 'numero', 'formato' => 0],
                    ],
                    'ordinamento' => [
                        ['campo' => 'data', 'dir' => 'DESC'],
                    ],
                ],
            ],
            [
                'codice' => 'rep_consuntivo_ditte',
                'nome' => 'Consuntivo ore per ditta esterna',
                'descrizione' => 'Ore e costo per ditta esterna e dipendente.',
                'categoria' => 'Amministrazione',
                'fonte' => 'flt_consuntivo_ditte',
                'tipo' => 'raggruppato',
                'ordine' => 30,
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'dal', 'label' => 'Dal', 'tipo' => 'date', 'default' => 'first-day-month'],
                        ['nome' => 'al', 'label' => 'Al', 'tipo' => 'date', 'default' => 'today'],
                    ],
                    'filtri' => [
                        ['campo' => 'pl.data_attivita', 'operatore' => 'between', 'param1' => 'dal', 'param2' => 'al'],
                    ],
                    'raggruppamenti' => [
                        ['campo' => 'de.descrizione', 'alias' => 'ditta', 'label' => 'Ditta esterna'],
                        ['campo' => 'dip.cognome', 'alias' => 'cognome', 'label' => 'Cognome'],
                        ['campo' => 'dip.nome', 'alias' => 'nome', 'label' => 'Nome'],
                    ],
                    'aggregazioni' => [
                        ['campo' => 'p.ore_lavorate', 'funzione' => 'SUM', 'alias' => 'tot_ore',
                            'label' => 'Ore', 'tipo' => 'numero', 'formato' => 1],
                        ['campo' => 'dip.tariffa_oraria', 'funzione' => 'MAX', 'alias' => 'tariffa',
                            'label' => 'Tariffa', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'calcolati' => [
                        ['nome' => 'costo', 'label' => 'Costo', 'formula' => 'tot_ore * tariffa',
                            'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'ditta', 'dir' => 'ASC'],
                        ['campo' => 'cognome', 'dir' => 'ASC'],
                    ],
                ],
            ],
            [
                'codice' => 'rep_clienti',
                'nome' => 'Anagrafica clienti e fornitori',
                'descrizione' => 'Elenco clienti/fornitori con ricerca per descrizione.',
                'categoria' => 'Anagrafica',
                'fonte' => 'flt_clienti',
                'tipo' => 'tabella',
                'ordine' => 40,
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'ricerca', 'label' => 'Ricerca descrizione', 'tipo' => 'testo'],
                        ['nome' => 'solo_clienti', 'label' => 'Solo clienti (1/0)', 'tipo' => 'testo', 'default' => '1'],
                    ],
                    'filtri' => [
                        ['campo' => 'cf.Descrizione', 'operatore' => 'like', 'param1' => 'ricerca', 'opzionale' => true],
                        ['campo' => 'cf.Cliente', 'operatore' => '=', 'param1' => 'solo_clienti', 'opzionale' => true],
                    ],
                    'colonne' => [
                        ['campo' => 'cf.Cd_CF', 'alias' => 'codice', 'label' => 'Codice'],
                        ['campo' => 'cf.Descrizione', 'alias' => 'descrizione', 'label' => 'Descrizione'],
                        ['campo' => 'cf.Localita', 'alias' => 'localita', 'label' => 'Localita'],
                        ['campo' => 'cf.Cd_Provincia', 'alias' => 'provincia', 'label' => 'Prov.'],
                        ['campo' => 'cf.PartitaIva', 'alias' => 'piva', 'label' => 'Partita IVA'],
                    ],
                    'ordinamento' => [
                        ['campo' => 'descrizione', 'dir' => 'ASC'],
                    ],
                ],
            ],
        ];

        foreach ($reports as $r) {
            if ((new Query())->from('{{%dash_report}}')->where(['codice' => $r['codice']])->exists()) {
                continue;
            }
            $this->insert('{{%dash_report}}', [
                'codice' => $r['codice'],
                'nome' => $r['nome'],
                'descrizione' => $r['descrizione'],
                'categoria' => $r['categoria'],
                'fonte_id' => $fonteId($r['fonte']),
                'tipo' => $r['tipo'],
                'definizione' => json_encode($r['definizione'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'layout' => null,
                'licenza_feature' => null,
                'attivo' => 1,
                'ordine' => $r['ordine'],
                'created_at' => new \yii\db\Expression('GETDATE()'),
                'updated_at' => new \yii\db\Expression('GETDATE()'),
            ]);
        }
    }

    private function creaPermesso()
    {
        if (!$this->tableExists('dash_permesso')) {
            return;
        }
        if ((new Query())->from('{{%dash_permesso}}')->where(['codice' => 'report'])->exists()) {
            return;
        }
        $this->insert('{{%dash_permesso}}', [
            'codice' => 'report',
            'descrizione' => 'Report',
            'gruppo' => 'Reportistica',
            'ordine' => 100,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function creaMenu()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }
        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'report-catalogo'])->exists()) {
            return;
        }
        $this->insert('{{%dash_menu}}', [
            'codice' => 'report-catalogo',
            'label' => 'Report',
            'icona' => 'chart-bar',
            'url' => 'report/catalogo',
            'genitore_id' => null,
            'livello_min' => 100,
            'ordine' => 85,
            'per_tutti' => 0,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
