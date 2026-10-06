-- =============================================================
-- Script SQL: anagrafica magazzini (codice, descrizione, anagrafica)
-- Compatibile con SQL Server
-- m261006_100000_create_mg_magazzino
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_magazzino')
BEGIN
    CREATE TABLE [dbo].[mg_magazzino] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(10) NOT NULL,
        [descrizione] VARCHAR(100) NOT NULL,
        [id_anagrafica] INT NULL,
        [attivo] BIT NOT NULL CONSTRAINT [DF_mg_magazzino_attivo] DEFAULT(1)
    );
    CREATE UNIQUE INDEX [idx-mg_magazzino-codice] ON [dbo].[mg_magazzino] ([codice]);
    CREATE INDEX [idx-mg_magazzino-id_anagrafica] ON [dbo].[mg_magazzino] ([id_anagrafica]);
END
GO

IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino])
    AND EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_anagrafica')
BEGIN
    DECLARE @id_anagrafica INT = NULL;
    SELECT TOP 1 @id_anagrafica = [id] FROM [dbo].[mg_anagrafica] ORDER BY [id] ASC;
    INSERT INTO [dbo].[mg_magazzino] ([codice],[descrizione],[id_anagrafica],[attivo])
    VALUES ('MAG01','Magazzino principale',@id_anagrafica,1);
END
GO
