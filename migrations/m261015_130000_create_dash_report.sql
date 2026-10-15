-- =============================================================
-- Script SQL: report designer (fonti dati, report, abilitazioni
-- per operatore, log esecuzioni)
-- Compatibile con SQL Server
-- m261015_130000_create_dash_report
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_report_fonte')
BEGIN
    CREATE TABLE [dbo].[dash_report_fonte] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(50) NOT NULL,
        [nome] VARCHAR(150) NOT NULL,
        [descrizione] VARCHAR(500) NULL,
        [tipo] VARCHAR(20) NOT NULL DEFAULT 'strutturata',
        [connessione] VARCHAR(20) NOT NULL DEFAULT 'db',
        [base_tabella] VARCHAR(128) NULL,
        [definizione] NVARCHAR(MAX) NULL,
        [attivo] BIT NOT NULL DEFAULT 1,
        [creato_da] INT NULL,
        [created_at] DATETIME NULL,
        [updated_at] DATETIME NULL
    );
    CREATE UNIQUE INDEX [uidx-dash_report_fonte-codice] ON [dbo].[dash_report_fonte] ([codice]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_report')
BEGIN
    CREATE TABLE [dbo].[dash_report] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(50) NOT NULL,
        [nome] VARCHAR(150) NOT NULL,
        [descrizione] VARCHAR(500) NULL,
        [categoria] VARCHAR(50) NULL,
        [fonte_id] INT NULL,
        [tipo] VARCHAR(20) NOT NULL DEFAULT 'tabella',
        [definizione] NVARCHAR(MAX) NULL,
        [layout] NVARCHAR(MAX) NULL,
        [licenza_feature] VARCHAR(50) NULL,
        [attivo] BIT NOT NULL DEFAULT 1,
        [ordine] INT NOT NULL DEFAULT 0,
        [creato_da] INT NULL,
        [created_at] DATETIME NULL,
        [updated_at] DATETIME NULL
    );
    CREATE UNIQUE INDEX [uidx-dash_report-codice] ON [dbo].[dash_report] ([codice]);
    CREATE INDEX [idx-dash_report-fonte] ON [dbo].[dash_report] ([fonte_id]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_report_utente')
BEGIN
    CREATE TABLE [dbo].[dash_report_utente] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [report_id] INT NOT NULL,
        [user_id] INT NOT NULL,
        [can_run] BIT NOT NULL DEFAULT 1,
        [can_edit] BIT NOT NULL DEFAULT 0,
        [created_at] DATETIME NULL
    );
    CREATE UNIQUE INDEX [uidx-dash_report_utente-unico] ON [dbo].[dash_report_utente] ([report_id], [user_id]);
    CREATE INDEX [idx-dash_report_utente-user] ON [dbo].[dash_report_utente] ([user_id]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_report_esecuzione')
BEGIN
    CREATE TABLE [dbo].[dash_report_esecuzione] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [report_id] INT NOT NULL,
        [user_id] INT NULL,
        [parametri] NVARCHAR(MAX) NULL,
        [esito] VARCHAR(20) NULL,
        [messaggio] VARCHAR(500) NULL,
        [righe] INT NULL,
        [durata_ms] INT NULL,
        [formato] VARCHAR(20) NULL,
        [created_at] DATETIME NULL
    );
    CREATE INDEX [idx-dash_report_esecuzione-report] ON [dbo].[dash_report_esecuzione] ([report_id]);
    CREATE INDEX [idx-dash_report_esecuzione-created] ON [dbo].[dash_report_esecuzione] ([created_at]);
END
GO
