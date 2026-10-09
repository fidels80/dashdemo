-- =============================================================
-- Script SQL: modalita' di pagamento SDI su mg_metodo_pagamento
-- Compatibile con SQL Server
-- m261009_200500_add_fe_to_mg_metodo_pagamento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_metodo_pagamento' AND COLUMN_NAME='fe_modalita_pagamento')
    ALTER TABLE [dbo].[mg_metodo_pagamento] ADD [fe_modalita_pagamento] VARCHAR(4) NULL;
GO

UPDATE [dbo].[mg_metodo_pagamento] SET fe_modalita_pagamento='MP01' WHERE descrizione LIKE '%contant%';
UPDATE [dbo].[mg_metodo_pagamento] SET fe_modalita_pagamento='MP12' WHERE descrizione LIKE 'ri.ba%' OR codice LIKE 'RIB%';
UPDATE [dbo].[mg_metodo_pagamento] SET fe_modalita_pagamento='MP02' WHERE descrizione LIKE '%assegno%';
UPDATE [dbo].[mg_metodo_pagamento] SET fe_modalita_pagamento='MP08' WHERE descrizione LIKE '%carta%';
UPDATE [dbo].[mg_metodo_pagamento] SET fe_modalita_pagamento='MP05' WHERE descrizione LIKE '%bonifico%';
UPDATE [dbo].[mg_metodo_pagamento] SET fe_modalita_pagamento='MP05'
    WHERE fe_modalita_pagamento IS NULL OR fe_modalita_pagamento='';
GO
