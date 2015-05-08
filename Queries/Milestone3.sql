#a Find the actors and actresses (and report the productions) who played in a production
#where they were 55 or more year older than the youngest actor/actress playing
# ~76 sec on Macbook Pro mid-2010
# ~45 sec on Macbook Pro late-2013

SELECT DISTINCT C.`person_id`, C.`production_id`
FROM `casting` C
INNER JOIN (SELECT P.`id`, P.`birthdate` FROM `person` P WHERE P.`birthdate` IS NOT NULL) P
ON C.`person_id` = P.`id`
INNER JOIN `role` R
ON C.`role_id` = R.`id`
WHERE R.`name` IN ("actor", "actress")
AND EXISTS
(
		SELECT MIN(PP.`birthdate`) as `min_birthdate`
		FROM `casting` CC
		INNER JOIN (SELECT PP.`id`, PP.`birthdate` FROM `person` PP WHERE PP.`birthdate` IS NOT NULL) PP
		ON CC.`person_id` = PP.`id`
		INNER JOIN `role` R
		ON CC.`role_id` = R.`id`
		WHERE CC.`production_id` = C.`production_id`
        AND R.`name` IN ("actor", "actress")
        HAVING TIMESTAMPDIFF(YEAR, `min_birthdate`, P.`birthdate`) >= 55
);

#b Given an actor, compute his most productive year
# ~0.001 sec on Macbook Pro mid-2010
# ~0.001 sec on Macbook Pro late-2013
# Given actor 4
SELECT P.year, COUNT(*) AS number
FROM
(
	SELECT C.`production_id`
	FROM `casting` C 
	WHERE C.`person_id` = 4 # Given actor
) T
INNER JOIN `production` P
ON T.`production_id` = P.`id`
WHERE P.`year` IS NOT NULL
GROUP BY P.`year`
ORDER BY number DESC
LIMIT 0,1;

#c Given a year, list the company with the highest number of productions in each genre
# Only production company, a company per gender
# ~2.4 sec on Macbook Pro mid-2010
# ~2.1 sec on Macbook Pro late-2013
SELECT T.`gender_id`, T.`company_id`, T.`number`#, MAX(T.number) as number
FROM
(
SELECT PP.`gender_id`, P.`company_id`, COUNT(P.`company_id`) AS `number`
	FROM `productioncompany` P
	INNER JOIN `production`PP
	ON PP.`id` = P.`production_id`
	INNER JOIN `type` T
	ON P.`type_id` = T.`id`
	WHERE T.`name` = "production companies"
	AND PP.`year` = 2013 # Given year
	AND PP.`gender_id` IS NOT NULL
	GROUP BY PP.`gender_id`, P.`company_id`
	ORDER BY PP.`gender_id` ASC, `number` DESC, P.`company_id`
) T
GROUP BY T.`gender_id`; # We can use this trick which takes the first element (which was sorted desc), rather than using a TOP-N trick

#d Compute who worked with spouses/children/potential relatives on the same production.
#(You can assume that the same real surname (last name) implies a relation)
# ~20 sec on Macbook Pro mid-2010
# ~3.6 sec on Macbook Pro late-2013

SELECT DISTINCT C.person_id, C.production_id
FROM `casting` C
INNER JOIN `person` P
ON C.person_id = P.id
INNER JOIN `name` N
ON P.name_id = N.id
WHERE EXISTS
(
	SELECT CC.id
    FROM `casting` CC
	INNER JOIN `person` PP
	ON CC.person_id = PP.id
	INNER JOIN `name` NN
	ON PP.name_id = NN.id
    WHERE CC.production_id = C.production_id
    AND CC.person_id <> C.person_id
    AND N.lastname = NN.lastname
);

#e Compute the of average number of actors per production per year
# ~110 sec on Macbook Pro mid-2010
# ~52 sec on Macbook Pro late-2013

SELECT P.`year`, AVG(T.`number`) AS `number`
FROM
(
	SELECT C.`production_id`, COUNT(DISTINCT C.`person_id`) as number
	FROM `casting` C
    INNER JOIN `role` R
    ON C.`role_id` = R.`id`
    WHERE R.`name` = "actor"
	GROUP BY C.`production_id`
) T
INNER JOIN `production` P
ON P.`id` = T.`production_id`
WHERE P.`year` IS NOT NULL
GROUP BY P.`year`;
    
#f Compute the average number of episodes per season
# ~1 sec on Macbook Pro mid-2010
# ~0.5 sec on Macbook Pro late-2013

SELECT AVG(T.`number`) AS `number`
FROM
(
	SELECT E.`season_id`, COUNT(E.`id`) AS `number`
	FROM `episode` E
	GROUP BY E.`season_id`
) T;

#g Compute the average number of seasons per series
# ~0.1 sec on Macbook Pro mid-2010
# ~0.07 sec on Macbook Pro late-2013

SELECT AVG(T.`number`) AS `number`
FROM
(
	SELECT S.`serie_id`, COUNT(S.`id`) AS `number`
	FROM `season` S
	GROUP BY S.`serie_id`
) T;

#h Compute the top ten tv-series (by number of seasons)
# ~0.1 sec on Macbook Pro mid-2010
# ~0.038 sec on Macbook Pro late-2013

