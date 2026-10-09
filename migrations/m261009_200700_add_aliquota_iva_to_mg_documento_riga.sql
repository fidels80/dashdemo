-- =============================================================
-- Script SQL: id_aliquota_iva su mg_documento_riga
-- Compatibile con SQL Server
-- m261009_200700_add_aliquota_iva_to_mg_documento_riga
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='id_aliquota_iva')
    ALTER TABLE [dbo].[mg_documento_riga] ADD [id_aliquota_iva] INT NULL;
GO

-- Aggancia le righe esistenti all'aliquota con la stessa percentuale
UPDATE d SET d.id_aliquota_iva = (
        SELECT TOP 1 a.id FROM [dbo].[mg_aliquota_iva] a
        WHERE a.percentuale = d.iva
        ORDER BY a.attivo DESC, a.id ASC)
FROM [dbo].[mg_documento_riga] d;
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
               WHERE TABLE_NAME='mg_documento_riga' AND CONSTRAINT_NAME='fk-mg_documento_riga-iva' AND CONSTRAINT_TYPE='FOREIGN KEY')
    ALTER TABLE [dbo].[mg_documento_riga] ADD CONSTRAINT [fk-mg_documento_riga-iva]
        FOREIGN KEY ([id_aliquota_iva]) REFERENCES [dbo].[mg_aliquota_iva] ([id]) ON DELETE SET NULL;
GO
