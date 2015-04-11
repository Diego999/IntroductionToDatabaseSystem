-- Search all characters with production and person counts, following the LIKE condition (here James Bond)
SELECT DISTINCT CH.`id`, CH.`name`, COUNT(DISTINCT CA.`person_id`) AS `persons_count`, COUNT(DISTINCT CA.`production_id`) AS `productions_count`
FROM
	`character` CH
    INNER JOIN `casting` CA ON CH.`id`=CA.`character_id`
WHERE
	CH.`name` LIKE "%James Bond%"
GROUP BY CH.`id`
ORDER BY CH.`id`;

