<?php

class ImportproductionModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // Importation protection
    private $isImportAuthorized = false;

    // Fields description for table "production"
    const PRODUCTION_ID = 'prod_id';
    const PRODUCTION_YEAR = 'prod_year';
    const PRODUCTION_TITLE_ID = 'prod_title_id';
    const PRODUCTION_GENDER_ID = 'prod_gender_id';

    // Fields description for table "title"
    const TITLE_ID = 'title_id';
    const TITLE_TITLE = 'title_title';
    const TITLE_PRODUCTION_ID = 'title_production_id';

    // Fields description for table "gender"
    const GENDER_ID = 'gender_id';
    const GENDER_NAME = 'gender_name';

    // Fields description for table "kind"
    const KIND_ID = 'kind_id';
    const KIND_NAME = 'kind_name';

    // Fields description for table "episode"
    const EPISODE_ID = 'episode_id';
    const EPISODE_NUMBER = 'episode_number';
    const EPISODE_SEASON_ID = 'episode_season_id';

    // Fields description for table "serie"
    const SERIE_ID = 'serie_id';
    const SERIE_YEARSTART = 'serie_yearstart';
    const SERIE_YEAREND = 'serie_yearend';

    // Fields description for table "singleproduction"
    const SINGLE_ID = 'single_id';
    const SINGLE_KIND_ID = 'single_kind_id';

    // Fields description for table "season"
    const SEASON_ID = 'season_id';
    const SEASON_NUMBER = 'season_number';
    const SEASON_SERIE_ID = 'season_serie_id';

    // Array identifiers
    const ARRAY_TITLES = 'titles';

    // Return status
    const IMPORT_STATUS_REDUNDANT_TITLE = -3;
    const IMPORT_STATUS_ERROR = -1;

    // Authorize importation of productions
    public function authorizeImport()
    {
        if (!$this->isImportAuthorized)
        {
            $query = new ILARIA_DatabaseQuery("ALTER TABLE production CHANGE COLUMN title_id title_id INT UNSIGNED NULL");
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                return false;
            }
            $this->isImportAuthorized = true;
        }
        return true;
    }

    // Prohibit importation of productions
    public function prohibitImport()
    {
        if ($this->isImportAuthorized)
        {
            $query = new ILARIA_DatabaseQuery("ALTER TABLE production CHANGE COLUMN title_id title_id INT UNSIGNED NOT NULL");
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                return false;
            }
            $this->isImportAuthorized = false;
        }
        return true;
    }

    // Import a production
    public function importProduction($details)
    {
        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        // Gather result (0 = insertion OK)
        $result = 0;
        try
        {
            // Check for authorization
            if (!$this->isImportAuthorized)
            {
                throw new ILARIA_CoreError("Insertion of production failed : import not authorized",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Prepare production ID
            $productionId = 0;
/*
            $sql = "SELECT id FROM production WHERE id=" . $details[self::PRODUCTION_ID];
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Chier putain de merde",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            else
            {
                if ($query->getCount() == 0)
                {
                    // Gather season ID
                    $seasonId = "";
                    if ($details[self::SEASON_NUMBER] == "NULL")
                    {
                        $sql = "SELECT id FROM season WHERE serie_id=" . $details[self::SEASON_SERIE_ID] . " AND number IS NULL";
                    }
                    else
                    {
                        $sql = "SELECT id FROM season WHERE serie_id=" . $details[self::SEASON_SERIE_ID] . " AND number=" . $details[self::SEASON_NUMBER];
                    }
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->query($query);
                    if ($query->getStatus() == 0)
                    {
                        switch($query->getCount())
                        {
                            case 0:

                                throw new ILARIA_CoreError("This is not supposed to arrive",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);

                                break;
                            case 1:

                                // Gather season ID
                                $seasonId = $query->getData()[0]['id'];

                                break;
                            default:
                                throw new ILARIA_CoreError("Insertion of production failed : " . $query->getCount() . " seasons found, expected 0 or 1",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                        }
                    }
                    else
                    {
                        throw new ILARIA_CoreError("Insertion of production failed : error while accessing seasons table",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }

                    // Find episode for which we are duplicating entry
                    $sql = "SELECT id FROM episode WHERE number=" . $details[self::EPISODE_NUMBER] . " AND season_id=" . $seasonId;
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->query($query);
                    if (($query->getStatus() == 0) && ($query->getCount() == 1))
                    {
                        // Gather original episode id
                        $episodeId = $query->getData()[0]['id'];
                    }
                    else
                    {
                        throw new ILARIA_CoreError("Insertion of production failed : error while accessing episodes table to find duplicate entry",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }

                    // Re-enable autocommit
                    $this->getDatabase()->transactionSetAutoCommit(1);

                    // Return immediately with redundant episode number indicator
                    return $episodeId;
                }
            }
*/
            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Insertion of production failed : error during begin",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Retrieve gender from name
            $genderId = "";
            if ($details[self::GENDER_NAME] == "")
            {
                $genderId = "NULL";
            }
            else
            {
                $sql = "SELECT id FROM gender WHERE name=" . $this->quote($details[self::GENDER_NAME]);
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus()==0)
                {
                    switch($query->getCount())
                    {
                        case 0:

                            // Insert gender
                            $sql = "INSERT INTO gender(name) VALUES (" . $this->quote($details[self::GENDER_NAME]) . ")";
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->exec($query);
                            if ($query->getStatus() != 0)
                            {
                                throw new ILARIA_CoreError("Insertion of production failed : unable to create gender " . $details[self::GENDER_NAME],
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }

                            // Gather back gender ID
                            $genderId = $this->getDatabase()->getLastInsertId();
                            if ($genderId <= 0)
                            {
                                throw new ILARIA_CoreError("Insertion of production failed : gathered back new ID " . $genderId . " for new gender " . $details[self::GENDER_NAME],
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }

                            break;

                        case 1:

                            // Gather gender ID
                            $genderId = $query->getData()[0]['id'];

                            break;

                        default:
                            throw new ILARIA_CoreError("Insertion of production failed : " . $query->getCount() . " genders found, expected 0 or 1",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("Insertion of production failed : error while accessing genders table",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Insert new production
            $sql = "INSERT INTO production(id, year, title_id, gender_id) VALUES ("
                . $details[self::PRODUCTION_ID] . ","
                . $this->quoteOrNull($details[self::PRODUCTION_YEAR]) . ","
                . "NULL" . ","
                . $genderId . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of production failed : unable to insert the production",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            $productionId = $details[self::PRODUCTION_ID];

            // Grab main title and insert it
            $sql = "INSERT INTO title(title, production_id) VALUES ("
                . $this->quote($details[self::TITLE_TITLE]) . ","
                . $productionId . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of production failed : unable to insert main title",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            $mainTitleId = $this->getDatabase()->getLastInsertId();

            // Update production to accomodate main title
            $sql = "UPDATE production SET title_id=" . $mainTitleId . " WHERE id=" . $productionId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of production failed : unable to link production to its main title",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // ISA hierarchy according to kind of production
            switch ($details[self::KIND_NAME])
            {
                // Production is a TV serie
                case "tv series":

                    // Insert into series table
                    $sql = "INSERT INTO serie(id, yearstart, yearend) VALUES ("
                        . $productionId . ","
                        . $this->quoteOrNull($details[self::SERIE_YEARSTART]) . ","
                        . $this->quoteOrNull($details[self::SERIE_YEAREND]) . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Insertion of production failed : unable to add child row in serie",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }

                    break;

                // Production is an episode of a TV serie
                case "episode":

                    // Gather season ID
                    $seasonId = "";
                    if ($details[self::SEASON_NUMBER] == "NULL")
                    {
                        $sql = "SELECT id FROM season WHERE serie_id=" . $details[self::SEASON_SERIE_ID] . " AND number IS NULL";
                    }
                    else
                    {
                        $sql = "SELECT id FROM season WHERE serie_id=" . $details[self::SEASON_SERIE_ID] . " AND number=" . $details[self::SEASON_NUMBER];
                    }
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->query($query);
                    if ($query->getStatus() == 0)
                    {
                        switch($query->getCount())
                        {
                            case 0:

                                // Insert season
                                $sql = "INSERT INTO season(number, serie_id) VALUES ("
                                    . $details[self::SEASON_NUMBER] . ","
                                    . $details[self::SEASON_SERIE_ID] . ")";
                                $query = new ILARIA_DatabaseQuery($sql);
                                $this->getDatabase()->exec($query);
                                if ($query->getStatus() != 0)
                                {
                                    throw new ILARIA_CoreError("Insertion of production failed : unable to create season " . $details[self::SEASON_NUMBER],
                                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                        ILARIA_CoreError::LEVEL_SERVER);
                                }

                                // Gather back season ID
                                $seasonId = $this->getDatabase()->getLastInsertId();
                                if ($seasonId <= 0)
                                {
                                    throw new ILARIA_CoreError("Insertion of production failed : gathered back new ID " . $seasonId . " for new season " . $details[self::SEASON_NUMBER],
                                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                        ILARIA_CoreError::LEVEL_SERVER);
                                }

                                break;
                            case 1:

                                // Gather gender ID
                                $seasonId = $query->getData()[0]['id'];

                                break;
                            default:
                                throw new ILARIA_CoreError("Insertion of production failed : " . $query->getCount() . " seasons found, expected 0 or 1",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                        }
                    }
                    else
                    {
                        throw new ILARIA_CoreError("Insertion of production failed : error while accessing seasons table",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }

                    // Insert into episode table
                    $sql = "INSERT INTO episode(id, number, season_id) VALUES ("
                        . $productionId . ","
                        . $details[self::EPISODE_NUMBER] . ","
                        . $seasonId . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        if ($query->getStatus() == 23000)
                        {
                            // Rollback previous transaction
                            if (!$this->getDatabase()->transactionRollback())
                            {
                                throw new ILARIA_CoreError("Insertion of production failed : unable to rollback on duplicate episode number",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }

                            // Re-enable autocommit
                            $this->getDatabase()->transactionSetAutoCommit(1);

                            // Find episode for which we are duplicating entry
                            $sql = "SELECT id FROM episode WHERE number=" . $details[self::EPISODE_NUMBER] . " AND season_id=" . $seasonId;
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->query($query);
                            if (($query->getStatus() == 0) && ($query->getCount() == 1))
                            {
                                // Gather original episode id
                                $episodeId = $query->getData()[0]['id'];

                                // Add alternative title for this episode
                                if ($this->importTitle(array(
                                    self::TITLE_PRODUCTION_ID => $episodeId,
                                    self::TITLE_TITLE => $details[self::TITLE_TITLE],
                                )) == self::IMPORT_STATUS_ERROR)
                                {
                                    throw new ILARIA_CoreError("Insertion of production failed : unable to import alternative title for original episode " . $episodeId,
                                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                        ILARIA_CoreError::LEVEL_SERVER);
                                }
                            }
                            else
                            {
                                throw new ILARIA_CoreError("Insertion of production failed : error while accessing episodes table to find duplicate entry",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }

                            // Return immediately with redundant episode number indicator
                            return $episodeId;
                        }
                        else
                        {
                            throw new ILARIA_CoreError("Insertion of production failed : unable to add child row in episode",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }
                    }

                    break;

                // Production is a single production
                default:

                    // Gather kind ID
                    $kindId = "";
                    $sql = "SELECT id FROM kind WHERE name=" . $this->quote($details[self::KIND_NAME]);
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->query($query);
                    if ($query->getStatus() == 0)
                    {
                        switch ($query->getCount())
                        {
                            case 0:

                                // Insert kind
                                $sql = "INSERT INTO kind(name) VALUES ("
                                    . $this->quote($details[self::KIND_NAME]) . ")";
                                $query = new ILARIA_DatabaseQuery($sql);
                                $this->getDatabase()->exec($query);
                                if ($query->getStatus() != 0)
                                {
                                    throw new ILARIA_CoreError("Insertion of production failed : unable to create kind " . $details[self::KIND_NAME],
                                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                        ILARIA_CoreError::LEVEL_SERVER);
                                }

                                // Gather back kind ID
                                $kindId = $this->getDatabase()->getLastInsertId();
                                if ($kindId <= 0)
                                {
                                    throw new ILARIA_CoreError("Insertion of production failed : gathered back new ID " . $kindId . " for new kind " . $details[self::KIND_NAME],
                                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                        ILARIA_CoreError::LEVEL_SERVER);
                                }

                                break;
                            case 1:

                                // Gather kind ID
                                $kindId = $query->getData()[0]['id'];

                                break;
                            default:
                                throw new ILARIA_CoreError("Insertion of production failed : " . $query->getCount() . " kinds found, expected 0 or 1",
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                        }
                    }
                    else
                    {
                        throw new ILARIA_CoreError("Insertion of production failed : error while accessing kinds table",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }

                    // Insert into singleproduction table
                    $sql = "INSERT INTO singleproduction(id, kind_id) VALUES ("
                        . $productionId . ","
                        . $kindId . ")";
                    $query = new ILARIA_DatabaseQuery($sql);
                    $this->getDatabase()->exec($query);
                    if ($query->getStatus() != 0)
                    {
                        throw new ILARIA_CoreError("Insertion of production failed : unable to add child row in singleproduction",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }

                    break;
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Insertion of production failed : error during commit",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Validate successful transaction
            $result = 0;
        }

        catch (ILARIA_CoreError $e)
        {
            // Write error
            $e->writeToLog();

            // Rollback the transaction
            $this->getDatabase()->transactionRollback();

            // Delete the inserted production
            if ($productionId > 0)
            {
                $this->deleteById($productionId);
            }

            // Invalidate transaction
            $result = self::IMPORT_STATUS_ERROR;
        }

        // Re-enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        return $result;
    }

    // Import an alternative title
    public function importTitle($details)
    {
        // Gather result (0 = insertion OK)
        $result = 0;

        try
        {
            // Insert title
            $sql = "INSERT INTO title(title, production_id) VALUES ("
                . $this->quote($details[self::TITLE_TITLE]) . ","
                . $details[self::TITLE_PRODUCTION_ID] . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                if ($query->getStatus() == 23000)
                {
                    return self::IMPORT_STATUS_REDUNDANT_TITLE;
                }
                throw new ILARIA_CoreError("Insertion of title failed : INSERT query generated an error",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Validate transaction
            $result = 0;
        }

        catch (ILARIA_CoreError $e)
        {
            // Write error
            $e->writeToLog();

            // Invalidate transaction
            $result = self::IMPORT_STATUS_ERROR;
        }

        return $result;
    }

    // Delete a production by its ID
    public function deleteById($id)
    {
        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        // Gather result (true = deletion OK)
        $result = false;

        try
        {
            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Deletion of production failed : error during begin",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Disable foreign key checks
            $query = new ILARIA_DatabaseQuery("SET FOREIGN_KEY_CHECKS=0");
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of production failed : impossible to disable referential integrity",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Delete from title
            $sql = "DELETE FROM title WHERE production_id=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of production failed : impossible to delete titles with production_id " . $id,
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Re-enable foreign key checks
            $query = new ILARIA_DatabaseQuery("SET FOREIGN_KEY_CHECKS=1");
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of production failed : impossible to re-enable referential integrity",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Delete from production
            $sql = "DELETE FROM production WHERE id=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of production failed : impossible to delete production with id " . $id,
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Deletion of production failed : error during commit",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Validate successful transaction
            $result = true;
        }

        catch (ILARIA_CoreError $e)
        {
            // Write error
            $e->writeToLog();

            // Rollback the transaction
            $this->getDatabase()->transactionRollback();

            // Invalidate transaction
            $result = false;
        }

        // Re-enable foreign key checks
        $query = new ILARIA_DatabaseQuery("SET FOREIGN_KEY_CHECKS=1");
        $this->getDatabase()->exec($query);
        if ($query->getStatus() != 0)
        {
            return false;
        }

        // Enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        return $result;
    }
}