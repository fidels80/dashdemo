-- =============================================================
-- Script SQL: voce menu Magazzini
-- Compatibile con SQL Server
-- m261006_120000_add_magazzini_menu
-- =============================================================

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @parent IS NOT NULL
   AND NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-magazzini')
BEGIN
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-magazzini', 'Magazzini', 'warehouse', 'mgmagazzino/index', @parent, 0, 16, 1, 1, GETDATE());
END
GO
