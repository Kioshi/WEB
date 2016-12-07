
DROP TABLE IF EXISTS `hral`;
DROP TABLE IF EXISTS `vypujcky`;
DROP TABLE IF EXISTS `clenove`;
DROP TABLE IF EXISTS `hry`;
DROP TABLE IF EXISTS `skrine`;


CREATE TABLE IF NOT EXISTS `skrine` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `skrin` VARCHAR(2) NOT NULL UNIQUE,
  PRIMARY KEY (`id`))
ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `hry` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nazev` VARCHAR(45) NOT NULL,
  `alternativniNazev` VARCHAR(45) NOT NULL DEFAULT '',
  `cena` INT NOT NULL DEFAULT '0',
  `datumPorizeni` DATE NOT NULL DEFAULT NOW(),
  `zpusob` VARCHAR(45) NOT NULL DEFAULT '',
  `pozn` TEXT NULL,
  `link` VARCHAR(200) NOT NULL DEFAULT '',
  `hernidoba` INT NOT NULL DEFAULT '1',
  `skrine` INT UNSIGNED NOT NULL,
  `minPocet` INT NOT NULL DEFAULT '1',
  `maxPocet` INT NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  INDEX `fk_hry_skrine_idx` (`skrine` ASC),
  CONSTRAINT `fk_hry_skrine`
    FOREIGN KEY (`skrine`)
    REFERENCES `skrine` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `clenove` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jmeno` VARCHAR(45) NOT NULL,
  `prezdivka` VARCHAR(45) NOT NULL DEFAULT '',
  `aktivni` TINYINT(1) NOT NULL DEFAULT '1',
  `userName` VARCHAR(45) NULL UNIQUE,
  `passHash` TEXT NULL,
  `session` TEXT NULL,
  `role` INT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_clenove_role`
    FOREIGN KEY (`role`)
    REFERENCES `role` (`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
)ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `vypujcky` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `clenove_id` INT UNSIGNED NULL,
  `hry_id` INT UNSIGNED NULL,
  `datumPujceni` DATE NULL,
  `datumVraceni` DATE NULL,
  `pozn` TEXT(100) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_clenove_has_hry_hry1_idx` (`hry_id` ASC),
  INDEX `fk_clenove_has_hry_clenove1_idx` (`clenove_id` ASC),
  CONSTRAINT `fk_clenove_has_hry_clenove1`
    FOREIGN KEY (`clenove_id`)
    REFERENCES `clenove` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_clenove_has_hry_hry1`
    FOREIGN KEY (`hry_id`)
    REFERENCES `hry` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `hral` (
  `hry_id` INT UNSIGNED NOT NULL,
  `clenove_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`hry_id`, `clenove_id`),
  INDEX `fk_hry_has_clenove_clenove1_idx` (`clenove_id` ASC),
  INDEX `fk_hry_has_clenove_hry1_idx` (`hry_id` ASC),
  CONSTRAINT `fk_hry_has_clenove_hry1`
    FOREIGN KEY (`hry_id`)
    REFERENCES `hry` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hry_has_clenove_clenove1`
    FOREIGN KEY (`clenove_id`)
    REFERENCES `clenove` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `role` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` VARCHAR(20) DEFAULT '',
  PRIMARY KEY (`id`))
ENGINE = INNODB;


DROP FUNCTION IF EXISTS login;
DELIMITER //
CREATE FUNCTION login(`user` VARCHAR(45)) RETURNS TEXT
BEGIN
  DECLARE `sess` TEXT;
  DECLARE `usr` VARCHAR(45);
  SELECT `userName`, `session` INTO `usr`,`sess` FROM clenove WHERE `userName` = `user`;
  IF `sess` IS NULL AND `usr` IS NOT NULL THEN
    SELECT UUID() INTO `sess`;
    UPDATE clenove SET `session` = `sess` WHERE `userName` = `user`;
  END IF;
  RETURN `sess`;
END;//
DELIMITER ;


DROP FUNCTION IF EXISTS register;
DELIMITER //
CREATE FUNCTION register(`user` VARCHAR(45), `pass` TEXT, `memberId` INT) RETURNS BOOL
BEGIN
  DECLARE `usr` VARCHAR(45);
  DECLARE `res` BOOL DEFAULT '0';
  
  SELECT `userName` INTO `usr` FROM clenove WHERE `id` = `memberId`;
  IF `usr` IS NULL THEN
    UPDATE clenove SET `userName` = `user`, `passHash` = `pass` WHERE `id` = `memberId`;
    SELECT '1' INTO `res`;
  END IF;  
  RETURN `res`;
END;//
DELIMITER ;
