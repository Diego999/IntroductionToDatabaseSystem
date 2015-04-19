-- Search all characters with production and person counts, following the LIKE condition (here Katniss Everdeen)
SELECT DISTINCT CH.`id`, CH.`name`, COUNT(DISTINCT CA.`person_id`) AS `persons_count`, COUNT(DISTINCT CA.`production_id`) AS `productions_count`
FROM
	`character` CH
    INNER JOIN `casting` CA ON CH.`id`=CA.`character_id`
WHERE
	CH.`name` COLLATE UTF8_GENERAL_CI LIKE "%everdeen%"
GROUP BY CH.`id`
ORDER BY CH.`id`;
    
-- gather the name of character based on its ID (here 2604958 = Katniss Everdeen)
SELECT DISTINCT CH.`name`
FROM
	`character` CH
WHERE
	CH.`id` = 2604958;

-- gather all persons who played a given character (here 2604958 = Katniss Everdeen)
SELECT DISTINCT PE.`id`, NA.`firstname`, NA.`lastname`
FROM
	`person` PE
    INNER JOIN `name` NA ON PE.`name_id` = NA.`id`
    INNER JOIN `casting` CA ON PE.`id` = CA.`person_id`
WHERE
	CA.`character_id` = 2604958;
    
-- gather all movies in which a given character appears (here 2604958 = Katniss Everdeen)
SELECT DISTINCT PR.`id`, TI.`title`, PR.`year`
FROM
	`production` PR
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    INNER JOIN `casting` CA ON PR.`id` = CA.`production_id`
WHERE
	CA.`character_id` = 2604958;
    
-- Search all companies with production and distribution counts, following the LIKE condition (here Lionsgate)
SELECT DISTINCT COM.`id` AS `id`, COM.`name` AS `name`, COU.`code` AS `country`, COUNT(DISTINCT PC_PROD.`production_id`) AS `produced_count`, COUNT(DISTINCT PC_DIST.`production_id`) AS `distributed_count`
FROM
	`company` COM
    LEFT JOIN `country` COU ON COM.`country_id` = COU.`id`
    INNER JOIN `productioncompany` PC_PROD ON COM.`id` = PC_PROD.`company_id` AND PC_PROD.`type_id` = (SELECT `id` FROM `type` WHERE `name`="production companies")
    INNER JOIN `productioncompany` PC_DIST ON COM.`id` = PC_DIST.`company_id` AND PC_DIST.`type_id` = (SELECT `id` FROM `type` WHERE `name`="distributors")
WHERE
	COM.`name` COLLATE UTF8_GENERAL_CI LIKE "%lionsgate%"
GROUP BY COM.`id`
ORDER BY COM.`name`;

-- gather the name and country of a company based on its ID (here 3293 = Lionsgate us)
SELECT DISTINCT COM.`name` AS `name`, COU.`code` AS `country`
FROM
	`company` COM
	LEFT JOIN `country` COU ON COM.`country_id` = COU.`id`
WHERE
	COM.`id`=3293;
    
-- gather all movies produced by a given company (here 3293 = Lionsgate us)
SELECT DISTINCT PR.`id` AS `id`, TI.`title` AS `title`, PR.`year` AS `year`
FROM
	`production` PR
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    INNER JOIN `productioncompany` PC ON PR.`id` = PC.`production_id`
    INNER JOIN `type` TY ON PC.`type_id` = TY.`id`
WHERE
	TY.`name`="production companies"
    AND PC.`company_id`=3293
ORDER BY PR.`year` DESC, TI.`title` ASC;

-- gather all movies distributed by a given company (here 3293 = Lionsgate us)
SELECT DISTINCT PR.`id` AS `id`, TI.`title` AS `title`, PR.`year` AS `year`
FROM
	`production` PR
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    INNER JOIN `productioncompany` PC ON PR.`id` = PC.`production_id`
    INNER JOIN `type` TY ON PC.`type_id` = TY.`id`
WHERE
	TY.`name`="distributors"
    AND PC.`company_id`=3293
ORDER BY PR.`year` DESC, TI.`title` ASC;

-- Search all genders with movies counts, following the LIKE condition (here action)
SELECT DISTINCT GE.`id` AS `id`, GE.`name` AS `name`, COUNT(DISTINCT PR.`id`) AS `count_prod`
FROM
	`gender` GE
    INNER JOIN `production` PR ON GE.`id` = PR.`gender_id`
WHERE
	GE.`name` COLLATE UTF8_GENERAL_CI LIKE "%action%"
GROUP BY GE.`id`
ORDER BY GE.`name`;

-- gather the name of a gender by its ID (here 19 = Action)
SELECT DISTINCT GE.`name`
FROM
	`gender` GE
WHERE
	GE.`id`=19;

-- gather all productions of a given gender
SELECT DISTINCT PR.`id` AS `id`, TI.`title` AS `title`, PR.`year` AS `year`
FROM
	`production` PR
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    INNER JOIN `gender` GE ON PR.`gender_id` = GE.`id`
WHERE
	GE.`id`=19
ORDER BY PR.`year` DESC, TI.`title` ASC;

-- Search all persons, following the LIKE condition on firstname, lastname (here Jennifer Lawrence)
SELECT DISTINCT PE.`id`, NA_MAIN.`lastname`, NA_MAIN.`firstname`, PE.`birthdate`, PE.`deathdate`
FROM
	`person` PE
    INNER JOIN `name` NA_SEARCH ON PE.`id` = NA_SEARCH.`person_id`
    INNER JOIN `name` NA_MAIN ON PE.`name_id` = NA_MAIN.`id`
WHERE
	(
		NA_SEARCH.`lastname` COLLATE UTF8_GENERAL_CI LIKE "%jennifer%"
		OR NA_SEARCH.`lastname` COLLATE UTF8_GENERAL_CI LIKE "%lawrence%"
	) AND (
		NA_SEARCH.`firstname` COLLATE UTF8_GENERAL_CI LIKE "%jennifer%"
        OR NA_SEARCH.`firstname` COLLATE UTF8_GENERAL_CI LIKE "%lawrence%"
	)
GROUP BY PE.`id`
ORDER BY NA_MAIN.`lastname` ASC, NA_MAIN.`firstname` ASC;