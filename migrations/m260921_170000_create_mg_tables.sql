-- =============================================================
-- Script SQL: microgestionale documentale (tabelle mg_*)
-- Indipendenti dalle tabelle uec_*
-- Compatibile con SQL Server
-- m260921_170000_create_mg_tables
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_anagrafica')
BEGIN
    CREATE TABLE [dbo].[mg_anagrafica] (
        [id]              INT IDENTITY(1,1) NOT NULL,
        [codice]          NVARCHAR(20) NOT NULL,
        [ragione_sociale] NVARCHAR(200) NOT NULL,
        [partita_iva]     NVARCHAR(20) NULL,
        [codice_fiscale]  NVARCHAR(20) NULL,
        [indirizzo]       NVARCHAR(200) NULL,
        [cap]             NVARCHAR(10) NULL,
        [citta]           NVARCHAR(100) NULL,
        [provincia]       NVARCHAR(3) NULL,
        [telefono]        NVARCHAR(50) NULL,
        [email]           NVARCHAR(100) NULL,
        [tipo]            NVARCHAR(20) NULL,
        [attivo]          BIT NOT NULL CONSTRAINT [DF_mg_anagrafica_attivo] DEFAULT(1),
        CONSTRAINT [PK_mg_anagrafica] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-mg_anagrafica-codice] ON [dbo].[mg_anagrafica] ([codice] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_articolo')
BEGIN
    CREATE TABLE [dbo].[mg_articolo] (
        [id]          INT IDENTITY(1,1) NOT NULL,
        [codice]      NVARCHAR(25) NOT NULL,
        [descrizione] NVARCHAR(250) NOT NULL,
        [um]          NVARCHAR(10) NULL,
        [prezzo]      DECIMAL(18,4) NULL CONSTRAINT [DF_mg_articolo_prezzo] DEFAULT(0),
        [iva]         DECIMAL(9,2) NULL CONSTRAINT [DF_mg_articolo_iva] DEFAULT(0),
        [attivo]      BIT NOT NULL CONSTRAINT [DF_mg_articolo_attivo] DEFAULT(1),
        CONSTRAINT [PK_mg_articolo] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-mg_articolo-codice] ON [dbo].[mg_articolo] ([codice] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_tipo_documento')
BEGIN
    CREATE TABLE [dbo].[mg_tipo_documento] (
        [id]              INT IDENTITY(1,1) NOT NULL,
        [codice]          NVARCHAR(20) NOT NULL,
        [descrizione]     NVARCHAR(200) NOT NULL,
        [anno]            INT NOT NULL CONSTRAINT [DF_mg_tipo_documento_anno] DEFAULT(0),
        [contatore]       INT NOT NULL CONSTRAINT [DF_mg_tipo_documento_contatore] DEFAULT(0),
        [usa_progressivo] BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_usa_progressivo] DEFAULT(1),
        [congruita]       BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_congruita] DEFAULT(0),
        [attivo]          BIT NOT NULL CONSTRAINT [DF_mg_tipo_documento_attivo] DEFAULT(1),
        [created_at]      DATETIME NULL,
        CONSTRAINT [PK_mg_tipo_documento] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-mg_tipo_documento-codice] ON [dbo].[mg_tipo_documento] ([codice] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_documento')
BEGIN
    CREATE TABLE [dbo].[mg_documento] (
        [id]            INT IDENTITY(1,1) NOT NULL,
        [id_tipo]       INT NOT NULL,
        [codice_tipo]   NVARCHAR(20) NOT NULL,
        [anno]          INT NOT NULL,
        [numero]        INT NOT NULL,
        [suffisso]      NVARCHAR(10) NOT NULL CONSTRAINT [DF_mg_documento_suffisso] DEFAULT(''),
        [data]          DATE NOT NULL,
        [id_anagrafica] INT NULL,
        [descrizione]   NVARCHAR(500) NULL,
        [stato]         NVARCHAR(20) NULL CONSTRAINT [DF_mg_documento_stato] DEFAULT('bozza'),
        [totale]        DECIMAL(18,2) NULL CONSTRAINT [DF_mg_documento_totale] DEFAULT(0),
        [note]          NVARCHAR(2000) NULL,
        [created_by]    NVARCHAR(50) NULL,
        [created_at]    DATETIME NULL,
        [updated_at]    DATETIME NULL,
        CONSTRAINT [PK_mg_documento] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    -- Unicità: nello stesso anno e per lo stesso tipo non possono esistere
    -- due documenti con lo stesso numero e suffisso.
    CREATE UNIQUE INDEX [idx-mg_documento-unico]
        ON [dbo].[mg_documento] ([id_tipo] ASC, [anno] ASC, [numero] ASC, [suffisso] ASC);
    CREATE INDEX [idx-mg_documento-data] ON [dbo].[mg_documento] ([data] ASC);
    CREATE INDEX [idx-mg_documento-anagrafica] ON [dbo].[mg_documento] ([id_anagrafica] ASC);

    ALTER TABLE [dbo].[mg_documento] WITH CHECK
        ADD CONSTRAINT [fk-mg_documento-tipo]
        FOREIGN KEY ([id_tipo]) REFERENCES [dbo].[mg_tipo_documento] ([id]);
    ALTER TABLE [dbo].[mg_documento] WITH CHECK
        ADD CONSTRAINT [fk-mg_documento-anagrafica]
        FOREIGN KEY ([id_anagrafica]) REFERENCES [dbo].[mg_anagrafica] ([id]) ON DELETE SET NULL;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_documento_riga')
BEGIN
    CREATE TABLE [dbo].[mg_documento_riga] (
        [id]              INT IDENTITY(1,1) NOT NULL,
        [id_documento]    INT NOT NULL,
        [id_articolo]     INT NULL,
        [codice_articolo] NVARCHAR(25) NULL,
        [descrizione]     NVARCHAR(500) NULL,
        [qta]             DECIMAL(18,4) NULL CONSTRAINT [DF_mg_documento_riga_qta] DEFAULT(0),
        [prezzo]          DECIMAL(18,4) NULL CONSTRAINT [DF_mg_documento_riga_prezzo] DEFAULT(0),
        [sconto]          DECIMAL(9,2) NULL CONSTRAINT [DF_mg_documento_riga_sconto] DEFAULT(0),
        [iva]             DECIMAL(9,2) NULL CONSTRAINT [DF_mg_documento_riga_iva] DEFAULT(0),
        [totale]          DECIMAL(18,2) NULL CONSTRAINT [DF_mg_documento_riga_totale] DEFAULT(0),
        [ordine]          INT NULL CONSTRAINT [DF_mg_documento_riga_ordine] DEFAULT(0),
        CONSTRAINT [PK_mg_documento_riga] PRIMARY KEY CLUSTERED ([id] ASC)
    );

    CREATE INDEX [idx-mg_documento_riga-doc] ON [dbo].[mg_documento_riga] ([id_documento] ASC);

    ALTER TABLE [dbo].[mg_documento_riga] WITH CHECK
        ADD CONSTRAINT [fk-mg_documento_riga-doc]
        FOREIGN KEY ([id_documento]) REFERENCES [dbo].[mg_documento] ([id]) ON DELETE CASCADE;
    ALTER TABLE [dbo].[mg_documento_riga] WITH CHECK
        ADD CONSTRAINT [fk-mg_documento_riga-art]
        FOREIGN KEY ([id_articolo]) REFERENCES [dbo].[mg_articolo] ([id]) ON DELETE SET NULL;
END
GO
