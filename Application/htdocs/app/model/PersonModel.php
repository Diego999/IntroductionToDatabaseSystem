<?php

class PersonModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getPersonInfosGeneral($personId)
    {
        try
        {
            $sql = "SELECT DISTINCT DISTINCT PE.`id`, PE.`gender`, PE.`trivia`, PE.`quotes`, PE.`birthdate`, PE.`deathdate`, PE.`birthname`, PE.`minibiography`, PE.`spouse`, PE.`height`, PE.`name_id`, NA.`lastname`, NA.`firstname`"
                . " FROM `person` PE"
                . " INNER JOIN `name` NA ON PE.`name_id` = NA.`id`"
                . " WHERE PE.`id`=" . $personId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in PersonModel::getPersonInfosGeneral : request returned status " . $query->getStatus(),
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

    public function getPersonInfosAlternativeNames($personId, $mainNameId)
    {
        try
        {
            $sql = "SELECT DISTINCT NA.`id`, NA.`lastname`, NA.`firstname`"
                . " FROM `name` NA"
                . " WHERE NA.`person_id`=" . $personId
                . " AND NA.`id` !=" . $mainNameId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in PersonModel::getPersonInfosAlternativeNames : request returned status " . $query->getStatus(),
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

    public function getPersonInfosRolesSingleProd($personId)
    {
        try
        {
            $sql = "SELECT DISTINCT SP.`id` AS `prod_id`, TI.`title` AS `prod_title`, KI.`name` AS `prod_kind`, GE.`name` AS `prod_gender`, PR.`year` AS `prod_year`, CH.`name` AS `char_name`, RO.`name` AS `role_name`"
                . " FROM `casting` CA"
                . " INNER JOIN `production` PR ON CA.`production_id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " INNER JOIN `singleproduction` SP ON PR.`id` = SP.`id`"
                . " INNER JOIN `kind` KI ON SP.`kind_id` = KI.`id`"
                . " LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`"
                . " INNER JOIN `role` RO ON CA.`role_id` = RO.`id`"
                . " WHERE CA.`person_id`=" . $personId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in PersonModel::getPersonInfosRolesSingleProd : request returned status " . $query->getStatus(),
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

    public function getPersonInfosRolesSeries($personId)
    {
        try
        {
            $sql = "("
                . " SELECT SER.`id` AS `prod_id`, TI.`title` AS `prod_title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `prod_yearstart`, SER.`yearend` AS `prod_yearend`, GE.`name` AS `prod_gender`, CH.`name` AS `char_name`, RO.`name` AS `role_name`"
                . " FROM `casting` CA"
                . " INNER JOIN `production` PR ON CA.`production_id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " INNER JOIN `serie` SER ON PR.`id` = SER.`id`"
                . " INNER JOIN `season` SEA ON SER.`id` = SEA.`serie_id`"
                . " INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`"
                . " LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`"
                . " INNER JOIN `role` RO ON CA.`role_id` = RO.`id`"
                . " WHERE CA.`person_id`=" . $personId
                . " GROUP BY PR.`id`"
                . " ) UNION DISTINCT ("
                . " SELECT SER.`id` AS `prod_id`, TI.`title` AS `prod_title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `prod_yearstart`, SER.`yearend` AS `prod_yearend`, GE.`name` AS `prod_gender`, CH.`name` AS `char_name`, RO.`name` AS `role_name`"
                . " FROM `casting` CA"
                . " INNER JOIN `production` PR_EP ON CA.`production_id` = PR_EP.`id`"
                . " INNER JOIN `episode` EP ON PR_EP.`id` = EP.`id`"
                . " INNER JOIN `season` SEA ON EP.`season_id` = SEA.`id`"
                . " INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`"
                . " INNER JOIN `production` PR_SER ON SER.`id` = PR_SER.`id`"
                . " INNER JOIN `title` TI ON PR_SER.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR_SER.`gender_id` = GE.`id`"
                . " LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`"
                . " INNER JOIN `role` RO ON CA.`role_id` = RO.`id`"
                . " WHERE CA.`person_id`=" . $personId
                . " GROUP BY PR_SER.`id`"
                . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in PersonModel::getPersonInfosRolesSeries : request returned status " . $query->getStatus(),
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