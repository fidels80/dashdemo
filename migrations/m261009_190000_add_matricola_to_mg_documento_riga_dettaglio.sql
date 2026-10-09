-- =============================================================
-- Script SQL: matricola sulle righe di dettaglio documento
-- Compatibile con SQL Server
-- m261009_190000_add_matricola_to_mg_documento_riga_dettaglio
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga_dettaglio' AND COLUMN_NAME='id_matricola')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga_dettaglio] ADD [id_matricola] INT NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('mg_documento_riga_dettaglio') AND name = 'idx-mg_documento_riga_dettaglio-matricola')
BEGIN
    CREATE INDEX [idx-mg_documento_riga_dettaglio-matricola] ON [dbo].[mg_documento_riga_dettaglio] ([id_matricola]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='mg_documento_riga_dettaglio' AND CONSTRAINT_NAME='fk-mg_documento_riga_dettaglio-matricola')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga_dettaglio] ADD CONSTRAINT [fk-mg_documento_riga_dettaglio-matricola]
        FOREIGN KEY ([id_matricola]) REFERENCES [dbo].[mg_matricola] ([id]) ON DELETE NO ACTION;
END
GO
