-- =============================================================
-- Script SQL: registro entita' del servizio REST + regole di
-- integrita' referenziale in cancellazione (solo tabelle mg_*)
-- Compatibile con SQL Server
-- m261005_140000_create_dash_api_entita
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_api_entita')
BEGIN
    CREATE TABLE [dbo].[dash_api_entita] (
        [id]            INT IDENTITY(1,1) NOT NULL,
        [codice]        NVARCHAR(30)  NOT NULL,
        [alias]         NVARCHAR(200) NULL,
        [classe]        NVARCHAR(200) NOT NULL,
        [tabella]       NVARCHAR(60)  NOT NULL,
        [descrizione]   NVARCHAR(200) NOT NULL,
        [chiave_upsert] NVARCHAR(200) NULL,
        [sola_lettura]  BIT NOT NULL DEFAULT(0),
        [cancellabile]  BIT NOT NULL DEFAULT(1),
        [attiva]        BIT NOT NULL DEFAULT(1),
        [ordinamento]   INT NOT NULL DEFAULT(0),
        CONSTRAINT [PK_dash_api_entita] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE UNIQUE INDEX [idx-dash_api_entita-codice] ON [dbo].[dash_api_entita] ([codice] ASC);
    CREATE INDEX [idx-dash_api_entita-tabella]       ON [dbo].[dash_api_entita] ([tabella] ASC);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='dash_api_rel')
BEGIN
    CREATE TABLE [dbo].[dash_api_rel] (
        [id]         INT IDENTITY(1,1) NOT NULL,
        [entita]     NVARCHAR(30)  NOT NULL,
        [tipo]       NVARCHAR(20)  NOT NULL,
        [tabella]    NVARCHAR(60)  NOT NULL,
        [colonna]    NVARCHAR(60)  NOT NULL,
        [etichetta]  NVARCHAR(100) NOT NULL,
        [cascade]    BIT NOT NULL DEFAULT(0),
        [attiva]     BIT NOT NULL DEFAULT(1),
        [ordinamento] INT NOT NULL DEFAULT(0),
        CONSTRAINT [PK_dash_api_rel] PRIMARY KEY CLUSTERED ([id] ASC)
    );
    CREATE INDEX [idx-dash_api_rel-entita] ON [dbo].[dash_api_rel] ([entita] ASC);
    CREATE UNIQUE INDEX [idx-dash_api_rel-unica] ON [dbo].[dash_api_rel] ([entita] ASC, [tipo] ASC, [tabella] ASC, [colonna] ASC);
END
ELSE IF EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME='dash_api_rel' AND COLUMN_NAME='tipo' AND CHARACTER_MAXIMUM_LENGTH < 20)
BEGIN
    -- "riferimento" richiede 11 caratteri
    ALTER TABLE [dbo].[dash_api_rel] ALTER COLUMN [tipo] NVARCHAR(20) NOT NULL;
END
GO

-- Seed registro entita' (idempotente)
INSERT INTO [dbo].[dash_api_entita]
    ([codice], [alias], [classe], [tabella], [descrizione], [chiave_upsert], [sola_lettura], [cancellabile], [attiva], [ordinamento])
