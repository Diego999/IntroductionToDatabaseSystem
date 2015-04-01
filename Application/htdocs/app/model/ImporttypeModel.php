<?php

class ImporttypeModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // Fields description for table "productioncompany"
    const PRODUCTIONCOMPANY_ID = 'productioncompany_id';
    const PRODUCTIONCOMPANY_PRODUCTION_ID = 'productioncompany_production_id';
    const PRODUCTIONCOMPANY_COMPANY_ID = 'productioncompany_company_id';
    const PRODUCTIONCOMPANY_TYPE_ID = 'productioncompany_type_id';

    // Fields description for table "type"
    const TYPE_ID = 'type_id';
    const TYPE_NAME = 'type_name';

    // Import a productioncompany
    public function ImportLink($details)
    {
        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        // Gather result (true = insertion OK)
        $result = false;

        try
        {
            // Prepare general ID
            $generalId = 0;

            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Insertion of productioncompany failed : error during begin",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Retrieve type from name
            $typeId = "";
            $sql = "SELECT id FROM `type` WHERE name=" . $this->quote($details[self::TYPE_NAME]);
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->query($query);
            if ($query->getStatus()==0)
            {
                switch ($query->getCount())
                {
                    case 0:

                        // Insert type
                        $sql = "INSERT INTO `type` (name) VALUES (" . $this->quote($details[self::TYPE_NAME]) . ")";
                        $query = new ILARIA_DatabaseQuery($sql);
                        $this->getDatabase()->exec($query);
                        if ($query->getStatus() != 0)
                        {
                            throw new ILARIA_CoreError("Insertion of productioncompany failed : unable to create type " . $details[self::TYPE_NAME],
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }

                        // Gather back type ID
                        $typeId = $this->getDatabase()->getLastInsertId();
                        if ($typeId <= 0)
                        {
                            throw new ILARIA_CoreError("Insertion of productioncompany failed : gathered back new ID " . $typeId . " for new type " . $details[self::TYPE_NAME],
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                        }

                        break;

                    case 1:

                        // Gathered type ID
                        $typeId = $query->getData()[0]['id'];

                        break;

                    default:
                        throw new ILARIA_CoreError("Insertion of productioncompany failed : " . $query->getCount() . " types found, expected 0 or 1",
                            ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                            ILARIA_CoreError::LEVEL_SERVER);
                }
            }
            else
            {
                throw new ILARIA_CoreError("Insertion of productioncompany failed : error while accessings types table",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Insert new productioncompany
            $sql = "INSERT INTO productioncompany(id, production_id, company_id, type_id) VALUES ("
                . $details[self::PRODUCTIONCOMPANY_ID] . ","
                . $details[self::PRODUCTIONCOMPANY_PRODUCTION_ID] . ","
                . $details[self::PRODUCTIONCOMPANY_COMPANY_ID] . ","
                . $typeId . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of productioncompany failed : unable to insert the row",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            $generalId = $details[self::PRODUCTIONCOMPANY_ID];

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Insertion of productioncompany failed : error during commit",
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
            if ($generalId > 0)
            {
                $this->deleteById($generalId);
            }

            // Invalidate transaction
            $result = false;
        }

        return $result;
    }

    // Delete a productioncompany by its ID
    public function deleteById($id)
    {
        // Gather result (true = deletion OK)
        $result = false;

        try
        {
            // Delete productioncompany
            $sql = "DELETE FROM productioncompany WHERE id=" . $id;
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of productioncompany failed : id " . $id . " does not seem to exist",
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

            // Invalidate transaction
            $result = false;
        }

        return $result;
    }
}