-- =============================================================
-- Script SQL: unità di misura e fattore sulle righe documento
-- Compatibile con SQL Server
-- m260922_000400_add_um_to_mg_documento_riga
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='id_unita_misura')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [id_unita_misura] INT NULL;
    CREATE INDEX [idx-mg_documento_riga-um] ON [dbo].[mg_documento_riga] ([id_unita_misura]);
    ALTER TABLE [dbo].[mg_documento_riga] ADD CONSTRAINT [fk-mg_documento_riga-um]
        FOREIGN KEY ([id_unita_misura]) REFERENCES [dbo].[mg_unita_misura] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='um')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [um] VARCHAR(10) NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='fattore')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [fattore] DECIMAL(18,4) NOT NULL
        CONSTRAINT [DF_mg_documento_riga_fattore] DEFAULT(1);
END
GO
