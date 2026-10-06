-- =============================================================
-- Script SQL: magazzini e segno del movimento sul tipo documento
-- Compatibile con SQL Server
-- m261006_110000_add_magazzino_to_mg_tipo_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='id_magazzino_partenza')
BEGIN
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [id_magazzino_partenza] INT NULL;
    CREATE INDEX [idx-mg_tipo_documento-id_magazzino_partenza] ON [dbo].[mg_tipo_documento] ([id_magazzino_partenza]);
END
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='id_magazzino_arrivo')
BEGIN
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [id_magazzino_arrivo] INT NULL;
    CREATE INDEX [idx-mg_tipo_documento-id_magazzino_arrivo] ON [dbo].[mg_tipo_documento] ([id_magazzino_arrivo]);
END
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='segno_movimento')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [segno_movimento] VARCHAR(10) NOT NULL CONSTRAINT [DF_mg_tipo_documento_segno] DEFAULT('nessuno');
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='varia_impegnato')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [varia_impegnato] VARCHAR(10) NOT NULL CONSTRAINT [DF_mg_tipo_documento_impegnato] DEFAULT('nessuno');
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='varia_ordinato')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [varia_ordinato] VARCHAR(10) NOT NULL CONSTRAINT [DF_mg_tipo_documento_ordinato] DEFAULT('nessuno');
GO
