<?php

class SingleproductionModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
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
            case 'year':
                $sql = "SELECT PR.`" . $fieldName . "`"
                    . " FROM `production` PR"
                    . " WHERE PR.`id`=" . $id;
                break;
            case 'gender':
                $fieldName .= '_id';
                $sql = "SELECT PR.`" . $fieldName . "`"
                    . " FROM `production` PR"
                    . " WHERE PR.`id`=" . $id;
                break;
            case 'kind':
                $fieldName .= '_id';
                $sql = "SELECT SP.`" . $fieldName . "`"
                    . " FROM `singleproduction` SP"
                    . " WHERE SP.`id`=" . $id;
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
                throw new ILARIA_CoreError("Error in SingleproductionModel::getFieldContent : request returned status " . $query->getStatus(),
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
    public function insert($title, $year, $kind, $gender)
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
                    throw new ILARIA_CoreError("Error in SingleproductionModel::insert : unable to disable foreign keys check",
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
                    throw new ILARIA_CoreError("Error in Singleproduction::insert : unable to start a new transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Insert production, invalid pointer at main title yet
                {
                    $sql = "INSERT INTO `production`(`year`,`gender_id`,`title_id`) VALUES ("
                        . $this->quoteOrNull($year) . ","
                        . ($gender == "u" ? "NULL" : $gender) . ","
                        . "0" . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in Singleproduction::insert : unable to insert the production",
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
                        throw new ILARIA_CoreError("Error in Singleproduction::insert : unable to insert the main title",
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
                        throw new ILARIA_CoreError("Error in Singleproduction::insert : unable to link production to its main title",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Insert singleproduction ISA child
                {
                    $sql = "INSERT INTO `singleproduction`(`id`,`kind_id`) VALUES ("
                        . $productionId . ","
                        . $kind . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in Singleproduction::insert : unable to insert the ISA singleproduction child",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in Singleproduction::insert : unable to commit the transaction",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }

                // Gather back person ID
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
                    throw new ILARIA_CoreError("Error in Singleproduction::insert : unable to re-enable foreign keys check",
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
    public function update($id, $title, $year, $kind, $gender)
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
                throw new ILARIA_CoreError("Error in Singleproduction::update : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Update production
            {
                $sql = "UPDATE `production` SET"
                    . " `year`=" . $this->quoteOrNull($year) . ","
                    . " `gender_id`=" . ($gender == "u" ? "NULL" : $gender)
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in SingleproductionModel::update : unable to update production record",
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
                    throw new ILARIA_CoreError("Error in SingleproductionModel::update : unable to find production's main title id",
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
                    throw new ILARIA_CoreError("Error in SingleproductionModel::update : unable to update main title record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Update singleproduction
            {
                $sql = "UPDATE `singleproduction` SET"
                    . " `kind_id`=" . $kind
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in SingleproductionModel::update : unable to update singleproduction record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in Singleproduction::update : unable to commit the transaction",
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
                    throw new ILARIA_CoreError("Error in SingleproductionModel::delete : unable to disable foreign keys check",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            try
            {
                // Start the transaction
                if (!$this->getDatabase()->transactionBegin())
                {
                    throw new ILARIA_CoreError("Error in SingleproductionModel::delete : unable to start a new transaction",
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
                        throw new ILARIA_CoreError("Error in SingleproductionModel::delete : unable to delete production's titles",
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
                        throw new ILARIA_CoreError("Error in SingleproductionModel::delete : unable to delete the production",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Delete singleproduction
                {
                    $sql = "DELETE FROM `singleproduction`"
                        . " WHERE `id`=" . $id;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Error in SingleproductionModel::delete : unable to delete the singleproduction",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }

                // Commit the transaction
                if (!$this->getDatabase()->transactionCommit())
                {
                    throw new ILARIA_CoreError("Error in SingleproductionModel::delete : unable to commit the transaction",
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
                throw new ILARIA_CoreError("Error in SingleproductionModel::getListGenders : request returned status " . $query->getStatus(),
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

    // List publication
    public function getListKinds()
    {
        try
        {
            $sql = "SELECT KI.`id`, KI.`name`"
                . " FROM `kind` KI"
                . " ORDER BY KI.`name` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                $list = array();
                foreach ($query->getData() as $kind)
                {
                    $list[] = array(
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => $kind['id'],
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => $kind['name'],
                    );
                }
                return $list;
            }
            else
            {
                throw new ILARIA_CoreError("Error in SingleproductionModel::getListKinds : request returned status " . $query->getStatus(),
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