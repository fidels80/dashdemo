-- ------------------------------------------------------------------
-- Aggiunge il flag "utente esterno" (portale CRM) alla tabella user.
-- Script gemello della migrazione m261016_100100_add_is_esterno_to_user
-- ------------------------------------------------------------------

IF COL_LENGTH('dbo.[user]', 'is_esterno') IS NULL
BEGIN
    ALTER TABLE dbo.[user] ADD is_esterno BIT NOT NULL CONSTRAINT df_user_is_esterno DEFAULT (0);
END;
GO
