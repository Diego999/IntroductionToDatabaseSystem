#a Compute the number of movies per year. Make sure to include tv and video movies.
# ~3.5 sec on Macbook Pro mid-2010
# ~2.0 sec on Macbook Pro late-2013
SELECT P.`year`, COUNT(P.`id`)
FROM `production` P
INNER JOIN `singleproduction` S ON P.`id` = S.`id` 
WHERE S.`kind_id` IN (
	SELECT K.`id`
    FROM `kind` K
    WHERE K.`name` IN ("tv movie","video movie","movie")
)
AND P.`year` IS NOT NULL
GROUP BY P.`year`;

#b Compute the ten countries with most production companies
# ~10 sec on Macbook Pro mid-2010
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
# Simple query
# ~750 on Macbook Pro mid-2010
# ~360 on Macbook Pro late-2013

# With materialized view
# 2.2 Macbook Pro
DROP TABLE IF EXISTS m2c_careerduration;
CREATE TABLE m2c_careerduration (
	person_id	 INT UNSIGNED,
    careerDuration INT UNSIGNED,
    PRIMARY KEY (`person_id`)
);

DROP PROCEDURE IF EXISTS refresh_m2c_careerduration;
DELIMITER $$
CREATE PROCEDURE refresh_m2c_careerduration ()
BEGIN

	TRUNCATE TABLE m2c_careerduration;

	INSERT INTO m2c_careerduration (`person_id`, `careerDuration`)
	SELECT C.`person_id`, (MAX(P.`year`) - MIN(P.`year`)) AS `careerDuration`
	FROM (
		SELECT DISTINCT C.`person_id`, C.`production_id`
		FROM `casting` C
	) C
	INNER JOIN (
		SELECT P.`id`, P.`year`
		FROM `production` P
		WHERE P.`year` IS NOT NULL
	) P ON C.`production_id` = P.`id`
	GROUP BY C.`person_id`;
END;
$$
DELIMITER ;
# 1150 sec on Macbook Pro 2013
CALL refresh_m2c_careerduration;

SELECT MIN(T.`careerDuration`) AS `min`, MAX(T.`careerDuration`) AS `max`, AVG(T.`careerDuration`) AS `avg`
FROM m2c_careerduration T;
    
#d Compute the min, max and average number of actors in a production
#Simple query
# ~170 sec on Macbook Pro 2010
# ~88 sec on Macbook Pro late-2013

# With materialized view
# 1 sec on Macbook pro 2010
DROP TABLE IF EXISTS m2d_nbactorproduction;
CREATE TABLE m2d_nbactorproduction (
	production_id	 INT UNSIGNED,
    nb_actor 		 INT UNSIGNED,
    PRIMARY KEY (`production_id`)
);

DROP PROCEDURE IF EXISTS refresh_m2d_nbactorproduction;
DELIMITER $$
CREATE PROCEDURE refresh_m2d_nbactorproduction ()
BEGIN

	TRUNCATE TABLE m2d_nbactorproduction;

	INSERT INTO m2d_nbactorproduction (`production_id`, `nb_actor`)
	SELECT C.`production_id`, COUNT(C.`id`) AS `number`
	FROM `casting` C
    INNER JOIN `role` R ON C.`role_id` = R.`id`
    WHERE R.`name` = "actor"
	GROUP BY C.`production_id`;
END;
$$
DELIMITER ;
# 227 sec on Macbook Pro 2010
CALL refresh_m2d_nbactorproduction;

SELECT MIN(T.`nb_actor`) AS `min`, MAX(T.`nb_actor`) AS `max`, AVG(T.`nb_actor`) AS `avg`
FROM m2d_nbactorproduction T;

#e Compute the min, max and average height of female persons.    
# ~1.0 on Macbook Pro mid-2010
# ~0.7 on Macbook Pro late-2013
SELECT MIN(P.`height`) AS `min`, MAX(P.`height`) AS `max`, AVG(P.`height`) AS `avg`
FROM `person` P
WHERE P.`height` IS NOT NULL AND P.`gender` = "f";

#f List all pairs of persons and movies where the person has both directed the movie and acted in the movie.
#Do not include tv and video movies.
# ~1.5 sec on Macbook Pro mid-2010
# ~0.7 sec on Macbook Pro late-2013
SELECT DISTINCT C.person_id, C.production_id
FROM `casting` C
WHERE EXISTS
(
	SELECT CC.id
    FROM `casting` CC
    INNER JOIN `role` R
    ON CC.role_id = R.id
    WHERE CC.person_id = C.person_id
    AND CC.production_id = C.production_id
    AND R.name = "director"
    AND EXISTS
    (
    	SELECT CC.id
		FROM `casting` CC
		INNER JOIN `role` R
		ON CC.role_id = R.id
        INNER JOIN `singleproduction` S
		ON CC.production_id = S.id
		INNER JOIN `kind` K ON S.`kind_id` = K.`id`
		WHERE K.`name` = "movie"
		AND CC.person_id = C.person_id
		AND CC.production_id = C.production_id
		AND R.name = "actor"
    )
);

#g List the three most popular character names
# 10.0 sec on Macbook Pro mid-2010
# 5.7 sec on Macbook Pro late-2013
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
