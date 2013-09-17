SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 ;
USE `test` ;

-- -----------------------------------------------------
-- Table `test`.`types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `test`.`subtypes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`subtypes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `test`.`parts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`parts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `inventory` INT NULL DEFAULT 0 COMMENT 'current inventory' ,
  `ordered` INT NULL DEFAULT 0 ,
  `partNo` VARCHAR(45) NULL DEFAULT NULL ,
  `footprint` VARCHAR(45) NULL DEFAULT NULL ,
  `value` VARCHAR(45) NULL DEFAULT NULL ,
  `voltage` VARCHAR(45) NULL DEFAULT NULL ,
  `tolerance` VARCHAR(45) NULL DEFAULT NULL ,
  `types_id` INT NOT NULL ,
  `subtypes_id` INT NOT NULL ,
  `partscol` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_parts_types` (`types_id` ASC) ,
  INDEX `fk_parts_subtype1` (`subtypes_id` ASC) ,
  CONSTRAINT `fk_parts_types`
    FOREIGN KEY (`types_id` )
    REFERENCES `test`.`types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_parts_subtype1`
    FOREIGN KEY (`subtypes_id` )
    REFERENCES `test`.`subtypes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Table `test`.`products`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`products` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `inventory` INT NULL DEFAULT 0 ,
  `needed` INT NULL DEFAULT 0 COMMENT 'number of products\nneeded to be built' ,
  `sold` INT NULL DEFAULT 0 COMMENT 'number of product\nsold' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `test`.`dataface__mtimes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`dataface__mtimes` (
  `name` VARCHAR(255) NOT NULL ,
  `mtime` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `test`.`dataface__preferences`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`dataface__preferences` (
  `pref_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(64) NOT NULL ,
  `table` VARCHAR(128) NOT NULL ,
  `record_id` VARCHAR(255) NOT NULL ,
  `key` VARCHAR(128) NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`pref_id`) ,
  INDEX `username` (`username` ASC) ,
  INDEX `table` (`table` ASC) ,
  INDEX `record_id` (`record_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `test`.`dataface__record_mtimes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`dataface__record_mtimes` (
  `recordhash` VARCHAR(32) NOT NULL ,
  `recordid` VARCHAR(255) NOT NULL ,
  `mtime` INT(11) NOT NULL ,
  PRIMARY KEY (`recordhash`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `test`.`dataface__version`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`dataface__version` (
  `version` INT(5) NOT NULL DEFAULT '0' )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `test`.`product_part`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`product_part` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'unique bom id' ,
  `products_id` INT NOT NULL COMMENT 'products key' ,
  `parts_id` INT(11) NOT NULL COMMENT 'parts key' ,
  `qty` INT NULL DEFAULT 0 COMMENT 'quantity used per product' ,
  `needed` INT NULL DEFAULT 0 COMMENT 'number needed for this particular products_id' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_products_has_parts_parts1` (`parts_id` ASC) ,
  INDEX `fk_products_has_parts_products1` (`products_id` ASC) ,
  CONSTRAINT `fk_products_has_parts_products1`
    FOREIGN KEY (`products_id` )
    REFERENCES `test`.`products` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_has_parts_parts1`
    FOREIGN KEY (`parts_id` )
    REFERENCES `test`.`parts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `test`.`build_history`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `test`.`build_history` (
  `id` INT NOT NULL ,
  `qty` INT NULL ,
  `products_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_build_history_products1` (`products_id` ASC) ,
  CONSTRAINT `fk_build_history_products1`
    FOREIGN KEY (`products_id` )
    REFERENCES `test`.`products` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Placeholder table for view `test`.`partcount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `test`.`partcount` (`id` INT, `total` INT, `available` INT);

-- -----------------------------------------------------
-- Placeholder table for view `test`.`builds`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `test`.`builds` (`parts_id` INT, `total` INT);

-- -----------------------------------------------------
-- View `test`.`partcount`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `test`.`partcount`;
USE `test`;
CREATE  OR REPLACE VIEW `test`.`partcount` AS
    SELECT parts.id, 
        sum(product_part.qty * products.needed) AS total,
        parts.inventory
            + parts.ordered
            - sum(product_part.qty * products.needed) AS available
    FROM parts 
    LEFT JOIN product_part ON parts.id=product_part.parts_id 
    LEFT JOIN products ON products.id=product_part.products_id 
    GROUP BY parts.id;
;

-- -----------------------------------------------------
-- View `test`.`builds`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `test`.`builds`;
USE `test`;
CREATE  OR REPLACE VIEW `test`.`builds` AS
    SELECT 
        product_part.parts_id, 
        sum(product_part.qty * build_history.qty) AS total
    FROM product_part 
    JOIN build_history 
        ON product_part.products_id = build_history.products_id
    GROUP BY parts_id;
;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
