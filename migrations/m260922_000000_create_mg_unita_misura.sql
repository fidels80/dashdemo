-- =============================================================
-- Script SQL: tabella unità di misura (codice, descrizione)
-- Compatibile con SQL Server
-- m260922_000000_create_mg_unita_misura
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_unita_misura')
BEGIN
    CREATE TABLE [dbo].[mg_unita_misura] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(10) NOT NULL,
        [descrizione] VARCHAR(100) NOT NULL,
        [attivo] BIT NOT NULL CONSTRAINT [DF_mg_unita_misura_attivo] DEFAULT(1)
    );
    CREATE UNIQUE INDEX [idx-mg_unita_misura-codice] ON [dbo].[mg_unita_misura] ([codice]);
END
GO

IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_unita_misura])
BEGIN
    INSERT INTO [dbo].[mg_unita_misura] ([codice],[descrizione],[attivo]) VALUES
        ('PZ','Pezzo',1),
        ('NR','Numero',1),
        ('CF','Confezione',1),
        ('CT','Cartone',1),
        ('PLT','Pallet',1),
        ('KG','Chilogrammo',1),
        ('GR','Grammo',1),
        ('LT','Litro',1),
        ('MT','Metro',1),
        ('MQ','Metro quadro',1);
END
GO
