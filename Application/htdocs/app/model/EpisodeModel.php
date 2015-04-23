<?php

class EpisodeModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // Get infos
    public function getEpisodeInfos($id)
    {
        try
        {
            $sql = "SELECT TI.`title`, SEA.`serie_id`"
                . " FROM `episode` EP"
                . " INNER JOIN `production` PR ON EP.`id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " INNER JOIN `season` SEA ON EP.`season_id` = SEA.`id`"
                . " WHERE EP.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in EpisodeModel::getEpisodeInfos : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
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
        switch($fieldName)
        {
            case 'title':
                $sql = "SELECT TI.`title`"
                    . " FROM `title` TI"
                    . " INNER JOIN `production` PR ON TI.`id` = PR.`title_id`"
                    . " WHERE PR.`id`=" . $id;
                break;
            case 'year':
                $sql = "SELECT PR.`" . $fieldName . "`"
                    . " FROM `production` PR"
                    . " WHERE PR.`id`=" . $id;
                break;
            case 'episode_number':
                $sql = "SELECT EP.`number` AS `episode_number`"
                    . " FROM `episode` EP"
                    . " WHERE EP.`id`=" . $id;
                break;
            case 'season_number':
                $sql = "SELECT SEA.`number` AS `season_number`"
                    . " FROM `season` SEA"
                    . " INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`"
                    . " WHERE EP.`id`=" . $id;
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
                $field = $query->getData()[0][$fieldName];
                return ($field ? $field : "");
            }
            else
            {
                throw new ILARIA_CoreError("Error in EpisodeModel::getFieldContent : request returned status " . $query->getStatus(),
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
    public function insert($title, $year, $episodeNumber, $seasonNumber, $serieId)
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
                    throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to disable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            try
            {
                // ID of inserted production
                $productionId = 0;
                $titleId = 0;

                // Start the transaction
                if (!$this->getDatabase()->transactionBegin())
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to start a new transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Insert production, invalid pointer at main title yet
                {
                    $sql = "INSERT INTO `production`(`year`,`gender_id`,`title_id`) VALUES ("
                        . $this->quoteOrNull($year) . ","
                        . "NULL" . ","
                        . "0" . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to insert the production",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                    $productionId = $this->getDatabase()->getLastInsertId();
                }

                // Insert main title, pointer to production
                {
                    $sql = "INSERT INTO `title`(`title`,`production_id`) VALUES ("
                        . $this->quote($title) . ","
                        . $productionId . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to insert the main title",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                    $titleId = $this->getDatabase()->getLastInsertId();
                }

                // Update production to accomodate main name
                {
                    $sql = "UPDATE `production` SET"
                        . " `title_id`=" . $titleId
                        . " WHERE `id`=" . $productionId;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to link production to its main title",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Search for season
                $seasonId = 0;
                {
                    $sql = "SELECT SEA.`id`"
                        . " FROM `season` SEA"
                        . " WHERE SEA.`serie_id`=" . $serieId
                        . " AND SEA.`number`" . ($seasonNumber == "" ? " IS NULL" : "=" . $seasonNumber);
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->query($query);
                    if ($query->getStatus() == 0)
                    {
                        // No season found, create
                        if ($query->getCount() == 0)
                        {
                            $sql = "INSERT INTO `season`(`number`,`serie_id`) VALUES ("
                                . $this->quoteOrNull($seasonNumber) . ","
                                . $serieId . ")";
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->exec($query);
                            if ($query->getStatus() != 0)
                            {
                                throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to insert new season",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }
                            $seasonId = $this->getDatabase()->getLastInsertId();
                        }

                        // Season found, gather ID
                        else
                        {
                            $seasonId = $query->getData()[0]['id'];
                        }
                    }
                    else
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to search through seasons",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Insert episode ISA child
                {
                    $sql = "INSERT INTO `episode`(`id`,`number`,`season_id`) VALUES ("
                        . $productionId . ","
                        . $this->quoteOrNull($episodeNumber) . ","
                        . $seasonId . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to insert the ISA episode child",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to commit the transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Gather back production ID
                $result = $productionId;
            }
            catch (ILARIA_CoreError $e)
            {
                $e->writeToLog();
                $this->getDatabase()->transactionRollback();
                $result = -1;
            }

            // terminate insertion
            {
                $sql = "SET FOREIGN_KEY_CHECKS=1";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::insert : unable to re-enable foreign keys check",
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
    public function update($id, $title, $year, $episodeNumber, $seasonNumber)
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
                throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Update production
            {
                $sql = "UPDATE `production` SET"
                    . " `year`=" . $this->quoteOrNull($year)
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to update production record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Get main title ID
            $titleId = 0;
            {
                $sql = "SELECT PR.`title_id`"
                    . " FROM `production` PR"
                    . " WHERE PR.`id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() != 0 || $query->getCount() != 1)
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to find production's main title id",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
                $titleId = $query->getData()[0]['title_id'];
            }

            // Update main title
            {
                $sql = "UPDATE `title` SET"
                    . " `title`=" . $this->quote($title)
                    . " WHERE `id`=" . $titleId;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to update main title record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Select current serie ID
            $serieId = 0;
            {
                $sql = "SELECT SEA.`serie_id`"
                    . " FROM `season` SEA"
                    . " INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`"
                    . " WHERE EP.`id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() != 0 || $query->getCount() != 1)
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to find related serie ID",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
                $serieId = $query->getData()[0]['serie_id'];
            }

            // Search for season
            $seasonId = 0;
            {
                $sql = "SELECT SEA.`id`"
                    . " FROM `season` SEA"
                    . " WHERE SEA.`serie_id`=" . $serieId
                    . " AND SEA.`number`" . ($seasonNumber == "" ? " IS NULL" : "=" . $seasonNumber);
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() == 0)
                {
                    // No season found, create
                    if ($query->getCount() == 0)
                    {
                        $sql = "INSERT INTO `season`(`number`,`serie_id`) VALUES ("
                            . $this->quoteOrNull($seasonNumber) . ","
                            . $serieId . ")";
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->exec($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to insert new season",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }
                        $seasonId = $this->getDatabase()->getLastInsertId();
                    }

                    // Season found, gather ID
                    else
                    {
                        $seasonId = $query->getData()[0]['id'];
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to search through seasons",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Update episode
            {
                $sql = "UPDATE `episode` SET"
                    . " `number`=" . $this->quoteOrNull($episodeNumber) . ","
                    . " `season_id`=" . $seasonId
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to update episode record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in EpisodeModel::update : unable to commit the transaction",
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
                    throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to disable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            try
            {
                // Start the transaction
                if (!$this->getDatabase()->transactionBegin())
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to start a new transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Delete titles
                {
                    $sql = "DELETE FROM `title`"
                        . " WHERE `production_id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to delete production's titles",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Delete production
                {
                    $sql = "DELETE FROM `production`"
                        . " WHERE `id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to delete the production",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Delete episode
                {
                    $sql = "DELETE FROM `episode`"
                        . " WHERE `id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to delete the episode",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Delete corresponding casting rows
                {
                    $sql = "DELETE FROM `casting`"
                        . " WHERE `production_id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to delete subsequent casting records",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Delete corresponding productioncompany rows
                {
                    $sql = "DELETE FROM `productioncompany`"
                        . " WHERE `production_id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to delete subsequent productioncompany records",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to commit the transaction",
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
                    throw new ILARIA_CoreError("Error in EpisodeModel::delete : unable to re-enable foreign keys check",
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