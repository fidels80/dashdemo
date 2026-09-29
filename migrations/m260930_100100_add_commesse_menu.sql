-- =============================================================
-- Script SQL: voci menu Commesse e Sottocommesse
-- Compatibile con SQL Server
-- m260930_100100_add_commesse_menu
-- =============================================================

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @parent IS NOT NULL
BEGIN
    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-commesse')
    BEGIN
        INSERT INTO [dbo].[dash_menu]
            ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
        VALUES
            ('micro-commesse','Commesse','diagram-project','mgcommessa/index',@parent,0,10,1,1,GETDATE());
    END

    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-sottocommesse')
    BEGIN
        INSERT INTO [dbo].[dash_menu]
            ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
        VALUES
            ('micro-sottocommesse','Sottocommesse','diagram-project','mgsottocommessa/index',@parent,0,11,1,1,GETDATE());
    END

    -- La nuova voce "Commesse" precede il wizard prodotti.
    UPDATE [dbo].[dash_menu] SET [ordine] = 12 WHERE [codice] = 'micro-wizard';
END
GO
