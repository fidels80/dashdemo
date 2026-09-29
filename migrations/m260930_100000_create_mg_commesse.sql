-- =============================================================
-- Script SQL: commesse e sottocommesse del microgestionale
-- Compatibile con SQL Server
-- m260930_100000_create_mg_commesse
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_commessa')
BEGIN
    CREATE TABLE [dbo].[mg_commessa] (
        [id]            INT IDENTITY(1,1) NOT NULL,
        [codice]        NVARCHAR(20) NOT NULL,
        [descrizione]   NVARCHAR(200) NOT NULL,
        [data_inizio]   DATE NULL,
        [data_fine]     DATE NULL,
        [id_anagrafica] INT NULL,
        [attivo]        BIT NOT NULL CONSTRAINT [DF_mg_commessa_attivo] DEFAULT(1),
        [created_at]    DATETIME NULL,
        CONSTRAINT [PK_mg_commessa] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-mg_commessa-codice] ON [dbo].[mg_commessa] ([codice] ASC);
    CREATE INDEX [idx-mg_commessa-anagrafica] ON [dbo].[mg_commessa] ([id_anagrafica] ASC);
END
GO

-- NO ACTION: SQL Server rifiuta più percorsi di propagazione
-- (anagrafica -> commessa -> sottocommessa e anagrafica -> sottocommessa).
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_anagrafica')
   AND EXISTS (SELECT 1 FROM sys.foreign_keys WHERE name = 'fk-mg_commessa-anagrafica') = 0
BEGIN
    ALTER TABLE [dbo].[mg_commessa] WITH CHECK
        ADD CONSTRAINT [fk-mg_commessa-anagrafica]
        FOREIGN KEY ([id_anagrafica]) REFERENCES [dbo].[mg_anagrafica] ([id]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_sottocommessa')
BEGIN
    CREATE TABLE [dbo].[mg_sottocommessa] (
        [id]            INT IDENTITY(1,1) NOT NULL,
        [id_commessa]   INT NOT NULL,
        [codice]        NVARCHAR(20) NOT NULL,
        [descrizione]   NVARCHAR(200) NOT NULL,
        [data_inizio]   DATE NULL,
        [data_fine]     DATE NULL,
        [id_anagrafica] INT NULL,
        [attivo]        BIT NOT NULL CONSTRAINT [DF_mg_sottocommessa_attivo] DEFAULT(1),
        [created_at]    DATETIME NULL,
        CONSTRAINT [PK_mg_sottocommessa] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-mg_sottocommessa-codice] ON [dbo].[mg_sottocommessa] ([codice] ASC);
    CREATE INDEX [idx-mg_sottocommessa-commessa] ON [dbo].[mg_sottocommessa] ([id_commessa] ASC);
    CREATE INDEX [idx-mg_sottocommessa-anagrafica] ON [dbo].[mg_sottocommessa] ([id_anagrafica] ASC);

    ALTER TABLE [dbo].[mg_sottocommessa] WITH CHECK
        ADD CONSTRAINT [fk-mg_sottocommessa-commessa]
        FOREIGN KEY ([id_commessa]) REFERENCES [dbo].[mg_commessa] ([id]) ON DELETE CASCADE;
END
GO

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_anagrafica')
   AND EXISTS (SELECT 1 FROM sys.foreign_keys WHERE name = 'fk-mg_sottocommessa-anagrafica') = 0
BEGIN
    ALTER TABLE [dbo].[mg_sottocommessa] WITH CHECK
        ADD CONSTRAINT [fk-mg_sottocommessa-anagrafica]
        FOREIGN KEY ([id_anagrafica]) REFERENCES [dbo].[mg_anagrafica] ([id]);
END
GO

-- rapportini.cd_cli era CHAR(7) (dimensionata sui vecchi codici anacli):
-- si allarga per contenere mg_anagrafica.codice (fino a 20 caratteri).
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
           WHERE TABLE_NAME='rapportini' AND COLUMN_NAME='cd_cli' AND DATA_TYPE='char')
BEGIN
    ALTER TABLE [dbo].[rapportini] ALTER COLUMN [cd_cli] NVARCHAR(20) NOT NULL;
END
GO
