-- =============================================================
-- Script SQL: voce di menu "Licenza e aggiornamenti" (supervisori)
-- Compatibile con SQL Server
-- m261012_110000_add_licenza_menu
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM [dbo].[dash_menu] WHERE [codice] = 'sistema-licenza')
BEGIN
    INSERT INTO [dbo].[dash_menu]
        ([codice],[label],[icona],[url],[genitore_id],[livello_min],[ordine],[per_tutti],[attivo],[created_at])
    VALUES
        ('sistema-licenza','Licenza e aggiornamenti','key','licenza/index',NULL,100,90,0,1,GETDATE());
END
GO
