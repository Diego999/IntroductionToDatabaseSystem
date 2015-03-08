-- 1. Create tables with unlinked columns, primary keys, unique indexes and simple indexes on future foreign keys

CREATE TABLE `name` (
	`id` INT UNSIGNED,
    `firstname` VARCHAR(255) NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `person_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_first_last_person` (`firstname`, `lastname`, `person_id`)
);

CREATE TABLE `person` (
	`id` INT UNSIGNED,
    `gender` VARCHAR(1) NOT NULL,
    `trivia` TEXT NULL,
    `quotes` TEXT NULL,
    `birthdate` DATE NULL,
    `deathdate` DATE NULL,
    `birthname` TEXT NULL,
    `minibiography` TEXT NULL,
    `spouse` VARCHAR(255) NULL,
    `height` FLOAT NULL,
    `name_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_main_name` (`name_id`)
);

CREATE TABLE `role` (
	`id` INT UNSIGNED,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_name` (`name`)
);

CREATE TABLE `character` (
	`id` INT UNSIGNED,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_name` (`name`)
);

CREATE TABLE `production` (
	`id` INT UNSIGNED,
    `year` YEAR NULL,
    `budget` INT UNSIGNED NULL,
    `title_id` INT UNSIGNED NOT NULL,
    `gender_id` INT UNSIGNED NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_main_title` (`title_id`),
    KEY `idx_gender` (`gender_id`)
);

CREATE TABLE `casting` (
	`id` INT UNSIGNED,
    `person_id` INT UNSIGNED NOT NULL,
    `production_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `character_id` INT UNSIGNED NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_person_prod_role` (`person_id`, `production_id`, `role_id`)
);

CREATE TABLE `title` (
	`id` INT UNSIGNED,
    `title` VARCHAR(255) NOT NULL,
    `production_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_title_production` (`title`, `production_id`)
);

CREATE TABLE `gender` (
	`id` INT UNSIGNED,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_name` (`name`)
);

CREATE TABLE `company` (
	`id` INT UNSIGNED,
    `name` VARCHAR(255) NOT NULL,
    `country_id` INT UNSIGNED NULL,
    `type_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_name_country` (`name`, `country_id`),
    KEY `idx_type` (`type_id`)
);

CREATE TABLE `country` (
	`id` INT UNSIGNED,
    `code` VARCHAR(2) NOT NULL,
	PRIMARY KEY (`id`),
    UNIQUE KEY `un_code` (`code`)
);

CREATE TABLE `type` (
	`id` INT UNSIGNED,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_name` (`name`)
);

CREATE TABLE `singleproduction` (
	`id` INT UNSIGNED,
    PRIMARY KEY (`id`)
);

CREATE TABLE `season` (
	`id` INT UNSIGNED,
    `number` INT NULL,
    `serie_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_serie_number` (`serie_id`, `number`)
);

CREATE TABLE `serie` (
	`id` INT UNSIGNED,
    `yearstart` YEAR NULL,
    `yearend` YEAR NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `episode` (
	`id` INT UNSIGNED,
    `number` INT NULL,
    `season_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_season_number` (`season_id`, `number`)
);

CREATE TABLE `productioncompany` (
	`id` INT UNSIGNED,
    `production_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `un_production_company` (`production_id`, `company_id`)
);

-- 2. add all foreign keys constraints that are on schema

ALTER TABLE `name`
	ADD CONSTRAINT `fk_nametoperson`FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `person`
	ADD CONSTRAINT `fk_mainname` FOREIGN KEY (`name_id`) REFERENCES `name` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `production`
	ADD CONSTRAINT `fk_maintitle` FOREIGN KEY (`title_id`) REFERENCES `title` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_productionhasgender` FOREIGN KEY (`gender_id`) REFERENCES `gender` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `casting`
	ADD CONSTRAINT `fk_casting_person` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `fk_casting_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
	ADD CONSTRAINT `fk_casting_production` FOREIGN KEY (`production_id`) REFERENCES `production` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `fk_casting_character` FOREIGN KEY (`character_id`) REFERENCES `character` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `title`
	ADD CONSTRAINT `fk_titletoproduction` FOREIGN KEY (`production_id`) REFERENCES `production` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `company`
	ADD CONSTRAINT `fk_companyhascountry` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_companyhastype` FOREIGN KEY (`type_id`) REFERENCES `type` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `season`
	ADD CONSTRAINT `fk_seasonhasserie` FOREIGN KEY (`serie_id`) REFERENCES `serie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `episode`
	ADD CONSTRAINT `fk_episodehasseason` FOREIGN KEY (`season_id`) REFERENCES `season` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `productioncompany`
	ADD CONSTRAINT `fk_productioncompany_production` FOREIGN KEY (`production_id`) REFERENCES `production` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_productioncompany_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 3. add foreign keys constraints relative to "ISA" architecture

ALTER TABLE `serie`
	ADD CONSTRAINT `fk_serie_isa_production` FOREIGN KEY (`id`) REFERENCES `production` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `episode`
	ADD CONSTRAINT `fk_episode_isa_production` FOREIGN KEY (`id`) REFERENCES `production` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `singleproduction`
	ADD CONSTRAINT `fk_singleproduction_isa_production` FOREIGN KEY (`id`) REFERENCES `production` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;