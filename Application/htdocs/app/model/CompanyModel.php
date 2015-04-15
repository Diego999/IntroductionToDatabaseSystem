<?php

class CompanyModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getCompanyInfos($companyId)
    {
        try
        {
            $sql = "SELECT COM.`id` AS `id`, COM.`name` AS `name`, COU.`code` AS `country`"
                . " FROM `company` COM"
                . " INNER JOIN `country` COU ON COM.`country_id` = COU.`id`"
                . " WHERE COM.`id`=" . $companyId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in CompanyModel::getCompanyInfos : request returned status " . $query->getStatus(),
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

    public function getCompanyInfosWorkSingle($companyId)
    {
        try
        {
            $sql = "SELECT SP.`id` AS `id`, TI.`title` AS `title`, KI.`name` AS `kind`, PR.`year` AS `year`, GE.`name` AS `gender`, TY.`name` AS `type`"
                . " FROM `productioncompany` PC"
                . " INNER JOIN `production` PR ON PC.`production_id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " INNER JOIN `singleproduction` SP ON PR.`id` = SP.`id`"
                . " INNER JOIN `kind` KI ON SP.`kind_id` = KI.`id`"
                . " INNER JOIN `type` TY ON PC.`type_id` = TY.`id`"
                . " WHERE PC.`company_id`=" . $companyId
                . " ORDER BY TI.`title`, TY.`id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in CompanyModel::getCompanyInfosWorkSingle : request returned status " . $query->getStatus(),
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

    public function getCompanyInfosWorkSeries($companyId)
    {
        try
        {
            $sql = "("
                . " SELECT SER.`id` AS `id`, TI.`title` AS `title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `yearstart`, SER.`yearend` AS `yearend`, GE.`name` AS `gender`, TY.`name` AS `type`"
                . " FROM `productioncompany` PC"
                . " INNER JOIN `production` PR ON PC.`production_id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " INNER JOIN `serie` SER ON PR.`id` = SER.`id`"
                . " INNER JOIN `season` SEA ON SER.`id` = SEA.`serie_id`"
                . " INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`"
                . " INNER JOIN `type` TY ON PC.`type_id` = TY.`id`"
                . " WHERE PC.`company_id`=" . $companyId
                . " GROUP BY PR.`id`, TY.`id`"
                . " ) UNION DISTINCT ("
                . " SELECT SER.`id` AS `id`, TI.`title` AS `title`, COUNT(DISTINCT EP.`id`) AS `episode_count`, COUNT(DISTINCT SEA.`id`) AS `season_count`, SER.`yearstart` AS `yearstart`, SER.`yearend` AS `yearend`, GE.`name` AS `gender`, TY.`name` AS `type`"
                . " FROM `productioncompany` PC"
                . " INNER JOIN `production` PR_EP ON PC.`production_id` = PR_EP.`id`"
                . " INNER JOIN `episode` EP ON PR_EP.`id` = EP.`id`"
                . " INNER JOIN `season` SEA ON EP.`season_id` = SEA.`id`"
                . " INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`"
                . " INNER JOIN `production` PR_SER ON SER.`id` = PR_SER.`id`"
                . " INNER JOIN `title` TI ON PR_SER.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR_SER.`gender_id` = GE.`id`"
                . " INNER JOIN `type` TY ON PC.`type_id` = TY.`id`"
                . " WHERE PC.`company_id`=" . $companyId
                . " GROUP BY PR_SER.`id`, TY.`id`"
                . ") ORDER BY `title`, `type`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in CompanyModel::getCompanyInfosWorkSeries : request returned status " . $query->getStatus(),
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