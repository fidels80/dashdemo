-- =============================================================
-- Script SQL: flag di evasione sul rapportino
-- Compatibile con SQL Server
-- m261001_100100_add_evaso_to_rapportini
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='rapportini' AND COLUMN_NAME='evaso')
BEGIN
    ALTER TABLE [dbo].[rapportini] ADD [evaso] BIT NOT NULL
        CONSTRAINT [DF_rapportini_evaso] DEFAULT(0);
END
GO
