<?php

class KindModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getFieldContent($id, $fieldName)
    {
        try
        {
            $sql = "SELECT KI.`" . $fieldName . "`"
                . " FROM `kind` KI"
                . " WHERE KI.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0][$fieldName];
            }
            else
            {
                throw new ILARIA_CoreError("Error in KindModel::getFieldContent : request returned status " . $query->getStatus(),
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
            $sql = "INSERT INTO `kind`(`name`)"
                . " VALUES(" . $this->quote($name) . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in KindModel::insert : request returned status " . $query->getStatus(),
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
            $sql = "UPDATE `kind` SET"
                . " `name`=" . $this->quote($name)
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in KindModel::update : request returned status " . $query->getStatus(),
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
            $sql = "DELETE FROM `kind`"
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in KindModel::delete : request returned status " . $query->getStatus(),
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

    public function getKindInfos($id)
    {
        try
        {
            $sql = "SELECT KI.`name`"
                . " FROM `kind` KI"
                . " WHERE KI.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in KindModel::getKindInfos : request returned status " . $query->getStatus(),
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