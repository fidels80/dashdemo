-- Rimuove la regola duplicata su mg_anagrafica_contatto (gemello SQL Server di
-- m261005_160000_fix_api_rel_duplicati.php).
-- La migrazione precedente aveva lasciato il vecchio "riferimento" accanto al
-- nuovo "figlio": il riferimento non e' mai rimosso in automatico, quindi la
-- cancellazione a cascata delle anagrafiche falliva.

DELETE FROM dash_api_rel
 WHERE entita = 'anagrafiche'
   AND tabella = 'mg_anagrafica_contatto'
   AND colonna = 'id_anagrafica'
   AND tipo = 'riferimento';
