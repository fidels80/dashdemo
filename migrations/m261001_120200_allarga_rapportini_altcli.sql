-- =============================================================
-- Script SQL: allarga rapportini.altcli da CHAR(7) a NVARCHAR(20)
-- Compatibile con SQL Server
-- m261001_120200_allarga_rapportini_altcli
-- =============================================================

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
           WHERE TABLE_NAME='rapportini' AND COLUMN_NAME='altcli' AND DATA_TYPE='char')
BEGIN
    ALTER TABLE [dbo].[rapportini] ALTER COLUMN [altcli] NVARCHAR(20) NULL;
END
GO
