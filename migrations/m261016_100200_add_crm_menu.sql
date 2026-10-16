-- ------------------------------------------------------------------
-- Menu e permessi del CRM.
-- Script gemello della migrazione m261016_100200_add_crm_menu
-- ------------------------------------------------------------------

-- Permessi ACL
IF NOT EXISTS (SELECT 1 FROM dbo.dash_permesso WHERE codice = 'crmticket')
    INSERT INTO dbo.dash_permesso (codice, descrizione, gruppo, ordine, attivo, created_at)
    VALUES ('crmticket', 'Ticket CRM', 'CRM', 100, 1, GETDATE());

IF NOT EXISTS (SELECT 1 FROM dbo.dash_permesso WHERE codice = 'crmconfig')
    INSERT INTO dbo.dash_permesso (codice, descrizione, gruppo, ordine, attivo, created_at)
    VALUES ('crmconfig', 'Configurazione CRM', 'CRM', 101, 1, GETDATE());
GO

-- Gruppo CRM (interno)
IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'crm')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('crm', 'CRM', 'headset', NULL, NULL, 0, 35, 0, 1, GETDATE());
GO

-- Gruppo Portale cliente (esterni)
IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'portale')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('portale', 'Portale cliente', 'user-circle', NULL, NULL, 0, 36, 1, 1, GETDATE());
GO

DECLARE @crm INT = (SELECT id FROM dbo.dash_menu WHERE codice = 'crm');
DECLARE @portale INT = (SELECT id FROM dbo.dash_menu WHERE codice = 'portale');

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'crm-ticket')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('crm-ticket', 'Ticket', 'list', 'crmticket/index', @crm, 0, 1, 0, 1, GETDATE());

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'crm-ticket-nuovo')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('crm-ticket-nuovo', 'Nuova richiesta', 'plus', 'crmticket/create', @crm, 0, 2, 0, 1, GETDATE());

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'crm-config')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('crm-config', 'Configurazione', 'tags', 'crmconfig/index', @crm, 0, 3, 0, 1, GETDATE());

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'portale-richieste')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('portale-richieste', 'Le mie richieste', 'life-ring', 'portale/index', @portale, 0, 1, 1, 1, GETDATE());

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'portale-nuova')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('portale-nuova', 'Nuova richiesta', 'plus', 'portale/richiesta-create', @portale, 0, 2, 1, 1, GETDATE());

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'portale-documenti')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('portale-documenti', 'Documenti', 'file-invoice', 'portale/documenti', @portale, 0, 3, 1, 1, GETDATE());

IF NOT EXISTS (SELECT 1 FROM dbo.dash_menu WHERE codice = 'portale-scadenze')
    INSERT INTO dbo.dash_menu (codice, label, icona, url, genitore_id, livello_min, ordine, per_tutti, attivo, created_at)
    VALUES ('portale-scadenze', 'Scadenze', 'calendar-check', 'portale/scadenze', @portale, 0, 4, 1, 1, GETDATE());
GO
