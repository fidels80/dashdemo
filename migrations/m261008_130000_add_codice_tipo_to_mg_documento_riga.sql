-- =============================================================
-- Script SQL: codice tipo documento sulle righe documento
-- Compatibile con SQL Server
-- m261008_130000_add_codice_tipo_to_mg_documento_riga
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento_riga' AND COLUMN_NAME='codice_tipo')
BEGIN
    ALTER TABLE [dbo].[mg_documento_riga] ADD [codice_tipo] NVARCHAR(20) NULL;
END
GO

UPDATE r SET r.codice_tipo = d.codice_tipo
FROM [dbo].[mg_documento_riga] r
INNER JOIN [dbo].[mg_documento] d ON d.id = r.id_documento
WHERE r.codice_tipo IS NULL;
GO
