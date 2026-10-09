-- =============================================================
-- Script SQL: tabella anagrafica lotti
-- Compatibile con SQL Server
-- m261009_130000_create_mg_lotto
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_lotto')
BEGIN
    CREATE TABLE [dbo].[mg_lotto] (
        [id]              INT IDENTITY(1,1) NOT NULL,
        [id_articolo]     INT NULL,
        [codice_articolo] NVARCHAR(25) NULL,
        [codice_lotto]    NVARCHAR(50) NOT NULL,
        [descrizione]     NVARCHAR(200) NULL,
        [data_scadenza]   DATE NULL,
        [nota]            NVARCHAR(500) NULL,
        [created_at]      DATETIME NULL,
        CONSTRAINT [PK_mg_lotto] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-mg_lotto-articolo] ON [dbo].[mg_lotto] ([id_articolo] ASC);
    CREATE INDEX [idx-mg_lotto-codice_articolo] ON [dbo].[mg_lotto] ([codice_articolo] ASC);

    ALTER TABLE [dbo].[mg_lotto] WITH CHECK
        ADD CONSTRAINT [fk-mg_lotto-articolo]
        FOREIGN KEY ([id_articolo]) REFERENCES [dbo].[mg_articolo] ([id]) ON DELETE NO ACTION;
END
GO
