<?php

class SearchModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    public function getCharactersLikeName($name)
    {
        try
        {
            $sql = "SELECT DISTINCT CH.`id`, CH.`name`, COUNT(DISTINCT CA.`person_id`) AS `persons_count`, COUNT(DISTINCT CA.`production_id`) AS `productions_count`"
                . " FROM `character` CH INNER JOIN `casting` CA ON CH.`id`=CA.`character_id`"
                . " WHERE CH.`name` LIKE \"%" . $name . "%\""
                . " GROUP BY CH.`id`"
                . " ORDER BY CH.`id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in SearchModel::getCharactersLikeName : request returned status " . $query->getStatus(),
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