<?php

class AlternativenameModel extends ILARIA_ApplicationModel implements ILARIA_ModuleFormbuilderFieldGetterModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getFieldContent($id, $fieldName)
    {
        try
        {
            $sql = "SELECT NA.`" . $fieldName . "`"
                . " FROM `name` NA"
                . " WHERE NA.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0][$fieldName];
            }
            else
            {
                throw new ILARIA_CoreError("Error in AlternativenameModel::getFieldContent : request returned status " . $query->getStatus(),
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

    public function insert($firstname, $lastname, $personId)
    {
        try
        {
            $sql = "INSERT INTO `name`(`firstname`,`lastname`,`person_id`) VALUES ("
                . $this->quoteOrNull($firstname) . ","
                . $this->quote($lastname) . ","
                . $personId . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in AlternativenameModel::insert : request returned status " . $query->getStatus(),
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

    public function update($id, $firstname, $lastname)
    {
        try
        {
            $sql = "UPDATE `name` SET"
                . " `firstname`=" . $this->quoteOrNull($firstname) . ","
                . " `lastname`=" . $this->quote($lastname)
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in AlternativenameModel::update : request returned status " . $query->getStatus(),
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
            $sql = "DELETE FROM `name`"
                . " WHERE `id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Error in AlternativenameModel::delete : request returned status " . $query->getStatus(),
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

    public function getAlternativeNameInfos($id)
    {
        try
        {
            $sql = "SELECT NA.`firstname`, NA.`lastname`, NA.`person_id`"
                . " FROM `name` NA"
                . " WHERE NA.`id`=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in AlternativenameModel::getAlternativeNameInfos : request returned status " . $query->getStatus(),
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