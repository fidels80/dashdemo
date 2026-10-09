-- =============================================================
-- Script SQL: flag gestione lotti sul tipo documento
-- Compatibile con SQL Server
-- m261009_120000_add_gestione_lotti_to_mg_tipo_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='gestione_lotti')
BEGIN
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [gestione_lotti] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_gestione_lotti] DEFAULT(0);
END
GO
