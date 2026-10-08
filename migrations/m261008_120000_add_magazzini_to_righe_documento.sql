-- =============================================================
-- Script SQL: magazzini di partenza e arrivo sulle righe documento
-- Compatibile con SQL Server
-- m261008_120000_add_magazzini_to_righe_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='id_magazzino_partenza')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [id_magazzino_partenza] INT NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('mg_documento_riga') AND name = 'idx-mg_documento_riga-partenza')
BEGIN
    CREATE INDEX [idx-mg_documento_riga-partenza] ON [dbo].[mg_documento_riga] ([id_magazzino_partenza]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='mg_documento_riga' AND CONSTRAINT_NAME='fk-mg_documento_riga-magazzino_partenza')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD CONSTRAINT [fk-mg_documento_riga-magazzino_partenza]
        FOREIGN KEY ([id_magazzino_partenza]) REFERENCES [dbo].[mg_magazzino] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='id_magazzino_arrivo')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [id_magazzino_arrivo] INT NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('mg_documento_riga') AND name = 'idx-mg_documento_riga-arrivo')
BEGIN
    CREATE INDEX [idx-mg_documento_riga-arrivo] ON [dbo].[mg_documento_riga] ([id_magazzino_arrivo]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='mg_documento_riga' AND CONSTRAINT_NAME='fk-mg_documento_riga-magazzino_arrivo')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD CONSTRAINT [fk-mg_documento_riga-magazzino_arrivo]
        FOREIGN KEY ([id_magazzino_arrivo]) REFERENCES [dbo].[mg_magazzino] ([id]) ON DELETE NO ACTION;
END
GO
