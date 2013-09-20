SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `subtypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `subtypes` ;

CREATE  TABLE IF NOT EXISTS `subtypes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL COMMENT 'subtype name' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `types` ;

CREATE  TABLE IF NOT EXISTS `types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL COMMENT 'type name' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `parts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `parts` ;

CREATE  TABLE IF NOT EXISTS `parts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `inventory` INT(11) NULL DEFAULT '0' COMMENT 'quantity in inventory' ,
  `ordered` INT(11) NULL DEFAULT '0' COMMENT 'quantity on order' ,
  `partNo` VARCHAR(45) NULL DEFAULT NULL COMMENT 'supplier part number' ,
  `footprint` VARCHAR(45) NULL DEFAULT NULL COMMENT 'part footprint' ,
  `value` VARCHAR(45) NULL DEFAULT NULL COMMENT 'value associated with part (passives,)' ,
  `voltage` VARCHAR(45) NULL DEFAULT NULL COMMENT 'voltage rating' ,
  `tolerance` VARCHAR(45) NULL DEFAULT NULL COMMENT 'tolerance (passives)' ,
  `types_id` INT(11) NOT NULL COMMENT 'part type: types key' ,
  `subtypes_id` INT(11) NOT NULL COMMENT 'part subtype: subtypes key' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_parts_types` (`types_id` ASC) ,
  INDEX `fk_parts_subtype1` (`subtypes_id` ASC) ,
  CONSTRAINT `fk_parts_subtype1`
    FOREIGN KEY (`subtypes_id` )
    REFERENCES `subtypes` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_parts_types`
    FOREIGN KEY (`types_id` )
    REFERENCES `types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 122
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Table `products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `products` ;

CREATE  TABLE IF NOT EXISTS `products` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL COMMENT 'product name' ,
  `inventory` INT NULL DEFAULT 0 COMMENT 'quantity in inventory' ,
  `needed` INT NULL DEFAULT 0 COMMENT 'number of products\nto be built' ,
  `sold` INT NULL DEFAULT 0 COMMENT 'number of product\nsold' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `product_part`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `product_part` ;

CREATE  TABLE IF NOT EXISTS `product_part` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'unique bom id' ,
  `products_id` INT NOT NULL COMMENT 'products key' ,
  `parts_id` INT(11) NOT NULL COMMENT 'parts key' ,
  `qty` INT NULL DEFAULT 0 COMMENT 'quantity used per product' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_products_has_parts_parts1` (`parts_id` ASC) ,
  INDEX `fk_products_has_parts_products1` (`products_id` ASC) ,
  CONSTRAINT `fk_products_has_parts_products1`
    FOREIGN KEY (`products_id` )
    REFERENCES `products` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_has_parts_parts1`
    FOREIGN KEY (`parts_id` )
    REFERENCES `parts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `build_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `build_history` ;

CREATE  TABLE IF NOT EXISTS `build_history` (
  `id` INT(11) NOT NULL ,
  `qty` INT(11) NULL DEFAULT NULL COMMENT 'number of product built' ,
  `products_id` INT(11) NOT NULL COMMENT 'product built' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_build_history_products1` (`products_id` ASC) ,
  CONSTRAINT `fk_build_history_products1`
    FOREIGN KEY (`products_id` )
    REFERENCES `products` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci
COMMENT = 'build_hour';


-- -----------------------------------------------------
-- Placeholder table for view `partcount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `partcount` (`id` INT, `total` INT, `available` INT);

-- -----------------------------------------------------
-- Placeholder table for view `builds`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `builds` (`parts_id` INT, `total` INT);

-- -----------------------------------------------------
-- procedure build_one
-- -----------------------------------------------------
DROP procedure IF EXISTS `build_one`;
CREATE PROCEDURE `build_one` (prod_id INT)
BEGIN
    -- update products needed and inventory, parts inventory
    update product_part
        inner join products on product_part.products_id = products.id
        inner join parts on product_part.parts_id = parts.id
        set
            parts.inventory=parts.inventory-product_part.qty, -- update parts inventory based on qty
            products.needed=products.needed-1,                -- decrement product needed
            products.inventory=products.inventory+1           -- increment product inventory
        where product_part.products_id=prod_id;
    -- add/update an entry to build_history
    insert into build_history (id, qty, products_id) 
        values(date_format(now(),'%Y%m%d%H'), 1, prod_id)     -- bucket builds per hour/day/month/year
        on duplicate key update qty=qty+values(qty);
END 
$$

-- -----------------------------------------------------
-- procedure unbuild_one
-- -----------------------------------------------------
DROP procedure IF EXISTS `unbuild_one`;


CREATE PROCEDURE `unbuild_one` (build_id INT)
BEGIN
    -- update products needed and inventory, parts inventory, build_history
    update product_part
        inner join build_history on product_part.products_id=build_history.products_id
        inner join products on product_part.products_id = products.id
        inner join parts on product_part.parts_id = parts.id
        set
            parts.inventory=parts.inventory+product_part.qty, -- update parts inventory based on qty
            products.needed=products.needed+1,                -- decrement product needed
            products.inventory=products.inventory-1,          -- increment product inventory
            build_history.qty=build_history.qty-1
        where build_history.id = build_id and build_history.qty > 0;
END 
$$

-- -----------------------------------------------------
-- View `partcount`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `partcount` ;
DROP TABLE IF EXISTS `partcount`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `partcount` AS select `parts`.`id` AS `id`,sum((`product_part`.`qty` * `products`.`needed`)) AS `total`,((`parts`.`inventory` + `parts`.`ordered`) - sum((`product_part`.`qty` * `products`.`needed`))) AS `available` from ((`parts` left join `product_part` on((`parts`.`id` = `product_part`.`parts_id`))) left join `products` on((`products`.`id` = `product_part`.`products_id`))) group by `parts`.`id`;

-- -----------------------------------------------------
-- View `builds`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `builds` ;
DROP TABLE IF EXISTS `builds`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `builds` AS select `product_part`.`parts_id` AS `parts_id`,sum((`product_part`.`qty` * `build_history`.`qty`)) AS `total` from (`product_part` join `build_history` on((`product_part`.`products_id` = `build_history`.`products_id`))) group by `product_part`.`parts_id`;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
