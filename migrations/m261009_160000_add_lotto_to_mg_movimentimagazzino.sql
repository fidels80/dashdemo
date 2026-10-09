-- =============================================================
-- Script SQL: lotto sul movimento di magazzino
-- Compatibile con SQL Server
-- m261009_160000_add_lotto_to_mg_movimentimagazzino
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_movimentimagazzino' AND COLUMN_NAME='id_lotto')
BEGIN
    ALTER TABLE [dbo].[mg_movimentimagazzino] ADD [id_lotto] INT NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE object_id = OBJECT_ID('mg_movimentimagazzino') AND name = 'idx-mg_movimentimagazzino-lotto')
BEGIN
    CREATE INDEX [idx-mg_movimentimagazzino-lotto] ON [dbo].[mg_movimentimagazzino] ([id_lotto]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME='mg_movimentimagazzino' AND CONSTRAINT_NAME='fk-mg_movimentimagazzino-lotto')
BEGIN
    ALTER TABLE [dbo].[mg_movimentimagazzino] ADD CONSTRAINT [fk-mg_movimentimagazzino-lotto]
        FOREIGN KEY ([id_lotto]) REFERENCES [dbo].[mg_lotto] ([id]) ON DELETE NO ACTION;
END
GO
