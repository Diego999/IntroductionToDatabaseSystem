#a Compute the number of movies per year. Make sure to include tv and video movies.
# ~2.0 sec on Macbook Pro mid-2010
# ~1.5 sec on Macbook Pro late-2013
SELECT P.`year`, COUNT(P.`id`)
FROM `production` P
INNER JOIN `singleproduction` S ON P.`id` = S.`id` 
WHERE S.`kind_id` IN (
	SELECT K.`id`
    FROM `kind` K
    WHERE K.`name` IN ("tv movie","video movie","movie")
)
GROUP BY P.`year`;

#b Compute the ten countries with most production companies
# ~11 sec on Macbook Pro mid-2010
# ~5 sec on Macbook Pro late-2013
SELECT COU.`id`, COU.`code`, SUB.`number`
FROM (
	SELECT COM.`country_id`, COUNT(DISTINCT COM.`id`) AS `number`
	FROM `company` COM
	INNER JOIN (
		SELECT PC.`company_id`
		FROM `productioncompany` PC
		INNER JOIN `type` TY ON PC.`type_id` = TY.`id`
		WHERE TY.`name`= "production companies"		
	) PC ON PC.`company_id` = COM.`id`
	GROUP BY COM.`country_id`
	HAVING COM.`country_id` IS NOT NULL
	ORDER BY `number` DESC
	LIMIT 10
) SUB
INNER JOIN `country` COU ON SUB.`country_id` = COU.`id`;

#c Compute the min, max and average career duration. (A career length is implied by the first and last production of a person)
# ~835 on Macbook Pro mid-2010
# ~447 on Macbook Pro late-2013
SELECT MIN(T.`careerDuration`) AS `min`, MAX(T.`careerDuration`) AS `max`, AVG(T.`careerDuration`) AS `avg`
FROM (
    SELECT (MAX(P.`year`) - MIN(P.`year`)) AS `careerDuration`
	FROM (
		SELECT DISTINCT C.`person_id`, C.`production_id`
        FROM `casting` C
	) C
    INNER JOIN (
		SELECT P.`id`, P.`year`
        FROM `production` P
        WHERE P.`year` IS NOT NULL
	) P ON C.`production_id` = P.`id`
    GROUP BY C.`person_id`
) T;
    
#d Compute the min, max and average number of actors in a production
# ~175 sec on Macbook Pro 2010
# ~95 sec on Macbook Pro late-2013
SELECT MIN(T.`number`) AS `min`, MAX(T.`number`) AS `max`, AVG(T.`number`) AS `avg`
FROM (
	SELECT COUNT(C.`id`) AS `number`
	FROM `casting` C
    INNER JOIN `role` R ON C.`role_id` = R.`id`
    WHERE R.`name` = "actor"
	GROUP BY C.`production_id`
) T;

#e Compute the min, max and average height of female persons.    
# ~1.0 on Macbook Pro mid-2010
# ~2.5 on Macbook Pro late-2013
SELECT MIN(P.`height`) AS `min`, MAX(P.`height`) AS `max`, AVG(P.`height`) AS `avg`
FROM `person` P
WHERE P.`height` IS NOT NULL
	AND P.`gender` = "f";

#f List all pairs of persons and movies where the person has both directed the movie and acted in the movie.
#Do not include tv and video movies.
# ~215 sec on Macbook Pro mid-2010
# ~324 sec on Macbook Pro late-2013
SELECT DISTINCT C1.`person_id`, C1.`production_id`
FROM (
	SELECT C.`person_id`, C.`production_id`
	FROM `casting` C
    INNER JOIN `role` R ON C.`role_id` = R.`id` 
    WHERE R.`name` = "actor"
	AND C.`production_id` NOT IN (
		SELECT S.`id`
        FROM `singleproduction` S
        INNER JOIN `kind` K ON S.`kind_id` = K.`id`
        WHERE K.`name` = "movie"
	)
) C1, (
	SELECT C.`person_id`, C.`production_id`
	FROM `casting` C
    INNER JOIN `role` R ON C.`role_id` = R.`id` 
    WHERE R.`name` = "producer"
	AND C.`production_id` NOT IN (
		SELECT S.`id`
        FROM `singleproduction` S
        INNER JOIN `kind` K ON S.`kind_id` = K.`id`
        WHERE K.`name` = "movie"
	)
) C2
WHERE C1.`person_id` = C2.`person_id`;

#g List the three most popular character names
# 11.3 sec on Macbook Pro mid-2010
# 18 sec on Macbook Pro late-2013
SELECT CH.`name`
FROM (
	SELECT CA.`character_id`, COUNT(CA.`id`) AS `number`
	FROM `casting` CA
    WHERE CA.`character_id` IS NOT NULL
	GROUP BY CA.`character_id`
    ORDER BY `number` DESC
    LIMIT 0,3
    ) T
INNER JOIN `character` CH ON T.`character_id` = CH.`id`;
