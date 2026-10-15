-- =============================================================
-- Script SQL: permesso "reportdesigner" e voce di menu Report Designer
-- m261015_180000_add_report_designer_menu
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM dash_permesso WHERE codice='reportdesigner')
INSERT INTO dash_permesso (codice,descrizione,gruppo,ordine,attivo,created_at)
VALUES ('reportdesigner','Report Designer','Reportistica',110,1,GETDATE());
GO

IF NOT EXISTS (SELECT 1 FROM dash_menu WHERE codice='report-designer')
INSERT INTO dash_menu (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
VALUES ('report-designer','Report Designer','tools','reportdesigner/index',NULL,0,86,0,1,GETDATE());
GO
