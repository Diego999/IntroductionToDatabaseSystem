-- check if a given production is single, serie or episode (here 2998449 = The Hunger Games)
(
	SELECT SP.`id` AS `single`, NULL AS `serie`, NULL AS `episode`
	FROM `singleproduction` SP
	WHERE SP.`id`=2998449
) UNION (
	SELECT NULL AS `single`, SE.`id` AS `serie`, NULL AS `episode`
	FROM `serie` SE
	WHERE SE.`id`=2998449
) UNION (
	SELECT NULL AS `single`, NULL AS `serie`, EP.`id` AS `episode`
	FROM `episode` EP
	WHERE EP.`id`=2998449
);

-- gather information about a single movie with particular ID (here 2998449 = The Hunger Games)
SELECT PR.`id` AS `prod_id`, TI.`title` AS `prod_title`, PR.`year` AS `prod_year`, KI.`name` AS `prod_kind`, GE.`name` AS `prod_gender`, PR.`title_id` AS `maintitle_id`
FROM
	`production` PR
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`
    INNER JOIN `singleproduction` SP ON PR.`id` = SP.`id`
    INNER JOIN `kind` KI ON SP.`kind_id` = KI.`id`
WHERE
	PR.`id`=2998449;
    
-- gather information about an episode with particular ID (here 11316 = 02pm-03pm, 24h chrono)
SELECT EP.`id` AS `prod_id`, EP_TI.`title` AS `episode_title`, EP.`number` AS `episode_number`, EP_PR.`year` AS `episode_year`, EP_TI.`id` AS `maintitle_id`, SEA.`number` AS `season_number`, SER.`id` AS `serie_id`, SER_TI.`title` AS `serie_title`, SER_GE.`name` AS `serie_gender`
FROM
	`episode` EP
    INNER JOIN `production` EP_PR ON EP.`id` = EP_PR.`id`
    INNER JOIN `title` EP_TI ON EP_PR.`title_id` = EP_TI.`id`
    INNER JOIN `season` SEA ON EP.`season_id` = SEA.`id`
    INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`
    INNER JOIN `production` SER_PR ON SER.`id` = SER_PR.`id`
    INNER JOIN `title` SER_TI ON SER_PR.`title_id` = SER_TI.`id`
    LEFT JOIN `gender` SER_GE ON SER_PR.`gender_id` = SER_GE.`id`
WHERE
	EP.`id`=11316;
    
-- gather information about a serie with particular ID (here 11306 = 24)
SELECT SER.`id` AS `prod_id`, TI.`title` AS `serie_title`, SER.`yearstart` AS `serie_yearstart`, SER.`yearend` AS `serie_yearend`, GE.`name` AS `serie_gender`, TI.`id` AS `maintitle_id`, COUNT(DISTINCT SEA.`id`) AS `season_count`, COUNT(DISTINCT EP.`id`) AS `episode_count`
FROM
	`serie` SER
    INNER JOIN `production` PR ON SER.`id` = PR.`id`
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`
    LEFT JOIN `season` SEA ON SER.`id` = SEA.`serie_id`
    LEFT JOIN `episode` EP ON SEA.`id` = EP.`season_id`
WHERE
	SER.`id`=11306
GROUP BY SER.`id`;
    
-- gather list of seasons of a serie with particular ID (here 11306 = 24)
SELECT SEA.`id` AS `season_id`, SEA.`number` AS `season_number`, COUNT(DISTINCT EP.`id`) AS `episode_count`
FROM
	`season` SEA
    INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`
WHERE
	SEA.`serie_id`=11306
GROUP BY SEA.`id`
ORDER BY SEA.`number`;

-- gather information of serie/season with particular season ID (here 920 = season 1 of 24)
SELECT SEA.`number` AS `season_number`, TI.`title` AS `serie_title`
FROM
	`season` SEA
    INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`
    INNER JOIN `production` PR ON SER.`id` = PR.`id`
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
WHERE
	SEA.`id`=920;

-- gather list of episodes of a season with particular ID (here 920 = season 1 of 24)
SELECT EP.`id` AS `episode_id`, TI.`title` AS `episode_title`, EP.`number` AS `episode_number`
FROM
	`episode` EP
    INNER JOIN `production` PR ON EP.`id` = PR.`id`
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
WHERE
	EP.`season_id`=920
ORDER BY EP.`number`;

-- gather the casting of a given production (here 2998449 = The Hunger Games)
SELECT PE.`id` AS `person_id`, NA.`lastname` AS `person_lastname`, NA.`firstname` AS `person_firstname`, RO.`name` AS `role_name`, CH.`name` AS `char_name`
FROM
	`casting` CA
    INNER JOIN `person` PE ON CA.`person_id` = PE.`id`
    INNER JOIN `name` NA ON PE.`name_id` = NA.`id`
    INNER JOIN `role` RO ON CA.`role_id` = RO.`id`
    LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`
WHERE
	CA.`production_id`=2998449;
    
-- gather the production companies of a given production (here 2998449 = The Hunger Games)
SELECT COM.`id` AS `id`, COM.`name` AS `name`, COU.`code` AS `country`, TY.`name` AS `type`
FROM
	`productioncompany` PC
    INNER JOIN `company` COM ON PC.`company_id` = COM.`id`
    INNER JOIN `country` COU ON COM.`country_id` = COU.`id`
    INNER JOIN `type` TY ON PC.`type_id` = TY.`id`
WHERE
	PC.`production_id`=2998449;
    
-- gather the alternative titles of a given production, except main title (here 2998449 = The Hunger Games, main title is 2998451)
SELECT TI.`title`
FROM
	`title` TI
WHERE
	TI.`production_id`=2998449
    AND TI.`id`!=2998451;













