-- ------------------------------------------------------------------
-- Voce di menu e permesso per il catalogo FAQ del CRM.
-- Script gemello della migrazione m261016_120000_add_crm_faq_menu
-- ------------------------------------------------------------------

IF NOT EXISTS (SELECT 1 FROM dbo.dash_permesso WHERE codice = 'crmfaq')
    INSERT INTO dbo.dash_permesso (codice, descrizione, gruppo, ordine, attivo, created_at)
    VALUES ('crmfaq', 'FAQ CRM', 'CRM', 102, 1, GETDATE());
GO

DECLARE @crm INT = (SELECT id FROM dbo.dash_menu WHERE codice = 'crm');

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'crm-faq')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('crm-faq', 'FAQ', 'question-circle', 'crmfaq/index', @crm, 0, 4, 0, 1, GETDATE());
GO
