-- =============================================================
-- Script SQL: voci menu Taglie, Tessuti e Colori
-- Sotto Microgestionale > Prodotti; aprono mgattributo/index
-- filtrato per tipo. Sposta in coda le voci esistenti.
-- Compatibile con SQL Server
-- m261006_140000_add_attributi_menu
-- =============================================================

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro-prodotti');
IF @parent IS NULL
    SET @parent = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');
IF @parent IS NULL
    RETURN;

IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-taglie')
    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('micro-taglie','Taglie','ruler','mgattributo/index?tipo=taglia',@parent,0,2,1,1,GETDATE());

IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-tessuti')
    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('micro-tessuti','Tessuti','tshirt','mgattributo/index?tipo=tessuto',@parent,0,3,1,1,GETDATE());

IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-colori')
    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('micro-colori','Colori','palette','mgattributo/index?tipo=colore',@parent,0,4,1,1,GETDATE());

UPDATE [dbo].[dash_menu] SET [ordine] = 1 WHERE [codice] = 'micro-art';
UPDATE [dbo].[dash_menu] SET [ordine] = 5 WHERE [codice] = 'micro-attributi';
UPDATE [dbo].[dash_menu] SET [ordine] = 6 WHERE [codice] = 'micro-um';
UPDATE [dbo].[dash_menu] SET [ordine] = 7 WHERE [codice] = 'micro-modelli';
UPDATE [dbo].[dash_menu] SET [ordine] = 8 WHERE [codice] = 'micro-wizard';
UPDATE [dbo].[dash_menu] SET [ordine] = 9 WHERE [codice] = 'micro-magazzini';
GO
