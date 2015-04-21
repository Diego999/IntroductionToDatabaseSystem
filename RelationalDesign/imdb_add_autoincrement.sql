-- temporary disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;

-- add auto-increment clauses on ID fields that were kept without auto-increment during data import

-- casting already has auto-increment

-- put auto-increment on character
ALTER TABLE `character` CHANGE COLUMN `id` `id` INT UNSIGNED AUTO_INCREMENT;

-- put auto-increment on company
ALTER TABLE `company` CHANGE COLUMN `id` `id` INT UNSIGNED AUTO_INCREMENT;

-- country already has auto-increment

-- episode do not need auto-increment, part of ISA hierarchy (auto-increment of production applies)

-- gender already has auto-increment

-- kind already has auto-increment

-- name already has auto-increment

-- put auto-increment on person
ALTER TABLE `person` CHANGE COLUMN `id` `id` INT UNSIGNED AUTO_INCREMENT;

-- put auto-increment on production
ALTER TABLE `production` CHANGE COLUMN `id` `id` INT UNSIGNED AUTO_INCREMENT;

-- put auto-increment on productioncompany
ALTER TABLE `productioncompany` CHANGE COLUMN `id` `id` INT UNSIGNED AUTO_INCREMENT;

-- role already has auto-increment

-- season already has auto-increment

-- serie do not need auto-increment, part of ISA hierarchy (auto-increment of production applies)

-- singleproduction do not need auto-increment, part of ISA hierarchy (auto-increment of production applies)

-- title already has auto-increment

-- type already has auto-increment

-- re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;