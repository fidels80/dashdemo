-- =============================================================
-- Script SQL: sezione di menu "Vtiger" (livello >= 70)
-- Compatibile con SQL Server
-- m260923_000100_add_vtiger_menu
-- =============================================================

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_menu')
   AND NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice]='vtiger')
BEGIN
    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('vtiger','Vtiger','chart-line','vtiger/search',NULL,70,25,1,1,GETDATE());
END
GO