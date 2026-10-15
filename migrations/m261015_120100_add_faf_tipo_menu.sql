-- =============================================================
-- Script SQL: tipo documento FAF, permesso e voce di menu
-- Compatibile con SQL Server
-- m261015_120100_add_faf_tipo_menu
-- =============================================================

-- Tipo documento "FAF" (fattura ricevuta da fornitore)
IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_tipo_documento] WHERE [codice] = 'FAF')
BEGIN
    INSERT INTO [dbo].[mg_tipo_documento]
        ([codice],[descrizione],[anno],[contatore],[usa_progressivo],[congruita],[attivo],
         [destinazione],[crea_scadenze],[mostra_varianti],[preleva_rapportini],[crea_articoli],
         [crea_anagrafiche],[mostra_matrice],[gestione_seriali],[gestione_data_consegna],
         [gestione_lotti],[elettronico],[segno_movimento],[varia_impegnato],[varia_ordinato],[created_at])
    VALUES
        ('FAF','Fattura fornitore',YEAR(GETDATE()),0,0,0,1,
         'fornitore',1,0,0,0,
         0,0,0,0,
         0,0,'nessuno','nessuno','nessuno',GETDATE());
END
GO

-- Permesso ACL per il controller "fatturafornitore"
IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_permesso] WHERE [codice] = 'fatturafornitore')
BEGIN
    INSERT INTO [dbo].[dash_permesso] ([codice],[descrizione],[gruppo],[ordine],[attivo],[created_at])
    VALUES ('fatturafornitore','Fatture fornitori','Microgestionale',36,1,GETDATE());
END
GO

-- Voce di menu "Fatture fornitori" sotto il gruppo Documentale
IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'micro-fatturefornitore')
BEGIN
    DECLARE @parent INT;
    SELECT @parent = [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro-documentale';
    IF @parent IS NULL
        SELECT @parent = [id] FROM [dbo].[dash_menu] WHERE [codice] = 'micro';

    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('micro-fatturefornitore','Fatture fornitori','file-invoice','fatturafornitore/index',@parent,0,8,1,1,GETDATE());
END
GO
