CREATE INDEX `hash_productioncompany_type` USING HASH ON `productioncompany` (`type_id`);
CREATE INDEX `idx_name_lastname` ON `name` (`lastname`);

CREATE INDEX `idx_person_height` ON `person` (`height`);
CREATE INDEX `idx_person_birthdate` ON `person` (`birthdate`);
CREATE INDEX `idx_person_deathdate` ON `person` (`deathdate`);

CREATE INDEX `idx_production_year` ON `production` (`year`);

CREATE INDEX `idx_productioncompany_production_id` ON `productioncompany` (`production_id`);
