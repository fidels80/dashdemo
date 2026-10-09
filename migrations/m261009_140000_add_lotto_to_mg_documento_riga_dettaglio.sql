-- =============================================================
-- Script SQL: lotto sulle righe di dettaglio documento
-- Compatibile con SQL Server
-- m261009_140000_add_lotto_to_mg_documento_riga_dettaglio
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga_dettaglio' AND COLUMN_NAME='id_lotto')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga_dettaglio] ADD [id_lotto] INT NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('mg_documento_riga_dettaglio') AND name = 'idx-mg_documento_riga_dettaglio-lotto')
BEGIN
    CREATE INDEX [idx-mg_documento_riga_dettaglio-lotto] ON [dbo].[mg_documento_riga_dettaglio] ([id_lotto]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='mg_documento_riga_dettaglio' AND CONSTRAINT_NAME='fk-mg_documento_riga_dettaglio-lotto')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga_dettaglio] ADD CONSTRAINT [fk-mg_documento_riga_dettaglio-lotto]
        FOREIGN KEY ([id_lotto]) REFERENCES [dbo].[mg_lotto] ([id]) ON DELETE NO ACTION;
END
GO
