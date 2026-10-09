-- =============================================================
-- Script SQL: seed 20 magazzini aggiuntivi (MAG02..MAG21)
-- Compatibile con SQL Server
-- m261009_150000_seed_mg_magazzino
-- =============================================================

IF OBJECT_ID('dbo.mg_magazzino', 'U') IS NOT NULL
BEGIN
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG02')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG02', 'Magazzino 02', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG03')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG03', 'Magazzino 03', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG04')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG04', 'Magazzino 04', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG05')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG05', 'Magazzino 05', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG06')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG06', 'Magazzino 06', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG07')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG07', 'Magazzino 07', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG08')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG08', 'Magazzino 08', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG09')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG09', 'Magazzino 09', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG10')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG10', 'Magazzino 10', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG11')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG11', 'Magazzino 11', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG12')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG12', 'Magazzino 12', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG13')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG13', 'Magazzino 13', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG14')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG14', 'Magazzino 14', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG15')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG15', 'Magazzino 15', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG16')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG16', 'Magazzino 16', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG17')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG17', 'Magazzino 17', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG18')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG18', 'Magazzino 18', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG19')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG19', 'Magazzino 19', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG20')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG20', 'Magazzino 20', 1);
    IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_magazzino] WHERE [codice] = 'MAG21')
        INSERT INTO [dbo].[mg_magazzino] ([codice], [descrizione], [attivo]) VALUES ('MAG21', 'Magazzino 21', 1);
END
GO
