-- Search all characters with production and person counts, following the LIKE condition (here Katniss Everdeen)
SELECT DISTINCT CH.`id`, CH.`name`, COUNT(DISTINCT CA.`person_id`) AS `persons_count`, COUNT(DISTINCT CA.`production_id`) AS `productions_count`
FROM
	`character` CH
    INNER JOIN `casting` CA ON CH.`id`=CA.`character_id`
WHERE
	CH.`name` LIKE "%Everdeen%"
GROUP BY CH.`id`
ORDER BY CH.`id`;

-- gather all persons who played a given character (here 2604958 = Katniss Everdeen)
SELECT DISTINCT PE.`id`, NA.`firstname`, NA.`lastname`
FROM
	`person` PE
    INNER JOIN `name` NA ON PE.`name_id` = NA.`id`
    INNER JOIN `casting` CA ON PE.`id` = CA.`person_id`
WHERE
	CA.`character_id` = 2604958;
    
-- gather the name of character based on its ID (here 2604958 = Katniss Everdeen)
SELECT DISTINCT CH.`name`
FROM
	`character` CH
WHERE
	CH.`id` = 2604958;
    
-- gather all movies in which a given character appears (here 2604958 = Katniss Everdeen)
SELECT DISTINCT PR.`id`, TI.`title`, PR.`year`
FROM
	`production` PR
    INNER JOIN `title` TI ON PR.`title_id` = TI.`id`
    INNER JOIN `casting` CA ON PR.`id` = CA.`production_id`
WHERE
	CA.`character_id` = 2604958;