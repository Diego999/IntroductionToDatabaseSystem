<?php

class TypeModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getFieldContent($id, $fieldName)
    {
        try
        {
            $sql = "SELECT TY.`" . $fieldName . "`"
                . " FROM `type` TY"
                . " WHERE TY.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0][$fieldName];
            }
            else
            {
                throw new ILARIA_CoreError("Error in TypeModel::getFieldContent : request returned status " . $query->getStatus(),
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

    public function insert($name)
    {
        try
        {
            $sql = "INSERT INTO `type`(`name`)"
                . " VALUES(" . $this->quote($name) . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in TypeModel::insert : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            return 0;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function update($id, $name)
    {
        try
        {
            $sql = "UPDATE `type` SET"
                . " `name`=" . $this->quote($name)
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in TypeModel::update : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            return 0;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function delete($id)
    {
        try
        {
            $sql = "DELETE FROM `type`"
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in TypeModel::delete : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            return 0;
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return -1;
        }
    }

    public function getTypeInfos($id)
    {
        try
        {
            $sql = "SELECT TY.`name`"
                . " FROM `type` TY"
                . " WHERE TY.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in TypeModel::getTypeInfos : request returned status " . $query->getStatus(),
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