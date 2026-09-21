-- =============================================================
-- Script SQL: ACL per form/funzione (dash_permesso + dash_permesso_utente)
-- Compatibile con SQL Server
-- m260921_200000_create_dash_permessi
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_permesso')
BEGIN
    CREATE TABLE [dbo].[dash_permesso] (
        [id]          INT IDENTITY(1,1) NOT NULL,
        [codice]      NVARCHAR(50) NOT NULL,
        [descrizione] NVARCHAR(150) NOT NULL,
        [gruppo]      NVARCHAR(50) NULL,
        [ordine]      INT NOT NULL CONSTRAINT [DF_dash_permesso_ordine] DEFAULT(0),
        [attivo]      BIT NOT NULL CONSTRAINT [DF_dash_permesso_attivo] DEFAULT(1),
        [created_at]  DATETIME NULL,
        CONSTRAINT [PK_dash_permesso] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-dash_permesso-codice] ON [dbo].[dash_permesso] ([codice] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_permesso_utente')
BEGIN
    CREATE TABLE [dbo].[dash_permesso_utente] (
        [id]         INT IDENTITY(1,1) NOT NULL,
        [user_id]    INT NOT NULL,
        [permesso_id] INT NOT NULL,
        [can_view]   BIT NOT NULL CONSTRAINT [DF_dpu_can_view] DEFAULT(0),
        [can_create] BIT NOT NULL CONSTRAINT [DF_dpu_can_create] DEFAULT(0),
        [can_update] BIT NOT NULL CONSTRAINT [DF_dpu_can_update] DEFAULT(0),
        [can_delete] BIT NOT NULL CONSTRAINT [DF_dpu_can_delete] DEFAULT(0),
        CONSTRAINT [PK_dash_permesso_utente] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-dash_permesso_utente-unico] ON [dbo].[dash_permesso_utente] ([user_id] ASC, [permesso_id] ASC);
END
GO