SELECT t.[codice], t.[alias], t.[classe], t.[tabella], t.[descrizione], t.[chiave_upsert], t.[sola_lettura], t.[cancellabile], t.[attiva], t.[ordinamento]
FROM (VALUES
    ('aliquote-iva',            'aliquotaiva,aliquote',              N'app\models\MgAliquotaIva',        'mg_aliquota_iva',         N'Aliquote IVA',                                                    'codice',                 0, 1, 1,  10),
    ('anagrafiche',             'anagrafica,anagrafico,clienti,fornitori', N'app\models\MgAnagrafica',   'mg_anagrafica',           N'Anagrafiche (clienti/fornitori)',                                'codice',                 0, 1, 1,  20),
    ('tipi-contatto',           'tipocontatto,tipi-contatti',        N'app\models\MgTipoContatto',      'mg_tipo_contatto',        N'Tipi di contatto',                                                'codice',                 0, 1, 1,  30),
    ('anagrafiche-contatti',    'contatti,anagrafica-contatti',      N'app\models\MgAnagraficaContatto','mg_anagrafica_contatto', N'Contatti anagrafiche',                                             NULL,                     0, 1, 1,  40),
    ('articoli',                'articolo,prodotti',                 N'app\models\MgArticolo',          'mg_articolo',             N'Articoli',                                                       'codice',                 0, 1, 1,  50),
    ('articoli-um',             'articoloum,unita-articolo',         N'app\models\MgArticoloUm',        'mg_articolo_um',          N'Unita di misura per articolo',                                   NULL,                     0, 1, 1,  60),
    ('attributi-articolo',      'attributi,varianti',                N'app\models\MgAttributoArticolo', 'mg_attributo_articolo',    N'Attributi articolo (marca/modello/taglia/colore/tessuto)',     'tipo,descrizione',       0, 1, 1,  70),
    ('modelli-tessuti',         'modellotessuto,modelli-tessuto',    N'app\models\MgModelloTessuto',    'mg_modello_tessuto',      N'Abbinamenti modello/tessuto',                                    NULL,                     0, 1, 1,  80),
    ('unita-misura',            'unita-misure,um',                   N'app\models\MgUnitaMisura',       'mg_unita_misura',         N'Unita di misura',                                                 'codice',                 0, 1, 1,  90),
    ('tipi-pagamento',          'tipopagamento,tipi-pagamenti',      N'app\models\MgTipoPagamento',     'mg_tipo_pagamento',       N'Tipi di pagamento',                                              'codice',                 0, 1, 1, 100),
    ('metodi-pagamento',        'metodopagamento,metodi-pagamento',  N'app\models\MgMetodoPagamento',  'mg_metodo_pagamento',     N'Metodi di pagamento',                                            'codice',                 0, 1, 1, 110),
    ('metodi-pagamento-rate',   'rate,scadenzario-metodo',           N'app\models\MgMetodoPagamentoRata','mg_metodo_pagamento_rata',N'Rate dei metodi di pagamento',                                   NULL,                     0, 1, 1, 120),
    ('tipi-documento',          'tipodocumento,tipi-documento',      N'app\models\MgTipoDocumento',     'mg_tipo_documento',       N'Tipi di documento',                                              'codice',                 0, 1, 1, 130),
    ('documenti',               'documento,testate',                 N'app\models\MgDocumento',         'mg_documento',            N'Documenti (testate)',                                            'id_tipo,anno,numero,suffisso',0, 1, 1, 140),
    ('righe',                   'riga,righe-documento,documentirighe',N'app\models\MgDocumentoRiga',   'mg_documento_riga',       N'Righe documento',                                                NULL,                     0, 1, 1, 150),
    ('scadenze',                'scadenza,scadenze-documento',        N'app\models\MgScadenza',          'mg_scadenza',             N'Scadenze documento',                                             NULL,                     0, 0, 1, 160),
    ('commesse',                'commessa',                          N'app\models\MgCommessa',          'mg_commessa',             N'Commesse',                                                      'codice',                 0, 1, 1, 170),
    ('sottocommesse',           'sottocommessa',                     N'app\models\MgSottocommessa',     'mg_sottocommessa',        N'Sottocommesse',                                                 'codice',                 0, 1, 1, 180)
) AS t ([codice], [alias], [classe], [tabella], [descrizione], [chiave_upsert], [sola_lettura], [cancellabile], [attiva], [ordinamento])
WHERE NOT EXISTS (SELECT 1 FROM [dbo].[dash_api_entita] e WHERE e.[codice] = t.[codice]);
GO

-- Seed regole di integrita' (idempotente)
INSERT INTO [dbo].[dash_api_rel]
    ([entita], [tipo], [tabella], [colonna], [etichetta], [cascade], [attiva], [ordinamento])
