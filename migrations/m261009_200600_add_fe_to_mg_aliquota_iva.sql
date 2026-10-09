-- =============================================================
-- Script SQL: natura IVA SDI su mg_aliquota_iva
-- Compatibile con SQL Server
-- m261009_200600_add_fe_to_mg_aliquota_iva
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_aliquota_iva' AND COLUMN_NAME='fe_natura')
    ALTER TABLE [dbo].[mg_aliquota_iva] ADD [fe_natura] VARCHAR(4) NULL;
GO

UPDATE [dbo].[mg_aliquota_iva] SET fe_natura='N4'
    WHERE percentuale = 0 AND (fe_natura IS NULL OR fe_natura='');
GO
