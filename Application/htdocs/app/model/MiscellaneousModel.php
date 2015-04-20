<?php

class MiscellaneousModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // used in misc direct access page : list of roles (casting)
    public function getListRoles()
    {
        try
        {
            $sql = "SELECT RO.`id` AS `id`, RO.`name` AS `name`"
                . " FROM `role` RO"
                . " ORDER BY RO.`name`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in MiscellaneousModel::getListRoles : request returned status " . $query->getStatus(),
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

    // used in misc direct access page : list of genders (genres)
    public function getListGenders()
    {
        try
        {
            $sql = "SELECT GE.`id` AS `id`, GE.`name` AS `name`"
                . " FROM `gender` GE"
                . " ORDER BY GE.`name`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in MiscellaneousModel::getListGenders : request returned status " . $query->getStatus(),
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

    // used in misc direct access page : list of types (companies)
    public function getListTypes()
    {
        try
        {
            $sql = "SELECT TY.`id` AS `id`, TY.`name` AS `name`"
                . " FROM `type` TY"
                . " ORDER BY TY.`name`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in MiscellaneousModel::getListTypes : request returned status " . $query->getStatus(),
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

    // used in misc direct access page : list of kinds (movies single)
    public function getListKinds()
    {
        try
        {
            $sql = "SELECT KI.`id` AS `id`, KI.`name` AS `name`"
                . " FROM `kind` KI"
                . " ORDER BY KI.`name`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in MiscellaneousModel::getListKinds : request returned status " . $query->getStatus(),
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