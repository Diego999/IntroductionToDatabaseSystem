<?php

class CompanyModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // used in company details page : infos on company
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

    // used in company details page : list of movies
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

    // used in company details page : list of series
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

    // used in company direct access page : get the statistics
    public function getStatistics()
    {
        try
        {
            $sql = "SELECT COUNT(DISTINCT `id`) AS `count_company`"
                . " FROM `company`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in CompanyModel::getStatistics : request returned status " . $query->getStatus(),
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

    // Field getter for AJAX form
    public function getFieldContent($id, $fieldName)
    {
        switch ($fieldName)
        {
            case 'name':
                $sql = "SELECT COM.`" . $fieldName . "`"
                    . " FROM `company` COM"
                    . " WHERE COM.`id`=" . $id;
                break;
            case 'country':
                $sql = "SELECT COU.`code` AS `country`"
                    . " FROM `country` COU"
                    . " RIGHT JOIN `company` COM ON COU.`id` = COM.`country_id`"
                    . " WHERE COM.`id`=" . $id;
                break;
            default:
                break;
        }

        try
        {
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return ($query->getData()[0][$fieldName] ? $query->getData()[0][$fieldName] : "");
            }
            else
            {
                throw new ILARIA_CoreError("Error in CompanyModel::getFieldContent : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            throw $e;
        }
    }

    // SCUD operation
    public function insert($name, $country)
    {
        // Result
        $result = 0;

        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        try
        {
            // ID of inserted company
            $companyId = 0;
            $countryId = 0;

            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Error in CompanyModel::insert : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // If any country is given in input
            if ($country != "")
            {
                // Get corresponding ID from country list
                $sql = "SELECT COU.`id`"
                    . " FROM `country` COU"
                    . " WHERE COU.`code` COLLATE UTF8_GENERAL_CI LIKE " . $this->quote($country)
                    . " LIMIT 1";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() == 0)
                {
                    if ($query->getCount() == 0)
                    {
                        // Insert country
                        $sql = "INSERT INTO `country`(`code`) VALUES ("
                            . $this->quote($country) . ")";
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->exec($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Error in CompanyModel::insert : unable to add new country",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }
                        $countryId = $this->getDatabase()->getLastInsertId();
                    }
                    else
                    {
                        $countryId = $query->getData()[0]['id'];
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("Error in CompanyModel::insert : unable to search through countries",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Insert new company
            {
                $sql = "INSERT INTO `company`(`name`,`country_id`) VALUES ("
                    . $this->quote($name) . ","
                    . ($countryId>0 ? $countryId : "NULL") . ")";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in CompanyModel::insert : unable to insert new company",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
                $companyId = $this->getDatabase()->getLastInsertId();
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in CompanyModel::insert : unable to commit the transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Gather back company ID
            $result = $companyId;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $this->getDatabase()->transactionRollback();
            $result = -1;
        }

        // Re-enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        // return success
        return $result;
    }

    // SCUD operation
    public function update($id, $name, $country)
    {
        // Result
        $result = 0;

        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        try
        {
            // Current country information
            $countryCode = "";
            $countryId = 0;

            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Error in CompanyModel::update : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Select current country code for current company
            {
                $sql = "SELECT COU.`code`, COU.`id`"
                    . " FROM `country` COU"
                    . " RIGHT JOIN `company` COM ON COU.`id` = COM.`country_id`"
                    . " WHERE COM.`id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() == 0 && $query->getCount() == 1)
                {
                    $infos = $query->getData()[0];
                    $countryCode = ($infos['code'] ? $infos['code'] : "");
                    $countryId = ($infos['id'] ? $infos['id'] : "NULL");
                }
                else
                {
                    throw new ILARIA_CoreError("Error in CompanyModel::update : unable to find current country for comparison",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // If the input country code differs from the current one
            if ($country != $countryCode)
            {
                // If the input country is non-empty
                if ($country != "")
                {
                    // Get corresponding ID from country list
                    $sql = "SELECT COU.`id`"
                        . " FROM `country` COU"
                        . " WHERE COU.`code` COLLATE UTF8_GENERAL_CI LIKE " . $this->quote($country)
                        . " LIMIT 1";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->query($query);
                    if ($query->getStatus() == 0)
                    {
                        if ($query->getCount() == 0)
                        {
                            // Insert country
                            $sql = "INSERT INTO `country`(`code`) VALUES ("
                                . $this->quote($country) . ")";
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->exec($query);
                            if ($query->getStatus() != 0)
                            {
                                throw new ILARIA_CoreError("Error in CompanyModel::update : unable to add new country",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }
                            $countryId = $this->getDatabase()->getLastInsertId();
                        }
                        else
                        {
                            $countryId = $query->getData()[0]['id'];
                        }
                    }
                    else
                    {
                        throw new ILARIA_CoreError("Error in CompanyModel::update : unable to search through countries",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // If the input country is empty
                else
                {
                    $countryId = "NULL";
                }
            }

            // Update company
            {
                $sql = "UPDATE `company` SET"
                    . " `name`=" . $this->quote($name) . ","
                    . " `country_id`=" . $countryId
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in CompanyModel::update : unable to insert new company",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in CompanyModel::update : unable to commit the transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $this->getDatabase()->transactionRollback();
            $result = -1;
        }

        // Re-enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        // return success
        return $result;
    }

    // SCUD operation
    public function delete($id)
    {
        // Result
        $result = 0;

        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        try
        {
            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Error in CompanyModel::delete : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Delete the company
            {
                $sql = "DELETE FROM `company`"
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in CompanyModel::delete : unable to delete company",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in CompanyModel::delete : unable to commit the transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $this->getDatabase()->transactionRollback();
            $result = -1;
        }

        // Re-enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        // return success
        return $result;
    }

}