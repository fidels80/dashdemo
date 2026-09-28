-- =============================================================
-- Script SQL: tabelle pagamenti (tipi, metodi, rate)
-- Compatibile con SQL Server
-- m260921_230100_create_mg_pagamento_tables
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_tipo_pagamento')
BEGIN
    CREATE TABLE [dbo].[mg_tipo_pagamento] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(20) NOT NULL,
        [descrizione] VARCHAR(100) NOT NULL,
        [attivo] BIT NOT NULL CONSTRAINT [DF_mg_tipo_pagamento_attivo] DEFAULT(1)
    );
    CREATE UNIQUE INDEX [idx-mg_tipo_pagamento-codice] ON [dbo].[mg_tipo_pagamento] ([codice]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_metodo_pagamento')
BEGIN
    CREATE TABLE [dbo].[mg_metodo_pagamento] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [codice] VARCHAR(20) NOT NULL,
        [descrizione] VARCHAR(200) NOT NULL,
        [id_tipo_pagamento] INT NULL,
        [partenza] VARCHAR(20) NOT NULL CONSTRAINT [DF_mg_metodo_pagamento_partenza] DEFAULT('emissione'),
        [giorni_partenza] INT NOT NULL CONSTRAINT [DF_mg_metodo_pagamento_giorni] DEFAULT(0),
        [n_rate] INT NOT NULL CONSTRAINT [DF_mg_metodo_pagamento_nrate] DEFAULT(1),
        [attivo] BIT NOT NULL CONSTRAINT [DF_mg_metodo_pagamento_attivo] DEFAULT(1),
        [created_at] DATETIME NULL,
        CONSTRAINT [fk-mg_metodo_pagamento-tipo] FOREIGN KEY ([id_tipo_pagamento])
            REFERENCES [dbo].[mg_tipo_pagamento] ([id]) ON DELETE SET NULL
    );
    CREATE UNIQUE INDEX [idx-mg_metodo_pagamento-codice] ON [dbo].[mg_metodo_pagamento] ([codice]);
    CREATE INDEX [idx-mg_metodo_pagamento-tipo] ON [dbo].[mg_metodo_pagamento] ([id_tipo_pagamento]);
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_metodo_pagamento_rata')
BEGIN
    CREATE TABLE [dbo].[mg_metodo_pagamento_rata] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [id_metodo] INT NOT NULL,
        [progressivo] INT NOT NULL CONSTRAINT [DF_mg_metodo_pagamento_rata_prog] DEFAULT(1),
        [giorni] INT NOT NULL CONSTRAINT [DF_mg_metodo_pagamento_rata_giorni] DEFAULT(0),
        [percentuale] DECIMAL(9,4) NOT NULL CONSTRAINT [DF_mg_metodo_pagamento_rata_pct] DEFAULT(0),
        CONSTRAINT [fk-mg_metodo_pagamento_rata-metodo] FOREIGN KEY ([id_metodo])
            REFERENCES [dbo].[mg_metodo_pagamento] ([id]) ON DELETE CASCADE
    );
    CREATE INDEX [idx-mg_metodo_pagamento_rata-metodo] ON [dbo].[mg_metodo_pagamento_rata] ([id_metodo]);
    CREATE UNIQUE INDEX [idx-mg_metodo_pagamento_rata-unico] ON [dbo].[mg_metodo_pagamento_rata] ([id_metodo], [progressivo]);
END
GO

-- Seed tipologie
IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_tipo_pagamento])
BEGIN
    INSERT INTO [dbo].[mg_tipo_pagamento] ([codice],[descrizione],[attivo]) VALUES
        ('CONT','Contanti',1),('BON','Bonifico bancario',1),('RIB','Ri.Ba.',1),
        ('ASS','Assegno',1),('CAR','Carta di credito',1);
END
GO

-- Seed metodi
IF NOT EXISTS (SELECT 1 FROM [dbo].[mg_metodo_pagamento])
BEGIN
    INSERT INTO [dbo].[mg_metodo_pagamento] ([codice],[descrizione],[id_tipo_pagamento],[partenza],[giorni_partenza],[n_rate],[attivo],[created_at])
    SELECT 'CONT','Contanti',t.id,'emissione',0,1,1,GETDATE() FROM [dbo].[mg_tipo_pagamento] t WHERE t.codice='CONT';
    INSERT INTO [dbo].[mg_metodo_pagamento] ([codice],[descrizione],[id_tipo_pagamento],[partenza],[giorni_partenza],[n_rate],[attivo],[created_at])
    SELECT 'BON30','Bonifico 30 gg',t.id,'giorni_dopo',30,1,1,GETDATE() FROM [dbo].[mg_tipo_pagamento] t WHERE t.codice='BON';
    INSERT INTO [dbo].[mg_metodo_pagamento] ([codice],[descrizione],[id_tipo_pagamento],[partenza],[giorni_partenza],[n_rate],[attivo],[created_at])
    SELECT 'BON30-60','Bonifico 30/60 gg',t.id,'giorni_dopo',30,2,1,GETDATE() FROM [dbo].[mg_tipo_pagamento] t WHERE t.codice='BON';
    INSERT INTO [dbo].[mg_metodo_pagamento] ([codice],[descrizione],[id_tipo_pagamento],[partenza],[giorni_partenza],[n_rate],[attivo],[created_at])
    SELECT 'RIB30FM','Ri.Ba. 30 gg fine mese',t.id,'fine_mese',30,1,1,GETDATE() FROM [dbo].[mg_tipo_pagamento] t WHERE t.codice='RIB';

    -- Rate
    INSERT INTO [dbo].[mg_metodo_pagamento_rata] ([id_metodo],[progressivo],[giorni],[percentuale])
    SELECT id,1,0,100 FROM [dbo].[mg_metodo_pagamento] WHERE codice='CONT';
    INSERT INTO [dbo].[mg_metodo_pagamento_rata] ([id_metodo],[progressivo],[giorni],[percentuale])
    SELECT id,1,0,100 FROM [dbo].[mg_metodo_pagamento] WHERE codice='BON30';
    INSERT INTO [dbo].[mg_metodo_pagamento_rata] ([id_metodo],[progressivo],[giorni],[percentuale])
    SELECT id,1,0,50 FROM [dbo].[mg_metodo_pagamento] WHERE codice='BON30-60';
    INSERT INTO [dbo].[mg_metodo_pagamento_rata] ([id_metodo],[progressivo],[giorni],[percentuale])
    SELECT id,2,30,50 FROM [dbo].[mg_metodo_pagamento] WHERE codice='BON30-60';
    INSERT INTO [dbo].[mg_metodo_pagamento_rata] ([id_metodo],[progressivo],[giorni],[percentuale])
    SELECT id,1,0,100 FROM [dbo].[mg_metodo_pagamento] WHERE codice='RIB30FM';
END
GO
