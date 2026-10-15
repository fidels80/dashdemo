-- =============================================================
-- Script SQL: report di esempio con raggruppamento e dettaglio
-- espandibile (colonne di dettaglio + raggruppamenti + aggregazioni)
-- Compatibile con SQL Server
-- m261015_171500_add_report_gruppi_esempi
-- =============================================================

-- -------------------------------------------------------------
-- Fonti dati
-- -------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_documenti_righe')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_documenti_righe','Documenti con righe e articoli','Documenti, anagrafica e righe documento con articolo.','strutturata','db','mg_documento',
N'{"base":{"db":"db","tabella":"mg_documento","alias":"d"},"join":[{"tipo":"left","db":"db","tabella":"mg_tipo_documento","alias":"td","on":[{"left":"td.id","right":"d.id_tipo"}]},{"tipo":"left","db":"db","tabella":"mg_anagrafica","alias":"an","on":[{"left":"an.id","right":"d.id_anagrafica"}]},{"tipo":"inner","db":"db","tabella":"mg_documento_riga","alias":"r","on":[{"left":"r.id_documento","right":"d.id"}]},{"tipo":"left","db":"db","tabella":"mg_articolo","alias":"ar","on":[{"left":"ar.id","right":"r.id_articolo"}]}]}',1,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_scadenze')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_scadenze','Scadenze con documento e cliente','Scadenze collegate al documento e all''anagrafica cliente.','strutturata','db','mg_scadenza',
N'{"base":{"db":"db","tabella":"mg_scadenza","alias":"s"},"join":[{"tipo":"inner","db":"db","tabella":"mg_documento","alias":"d","on":[{"left":"d.id","right":"s.id_documento"}]},{"tipo":"left","db":"db","tabella":"mg_anagrafica","alias":"an","on":[{"left":"an.id","right":"d.id_anagrafica"}]},{"tipo":"left","db":"db","tabella":"mg_tipo_documento","alias":"td","on":[{"left":"td.id","right":"d.id_tipo"}]}]}',1,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_movimenti')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_movimenti','Movimenti di magazzino con articolo','Movimenti di magazzino con riga, documento, articolo e magazzini.','strutturata','db','mg_movimentimagazzino',
N'{"base":{"db":"db","tabella":"mg_movimentimagazzino","alias":"m"},"join":[{"tipo":"inner","db":"db","tabella":"mg_documento_riga","alias":"r","on":[{"left":"r.id","right":"m.id_documento_riga"}]},{"tipo":"inner","db":"db","tabella":"mg_documento","alias":"d","on":[{"left":"d.id","right":"r.id_documento"}]},{"tipo":"left","db":"db","tabella":"mg_tipo_documento","alias":"td","on":[{"left":"td.id","right":"d.id_tipo"}]},{"tipo":"left","db":"db","tabella":"mg_articolo","alias":"ar","on":[{"left":"ar.id","right":"r.id_articolo"}]},{"tipo":"left","db":"db","tabella":"mg_magazzino","alias":"mp","on":[{"left":"mp.id","right":"m.id_magazzino_partenza"}]},{"tipo":"left","db":"db","tabella":"mg_magazzino","alias":"ma","on":[{"left":"ma.id","right":"m.id_magazzino_arrivo"}]}]}',1,GETDATE(),GETDATE());
GO

