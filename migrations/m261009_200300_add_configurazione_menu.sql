-- =============================================================
-- Script SQL: voce menu Configurazione
-- Compatibile con SQL Server
-- m261009_200300_add_configurazione_menu
-- =============================================================

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @parent IS NOT NULL
   AND NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-configurazione')
BEGIN
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-configurazione', 'Configurazione', 'cog', 'mgconfigurazione/index', @parent, 0, 20, 1, 1, GETDATE());
END
GO
