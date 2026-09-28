-- =============================================================
-- Script SQL: provvigione e metodo di pagamento su mg_anagrafica
-- Compatibile con SQL Server
-- m260921_230200_add_pagamento_to_mg_anagrafica
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='perc_provvigione')
    ALTER TABLE [dbo].[mg_anagrafica] ADD [perc_provvigione] DECIMAL(9,2) NOT NULL
        CONSTRAINT [DF_mg_anagrafica_perc_provv] DEFAULT(0);
GO

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_anagrafica' AND COLUMN_NAME='id_metodo_pagamento')
BEGIN
    ALTER TABLE [dbo].[mg_anagrafica] ADD [id_metodo_pagamento] INT NULL;
    CREATE INDEX [idx-mg_anagrafica-metodo] ON [dbo].[mg_anagrafica] ([id_metodo_pagamento]);
    ALTER TABLE [dbo].[mg_anagrafica] ADD CONSTRAINT [fk-mg_anagrafica-metodo]
        FOREIGN KEY ([id_metodo_pagamento]) REFERENCES [dbo].[mg_metodo_pagamento] ([id]) ON DELETE SET NULL;
END
GO
