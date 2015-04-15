-- gather information about a company with particular ID (here 3293 = Lionsgate)
SELECT COM.`id` AS `id`, COM.`name` AS `name`, COU.`code` AS `country`
FROM
	`company` COM
    INNER JOIN `country` COU ON COM.`country_id` = COU.`id`
WHERE
	COM.`id`=3293;
    
-- gather list of single productions in which company is involved (here 3293 = Lionsgate)
SELECT SP.`id` AS `id`, TI.`title` AS `title`, KI.`name` AS `kind`, PR.`year` AS `year`, GE.`name` AS `gender`, TY.`name` AS `type`
FROM
	`productioncompany` PC
    INNER JOIN `production` PR ON PC.`production_id` = PR.`id`
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`
    INNER JOIN `singleproduction` SP ON PR.`id` = SP.`id`
    INNER JOIN `kind` KI ON SP.`kind_id` = KI.`id`
    INNER JOIN `type` TY ON PC.`type_id` = TY.`id`
WHERE
	PC.`company_id`=3293
ORDER BY TI.`title`, TY.`id`;
    
-- gather list of series in which company is involved (here 3293 = Lionsgate)
(
	SELECT SER.`id` AS `id`, TI.`title` AS `title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `yearstart`, SER.`yearend` AS `yearend`, GE.`name` AS `gender`, TY.`name` AS `type`
    FROM
		`productioncompany` PC
        INNER JOIN `production` PR ON PC.`production_id` = PR.`id`
        INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
        LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`
        INNER JOIN `serie` SER ON PR.`id` = SER.`id`
        INNER JOIN `season` SEA ON SER.`id` = SEA.`serie_id`
        INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`
        INNER JOIN `type` TY ON PC.`type_id` = TY.`id`
	WHERE
		PC.`company_id`=3293
	GROUP BY PR.`id`, TY.`id`
)
UNION DISTINCT
(
	SELECT SER.`id` AS `id`, TI.`title` AS `title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `yearstart`, SER.`yearend` AS `yearend`, GE.`name` AS `gender`, TY.`name` AS `type`
    FROM
		`productioncompany` PC
        INNER JOIN `production` PR_EP ON PC.`production_id` = PR_EP.`id`
        INNER JOIN `episode` EP ON PR_EP.`id` = EP.`id`
        INNER JOIN `season` SEA ON EP.`season_id` = SEA.`id`
        INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`
        INNER JOIN `production` PR_SER ON SER.`id` = PR_SER.`id`
        INNER JOIN `title` TI ON PR_SER.`title_id` = TI.`id`
        LEFT JOIN `gender` GE ON PR_SER.`gender_id` = GE.`id`
        INNER JOIN `type` TY ON PC.`type_id` = TY.`id`
	WHERE
		PC.`company_id`=3293
	GROUP BY PR_SER.`id`, TY.`id`
)
ORDER BY `title`, `type`;
