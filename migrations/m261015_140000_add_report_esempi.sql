-- =============================================================
-- Script SQL: report di esempio per il report designer
-- (fonti dati + report + permesso + voce di menu)
-- Compatibile con SQL Server
-- m261015_140000_add_report_esempi
-- =============================================================

-- -------------------------------------------------------------
-- Fonti dati
-- -------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_presenze_dip')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_presenze_dip','Presenze per dipendente','Presenze con dipendente e tipologia assenza.','strutturata','db','presenze',
N'{"base":{"db":"db","tabella":"presenze","alias":"p"},"join":[{"tipo":"left","db":"db","tabella":"personale","alias":"dip","on":[{"left":"dip.id","right":"p.personale_id"}]},{"tipo":"left","db":"db","tabella":"tipologia_presenza","alias":"tp","on":[{"left":"tp.codice","right":"p.tipo_assenza"}]}]}',1,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_attivita')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_attivita','Attivita'' flotta (cross-db)','Planning con ditta esterna e descrizione cliente da ADB_UFFICIO2000.','strutturata','db','planning',
N'{"base":{"db":"db","tabella":"planning","alias":"pl"},"join":[{"tipo":"left","db":"db","tabella":"dittaesterna","alias":"de","on":[{"left":"de.codice","right":"pl.ditta_esterna"}]},{"tipo":"left","db":"db5","tabella":"cf","alias":"cli","on":[{"left":"cli.Cd_CF","right":"pl.cd_cf"}]}]}',1,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_consuntivo_ditte')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_consuntivo_ditte','Ore e costi per ditta esterna','Incrocio presenze/planning per il consuntivo per ditta esterna.','strutturata','db','presenze',
N'{"base":{"db":"db","tabella":"presenze","alias":"p"},"join":[{"tipo":"inner","db":"db","tabella":"personale","alias":"dip","on":[{"left":"dip.id","right":"p.personale_id"}]},{"tipo":"inner","db":"db","tabella":"planning_personale","alias":"pp","on":[{"left":"pp.personale_id","right":"dip.id"}]},{"tipo":"inner","db":"db","tabella":"planning","alias":"pl","on":[{"left":"pl.id","right":"pp.planning_id"},{"left":"pl.data_attivita","right":"p.data_presenza"}]},{"tipo":"inner","db":"db","tabella":"dittaesterna","alias":"de","on":[{"left":"de.codice","right":"pl.ditta_esterna"}]}]}',1,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report_fonte WHERE codice='flt_clienti')
INSERT INTO dash_report_fonte (codice,nome,descrizione,tipo,connessione,base_tabella,definizione,attivo,created_at,updated_at)
VALUES ('flt_clienti','Anagrafica clienti/fornitori (db5)','Tabella cf di ADB_UFFICIO2000.','strutturata','db5','cf',
N'{"base":{"db":"db5","tabella":"cf","alias":"cf"}}',1,GETDATE(),GETDATE());
GO

