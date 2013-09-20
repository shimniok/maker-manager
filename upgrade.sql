SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `test`.`parts` DROP FOREIGN KEY `fk_parts_types` ;

ALTER TABLE `test`.`build_history` DROP FOREIGN KEY `fk_build_history_products1` ;

ALTER TABLE `test`.`parts` DROP COLUMN `partscol` , CHANGE COLUMN `inventory` `inventory` INT(11) NULL DEFAULT '0' COMMENT 'quantity in inventory'  , CHANGE COLUMN `ordered` `ordered` INT(11) NULL DEFAULT '0' COMMENT 'quantity on order'  , CHANGE COLUMN `partNo` `partNo` VARCHAR(45) NULL DEFAULT NULL COMMENT 'supplier part number'  , CHANGE COLUMN `footprint` `footprint` VARCHAR(45) NULL DEFAULT NULL COMMENT 'part footprint'  , CHANGE COLUMN `value` `value` VARCHAR(45) NULL DEFAULT NULL COMMENT 'value associated with part (passives,)'  , CHANGE COLUMN `voltage` `voltage` VARCHAR(45) NULL DEFAULT NULL COMMENT 'voltage rating'  , CHANGE COLUMN `tolerance` `tolerance` VARCHAR(45) NULL DEFAULT NULL COMMENT 'tolerance (passives)'  , CHANGE COLUMN `types_id` `types_id` INT(11) NOT NULL COMMENT 'part type: types key'  , CHANGE COLUMN `subtypes_id` `subtypes_id` INT(11) NOT NULL COMMENT 'part subtype: subtypes key'  , DROP FOREIGN KEY `fk_parts_subtype1` ;

ALTER TABLE `test`.`parts` 
  ADD CONSTRAINT `fk_parts_subtype1`
  FOREIGN KEY (`subtypes_id` )
  REFERENCES `test`.`subtypes` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
  ADD CONSTRAINT `fk_parts_types`
  FOREIGN KEY (`types_id` )
  REFERENCES `test`.`types` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `test`.`products` CHANGE COLUMN `name` `name` VARCHAR(45) NULL DEFAULT NULL COMMENT 'product name'  , CHANGE COLUMN `inventory` `inventory` INT(11) NULL DEFAULT 0 COMMENT 'quantity in inventory'  , CHANGE COLUMN `needed` `needed` INT(11) NULL DEFAULT 0 COMMENT 'number of products\nto be built'  ;

ALTER TABLE `test`.`types` CHANGE COLUMN `name` `name` VARCHAR(45) NULL DEFAULT NULL COMMENT 'type name'  ;

ALTER TABLE `test`.`subtypes` CHANGE COLUMN `name` `name` VARCHAR(45) NULL DEFAULT NULL COMMENT 'subtype name'  ;

ALTER TABLE `test`.`product_part` DROP COLUMN `needed` ;

ALTER TABLE `test`.`build_history` CHANGE COLUMN `qty` `qty` INT(11) NULL DEFAULT NULL COMMENT 'number of product built'  , CHANGE COLUMN `products_id` `products_id` INT(11) NOT NULL COMMENT 'product built'  , 
  ADD CONSTRAINT `fk_build_history_products1`
  FOREIGN KEY (`products_id` )
  REFERENCES `test`.`products` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION, 
COMMENT = 'build_hour' ;

DROP TABLE IF EXISTS `test`.`dataface__mtimes` ;


-- -----------------------------------------------------
-- Placeholder table for view `test`.`partcount`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `test`.`partcount` (`id` INT, `total` INT, `available` INT);

-- -----------------------------------------------------
-- Placeholder table for view `test`.`builds`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `test`.`builds` (`parts_id` INT, `total` INT);


USE `test`;

-- -----------------------------------------------------
-- View `test`.`partcount`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `test`.`partcount`;
USE `test`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`partcount` AS select `test`.`parts`.`id` AS `id`,sum((`test`.`product_part`.`qty` * `test`.`products`.`needed`)) AS `total`,((`test`.`parts`.`inventory` + `test`.`parts`.`ordered`) - sum((`test`.`product_part`.`qty` * `test`.`products`.`needed`))) AS `available` from ((`test`.`parts` left join `test`.`product_part` on((`test`.`parts`.`id` = `test`.`product_part`.`parts_id`))) left join `test`.`products` on((`test`.`products`.`id` = `test`.`product_part`.`products_id`))) group by `test`.`parts`.`id`;


USE `test`;

-- -----------------------------------------------------
-- View `test`.`builds`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `test`.`builds`;
USE `test`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `test`.`builds` AS select `test`.`product_part`.`parts_id` AS `parts_id`,sum((`test`.`product_part`.`qty` * `test`.`build_history`.`qty`)) AS `total` from (`test`.`product_part` join `test`.`build_history` on((`test`.`product_part`.`products_id` = `test`.`build_history`.`products_id`))) group by `test`.`product_part`.`parts_id`;

DELIMITER $$
USE `test`$$
CREATE PROCEDURE `test`.`build_one` (prod_id INT)
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

DELIMITER ;

DELIMITER $$
USE `test`$$


CREATE PROCEDURE `test`.`unbuild_one` (build_id INT)
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

DELIMITER ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
