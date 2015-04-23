<?php

class ProductioncompanyModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getFieldContent($id, $fieldName)
    {
        switch ($fieldName)
        {
            case 'company':
                $sql = "SELECT COM.`name`, COU.`code` AS `country`, COM.`id`"
                    . " FROM `company` COM"
                    . " LEFT JOIN `country` COU ON COM.`country_id` = COU.`id`"
                    . " INNER JOIN `productioncompany` PC ON COM.`id` = PC.`company_id`"
                    . " WHERE PC.`id`=" . $id;
                break;
            case 'type':
                $sql = "SELECT PC.`type_id` AS `type`"
                    . " FROM `productioncompany` PC"
                    . " WHERE PC.`id`=" . $id;
                break;
            case 'production':
                $sql = "SELECT PC.`production_id` AS `production`"
                    . " FROM `productioncompany` PC"
                    . " WHERE PC.`id`=" . $id;
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
                    case 'company':
                        return array(
                            'id' => $result['id'],
                            'val' => $result['name'] . ($result['country'] ? " (" . $result['country'] . ")" : ""),
                        );
                    default:
                        return $result[$fieldName];
                }
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::getFieldContent : request returned status " . $query->getStatus(),
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

    public function insert($productionId, $companyId, $typeId)
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
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::insert : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Insert productioncompany
            {
                $sql = "INSERT INTO `productioncompany`(`production_id`,`company_id`,`type_id`) VALUES ("
                    . $productionId . ","
                    . $companyId . ","
                    . $typeId . ")";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in ProductioncompanyModel::insert : unable to insert productioncompany record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::insert : unable to commit the transaction",
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

    public function update($id, $companyId, $typeId)
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
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::update : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Update productioncompany
            {
                $sql = "UPDATE `productioncompany` SET"
                    . " `company_id`=" . $companyId . ","
                    . " `type_id`=" . $typeId
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in ProductioncompanyModel::update : unable to update productioncompany record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::update : unable to commit the transaction",
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
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::delete : unable to start a new transaction",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Delete casting
            {
                $sql = "DELETE FROM `productioncompany`"
                    . " WHERE `id`=" . $id;
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->exec($query);
                if ($query->getStatus() != 0)
                {
                    throw new ILARIA_CoreError("Error in ProductioncompanyModel::delete : unable to delete productioncompany record",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::delete : unable to commit the transaction",
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

    public function getListTypes()
    {
        try
        {
            $sql = "SELECT TY.`id`, TY.`name`"
                . " FROM `type` TY"
                . " ORDER BY TY.`name` ASC";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                $list = array();
                foreach ($query->getData() as $type)
                {
                    $list[] = array(
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_KEY => $type['id'],
                        ILARIA_ModuleFormbuilderFieldSelect::ELEM_VAL => $type['name'],
                    );
                }
                return $list;
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductioncompanyModel::getListTypes : request returned status " . $query->getStatus(),
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