-- -------------------------------------------------------------
-- Report
-- -------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_presenze_costi')
INSERT INTO dash_report (codice,nome,descrizione,categoria,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_presenze_costi','Presenze e costi per dipendente','Ore lavorate e costo per dipendente nel periodo selezionato.','Personale',
(SELECT id FROM dash_report_fonte WHERE codice='flt_presenze_dip'),'raggruppato',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"}],"filtri":[{"campo":"p.data_presenza","operatore":"between","param1":"dal","param2":"al"}],"raggruppamenti":[{"campo":"dip.cognome","alias":"cognome","label":"Cognome"},{"campo":"dip.nome","alias":"nome","label":"Nome"}],"aggregazioni":[{"campo":"p.ore_lavorate","funzione":"SUM","alias":"tot_ore","label":"Ore","tipo":"numero","formato":2},{"campo":"dip.tariffa_oraria","funzione":"MAX","alias":"tariffa","label":"Tariffa","tipo":"valuta","formato":2}],"calcolati":[{"nome":"costo","label":"Costo","formula":"tot_ore * tariffa","tipo":"valuta","formato":2}],"ordinamento":[{"campo":"cognome","dir":"ASC"},{"campo":"nome","dir":"ASC"}]}',
NULL,NULL,1,10,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_attivita_flotta')
INSERT INTO dash_report (codice,nome,descrizione,categoria,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_attivita_flotta','Attivita'' flotta per periodo','Elenco delle attivita'' pianificate con ditta esterna e cliente.','Operativo',
(SELECT id FROM dash_report_fonte WHERE codice='flt_attivita'),'tabella',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"},{"nome":"ditta","label":"Ditta esterna","tipo":"testo"}],"filtri":[{"campo":"pl.data_attivita","operatore":"between","param1":"dal","param2":"al"},{"campo":"de.descrizione","operatore":"like","param1":"ditta","opzionale":true}],"colonne":[{"campo":"pl.data_attivita","alias":"data","label":"Data","tipo":"data","formato":"d/m/Y"},{"campo":"de.descrizione","alias":"ditta","label":"Ditta esterna"},{"campo":"cli.Descrizione","alias":"cliente","label":"Cliente"},{"campo":"pl.indirizzo","alias":"indirizzo","label":"Indirizzo"},{"campo":"pl.descrizione","alias":"note","label":"Note"},{"campo":"pl.qta_operai","alias":"operai","label":"Operai","tipo":"numero","formato":0}],"ordinamento":[{"campo":"data","dir":"DESC"}]}',
NULL,NULL,1,20,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_consuntivo_ditte')
INSERT INTO dash_report (codice,nome,descrizione,categoria,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_consuntivo_ditte','Consuntivo ore per ditta esterna','Ore e costo per ditta esterna e dipendente.','Amministrazione',
(SELECT id FROM dash_report_fonte WHERE codice='flt_consuntivo_ditte'),'raggruppato',
N'{"parametri":[{"nome":"dal","label":"Dal","tipo":"date","default":"first-day-month"},{"nome":"al","label":"Al","tipo":"date","default":"today"}],"filtri":[{"campo":"pl.data_attivita","operatore":"between","param1":"dal","param2":"al"}],"raggruppamenti":[{"campo":"de.descrizione","alias":"ditta","label":"Ditta esterna"},{"campo":"dip.cognome","alias":"cognome","label":"Cognome"},{"campo":"dip.nome","alias":"nome","label":"Nome"}],"aggregazioni":[{"campo":"p.ore_lavorate","funzione":"SUM","alias":"tot_ore","label":"Ore","tipo":"numero","formato":1},{"campo":"dip.tariffa_oraria","funzione":"MAX","alias":"tariffa","label":"Tariffa","tipo":"valuta","formato":2}],"calcolati":[{"nome":"costo","label":"Costo","formula":"tot_ore * tariffa","tipo":"valuta","formato":2}],"ordinamento":[{"campo":"ditta","dir":"ASC"},{"campo":"cognome","dir":"ASC"}]}',
NULL,NULL,1,30,GETDATE(),GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_report WHERE codice='rep_clienti')
INSERT INTO dash_report (codice,nome,descrizione,categoria,fonte_id,tipo,definizione,layout,licenza_feature,attivo,ordine,created_at,updated_at)
VALUES ('rep_clienti','Anagrafica clienti e fornitori','Elenco clienti/fornitori con ricerca per descrizione.','Anagrafica',
(SELECT id FROM dash_report_fonte WHERE codice='flt_clienti'),'tabella',
N'{"parametri":[{"nome":"ricerca","label":"Ricerca descrizione","tipo":"testo"},{"nome":"solo_clienti","label":"Solo clienti (1/0)","tipo":"testo","default":"1"}],"filtri":[{"campo":"cf.Descrizione","operatore":"like","param1":"ricerca","opzionale":true},{"campo":"cf.Cliente","operatore":"=","param1":"solo_clienti","opzionale":true}],"colonne":[{"campo":"cf.Cd_CF","alias":"codice","label":"Codice"},{"campo":"cf.Descrizione","alias":"descrizione","label":"Descrizione"},{"campo":"cf.Localita","alias":"localita","label":"Localita"},{"campo":"cf.Cd_Provincia","alias":"provincia","label":"Prov."},{"campo":"cf.PartitaIva","alias":"piva","label":"Partita IVA"}],"ordinamento":[{"campo":"descrizione","dir":"ASC"}]}',
NULL,NULL,1,40,GETDATE(),GETDATE());
GO

-- -------------------------------------------------------------
-- Permesso e voce di menu
-- -------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dash_permesso WHERE codice='report')
INSERT INTO dash_permesso (codice,descrizione,gruppo,ordine,attivo,created_at)
VALUES ('report','Report','Reportistica',100,1,GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_menu WHERE codice='report-catalogo')
INSERT INTO dash_menu (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
VALUES ('report-catalogo','Report','chart-bar','report/catalogo',NULL,100,85,0,1,GETDATE());
GO
