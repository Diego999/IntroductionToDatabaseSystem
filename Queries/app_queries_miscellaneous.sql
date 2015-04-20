-- gather list of roles
SELECT RO.`id` AS `id`, RO.`name` AS `name`
FROM
	`role` RO
ORDER BY RO.`name`;

-- gather list of genders
SELECT GE.`id` AS `id`, GE.`name` AS `name`
FROM
	`gender` GE
ORDER BY GE.`name`;

-- gather list of types
SELECT TY.`id` AS `id`, TY.`name` AS `name`
FROM
	`type` TY
ORDER BY TY.`name`;

-- gather list of kinds
SELECT KI.`id` AS `id`, KI.`name` AS `name`
FROM
	`kind` KI
ORDER BY KI.`name`;