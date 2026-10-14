-- =============================================================
-- Script SQL: tipo documento DCF, permesso e voce di menu
-- Compatibile con SQL Server
-- m261014_120100_add_dcf_tipo_menu
-- =============================================================

-- Tipo documento "DCF" (documento di trasporto a fornitore)
IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_tipo_documento] WHERE [codice] = 'DCF')
BEGIN
    INSERT INTO [dbo].[mg_tipo_documento]
        ([codice],[descrizione],[anno],[contatore],[usa_progressivo],[congruita],[attivo],
         [destinazione],[crea_scadenze],[mostra_varianti],[preleva_rapportini],[crea_articoli],
         [crea_anagrafiche],[mostra_matrice],[gestione_seriali],[gestione_data_consegna],
         [gestione_lotti],[elettronico],[segno_movimento],[varia_impegnato],[varia_ordinato],[created_at])
    VALUES
        ('DCF','Documento di trasporto a fornitore',YEAR(GETDATE()),0,1,0,1,
         'fornitore',0,0,0,0,
         0,0,0,0,
         0,0,'nessuno','nessuno','nessuno',GETDATE());
END
GO

-- Permesso ACL per il controller "dcf"
IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE [codice] = 'dcf')
BEGIN
    INSERT INTO [dbo].[dash_permesso] ([codice],[descrizione],[gruppo],[ordine],[attivo],[created_at])
    VALUES ('dcf','Controllo documenti fornitore','Microgestionale',35,1,GETDATE());
END
GO

-- Voce di menu "Controllo DCF" sotto il gruppo Documentale
IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-dcf')
BEGIN
    DECLARE @parent INT;
    SELECT @parent = [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro-documentale';
    IF @parent IS NULL
        SELECT @parent = [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro';

    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('micro-dcf','Controllo documenti fornitore','truck','dcf/index',@parent,0,7,1,1,GETDATE());
END
GO
