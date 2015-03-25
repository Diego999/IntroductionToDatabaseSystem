#Compute the number of movies per year. Make sure to include tv and video movies.
SELECT P.year, COUNT(*)
FROM `production` P, `singleproduction` S
WHERE P.id = S.id AND S.kind_id IN (SELECT K.id FROM `kind` K WHERE K.name IN ("tv movie","video movie","movie"))
GROUP BY P.year;

#Compute the ten countries with most production companies
SELECT Coun.code, Temp.number
FROM (
	SELECT Comp.country_id, COUNT(*) as number
	FROM `company` Comp, `productioncompany` ProdComp
	WHERE ProdComp.company_id = Comp.id
			AND ProdComp.type_id = (SELECT T.id FROM `Type` T WHERE T.name = "production companies")
	GROUP BY Comp.country_id
	ORDER BY number DESC 
	LIMIT 0,10
    ) Temp, country Coun
WHERE Coun.id = Temp.country_id;

#Compute the min, max and average career duration. (A career length is implied by the first and last production of a person)
SELECT MIN(T.careerDuration) AS minDuration, MAX(T.careerDuration) AS maxDuration, AVG(T.careerDuration) AS avgDuration
FROM
	(
    SELECT C.person_id, (MAX(P.year) - MIN(P.year)) as careerDuration
	FROM `casting` C, `production` P
	WHERE C.production_id = P.id
	GROUP BY C.person_id
    ) T;
    
#Compute the min, max and average number of actors in a production
SELECT MIN(T.number), MAX(T.number), AVG(T.number)
FROM
	(
	SELECT C.production_id, COUNT(*) as number
	FROM `casting` C
	WHERE C.role_id = (SELECT R.id FROM `Role` R WHERE R.name = "actor")
	GROUP BY C.production_id
    ) T;

#Compute the min, max and average height of female persons.    
SELECT MIN(P.height) AS minHeight, MAX(P.height) AS maxHeight, AVG(P.height) AS avgHeight
FROM `person` P
where P.gender = "f"
GROUP BY P.height;

#List all pairs of persons and movies where the person has both directed the movie and acted in the movie.
#Do not include tv and video movies.
SELECT C1.person_id, C2.production_id
FROM `casting` C1, `casting` C2
WHERE 	C1.id <> C2.id
		AND C1.production_id NOT IN (
									SELECT S.id
									FROM `singleproduction` S
									WHERE S.kind_id IN (SELECT K.id FROM `kind` K WHERE K.name NOT IN ("tv movie","video movie"))
                                    )
		AND C1.production_id = C2.production_id 
		AND C1.person_id = C2.person_id
		AND C1.role_id = (SELECT R.id FROM `role` R WHERE R.name = "actor")
        AND C2.role_id = (SELECT R.id FROM `role` R WHERE R.name = "producer") ;

#List the three most popular character names
SELECT Ch.name, T.number
FROM (
	SELECT Ca.id, COUNT(*) as number
	FROM `casting` Ca
	GROUP BY Ca.id
    ) T, `character` Ch
WHERE T.id = Ch.id
ORDER BY T.number DESC
LIMIT 0,3;