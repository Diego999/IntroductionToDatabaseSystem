<?php

class CastingModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getFieldContent($id, $fieldName)
    {
        switch ($fieldName)
        {
            case 'person':
                $sql = "SELECT NA.`firstname`, NA.`lastname`, PE.`id`"
                    . " FROM `person` PE"
                    . " INNER JOIN `name` NA ON PE.`name_id` = NA.`id`"
                    . " INNER JOIN `casting` CA ON PE.`id` = CA.`person_id`"
                    . " WHERE CA.`id`=" . $id;
                break;
            case 'role':
                $sql = "SELECT CA.`role_id` AS `role`"
                    . " FROM `casting` CA"
                    . " WHERE CA.`id`=" . $id;
                break;
            case 'character':
                $sql = "SELECT CH.`name` AS `character`"
                    . " FROM `character` CH"
                    . " RIGHT JOIN `casting` CA ON CH.`id` = CA.`character_id`"
                    . " WHERE CA.`id`=" . $id;
                break;
            case 'production':
                $sql = "SELECT CA.`production_id` AS `production`"
                    . " FROM `casting` CA"
                    . " WHERE CA.`id`=" . $id;
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
                $result = $query->getData()[0];
                switch ($fieldName)
                {
                    case 'person':
                        return array(
                            'id' => $result['id'],
                            'val' => $result['firstname'] . " " . $result['lastname'],
                        );
                    default:
                        return $result[$fieldName];
                }
            }
            else
            {
                throw new ILARIA_CoreError("Error in CastingModel::getFieldContent : request returned status " . $query->getStatus(),
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

    public function insert($personId, $productionId, $roleId, $character)
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
                throw new ILARIA_CoreError("Error in CastingModel::insert : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Search for character
            $characterId = "NULL";
            if ($character != "")
            {
                $sql = "SELECT CH.`id`"
                    . " FROM `character` CH"
                    . " WHERE CH.`name`=" . $this->quote($character);
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() == 0)
                {
                    // No character found, create
                    if ($query->getCount() == 0)
                    {
                        $sql = "INSERT INTO `character`(`name`) VALUES ("
                            . $this->quote($character) . ")";
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->exec($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Error in CastingModel::insert : unable to insert new character",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }
                        $characterId = $this->getDatabase()->getLastInsertId();
                    }

                    // Character found, gather ID
                    else
                    {
                        $characterId = $query->getData()[0]['id'];
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("Error in CastingModel::insert : unable to search through characters",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Insert casting
            {
                $sql = "INSERT INTO `casting`(`person_id`,`production_id`,`role_id`,`character_id`) VALUES ("
                    . $personId . ","
                    . $productionId . ","
                    . $roleId . ","
                    . $characterId . ")";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in CastingModel::insert : unable to insert casting record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in CastingModel::insert : unable to commit the transaction",
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

    public function update($id, $personId, $roleId, $character)
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
                throw new ILARIA_CoreError("Error in CastingModel::update : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Search for character
            $characterId = "NULL";
            if ($character != "")
            {
                $sql = "SELECT CH.`id`"
                    . " FROM `character` CH"
                    . " WHERE CH.`name`=" . $this->quote($character);
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus() == 0)
                {
                    // No character found, create
                    if ($query->getCount() == 0)
                    {
                        $sql = "INSERT INTO `character`(`name`) VALUES ("
                            . $this->quote($character) . ")";
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->exec($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Error in CastingModel::update : unable to insert new character",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }
                        $characterId = $this->getDatabase()->getLastInsertId();
                    }

                    // Character found, gather ID
                    else
                    {
                        $characterId = $query->getData()[0]['id'];
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("Error in CastingModel::update : unable to search through characters",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Update casting
            {
                $sql = "UPDATE `casting` SET"
                    . " `person_id`=" . $personId . ","
                    . " `role_id`=" . $roleId . ","
                    . " `character_id`=" . $characterId
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in CastingModel::update : unable to update casting record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in CastingModel::update : unable to commit the transaction",
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
                throw new ILARIA_CoreError("Error in CastingModel::delete : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Delete casting
            {
                $sql = "DELETE FROM `casting`"
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in CastingModel::delete : unable to delete casting record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in CastingModel::delete : unable to commit the transaction",
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

    public function getListRoles()
    {
        try
        {
            $sql = "SELECT RO.`id`, RO.`name`"
                . " FROM `role` RO"
                . " ORDER BY RO.`name` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                $list = array();
                foreach ($query->getData() as $role)
                {
                    $list[] = array(
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => $role['id'],
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => $role['name'],
                    );
                }
                return $list;
            }
            else
            {
                throw new ILARIA_CoreError("Error in CastingModel::getListRoles : request returned status " . $query->getStatus(),
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