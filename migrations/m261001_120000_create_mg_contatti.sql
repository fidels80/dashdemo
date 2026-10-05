-- =============================================================
-- Script SQL: contatti delle anagrafiche del microgestionale
-- Compatibile con SQL Server
-- m261001_120000_create_mg_contatti
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_tipo_contatto')
BEGIN
    CREATE TABLE [dbo].[mg_tipo_contatto] (
        [id]          INT IDENTITY(1,1) NOT NULL,
        [codice]      NVARCHAR(30) NOT NULL,
        [descrizione] NVARCHAR(100) NOT NULL,
        [icona]       NVARCHAR(50) NULL,
        [ordine]      INT NOT NULL CONSTRAINT [DF_mg_tipo_contatto_ordine] DEFAULT(0),
        [attivo]      BIT NOT NULL CONSTRAINT [DF_mg_tipo_contatto_attivo] DEFAULT(1),
        CONSTRAINT [PK_mg_tipo_contatto] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-mg_tipo_contatto-codice] ON [dbo].[mg_tipo_contatto] ([codice] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_anagrafica_contatto')
BEGIN
    CREATE TABLE [dbo].[mg_anagrafica_contatto] (
        [id]               INT IDENTITY(1,1) NOT NULL,
        [id_anagrafica]    INT NOT NULL,
        [id_tipo_contatto] INT NOT NULL,
        [valore]           NVARCHAR(200) NOT NULL,
        [etichetta]        NVARCHAR(100) NULL,
        [predefinito]      BIT NOT NULL CONSTRAINT [DF_mg_anagrafica_contatto_predefinito] DEFAULT(0),
        [note]             NVARCHAR(500) NULL,
        [attivo]           BIT NOT NULL CONSTRAINT [DF_mg_anagrafica_contatto_attivo] DEFAULT(1),
        [created_at]       DATETIME NULL,
        CONSTRAINT [PK_mg_anagrafica_contatto] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE INDEX [idx-mg_anagrafica_contatto-anagrafica] ON [dbo].[mg_anagrafica_contatto] ([id_anagrafica] ASC);
    CREATE INDEX [idx-mg_anagrafica_contatto-tipo] ON [dbo].[mg_anagrafica_contatto] ([id_tipo_contatto] ASC);
    CREATE UNIQUE INDEX [idx-mg_anagrafica_contatto-unico]
        ON [dbo].[mg_anagrafica_contatto] ([id_anagrafica] ASC, [id_tipo_contatto] ASC, [valore] ASC);

    ALTER TABLE [dbo].[mg_anagrafica_contatto] WITH CHECK
        ADD CONSTRAINT [fk-mg_anagrafica_contatto-anagrafica]
        FOREIGN KEY ([id_anagrafica]) REFERENCES [dbo].[mg_anagrafica] ([id]) ON DELETE CASCADE;

    ALTER TABLE [dbo].[mg_anagrafica_contatto] WITH CHECK
        ADD CONSTRAINT [fk-mg_anagrafica_contatto-tipo]
        FOREIGN KEY ([id_tipo_contatto]) REFERENCES [dbo].[mg_tipo_contatto] ([id]);
END
GO

-- Seed dei tipi contatto (solo se la tabella è vuota)
IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_tipo_contatto])
BEGIN
    INSERT INTO [dbo].[mg_tipo_contatto] ([codice], [descrizione], [icona], [ordine], [attivo]) VALUES
        ('email',              'Email',              'fas fa-envelope',     10, 1),
        ('pec',                'PEC',                'fas fa-certificate',  20, 1),
        ('cellulare',          'Cellulare',          'fas fa-mobile-alt',   30, 1),
        ('telefono_fisso',     'Telefono fisso',     'fas fa-phone',        40, 1),
        ('telefono_personale', 'Telefono personale', 'fas fa-phone-alt',    50, 1),
        ('fax',                'Fax',                'fas fa-fax',          60, 1),
        ('sito_web',           'Sito web',           'fas fa-globe',        70, 1),
        ('linkedin',           'LinkedIn',           'fab fa-linkedin',     80, 1),
        ('skype',              'Skype',              'fab fa-skype',        90, 1),
        ('discord',            'Discord',            'fab fa-discord',     100, 1),
        ('whatsapp',           'WhatsApp',           'fab fa-whatsapp',    110, 1),
        ('telegram',           'Telegram',           'fab fa-telegram',    120, 1);
END
GO
