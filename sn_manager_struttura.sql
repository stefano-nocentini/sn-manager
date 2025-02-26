CREATE TABLE `clienti` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `societa` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nome` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `cognome` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `indirizzo` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id_comune` INTEGER(11) DEFAULT 0,
  `id_provincia` INTEGER(11) DEFAULT 0,
  `id_regione` INTEGER(11) DEFAULT 0,
  `p_iva` VARCHAR(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rea` VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` VARCHAR(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pec` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_provincia` USING BTREE (`id_provincia`) COMMENT '',
   INDEX `id_regione` USING BTREE (`id_regione`) COMMENT '',
   INDEX `clienti_ibfk_1` USING BTREE (`id_comune`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=13 AVG_ROW_LENGTH=1365 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `posatori` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `societa` VARCHAR(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nome` VARCHAR(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cognome` VARCHAR(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indirizzo` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_comune` INTEGER(11) NOT NULL,
  `id_provincia` INTEGER(11) NOT NULL,
  `id_regione` INTEGER(11) NOT NULL,
  `p_iva` VARCHAR(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rea` VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` VARCHAR(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pec` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
  UNIQUE INDEX `email` USING BTREE (`email`) COMMENT '',
   INDEX `id_comune` USING BTREE (`id_comune`) COMMENT '',
   INDEX `id_provincia` USING BTREE (`id_provincia`) COMMENT '',
   INDEX `id_regione` USING BTREE (`id_regione`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=3 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `regioni` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `regione` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=21 AVG_ROW_LENGTH=819 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `province` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `provincia` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_regione` INTEGER(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_regione` USING BTREE (`id_regione`) COMMENT '',
  CONSTRAINT `province_ibfk_1` FOREIGN KEY (`id_regione`) REFERENCES `regioni` (`id`) ON UPDATE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=293 AVG_ROW_LENGTH=153 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `stato_cantiere` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `stato_cantiere` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `colore` VARCHAR(7) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=6 AVG_ROW_LENGTH=3276 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `aziende` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `societa` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cognome` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indirizzo` TEXT COLLATE utf8mb4_general_ci,
  `id_comune` INTEGER(11) NOT NULL,
  `id_provincia` INTEGER(11) NOT NULL,
  `id_regione` INTEGER(11) NOT NULL,
  `p_iva` VARCHAR(11) COLLATE utf8mb4_general_ci NOT NULL,
  `rea` VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pec` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=2 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `cantieri` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` INTEGER(11) NOT NULL,
  `id_azienda` INTEGER(11) DEFAULT NULL,
  `id_posatore` INTEGER(11) NOT NULL,
  `id_comune` INTEGER(11) NOT NULL,
  `id_provincia` INTEGER(11) NOT NULL,
  `id_regione` INTEGER(11) NOT NULL,
  `id_stato_cantiere` INTEGER(11) NOT NULL,
  `indirizzo` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `note` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `data_inizio` DATE NOT NULL,
  `data_fine` DATE NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `fk_cantieri_clienti` USING BTREE (`id_cliente`) COMMENT '',
   INDEX `fk_cantieri_posatore` USING BTREE (`id_posatore`) COMMENT '',
   INDEX `fk_cantieri_regione` USING BTREE (`id_regione`) COMMENT '',
   INDEX `fk_cantieri_provincia` USING BTREE (`id_provincia`) COMMENT '',
   INDEX `fk_cantieri_stato_cantiere` USING BTREE (`id_stato_cantiere`) COMMENT '',
   INDEX `fk_cantieri_azienda` USING BTREE (`id_azienda`) COMMENT '',
  CONSTRAINT `cantieri_ibfk_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clienti` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `cantieri_ibfk_posatore` FOREIGN KEY (`id_posatore`) REFERENCES `posatori` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `cantieri_ibfk_provincia` FOREIGN KEY (`id_provincia`) REFERENCES `province` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `cantieri_ibfk_regione` FOREIGN KEY (`id_regione`) REFERENCES `regioni` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `cantieri_ibfk_stato_cantiere` FOREIGN KEY (`id_stato_cantiere`) REFERENCES `stato_cantiere` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cantieri_azienda` FOREIGN KEY (`id_azienda`) REFERENCES `aziende` (`id`) ON DELETE SET NULL
)ENGINE=InnoDB
AUTO_INCREMENT=14 AVG_ROW_LENGTH=1489 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `allegati_cantiere` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_cantiere` INTEGER(11) NOT NULL,
  `nome_file` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_cantiere` USING BTREE (`id_cantiere`) COMMENT '',
  CONSTRAINT `allegati_cantiere_ibfk_1` FOREIGN KEY (`id_cantiere`) REFERENCES `cantieri` (`id`) ON UPDATE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=22 AVG_ROW_LENGTH=780 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `banche` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `agenzia` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `iban` VARCHAR(34) COLLATE utf8mb4_general_ci NOT NULL,
  `bic` VARCHAR(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `swift` VARCHAR(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` VARCHAR(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indirizzo` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_comune` INTEGER(11) NOT NULL DEFAULT 0,
  `id_provincia` INTEGER(11) NOT NULL DEFAULT 0,
  `id_regione` INTEGER(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=4 AVG_ROW_LENGTH=5461 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `cantiere_indirizzi` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_cantiere` INTEGER(11) NOT NULL,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indirizzo` TEXT COLLATE utf8mb4_general_ci,
  `distanza` VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitudine` DECIMAL(10,7) DEFAULT NULL,
  `longitudine` DECIMAL(10,7) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_cantiere` USING BTREE (`id_cantiere`) COMMENT '',
  CONSTRAINT `cantiere_indirizzi_ibfk_1` FOREIGN KEY (`id_cantiere`) REFERENCES `cantieri` (`id`) ON DELETE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `categorie` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` TEXT COLLATE utf8mb4_general_ci,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `comuni` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `comune` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `cap` VARCHAR(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_provincia` INTEGER(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_provincia` USING BTREE (`id_provincia`) COMMENT '',
  CONSTRAINT `comuni_ibfk_1` FOREIGN KEY (`id_provincia`) REFERENCES `province` (`id`) ON UPDATE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=7897 AVG_ROW_LENGTH=60 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `esercizi_commerciali` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `cantiere_id` INTEGER(11) NOT NULL,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `indirizzo` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitudine` DECIMAL(10,8) DEFAULT NULL,
  `longitudine` DECIMAL(11,8) DEFAULT NULL,
  `rating` FLOAT DEFAULT NULL,
  `user_ratings_total` INTEGER(11) DEFAULT NULL,
  `foto_referenza` TEXT COLLATE utf8mb4_general_ci,
  `place_id` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aperto` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `cantiere_id` USING BTREE (`cantiere_id`) COMMENT '',
  CONSTRAINT `esercizi_commerciali_ibfk_1` FOREIGN KEY (`cantiere_id`) REFERENCES `cantieri` (`id`) ON DELETE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `fornitori` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `societa` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `cognome` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `indirizzo` TEXT COLLATE utf8mb4_general_ci,
  `id_comune` INTEGER(11) NOT NULL,
  `id_provincia` INTEGER(11) NOT NULL,
  `id_regione` INTEGER(11) NOT NULL,
  `p_iva` VARCHAR(11) COLLATE utf8mb4_general_ci NOT NULL,
  `rea` VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pec` VARCHAR(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=4 AVG_ROW_LENGTH=5461 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `tipologia` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=9 AVG_ROW_LENGTH=2048 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `listino` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `voce` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `prezzo` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id_tipologia` INTEGER(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_tipologia` USING BTREE (`id_tipologia`) COMMENT '',
  CONSTRAINT `listino_ibfk_1` FOREIGN KEY (`id_tipologia`) REFERENCES `tipologia` (`id`) ON UPDATE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=86 AVG_ROW_LENGTH=192 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `materiali` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` TEXT COLLATE utf8mb4_general_ci,
  `prezzo` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=15 AVG_ROW_LENGTH=1170 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `materiali_cantiere` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_cantiere` INTEGER(11) NOT NULL,
  `nome` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` TEXT COLLATE utf8mb4_general_ci,
  `quantita` INTEGER(11) NOT NULL,
  `prezzo` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_cantiere` USING BTREE (`id_cantiere`) COMMENT '',
  CONSTRAINT `materiali_cantiere_ibfk_1` FOREIGN KEY (`id_cantiere`) REFERENCES `cantieri` (`id`)
)ENGINE=InnoDB
AUTO_INCREMENT=4 AVG_ROW_LENGTH=8192 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `motivo_spese` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `motivo_spesa` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=8 AVG_ROW_LENGTH=2340 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `photogallery_cantiere` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_cantiere` INTEGER(11) NOT NULL,
  `nome_file` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_cantiere` USING BTREE (`id_cantiere`) COMMENT '',
  CONSTRAINT `photogallery_cantiere_ibfk_1` FOREIGN KEY (`id_cantiere`) REFERENCES `cantieri` (`id`) ON UPDATE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=85 AVG_ROW_LENGTH=195 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `scadenze` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `titolo` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` TEXT COLLATE utf8mb4_general_ci,
  `data_scadenza` DATETIME NOT NULL,
  `avviso_email` TINYINT(1) NOT NULL DEFAULT 0,
  `avviso_push` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=6 AVG_ROW_LENGTH=5461 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `spese` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `descrizione` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `importo` DECIMAL(10,2) NOT NULL,
  `data_spesa` DATE NOT NULL,
  `id_motivo_spesa` INTEGER(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `spese_cantiere` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_cantiere` INTEGER(11) NOT NULL,
  `note` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `prezzo` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_cantiere` USING BTREE (`id_cantiere`) COMMENT '',
  CONSTRAINT `spese_cantiere_ibfk_1` FOREIGN KEY (`id_cantiere`) REFERENCES `cantieri` (`id`) ON UPDATE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `utenti` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cognome` VARCHAR(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` VARCHAR(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
  UNIQUE INDEX `email` USING BTREE (`email`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=2 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

CREATE TABLE `voci_cantiere` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_cantiere` INTEGER(11) NOT NULL,
  `id_listino` INTEGER(11) NOT NULL,
  `quantita` INTEGER(11) NOT NULL,
  `prezzo` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `id_cantiere` USING BTREE (`id_cantiere`) COMMENT '',
   INDEX `id_listino` USING BTREE (`id_listino`) COMMENT '',
  CONSTRAINT `voci_cantiere_ibfk_1` FOREIGN KEY (`id_cantiere`) REFERENCES `cantieri` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `voci_cantiere_ibfk_2` FOREIGN KEY (`id_listino`) REFERENCES `listino` (`id`) ON UPDATE CASCADE
)ENGINE=InnoDB
AUTO_INCREMENT=100 AVG_ROW_LENGTH=172 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT=''
;

