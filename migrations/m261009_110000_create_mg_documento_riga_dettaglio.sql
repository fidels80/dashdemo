-- =============================================================
-- Script SQL: tabella dettaglio seriali / date consegna righe documento
-- Compatibile con SQL Server
-- m261009_110000_create_mg_documento_riga_dettaglio
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_documento_riga_dettaglio')
BEGIN
    CREATE TABLE [dbo].[mg_documento_riga_dettaglio] (
        [id]                INT IDENTITY(1,1) NOT NULL,
        [id_documento_riga] INT NOT NULL,
        [seriale]           NVARCHAR(100) NULL,
        [data_consegna]     DATE NULL,
        [qta]               DECIMAL(18,4) NULL CONSTRAINT [DF_mg_documento_riga_dettaglio_qta] DEFAULT(1),
        [ordine]            INT NULL CONSTRAINT [DF_mg_documento_riga_dettaglio_ordine] DEFAULT(0),
        CONSTRAINT [PK_mg_documento_riga_dettaglio] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-mg_documento_riga_dettaglio-riga] ON [dbo].[mg_documento_riga_dettaglio] ([id_documento_riga] ASC);

    ALTER TABLE [dbo].[mg_documento_riga_dettaglio] WITH CHECK
        ADD CONSTRAINT [fk-mg_documento_riga_dettaglio-riga]
        FOREIGN KEY ([id_documento_riga]) REFERENCES [dbo].[mg_documento_riga] ([id]) ON DELETE CASCADE;
END
GO
