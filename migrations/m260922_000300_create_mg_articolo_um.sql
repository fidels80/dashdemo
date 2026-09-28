-- =============================================================
-- Script SQL: unità di misura per articolo (fattore + predefinita)
-- Compatibile con SQL Server
-- m260922_000300_create_mg_articolo_um
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_articolo_um')
BEGIN
    CREATE TABLE [dbo].[mg_articolo_um] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [id_articolo] INT NOT NULL,
        [id_unita_misura] INT NOT NULL,
        [fattore] DECIMAL(18,4) NOT NULL CONSTRAINT [DF_mg_articolo_um_fattore] DEFAULT(1),
        [predefinita] BIT NOT NULL CONSTRAINT [DF_mg_articolo_um_pred] DEFAULT(0),
        [attivo] BIT NOT NULL CONSTRAINT [DF_mg_articolo_um_attivo] DEFAULT(1)
    );
    CREATE INDEX [idx-mg_articolo_um-articolo] ON [dbo].[mg_articolo_um] ([id_articolo]);
    CREATE INDEX [idx-mg_articolo_um-unita] ON [dbo].[mg_articolo_um] ([id_unita_misura]);
    CREATE UNIQUE INDEX [idx-mg_articolo_um-unico] ON [dbo].[mg_articolo_um] ([id_articolo],[id_unita_misura]);
    ALTER TABLE [dbo].[mg_articolo_um] ADD CONSTRAINT [fk-mg_articolo_um-articolo]
        FOREIGN KEY ([id_articolo]) REFERENCES [dbo].[mg_articolo] ([id]) ON DELETE CASCADE;
    ALTER TABLE [dbo].[mg_articolo_um] ADD CONSTRAINT [fk-mg_articolo_um-unita]
        FOREIGN KEY ([id_unita_misura]) REFERENCES [dbo].[mg_unita_misura] ([id]) ON DELETE NO ACTION;
END
GO
