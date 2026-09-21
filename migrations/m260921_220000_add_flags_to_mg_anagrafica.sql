-- =============================================================
-- Script SQL: flag cliente/fornitore/agente su mg_anagrafica
-- Compatibile con SQL Server
-- m260921_220000_add_flags_to_mg_anagrafica
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='is_cliente')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [is_cliente] BIT NOT NULL CONSTRAINT [DF_mg_anagrafica_is_cliente] DEFAULT(0);
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='is_fornitore')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [is_fornitore] BIT NOT NULL CONSTRAINT [DF_mg_anagrafica_is_fornitore] DEFAULT(0);
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='is_agente')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [is_agente] BIT NOT NULL CONSTRAINT [DF_mg_anagrafica_is_agente] DEFAULT(0);
GO

-- Migra i vecchi valori di "tipo"
UPDATE [dbo].[mg_anagrafica] SET is_cliente = CASE WHEN tipo IN ('cliente','entrambi') THEN 1 ELSE 0 END;
UPDATE [dbo].[mg_anagrafica] SET is_fornitore = CASE WHEN tipo IN ('fornitore','entrambi') THEN 1 ELSE 0 END;
GO
