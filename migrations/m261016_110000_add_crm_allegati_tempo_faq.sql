-- ------------------------------------------------------------------
-- Estensioni CRM: allegati, tempo impiegato, soluzione, rapportino, FAQ.
-- Script gemello della migrazione m261016_110000_add_crm_allegati_tempo_faq
-- ------------------------------------------------------------------

IF OBJECT_ID(N'dbo.mg_crm_allegato', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_allegato (
        id             INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        id_ticket      INT           NULL,
        id_messaggio   INT           NULL,
        user_id        INT           NULL,
        nome_file      VARCHAR(255)  NOT NULL,
        nome_originale VARCHAR(255)  NOT NULL,
        mime           VARCHAR(100)  NULL,
        dimensione     INT           NULL,
        percorso       VARCHAR(500)  NOT NULL,
        created_at     DATETIME      NULL
    );
    CREATE INDEX idx_mg_crm_allegato_ticket    ON dbo.mg_crm_allegato (id_ticket);
    CREATE INDEX idx_mg_crm_allegato_messaggio ON dbo.mg_crm_allegato (id_messaggio);
END;
GO

IF OBJECT_ID(N'dbo.mg_crm_faq', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.mg_crm_faq (
        id              INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        id_ticket       INT           NULL,
        id_articolo     INT           NULL,
        codice_articolo VARCHAR(25)   NULL,
        domanda         VARCHAR(500)  NOT NULL,
        soluzione       VARCHAR(4000) NULL,
        attivo          BIT           NOT NULL DEFAULT 1,
        created_by      VARCHAR(50)   NULL,
        created_at      DATETIME      NULL,
        updated_at      DATETIME      NULL
    );
    CREATE INDEX idx_mg_crm_faq_articolo ON dbo.mg_crm_faq (id_articolo);
    CREATE INDEX idx_mg_crm_faq_codice   ON dbo.mg_crm_faq (codice_articolo);
END;
GO

IF COL_LENGTH('dbo.mg_crm_ticket', 'tempo_impiegato_ore') IS NULL
    ALTER TABLE dbo.mg_crm_ticket ADD tempo_impiegato_ore DECIMAL(9,2) NULL;
IF COL_LENGTH('dbo.mg_crm_ticket', 'soluzione') IS NULL
    ALTER TABLE dbo.mg_crm_ticket ADD soluzione VARCHAR(4000) NULL;
IF COL_LENGTH('dbo.mg_crm_ticket', 'id_rapportino') IS NULL
    ALTER TABLE dbo.mg_crm_ticket ADD id_rapportino VARCHAR(36) NULL;
IF COL_LENGTH('dbo.mg_crm_ticket', 'evaso') IS NULL
    ALTER TABLE dbo.mg_crm_ticket ADD evaso BIT NOT NULL CONSTRAINT df_mg_crm_ticket_evaso DEFAULT (0);
GO

IF COL_LENGTH('dbo.mg_crm_ticket_messaggio', 'is_soluzione') IS NULL
    ALTER TABLE dbo.mg_crm_ticket_messaggio ADD is_soluzione BIT NOT NULL CONSTRAINT df_mg_crm_ticket_messaggio_is_soluzione DEFAULT (0);
GO
