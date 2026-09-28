-- =============================================================
-- Script SQL: varianti nelle righe documento + flag su tipo documento
-- Compatibile con SQL Server
-- m260922_000600_add_varianti_to_righe_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='mostra_varianti')
BEGIN
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [mostra_varianti] BIT NOT NULL
        CONSTRAINT [DF_mg_tipo_documento_varianti] DEFAULT(0);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='taglia')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [taglia] VARCHAR(50) NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='colore')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [colore] VARCHAR(50) NULL;
END
GO
