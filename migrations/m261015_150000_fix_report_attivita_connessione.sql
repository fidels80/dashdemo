-- =============================================================
-- Script SQL: correzione connessione fonte cross-db di esempio
-- (sa non utilizzabile -> usa la connessione 'db', utente yii)
-- m261015_150000_fix_report_attivita_connessione
-- =============================================================

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_report_fonte')
BEGIN
    UPDATE dash_report_fonte
        SET connessione='db', updated_at=GETDATE()
        WHERE codice='flt_attivita';
END
GO
