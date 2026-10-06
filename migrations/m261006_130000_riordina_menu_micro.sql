-- =============================================================
-- Script SQL: riordino menu Microgestionale
-- Crea i gruppi Anagrafiche / Documentale / Prodotti e vi sposta
-- le voci esistenti; disattiva il duplicato "Sotto Commessa".
-- Compatibile con SQL Server
-- m261006_130000_riordina_menu_micro
-- =============================================================

DECLARE @micro INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro');

IF @micro IS NULL
    RETURN;

-- -------------------------------------------------------------
-- Gruppi di secondo livello
-- -------------------------------------------------------------
IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-anagrafiche')
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-anagrafiche', 'Anagrafiche', 'address-book', NULL, @micro, 0, 1, 1, 1, GETDATE());
ELSE
    UPDATE [dbo].[dash_menu]
       SET [genitore_id] = @micro, [ordine] = 1, [attivo] = 1
     WHERE [codice] = 'micro-anagrafiche';

IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-documentale')
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-documentale', 'Documentale', 'folder-open', NULL, @micro, 0, 2, 1, 1, GETDATE());
ELSE
    UPDATE [dbo].[dash_menu]
       SET [genitore_id] = @micro, [ordine] = 2, [attivo] = 1
     WHERE [codice] = 'micro-documentale';

IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-prodotti')
    INSERT INTO [dbo].[dash_menu]
        ([codice], [label], [icona], [url], [genitore_id], [livello_min], [ordine], [per_tutti], [attivo], [created_at])
    VALUES
        ('micro-prodotti', 'Prodotti', 'cubes', NULL, @micro, 0, 3, 1, 1, GETDATE());
ELSE
    UPDATE [dbo].[dash_menu]
       SET [genitore_id] = @micro, [ordine] = 3, [attivo] = 1
     WHERE [codice] = 'micro-prodotti';

DECLARE @anagrafiche  INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro-anagrafiche');
DECLARE @documentale  INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro-documentale');
DECLARE @prodotti     INT = (SELECT [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro-prodotti');

-- -------------------------------------------------------------
-- Anagrafiche
-- -------------------------------------------------------------
UPDATE [dbo].[dash_menu] SET [genitore_id] = @anagrafiche, [ordine] = 1 WHERE [codice] = 'micro-anag';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @anagrafiche, [ordine] = 2 WHERE [codice] = 'micro-contatti';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @anagrafiche, [ordine] = 3 WHERE [codice] = 'micro-tipicontatto';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @anagrafiche, [ordine] = 4 WHERE [codice] = 'micro-commesse';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @anagrafiche, [ordine] = 5 WHERE [codice] = 'micro-sottocommesse';

-- -------------------------------------------------------------
-- Documentale
-- -------------------------------------------------------------
UPDATE [dbo].[dash_menu] SET [genitore_id] = @documentale, [ordine] = 1 WHERE [codice] = 'micro-doc';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @documentale, [ordine] = 2 WHERE [codice] = 'planning-rapportini';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @documentale, [ordine] = 3 WHERE [codice] = 'micro-tipi';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @documentale, [ordine] = 4 WHERE [codice] = 'micro-metodipag';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @documentale, [ordine] = 5 WHERE [codice] = 'micro-tipipag';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @documentale, [ordine] = 6 WHERE [codice] = 'micro-aliquote';

-- -------------------------------------------------------------
-- Prodotti
-- -------------------------------------------------------------
UPDATE [dbo].[dash_menu] SET [genitore_id] = @prodotti, [ordine] = 1 WHERE [codice] = 'micro-art';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @prodotti, [ordine] = 2 WHERE [codice] = 'micro-attributi';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @prodotti, [ordine] = 3 WHERE [codice] = 'micro-um';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @prodotti, [ordine] = 4 WHERE [codice] = 'micro-modelli';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @prodotti, [ordine] = 5 WHERE [codice] = 'micro-wizard';
UPDATE [dbo].[dash_menu] SET [genitore_id] = @prodotti, [ordine] = 6 WHERE [codice] = 'micro-magazzini';

-- -------------------------------------------------------------
-- Duplicato "Sotto Commessa": lo disattiviamo (non lo cancelliamo)
-- -------------------------------------------------------------
UPDATE [dbo].[dash_menu] SET [attivo] = 0 WHERE [codice] = 'mgsottocommessa';
GO
