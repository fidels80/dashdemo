-- =============================================================
-- Script SQL: blocchi temporanei dei record in modifica
-- Compatibile con SQL Server
-- m261014_100000_create_dash_record_lock
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_record_lock')
BEGIN
    CREATE TABLE [dbo].[dash_record_lock] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [tabella] VARCHAR(128) NOT NULL,
        [id_record] VARCHAR(100) NOT NULL,
        [utente] VARCHAR(125) NOT NULL,
        [utente_id] INT NULL,
        [scadenza] DATETIME NOT NULL,
        [creato_il] DATETIME NOT NULL
    );
    CREATE UNIQUE INDEX [uidx-dash_record_lock-record] ON [dbo].[dash_record_lock] ([tabella], [id_record]);
    CREATE INDEX [idx-dash_record_lock-scadenza] ON [dbo].[dash_record_lock] ([scadenza]);
END
GO
