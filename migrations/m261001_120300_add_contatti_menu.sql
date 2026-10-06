-- =============================================================
-- Script SQL: voce menu Contatti (elenco globale)
-- Compatibile con SQL Server
-- m261001_120300_add_contatti_menu
-- =============================================================

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @parent IS NOT NULL
   AND NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-contatti')
BEGIN
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-contatti', 'Contatti', 'address-card', 'mgcontatto/index', @parent, 0, 15, 1, 1, GETDATE());
END
GO
