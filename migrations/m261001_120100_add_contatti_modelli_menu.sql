-- =============================================================
-- Script SQL: voci menu Tipi contatto e Modelli
-- Compatibile con SQL Server
-- m261001_120100_add_contatti_modelli_menu
-- =============================================================

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @parent IS NOT NULL
   AND NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-tipicontatto')
BEGIN
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-tipicontatto', 'Tipi contatto', 'address-book', 'mgtipocontatto/index', @parent, 0, 13, 1, 1, GETDATE());
END
GO

DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @parent IS NOT NULL
   AND NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-modelli')
BEGIN
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-modelli', 'Modelli', 'tshirt', 'mgmodello/index', @parent, 0, 14, 1, 1, GETDATE());
END
GO
