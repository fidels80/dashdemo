-- =============================================================
-- Script SQL: voce di menu "Changelog" (solo livello >= 100)
-- Compatibile con SQL Server
-- m260922_030100_add_changelog_menu
-- =============================================================

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_menu')
   AND NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice]='sic-changelog')
BEGIN
    DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice]='sicurezza');

    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('sic-changelog','Changelog','clipboard-list','dashchangelog/index',@parent,100,10,0,1,GETDATE());
END
GO
