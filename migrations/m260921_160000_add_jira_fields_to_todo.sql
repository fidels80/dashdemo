-- =============================================================
-- Script SQL: campi stile Jira nella tabella [to_do_main]
-- Compatibile con SQL Server
-- m260921_160000_add_jira_fields_to_todo
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='to_do_main' AND COLUMN_NAME='tipo')
    ALTER TABLE [dbo].[to_do_main] ADD [tipo] INT NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='to_do_main' AND COLUMN_NAME='story_points')
    ALTER TABLE [dbo].[to_do_main] ADD [story_points] INT NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='to_do_main' AND COLUMN_NAME='sprint_id')
    ALTER TABLE [dbo].[to_do_main] ADD [sprint_id] INT NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='to_do_main' AND COLUMN_NAME='posizione')
    ALTER TABLE [dbo].[to_do_main] ADD [posizione] INT NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='to_do_main' AND COLUMN_NAME='reporter')
    ALTER TABLE [dbo].[to_do_main] ADD [reporter] NVARCHAR(30) NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='to_do_main' AND COLUMN_NAME='created_at')
    ALTER TABLE [dbo].[to_do_main] ADD [created_at] DATETIME NULL;
GO
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='to_do_main' AND COLUMN_NAME='updated_at')
    ALTER TABLE [dbo].[to_do_main] ADD [updated_at] DATETIME NULL;
GO
