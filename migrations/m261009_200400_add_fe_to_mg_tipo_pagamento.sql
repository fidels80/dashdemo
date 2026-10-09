-- =============================================================
-- Script SQL: condizioni di pagamento SDI su mg_tipo_pagamento
-- Compatibile con SQL Server
-- m261009_200400_add_fe_to_mg_tipo_pagamento
-- =============================================================

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='mg_tipo_pagamento' AND COLUMN_NAME='fe_condizioni_pagamento')
    ALTER TABLE [dbo].[mg_tipo_pagamento] ADD [fe_condizioni_pagamento] VARCHAR(4) NULL;
GO

UPDATE [dbo].[mg_tipo_pagamento] SET fe_condizioni_pagamento='TP02'
    WHERE fe_condizioni_pagamento IS NULL OR fe_condizioni_pagamento='';
GO
