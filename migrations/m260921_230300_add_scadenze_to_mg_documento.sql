-- =============================================================
-- Script SQL: scadenze documento (mg_documento + mg_scadenza)
-- Compatibile con SQL Server
-- m260921_230300_add_scadenze_to_mg_documento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento' AND COLUMN_NAME='crea_scadenze')
    ALTER TABLE [dbo].[mg_documento] ADD [crea_scadenze] BIT NOT NULL
        CONSTRAINT [DF_mg_documento_crea_scadenze] DEFAULT(0);
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_documento' AND COLUMN_NAME='id_metodo_pagamento')
BEGIN
    ALTER TABLE [dbo].[mg_documento] ADD [id_metodo_pagamento] INT NULL;
    CREATE INDEX [idx-mg_documento-metodo] ON [dbo].[mg_documento] ([id_metodo_pagamento]);
    ALTER TABLE [dbo].[mg_documento] ADD CONSTRAINT [fk-mg_documento-metodo]
        FOREIGN KEY ([id_metodo_pagamento]) REFERENCES [dbo].[mg_metodo_pagamento] ([id]) ON DELETE NO ACTION;
END
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME='mg_scadenza')
BEGIN
    CREATE TABLE [dbo].[mg_scadenza] (
        [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [id_documento] INT NOT NULL,
        [id_metodo_pagamento] INT NULL,
        [progressivo] INT NOT NULL CONSTRAINT [DF_mg_scadenza_prog] DEFAULT(1),
        [data_scadenza] DATE NOT NULL,
        [percentuale] DECIMAL(9,4) NOT NULL CONSTRAINT [DF_mg_scadenza_pct] DEFAULT(0),
        [importo] DECIMAL(18,2) NOT NULL CONSTRAINT [DF_mg_scadenza_importo] DEFAULT(0),
        [stato] VARCHAR(20) NOT NULL CONSTRAINT [DF_mg_scadenza_stato] DEFAULT('aperta'),
        [created_at] DATETIME NULL,
        CONSTRAINT [fk-mg_scadenza-doc] FOREIGN KEY ([id_documento])
            REFERENCES [dbo].[mg_documento] ([id]) ON DELETE CASCADE,
        CONSTRAINT [fk-mg_scadenza-metodo] FOREIGN KEY ([id_metodo_pagamento])
            REFERENCES [dbo].[mg_metodo_pagamento] ([id]) ON DELETE NO ACTION
    );
    CREATE INDEX [idx-mg_scadenza-doc] ON [dbo].[mg_scadenza] ([id_documento]);
    CREATE UNIQUE INDEX [idx-mg_scadenza-unico] ON [dbo].[mg_scadenza] ([id_documento], [progressivo]);
END
GO
