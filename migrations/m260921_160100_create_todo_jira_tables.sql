-- =============================================================
-- Script SQL: tabelle ToDo stile Jira
-- Compatibile con SQL Server
-- m260921_160100_create_todo_jira_tables
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='to_do_tipo')
BEGIN
    CREATE TABLE [dbo].[to_do_tipo] (
        [id]     INT IDENTITY(1,1) NOT NULL,
        [tipo]   NVARCHAR(50) NOT NULL,
        [icona]  NVARCHAR(50) NULL,
        [colore] NVARCHAR(20) NULL,
        [ordine] INT NULL,
        CONSTRAINT [PK_to_do_tipo] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    INSERT INTO [dbo].[to_do_tipo] ([tipo],[icona],[colore],[ordine]) VALUES
        (N'Task',    N'fa-tasks',   N'#0d6efd', 1),
        (N'Bug',     N'fa-bug',     N'#dc3545', 2),
        (N'Story',   N'fa-bookmark',N'#198754', 3),
        (N'Epic',    N'fa-bolt',    N'#6f42c1', 4),
        (N'Subtask', N'fa-sitemap', N'#6c757d', 5);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='to_do_sprint')
BEGIN
    CREATE TABLE [dbo].[to_do_sprint] (
        [id]          INT IDENTITY(1,1) NOT NULL,
        [nome]        NVARCHAR(100) NOT NULL,
        [obiettivo]   NVARCHAR(500) NULL,
        [data_inizio] DATE NULL,
        [data_fine]   DATE NULL,
        [stato]       NVARCHAR(20) NULL CONSTRAINT [DF_to_do_sprint_stato] DEFAULT(N'pianificato'),
        [created_at]  DATETIME NULL,
        CONSTRAINT [PK_to_do_sprint] PRIMARY KEY CLUSTERED ([id] ASC)
    );
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='to_do_attivita')
BEGIN
    CREATE TABLE [dbo].[to_do_attivita] (
        [id]           INT IDENTITY(1,1) NOT NULL,
        [id_todo]      NVARCHAR(50) NOT NULL,
        [user]         NVARCHAR(50) NULL,
        [azione]       NVARCHAR(50) NULL,
        [campo]        NVARCHAR(50) NULL,
        [valore_prima] NVARCHAR(500) NULL,
        [valore_dopo]  NVARCHAR(500) NULL,
        [data]         DATETIME NULL,
        CONSTRAINT [PK_to_do_attivita] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-to_do_attivita-id_todo] ON [dbo].[to_do_attivita] ([id_todo] ASC);
END
GO
