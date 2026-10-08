-- =============================================================
-- Script SQL: tabella movimenti di magazzino
-- Compatibile con SQL Server
-- m261008_140000_create_mg_movimentimagazzino
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_movimentimagazzino')
BEGIN
    CREATE TABLE [dbo].[mg_movimentimagazzino] (
        [id]                    INT IDENTITY(1,1) NOT NULL,
        [id_documento_riga]     INT NOT NULL,
        [codice_articolo]       NVARCHAR(25) NULL,
        [qta]                   DECIMAL(18,4) NULL CONSTRAINT [DF_mg_movimentimagazzino_qta] DEFAULT(0),
        [id_unita_misura]       INT NULL,
        [um]                    NVARCHAR(10) NULL,
        [fattore]               DECIMAL(18,4) NOT NULL CONSTRAINT [DF_mg_movimentimagazzino_fattore] DEFAULT(1),
        [id_magazzino_partenza] INT NULL,
        [id_magazzino_arrivo]   INT NULL,
        [segno_movimento]       NVARCHAR(10) NOT NULL CONSTRAINT [DF_mg_movimentimagazzino_segno] DEFAULT('nessuno'),
        [varia_impegnato]       NVARCHAR(10) NOT NULL CONSTRAINT [DF_mg_movimentimagazzino_imp] DEFAULT('nessuno'),
        [varia_ordinato]        NVARCHAR(10) NOT NULL CONSTRAINT [DF_mg_movimentimagazzino_ord] DEFAULT('nessuno'),
        [qta_movimento]         DECIMAL(18,4) NULL CONSTRAINT [DF_mg_movimentimagazzino_qmov] DEFAULT(0),
        [qta_impegnato]         DECIMAL(18,4) NULL CONSTRAINT [DF_mg_movimentimagazzino_qimp] DEFAULT(0),
        [qta_ordinato]          DECIMAL(18,4) NULL CONSTRAINT [DF_mg_movimentimagazzino_qord] DEFAULT(0),
        CONSTRAINT [PK_mg_movimentimagazzino] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-mg_movimentimagazzino-riga] ON [dbo].[mg_movimentimagazzino] ([id_documento_riga] ASC);

    ALTER TABLE [dbo].[mg_movimentimagazzino] WITH CHECK
        ADD CONSTRAINT [fk-mg_movimentimagazzino-riga]
        FOREIGN KEY ([id_documento_riga]) REFERENCES [dbo].[mg_documento_riga] ([id]) ON DELETE CASCADE;
    ALTER TABLE [dbo].[mg_movimentimagazzino] WITH CHECK
        ADD CONSTRAINT [fk-mg_movimentimagazzino-um]
        FOREIGN KEY ([id_unita_misura]) REFERENCES [dbo].[mg_unita_misura] ([id]) ON DELETE NO ACTION;
    ALTER TABLE [dbo].[mg_movimentimagazzino] WITH CHECK
        ADD CONSTRAINT [fk-mg_movimentimagazzino-magazzino_partenza]
        FOREIGN KEY ([id_magazzino_partenza]) REFERENCES [dbo].[mg_magazzino] ([id]) ON DELETE NO ACTION;
    ALTER TABLE [dbo].[mg_movimentimagazzino] WITH CHECK
        ADD CONSTRAINT [fk-mg_movimentimagazzino-magazzino_arrivo]
        FOREIGN KEY ([id_magazzino_arrivo]) REFERENCES [dbo].[mg_magazzino] ([id]) ON DELETE NO ACTION;
END
GO
