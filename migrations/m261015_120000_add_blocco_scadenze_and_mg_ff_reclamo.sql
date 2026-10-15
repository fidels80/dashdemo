-- =============================================================
-- Script SQL: blocco scadenze (mg_scadenza) + reclami fatture (mg_ff_reclamo)
-- Compatibile con SQL Server
-- m261015_120000_add_blocco_scadenze_and_mg_ff_reclamo
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_scadenza' AND COLUMN_NAME='bloccata')
    ALTER TABLE [dbo].[mg_scadenza] ADD [bloccata] BIT NOT NULL
        CONSTRAINT [DF_mg_scadenza_bloccata] DEFAULT(0);
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_scadenza' AND COLUMN_NAME='bloccata_at')
    ALTER TABLE [dbo].[mg_scadenza] ADD [bloccata_at] DATETIME NULL;
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_scadenza' AND COLUMN_NAME='bloccata_by')
    ALTER TABLE [dbo].[mg_scadenza] ADD [bloccata_by] INT NULL;
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_ff_reclamo')
BEGIN
    CREATE TABLE [dbo].[mg_ff_reclamo] (
        [id]           INT IDENTITY(1,1) NOT NULL,
        [id_documento] INT NOT NULL,
        [id_utente]    INT NULL,
        [oggetto]      NVARCHAR(200) NOT NULL,
        [messaggio]    NVARCHAR(MAX) NULL,
        [destinatari]  NVARCHAR(400) NOT NULL,
        [esito]        BIT NOT NULL CONSTRAINT [DF_mg_ff_reclamo_esito] DEFAULT(0),
        [errore]       NVARCHAR(500) NULL,
        [inviata_at]   DATETIME NULL,
        [created_at]   DATETIME NULL,
        CONSTRAINT [PK_mg_ff_reclamo] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-mg_ff_reclamo-documento] ON [dbo].[mg_ff_reclamo] ([id_documento] ASC);

    ALTER TABLE [dbo].[mg_ff_reclamo] WITH CHECK
        ADD CONSTRAINT [fk-mg_ff_reclamo-documento]
        FOREIGN KEY ([id_documento]) REFERENCES [dbo].[mg_documento] ([id]) ON DELETE CASCADE;
END
GO
