-- =============================================================
-- Script SQL: guid + attributi variante su mg_articolo
-- Compatibile con SQL Server
-- m260922_000200_add_varianti_to_mg_articolo
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='guid')
BEGIN
    ALTER TABLE [dbo].[mg_articolo] ADD [guid] UNIQUEIDENTIFIER NULL;
    UPDATE [dbo].[mg_articolo] SET [guid] = NEWID() WHERE [guid] IS NULL;
    ALTER TABLE [dbo].[mg_articolo] ALTER COLUMN [guid] UNIQUEIDENTIFIER NOT NULL;
    ALTER TABLE [dbo].[mg_articolo] ADD CONSTRAINT [DF_mg_articolo_guid] DEFAULT NEWID() FOR [guid];
    CREATE UNIQUE INDEX [idx-mg_articolo-guid] ON [dbo].[mg_articolo] ([guid]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='id_marca')
BEGIN
    ALTER TABLE [dbo].[mg_articolo] ADD [id_marca] INT NULL;
    CREATE INDEX [idx-mg_articolo-id_marca] ON [dbo].[mg_articolo] ([id_marca]);
    ALTER TABLE [dbo].[mg_articolo] ADD CONSTRAINT [fk-mg_articolo-marca]
        FOREIGN KEY ([id_marca]) REFERENCES [dbo].[mg_attributo_articolo] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='id_modello')
BEGIN
    ALTER TABLE [dbo].[mg_articolo] ADD [id_modello] INT NULL;
    CREATE INDEX [idx-mg_articolo-id_modello] ON [dbo].[mg_articolo] ([id_modello]);
    ALTER TABLE [dbo].[mg_articolo] ADD CONSTRAINT [fk-mg_articolo-modello]
        FOREIGN KEY ([id_modello]) REFERENCES [dbo].[mg_attributo_articolo] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='id_taglia')
BEGIN
    ALTER TABLE [dbo].[mg_articolo] ADD [id_taglia] INT NULL;
    CREATE INDEX [idx-mg_articolo-id_taglia] ON [dbo].[mg_articolo] ([id_taglia]);
    ALTER TABLE [dbo].[mg_articolo] ADD CONSTRAINT [fk-mg_articolo-taglia]
        FOREIGN KEY ([id_taglia]) REFERENCES [dbo].[mg_attributo_articolo] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='id_colore')
BEGIN
    ALTER TABLE [dbo].[mg_articolo] ADD [id_colore] INT NULL;
    CREATE INDEX [idx-mg_articolo-id_colore] ON [dbo].[mg_articolo] ([id_colore]);
    ALTER TABLE [dbo].[mg_articolo] ADD CONSTRAINT [fk-mg_articolo-colore]
        FOREIGN KEY ([id_colore]) REFERENCES [dbo].[mg_attributo_articolo] ([id]) ON DELETE NO ACTION;
END
GO
