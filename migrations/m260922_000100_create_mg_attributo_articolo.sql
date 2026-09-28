-- =============================================================
-- Script SQL: attributi variante articolo (marca, modello, taglia, colore)
-- Tabella unica discriminata dal campo "tipo"
-- Compatibile con SQL Server
-- m260922_000100_create_mg_attributo_articolo
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_attributo_articolo')
BEGIN
    CREATE TABLE [dbo].[mg_attributo_articolo] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [tipo] VARCHAR(20) NOT NULL,
        [codice] VARCHAR(30) NULL,
        [descrizione] VARCHAR(100) NOT NULL,
        [attivo] BIT NOT NULL CONSTRAINT [DF_mg_attributo_attivo] DEFAULT(1)
    );
    CREATE UNIQUE INDEX [idx-mg_attributo-tipo] ON [dbo].[mg_attributo_articolo] ([tipo],[descrizione]);
END
GO

IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_attributo_articolo])
BEGIN
    INSERT INTO [dbo].[mg_attributo_articolo] ([tipo],[codice],[descrizione],[attivo]) VALUES
        ('marca','GEN','Generico',1),
        ('modello','STD','Standard',1),
        ('taglia','XS','Extra Small',1),
        ('taglia','S','Small',1),
        ('taglia','M','Medium',1),
        ('taglia','L','Large',1),
        ('taglia','XL','Extra Large',1),
        ('taglia','XXL','Double Extra Large',1),
        ('colore','NER','Nero',1),
        ('colore','BIA','Bianco',1),
        ('colore','ROS','Rosso',1),
        ('colore','BLU','Blu',1),
        ('colore','VER','Verde',1),
        ('colore','GIA','Giallo',1);
END
GO
