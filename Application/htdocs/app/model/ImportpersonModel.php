<?php

class ImportpersonModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // Importation protection
    private $isImportAuthorized = false;

    // Fields description for table "person"
    const PERSON_ID = 'id';
    const PERSON_GENDER = 'gender';
    const PERSON_TRIVIA = 'trivia';
    const PERSON_QUOTES = 'quotes';
    const PERSON_BIRTHDATE = 'birthdate';
    const PERSON_DEATHDATE = 'deathdate';
    const PERSON_BIRTHNAME = 'birthname';
    const PERSON_MINIBIOGRAPHY = 'minibiography';
    const PERSON_SPOUSE = 'spouse';
    const PERSON_HEIGHT = 'height';
    const PERSON_NAME_ID = 'name_id';

    // Fields description for table "name"
    const NAME_ID = 'id';
    const NAME_FIRSTNAME = 'firstname';
    const NAME_LASTNAME = 'lastname';
    const NAME_PERSON_ID = 'person_id';

    // Array identifiers
    const ARRAY_NAMES = 'names';

    // Authorize importation of persons
    public function authorizeImport()
    {
        if (!$this->isImportAuthorized)
        {
            $query = new ILARIA_DatabaseQuery("ALTER TABLE person CHANGE COLUMN name_id name_id INT UNSIGNED NULL");
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                return false;
            }
            $this->isImportAuthorized = true;
        }
        return true;
    }

    // Prohibit importation of persons
    public function prohibitImport()
    {
        if ($this->isImportAuthorized)
        {
            $query = new ILARIA_DatabaseQuery("ALTER TABLE person CHANGE COLUMN name_id name_id INT UNSIGNED NOT NULL");
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                return false;
            }
            $this->isImportAuthorized = false;
        }
        return true;
    }

    // Import a person with all its names
    public function importPerson($details)
    {
        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        // Gather result (true = insertion OK)
        $result = false;

        try
        {
            // Check for authorization
            if (!$this->isImportAuthorized)
            {
                throw new ILARIA_CoreError("Insertion of person failed : import not authorized",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Prepare person ID
            $personId = 0;

            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Insertion of person failed : error during begin",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Insert person, no pointer at main name yet
            $sql = "INSERT INTO person(id, gender, trivia, quotes, birthdate, deathdate, birthname, minibiography, spouse, height, name_id) VALUES ("
                . $details[self::PERSON_ID] . ","
                . $this->quoteOrNull($details[self::PERSON_GENDER]) . ","
                . $this->quoteOrNull($details[self::PERSON_TRIVIA]) . ","
                . $this->quoteOrNull($details[self::PERSON_QUOTES]) . ","
                . $this->quoteOrNull($details[self::PERSON_BIRTHDATE]) . ","
                . $this->quoteOrNull($details[self::PERSON_DEATHDATE]) . ","
                . $this->quoteOrNull($details[self::PERSON_BIRTHNAME]) . ","
                . $this->quoteOrNull($details[self::PERSON_MINIBIOGRAPHY]) . ","
                . $this->quoteOrNull($details[self::PERSON_SPOUSE]) . ","
                . $details[self::PERSON_HEIGHT] . ","
                . "NULL" . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of person failed : unable to insert the person",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            $personId = $details[self::PERSON_ID];

            // Grab main name and insert it
            $sql = "INSERT INTO name(firstname, lastname, person_id) VALUES ("
                . $this->quoteOrNull($details[self::PERSON_NAME_ID][self::NAME_FIRSTNAME]) . ","
                . $this->quote($details[self::PERSON_NAME_ID][self::NAME_LASTNAME]) . ","
                . $personId . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of person failed : unable to insert main name",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            $mainNameId = $this->getDatabase()->getLastInsertId();

            // Update person to accomodate main name
            $sql = "UPDATE person SET name_id=" . $mainNameId . " WHERE id=" . $personId;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of person failed : unable to link person to its main name",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Add all secondary names if specified
            if (isset($details[self::ARRAY_NAMES]))
            {
                foreach ($details[self::ARRAY_NAMES] as $name)
                {
                    if (!$this->importName(array(
                        self::NAME_PERSON_ID => $personId,
                        self::NAME_FIRSTNAME => $name[self::NAME_FIRSTNAME],
                        self::NAME_LASTNAME => $name[self::NAME_LASTNAME]
                    )))
                    {
                        throw new ILARIA_CoreError("Insertion of person failed : one secondary name could not be inserted",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                    }
                }
            }

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Insertion of person failed : error during commit",
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

            // Delete the inserted person
            if ($personId > 0)
            {
                $this->deleteById($personId);
            }

            // Invalidate transaction
            $result = false;
        }

        // Enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        return $result;
    }

    // Import an alternative name
    public function importName($details)
    {
        // Gather result (true = insertion OK)
        $result = false;

        try
        {
            // Insert name
            $sql = "INSERT INTO name(firstname, lastname, person_id) VALUES ("
                . $this->quoteOrNull($details[self::NAME_FIRSTNAME]) . ","
                . $this->quote($details[self::NAME_LASTNAME]) . ","
                . $details[self::NAME_PERSON_ID] . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of name failed : INSERT query generated an error",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Validate transaction
            $result = true;
        }

        catch(ILARIA_CoreError $e)
        {
            // Write error
            $e->writeToLog();

            // Invalidate transaction
            $result = false;
        }

        return $result;
    }

    // Delete a person by its ID
    public function deleteById($id)
    {
        // Gather result (true = insertion OK)
        $result = false;

        try
        {
            // Disable foreign key checks
            $query = new ILARIA_DatabaseQuery("SET FOREIGN_KEY_CHECKS=0");
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of person failed : impossible to disable referential integrity",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Delete person
            $query = new ILARIA_DatabaseQuery("DELETE FROM person WHERE id=" . $id);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of person failed : given id " . $id . " does not seem to exist",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Delete names
            $query = new ILARIA_DatabaseQuery("DELETE FROM name WHERE person_id=" . $id);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of person failed : a linked name failed to delete",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Validate transaction
            $result = true;
        }

        catch (ILARIA_CoreError $e)
        {
            // Write error
            $e->writeToLog();

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

        return $result;
    }
}