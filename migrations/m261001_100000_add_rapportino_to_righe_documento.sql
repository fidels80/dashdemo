-- =============================================================
-- Script SQL: collegamento riga documento -> rapportino
-- Compatibile con SQL Server
-- m261001_100000_add_rapportino_to_righe_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='id_rapportino')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [id_rapportino] UNIQUEIDENTIFIER NULL;
    CREATE INDEX [idx-mg_documento_riga-rapportino] ON [dbo].[mg_documento_riga] ([id_rapportino]);
    ALTER TABLE [dbo].[mg_documento_riga] ADD CONSTRAINT [fk-mg_documento_riga-rapportino]
        FOREIGN KEY ([id_rapportino]) REFERENCES [dbo].[rapportini] ([id]) ON DELETE NO ACTION;
END
GO
