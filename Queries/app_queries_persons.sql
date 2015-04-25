-- gather informations about a person with particular ID (here 2682115 = Jennifer Lawrence)
SELECT DISTINCT PE.`id`, PE.`gender`, PE.`trivia`, PE.`quotes`, PE.`birthdate`, PE.`deathdate`, PE.`birthname`, PE.`minibiography`, PE.`spouse`, PE.`height`, PE.`name_id`, NA.`lastname`, NA.`firstname`
FROM
	`person` PE
    INNER JOIN `name` NA ON PE.`name_id` = NA.`id`
WHERE
	PE.`id` = /*2682115*/4883832;
    
-- gather alternative names of a person with particular ID, except main name (here 2682115 = Jennifer Lawrence, main name is 2666765)
SELECT DISTINCT NA.`id`, NA.`lastname`, NA.`firstname`
FROM
	`name` NA
WHERE
	NA.`person_id` = 2682115
    AND NA.`id` != 2666765;
    
-- gather roles of a person with particular ID in single productions (here 2682115 = Jennifer Lawrence)
SELECT DISTINCT SP.`id` AS `prod_id`, TI.`title` AS `prod_title`, KI.`name` AS `prod_kind`, GE.`name` AS `prod_gender`, PR.`year` AS `prod_year`, CH.`name` AS `char_name`, RO.`name` AS `role_name`
FROM
	`casting` CA
    INNER JOIN `production` PR ON CA.`production_id` = PR.`id`
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`
    INNER JOIN `singleproduction` SP ON PR.`id` = SP.`id`
    INNER JOIN `kind` KI ON SP.`kind_id` = KI.`id`
    LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`
    INNER JOIN `role` RO ON CA.`role_id` = RO.`id`
WHERE
	CA.`person_id` = 2682115
ORDER BY PR.`year` DESC, TI.`title` ASC;
    
-- gather roles of a person with particular ID in series (here 1831321 = Kiefer Sutherland)
(
	SELECT SER.`id` AS `prod_id`, TI.`title` AS `prod_title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `prod_yearstart`, SER.`yearend` AS `prod_yearend`, GE.`name` AS `prod_gender`, CH.`name` AS `char_name`, RO.`name` AS `role_name`
	FROM
		`casting` CA
		INNER JOIN `production` PR ON CA.`production_id` = PR.`id`
		INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
		LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`
		INNER JOIN `serie` SER ON PR.`id` = SER.`id`
        INNER JOIN `season` SEA ON SER.`id` = SEA.`serie_id`
        INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`
		LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`
		INNER JOIN `role` RO ON CA.`role_id` = RO.`id`
	WHERE
		CA.`person_id` = 1831321
	GROUP BY PR.`id`, RO.`name`, CH.`name`
)
UNION DISTINCT
(
	SELECT SER.`id` AS `prod_id`, TI.`title` AS `prod_title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `prod_yearstart`, SER.`yearend` AS `prod_yearend`, GE.`name` AS `prod_gender`, CH.`name` AS `char_name`, RO.`name` AS `role_name`
	FROM
		`casting` CA
		INNER JOIN `production` PR_EP ON CA.`production_id` = PR_EP.`id`
		INNER JOIN `episode` EP ON PR_EP.`id` = EP.`id`
		INNER JOIN `season` SEA ON EP.`season_id` = SEA.`id`
		INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`
		INNER JOIN `production` PR_SER ON SER.`id` = PR_SER.`id`
		INNER JOIN `title` TI ON PR_SER.`title_id` = TI.`id`
		LEFT JOIN `gender` GE ON PR_SER.`gender_id` = GE.`id`
		LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`
		INNER JOIN `role` RO ON CA.`role_id` = RO.`id`
	WHERE
		CA.`person_id` = 1831321
	GROUP BY PR_SER.`id`, RO.`name`, CH.`name`
)
ORDER BY `prod_yearstart` DESC, `prod_title` ASC;

-- gather the statistics about persons
SELECT COUNT(DISTINCT `id`) AS `count_person`
FROM `person`;
