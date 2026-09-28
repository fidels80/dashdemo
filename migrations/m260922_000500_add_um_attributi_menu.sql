-- =============================================================
-- Script SQL: voci menu Unità di misura e Attributi articolo
-- Compatibile con SQL Server
-- m260922_000500_add_um_attributi_menu
-- =============================================================

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @parent IS NOT NULL
BEGIN
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-um')
    BEGIN
        INSERT INTO [dbo].[dash_menu]
            ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
        VALUES
            ('micro-um','Unità di misura','ruler','mgunitamisura/index',@parent,0,8,1,1,GETDATE());
    END

    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-attributi')
    BEGIN
        INSERT INTO [dbo].[dash_menu]
            ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
        VALUES
            ('micro-attributi','Attributi articolo','tags','mgattributo/index',@parent,0,9,1,1,GETDATE());
    END
END
GO
