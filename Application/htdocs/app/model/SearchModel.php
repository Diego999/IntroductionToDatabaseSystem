<?php

class SearchModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getCharactersLikeName($name)
    {
        try
        {
            $sql = "SELECT DISTINCT CH.`id`, CH.`name`, COUNT(DISTINCT CA.`person_id`) AS `persons_count`, COUNT(DISTINCT CA.`production_id`) AS `productions_count`"
                . " FROM `character` CH INNER JOIN `casting` CA ON CH.`id`=CA.`character_id`"
                . " WHERE CH.`name` COLLATE UTF8_GENERAL_CI LIKE \"%" . $name . "%\""
                . " GROUP BY CH.`id`"
                . " ORDER BY CH.`id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getCharactersLikeName : request returned status " . $query->getStatus(),
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

    public function getCharacterName($characterId)
    {
        try
        {
            $sql = "SELECT DISTINCT CH.`name`"
                . " FROM `character` CH"
                . " WHERE CH.`id`=" . $characterId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0]['name'];
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getCharacterName : request returned status " . $query->getStatus(),
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

    public function getActorsPlayingCharacter($characterId)
    {
        try
        {
            $sql = "SELECT DISTINCT PE.`id`, NA.`firstname`, NA.`lastname`"
                . " FROM `person` PE"
                . " INNER JOIN `name` NA ON PE.`name_id` = NA.`id`"
                . " INNER JOIN `casting` CA ON PE.`id` = CA.`person_id`"
                . " WHERE CA.`character_id`=" . $characterId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getActorsPlayingCharacter : request returned status " . $query->getStatus(),
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

    public function getMoviesContainingCharacter($characterId)
    {
        try
        {
            $sql = "SELECT DISTINCT PR.`id`, PR.`year`, TI.`title`"
                . " FROM `production` PR"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " INNER JOIN `casting` CA ON PR.`id` = CA.`production_id`"
                . " WHERE CA.`character_id`=" . $characterId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getMoviesContainingCharacter : request returned status " . $query->getStatus(),
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

    public function getCompaniesLikeName($name)
    {
        try
        {
            $sql = "SELECT DISTINCT COM.`id` AS `id`, COM.`name` AS `name`, COU.`code` AS `country`, COUNT(DISTINCT PC_PROD.`production_id`) AS `produced_count`, COUNT(DISTINCT PC_DIST.`production_id`) AS `distributed_count`"
                . " FROM `company` COM"
                . " LEFT JOIN `country` COU ON COM.`country_id` = COU.`id`"
                . " INNER JOIN `productioncompany` PC_PROD ON COM.`id` = PC_PROD.`company_id` AND PC_PROD.`type_id` = (SELECT `id` FROM `type` WHERE `name`=\"production companies\")"
                . " INNER JOIN `productioncompany` PC_DIST ON COM.`id` = PC_DIST.`company_id` AND PC_DIST.`type_id` = (SELECT `id` FROM `type` WHERE `name`=\"distributors\")"
                . " WHERE COM.`name` COLLATE UTF8_GENERAL_CI LIKE \"%" . $name . "%\""
                . " GROUP BY COM.`id`"
                . " ORDER BY COM.`name`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getCompaniesLikeName : request returned status " . $query->getStatus(),
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

    public function getCompanyName($companyId)
    {
        try
        {
            $sql = "SELECT DISTINCT COM.`name` AS `name`, COU.`code` AS `country`"
                . " FROM `company` COM"
                . " LEFT JOIN `country` COU ON COM.`country_id` = COU.`id`"
                . " WHERE COM.`id`=" . $companyId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getCompanyName : request returned status " . $query->getStatus(),
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

    public function getMoviesProducedByCompany($companyId)
    {
        try
        {
            $sql = "SELECT DISTINCT PR.`id` AS `id`, TI.`title` AS `title`, PR.`year` AS `year`"
                . " FROM `production` PR"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " INNER JOIN `productioncompany` PC ON PR.`id` = PC.`production_id`"
                . " INNER JOIN `type` TY ON PC.`type_id` = TY.`id`"
                . " WHERE TY.`name`=\"production companies\""
                . " AND PC.`company_id`=" . $companyId
                . " ORDER BY PR.`year` DESC, TI.`title` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getMoviesProducedByCompany : request returned status " . $query->getStatus(),
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

    public function getMoviesDistributedByCompany($companyId)
    {
        try
        {
            $sql = "SELECT DISTINCT PR.`id` AS `id`, TI.`title` AS `title`, PR.`year` AS `year`"
                . " FROM `production` PR"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " INNER JOIN `productioncompany` PC ON PR.`id` = PC.`production_id`"
                . " INNER JOIN `type` TY ON PC.`type_id` = TY.`id`"
                . " WHERE TY.`name`=\"distributors\""
                . " AND PC.`company_id`=" . $companyId
                . " ORDER BY PR.`year` DESC, TI.`title` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getMoviesDistributedByCompany : request returned status " . $query->getStatus(),
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

    public function getGendersLikeName($name)
    {
        try
        {
            $sql = "SELECT DISTINCT GE.`id` AS `id`, GE.`name` AS `name`, COUNT(DISTINCT PR.`id`) AS `count_prod`"
                . " FROM `gender` GE"
                . " INNER JOIN `production` PR ON GE.`id` = PR.`gender_id`"
                . " WHERE GE.`name` COLLATE UTF8_GENERAL_CI LIKE \"%" . $name . "%\""
                . " GROUP BY GE.`id`"
                . " ORDER BY GE.`name`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getGendersLikeName : request returned status " . $query->getStatus(),
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

    public function getGenderName($genderId)
    {
        try
        {
            $sql = "SELECT DISTINCT GE.`name`"
                . " FROM `gender` GE"
                . " WHERE GE.`id`=" . $genderId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getGenderName : request returned status " . $query->getStatus(),
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

    public function getMoviesHavingGender($genderId)
    {
        try
        {
            $sql = "SELECT DISTINCT PR.`id` AS `id`, TI.`title` AS `title`, PR.`year` AS `year`"
                . " FROM `production` PR"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " INNER JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " WHERE GE.`id`=" . $genderId
                . " ORDER BY PR.`year` DESC, TI.`title` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getMoviesHavingGender : request returned status " . $query->getStatus(),
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

    public function getPersonsLikeName($name)
    {
        // Break up name
        $nameArray = explode(" ", trim($name));

        try
        {
            $sql = "SELECT DISTINCT PE.`id`, NA_MAIN.`lastname`, NA_MAIN.`firstname`, PE.`birthdate`, PE.`deathdate`"
                . " FROM `person` PE"
                . " INNER JOIN `name` NA_SEARCH ON PE.`id` = NA_SEARCH.`person_id`"
                . " INNER JOIN `name` NA_MAIN ON PE.`name_id` = NA_MAIN.`id`"
                . " WHERE (";
            $first = true;
            foreach ($nameArray as $nameElem)
            {
                $sql .= ($first ? "" : " OR") . " NA_SEARCH.`lastname` COLLATE UTF8_GENERAL_CI LIKE \"%" . $nameElem . "%\"";
                $first = false;
            }
            $sql .= ") AND (";
            $first = true;
            foreach ($nameArray as $nameElem)
            {
                $sql .= ($first ? "" : " OR") . " NA_SEARCH.`firstname` COLLATE UTF8_GENERAL_CI LIKE \"%" . $nameElem . "%\"";
                $first = false;
            }
            $sql .= ") GROUP BY PE.`id`"
                . " ORDER BY NA_MAIN.`lastname` ASC,  NA_MAIN.`firstname` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getPersonsLikeName : request returned status " . $query->getStatus(),
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

    public function getProductionsLikeName($name)
    {
        try
        {
            $sql = "SELECT DISTINCT PR.`id`, TI_MAIN.`title`, PR.`year`, GE.`name` AS `gender`"
                . " FROM `production` PR"
                . " INNER JOIN ("
                . " SELECT TI.`id`, TI.`production_id` AS `prod_id`"
                . " FROM `title` TI"
                . " WHERE TI.`title` COLLATE UTF8_GENERAL_CI LIKE \"%" . $name . "%\""
                . " ) TI_SEARCH ON PR.`id` = TI_SEARCH.`prod_id`"
                . " INNER JOIN `title` TI_MAIN ON PR.`title_id` = TI_MAIN.`id`"
                . " LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " GROUP BY PR.`id`"
                . " ORDER BY PR.`year` DESC, TI_MAIN.`title` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getProductionsLikeName : request returned status " . $query->getStatus(),
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