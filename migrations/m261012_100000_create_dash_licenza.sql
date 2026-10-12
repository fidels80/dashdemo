-- =============================================================
-- Script SQL: stato licenza + log eventi licenza/aggiornamenti
-- Compatibile con SQL Server
-- m261012_100000_create_dash_licenza
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_licenza')
BEGIN
    CREATE TABLE [dbo].[dash_licenza] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [license_key] VARCHAR(64) NULL,
        [machine_id] VARCHAR(64) NULL,
        [product] VARCHAR(50) NULL,
        [tier] VARCHAR(20) NULL,
        [features] NVARCHAR(MAX) NULL,
        [expires_at] DATETIME NULL,
        [status] VARCHAR(20) NULL,
        [signature] VARCHAR(255) NULL,
        [first_run_at] DATETIME NULL,
        [activated_at] DATETIME NULL,
        [last_check_at] DATETIME NULL,
        [last_check_ok] DATETIME NULL,
        [last_error] VARCHAR(500) NULL,
        [update_version] VARCHAR(20) NULL,
        [updated_at] DATETIME NULL
    );
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_licenza_log')
BEGIN
    CREATE TABLE [dbo].[dash_licenza_log] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [event_type] VARCHAR(50) NOT NULL,
        [detail] VARCHAR(1000) NULL,
        [created_at] DATETIME NULL
    );
    CREATE INDEX [idx-dash_licenza_log-created] ON [dbo].[dash_licenza_log] ([created_at]);
END
GO
