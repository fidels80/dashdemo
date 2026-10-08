-- =============================================================
-- Script SQL: voce menu Dashboard (cruscotto di riepilogo)
-- Voce radice visibile a tutti, in cima al menu laterale;
-- apre dashboard/index (pagina post-login). Compatibile con SQL Server
-- m261008_150000_add_menu_dashboard
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'dashboard')
    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('dashboard','Dashboard','tachometer-alt','dashboard/index',NULL,0,0,1,1,GETDATE());
GO