SELECT S.`id`, T.`number`
FROM
(
	SELECT S.`serie_id`, COUNT(S.`id`) AS `number`
	FROM `season` S
	GROUP BY S.`serie_id`
	ORDER BY `number` DESC
	LIMIT 0,10
) T
INNER JOIN `serie` S
ON S.`id` = T.`serie_id`;

#i Compute the top ten tv-series (by number of episodes per season)
# ~1 sec on Macbook Pro mid-2010
# ~0.6 sec on Macbook Pro late-2013

SELECT S.`serie_id`, AVG(T.`number`) AS `number`
FROM
(
	SELECT E.`season_id`, COUNT(E.`id`) AS `number`
	FROM `episode` E
	GROUP BY E.`season_id`
) T
INNER JOIN `season` S
ON T.`season_id` = S.`id`
GROUP BY S.`serie_id`
ORDER BY `number` DESC
LIMIT 0,10;

#j Find actors, actresses and directors who have movies (including tv movies and video movies) released after their death
# ~14.5 sec on Macbook Pro mid-2010
# ~6 sec on Macbook Pro late-2013

SELECT DISTINCT Per.`id`#, C.production_id, P.year, Per.deathdate
FROM `casting` C
INNER JOIN `role` R
ON C.`role_id` = R.`id`
INNER JOIN `production` P
ON C.`production_id` = P.`id`
INNER JOIN `singleproduction` S
ON P.`id` = S.`id`
INNER JOIN `kind` K
ON S.`kind_id` = K.`id`
INNER JOIN `person` Per
ON C.`person_id` = Per.`id`
WHERE R.`name` IN ("actor", "actress", "director")
AND K.`name` IN ("movie", "tv movie", "video movie")
AND Per.`deathdate` IS NOT NULL
AND P.`year` > EXTRACT(YEAR FROM Per.`deathdate`);

#k For each year, show three companies that released the most movies
# ~162 sec on Macbook Pro mid-2010
# ~75 sec on Macbook Pro late-2013

# TODO : IMPROVE MATERIALIZED VIEW

SELECT T.`year`, T.`company_id`, T.`number`
FROM
(
	SELECT T.`year`, T.`company_id`, T.`number`,
	@year_rank := IF(@current_year = T.`year`, @year_rank + 1, 1) AS `year_rank`,
	@current_year := T.`year`
	FROM
	(
		SELECT P.`year`, PC.`company_id`, COUNT(P.`id`) AS `number`
		FROM `casting` C
		INNER JOIN `production` P
		ON C.`production_id` = P.`id`
		INNER JOIN `productioncompany` PC
		ON P.`id` = PC.`production_id`
		INNER JOIN `type` T
		ON PC.`type_id` = T.`id`
		WHERE T.`name` = "production companies"
		AND P.`year` IS NOT NULL
		GROUP BY P.`year`, PC.`company_id`
		ORDER BY P.`year`, `number` DESC 
	) T
) T
WHERE year_rank <= 3;

#l List all living people who are opera singers ordered from youngest to oldest
# ~5 sec on Macbook Pro mid-2010
# ~4 sec on Macbook Pro late-2013

SELECT P.`id` FROM `person` P 
WHERE 
P.`birthdate` IS NOT NULL AND P.`deathdate` IS NULL AND
(P.`trivia` LIKE "%opera singer%" OR P.`minibiography` LIKE "%opera singer%") 
ORDER BY P.`birthdate` DESC;

#m List 10 most ambiguous credits (pairs of people and productions) ordered by the degree of ambiguity.
#A credit is ambiguous if either a person has multiple alternative names or a production has multiple alternative titles.
#The degree of ambiguity is a product of the number of possible names (real name + all alternatives) and the number of possible titles (real + alternatives)
# ~42 sec on Macbook Pro mid-2010
# ~25 sec on Macbook Pro late-2013

SELECT DISTINCT C.person_id, C.production_id, N.`number`*T.`number` AS `number`
FROM `casting` C
INNER JOIN
(
	SELECT N.person_id, COUNT(N.id) AS `number`
	FROM `name` N
	GROUP BY N.person_id
    HAVING `number` > 1
) N
ON C.person_id = N.person_id
INNER JOIN
(
	SELECT T.production_id, COUNT(T.id) AS `number`
	FROM `title` T
	GROUP BY T.production_id
    HAVING `number` > 1
) T
ON C.production_id = T.production_id
ORDER BY number DESC
LIMIT 0,10;

#n For each country, list the most frequent character name that appears in the productions of a production company (not a distributor) from that country
# ~X sec on Macbook Pro mid-2010
# ~X sec on Macbook Pro late-2013

# TODO : IMPROVE MATERIALIZED VIEW

SELECT Comp.`country_id`, COUNT(C.`character_id`) AS `number`
FROM `casting` C
INNER JOIN `productioncompany` PC
ON C.`production_id` = PC.`production_id`
INNER JOIN `type` T
ON PC.`type_id` = T.`id`
INNER JOIN `company` Comp
ON PC.`company_id` = Comp.`id`
WHERE T.`name` = "production companies"
AND Comp.`country_id` IS NOT NULL
AND C.`character_id` IS NOT NULL
GROUP BY Comp.`country_id`
ORDER BY `number`;
