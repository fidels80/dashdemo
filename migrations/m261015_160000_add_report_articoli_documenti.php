<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Altri due report di esempio per il report designer:
 * - Articoli: tabella con attributi (marca/taglia/colore), aliquota IVA e
 *   colonna calcolata prezzo ivato.
 * - Documenti: raggruppato per tipo documento e anno con conteggio e somma
 *   del totale, piu' valore medio per documento.
 */
class m261015_160000_add_report_articoli_documenti extends Migration
{
    public function safeUp()
    {
        $this->creaFonti();
        $this->creaReport();
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_report')) {
            $this->delete('{{%dash_report}}', ['codice' => ['rep_articoli', 'rep_documenti']]);
        }
        if ($this->tableExists('dash_report_fonte')) {
            $this->delete('{{%dash_report_fonte}}', ['codice' => ['flt_articoli', 'flt_documenti']]);
        }
    }

    private function creaFonti()
    {
        if (!$this->tableExists('dash_report_fonte')) {
            return;
        }

        $fonti = [
            [
                'codice' => 'flt_articoli',
                'nome' => 'Articoli con varianti e IVA',
                'descrizione' => 'Articoli con marca, taglia, colore e aliquota IVA di vendita.',
                'tipo' => 'strutturata',
                'connessione' => 'db',
                'base_tabella' => 'mg_articolo',
                'definizione' => [
                    'base' => ['db' => 'db', 'tabella' => 'mg_articolo', 'alias' => 'a'],
                    'join' => [
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_aliquota_iva', 'alias' => 'ali',
                            'on' => [['left' => 'ali.id', 'right' => 'a.id_iva_vendita']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_attributo_articolo', 'alias' => 'mar',
                            'on' => [['left' => 'mar.id', 'right' => 'a.id_marca']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_attributo_articolo', 'alias' => 'tag',
                            'on' => [['left' => 'tag.id', 'right' => 'a.id_taglia']]],
                        ['tipo' => 'left', 'db' => 'db', 'tabella' => 'mg_attributo_articolo', 'alias' => 'col',
                            'on' => [['left' => 'col.id', 'right' => 'a.id_colore']]],
                    ],
                ],
            ],
            [
                'codice' => 'flt_documenti',
                'nome' => 'Documenti con tipo e cliente',
                'descrizione' => 'Documenti del microgestionale con tipo documento e anagrafica.',
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
                'codice' => 'rep_articoli',
                'nome' => 'Articoli con prezzo ivato',
                'descrizione' => 'Elenco articoli con varianti, aliquota IVA e prezzo ivato calcolato.',
                'categoria' => 'Prodotti',
                'fonte' => 'flt_articoli',
                'tipo' => 'tabella',
                'ordine' => 50,
                'definizione' => [
                    'parametri' => [
                        ['nome' => 'ricerca', 'label' => 'Ricerca descrizione', 'tipo' => 'testo'],
                        ['nome' => 'solo_attivi', 'label' => 'Solo attivi (1/0)', 'tipo' => 'testo', 'default' => '1'],
                    ],
                    'filtri' => [
                        ['campo' => 'a.descrizione', 'operatore' => 'like', 'param1' => 'ricerca', 'opzionale' => true],
                        ['campo' => 'a.attivo', 'operatore' => '=', 'param1' => 'solo_attivi', 'opzionale' => true],
                    ],
                    'colonne' => [
                        ['campo' => 'a.codice', 'alias' => 'codice', 'label' => 'Codice'],
                        ['campo' => 'a.descrizione', 'alias' => 'descrizione', 'label' => 'Descrizione'],
                        ['campo' => 'a.um', 'alias' => 'um', 'label' => 'U.M.'],
                        ['campo' => 'mar.descrizione', 'alias' => 'marca', 'label' => 'Marca'],
                        ['campo' => 'tag.descrizione', 'alias' => 'taglia', 'label' => 'Taglia'],
                        ['campo' => 'col.descrizione', 'alias' => 'colore', 'label' => 'Colore'],
                        ['campo' => 'a.prezzo', 'alias' => 'prezzo', 'label' => 'Prezzo', 'tipo' => 'valuta', 'formato' => 2],
                        ['campo' => 'ali.percentuale', 'alias' => 'iva', 'label' => 'IVA %', 'tipo' => 'numero', 'formato' => 0, 'totale' => false],
                    ],
                    'calcolati' => [
                        ['nome' => 'prezzo_ivato', 'label' => 'Prezzo ivato',
                            'formula' => 'prezzo * (1 + iva / 100)', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'descrizione', 'dir' => 'ASC'],
                    ],
                ],
            ],
            [
                'codice' => 'rep_documenti',
                'nome' => 'Documenti per tipo e anno',
                'descrizione' => 'Numero, totale e valore medio dei documenti per tipo e anno.',
                'categoria' => 'Documentale',
                'fonte' => 'flt_documenti',
                'tipo' => 'raggruppato',
                'ordine' => 60,
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
                    ],
                    'aggregazioni' => [
                        ['campo' => '', 'funzione' => 'COUNT', 'alias' => 'num_documenti',
                            'label' => 'N. documenti', 'tipo' => 'intero'],
                        ['campo' => 'd.totale', 'funzione' => 'SUM', 'alias' => 'totale',
                            'label' => 'Totale', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'calcolati' => [
                        ['nome' => 'medio', 'label' => 'Medio',
                            'formula' => 'totale / num_documenti', 'tipo' => 'valuta', 'formato' => 2],
                    ],
                    'ordinamento' => [
                        ['campo' => 'tipo_documento', 'dir' => 'ASC'],
                        ['campo' => 'anno', 'dir' => 'DESC'],
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

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
