-- =============================================================
-- Script SQL: tabella user_trusted_device (dispositivi fidati 2FA)
-- Compatibile con SQL Server
-- Eseguito manualmente OPPURE tramite la migrazione Yii2
-- m260921_150100_add_trusted_device_to_user
-- =============================================================

IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'user_trusted_device'
)
BEGIN
    CREATE TABLE [dbo].[user_trusted_device] (
        [id]           INT IDENTITY(1,1) NOT NULL,
        [user_id]      INT NOT NULL,
        [token_hash]   NVARCHAR(64)  NOT NULL,
        [expires_at]   BIGINT NOT NULL,
        [user_agent]   NVARCHAR(500) NULL,
        [created_at]   BIGINT NOT NULL,
        [last_used_at] BIGINT NULL,
        CONSTRAINT [PK_user_trusted_device] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE UNIQUE INDEX [idx-user_trusted_device-token_hash]
        ON [dbo].[user_trusted_device] ([token_hash] ASC);

    CREATE INDEX [idx-user_trusted_device-user_id]
        ON [dbo].[user_trusted_device] ([user_id] ASC);

    ALTER TABLE [dbo].[user_trusted_device] WITH CHECK
        ADD CONSTRAINT [fk-user_trusted_device-user_id]
        FOREIGN KEY ([user_id]) REFERENCES [dbo].[user] ([id])
        ON DELETE CASCADE;

    ALTER TABLE [dbo].[user_trusted_device] CHECK
        CONSTRAINT [fk-user_trusted_device-user_id];
END
GO
