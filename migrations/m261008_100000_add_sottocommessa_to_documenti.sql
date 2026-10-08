-- =============================================================
-- Script SQL: sottocommessa su testata e righe documento
-- Compatibile con SQL Server
-- m261008_100000_add_sottocommessa_to_documenti
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento' AND COLUMN_NAME='id_sottocommessa')
BEGIN
    ALTER TABLE [dbo].[mg_documento] ADD [id_sottocommessa] INT NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('mg_documento') AND name = 'idx-mg_documento-sottocommessa')
BEGIN
    CREATE INDEX [idx-mg_documento-sottocommessa] ON [dbo].[mg_documento] ([id_sottocommessa]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='mg_documento' AND CONSTRAINT_NAME='fk-mg_documento-sottocommessa')
BEGIN
    ALTER TABLE [dbo].[mg_documento] ADD CONSTRAINT [fk-mg_documento-sottocommessa]
        FOREIGN KEY ([id_sottocommessa]) REFERENCES [dbo].[mg_sottocommessa] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='id_sottocommessa')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [id_sottocommessa] INT NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('mg_documento_riga') AND name = 'idx-mg_documento_riga-sottocommessa')
BEGIN
    CREATE INDEX [idx-mg_documento_riga-sottocommessa] ON [dbo].[mg_documento_riga] ([id_sottocommessa]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='mg_documento_riga' AND CONSTRAINT_NAME='fk-mg_documento_riga-sottocommessa')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD CONSTRAINT [fk-mg_documento_riga-sottocommessa]
        FOREIGN KEY ([id_sottocommessa]) REFERENCES [dbo].[mg_sottocommessa] ([id]) ON DELETE NO ACTION;
END
GO
