-- =============================================================
-- Script SQL: destinazione (cliente/fornitore) su mg_tipo_documento
-- Compatibile con SQL Server
-- m260921_230000_add_destinazione_to_mg_tipo_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='destinazione')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [destinazione] VARCHAR(20) NOT NULL
        CONSTRAINT [DF_mg_tipo_documento_destinazione] DEFAULT('cliente');
GO
