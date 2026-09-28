-- =============================================================
-- Script SQL: tabella changelog applicativo (dash_changelog)
-- Compatibile con SQL Server
-- m260922_030000_create_dash_changelog
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_changelog')
BEGIN
    CREATE TABLE [dbo].[dash_changelog] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [versione] VARCHAR(50) NULL,
        [commit_hash] VARCHAR(64) NULL,
        [tipo] VARCHAR(20) NULL,
        [titolo] VARCHAR(300) NOT NULL,
        [dettaglio] NVARCHAR(MAX) NULL,
        [file_modificati] NVARCHAR(MAX) NULL,
        [autore] VARCHAR(150) NULL,
        [data_commit] DATETIME NULL,
        [created_at] DATETIME NULL CONSTRAINT [DF_dash_changelog_created] DEFAULT(GETDATE())
    );
    CREATE UNIQUE INDEX [idx-dash_changelog-commit] ON [dbo].[dash_changelog] ([commit_hash]);
    CREATE INDEX [idx-dash_changelog-data] ON [dbo].[dash_changelog] ([data_commit]);
END
GO
