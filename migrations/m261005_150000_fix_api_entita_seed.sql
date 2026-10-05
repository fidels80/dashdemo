-- Correzione dei vincoli di integrità del registro API (gemello SQL Server di
-- m261005_150000_fix_api_entita_seed.php).

-- La regola era priva di senso: mg_articolo_um non è il padre di quella colonna.
-- Disattivata, non cancellata, per non perdere lo storico.
UPDATE dash_api_rel SET attiva = 0
 WHERE entita = 'articoli-um'
   AND tabella = 'mg_documento_riga'
   AND colonna = 'id_unita_misura';

-- Le regole figlie si sostituiscono a quelle riferimento già presenti.
DELETE FROM dash_api_rel
 WHERE entita = 'anagrafiche' AND tipo = 'figlio'
   AND tabella = 'mg_anagrafica_contatto' AND colonna = 'id_anagrafica';

DELETE FROM dash_api_rel
 WHERE entita = 'articoli'
   AND tabella = 'mg_articolo_um' AND colonna = 'id_articolo';

DELETE FROM dash_api_rel
 WHERE entita = 'attributi-articolo'
   AND tabella = 'mg_modello_tessuto' AND colonna = 'id_modello';

-- Regole derivate dalle chiavi esterne reali (ON DELETE CASCADE).
INSERT INTO dash_api_rel (entita, tipo, tabella, colonna, etichetta, cascade, attiva, ordinamento)
VALUES
    ('anagrafiche',       'figlio', 'mg_anagrafica_contatto', 'id_anagrafica',   N'contatti di questa anagrafica',          1, 1, 40),
    ('articoli',          'figlio', 'mg_articolo_um',         'id_articolo',     N'conversioni di unita di misura',         1, 1, 20),
    ('attributi-articolo','figlio', 'mg_modello_tessuto',     'id_modello',      N'abbinamenti con questo modello',         1, 1, 60);
