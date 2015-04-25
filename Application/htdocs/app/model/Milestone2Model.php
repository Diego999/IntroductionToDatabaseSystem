<?php

class Milestone2Model extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function queryA()
    {
        try
        {
            $sql = "SELECT P.`year` AS `year`, COUNT(P.`id`) AS `count`"
                . " FROM `production` P"
                . " INNER JOIN `singleproduction` S ON P.`id` = S.`id`"
                . " WHERE S.`kind_id` IN ("
                . " SELECT K.`id`"
                . " FROM `kind` K"
                . " WHERE K.`name` IN (\"tv movie\",\"video movie\",\"movie\")"
                . ") GROUP BY P.`year`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone2Model::queryA : request returned status " . $query->getStatus(),
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

    public function queryB()
    {
        try
        {
            $sql = "SELECT COU.`id`, COU.`code`, SUB.`number`"
                . " FROM ("
	            . " SELECT COM.`country_id`, COUNT(DISTINCT COM.`id`) AS `number`"
	            . " FROM `company` COM"
	            . " INNER JOIN ("
		        . " SELECT PC.`company_id`"
		        . " FROM `productioncompany` PC"
		        . " INNER JOIN `type` TY ON PC.`type_id` = TY.`id`"
		        . " WHERE TY.`name`= \"production companies\""
	            . " ) PC ON PC.`company_id` = COM.`id`"
	            . " GROUP BY COM.`country_id`"
	            . " HAVING COM.`country_id` IS NOT NULL"
	            . " ORDER BY `number` DESC"
	            . " LIMIT 10"
                . " ) SUB"
                . " INNER JOIN `country` COU ON SUB.`country_id` = COU.`id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone2Model::queryB : request returned status " . $query->getStatus(),
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

    public function queryC()
    {
        try
        {
            $sql = "SELECT MIN(T.`careerDuration`) AS `min`, MAX(T.`careerDuration`) AS `max`, AVG(T.`careerDuration`) AS `avg`"
                . " FROM ("
                . " SELECT (MAX(P.`year`) - MIN(P.`year`)) AS `careerDuration`"
	            . " FROM ("
		        . " SELECT DISTINCT C.`person_id`, C.`production_id`"
                . " FROM `casting` C"
	            . " ) C"
                . " INNER JOIN ("
		        . " SELECT P.`id`, P.`year`"
                . " FROM `production` P"
                . " WHERE P.`year` IS NOT NULL"
	            . " ) P ON C.`production_id` = P.`id`"
                . " GROUP BY C.`person_id`"
                . " ) T";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone2Model::queryC : request returned status " . $query->getStatus(),
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
            $sql = "SELECT MIN(T.`number`) AS `min`, MAX(T.`number`) AS `max`, AVG(T.`number`) AS `avg`"
                . " FROM ("
	            . " SELECT COUNT(C.`id`) AS `number`"
	            . " FROM `casting` C"
                . " INNER JOIN `role` R ON C.`role_id` = R.`id`"
                . " WHERE R.`name` = \"actor\""
	            . " GROUP BY C.`production_id`"
                . " ) T";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone2Model::queryD : request returned status " . $query->getStatus(),
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
            $sql = "SELECT MIN(P.`height`) AS `min`, MAX(P.`height`) AS `max`, AVG(P.`height`) AS `avg`"
                . " FROM `person` P"
                . " WHERE P.`height` IS NOT NULL"
	            . " AND P.`gender` = \"f\"";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone2Model::queryE : request returned status " . $query->getStatus(),
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
            $sql = "SELECT DISTINCT C1.`person_id`, C1.`production_id`"
                . " FROM ("
	            . " SELECT C.`person_id`, C.`production_id`"
	            . " FROM `casting` C"
                . " INNER JOIN `role` R ON C.`role_id` = R.`id`"
                . " WHERE R.`name` = \"actor\""
	            . " AND C.`production_id` NOT IN ("
		        . " SELECT S.`id`"
                . " FROM `singleproduction` S"
                . " INNER JOIN `kind` K ON S.`kind_id` = K.`id`"
                . " WHERE K.`name` = \"movie\""
	            . " )"
                . " ) C1, ("
	            . " SELECT C.`person_id`, C.`production_id`"
	            . " FROM `casting` C"
                . " INNER JOIN `role` R ON C.`role_id` = R.`id`"
                . " WHERE R.`name` = \"producer\""
	            . " AND C.`production_id` NOT IN ("
		        . " SELECT S.`id`"
                . " FROM `singleproduction` S"
                . " INNER JOIN `kind` K ON S.`kind_id` = K.`id`"
                . " WHERE K.`name` = \"movie\""
	            . " )"
                . " ) C2"
                . " WHERE C1.`person_id` = C2.`person_id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone2Model::queryF : request returned status " . $query->getStatus(),
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
            $sql = "SELECT CH.`name`"
                . " FROM ("
	            . " SELECT CA.`character_id`, COUNT(CA.`id`) AS `number`"
	            . " FROM `casting` CA"
                . " WHERE CA.`character_id` IS NOT NULL"
	            . " GROUP BY CA.`character_id`"
                . " ORDER BY `number` DESC"
                . " LIMIT 0,3"
                . " ) T"
                . " INNER JOIN `character` CH ON T.`character_id` = CH.`id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in Milestone2Model::queryG : request returned status " . $query->getStatus(),
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