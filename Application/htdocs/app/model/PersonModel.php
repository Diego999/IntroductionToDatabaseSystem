<?php

class PersonModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // used in person details page : infos on a person
    public function getPersonInfosGeneral($personId)
    {
        try
        {
            $sql = "SELECT DISTINCT PE.`id`, PE.`gender`, PE.`trivia`, PE.`quotes`, PE.`birthdate`, PE.`deathdate`, PE.`birthname`, PE.`minibiography`, PE.`spouse`, PE.`height`, PE.`name_id`, NA.`lastname`, NA.`firstname`"
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

    // used in person details page : list of alternative names
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

    // used in person details page : list of roles in movies
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
                . " WHERE CA.`person_id`=" . $personId
                . " ORDER BY PR.`year` DESC, TI.`title` ASC";
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

    // used in person details page : list of roles in series
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
                . " GROUP BY PR.`id`, RO.`name`, CH.`name`"
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
                . " GROUP BY PR_SER.`id`, RO.`name`, CH.`name`"
                . ")"
                . " ORDER BY `prod_yearstart` DESC, `prod_title` ASC";
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

    // used in person direct access page : get the statistics
    public function getStatistics()
    {
        try
        {
            $sql = "SELECT COUNT(DISTINCT `id`) AS `count_person`"
                . " FROM `person`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in PersonModel::getStatistics : request returned status " . $query->getStatus(),
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

    // Field getter for ajax form
    public function getFieldContent($id, $fieldName)
    {
        switch ($fieldName)
        {
            case 'gender':
            case 'trivia':
            case 'quotes':
            case 'birthdate':
            case 'deathdate':
            case 'birthname':
            case 'minibiography':
            case 'spouse':
            case 'height':
                $sql = "SELECT PE.`" . $fieldName . "`"
                    . " FROM `person` PE"
                    . " WHERE PE.`id`=" . $id;
                break;
            case 'firstname':
            case 'lastname':
                $sql = "SELECT NA.`" . $fieldName . "`"
                    . " FROM `name` NA"
                    . " INNER JOIN `person` PE ON NA.`id` = PE.`name_id`"
                    . " WHERE PE.`id`=" . $id;
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
                switch ($fieldName)
                {
                    case 'gender':
                        return ($query->getData()[0][$fieldName] ? $query->getData()[0][$fieldName] : "u");
                    default:
                        return ($query->getData()[0][$fieldName] ? $query->getData()[0][$fieldName] : "");
                }
            }
            else
            {
                throw new ILARIA_CoreError("Error in PersonModel::getFieldContent : request returned status " . $query->getStatus(),
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
    public function insert($firstname, $lastname, $gender, $trivia, $quotes, $birthdate, $deathdate, $birthname, $minibiography, $spouse, $height)
    {
        // Result
        $result = 0;

        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        try
        {
            // authorize insertion
            {
                $sql = "SET FOREIGN_KEY_CHECKS=0";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in PersonModel::insert : unable to disable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            try
            {
                // ID of inserted person
                $personId = 0;
                $nameId = 0;

                // Start the transaction
                if (!$this->getDatabase()->transactionBegin())
                {
                    throw new ILARIA_CoreError("Error in PersonModel::insert : unable to start a new transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Insert person, invalid pointer at main name yet
                {
                    $sql = "INSERT INTO `person`(`gender`,`trivia`,`quotes`,`birthdate`,`deathdate`,`birthname`,`minibiography`,`spouse`,`height`,`name_id`) VALUES("
                        . $this->quoteOrNull($gender) . ","
                        . $this->quoteOrNull($trivia) . ","
                        . $this->quoteOrNull($quotes) . ","
                        . $this->quoteOrNull($birthdate) . ","
                        . $this->quoteOrNull($deathdate) . ","
                        . $this->quoteOrNull($birthname) . ","
                        . $this->quoteOrNull($minibiography) . ","
                        . $this->quoteOrNull($spouse) . ","
                        . $this->quoteOrNull($height) . ","
                        . "0" . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in PersonModel::insert : unable to insert the person",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                    $personId = $this->getDatabase()->getLastInsertId();
                }

                // Insert main name, pointer to person
                {
                    $sql = "INSERT INTO `name`(`firstname`,`lastname`,`person_id`) VALUES ("
                        . $this->quoteOrNull($firstname) . ","
                        . $this->quote($lastname) . ","
                        . $personId . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in PersonModel::insert : unable to insert the main name",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                    $nameId = $this->getDatabase()->getLastInsertId();
                }

                // Update person to accomodate main name
                {
                    $sql = "UPDATE `person` SET"
                        . " `name_id`=" . $nameId
                        . " WHERE `id`=" . $personId;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in PersonModel::insert : unable to link person to its main name",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in PersonModel::insert : unable to commit the transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Gather back person ID
                $result = $personId;
            }
            catch (ILARIA_CoreError $e)
            {
                $e->writeToLog();
                $this->getDatabase()->transactionRollback();
                if ($personId > 0)
                {
                    $this->delete($personId);
                }
                $result = -1;
            }

            // terminate insertion
            {
                $sql = "SET FOREIGN_KEY_CHECKS=1";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in PersonModel::insert : unable to re-enable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $result = -1;
        }

        // Re-enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        // return success
        return $result;
    }

    // SCUD operation
    public function update($id, $firstname, $lastname, $gender, $trivia, $quotes, $birthdate, $deathdate, $birthname, $minibiography, $spouse, $height)
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
                throw new ILARIA_CoreError("Error in PersonModel::update : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Update person
            {
                $sql = "UPDATE `person` SET"
                    . " `gender`=" . $this->quoteOrNull($gender) . ","
                    . " `trivia`=" . $this->quoteOrNull($trivia) . ","
                    . " `quotes`=" . $this->quoteOrNull($quotes) . ","
                    . " `birthdate`=" . $this->quoteOrNull($birthdate) . ","
                    . " `deathdate`=" . $this->quoteOrNull($deathdate) . ","
                    . " `birthname`=" . $this->quoteOrNull($birthname) . ","
                    . " `minibiography`=" . $this->quoteOrNull($minibiography) . ","
                    . " `spouse`=" . $this->quoteOrNull($spouse) . ","
                    . " `height`=" . $this->quoteOrNull($height)
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in PersonModel::update : unable to update person record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Get main name ID
            $nameId = 0;
            {
                $sql = "SELECT PE.`name_id`"
                    . " FROM `person` PE"
                    . " WHERE PE.`id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() != 0 || $query->getCount() != 1)
                {
                    throw new ILARIA_CoreError("Error in PersonModel::update : unable to find person's main name id",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
                $nameId = $query->getData()[0]['name_id'];
            }

            // Update main name
            {
                $sql = "UPDATE `name` SET"
                    . " `firstname`=" . $this->quoteOrNull($firstname) . ","
                    . " `lastname`=" . $this->quote($lastname)
                    . " WHERE `id`=" . $nameId;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in PersonModel::update : unable to update main name record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in PersonModel::update : unable to commit the transaction",
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
            // authorize deletion
            {
                $sql = "SET FOREIGN_KEY_CHECKS=0";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in PersonModel::delete : unable to disable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            try
            {
                // Start the transaction
                if (!$this->getDatabase()->transactionBegin())
                {
                    throw new ILARIA_CoreError("Error in PersonModel::delete : unable to start a new transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Delete names
                {
                    $sql = "DELETE FROM `name`"
                        . " WHERE `person_id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in PersonModel::delete : unable to delete person's names",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Delete person
                {
                    $sql = "DELETE FROM `person`"
                        . " WHERE `id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in PersonModel::delete : unable to delete the person",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in PersonModel::delete : unable to commit the transaction",
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

            // terminate deletion
            {
                $sql = "SET FOREIGN_KEY_CHECKS=1";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in PersonModel::delete : unable to re-enable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            $result = -1;
        }

        // Re-enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        // return success
        return $result;
    }
}