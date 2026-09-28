-- =============================================================
-- Script SQL: rinomina dati dimostrativi (anagrafiche + articoli)
-- Compatibile con SQL Server
-- m260921_230500_rinomina_dati_dimostrativi
-- =============================================================

-- -------------------------------------------------------------
-- ANAGRAFICHE: "Azienda Demo NNN ..." -> "Cognome Settore Forma"
-- -------------------------------------------------------------
;WITH src AS (
    SELECT id, ROW_NUMBER() OVER (ORDER BY id) - 1 AS rn
    FROM [dbo].[mg_anagrafica]
    WHERE ragione_sociale LIKE 'Azienda Demo%'
),
cognomi AS (
    SELECT * FROM (VALUES
        (0,'Rossi'),(1,'Bianchi'),(2,'Ferrari'),(3,'Russo'),(4,'Romano'),(5,'Colombo'),(6,'Ricci'),(7,'Marino'),
        (8,'Greco'),(9,'Bruno'),(10,'Gallo'),(11,'Conti'),(12,'De Luca'),(13,'Costa'),(14,'Giordano'),(15,'Mancini'),
        (16,'Rizzo'),(17,'Lombardi'),(18,'Moretti'),(19,'Barbieri'),(20,'Fontana'),(21,'Santoro'),(22,'Mariani'),(23,'Rinaldi'),
        (24,'Caruso'),(25,'Ferrara'),(26,'Galli'),(27,'Martini'),(28,'Leone'),(29,'Longo'),(30,'Gentile'),(31,'Martinelli'),
        (32,'Vitale'),(33,'Lombardo'),(34,'Serra'),(35,'Coppola'),(36,'De Santis'),(37,'D''Angelo'),(38,'Marchetti'),(39,'Parisi'),
        (40,'Villa'),(41,'Conte'),(42,'Ferraro'),(43,'Ferri'),(44,'Fabbri'),(45,'Bianco'),(46,'Marini'),(47,'Grillo'),
        (48,'Valentini'),(49,'Messina'),(50,'Sala'),(51,'De Angelis'),(52,'Gatti'),(53,'Pellegrini'),(54,'Palumbo'),(55,'Sanna'),
        (56,'Farina'),(57,'Rizzi'),(58,'Monti'),(59,'Cattaneo'),(60,'Morelli'),(61,'Amato'),(62,'Silvestri'),(63,'Mazza'),
        (64,'Testa'),(65,'Grassi'),(66,'Pellegrino'),(67,'Carbone'),(68,'Giuliani'),(69,'Benedetti'),(70,'Barone'),(71,'Rossetti'),
        (72,'Caputo'),(73,'Montanari'),(74,'Guerra'),(75,'Palmieri'),(76,'Bernardi'),(77,'Martino'),(78,'Fiore'),(79,'De Rosa'),
        (80,'Ferretti'),(81,'Bellini'),(82,'Basile'),(83,'Riva'),(84,'Donati'),(85,'Piras'),(86,'Vitali'),(87,'Battaglia'),
        (88,'Sartori'),(89,'Neri'),(90,'Costantini'),(91,'Milani'),(92,'Pagano'),(93,'Ruggiero'),(94,'Sorrentino'),(95,'D''Amico'),
        (96,'Negri'),(97,'Guerrini'),(98,'Orlando'),(99,'Ferrante')
    ) v(i, nome)
),
settori AS (
    SELECT * FROM (VALUES
        (0,'Impianti'),(1,'Costruzioni'),(2,'Meccanica'),(3,'Elettronica'),(4,'Logistica'),
        (5,'Arredamenti'),(6,'Tecnologie'),(7,'Servizi'),(8,'Energia'),(9,'Automazione'),
        (10,'Utensileria'),(11,'Plastica'),(12,'Metalli'),(13,'Legnami'),(14,'Tessile'),
        (15,'Alimentari'),(16,'Trasporti'),(17,'Pulizie'),(18,'Informatica'),(19,'Ceramiche'),
        (20,'Verniciature'),(21,'Ferramenta'),(22,'Carpenteria'),(23,'Fonderia'),(24,'Tinteggiature')
    ) v(i, nome)
),
forme AS (
    SELECT * FROM (VALUES (0,'SRL'),(1,'SPA'),(2,'SNC'),(3,'SAS'),(4,'SRLS')) v(i, nome)
)
UPDATE a
SET a.ragione_sociale = c.nome + ' ' + s.nome + ' ' + f.nome
FROM [dbo].[mg_anagrafica] a
JOIN src ON src.id = a.id
JOIN cognomi c ON c.i = src.rn % 100
JOIN settori s ON s.i = (src.rn / 100) % 25
JOIN forme f ON f.i = src.rn % 5;
GO

-- -------------------------------------------------------------
-- ARTICOLI: "Articolo demo NNN" -> "Prodotto NNN"
-- -------------------------------------------------------------
;WITH src AS (
    SELECT id, ROW_NUMBER() OVER (ORDER BY id) - 1 AS rn
    FROM [dbo].[mg_articolo]
    WHERE descrizione LIKE 'Articolo demo%'
),
prod AS (
    SELECT * FROM (VALUES
        (0,'Vite a brugola'),(1,'Bullone esagonale'),(2,'Dado autobloccante'),(3,'Rondella piana'),
        (4,'Vite autofilettante'),(5,'Cavo elettrico'),(6,'Tubo in acciaio inox'),(7,'Guarnizione in gomma'),
        (8,'Cuscinetto a sfere'),(9,'Ingranaggio cilindrico'),(10,'Pompa idraulica'),(11,'Valvola a sfera'),
        (12,'Motore elettrico'),(13,'Sensore di prossimità'),(14,'Contattore'),(15,'Interruttore magnetotermico'),
        (16,'Fusibile'),(17,'Relè'),(18,'Trasformatore'),(19,'Quadro elettrico'),
        (20,'Pressostato'),(21,'Manometro'),(22,'Filtro olio'),(23,'Filtro aria'),
        (24,'Cinghia di trasmissione'),(25,'Puleggia'),(26,'Giunto elastico'),(27,'Riduttore di velocità'),
        (28,'Elettrovalvola'),(29,'Cilindro pneumatico'),(30,'Compressore'),(31,'Serbatoio'),
        (32,'Scambiatore di calore'),(33,'Termostato'),(34,'Resistenza elettrica'),(35,'Cablaggio'),
        (36,'Connettore'),(37,'Morsettiera'),(38,'Canalina portacavi'),(39,'Guaina termorestringente')
    ) v(i, nome)
)
UPDATE a
SET a.descrizione = p.nome + ' ' + RIGHT('000' + CAST(src.rn + 1 AS VARCHAR(3)), 3)
FROM [dbo].[mg_articolo] a
JOIN src ON src.id = a.id
JOIN prod p ON p.i = src.rn % 40;
GO
