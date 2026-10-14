-- =============================================================
-- Script SQL: tabelle controllo (riscontro) DCF a fornitore
-- Compatibile con SQL Server
-- m261014_120000_create_mg_dcf_controllo
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_dcf_controllo')
BEGIN
    CREATE TABLE [dbo].[mg_dcf_controllo] (
        [id]                       INT IDENTITY(1,1) NOT NULL,
        [id_documento]             INT NOT NULL,
        [stato]                    NVARCHAR(20) NOT NULL CONSTRAINT [DF_mg_dcf_controllo_stato] DEFAULT('da_controllare'),
        [totale_pezzi_documento]   DECIMAL(18,4) NOT NULL CONSTRAINT [DF_mg_dcf_controllo_tpd] DEFAULT(0),
        [totale_documento]         DECIMAL(18,2) NOT NULL CONSTRAINT [DF_mg_dcf_controllo_td] DEFAULT(0),
        [totale_pezzi_riscontrati] DECIMAL(18,4) NOT NULL CONSTRAINT [DF_mg_dcf_controllo_tpr] DEFAULT(0),
        [righe_totali]             INT NOT NULL CONSTRAINT [DF_mg_dcf_controllo_rt] DEFAULT(0),
        [righe_riscontrate]        INT NOT NULL CONSTRAINT [DF_mg_dcf_controllo_rr] DEFAULT(0),
        [righe_differenza]         INT NOT NULL CONSTRAINT [DF_mg_dcf_controllo_rd] DEFAULT(0),
        [email_inviata]            BIT NOT NULL CONSTRAINT [DF_mg_dcf_controllo_ei] DEFAULT(0),
        [email_destinatari]        NVARCHAR(400) NULL,
        [email_oggetto]            NVARCHAR(200) NULL,
        [email_inviata_at]         DATETIME NULL,
        [id_utente]                INT NULL,
        [created_at]               DATETIME NULL,
        [updated_at]               DATETIME NULL,
        CONSTRAINT [PK_mg_dcf_controllo] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE UNIQUE INDEX [uidx-mg_dcf_controllo-documento] ON [dbo].[mg_dcf_controllo] ([id_documento] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_dcf_controllo_riga')
BEGIN
    CREATE TABLE [dbo].[mg_dcf_controllo_riga] (
        [id]              INT IDENTITY(1,1) NOT NULL,
        [id_controllo]    INT NOT NULL,
        [id_riga]         INT NOT NULL,
        [qta_documento]   DECIMAL(18,4) NOT NULL CONSTRAINT [DF_mg_dcf_controllo_riga_qd] DEFAULT(0),
        [qta_riscontrata] DECIMAL(18,4) NULL,
        [differenza]      DECIMAL(18,4) NOT NULL CONSTRAINT [DF_mg_dcf_controllo_riga_diff] DEFAULT(0),
        [note]            NVARCHAR(500) NULL,
        [created_at]      DATETIME NULL,
        [updated_at]      DATETIME NULL,
        CONSTRAINT [PK_mg_dcf_controllo_riga] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-mg_dcf_controllo_riga-controllo] ON [dbo].[mg_dcf_controllo_riga] ([id_controllo] ASC);
    CREATE INDEX [idx-mg_dcf_controllo_riga-riga] ON [dbo].[mg_dcf_controllo_riga] ([id_riga] ASC);

    ALTER TABLE [dbo].[mg_dcf_controllo_riga] WITH CHECK
        ADD CONSTRAINT [fk-mg_dcf_controllo_riga-controllo]
        FOREIGN KEY ([id_controllo]) REFERENCES [dbo].[mg_dcf_controllo] ([id]) ON DELETE CASCADE;
END
GO
