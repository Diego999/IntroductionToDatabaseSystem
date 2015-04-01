<?php

class ImportcastingModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // Fields description for table "casting"
    const CASTING_ID = 'casting_id';
    const CASTING_PERSON_ID = 'casting_person_id';
    const CASTING_PRODUCTION_ID = 'casting_production_id';
    const CASTING_ROLE_ID = 'casting_role_id';
    const CASTING_CHARACTER_ID = 'casting_character_id';

    // Fields description for table "character"
    const CHARACTER_ID = 'character_id';
    const CHARACTER_NAME = 'character_name';

    // Fields description for table "role"
    const ROLE_ID = 'role_id';
    const ROLE_NAME = 'role_name';

    // Import a character
    public function importCharacter($details)
    {
        try
        {
            // Check that character does not already exist
            $sql = "SELECT id FROM `character` WHERE name=" . $this->quote($details[self::CHARACTER_NAME]);
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus() == 0)
            {
                if ($query->getCount() == 1)
                {
                    // Return ID of character already existing with same name
                    return $query->getData()[0]['id'];
                }
            }
            else
            {
                // Indicate error
                throw new ILARIA_CoreError("Insertion of character failed : unable to check for pre-existence",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Insert character
            $sql = "INSERT INTO `character` (id, name) VALUES ("
                . $details[self::CHARACTER_ID] . ","
                . $this->quote($details[self::CHARACTER_NAME]) . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of character failed : INSERT query returned an error",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Return success
            return 0;
        }

        catch (ILARIA_CoreError $e)
        {
            // Write error
            $e->writeToLog();

            // Invalidate transaction
            return -1;
        }
    }

    // Import a casting
    public function importCasting($details)
    {
        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        // Gather result (true = insertion OK)
        $result = false;

        try
        {
            // Prepare casting ID
            $castingId = 0;

            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Insertion of casting failed : error during begin",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Retrieve role from name
            $roleId = "";
            $sql = "SELECT id FROM role WHERE name=" . $this->quote($details[self::ROLE_NAME]);
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus()==0)
            {
                switch ($query->getCount())
                {
                    case 0:

                        // Insert role
                        $sql = "INSERT INTO role(name) VALUES (" . $this->quote($details[self::ROLE_NAME]) . ")";
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->exec($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Insertion of casting failed : unable to create role " . $details[self::ROLE_NAME],
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }

                        // Gather back role ID
                        $roleId = $this->getDatabase()->getLastInsertId();
                        if ($roleId <= 0)
                        {
                            throw new ILARIA_CoreError("Insertion of casting failed : gathered back new ID " . $roleId . " for new role " . $details[self::ROLE_NAME],
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }

                        break;

                    case 1:

                        // Gather role ID
                        $roleId = $query->getData()[0]['id'];

                        break;

                    default:
                        throw new ILARIA_CoreError("Insertion of casting failed : " . $query->getCount() . " roles found, expected 0 or 1",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                }
            }
            else
            {
                throw new ILARIA_CoreError("Insertion of casting failed : error while accessing roles table",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Insert new casting
            $sql = "INSERT INTO casting(person_id, production_id, role_id, character_id) VALUES ("
                . $details[self::CASTING_PERSON_ID] . ","
                . $details[self::CASTING_PRODUCTION_ID] . ","
                . $roleId . ","
                . $details[self::CASTING_CHARACTER_ID] . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of casting failed : unable to insert the casting",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            $castingId = $this->getDatabase()->getLastInsertId();

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Insertion of casting failed : error during commit",
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

            // Delete the inserted production
            if ($castingId > 0)
            {
                $this->deleteById($castingId);
            }

            // Invalidate transaction
            $result = false;
        }

        // Re-enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        return $result;
    }

    // Delete a casting by its ID
    public function deleteById($id)
    {
        // Gather result (true = insertion OK)
        $result = false;

        try
        {
            // Delete casting
            $query = new ILARIA_DatabaseQuery("DELETE FROM casting WHERE id=" . $id);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of casting failed : given id " . $id . " does not seem to exist",
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

        return $result;
    }
}