SELECT t.[entita], t.[tipo], t.[tabella], t.[colonna], t.[etichetta], t.[cascade], 1, t.[ordinamento]
FROM (VALUES
    ('documenti',         'figlio',      'mg_documento_riga',          'id_documento',        N'righe del documento',                          1, 10),
    ('documenti',         'figlio',      'mg_scadenza',                'id_documento',        N'scadenze del documento',                       1, 20),
    ('tipi-documento',    'riferimento', 'mg_documento',               'id_tipo',             N'documenti di questo tipo',                      0, 10),
    ('anagrafiche',       'riferimento', 'mg_documento',               'id_anagrafica',       N'documenti di questa anagrafica',                0, 10),
    ('anagrafiche',       'riferimento', 'mg_commessa',                'id_anagrafica',       N'commesse di questa anagrafica',                 0, 20),
    ('anagrafiche',       'riferimento', 'mg_sottocommessa',           'id_anagrafica',       N'sottocommesse di questa anagrafica',            0, 30),
    ('anagrafiche',       'figlio',      'mg_anagrafica_contatto',     'id_anagrafica',       N'contatti di questa anagrafica',                 1, 40),
    ('articoli',          'figlio',      'mg_articolo_um',             'id_articolo',         N'conversioni di unita di misura',                1, 20),
    ('articoli',          'riferimento', 'mg_documento_riga',          'id_articolo',         N'righe documento con questo articolo',          0, 10),
    ('unita-misura',      'riferimento', 'mg_articolo_um',             'id_unita_misura',     N'conversioni che la usano',                     0, 10),
    ('unita-misura',      'riferimento', 'mg_documento_riga',          'id_unita_misura',     N'righe documento con questa unita',             0, 20),
    ('aliquote-iva',      'riferimento', 'mg_articolo',                'id_iva_vendita',      N'articoli con IVA di vendita',                   0, 10),
    ('aliquote-iva',      'riferimento', 'mg_articolo',                'id_iva_acquisto',     N'articoli con IVA di acquisto',                  0, 20),
    ('aliquote-iva',      'riferimento', 'mg_anagrafica',              'id_aliquota_iva',     N'anagrafiche con questa aliquota',               0, 30),
    ('attributi-articolo','riferimento', 'mg_articolo',                'id_marca',            N'articoli con questa marca',                    0, 10),
    ('attributi-articolo','riferimento', 'mg_articolo',                'id_modello',          N'articoli con questo modello',                  0, 20),
    ('attributi-articolo','riferimento', 'mg_articolo',                'id_tessuto',          N'articoli con questo tessuto',                  0, 30),
    ('attributi-articolo','riferimento', 'mg_articolo',                'id_taglia',           N'articoli con questa taglia',                   0, 40),
    ('attributi-articolo','riferimento', 'mg_articolo',                'id_colore',           N'articoli con questo colore',                   0, 50),
    ('attributi-articolo','figlio',      'mg_modello_tessuto',         'id_modello',          N'abbinamenti con questo modello',                1, 60),
    ('attributi-articolo','riferimento', 'mg_modello_tessuto',         'id_tessuto',          N'abbinamenti con questo tessuto',                0, 70),
    ('tipi-pagamento',    'riferimento', 'mg_metodo_pagamento',        'id_tipo_pagamento',   N'metodi di pagamento di questo tipo',           0, 10),
    ('metodi-pagamento',  'figlio',      'mg_metodo_pagamento_rata',   'id_metodo',           N'rate di questo metodo',                        1, 10),
    ('metodi-pagamento',  'riferimento', 'mg_documento',               'id_metodo_pagamento', N'documenti con questo metodo',                  0, 20),
    ('metodi-pagamento',  'riferimento', 'mg_scadenza',                'id_metodo_pagamento', N'scadenze con questo metodo',                   0, 30),
    ('metodi-pagamento',  'riferimento', 'mg_anagrafica',              'id_metodo_pagamento', N'anagrafiche con questo metodo',                0, 40),
    ('commesse',          'figlio',      'mg_sottocommessa',           'id_commessa',         N'sottocommesse della commessa',                  1, 10),
    ('tipi-contatto',     'riferimento', 'mg_anagrafica_contatto',     'id_tipo_contatto',    N'contatti di questo tipo',                      0, 10)
) AS t ([entita], [tipo], [tabella], [colonna], [etichetta], [cascade], [ordinamento])
WHERE EXISTS (SELECT 1 FROM [dbo].[dash_api_entita] e WHERE e.[codice] = t.[entita])
  AND NOT EXISTS (
      SELECT 1 FROM [dbo].[dash_api_rel] r
      WHERE r.[entita] = t.[entita] AND r.[tipo] = t.[tipo]
        AND r.[tabella] = t.[tabella] AND r.[colonna] = t.[colonna]
  );
GO

-- I token precedenti non avevano permessi applicati: si portano a "*"
IF OBJECT_ID('[dbo].[api_token]', 'U') IS NOT NULL
BEGIN
    UPDATE [dbo].[api_token] SET [scopes] = '*' WHERE [scopes] IS NULL OR [scopes] = '';
END
GO
