-- =============================================================
-- Script SQL: tabella api_token (Bearer token servizio REST)
-- Compatibile con SQL Server
-- m260921_180000_create_api_token
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='api_token')
BEGIN
    CREATE TABLE [dbo].[api_token] (
        [id]           INT IDENTITY(1,1) NOT NULL,
        [user_id]      INT NULL,
        [descrizione]  NVARCHAR(200) NULL,
        [token_hash]   NVARCHAR(64) NOT NULL,
        [scopes]       NVARCHAR(500) NULL,
        [expires_at]   BIGINT NULL,
        [created_at]   BIGINT NOT NULL,
        [last_used_at] BIGINT NULL,
        CONSTRAINT [PK_api_token] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE UNIQUE INDEX [idx-api_token-token_hash] ON [dbo].[api_token] ([token_hash] ASC);
    CREATE INDEX [idx-api_token-user_id] ON [dbo].[api_token] ([user_id] ASC);
END
GO
