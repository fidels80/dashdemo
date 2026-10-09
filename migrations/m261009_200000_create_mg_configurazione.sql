-- =============================================================
-- Script SQL: tabella di configurazione generica (codice/valore)
-- Compatibile con SQL Server
-- m261009_200000_create_mg_configurazione
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_configurazione')
BEGIN
    CREATE TABLE [dbo].[mg_configurazione] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(100) NOT NULL,
        [descrizione] VARCHAR(255) NOT NULL,
        [valore] VARCHAR(500) NULL
    );
    CREATE UNIQUE INDEX [idx-mg_configurazione-codice] ON [dbo].[mg_configurazione] ([codice]);
END
GO

IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_configurazione])
BEGIN
    INSERT INTO [dbo].[mg_configurazione] ([codice],[descrizione],[valore]) VALUES
        ('fe.cedente.id_paese','Cedente - Paese (ISO)','IT'),
        ('fe.cedente.denominazione','Cedente - Denominazione / Ragione sociale',''),
        ('fe.cedente.partita_iva','Cedente - Partita IVA',''),
        ('fe.cedente.codice_fiscale','Cedente - Codice fiscale',''),
        ('fe.cedente.regime_fiscale','Cedente - Regime fiscale (RF01...)','RF01'),
        ('fe.cedente.indirizzo','Cedente - Indirizzo (via/piazza)',''),
        ('fe.cedente.numero_civico','Cedente - Numero civico',''),
        ('fe.cedente.cap','Cedente - CAP',''),
        ('fe.cedente.comune','Cedente - Comune',''),
        ('fe.cedente.provincia','Cedente - Provincia (2 lettere)',''),
        ('fe.cedente.nazione','Cedente - Nazione (ISO)','IT'),
        ('fe.cedente.telefono','Cedente - Telefono',''),
        ('fe.cedente.email','Cedente - Email',''),
        ('fe.trasmissione.id_paese','Trasmissione - IdPaese del trasmittente','IT'),
        ('fe.trasmissione.id_codice','Trasmissione - IdCodice (P.IVA/CF) del trasmittente',''),
        ('fe.trasmissione.formato','Trasmissione - Formato (FPR12/FPA12)','FPR12'),
        ('fe.trasmissione.progressivo_invio','Trasmissione - Prefisso progressivo invio (seguito dall''ID documento)',''),
        ('fe.trasmissione.codice_destinatario','Trasmissione - Codice destinatario di default','0000000'),
        ('fe.divisa','Documento - Divisa di default','EUR'),
        ('fe.condizioni_pagamento','Documento - Condizioni pagamento di default (TP01/TP02/TP03)','TP02'),
        ('fe.modalita_pagamento','Documento - Modalita'' pagamento di default (MP01..MP23)','MP05'),
        ('fe.natura','Documento - Natura IVA di default per aliquota 0 (N1..N7)','N1');
END
GO
