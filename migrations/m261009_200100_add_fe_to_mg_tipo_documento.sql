-- =============================================================
-- Script SQL: campi fatturazione elettronica su mg_tipo_documento
-- Compatibile con SQL Server
-- m261009_200100_add_fe_to_mg_tipo_documento
--
-- Nota: condizioni/modalita' di pagamento e natura IVA NON sono qui:
-- sono configurate su mg_tipo_pagamento, mg_metodo_pagamento e
-- mg_aliquota_iva (migrazioni m261009_200400/500/600).
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='elettronico')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [elettronico] BIT NOT NULL CONSTRAINT [DF_mg_td_elettronico] DEFAULT(0);
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='fe_tipo_documento')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [fe_tipo_documento] VARCHAR(4) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='fe_regime_fiscale')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [fe_regime_fiscale] VARCHAR(4) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='fe_divisa')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [fe_divisa] VARCHAR(3) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='fe_causale')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [fe_causale] VARCHAR(200) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='fe_esigibilita_iva')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [fe_esigibilita_iva] VARCHAR(1) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='fe_riferimento_normativo')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [fe_riferimento_normativo] VARCHAR(100) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='fe_codice_destinatario')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [fe_codice_destinatario] VARCHAR(7) NULL;
GO

-- Valori di default per i tipi documento esistenti
UPDATE [dbo].[mg_tipo_documento] SET fe_tipo_documento='TD01', fe_regime_fiscale='RF01', fe_divisa='EUR',
    fe_esigibilita_iva='I' WHERE codice='FTT';
GO
