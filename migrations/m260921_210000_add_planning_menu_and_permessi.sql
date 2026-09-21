-- =============================================================
-- Script SQL: voci menu Planning (CRUD anagrafiche) + risorse permessi
-- Compatibile con SQL Server
-- m260921_210000_add_planning_menu_and_permessi
-- =============================================================

-- Voci di menu sotto Planning
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_menu')
BEGIN
    DECLARE @planningId INT = (SELECT id FROM [dbo].[dash_menu] WHERE codice = 'planning');

    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-personale')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-personale','Personale','users','personale/index',@planningId,0,4,1,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-veicoli')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-veicoli','Veicoli','truck','veicoli/index',@planningId,0,5,1,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-presenze')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-presenze','Presenze','user-clock','presenze/index',@planningId,0,6,1,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-rapportini')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-rapportini','Rapportini','clock','rapportini/index',@planningId,0,7,1,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-mansioni')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-mansioni','Mansioni','briefcase','mansioni/index',@planningId,0,8,1,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-reparti')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-reparti','Reparti','sitemap','reparti/index',@planningId,0,9,1,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-locazioni')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-locazioni','Locazioni','map-marker-alt','locazioni/index',@planningId,0,10,1,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE codice='planning-tipopres')
        INSERT INTO [dbo].[dash_menu] (codice,label,icona,url,genitore_id,livello_min,ordine,per_tutti,attivo,created_at)
        VALUES ('planning-tipopres','Tipologie presenza','clipboard-list','tipologiapresenza/index',@planningId,0,11,1,1,GETDATE());
END
GO

-- Risorse permessi
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_permesso')
BEGIN
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='personale')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('personale','Personale','Operativo',100,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='veicoli')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('veicoli','Veicoli','Operativo',110,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='presenze')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('presenze','Presenze','Operativo',120,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='rapportini')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('rapportini','Rapportini','Operativo',130,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='mansioni')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('mansioni','Mansioni','Operativo',140,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='reparti')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('reparti','Reparti','Operativo',150,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='locazioni')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('locazioni','Locazioni','Operativo',160,1,GETDATE());
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE codice='tipologiapresenza')
        INSERT INTO [dbo].[dash_permesso] (codice,descrizione,gruppo,ordine,attivo,created_at) VALUES ('tipologiapresenza','Tipologie presenza','Operativo',170,1,GETDATE());
END
GO
