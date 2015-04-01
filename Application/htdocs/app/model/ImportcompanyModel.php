<?php

class ImportcompanyModel extends ILARIA_ApplicationModel
{
    protected function getDbIdentifier() { return 'app'; }

    protected function getDbModule() { return 'Mysql'; }

    protected function getDbConnectionSettings() { return ILARIA_ModuleMysql::getConnectionSettings(); }

    // Fields description for table "company"
    const COMPANY_ID = 'id';
    const COMPANY_NAME = 'name';
    const COMPANY_COUNTRY_ID = 'country_id';

    // Fields description for table "country"
    const COUNTRY_ID = 'id';
    const COUNTRY_CODE = 'code';

    // Import a company
    public function importCompany($details)
    {
        // Disable autocommit
        $this->getDatabase()->transactionSetAutoCommit(0);

        // Gather result (true = insertion OK)
        $result = false;

        try
        {
            // Prepare company ID
            $companyId = 0;

            // Start the transaction
            if (!$this->getDatabase()->transactionBegin())
            {
                throw new ILARIA_CoreError("Insertion of company failed : error during begin",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }

            // Search for country
            $countryId = "";
            if ($details[self::COUNTRY_CODE] == "")
            {
                $countryId = "NULL";
            }
            else
            {
                $sql = "SELECT id FROM country WHERE code='" . $details[self::COUNTRY_CODE] . "'";
                $query = new ILARIA_DatabaseQuery($sql);
                $this->getDatabase()->query($query);
                if ($query->getStatus()==0)
                {
                    switch($query->getCount())
                    {
                        case 0:

                            // Insert country
                            $sql = "INSERT INTO country(code) VALUES (" . $this->quote($details[self::COUNTRY_CODE]) . ")";
                            $query = new ILARIA_DatabaseQuery($sql);
                            $this->getDatabase()->exec($query);
                            if ($query->getStatus() != 0)
                            {
                                throw new ILARIA_CoreError("Insertion of company failed : unable to create country " . $details[self::COUNTRY_CODE],
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }

                            // Gather back country ID
                            $countryId = $this->getDatabase()->getLastInsertId();
                            if ($countryId <= 0)
                            {
                                throw new ILARIA_CoreError("Insertion of company failed : gathered back new ID " . $countryId . " for new country " . $details[self::COUNTRY_CODE],
                                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                    ILARIA_CoreError::LEVEL_SERVER);
                            }

                            break;

                        case 1:

                            // Gather country ID
                            $countryId = $query->getData()[0]['id'];

                            break;
                        default:
                            throw new ILARIA_CoreError("Insertion of company failed : " . $query->getCount() . " coutries found, expected 0 or 1",
                                ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                                ILARIA_CoreError::LEVEL_SERVER);
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("Insertion of company failed : error while accessing countries table",
                        ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                        ILARIA_CoreError::LEVEL_SERVER);
                }
            }

            // Insert new company
            $sql = "INSERT INTO company(id, name, country_id) VALUES ("
                . $details[self::COMPANY_ID] . ","
                . $this->quote($details[self::COMPANY_NAME]) . ","
                . $countryId . ")";
            $query = new ILARIA_DatabaseQuery($sql);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Insertion of company failed : unable to insert the company",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_SERVER);
            }
            $companyId = $details[self::COMPANY_ID];

            // Commit the transaction
            if (!$this->getDatabase()->transactionCommit())
            {
                throw new ILARIA_CoreError("Insertion of company failed : error during commit",
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

            // Delete the inserted company
            if ($companyId > 0)
            {
                $this->deleteById($companyId);
            }

            // Invalidate transaction
            $result = false;
        }

        // Enable autocommit
        $this->getDatabase()->transactionSetAutoCommit(1);

        return $result;
    }

    // Delete a company
    public function deleteById($id)
    {
        // Gather result
        $result = false;

        try
        {
            // Delete company
            $query = new ILARIA_DatabaseQuery("DELETE FROM company WHERE id=" . $id);
            $this->getDatabase()->exec($query);
            if ($query->getStatus() != 0)
            {
                throw new ILARIA_CoreError("Deletion of company failed : given id " . $id . " does not seem to exist",
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