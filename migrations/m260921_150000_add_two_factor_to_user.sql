-- =============================================================
-- Script SQL: campi 2FA nella tabella [user]
-- Compatibile con SQL Server
-- Eseguito manualmente OPPURE tramite la migrazione Yii2
-- m260921_150000_add_two_factor_to_user
-- =============================================================

IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'user' AND COLUMN_NAME = 'two_factor_secret'
)
BEGIN
    ALTER TABLE [dbo].[user] ADD [two_factor_secret] NVARCHAR(255) NULL;
END
GO

IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'user' AND COLUMN_NAME = 'two_factor_enabled'
)
BEGIN
    ALTER TABLE [dbo].[user] ADD [two_factor_enabled] BIT NOT NULL CONSTRAINT [DF_user_two_factor_enabled] DEFAULT(0);
END
GO

IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'user' AND COLUMN_NAME = 'two_factor_verified_at'
)
BEGIN
    ALTER TABLE [dbo].[user] ADD [two_factor_verified_at] INT NULL;
END
GO
