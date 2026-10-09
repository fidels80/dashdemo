-- =============================================================
-- Script SQL: flag gestione seriali e data consegna sul tipo documento
-- Compatibile con SQL Server
-- m261009_100000_add_gestione_seriali_to_mg_tipo_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='gestione_seriali')
BEGIN
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [gestione_seriali] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_gestione_seriali] DEFAULT(0);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='gestione_data_consegna')
BEGIN
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [gestione_data_consegna] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_gestione_data_consegna] DEFAULT(0);
END
GO
