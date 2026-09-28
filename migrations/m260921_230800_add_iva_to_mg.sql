-- =============================================================
-- Script SQL: aliquote IVA su articoli e soggetti
-- Compatibile con SQL Server
-- m260921_230800_add_iva_to_mg
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='id_iva_vendita')
BEGIN
    ALTER TABLE [dbo].[mg_articolo] ADD [id_iva_vendita] INT NULL;
    CREATE INDEX [idx-mg_articolo-iva_vendita] ON [dbo].[mg_articolo] ([id_iva_vendita]);
    ALTER TABLE [dbo].[mg_articolo] ADD CONSTRAINT [fk-mg_articolo-iva_vendita]
        FOREIGN KEY ([id_iva_vendita]) REFERENCES [dbo].[mg_aliquota_iva] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='id_iva_acquisto')
BEGIN
    ALTER TABLE [dbo].[mg_articolo] ADD [id_iva_acquisto] INT NULL;
    CREATE INDEX [idx-mg_articolo-iva_acquisto] ON [dbo].[mg_articolo] ([id_iva_acquisto]);
    ALTER TABLE [dbo].[mg_articolo] ADD CONSTRAINT [fk-mg_articolo-iva_acquisto]
        FOREIGN KEY ([id_iva_acquisto]) REFERENCES [dbo].[mg_aliquota_iva] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='id_aliquota_iva')
BEGIN
    ALTER TABLE [dbo].[mg_anagrafica] ADD [id_aliquota_iva] INT NULL;
    CREATE INDEX [idx-mg_anagrafica-iva] ON [dbo].[mg_anagrafica] ([id_aliquota_iva]);
    ALTER TABLE [dbo].[mg_anagrafica] ADD CONSTRAINT [fk-mg_anagrafica-iva]
        FOREIGN KEY ([id_aliquota_iva]) REFERENCES [dbo].[mg_aliquota_iva] ([id]) ON DELETE NO ACTION;
END
GO

-- Migra i vecchi valori di mg_articolo.iva in aliquote
IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_articolo' AND COLUMN_NAME='id_iva_vendita')
BEGIN
    DECLARE @perc DECIMAL(9,2), @codice VARCHAR(20), @id INT, @desc VARCHAR(100);
    DECLARE cur CURSOR FOR SELECT DISTINCT iva FROM [dbo].[mg_articolo] WHERE iva IS NOT NULL;
    OPEN cur;
    FETCH NEXT FROM cur INTO @perc;
    WHILE @@FETCH_STATUS = 0
    BEGIN
        SET @codice = RIGHT('00' + CAST(CAST(ROUND(@perc,0) AS INT) AS VARCHAR(2)), 2);
        SET @id = (SELECT id FROM [dbo].[mg_aliquota_iva] WHERE codice = @codice);
        IF @id IS NULL
        BEGIN
            SET @desc = 'IVA ' + CAST(CAST(ROUND(@perc,0) AS INT) AS VARCHAR(4)) + '%';
            INSERT INTO [dbo].[mg_aliquota_iva] ([codice],[descrizione],[percentuale],[attivo])
                VALUES (@codice, @desc, @perc, 1);
            SET @id = SCOPE_IDENTITY();
        END
        UPDATE [dbo].[mg_articolo] SET id_iva_vendita = @id, id_iva_acquisto = @id WHERE iva = @perc;
        FETCH NEXT FROM cur INTO @perc;
    END
    CLOSE cur;
    DEALLOCATE cur;
END
GO