-- -------------------------------------------------------------
-- Report
-- -------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_documenti_dettaglio')
INSERT INTO dash_report (codice,nome,descrizione,categoria,icona,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_documenti_dettaglio','Elenco documenti con righe','Documenti con dettaglio righe, quantità, importo e prezzo medio per documento.','Documentale','file-invoice',
(SELECT id FROM dash_report_fonte WHERE codice='flt_documenti_righe'),'raggruppato',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"},{"nome":"tipo","label":"Tipo documento","tipo":"testo"}],"filtri":[{"campo":"d.data","operatore":"between","param1":"dal","param2":"al"},{"campo":"td.descrizione","operatore":"like","param1":"tipo","opzionale":true}],"raggruppamenti":[{"campo":"td.descrizione","alias":"tipo_documento","label":"Tipo documento"},{"campo":"d.anno","alias":"anno","label":"Anno","tipo":"testo"},{"campo":"d.numero","alias":"numero","label":"Numero","tipo":"testo"}],"colonne":[{"campo":"r.codice_articolo","alias":"codice","label":"Codice"},{"campo":"r.descrizione","alias":"descrizione","label":"Descrizione"},{"campo":"r.qta","alias":"qta","label":"Q.tà","tipo":"numero","formato":2},{"campo":"r.prezzo","alias":"prezzo","label":"Prezzo","tipo":"valuta","formato":2},{"campo":"r.totale","alias":"totale","label":"Totale riga","tipo":"valuta","formato":2}],"aggregazioni":[{"campo":"","funzione":"COUNT","alias":"num_righe","label":"N. righe","tipo":"intero"},{"campo":"r.qta","funzione":"SUM","alias":"tot_qta","label":"Q.tà tot.","tipo":"numero","formato":2},{"campo":"r.totale","funzione":"SUM","alias":"tot_importo","label":"Importo tot.","tipo":"valuta","formato":2},{"campo":"r.prezzo","funzione":"AVG","alias":"prezzo_medio","label":"Prezzo medio","tipo":"valuta","formato":2}],"ordinamento":[{"campo":"tipo_documento","dir":"ASC"},{"campo":"anno","dir":"DESC"},{"campo":"numero","dir":"ASC"}]}',
NULL,NULL,1,70,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_vendite_articolo')
INSERT INTO dash_report (codice,nome,descrizione,categoria,icona,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_vendite_articolo','Vendite per articolo','Vendite raggruppate per articolo con dettaglio delle righe e importo medio.','Vendite','cart-shopping',
(SELECT id FROM dash_report_fonte WHERE codice='flt_documenti_righe'),'raggruppato',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"},{"nome":"articolo","label":"Articolo","tipo":"testo"}],"filtri":[{"campo":"d.data","operatore":"between","param1":"dal","param2":"al"},{"campo":"ar.descrizione","operatore":"like","param1":"articolo","opzionale":true}],"raggruppamenti":[{"campo":"ar.codice","alias":"codice_articolo","label":"Codice"},{"campo":"ar.descrizione","alias":"articolo","label":"Articolo"}],"colonne":[{"campo":"d.data","alias":"data","label":"Data","tipo":"data","formato":"d/m/Y"},{"campo":"td.descrizione","alias":"tipo_documento","label":"Tipo doc."},{"campo":"an.ragione_sociale","alias":"cliente","label":"Cliente"},{"campo":"r.qta","alias":"qta","label":"Q.tà","tipo":"numero","formato":2},{"campo":"r.prezzo","alias":"prezzo","label":"Prezzo","tipo":"valuta","formato":2},{"campo":"r.totale","alias":"totale","label":"Totale riga","tipo":"valuta","formato":2}],"aggregazioni":[{"campo":"","funzione":"COUNT","alias":"num_righe","label":"N. righe","tipo":"intero"},{"campo":"r.qta","funzione":"SUM","alias":"tot_qta","label":"Q.tà tot.","tipo":"numero","formato":2},{"campo":"r.totale","funzione":"SUM","alias":"tot_importo","label":"Importo tot.","tipo":"valuta","formato":2},{"campo":"r.prezzo","funzione":"AVG","alias":"prezzo_medio","label":"Prezzo medio","tipo":"valuta","formato":2}],"ordinamento":[{"campo":"codice_articolo","dir":"ASC"}]}',
NULL,NULL,1,80,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_scadenze_cliente')
INSERT INTO dash_report (codice,nome,descrizione,categoria,icona,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_scadenze_cliente','Scadenze per cliente','Scadenze aperte/pagate per cliente con importo totale e medio.','Amministrazione','calendar-days',
(SELECT id FROM dash_report_fonte WHERE codice='flt_scadenze'),'raggruppato',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"},{"nome":"stato","label":"Stato scadenza","tipo":"testo"}],"filtri":[{"campo":"s.data_scadenza","operatore":"between","param1":"dal","param2":"al"},{"campo":"s.stato","operatore":"=","param1":"stato","opzionale":true}],"raggruppamenti":[{"campo":"an.ragione_sociale","alias":"cliente","label":"Cliente"},{"campo":"s.stato","alias":"stato","label":"Stato"}],"colonne":[{"campo":"d.data","alias":"data","label":"Data doc.","tipo":"data","formato":"d/m/Y"},{"campo":"td.descrizione","alias":"tipo_documento","label":"Tipo doc."},{"campo":"d.anno","alias":"anno","label":"Anno","tipo":"testo"},{"campo":"d.numero","alias":"numero","label":"Numero","tipo":"testo"},{"campo":"s.progressivo","alias":"rata","label":"Rata","tipo":"intero"},{"campo":"s.data_scadenza","alias":"data_scadenza","label":"Scadenza","tipo":"data","formato":"d/m/Y"},{"campo":"s.importo","alias":"importo","label":"Importo","tipo":"valuta","formato":2}],"aggregazioni":[{"campo":"","funzione":"COUNT","alias":"num_scadenze","label":"N. scadenze","tipo":"intero"},{"campo":"s.importo","funzione":"SUM","alias":"tot_importo","label":"Importo tot.","tipo":"valuta","formato":2},{"campo":"s.importo","funzione":"AVG","alias":"importo_medio","label":"Importo medio","tipo":"valuta","formato":2}],"ordinamento":[{"campo":"cliente","dir":"ASC"},{"campo":"stato","dir":"ASC"}]}',
NULL,NULL,1,90,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_magazzino_movimenti')
INSERT INTO dash_report (codice,nome,descrizione,categoria,icona,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_magazzino_movimenti','Movimenti di magazzino per articolo','Movimenti netti, impegnati e ordinati per articolo e magazzino.','Magazzino','warehouse',
(SELECT id FROM dash_report_fonte WHERE codice='flt_movimenti'),'raggruppato',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"},{"nome":"articolo","label":"Articolo","tipo":"testo"}],"filtri":[{"campo":"d.data","operatore":"between","param1":"dal","param2":"al"},{"campo":"ar.descrizione","operatore":"like","param1":"articolo","opzionale":true}],"raggruppamenti":[{"campo":"ar.codice","alias":"codice_articolo","label":"Codice"},{"campo":"ar.descrizione","alias":"articolo","label":"Articolo"},{"campo":"mp.descrizione","alias":"magazzino","label":"Magazzino"}],"colonne":[{"campo":"d.data","alias":"data","label":"Data","tipo":"data","formato":"d/m/Y"},{"campo":"td.descrizione","alias":"tipo_documento","label":"Tipo doc."},{"campo":"m.qta","alias":"qta","label":"Q.tà","tipo":"numero","formato":2},{"campo":"m.segno_movimento","alias":"segno","label":"Segno"},{"campo":"m.qta_movimento","alias":"qta_movimento","label":"Q.tà mov.","tipo":"numero","formato":2}],"aggregazioni":[{"campo":"","funzione":"COUNT","alias":"num_movimenti","label":"N. movimenti","tipo":"intero"},{"campo":"m.qta_movimento","funzione":"SUM","alias":"tot_movimento","label":"Movimento netto","tipo":"numero","formato":2},{"campo":"m.qta_impegnato","funzione":"SUM","alias":"tot_impegnato","label":"Impegnato","tipo":"numero","formato":2},{"campo":"m.qta_ordinato","funzione":"SUM","alias":"tot_ordinato","label":"Ordinato","tipo":"numero","formato":2}],"ordinamento":[{"campo":"codice_articolo","dir":"ASC"},{"campo":"magazzino","dir":"ASC"}]}',
NULL,NULL,1,100,GETDATE(),GETDATE());
GO
