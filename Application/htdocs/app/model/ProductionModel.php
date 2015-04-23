<?php

class ProductionModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    const CARD_SINGLE = 'eb266fc5bd4fe5120caaad36f37590e0a34232f8';
    const CARD_SERIE = 'a87213e43ac6b8d92be460427c1031e3ab26b238';
    const CARD_EPISODE = 'a5220d1ce7bdd2037d90a24677fcd9a6927c1c28';

    // used in production details page : determine kind of production (ISA hierarchy)
    public function getProductionCardinality($productionId)
    {
        try
        {
            $sql = "("
                . " SELECT SP.`id` AS `single`, NULL AS `serie`, NULL AS `episode`"
                . " FROM `singleproduction` SP"
                . " WHERE SP.`id`=" . $productionId
                . " ) UNION ("
                . " SELECT NULL AS `single`, SE.`id` AS `serie`, NULL AS `episode`"
                . " FROM `serie` SE"
                . " WHERE SE.`id`=" . $productionId
                . " ) UNION ("
                . " SELECT NULL AS `single`, NULL AS `serie`, EP.`id` AS `episode`"
                . " FROM `episode` EP"
                . " WHERE EP.`id`=" . $productionId
                . " )";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                $result = $query->getData()[0];
                if ($result['single'])
                {
                    return self::CARD_SINGLE;
                }
                else if ($result['serie'])
                {
                    return self::CARD_SERIE;
                }
                else if ($result['episode'])
                {
                    return self::CARD_EPISODE;
                }
                else
                {
                    throw new ILARIA_CoreError("Error in ProductionModel::getProductionCardinality : covering constraint of ISA hierarchy violated",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_ADMIN);
                }
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getProductionCardinality : request returned status " . $query->getStatus(),
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

    // used in production details page : infos of a singleproduction
    public function getSingleInfosGeneral($productionId)
    {
        try
        {
            $sql = "SELECT PR.`id` AS `prod_id`, TI.`title` AS `prod_title`, PR.`year` AS `prod_year`, KI.`name` AS `prod_kind`, GE.`name` AS `prod_gender`, PR.`title_id` AS `maintitle_id`"
                . " FROM `production` PR"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " INNER JOIN `singleproduction` SP ON PR.`id` = SP.`id`"
                . " INNER JOIN `kind` KI ON SP.`kind_id` = KI.`id`"
                . " WHERE PR.`id`=" . $productionId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getSingleInfosGeneral : request returned status " . $query->getStatus(),
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

    // used in production details page : infos of an episode
    public function getEpisodeInfosGeneral($productionId)
    {
        try
        {
            $sql = "SELECT EP.`id` AS `prod_id`, EP_TI.`title` AS `episode_title`, EP.`number` AS `episode_number`, EP_PR.`year` AS `episode_year`, EP_TI.`id` AS `maintitle_id`, SEA.`number` AS `season_number`, SER.`id` AS `serie_id`, SER_TI.`title` AS `serie_title`, SER_GE.`name` AS `serie_gender`"
                . " FROM `episode` EP"
                . " INNER JOIN `production` EP_PR ON EP.`id` = EP_PR.`id`"
                . " INNER JOIN `title` EP_TI ON EP_PR.`title_id` = EP_TI.`id`"
                . " INNER JOIN `season` SEA ON EP.`season_id` = SEA.`id`"
                . " INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`"
                . " INNER JOIN `production` SER_PR ON SER.`id` = SER_PR.`id`"
                . " INNER JOIN `title` SER_TI ON SER_PR.`title_id` = SER_TI.`id`"
                . " LEFT JOIN `gender` SER_GE ON SER_PR.`gender_id` = SER_GE.`id`"
                . " WHERE EP.`id`=" . $productionId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getEpisodeInfosGeneral : request returned status " . $query->getStatus(),
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

    // used in production details page : infos of a serie
    public function getSerieInfosGeneral($productionId)
    {
        try
        {
            $sql = "SELECT SER.`id` AS `prod_id`, TI.`title` AS `serie_title`, SER.`yearstart` AS `serie_yearstart`, SER.`yearend` AS `serie_yearend`, GE.`name` AS `serie_gender`, TI.`id` AS `maintitle_id`, COUNT(DISTINCT SEA.`id`) AS `season_count`, COUNT(DISTINCT EP.`id`) AS `episode_count`"
                . " FROM `serie` SER"
                . " INNER JOIN `production` PR ON SER.`id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " LEFT JOIN `gender` GE ON PR.`gender_id` = GE.`id`"
                . " LEFT JOIN `season` SEA ON SER.`id` = SEA.`serie_id`"
                . " LEFT JOIN `episode` EP ON SEA.`id` = EP.`season_id`"
                . " WHERE SER.`id`=" . $productionId
                . " GROUP BY SER.`id`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getSerieInfosGeneral : request returned status " . $query->getStatus(),
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

    // used in production details page : list of seasons in a serie
    public function getSeasonsList($serieId)
    {
        try
        {
            $sql = "SELECT SEA.`id` AS `season_id`, SEA.`number` AS `season_number`, COUNT(DISTINCT EP.`id`) AS `episode_count`"
                . " FROM `season` SEA"
                . " INNER JOIN `episode` EP ON SEA.`id` = EP.`season_id`"
                . " WHERE SEA.`serie_id`=" . $serieId
                . " GROUP BY SEA.`id`"
                . " ORDER BY SEA.`number`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getSeasonsList : request returned status " . $query->getStatus(),
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

    // used in production details page : informations about a season (serie name, season number)
    public function getSeasonInfos($seasonId)
    {
        try
        {
            $sql = "SELECT SEA.`number` AS `season_number`, TI.`title` AS `serie_title`"
                . " FROM `season` SEA"
                . " INNER JOIN `serie` SER ON SEA.`serie_id` = SER.`id`"
                . " INNER JOIN `production` PR ON SER.`id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " WHERE SEA.`id`=" . $seasonId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getSeasonInfos : request returned status " . $query->getStatus(),
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

    // used in production details page : list of episodes in a given season
    public function getEpisodesList($seasonId)
    {
        try
        {
            $sql = "SELECT EP.`id` AS `episode_id`, TI.`title` AS `episode_title`, EP.`number` AS `episode_number`"
                . " FROM `episode` EP"
                . " INNER JOIN `production` PR ON EP.`id` = PR.`id`"
                . " INNER JOIN `title` TI ON PR.`title_id` = TI.`id`"
                . " WHERE EP.`season_id`=" . $seasonId
                . " ORDER BY EP.`number`";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getEpisodesList : request returned status " . $query->getStatus(),
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

    // used in production details page : get the casting (persons, characters, roles, ...)
    public function getProductionCasting($productionId)
    {
        try
        {
            $sql = "SELECT PE.`id` AS `person_id`, NA.`lastname` AS `person_lastname`, NA.`firstname` AS `person_firstname`, RO.`name` AS `role_name`, CH.`name` AS `char_name`"
                . " FROM `casting` CA"
                . " INNER JOIN `person` PE ON CA.`person_id` = PE.`id`"
                . " INNER JOIN `name` NA ON PE.`name_id` = NA.`id`"
                . " INNER JOIN `role` RO ON CA.`role_id` = RO.`id`"
                . " LEFT JOIN `character` CH ON CA.`character_id` = CH.`id`"
                . " WHERE CA.`production_id`=" . $productionId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getProductionCasting : request returned status " . $query->getStatus(),
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

    // used in production details page : get the companies
    public function getProductionCompanies($productionId)
    {
        try
        {
            $sql = "SELECT COM.`id` AS `id`, COM.`name` AS `name`, COU.`code` AS `country`, TY.`name` AS `type`"
                . " FROM `productioncompany` PC"
                . " INNER JOIN `company` COM ON PC.`company_id` = COM.`id`"
                . " INNER JOIN `country` COU ON COM.`country_id` = COU.`id`"
                . " INNER JOIN `type` TY ON PC.`type_id` = TY.`id`"
                . " WHERE PC.`production_id`=" . $productionId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getProductionCompanies : request returned status " . $query->getStatus(),
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

    // used in production details page : get the alternative titles
    public function getProductionAlternativeTitles($productionId, $mainTitleId)
    {
        try
        {
            $sql = "SELECT TI.`id`, TI.`title`"
                . " FROM `title` TI"
                . " WHERE TI.`production_id`=" . $productionId
                . " AND TI.`id`!=" . $mainTitleId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                return $query->getData();
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getProductionAlternativeTitles : request returned status " . $query->getStatus(),
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

    // used in production direct access page : get the statistics
    public function getStatistics()
    {
        try
        {
            $sql = "SELECT SINGLE.`count` AS `count_single`, SERIE.`count` AS `count_serie`, EPISODE.`count` AS `count_episode`"
                . " FROM"
                . " (SELECT COUNT(`id`) AS `count` FROM `singleproduction`) SINGLE,"
                . " (SELECT COUNT(`id`) AS `count` FROM `serie`) SERIE,"
                . " (SELECT COUNT(`id`) AS `count` FROM `episode`) EPISODE";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0 && $query->getCount() == 1)
            {
                return $query->getData()[0];
            }
            else
            {
                throw new ILARIA_CoreError("Error in ProductionModel::getStatistics : request returned status " . $query->getStatus(),
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