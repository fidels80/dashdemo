-- =============================================================
-- Script SQL: tabella anagrafica matricole / numeri di serie
-- Compatibile con SQL Server
-- m261009_170000_create_mg_matricola
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_matricola')
BEGIN
    CREATE TABLE [dbo].[mg_matricola] (
        [id]              INT IDENTITY(1,1) NOT NULL,
        [id_articolo]     INT NULL,
        [codice_articolo] NVARCHAR(25) NULL,
        [matricola]       NVARCHAR(100) NOT NULL,
        [descrizione]     NVARCHAR(200) NULL,
        [nota]            NVARCHAR(500) NULL,
        [attivo]          BIT NOT NULL CONSTRAINT [DF_mg_matricola_attivo] DEFAULT(1),
        [created_at]      DATETIME NULL,
        CONSTRAINT [PK_mg_matricola] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-mg_matricola-articolo] ON [dbo].[mg_matricola] ([id_articolo] ASC);
    CREATE INDEX [idx-mg_matricola-codice_articolo] ON [dbo].[mg_matricola] ([codice_articolo] ASC);

    ALTER TABLE [dbo].[mg_matricola] WITH CHECK
        ADD CONSTRAINT [fk-mg_matricola-articolo]
        FOREIGN KEY ([id_articolo]) REFERENCES [dbo].[mg_articolo] ([id]) ON DELETE NO ACTION;
END
GO
