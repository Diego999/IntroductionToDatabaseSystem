<?php

class FormbuilderModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function checkUnique($table, $field, $value, $ignore)
    {
        try
        {
            $value = (is_numeric($value) ? intval($value) : $this->quote($value));
            $sql = "SELECT COUNT(T.`id`) AS `count`"
                . " FROM `" . $table . "` T"
                . " WHERE T.`" . $field . "`=" . $value
                . " AND T.`id`!=" . $ignore;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return ($query->getData()[0]['count'] == 0);
            }
            else
            {
                throw new ILARIA_CoreError("Error in FormbuilderModel::checkUnique : request returned status " . $query->getStatus(),
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            $e->writeToLog();
            return false;
        }
    }
}