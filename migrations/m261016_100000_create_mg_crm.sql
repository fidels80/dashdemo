-- ------------------------------------------------------------------
-- Modulo CRM del microgestionale (tabelle mg_crm_*)
-- Script gemello della migrazione m261016_100000_create_mg_crm
-- Eseguire manualmente su SQL Server quando non si usa `php yii migrate`.
-- ------------------------------------------------------------------

IF OBJECT_ID(N'dbo.mg_crm_tipo', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_tipo (
        id          INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        codice      VARCHAR(30)  NOT NULL,
        descrizione VARCHAR(100) NOT NULL,
        icona       VARCHAR(50)  NULL,
        ordine      INT          NOT NULL DEFAULT 0,
        attivo      BIT          NOT NULL DEFAULT 1,
        CONSTRAINT ux_mg_crm_tipo_codice UNIQUE (codice)
    );
END;
GO

IF OBJECT_ID(N'dbo.mg_crm_stato', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_stato (
        id          INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        codice      VARCHAR(30)  NOT NULL,
        descrizione VARCHAR(100) NOT NULL,
        colore      VARCHAR(20)  NULL,
        is_chiuso   BIT          NOT NULL DEFAULT 0,
        ordine      INT          NOT NULL DEFAULT 0,
        attivo      BIT          NOT NULL DEFAULT 1,
        CONSTRAINT ux_mg_crm_stato_codice UNIQUE (codice)
    );
END;
GO

IF OBJECT_ID(N'dbo.mg_crm_priorita', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_priorita (
        id          INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        codice      VARCHAR(30)  NOT NULL,
        descrizione VARCHAR(100) NOT NULL,
        colore      VARCHAR(20)  NULL,
        ordine      INT          NOT NULL DEFAULT 0,
        attivo      BIT          NOT NULL DEFAULT 1,
        CONSTRAINT ux_mg_crm_priorita_codice UNIQUE (codice)
    );
END;
GO

IF OBJECT_ID(N'dbo.mg_crm_ticket', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_ticket (
        id                 INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        anno               INT           NOT NULL,
        numero             INT           NOT NULL,
        id_tipo            INT           NOT NULL,
        id_stato           INT           NOT NULL,
        id_priorita        INT           NULL,
        id_anagrafica      INT           NULL,
        id_utente          INT           NULL,
        id_articolo        INT           NULL,
        id_matricola       INT           NULL,
        id_documento       INT           NULL,
        oggetto            VARCHAR(250)  NOT NULL,
        descrizione        VARCHAR(2000) NULL,
        assegnato_a        INT           NULL,
        data_apertura      DATETIME      NOT NULL,
        data_presa_carico  DATETIME      NULL,
        data_chiusura      DATETIME      NULL,
        data_scadenza      DATETIME      NULL,
        created_by         VARCHAR(50)   NULL,
        created_at         DATETIME      NULL,
        updated_at         DATETIME      NULL,
        CONSTRAINT ux_mg_crm_ticket_anno_numero UNIQUE (anno, numero)
    );

    CREATE INDEX idx_mg_crm_ticket_anagrafica ON dbo.mg_crm_ticket (id_anagrafica);
    CREATE INDEX idx_mg_crm_ticket_stato      ON dbo.mg_crm_ticket (id_stato);
    CREATE INDEX idx_mg_crm_ticket_utente     ON dbo.mg_crm_ticket (id_utente);
    CREATE INDEX idx_mg_crm_ticket_assegnato  ON dbo.mg_crm_ticket (assegnato_a);
    CREATE INDEX idx_mg_crm_ticket_documento  ON dbo.mg_crm_ticket (id_documento);

    ALTER TABLE dbo.mg_crm_ticket ADD CONSTRAINT fk_mg_crm_ticket_tipo
        FOREIGN KEY (id_tipo) REFERENCES dbo.mg_crm_tipo (id);
    ALTER TABLE dbo.mg_crm_ticket ADD CONSTRAINT fk_mg_crm_ticket_stato
        FOREIGN KEY (id_stato) REFERENCES dbo.mg_crm_stato (id);
    ALTER TABLE dbo.mg_crm_ticket ADD CONSTRAINT fk_mg_crm_ticket_priorita
        FOREIGN KEY (id_priorita) REFERENCES dbo.mg_crm_priorita (id);
    ALTER TABLE dbo.mg_crm_ticket ADD CONSTRAINT fk_mg_crm_ticket_anagrafica
        FOREIGN KEY (id_anagrafica) REFERENCES dbo.mg_anagrafica (id);
    ALTER TABLE dbo.mg_crm_ticket ADD CONSTRAINT fk_mg_crm_ticket_articolo
        FOREIGN KEY (id_articolo) REFERENCES dbo.mg_articolo (id);
    ALTER TABLE dbo.mg_crm_ticket ADD CONSTRAINT fk_mg_crm_ticket_matricola
        FOREIGN KEY (id_matricola) REFERENCES dbo.mg_matricola (id);
    ALTER TABLE dbo.mg_crm_ticket ADD CONSTRAINT fk_mg_crm_ticket_documento
        FOREIGN KEY (id_documento) REFERENCES dbo.mg_documento (id);
END;
GO

IF OBJECT_ID(N'dbo.mg_crm_ticket_messaggio', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_ticket_messaggio (
        id               INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        id_ticket        INT           NOT NULL,
        user_id          INT           NULL,
        autore           VARCHAR(100)  NULL,
        is_nota_interna  BIT           NOT NULL DEFAULT 0,
        messaggio        VARCHAR(2000) NOT NULL,
        created_at       DATETIME      NULL
    );

    CREATE INDEX idx_mg_crm_ticket_messaggio_ticket ON dbo.mg_crm_ticket_messaggio (id_ticket);
    ALTER TABLE dbo.mg_crm_ticket_messaggio ADD CONSTRAINT fk_mg_crm_ticket_messaggio_ticket
        FOREIGN KEY (id_ticket) REFERENCES dbo.mg_crm_ticket (id) ON DELETE CASCADE;
END;
GO

IF OBJECT_ID(N'dbo.mg_crm_utente_anagrafica', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_utente_anagrafica (
        id            INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        user_id       INT      NOT NULL,
        id_anagrafica INT      NOT NULL,
        created_at    DATETIME NULL,
        CONSTRAINT ux_mg_crm_utente_anagrafica UNIQUE (user_id, id_anagrafica)
    );

    ALTER TABLE dbo.mg_crm_utente_anagrafica ADD CONSTRAINT fk_mg_crm_utente_anagrafica_anagrafica
        FOREIGN KEY (id_anagrafica) REFERENCES dbo.mg_anagrafica (id) ON DELETE CASCADE;
END;
GO

-- Seed tipi/stati/priorita (solo se vuoti)
IF NOT EXISTS (SELECT 1 FROM dbo.mg_crm_tipo)
BEGIN
    INSERT INTO dbo.mg_crm_tipo (codice, descrizione, icona, ordine, attivo) VALUES
        ('assistenza',     'Assistenza tecnica',         'fas fa-tools',               10, 1),
        ('amministrativa', 'Richiesta amministrativa',   'fas fa-file-invoice',        20, 1),
        ('preventivo',     'Richiesta preventivo',       'fas fa-file-signature',      30, 1),
        ('commerciale',    'Informazione commerciale',   'fas fa-comments',            40, 1),
        ('reclamo',        'Reclamo',                    'fas fa-exclamation-triangle',50, 1);
END;
GO

IF NOT EXISTS (SELECT 1 FROM dbo.mg_crm_stato)
BEGIN
    INSERT INTO dbo.mg_crm_stato (codice, descrizione, colore, is_chiuso, ordine, attivo) VALUES
        ('nuovo',           'Nuovo',                'secondary', 0, 10, 1),
        ('aperto',          'Aperto',               'primary',   0, 20, 1),
        ('preso_in_carico', 'Preso in carico',      'info',      0, 30, 1),
        ('in_lavorazione',  'In lavorazione',       'warning',   0, 40, 1),
        ('attesa_cliente',  'In attesa del cliente','dark',      0, 50, 1),
        ('risolto',         'Risolto',              'success',   0, 60, 1),
        ('chiuso',          'Chiuso',               'success',   1, 70, 1),
        ('annullato',       'Annullato',            'danger',    1, 80, 1);
END;
GO

IF NOT EXISTS (SELECT 1 FROM dbo.mg_crm_priorita)
BEGIN
    INSERT INTO dbo.mg_crm_priorita (codice, descrizione, colore, ordine, attivo) VALUES
        ('bassa',   'Bassa',   'secondary', 10, 1),
        ('normale', 'Normale', 'primary',   20, 1),
        ('alta',    'Alta',    'warning',   30, 1),
        ('urgente', 'Urgente', 'danger',    40, 1);
END;
GO
