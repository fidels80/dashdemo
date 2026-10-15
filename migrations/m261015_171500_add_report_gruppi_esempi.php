<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Report di esempio che sfruttano il raggruppamento con dettaglio espandibile
 * (colonne di dettaglio + raggruppamenti + aggregazioni):
 *
 * 1. Elenco documenti con righe        (db) - gruppo per documento, dettaglio righe
 * 2. Vendite per articolo               (db) - gruppo per articolo, dettaglio righe
 * 3. Scadenze per cliente               (db) - gruppo per cliente/stato, dettaglio scadenze
 * 4. Movimenti di magazzino per articolo(db) - gruppo per articolo/magazzino, dettaglio movimenti
 */
class m261015_171500_add_report_gruppi_esempi extends Migration
{
    public function safeUp()
    {
        $this->creaFonti();
        $this->creaReport();
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_report')) {
            $this->delete('{{%dash_report}}', ['codice' => [
                'rep_documenti_dettaglio', 'rep_vendite_articolo',
                'rep_scadenze_cliente', 'rep_magazzino_movimenti',
            ]]);
        }
        if ($this->tableExists('dash_report_fonte')) {
            $this->delete('{{%dash_report_fonte}}', ['codice' => [
                'flt_documenti_righe', 'flt_scadenze', 'flt_movimenti',
            ]]);
        }
    }

    private function creaFonti()
    {
        if (!$this->tableExists('dash_report_fonte')) {
            return;
        }

        $fonti = [
            [
                'codice' => 'flt_documenti_righe',
                'nome' => 'Documenti con righe e articoli',
                'descrizione' => 'Documenti, anagrafica e righe documento con articolo.',
                'tipo' => 'strutturata',
                'connessione' => 'db',
                'base_tabella' => 'mg_documento',
                'definizione' => [
                    'base' => ['db' => 'db', 'tabella' => 'mg_documento', 'alias' => 'd'],
                    'join' => [
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_tipo_documento', 'alias' => 'td',
                            'on' => [['left' => 'td.id', 'right' => 'd.id_tipo']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_anagrafica', 'alias' => 'an',
                            'on' => [['left' => 'an.id', 'right' => 'd.id_anagrafica']]],
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'mg_documento_riga', 'alias' => 'r',
                            'on' => [['left' => 'r.id_documento', 'right' => 'd.id']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_articolo', 'alias' => 'ar',
                            'on' => [['left' => 'ar.id', 'right' => 'r.id_articolo']]],
                    ],
                ],
            ],
            [
                'codice' => 'flt_scadenze',
                'nome' => 'Scadenze con documento e cliente',
                'descrizione' => 'Scadenze collegate al documento e all\'anagrafica cliente.',
                'tipo' => 'strutturata',
                'connessione' => 'db',
                'base_tabella' => 'mg_scadenza',
                'definizione' => [
                    'base' => ['db' => 'db', 'tabella' => 'mg_scadenza', 'alias' => 's'],
                    'join' => [
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'mg_documento', 'alias' => 'd',
                            'on' => [['left' => 'd.id', 'right' => 's.id_documento']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_anagrafica', 'alias' => 'an',
                            'on' => [['left' => 'an.id', 'right' => 'd.id_anagrafica']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_tipo_documento', 'alias' => 'td',
                            'on' => [['left' => 'td.id', 'right' => 'd.id_tipo']]],
                    ],
                ],
            ],
            [
                'codice' => 'flt_movimenti',
                'nome' => 'Movimenti di magazzino con articolo',
                'descrizione' => 'Movimenti di magazzino con riga, documento, articolo e magazzini.',
                'tipo' => 'strutturata',
                'connessione' => 'db',
                'base_tabella' => 'mg_movimentimagazzino',
                'definizione' => [
                    'base' => ['db' => 'db', 'tabella' => 'mg_movimentimagazzino', 'alias' => 'm'],
                    'join' => [
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'mg_documento_riga', 'alias' => 'r',
                            'on' => [['left' => 'r.id', 'right' => 'm.id_documento_riga']]],
                        ['tipo' => 'inner', 'db' => 'db', 'tabella' => 'mg_documento', 'alias' => 'd',
                            'on' => [['left' => 'd.id', 'right' => 'r.id_documento']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_tipo_documento', 'alias' => 'td',
                            'on' => [['left' => 'td.id', 'right' => 'd.id_tipo']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_articolo', 'alias' => 'ar',
                            'on' => [['left' => 'ar.id', 'right' => 'r.id_articolo']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_magazzino', 'alias' => 'mp',
                            'on' => [['left' => 'mp.id', 'right' => 'm.id_magazzino_partenza']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_magazzino', 'alias' => 'ma',
                            'on' => [['left' => 'ma.id', 'right' => 'm.id_magazzino_arrivo']]],
                    ],
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
                'codice' => 'rep_documenti_dettaglio',
                'nome' => 'Elenco documenti con righe',
                'descrizione' => 'Documenti con dettaglio righe, quantità, importo e prezzo medio per documento.',
                'categoria' => 'Documentale',
                'fonte' => 'flt_documenti_righe',
                'tipo' => 'raggruppato',
                'ordine' => 70,
                'icona' => 'file-invoice',
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'dal', 'label' => 'Dal', 'tipo' => 'date', 'default' => 'first-day-month'],
                        ['nome' => 'al', 'label' => 'Al', 'tipo' => 'date', 'default' => 'today'],
                        ['nome' => 'tipo', 'label' => 'Tipo documento', 'tipo' => 'testo'],
                    ],
                    'filtri' => [
                        ['campo' => 'd.data', 'operatore' => 'between', 'param1' => 'dal', 'param2' => 'al'],
                        ['campo' => 'td.descrizione', 'operatore' => 'like', 'param1' => 'tipo', 'opzionale' => true],
                    ],
                    'raggruppamenti' => [
                        ['campo' => 'td.descrizione', 'alias' => 'tipo_documento', 'label' => 'Tipo documento'],
                        ['campo' => 'd.anno', 'alias' => 'anno', 'label' => 'Anno', 'tipo' => 'testo'],
                        ['campo' => 'd.numero', 'alias' => 'numero', 'label' => 'Numero', 'tipo' => 'testo'],
                    ],
                    'colonne' => [
                        ['campo' => 'r.codice_articolo', 'alias' => 'codice', 'label' => 'Codice'],
                        ['campo' => 'r.descrizione', 'alias' => 'descrizione', 'label' => 'Descrizione'],
                        ['campo' => 'r.qta', 'alias' => 'qta', 'label' => 'Q.tà', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'r.prezzo', 'alias' => 'prezzo', 'label' => 'Prezzo', 'tipo' => 'valuta', 'formato' => 2],
                        ['campo' => 'r.totale', 'alias' => 'totale', 'label' => 'Totale riga', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'aggregazioni' => [
                        ['campo' => '', 'funzione' => 'COUNT', 'alias' => 'num_righe', 'label' => 'N. righe', 'tipo' => 'intero'],
                        ['campo' => 'r.qta', 'funzione' => 'SUM', 'alias' => 'tot_qta', 'label' => 'Q.tà tot.', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'r.totale', 'funzione' => 'SUM', 'alias' => 'tot_importo', 'label' => 'Importo tot.', 'tipo' => 'valuta', 'formato' => 2],
                        ['campo' => 'r.prezzo', 'funzione' => 'AVG', 'alias' => 'prezzo_medio', 'label' => 'Prezzo medio', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'tipo_documento', 'dir' => 'ASC'],
                        ['campo' => 'anno', 'dir' => 'DESC'],
                        ['campo' => 'numero', 'dir' => 'ASC'],
                    ],
                ],
            ],
            [
                'codice' => 'rep_vendite_articolo',
                'nome' => 'Vendite per articolo',
                'descrizione' => 'Vendite raggruppate per articolo con dettaglio delle righe e importo medio.',
                'categoria' => 'Vendite',
                'fonte' => 'flt_documenti_righe',
                'tipo' => 'raggruppato',
                'ordine' => 80,
                'icona' => 'cart-shopping',
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'dal', 'label' => 'Dal', 'tipo' => 'date', 'default' => 'first-day-month'],
                        ['nome' => 'al', 'label' => 'Al', 'tipo' => 'date', 'default' => 'today'],
                        ['nome' => 'articolo', 'label' => 'Articolo', 'tipo' => 'testo'],
                    ],
                    'filtri' => [
                        ['campo' => 'd.data', 'operatore' => 'between', 'param1' => 'dal', 'param2' => 'al'],
                        ['campo' => 'ar.descrizione', 'operatore' => 'like', 'param1' => 'articolo', 'opzionale' => true],
                    ],
                    'raggruppamenti' => [
                        ['campo' => 'ar.codice', 'alias' => 'codice_articolo', 'label' => 'Codice'],
                        ['campo' => 'ar.descrizione', 'alias' => 'articolo', 'label' => 'Articolo'],
                    ],
                    'colonne' => [
                        ['campo' => 'd.data', 'alias' => 'data', 'label' => 'Data', 'tipo' => 'data', 'formato' => 'd/m/Y'],
                        ['campo' => 'td.descrizione', 'alias' => 'tipo_documento', 'label' => 'Tipo doc.'],
                        ['campo' => 'an.ragione_sociale', 'alias' => 'cliente', 'label' => 'Cliente'],
                        ['campo' => 'r.qta', 'alias' => 'qta', 'label' => 'Q.tà', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'r.prezzo', 'alias' => 'prezzo', 'label' => 'Prezzo', 'tipo' => 'valuta', 'formato' => 2],
                        ['campo' => 'r.totale', 'alias' => 'totale', 'label' => 'Totale riga', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'aggregazioni' => [
                        ['campo' => '', 'funzione' => 'COUNT', 'alias' => 'num_righe', 'label' => 'N. righe', 'tipo' => 'intero'],
                        ['campo' => 'r.qta', 'funzione' => 'SUM', 'alias' => 'tot_qta', 'label' => 'Q.tà tot.', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'r.totale', 'funzione' => 'SUM', 'alias' => 'tot_importo', 'label' => 'Importo tot.', 'tipo' => 'valuta', 'formato' => 2],
                        ['campo' => 'r.prezzo', 'funzione' => 'AVG', 'alias' => 'prezzo_medio', 'label' => 'Prezzo medio', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'codice_articolo', 'dir' => 'ASC'],
                    ],
                ],
            ],
            [
                'codice' => 'rep_scadenze_cliente',
                'nome' => 'Scadenze per cliente',
                'descrizione' => 'Scadenze aperte/pagate per cliente con importo totale e medio.',
                'categoria' => 'Amministrazione',
                'fonte' => 'flt_scadenze',
                'tipo' => 'raggruppato',
                'ordine' => 90,
                'icona' => 'calendar-days',
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'dal', 'label' => 'Dal', 'tipo' => 'date', 'default' => 'first-day-month'],
                        ['nome' => 'al', 'label' => 'Al', 'tipo' => 'date', 'default' => 'today'],
                        ['nome' => 'stato', 'label' => 'Stato scadenza', 'tipo' => 'testo'],
                    ],
                    'filtri' => [
                        ['campo' => 's.data_scadenza', 'operatore' => 'between', 'param1' => 'dal', 'param2' => 'al'],
                        ['campo' => 's.stato', 'operatore' => '=', 'param1' => 'stato', 'opzionale' => true],
                    ],
                    'raggruppamenti' => [
                        ['campo' => 'an.ragione_sociale', 'alias' => 'cliente', 'label' => 'Cliente'],
                        ['campo' => 's.stato', 'alias' => 'stato', 'label' => 'Stato'],
                    ],
                    'colonne' => [
                        ['campo' => 'd.data', 'alias' => 'data', 'label' => 'Data doc.', 'tipo' => 'data', 'formato' => 'd/m/Y'],
                        ['campo' => 'td.descrizione', 'alias' => 'tipo_documento', 'label' => 'Tipo doc.'],
                        ['campo' => 'd.anno', 'alias' => 'anno', 'label' => 'Anno', 'tipo' => 'testo'],
                        ['campo' => 'd.numero', 'alias' => 'numero', 'label' => 'Numero', 'tipo' => 'testo'],
                        ['campo' => 's.progressivo', 'alias' => 'rata', 'label' => 'Rata', 'tipo' => 'intero'],
                        ['campo' => 's.data_scadenza', 'alias' => 'data_scadenza', 'label' => 'Scadenza', 'tipo' => 'data', 'formato' => 'd/m/Y'],
                        ['campo' => 's.importo', 'alias' => 'importo', 'label' => 'Importo', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'aggregazioni' => [
                        ['campo' => '', 'funzione' => 'COUNT', 'alias' => 'num_scadenze', 'label' => 'N. scadenze', 'tipo' => 'intero'],
                        ['campo' => 's.importo', 'funzione' => 'SUM', 'alias' => 'tot_importo', 'label' => 'Importo tot.', 'tipo' => 'valuta', 'formato' => 2],
                        ['campo' => 's.importo', 'funzione' => 'AVG', 'alias' => 'importo_medio', 'label' => 'Importo medio', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'cliente', 'dir' => 'ASC'],
                        ['campo' => 'stato', 'dir' => 'ASC'],
                    ],
                ],
            ],
            [
                'codice' => 'rep_magazzino_movimenti',
                'nome' => 'Movimenti di magazzino per articolo',
                'descrizione' => 'Movimenti netti, impegnati e ordinati per articolo e magazzino.',
                'categoria' => 'Magazzino',
                'fonte' => 'flt_movimenti',
                'tipo' => 'raggruppato',
                'ordine' => 100,
                'icona' => 'warehouse',
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'dal', 'label' => 'Dal', 'tipo' => 'date', 'default' => 'first-day-month'],
                        ['nome' => 'al', 'label' => 'Al', 'tipo' => 'date', 'default' => 'today'],
                        ['nome' => 'articolo', 'label' => 'Articolo', 'tipo' => 'testo'],
                    ],
                    'filtri' => [
                        ['campo' => 'd.data', 'operatore' => 'between', 'param1' => 'dal', 'param2' => 'al'],
                        ['campo' => 'ar.descrizione', 'operatore' => 'like', 'param1' => 'articolo', 'opzionale' => true],
                    ],
                    'raggruppamenti' => [
                        ['campo' => 'ar.codice', 'alias' => 'codice_articolo', 'label' => 'Codice'],
                        ['campo' => 'ar.descrizione', 'alias' => 'articolo', 'label' => 'Articolo'],
                        ['campo' => 'mp.descrizione', 'alias' => 'magazzino', 'label' => 'Magazzino'],
                    ],
                    'colonne' => [
                        ['campo' => 'd.data', 'alias' => 'data', 'label' => 'Data', 'tipo' => 'data', 'formato' => 'd/m/Y'],
                        ['campo' => 'td.descrizione', 'alias' => 'tipo_documento', 'label' => 'Tipo doc.'],
                        ['campo' => 'm.qta', 'alias' => 'qta', 'label' => 'Q.tà', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'm.segno_movimento', 'alias' => 'segno', 'label' => 'Segno'],
                        ['campo' => 'm.qta_movimento', 'alias' => 'qta_movimento', 'label' => 'Q.tà mov.', 'tipo' => 'numero', 'formato' => 2],
                    ],
                    'aggregazioni' => [
                        ['campo' => '', 'funzione' => 'COUNT', 'alias' => 'num_movimenti', 'label' => 'N. movimenti', 'tipo' => 'intero'],
                        ['campo' => 'm.qta_movimento', 'funzione' => 'SUM', 'alias' => 'tot_movimento', 'label' => 'Movimento netto', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'm.qta_impegnato', 'funzione' => 'SUM', 'alias' => 'tot_impegnato', 'label' => 'Impegnato', 'tipo' => 'numero', 'formato' => 2],
                        ['campo' => 'm.qta_ordinato', 'funzione' => 'SUM', 'alias' => 'tot_ordinato', 'label' => 'Ordinato', 'tipo' => 'numero', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'codice_articolo', 'dir' => 'ASC'],
                        ['campo' => 'magazzino', 'dir' => 'ASC'],
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
                'icona' => $r['icona'],
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

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
