-- =============================================================
-- Script SQL: flag "totale: false" sulla colonna IVA % del report
-- articoli (la percentuale non va sommata nei totali)
-- m261015_170000_fix_report_articoli_iva
-- =============================================================

IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_report')
BEGIN
    UPDATE dash_report
        SET definizione = REPLACE(
                definizione,
                '"alias":"iva","label":"IVA %","tipo":"numero","formato":0}',
                '"alias":"iva","label":"IVA %","tipo":"numero","formato":0,"totale":false}')
        , updated_at = GETDATE()
        WHERE codice='rep_articoli'
          AND definizione LIKE '%"alias":"iva","label":"IVA %","tipo":"numero","formato":0}%';
END
GO
