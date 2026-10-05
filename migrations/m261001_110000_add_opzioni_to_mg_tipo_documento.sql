-- =============================================================
-- Script SQL: opzioni operative sul tipo documento
-- Compatibile con SQL Server
-- m261001_110000_add_opzioni_to_mg_tipo_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='preleva_rapportini')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [preleva_rapportini] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_preleva] DEFAULT(1);
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='crea_articoli')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [crea_articoli] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_art] DEFAULT(1);
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='crea_anagrafiche')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [crea_anagrafiche] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_ana] DEFAULT(1);
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_documento' AND COLUMN_NAME='mostra_matrice')
    ALTER TABLE [dbo].[mg_tipo_documento] ADD [mostra_matrice] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_mat] DEFAULT(1);
GO
