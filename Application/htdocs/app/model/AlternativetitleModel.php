<?php

class AlternativetitleModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getFieldContent($id, $fieldName)
    {
        try
        {
            $sql = "SELECT TI.`" . $fieldName . "`"
                . " FROM `title` TI"
                . " WHERE TI.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0][$fieldName];
            }
            else
            {
                throw new ILARIA_CoreError("Error in AlternativetitleModel::getFieldContent : request returned status " . $query->getStatus(),
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

    public function insert($title, $productionId)
    {
        try
        {
            $sql = "INSERT INTO `title`(`title`,`production_id`) VALUES ("
                . $this->quote($title) . ","
                . $productionId . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in AlternativetitleModel::insert : request returned status " . $query->getStatus(),
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

    public function update($id, $title)
    {
        try
        {
            $sql = "UPDATE `title` SET"
                . " `title`=" . $this->quote($title)
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in AlternativetitleModel::update : request returned status " . $query->getStatus(),
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
            $sql = "DELETE FROM `title`"
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in AlternativetitleModel::delete : request returned status " . $query->getStatus(),
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

    public function getAlternativeTitleInfos($id)
    {
        try
        {
            $sql = "SELECT TI.`title`, TI.`production_id`"
                . " FROM `title` TI"
                . " WHERE TI.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in AlternativetitleModel::getAlternativeTitleInfos : request returned status " . $query->getStatus(),
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
}