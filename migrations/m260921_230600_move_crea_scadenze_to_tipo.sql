-- =============================================================
-- Script SQL: sposta "crea_scadenze" da mg_documento a mg_tipo_documento
-- Compatibile con SQL Server
-- m260921_230600_move_crea_scadenze_to_tipo
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='crea_scadenze')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [crea_scadenze] BIT NOT NULL
        CONSTRAINT [DF_mg_tipo_documento_crea_scadenze] DEFAULT(0);
GO

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento' AND COLUMN_NAME='crea_scadenze')
BEGIN
    UPDATE t SET t.crea_scadenze = 1
    FROM [dbo].[mg_tipo_documento] t
    WHERE EXISTS (SELECT 1 FROM [dbo].[mg_documento] d WHERE d.id_tipo = t.id AND d.crea_scadenze = 1);

    IF EXISTS (SELECT 1 FROM sys.default_constraints WHERE name = 'DF_mg_documento_crea_scadenze')
        ALTER TABLE [dbo].[mg_documento] DROP CONSTRAINT [DF_mg_documento_crea_scadenze];

    ALTER TABLE [dbo].[mg_documento] DROP COLUMN [crea_scadenze];
END
GO
