-- =============================================================
-- Script SQL: report di esempio articoli e documenti
-- Compatibile con SQL Server
-- m261015_160000_add_report_articoli_documenti
-- =============================================================

-- -------------------------------------------------------------
-- Fonti dati
-- -------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_articoli')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_articoli','Articoli con varianti e IVA','Articoli con marca, taglia, colore e aliquota IVA di vendita.','strutturata','db','mg_articolo',
N'{"base":{"db":"db","tabella":"mg_articolo","alias":"a"},"join":[{"tipo":"left","db":"db","tabella":"mg_aliquota_iva","alias":"ali","on":[{"left":"ali.id","right":"a.id_iva_vendita"}]},{"tipo":"left","db":"db","tabella":"mg_attributo_articolo","alias":"mar","on":[{"left":"mar.id","right":"a.id_marca"}]},{"tipo":"left","db":"db","tabella":"mg_attributo_articolo","alias":"tag","on":[{"left":"tag.id","right":"a.id_taglia"}]},{"tipo":"left","db":"db","tabella":"mg_attributo_articolo","alias":"col","on":[{"left":"col.id","right":"a.id_colore"}]}]}',1,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_documenti')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_documenti','Documenti con tipo e cliente','Documenti del microgestionale con tipo documento e anagrafica.','strutturata','db','mg_documento',
N'{"base":{"db":"db","tabella":"mg_documento","alias":"d"},"join":[{"tipo":"left","db":"db","tabella":"mg_tipo_documento","alias":"td","on":[{"left":"td.id","right":"d.id_tipo"}]},{"tipo":"left","db":"db","tabella":"mg_anagrafica","alias":"an","on":[{"left":"an.id","right":"d.id_anagrafica"}]}]}',1,GETDATE(),GETDATE());
GO

-- -------------------------------------------------------------
-- Report
-- -------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_articoli')
INSERT INTO dash_report (codice,nome,descrizione,categoria,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_articoli','Articoli con prezzo ivato','Elenco articoli con varianti, aliquota IVA e prezzo ivato calcolato.','Prodotti',
(SELECT id FROM dash_report_fonte WHERE codice='flt_articoli'),'tabella',
N'{"parametri":[{"nome":"ricerca","label":"Ricerca descrizione","tipo":"testo"},{"nome":"solo_attivi","label":"Solo attivi (1/0)","tipo":"testo","default":"1"}],"filtri":[{"campo":"a.descrizione","operatore":"like","param1":"ricerca","opzionale":true},{"campo":"a.attivo","operatore":"=","param1":"solo_attivi","opzionale":true}],"colonne":[{"campo":"a.codice","alias":"codice","label":"Codice"},{"campo":"a.descrizione","alias":"descrizione","label":"Descrizione"},{"campo":"a.um","alias":"um","label":"U.M."},{"campo":"mar.descrizione","alias":"marca","label":"Marca"},{"campo":"tag.descrizione","alias":"taglia","label":"Taglia"},{"campo":"col.descrizione","alias":"colore","label":"Colore"},{"campo":"a.prezzo","alias":"prezzo","label":"Prezzo","tipo":"valuta","formato":2},{"campo":"ali.percentuale","alias":"iva","label":"IVA %","tipo":"numero","formato":0,"totale":false}],"calcolati":[{"nome":"prezzo_ivato","label":"Prezzo ivato","formula":"prezzo * (1 + iva / 100)","tipo":"valuta","formato":2}],"ordinamento":[{"campo":"descrizione","dir":"ASC"}]}',
NULL,NULL,1,50,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_documenti')
INSERT INTO dash_report (codice,nome,descrizione,categoria,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_documenti','Documenti per tipo e anno','Numero, totale e valore medio dei documenti per tipo e anno.','Documentale',
(SELECT id FROM dash_report_fonte WHERE codice='flt_documenti'),'raggruppato',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"},{"nome":"tipo","label":"Tipo documento","tipo":"testo"}],"filtri":[{"campo":"d.data","operatore":"between","param1":"dal","param2":"al"},{"campo":"td.descrizione","operatore":"like","param1":"tipo","opzionale":true}],"raggruppamenti":[{"campo":"td.descrizione","alias":"tipo_documento","label":"Tipo documento"},{"campo":"d.anno","alias":"anno","label":"Anno","tipo":"testo"}],"aggregazioni":[{"campo":"","funzione":"COUNT","alias":"num_documenti","label":"N. documenti","tipo":"intero"},{"campo":"d.totale","funzione":"SUM","alias":"totale","label":"Totale","tipo":"valuta","formato":2}],"calcolati":[{"nome":"medio","label":"Medio","formula":"totale / num_documenti","tipo":"valuta","formato":2}],"ordinamento":[{"campo":"tipo_documento","dir":"ASC"},{"campo":"anno","dir":"DESC"}]}',
NULL,NULL,1,60,GETDATE(),GETDATE());
GO
