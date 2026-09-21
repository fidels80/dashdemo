-- =============================================================
-- Script SQL: menu laterale dinamico (dash_menu + dash_menu_utente)
-- Compatibile con SQL Server
-- m260921_190000_create_dash_menu
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_menu')
BEGIN
    CREATE TABLE [dbo].[dash_menu] (
        [id]          INT IDENTITY(1,1) NOT NULL,
        [codice]      NVARCHAR(50) NOT NULL,
        [label]       NVARCHAR(100) NOT NULL,
        [icona]       NVARCHAR(50) NULL,
        [url]         NVARCHAR(200) NULL,
        [genitore_id] INT NULL,
        [livello_min] INT NOT NULL CONSTRAINT [DF_dash_menu_livello_min] DEFAULT(0),
        [ordine]      INT NOT NULL CONSTRAINT [DF_dash_menu_ordine] DEFAULT(0),
        [per_tutti]   BIT NOT NULL CONSTRAINT [DF_dash_menu_per_tutti] DEFAULT(1),
        [attivo]      BIT NOT NULL CONSTRAINT [DF_dash_menu_attivo] DEFAULT(1),
        [created_at]  DATETIME NULL,
        CONSTRAINT [PK_dash_menu] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE UNIQUE INDEX [idx-dash_menu-codice] ON [dbo].[dash_menu] ([codice] ASC);
    CREATE INDEX [idx-dash_menu-genitore] ON [dbo].[dash_menu] ([genitore_id] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_menu_utente')
BEGIN
    CREATE TABLE [dbo].[dash_menu_utente] (
        [id]      INT IDENTITY(1,1) NOT NULL,
        [user_id] INT NOT NULL,
        [menu_id] INT NOT NULL,
        CONSTRAINT [PK_dash_menu_utente] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE UNIQUE INDEX [idx-dash_menu_utente-unico] ON [dbo].[dash_menu_utente] ([user_id] ASC, [menu_id] ASC);
END
GO
