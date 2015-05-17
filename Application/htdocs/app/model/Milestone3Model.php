<?php

class Milestone3Model extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    const GENERAL_LIMIT = 1000;

    public function queryA()
    {
        try
        {
            $sql = "SELECT DISTINCT C.`person_id`, C.`production_id`"
                . " FROM `casting` C"
                . " INNER JOIN ("
                . " SELECT P.`id`, P.`birthdate`"
                . " FROM `person` P"
                . " WHERE P.`birthdate` IS NOT NULL"
                . " ) P ON C.`person_id` = P.`id`"
                . " INNER JOIN `role` R ON C.`role_id` = R.`id`"
                . " WHERE R.`name` IN (\"actor\", \"actress\")"
                . " AND EXISTS ("
		        . " SELECT MIN(PP.`birthdate`) as `min_birthdate`"
		        . " FROM `casting` CC"
		        . " INNER JOIN ("
                . " SELECT PP.`id`, PP.`birthdate`"
                . " FROM `person` PP"
                . " WHERE PP.`birthdate` IS NOT NULL"
                . " ) PP ON CC.`person_id` = PP.`id`"
		        . " INNER JOIN `role` R ON CC.`role_id` = R.`id`"
		        . " WHERE CC.`production_id` = C.`production_id`"
                . " AND R.`name` IN (\"actor\", \"actress\")"
                . " HAVING TIMESTAMPDIFF(YEAR, `min_birthdate`, P.`birthdate`) >= 55"
                . " )"
                . (self::GENERAL_LIMIT > 0 ? " LIMIT 0," . self::GENERAL_LIMIT : "");
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryA : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryB($actorId)
    {
        try
        {
            $sql = "SELECT P.year, COUNT(*) AS number"
                . " FROM ("
	            . " SELECT C.`production_id`"
	            . " FROM `casting` C"
	            . " WHERE C.`person_id` = " . $actorId
                . " ) T"
                . " INNER JOIN `production` P ON T.`production_id` = P.`id`"
                . " WHERE P.`year` IS NOT NULL"
                . " GROUP BY P.`year`"
                . " ORDER BY number DESC"
                . " LIMIT 0,1";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryB : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryC($year)
    {
        try
        {
            $sql = "SELECT T.`gender_id`, T.`company_id`, T.`number`, MAX(T.number) as number"
                . " FROM ("
	            . " SELECT PP.`gender_id`, P.`company_id`, COUNT(P.`company_id`) AS `number`"
	            . " FROM `productioncompany` P"
	            . " INNER JOIN `production`PP ON PP.`id` = P.`production_id`"
	            . " INNER JOIN `type` T ON P.`type_id` = T.`id`"
	            . " WHERE T.`name` = \"production companies\""
	            . " AND PP.`year` = " . $year
	            . " AND PP.`gender_id` IS NOT NULL"
	            . " GROUP BY PP.`gender_id`, P.`company_id`"
	            . " ORDER BY PP.`gender_id` ASC, `number` DESC, P.`company_id`"
                . " ) T"
                . " GROUP BY T.`gender_id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryC : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryD()
    {
        try
        {
            $sql = "SELECT DISTINCT C.person_id, C.production_id"
                . " FROM `casting` C"
                . " INNER JOIN `person` P ON C.person_id = P.id"
                . " INNER JOIN `name` N ON P.name_id = N.id"
                . " WHERE EXISTS ("
	            . " SELECT CC.id"
                . " FROM `casting` CC"
	            . " INNER JOIN `person` PP ON CC.person_id = PP.id"
	            . " INNER JOIN `name` NN ON PP.name_id = NN.id"
                . " WHERE CC.production_id = C.production_id"
                . " AND CC.person_id <> C.person_id"
                . " AND N.lastname = NN.lastname"
                . " ) "
                . (self::GENERAL_LIMIT > 0 ? "LIMIT 0," . self::GENERAL_LIMIT : "");
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryD : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryE()
    {
        try
        {
            $sql = "SELECT P.`year`, AVG(T.`number`) AS `number`"
                . " FROM ("
	            . " SELECT C.`production_id`, COUNT(DISTINCT C.`person_id`) as number"
	            . " FROM `casting` C"
                . " INNER JOIN `role` R ON C.`role_id` = R.`id`"
                . " WHERE R.`name` = \"actor\""
	            . " GROUP BY C.`production_id`"
                . " ) T"
                . " INNER JOIN `production` P ON P.`id` = T.`production_id`"
                . " WHERE P.`year` IS NOT NULL"
                . " GROUP BY P.`year`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryE : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryF()
    {
        try
        {
            $sql = "SELECT AVG(T.`number`) AS `number`"
                . " FROM ("
	            . " SELECT E.`season_id`, COUNT(E.`id`) AS `number`"
	            . " FROM `episode` E"
                . " GROUP BY E.`season_id`"
                . " ) T";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryF : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryG()
    {
        try
        {
            $sql = "SELECT AVG(T.`number`) AS `number`"
                . " FROM ("
	            . " SELECT S.`serie_id`, COUNT(S.`id`) AS `number`"
	            . " FROM `season` S"
	            . " GROUP BY S.`serie_id`"
                . " ) T";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryG : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryH()
    {
        try
        {
            $sql = "SELECT S.`id`, T.`number`"
                . " FROM ("
	            . " SELECT S.`serie_id`, COUNT(S.`id`) AS `number`"
	            . " FROM `season` S"
	            . " GROUP BY S.`serie_id`"
	            . " ORDER BY `number` DESC"
	            . " LIMIT 0,10"
                . " ) T"
                . " INNER JOIN `serie` S ON S.`id` = T.`serie_id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryH : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryI()
    {
        try
        {
            $sql = "SELECT S.`serie_id`, AVG(T.`number`) AS `number`"
                . " FROM ("
	            . " SELECT E.`season_id`, COUNT(E.`id`) AS `number`"
	            . " FROM `episode` E"
	            . " GROUP BY E.`season_id`"
                . " ) T"
                . " INNER JOIN `season` S ON T.`season_id` = S.`id`"
                . " GROUP BY S.`serie_id`"
                . " ORDER BY `number` DESC"
                . " LIMIT 0,10";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryI : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryJ()
    {
        try
        {
            $sql = "SELECT DISTINCT Per.`id`, C.production_id, P.year, Per.deathdate"
                . " FROM `casting` C"
                . " INNER JOIN `role` R ON C.`role_id` = R.`id`"
                . " INNER JOIN `production` P ON C.`production_id` = P.`id`"
                . " INNER JOIN `singleproduction` S ON P.`id` = S.`id`"
                . " INNER JOIN `kind` K ON S.`kind_id` = K.`id`"
                . " INNER JOIN `person` Per ON C.`person_id` = Per.`id`"
                . " WHERE R.`name` IN (\"actor\", \"actress\", \"director\")"
                . " AND K.`name` IN (\"movie\", \"tv movie\", \"video movie\")"
                . " AND Per.`deathdate` IS NOT NULL"
                . " AND P.`year` > EXTRACT(YEAR FROM Per.`deathdate`)"
                . (self::GENERAL_LIMIT > 0 ? " LIMIT 0," . self::GENERAL_LIMIT : "");
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryJ : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function refreshK()
    {
        try
        {
            $sql = "CALL refresh_m3k_moviesbycompanyperyear";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() == 0)
            {
                return 0;
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::refreshK : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryK()
    {
        try
        {
            $sql = "SELECT T.`year`, T.`company_id`, T.`nb_movie`"
                . " FROM ("
	            . " SELECT T.`year`, T.`company_id`, T.`nb_movie`,"
	            . " @year_rank := IF(@current_year = T.`year`, @year_rank + 1, 1) AS `year_rank`,"
	            . " @current_year := T.`year`"
	            . " FROM m3k_moviesbycompanyperyear T"
                . " ) T"
                . " WHERE year_rank <= 3"
                . (self::GENERAL_LIMIT > 0 ? " LIMIT 0," . self::GENERAL_LIMIT : "");
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryK : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryL()
    {
        try
        {
            $sql = "SELECT P.`id` FROM `person` P"
                . " WHERE P.`birthdate` IS NOT NULL"
                . " AND P.`deathdate` IS NULL"
                . " AND ("
	            . " P.`trivia` LIKE \"%opera singer%\""
                . " OR P.`minibiography` LIKE \"%opera singer%\""
                . " )"
                . " ORDER BY P.`birthdate` DESC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryL : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryM()
    {
        try
        {
            $sql = "SELECT DISTINCT C.person_id, C.production_id, N.`number`*T.`number` AS `number`"
                . " FROM `casting` C"
                . " INNER JOIN ("
	            . " SELECT N.person_id, COUNT(N.id) AS `number`"
	            . " FROM `name` N"
	            . " GROUP BY N.person_id"
                . " HAVING `number` > 1"
                . " ) N ON C.person_id = N.person_id"
                . " INNER JOIN ("
	            . " SELECT T.production_id, COUNT(T.id) AS `number`"
	            . " FROM `title` T"
	            . " GROUP BY T.production_id"
                . " HAVING `number` > 1"
                . " ) T ON C.production_id = T.production_id"
                . " ORDER BY number DESC"
                . " LIMIT 0,10";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryM : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function refreshN()
    {
        try
        {
            $sql = "CALL refresh_m3n_mostcharactercountry";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() == 0)
            {
                return 0;
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::refreshN : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function queryN()
    {
        try
        {
            $sql = "SELECT T.`country_id`, T.`character_id`"
                . " FROM m3n_mostcharactercountry T"
                . " GROUP BY T.`country_id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone3Model::queryN : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }
}