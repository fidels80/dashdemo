-- =============================================================
-- Script SQL: tabella aliquote IVA (codice, descrizione, %)
-- Compatibile con SQL Server
-- m260921_230700_create_mg_aliquota_iva
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_aliquota_iva')
BEGIN
    CREATE TABLE [dbo].[mg_aliquota_iva] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(20) NOT NULL,
        [descrizione] VARCHAR(100) NOT NULL,
        [percentuale] DECIMAL(9,2) NOT NULL CONSTRAINT [DF_mg_aliquota_iva_perc] DEFAULT(0),
        [attivo] BIT NOT NULL CONSTRAINT [DF_mg_aliquota_iva_attivo] DEFAULT(1)
    );
    CREATE UNIQUE INDEX [idx-mg_aliquota_iva-codice] ON [dbo].[mg_aliquota_iva] ([codice]);
END
GO

IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_aliquota_iva])
BEGIN
    INSERT INTO [dbo].[mg_aliquota_iva] ([codice],[descrizione],[percentuale],[attivo]) VALUES
        ('22','IVA 22%',22.00,1),
        ('10','IVA 10%',10.00,1),
        ('05','IVA 5%',5.00,1),
        ('04','IVA 4%',4.00,1),
        ('00','Esente / Non imponibile',0.00,1);
END
GO
