-- =============================================================
-- Script SQL: aggiunta colonna icona a dash_report
-- Compatibile con SQL Server
-- m261015_170000_add_report_icona
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='dash_report' AND COLUMN_NAME='icona')
    ALTER TABLE [dbo].[dash_report] ADD [icona] VARCHAR(50) NULL;
GO
