<?php

class SerieModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // Field getter for ajax form
    public function getFieldContent($id, $fieldName)
    {
        switch ($fieldName)
        {
            case 'title':
                $sql = "SELECT TI.`title`"
                    . " FROM `title` TI"
                    . " INNER JOIN `production` PR ON TI.`id` = PR.`title_id`"
                    . " WHERE PR.`id`=" . $id;
                break;
            case 'yearstart':
            case 'yearend':
                $sql = "SELECT SE.`" . $fieldName . "`"
                    . " FROM `serie` SE"
                    . " WHERE SE.`id`=" . $id;
                break;
            case 'gender':
                $fieldName .= '_id';
                $sql = "SELECT PR.`" . $fieldName . "`"
                    . " FROM `production` PR"
                    . " WHERE PR.`id`=" . $id;
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
                switch ($fieldName)
                {
                    case 'gender_id':
                        return ($field ? $field : "u");
                    default:
                        return ($field ? $field : "");
                }
            }
            else
            {
                throw new ILARIA_CoreError("Error in SerieModel::getFieldContent : request returned status " . $query->getStatus(),
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
    public function insert($title, $yearstart, $yearend, $gender)
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
                    throw new ILARIA_CoreError("Error in SerieModel::insert : unable to disable foreign keys check",
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
                    throw new ILARIA_CoreError("Error in SerieModel::insert : unable to start a new transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Insert production, invalid pointer at main title yet
                {
                    $sql = "INSERT INTO `production`(`year`,`gender_id`,`title_id`) VALUES ("
                        . "NULL" . ","
                        . ($gender == "u" ? "NULL" : $gender) . ","
                        . "0" . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in SerieModel::insert : unable to insert the production",
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
                        throw new ILARIA_CoreError("Error in SerieModel::insert : unable to insert the main title",
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
                        throw new ILARIA_CoreError("Error in SerieModel::insert : unable to link production to its main title",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Insert serie ISA child
                {
                    $sql = "INSERT INTO `serie`(`id`,`yearstart`,`yearend`) VALUES ("
                        . $productionId . ","
                        . $this->quoteOrNull($yearstart) . ","
                        . $this->quoteOrNull($yearend) . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in SerieModel::insert : unable to insert the ISA serie child",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in SerieModel::insert : unable to commit the transaction",
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
                    throw new ILARIA_CoreError("Error in SerieModel::insert : unable to re-enable foreign keys check",
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
    public function update($id, $title, $yearstart, $yearend, $gender)
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
                throw new ILARIA_CoreError("Error in SerieModel::update : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Update production
            {
                $sql = "UPDATE `production` SET"
                    . " `gender_id`=" . ($gender == "u" ? "NULL" : $gender)
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in SerieModel::update : unable to update production record",
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
                    throw new ILARIA_CoreError("Error in SerieModel::update : unable to find production's main title id",
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
                    throw new ILARIA_CoreError("Error in SerieModel::update : unable to update main title record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Update serie
            {
                $sql = "UPDATE `serie` SET"
                    . " `yearstart`=" . $this->quoteOrNull($yearstart) . ","
                    . " `yearend`=" . $this->quoteOrNull($yearend)
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in SerieModel::update : unable to update serie record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in SerieModel::update : unable to commit the transaction",
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
                    throw new ILARIA_CoreError("Error in SerieModel::delete : unable to disable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            try
            {
                // Start the transaction
                if (!$this->getDatabase()->transactionBegin())
                {
                    throw new ILARIA_CoreError("Error in SerieModel::delete : unable to start a new transaction",
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
                        throw new ILARIA_CoreError("Error in SerieModel::delete : unable to delete production's titles",
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
                        throw new ILARIA_CoreError("Error in SerieModel::delete : unable to delete the production",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Delete singleproduction
                {
                    $sql = "DELETE FROM `serie`"
                        . " WHERE `id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in SerieModel::delete : unable to delete the serie",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Select corresponding seasons
                $seasonsList = array();
                {
                    $sql = "SELECT SEA.`id`"
                        . " FROM `season` SEA"
                        . " WHERE SEA.`serie_id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->query($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in SerieModel::delete : unable to find subsequent seasons",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                    foreach ($query->getData() as $season)
                    {
                        $seasonsList[] = $season['id'];
                    }
                }

                // Next happen only if there are seasons
                if (count($seasonsList) > 0)
                {
                    // Delete seasons
                    {
                        $sql = "DELETE FROM `season`"
                            . " WHERE `serie_id`=" . $id;
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->exec($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Error in SerieModel::delete : unable to delete subsequent seasons",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }
                    }

                    // Select corresponding episodes
                    $episodesList = array();
                    {
                        $sql = "SELECT EP.`id`"
                            . " FROM `episode` EP"
                            . " WHERE EP.`season_id` IN (";
                        $first = true;
                        foreach ($seasonsList as $season)
                        {
                            $sql .= ($first ? "" : ",") . $season;
                            $first = false;
                        }
                        $sql .= ")";
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->query($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Error in SerieModel::delete : unable to find subsequent episodes",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }
                        foreach ($query->getData() as $episode)
                        {
                            $episodesList[] = $episode['id'];
                        }
                    }

                    // Next happen only if there are episodes
                    if (count($episodesList) > 0)
                    {
                        // Delete episodes
                        {
                            $sql = "DELETE FROM `episode`"
                                . " WHERE `season_id` IN (";
                            $first = true;
                            foreach ($seasonsList as $season)
                            {
                                $sql .= ($first ? "" : ",") . $season;
                                $first = false;
                            }
                            $sql .= ")";
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->exec($query);
                            if ($query->getStatus() != 0)
                            {
                                throw new ILARIA_CoreError("Error in SerieModel::delete : unable to delete subsequent episodes",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }
                        }

                        // Delete episode's productions
                        {
                            $sql = "DELETE FROM `production`"
                                . " WHERE `id` IN (";
                            $first = true;
                            foreach ($episodesList as $episode)
                            {
                                $sql .= ($first ? "" : ",") . $episode;
                                $first = false;
                            }
                            $sql .= ")";
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->exec($query);
                            if ($query->getStatus() != 0)
                            {
                                throw new ILARIA_CoreError("Error in SerieModel::delete : unable to delete subsequent episode's productions",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }
                        }

                        // Delete episode's titles
                        {
                            $sql = "DELETE FROM `title`"
                                . " WHERE `production_id` IN (";
                            $first = true;
                            foreach ($episodesList as $episode)
                            {
                                $sql .= ($first ? "" : ",") . $episode;
                                $first = false;
                            }
                            $sql .= ")";
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->exec($query);
                            if ($query->getStatus() != 0)
                            {
                                throw new ILARIA_CoreError("Error in SerieModel::delete : unable to delete subsequent episode's titles",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }
                        }
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in SerieModel::delete : unable to commit the transaction",
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
                    throw new ILARIA_CoreError("Error in SingleproductionModel::delete : unable to re-enable foreign keys check",
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

    // List publication
    public function getListGenders()
    {
        try
        {
            $sql = "SELECT GE.`id`, GE.`name`"
                . " FROM `gender` GE"
                . " ORDER BY GE.`name` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                $list = array(
                    array(
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => "u",
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => "-unknown-",
                    ),
                );
                foreach ($query->getData() as $gender)
                {
                    $list[] = array(
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => $gender['id'],
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => $gender['name'],
                    );
                }
                return $list;
            }
            else
            {
                throw new ILARIA_CoreError("Error in SerieModel::getListGenders : request returned status " . $query->getStatus(),
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
}