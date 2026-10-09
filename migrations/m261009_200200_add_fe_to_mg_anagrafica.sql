-- =============================================================
-- Script SQL: dati documenti elettronici su mg_anagrafica
-- Compatibile con SQL Server
-- m261009_200200_add_fe_to_mg_anagrafica
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_codice_destinatario')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_codice_destinatario] VARCHAR(7) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_pec')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_pec] VARCHAR(100) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_id_paese')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_id_paese] VARCHAR(2) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_nazione')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_nazione] VARCHAR(2) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_tipo_soggetto')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_tipo_soggetto] VARCHAR(1) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_nome')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_nome] VARCHAR(100) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_cognome')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_cognome] VARCHAR(100) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='fe_regime_fiscale')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [fe_regime_fiscale] VARCHAR(4) NULL;
GO

UPDATE [dbo].[mg_anagrafica] SET fe_id_paese='IT' WHERE fe_id_paese IS NULL;
UPDATE [dbo].[mg_anagrafica] SET fe_nazione='IT' WHERE fe_nazione IS NULL;
UPDATE [dbo].[mg_anagrafica] SET fe_tipo_soggetto='G' WHERE fe_tipo_soggetto IS NULL;
GO
