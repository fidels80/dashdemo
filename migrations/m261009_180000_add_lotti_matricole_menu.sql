-- =============================================================
-- Script SQL: voci di menu Lotti e Matricole
-- Compatibile con SQL Server
-- m261009_180000_add_lotti_matricole_menu
-- =============================================================

IF OBJECT_ID('dbo.dash_menu', 'U') IS NOT NULL
BEGIN
    DECLARE @parent INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-lotti')
        INSERT INTO [dbo].[dash_menu] ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
        VALUES ('micro-lotti', 'Lotti', 'boxes', 'mglotto/index', @parent, 0, 17, 1, 1, GETDATE());

    IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-matricole')
        INSERT INTO [dbo].[dash_menu] ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
        VALUES ('micro-matricole', 'Matricole', 'barcode', 'mgmatricola/index', @parent, 0, 18, 1, 1, GETDATE());
END
GO
