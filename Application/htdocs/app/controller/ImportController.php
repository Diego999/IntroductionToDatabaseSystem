<?php

class ImportController extends ILARIA_ApplicationController
{
    public function isAuthorized($actionName, $userToken)
    {
        switch ($actionName)
        {
            case 'index':
                return true;
            default:
                return false;
        }
    }

    public function action_index($request)
    {
        // Create view
        $view = $this->getView('importindex');

        // Define template
        $view->setTemplateName('import');

        // Importation
        //$output = $this->importPersons();
        //$output = $this->importAlternativeNames();
        //$output = $this->importCompanies();
        //$output = $this->importProductions();
        //$output = $this->importAlternativeTitles();
        //$output = $this->importCharacters();
        //$output = $this->importCastings();
        //$output = $this->importProdComps();
        $output = array("Nothing to do");

        // Output to view
        $view->prepare($output);

        // Return view
        return $view;
    }

    private function importPersons()
    {
        // Result array
        $result = array();

        // Create model
        $model = $this->getModel('importperson');

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'PERSON.csv', "r");
        if ($handle)
        {
            // Authorize imports on database
            $model->authorizeImport();

            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 11)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 11 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Gather numeric ID
                if (!is_numeric($data[0]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, ID " . $data[0] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }
                $personId = intval($data[0]);

                // Gather name
                $personName = NULL;
                try
                {
                    $personName = $this->nameStrToSql($data[1]);
                    if (!is_array($personName))
                    {
                        throw new ILARIA_CoreError("The name \"" . $data[1] . "\" does not generate an array",
                            ILARIA_CoreError::GEN_PARSE_NAME_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                }
                catch (ILARIA_CoreError $e)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, name could not be converted";
                    $result[] = " [ILARIA_CoreError] " . $e->getMsg();
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Gather gender
                $personGender = '';
                switch ($data[2])
                {
                    case "m":
                        $personGender = "m";
                        break;
                    case "f":
                        $personGender = "f";
                        break;
                    case "\\N":
                        $personGender = "";
                        break;
                    default:
                        $result[] = "ERROR: at line " . $importedLines . " of file, gender " . $data[1] . " not accepted";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        continue;
                }

                // Gather trivia
                $personTrivia = ($data[3] == "\\N" ? "" : $data[3]);

                // Gather quotes
                $personQuotes = ($data[4] == "\\N" ? "" : $data[4]);

                // Gather birthdate
                $personBirthdate = "";
                try
                {
                    $personBirthdate = $this->dateStrToSql($data[5]);
                }
                catch (ILARIA_CoreError $e)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, birthdate could not be converted";
                    $result[] = " [ILARIA_CoreError] " . $e->getMsg();
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Gather deathdate
                $personDeathdate = "";
                try
                {
                    $personDeathdate = $this->dateStrToSql($data[6]);
                }
                catch (ILARIA_CoreError $e)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, deathdate could not be converted";
                    $result[] = " [ILARIA_CoreError] " . $e->getMsg();
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Gather birthname
                $personBirthname = ($data[7] == "\\N" ? "" : $data[7]);

                // Gather minibiography
                $personMinibiography = ($data[8] == "\\N" ? "" : $data[8]);

                // Gather spouse
                $personSpouse = ($data[9] == "\\N" ? "" : $data[9]);
                if (strlen($personSpouse) > 255)
                {
                    $result[] = "WARNING: at line " . $importedLines . " of file, spouse is truncated";
                    $result[] = " [ImportController] truncated part : " . substr($personSpouse, 255, 1000);
                    $personSpouse = substr($personSpouse, 0, 255);
                    $warningLines++;
                }

                // Gather height
                $personHeight = "";
                try
                {
                    $personHeight = $this->sizeStrToSql($data[10]);
                }
                catch (ILARIA_CoreError $e)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, height could not be converted";
                    $result[] = " [ILARIA_CoreError] " . $e->getMsg();
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Insert into database
                if (!$model->importPerson(array(
                    PersonModel::PERSON_ID => $personId,
                    PersonModel::PERSON_GENDER => $personGender,
                    PersonModel::PERSON_TRIVIA => $personTrivia,
                    PersonModel::PERSON_QUOTES => $personQuotes,
                    PersonModel::PERSON_BIRTHDATE => $personBirthdate,
                    PersonModel::PERSON_DEATHDATE => $personDeathdate,
                    PersonModel::PERSON_BIRTHNAME => $personBirthname,
                    PersonModel::PERSON_MINIBIOGRAPHY => $personMinibiography,
                    PersonModel::PERSON_SPOUSE => $personSpouse,
                    PersonModel::PERSON_HEIGHT => $personHeight,
                    PersonModel::PERSON_NAME_ID => $personName,
                )))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, importation into database failed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file PERSON.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);

            // Prohibit imports on database
            if (!$model->prohibitImport())
            {
                $result[] = "ERROR: global importation failed : unable to re-enable referential integrity";
                $result[] = " [ImportController] you MUST flush the database to clean partial data before re-trying";
                $errorLines = $importedLines;
            }

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file PERSON.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
    }

    private function importAlternativeNames()
    {
        // result array
        $result = array();

        // Create model
        $model = $this->getModel('importperson');

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'ALTERNATIVE_NAME.csv', "r");
        if ($handle)
        {
            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 3)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 3 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Gather ID of person
                if (!is_numeric($data[1]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, person ID " . $data[1] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }
                $personId = intval($data[1]);

                // Gather name of person
                $personName = NULL;
                try
                {
                    $personName = $this->nameStrToSql($data[2]);
                    if (!is_array($personName))
                    {
                        throw new ILARIA_CoreError("The name \"" . $data[2] . "\" does not generate an array",
                            ILARIA_CoreError::GEN_PARSE_NAME_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                }
                catch (ILARIA_CoreError $e)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, name could not be converted";
                    $result[] = " [ILARIA_CoreError] " . $e->getMsg();
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Insert into database
                if (!$model->importName(array(
                    PersonModel::NAME_FIRSTNAME => $personName['firstname'],
                    PersonModel::NAME_LASTNAME => $personName['lastname'],
                    PersonModel::NAME_PERSON_ID => $personId,
                )))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, importation into database failed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file ALTERNATIVE_NAME.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file ALTERNATIVE_NAME.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
    }

    private function importCompanies()
    {
        // result array
        $result = array();

        // Create model
        $model = $this->getModel('importcompany');

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'COMPANY.csv', "r");
        if ($handle)
        {
            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 3)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 3 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Gather ID of company
                if (!is_numeric($data[0]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, company ID " . $data[0] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }
                $companyId = intval($data[0]);

                // Gather country code
                $countryCode = "";
                if (trim($data[1]) != "\\N")
                {
                    if ((substr(trim($data[1]), 0, 1) != "[")
                        || (substr(trim($data[1]), -1) != "]"))
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, incorrect format for country \"" . trim($data[1]) . "\"";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        continue;
                    }

                    $countryCode = substr(trim($data[1]), 1, strlen(trim($data[1]))-2);
                }

                // Gather name of company
                $companyName = trim($data[2]);
                if (strlen($companyName) == 0)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, name is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Insert into database
                if (!$model->importCompany(array(
                    CompanyModel::COMPANY_ID => $companyId,
                    CompanyModel::COMPANY_NAME => $companyName,
                    CompanyModel::COUNTRY_CODE => $countryCode,
                )))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, importation into database failed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file COMPANY.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file COMPANY.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
    }

    private function importProductions()
    {
        $result = array();

        // Create model
        $model = $this->getModel('importproduction');

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'PRODUCTION_STAGE1.csv', "r");
        if ($handle)
        {
            // Create error lines file
            $errHandle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . "PRODUCTION_ERRORS.csv", "w");
            if (!$errHandle)
            {
                fclose($handle);
                return array("unable to create dump file for error lines");
            }

            // Create duplicate entries reference file
            $duplicateHandle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . "PRODUCTION_DUPLICATE_REF.csv", "w");
            if (!$duplicateHandle)
            {
                fclose($handle);
                return array("unable to create duplicate reference file for productions");
            }

            // Authorize imports on database
            $model->authorizeImport();

            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 9)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 9 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Trim all data
                for ($i=0; $i<9; $i++)
                {
                    $data[$i] = trim($data[$i]);
                }

                // Gather ID of production
                if (!is_numeric($data[0]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, production ID " . $data[0] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                $productionId = intval($data[0]);

                // Gather title of production
                $productionTitle = $data[1];
                if (strlen($productionTitle) == 0)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, title is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                if (strlen($productionTitle) > 255)
                {
                    $result[] = "WARNING: at line " . $importedLines . " of file, title has length " . strlen($productionTitle) . ", truncated to 255";
                    $warningLines++;
                    $productionTitle = substr($productionTitle, 0, 255);
                }

                // Gather production year
                $productionYear = "";
                if ($data[2] != "\\N")
                {
                    if (strlen($data[2]) > 4)
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, production year has more than 4 positions";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }

                    if (!is_numeric($data[2]))
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, production year is not numeric";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }

                    $productionYear = intval($data[2]);
                }

                // Gather serie ID
                $serieId = "";
                if ($data[3] != "\\N")
                {
                    if (!is_numeric($data[3]))
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, serie ID " . $data[3] . " is not numeric";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }

                    $serieId = intval($data[3]);
                }

                // Gather season number
                $seasonNumber = "";
                if ($data[4] == "\\N")
                {
                    $seasonNumber = "NULL";
                }
                else
                {
                    if (!is_numeric($data[4]))
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, season number " . $data[4] . " is not numeric";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }

                    $seasonNumber = intval($data[4]);
                }

                // Gather episode number
                $episodeNumber = "";
                if ($data[5] == "\\N")
                {
                    $episodeNumber = "NULL";
                }
                else
                {
                    if (!is_numeric($data[5]))
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, episode number " . $data[5] . " is not numeric";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }

                    $episodeNumber = intval($data[5]);
                }

                // Gather year start and year end
                $yearRange = NULL;
                try
                {
                    $yearRange = $this->yearRangeStrToSql($data[6]);
                    if (!is_array($yearRange))
                    {
                        throw new ILARIA_CoreError("The year range \"" . $data[6] . "\" does not generate an array",
                            ILARIA_CoreError::GEN_PARSE_NAME_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                }
                catch (ILARIA_CoreError $e)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, year range could not be converted";
                    $result[] = " [ILARIA_CoreError] " . $e->getMsg();
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Gather kind name
                $kindName = "";
                if ($data[7] == "\\N" || $data[7] == "")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, empty kind is not allowed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                else
                {
                    $kindName = $data[7];
                }

                // Gather gender name
                $genderName = "";
                if ($data[8] != "\\N")
                {
                    $genderName = $data[8];
                }

                // Insert into database
                $returnedValue = $model->importProduction(array(
                    ProductionModel::PRODUCTION_ID => $productionId,
                    ProductionModel::TITLE_TITLE => $productionTitle,
                    ProductionModel::PRODUCTION_YEAR => $productionYear,
                    ProductionModel::SEASON_SERIE_ID => $serieId,
                    ProductionModel::SEASON_NUMBER => $seasonNumber,
                    ProductionModel::EPISODE_NUMBER => $episodeNumber,
                    ProductionModel::SERIE_YEARSTART => $yearRange[ProductionModel::SERIE_YEARSTART],
                    ProductionModel::SERIE_YEAREND => $yearRange[ProductionModel::SERIE_YEAREND],
                    ProductionModel::KIND_NAME => $kindName,
                    ProductionModel::GENDER_NAME => $genderName,
                ));

                // Insertion OK
                if ($returnedValue == 0)
                {
                    // nothing
                }

                // Duplicate episode number, added as alternative title
                else if ($returnedValue > 0)
                {
                    fwrite($duplicateHandle, $productionId . "\t" . $returnedValue . "\n");
                    $result[] = "WARNING: at line " . $importedLines . " of file, episode imported as alternative title";
                    $warningLines++;
                }

                // Error
                else
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, importation into database failed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file PRODUCTION.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);
            fclose($errHandle);
            fclose($duplicateHandle);

            // Prohibit imports on database
            if (!$model->prohibitImport())
            {
                $result[] = "ERROR: global importation failed : unable to re-enable referential integrity";
                $result[] = " [ImportController] you MUST flush the database to clean partial data before re-trying";
                $errorLines = $importedLines;
            }

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file PRODUCTION.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
    }

    private function importAlternativeTitles()
    {
        // result array
        $result = array();

        // Create model
        $model = $this->getModel('importproduction');

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'ALTERNATIVE_TITLE.csv', "r");
        if ($handle)
        {
            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 3)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 3 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Trim all data
                for ($i=0; $i<3; $i++)
                {
                    $data[$i] = trim($data[$i]);
                }

                // Gather ID of production
                if (!is_numeric($data[1]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, production ID " . $data[1] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }
                $productionId = intval($data[1]);

                // Gather title of production
                $productionTitle = $data[2];
                if (strlen($productionTitle) == 0)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, title is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }
                if (strlen($productionTitle) > 255)
                {
                    $result[] = "WARNING: at line " . $importedLines . " of file, title has length " . strlen($productionTitle) . ", truncated to 255";
                    $warningLines++;
                    $productionTitle = substr($productionTitle, 0, 255);
                }

                // Insert into database
                switch ($model->importTitle(array(
                    ProductionModel::TITLE_PRODUCTION_ID => $productionId,
                    ProductionModel::TITLE_TITLE => $productionTitle,
                )))
                {
                    case 0:
                        break;

                    case ProductionModel::IMPORT_STATUS_REDUNDANT_TITLE:
                        $result[] = "WARNING: at line " . $importedLines . " of file, duplicate title for production " . $productionId . " has been dropped";
                        $warningLines++;
                        break;

                    case ProductionModel::IMPORT_STATUS_ERROR:
                        $result[] = "ERROR: at line " . $importedLines . " of file, importation into database failed";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        continue 2;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file ALTERNATIVE_TITLE.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file ALTERNATIVE_TITLE.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
    }

    private function importCharacters()
    {
        // result array
        $result = array();

        // Create model
        $model = $this->getModel('importcasting');

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'CHARACTER.csv', "r");
        if ($handle)
        {
            // Create duplicate entries reference file
            $duplicateHandle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . "CHARACTER_DUPLICATE_REF.csv", "w");
            if (!$duplicateHandle)
            {
                fclose($handle);
                return array("unable to create duplicate reference file for characters");
            }

            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 2)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 2 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Trim all data
                for ($i=0; $i<2; $i++)
                {
                    $data[$i] = trim($data[$i]);
                }

                // Gather ID of character
                if (!is_numeric($data[0]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, character ID " . $data[0] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }
                $characterId = intval($data[0]);

                // Gather name of character
                $characterName = $data[1];
                if (strlen($characterName) == 0)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, name is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }
                if (strlen($characterName) > 255)
                {
                    $result[] = "WARNING: at line " . $importedLines . " of file, name has length " . strlen($characterName) . ", truncated to 255";
                    $warningLines++;
                    $characterName = substr($characterName, 0, 255);
                }

                // Insert into database
                $returnedValue = $model->importCharacter(array(
                    CastingModel::CHARACTER_ID => $characterId,
                    CastingModel::CHARACTER_NAME => $characterName,
                ));

                // Insertion is OK, character has been added
                if ($returnedValue == 0)
                {
                    // nothing
                }

                // Insertion failed, unknown reason
                else if ($returnedValue < 0)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, importation into database failed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    continue;
                }

                // Insertion failed, duplicate entry to reference
                else
                {
                    fwrite($duplicateHandle, $characterId . "\t" . $returnedValue . "\n");
                    $result[] = "WARNING: at line " . $importedLines . " of file, character \"" . $characterName . "\" already exists with ID " . $returnedValue;
                    $result[] = "   => added to duplicate reference file";
                    $warningLines++;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file CHARACTER.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);
            fclose($duplicateHandle);

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file CHARACTER.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
    }

    private function importCastings()
    {
        // result array
        $result = array();

        // Create model
        $model = $this->getModel('importcasting');

        // Load characters duplicate ref mapping
        $duplicateCharactersMap = NULL;
        try
        {
            $duplicateCharactersMap = $this->loadCharacterDuplicateRefs();
            if (!is_array($duplicateCharactersMap))
            {
                throw new ILARIA_CoreError("The characters duplicate ref mapping is not an array",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            return array("ERROR: unable to load characters duplicate refs file");
        }

        // Load productions duplicate ref mapping
        $duplicateProductionsMap = NULL;
        try
        {
            $duplicateProductionsMap = $this->loadProductionDuplicateRefs();
            if (!is_array($duplicateProductionsMap))
            {
                throw new ILARIA_CoreError("The productions duplicate ref mapping is not an array",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            return array("ERROR: unable to load productions duplicate refs file");
        }

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'PRODUCTION_CAST_STAGE3.csv', "r");
        if ($handle)
        {
            // Create error lines file
            $errHandle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . "PRODUCTION_CAST_ERRORS.csv", "w");
            if (!$errHandle)
            {
                fclose($handle);
                return array("unable to create dump file for error lines");
            }

            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 4)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 4 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Trim all data
                for ($i=0; $i<4; $i++)
                {
                    $data[$i] = trim($data[$i]);
                }

                // Gather ID of production
                $productionId = "";
                if ($data[0] == "\\N" || $data[0] == "")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, production ID is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                else
                {
                    if (is_numeric($data[0]))
                    {
                        if (isset($duplicateProductionsMap[self::LTC . $data[0]]))
                        {
                            $result[] = "WARNING: at line " . $importedLines . " of file, production ID " . $data[0] . " is a duplicate entry referencing " . $duplicateProductionsMap[self::LTC . $data[0]];
                            $warningLines++;
                            $data[0] = $duplicateProductionsMap[self::LTC . $data[0]];
                        }
                        $productionId = intval($data[0]);
                    }
                    else
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, production ID " . $data[0] . " is not numeric";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }
                }

                // Gather ID of person
                if ($data[1] == "\\N" || $data[1] == "")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, person ID is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                if (!is_numeric($data[1]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, person ID " . $data[1] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                $personId = intval($data[1]);

                // Gather ID of character
                $characterId = "";
                if ($data[2] == "\\N")
                {
                    $characterId = "NULL";
                }
                else if ($data[2] == "")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, character ID is empty but not null";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                else
                {
                    if (is_numeric($data[2]))
                    {
                        if (isset($duplicateCharactersMap[self::LTC . $data[2]]))
                        {
                            $result[] = "WARNING: at line " . $importedLines . " of file, character ID " . $data[2] . " is a duplicate entry referencing " . $duplicateCharactersMap[self::LTC . $data[2]];
                            $warningLines++;
                            $data[2] = $duplicateCharactersMap[self::LTC . $data[2]];
                        }
                        $characterId = intval($data[2]);
                    }
                    else
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, character ID " . $data[2] . " is not numeric";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }
                }

                // Gather role name
                $roleName = $data[3];
                if ($roleName == "" || $roleName == "\\N")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, role name is empty or null";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Insert into database
                if (!$model->importCasting(array(
                    CastingModel::CASTING_PERSON_ID => $personId,
                    CastingModel::CASTING_PRODUCTION_ID => $productionId,
                    CastingModel::CASTING_CHARACTER_ID => $characterId,
                    CastingModel::ROLE_NAME => $roleName,
                )))
                {
                    $result[] = "ERROR: at line " . $importedLines ." of file, importation into database failed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file PRODUCTION_CAST.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);
            fclose($errHandle);

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file PRODUCTION_CAST.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
    }

    private function importProdComps()
    {
        // result array
        $result = array();

        // Create model
        $model = $this->getModel('importtype');

        // Load productions duplicate ref mapping
        $duplicateProductionsMap = NULL;
        try
        {
            $duplicateProductionsMap = $this->loadProductionDuplicateRefs();
            if (!is_array($duplicateProductionsMap))
            {
                throw new ILARIA_CoreError("The productions duplicate ref mapping is not an array",
                    ILARIA_CoreError::GEN_DB_QUERY_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
        catch (ILARIA_CoreError $e)
        {
            return array("ERROR: unable to load productions duplicate refs file");
        }

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'PRODUCTION_COMPANY.csv', "r");
        if ($handle)
        {
            // Create error lines file
            $errHandle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . "PRODUCTION_COMPANY_ERRORS.csv", "w");
            if (!$errHandle)
            {
                fclose($handle);
                return array("unable to create dump file for error lines");
            }

            $importedLines = 0;
            $warningLines = 0;
            $errorLines = 0;
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of fields
                if (count($data) != 4)
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, " . count($data) . " fields found in CSV, 4 required";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Trim all data
                for ($i=0; $i<4; $i++)
                {
                    $data[$i] = trim($data[$i]);
                }

                // Gather ID
                if (!is_numeric($data[0]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, productioncompany ID " . $data[0] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                $generalId = intval($data[0]);

                // Gather ID of company
                if ($data[1] == "\\N" || $data[1] == "")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, company ID is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                if (!is_numeric($data[1]))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, company ID " . $data[1] . " is not numeric";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                $companyId = intval($data[1]);

                // Gather ID of production
                $productionId = "";
                if ($data[2] == "\\N" || $data[2] == "")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, production ID is empty";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }
                else
                {
                    if (is_numeric($data[2]))
                    {
                        if (isset($duplicateProductionsMap[self::LTC . $data[2]]))
                        {
                            $result[] = "WARNING: at line " . $importedLines . " of file, production ID " . $data[2] . " is a duplicate entry referencing " . $duplicateProductionsMap[self::LTC . $data[2]];
                            $warningLines++;
                            $data[2] = $duplicateProductionsMap[self::LTC . $data[2]];
                        }
                        $productionId = intval($data[2]);
                    }
                    else
                    {
                        $result[] = "ERROR: at line " . $importedLines . " of file, production ID " . $data[2] . " is not numeric";
                        $result[] = "   => line " . $importedLines . " not imported";
                        $errorLines++;
                        $importedLines++;
                        fwrite($errHandle, $buffer);
                        continue;
                    }
                }

                // Gather type name
                $typeName = $data[3];
                if ($typeName == "" || $typeName == "\\N")
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, type name is empty or null";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Import into database
                if (!$model->importLink(array(
                    TypeModel::PRODUCTIONCOMPANY_ID => $generalId,
                    TypeModel::PRODUCTIONCOMPANY_PRODUCTION_ID => $productionId,
                    TypeModel::PRODUCTIONCOMPANY_COMPANY_ID => $companyId,
                    TypeModel::TYPE_NAME => $typeName,
                )))
                {
                    $result[] = "ERROR: at line " . $importedLines . " of file, importation into database failed";
                    $result[] = "   => line " . $importedLines . " not imported";
                    $errorLines++;
                    $importedLines++;
                    fwrite($errHandle, $buffer);
                    continue;
                }

                // Increase number of lines
                $importedLines++;
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file PRODUCTION_COMPANY.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);
            fclose($errHandle);

            $result[] = "Total treated lines : " . $importedLines;
            $result[] = "Lines imported : " . ($importedLines - $errorLines);
            $result[] = " (with warnings) : " . $warningLines;
            $result[] = "Lines not imported (error) : " . $errorLines;
            return $result;
        }
        else
        {
            throw new ILARIA_CoreError("The file PRODUCTION_COMPANY.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

    }

    const LTC = 'ltc';

    private function loadCharacterDuplicateRefs()
    {
        // Result tab
        $result = array();

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'CHARACTER_DUPLICATE_REF.csv', "r");
        if ($handle)
        {
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of params
                if (count($data) != 2)
                {
                    throw new ILARIA_CoreError("The file CHARACTER_DUPLICATE_REF.csv cannot be imported : a line has more than 2 params",
                        ILARIA_CoreError::GEN_INCORRECT_GET_ARG,
                        ILARIA_CoreError::LEVEL_ADMIN);
                }

                // Add mapping to array
                $result[self::LTC . $data[0]] = $data[1];
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file CHARACTER_DUPLICATE_REF.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);
        }
        else
        {
            throw new ILARIA_CoreError("The file CHARACTER_DUPLICATE_REF.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

        return $result;
    }

    private function loadProductionDuplicateRefs()
    {
        // Result tab
        $result = array();

        // Open CSV file
        $handle = fopen(ILARIA_ConfigurationGlobal::getFsWebStaticAssets() . DS . 'csv' . DS . 'PRODUCTION_DUPLICATE_REF.csv', "r");
        if ($handle)
        {
            while (($buffer = fgets($handle)) !== false)
            {
                // Explode data
                $data = explode("\t", trim($buffer));

                // Check for number of params
                if (count($data) != 2)
                {
                    throw new ILARIA_CoreError("The file PRODUCTION_DUPLICATE_REF.csv cannot be imported : a line has more than 2 params",
                        ILARIA_CoreError::GEN_INCORRECT_GET_ARG,
                        ILARIA_CoreError::LEVEL_ADMIN);
                }

                // Add mapping to array
                $result[self::LTC . $data[0]] = $data[1];
            }
            if (!feof($handle))
            {
                throw new ILARIA_CoreError("The file PRODUCTION_DUPLICATE_REF.csv exited while not read until the end",
                    ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            fclose($handle);
        }
        else
        {
            throw new ILARIA_CoreError("The file PRODUCTION_DUPLICATE_REF.csv cannot be opened on server",
                ILARIA_CoreError::GEN_FILE_NOT_FOUND,
                ILARIA_CoreError::LEVEL_ADMIN);
        }

        return $result;
    }

    private function yearRangeStrToSql($text)
    {
        if ($text == "\\N" || $text == "????")
        {
            return array(
                ProductionModel::SERIE_YEARSTART => "",
                ProductionModel::SERIE_YEAREND => "",
            );
        }
        else
        {
            $tab = explode('-', trim($text));
            if (count($tab) == 2)
            {
                $yearStart = trim($tab[0]);
                if (is_numeric($yearStart))
                {
                    if (strlen($yearStart) <= 4)
                    {
                        $yearStart = intval($yearStart);
                    }
                    else
                    {
                        throw new ILARIA_CoreError("The start year \"" . $yearStart . "\" has more than 4 positions",
                            ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                }
                else
                {
                    $yearStart = "";
                }

                $yearEnd = trim($tab[1]);
                if (is_numeric($yearEnd))
                {
                    if (strlen($yearEnd) <= 4)
                    {
                        $yearEnd = intval($yearEnd);
                    }
                    else
                    {
                        throw new ILARIA_CoreError("The end year \"" . $yearEnd . "\" has more than 4 positions",
                            ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                }
                else
                {
                    $yearEnd = "";
                }

                return array(
                    ProductionModel::SERIE_YEARSTART => $yearStart,
                    ProductionModel::SERIE_YEAREND => $yearEnd,
                );
            }
            else
            {
                throw new ILARIA_CoreError("The year range \"" . trim($text) . "\" is made of " . count($tab) . " parts, 2 required",
                    ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
    }

    private function nameStrToSql($text)
    {
        if ($text == "")
        {
            throw new ILARIA_CoreError("An empty name is not authorized",
                ILARIA_CoreError::GEN_PARSE_NAME_FAILED,
                ILARIA_CoreError::LEVEL_ADMIN);
        }
        else
        {
            $result = array();
            $tab = explode(',', trim($text));
            if (count($tab) > 0)
            {
                $result[PersonModel::NAME_LASTNAME] = trim($tab[0]);
                $result[PersonModel::NAME_FIRSTNAME] = '';
                if (count($tab) > 1)
                {
                    for ($i=1; $i<count($tab); $i++)
                    {
                        if ($i > 1)
                        {
                            $result[PersonModel::NAME_FIRSTNAME] .= ", ";
                        }
                        $result[PersonModel::NAME_FIRSTNAME] .= trim($tab[$i]);
                    }
                }
            }
            else
            {
                throw new ILARIA_CoreError("The name \"" . trim($text) . "\" seems empty",
                    ILARIA_CoreError::GEN_PARSE_NAME_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
            return $result;
        }
    }

    private function dateStrToSql($text)
    {
        if ($text == "\\N" || $text == "")
        {
            return "";
        }
        else
        {
            $text = trim(str_replace(".", "", str_replace("BC", "", $text)));
            $tab = explode(' ', trim($text));
            if (count($tab) >= 3)
            {
                // Check for day value
                $day = "";
                if (is_numeric($tab[0]))
                {
                    if (strlen($tab[0]) >= 1 && strlen($tab[0]) <= 2)
                    {
                        $day = str_pad($tab[0], 2, "0", STR_PAD_LEFT);
                    }
                    else
                    {
                        throw new ILARIA_CoreError("The date \"" . trim($text) . "\" has a too long day argument",
                            ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("The date \"" . trim($text) . "\" has a non-integer day",
                        ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                        ILARIA_CoreError::LEVEL_ADMIN);
                }

                // Check for month value
                $month = "";
                switch (strtolower($tab[1]))
                {
                    case "january":
                        $month = "01";
                        break;
                    case "february":
                        $month = "02";
                        break;
                    case "march":
                        $month = "03";
                        break;
                    case "april":
                        $month = "04";
                        break;
                    case "may":
                        $month = "05";
                        break;
                    case "june":
                        $month = "06";
                        break;
                    case "july":
                        $month = "07";
                        break;
                    case "august":
                        $month = "08";
                        break;
                    case "september":
                        $month = "09";
                        break;
                    case "october":
                        $month = "10";
                        break;
                    case "november":
                        $month = "11";
                        break;
                    case "december":
                        $month = "12";
                        break;
                    default:
                        throw new ILARIA_CoreError("The date \"" . trim($text) . "\" has an unknown month",
                            ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                }

                // Check for year value
                $year = "";
                if (is_numeric($tab[2]))
                {
                    if ((strlen($tab[2]) >= 1) && (strlen($tab[2]) <=4))
                    {
                        $year = str_pad($tab[2], 4, "0", STR_PAD_LEFT);
                    }
                    else
                    {
                        throw new ILARIA_CoreError("The date \"" . trim($text) . "\" has a year over invalid number of positions",
                            ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                }
                else
                {
                    throw new ILARIA_CoreError("The date \"" . trim($text) . "\" has a non-numeric year",
                        ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                        ILARIA_CoreError::LEVEL_ADMIN);
                }

                // Return formatted string
                return $year . "-" . $month . "-" . $day;
            }
            else
            {
                throw new ILARIA_CoreError("The date \"" . trim($text) . "\" is not composed of 3 parts",
                    ILARIA_CoreError::GEN_PARSE_DATE_FAILED,
                    ILARIA_CoreError::LEVEL_ADMIN);
            }
        }
    }

    private function sizeStrToSql($text)
    {
        if ($text == "\\N" || $text == "")
        {
            return "NULL";
        }
        else
        {
            // Size expressed in meters
            $size = 0.0;
            $text = trim(str_replace("cm", "", str_replace(",", ".", $text)));
            $tab = explode(' ', trim($text));
            switch (count($tab))
            {
                case 0:
                    throw new ILARIA_CoreError("The size \"" . trim($text) . "\" seems empty",
                        ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                        ILARIA_CoreError::LEVEL_ADMIN);
                case 1:
                    if (substr(trim($tab[0]), -1) == "'")
                    {
                        // Add feets
                        $size += 0.3048 * floatval(substr(trim($tab[0]), 0, strlen(trim($tab[0]))-1));
                    }
                    else if (substr(trim($tab[0]), -1) == "\"")
                    {
                        // Add feets and inches
                        $subtab = explode("'", trim($tab[0]));
                        if (count($subtab) == 2)
                        {
                            $size += 0.3048 * floatval(trim($subtab[0]));
                            $size += 0.0254 * floatval(substr(trim($subtab[1]), 0, strlen(trim($subtab[1]))-1));
                        }
                        else
                        {
                            throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has only one component, which cannot be broken down",
                                ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                                ILARIA_CoreError::LEVEL_ADMIN);
                        }
                    }
                    else if (strpos($tab[0], "'") !== false)
                    {
                        // Add feets and inches
                        $subtab = explode("'", trim($tab[0]));
                        if (count($subtab) == 2)
                        {
                            $size += 0.3048 * floatval(trim($subtab[0]));
                            $size += 0.0254 * floatval(trim($subtab[1]));
                        }
                        else
                        {
                            throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has only one component, which cannot be broken down",
                                ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                                ILARIA_CoreError::LEVEL_ADMIN);
                        }
                    }
                    else if (is_numeric(trim($tab[0])))
                    {
                        // Add centimeters
                        $size += 0.01 * floatval(trim($tab[0]));
                    }
                    else
                    {
                        throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has only one component, which is not feet",
                            ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                    break;
                case 2:
                    if ((substr(trim($tab[0]), -1) == "'")
                        && (substr(trim($tab[1]), -1) == "\""))
                    {
                        // Add feets and inches
                        $size += 0.3048 * floatval(substr(trim($tab[0]), 0, strlen(trim($tab[0]))-1));
                        $size += 0.0254 * floatval(substr(trim($tab[1]), 0, strlen(trim($tab[1]))-1));
                    }
                    else if (substr(trim($tab[0]), -1) == "'")
                    {
                        // Add feets and inches
                        $size += 0.3048 * floatval(substr(trim($tab[0]), 0, strlen(trim($tab[0]))-1));
                        $size += 0.0254 * floatval(trim($tab[1]));
                    }
                    else
                    {
                        throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has only two components, which are not either feets, inches or centimeters",
                            ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                    break;
                case 3:
                    if ((substr(trim($tab[0]),-1) == "'")
                        && is_numeric(trim($tab[1]))
                        && (substr(trim($tab[2]), -1) == "\""))
                    {
                        // Add feets, inches and parts of inches
                        $size += 0.3048 * floatval(substr(trim($tab[0]), 0, strlen(trim($tab[0]))-1));
                        $size += 0.0254 * floatval(trim($tab[1]));
                        $tabInches = explode('/', substr(trim($tab[2]), 0, strlen(trim($tab[2]))-1));
                        if (count($tabInches) == 2)
                        {
                            if (is_numeric(trim($tabInches[0])) && is_numeric(trim($tabInches[1])))
                            {
                                $size += 0.0254 * floatval(trim($tabInches[0])) / floatval(trim($tabInches[1]));
                            }
                            else
                            {
                                throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has a non-numeric value as third component",
                                    ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                                    ILARIA_CoreError::LEVEL_ADMIN);
                            }
                        }
                        else
                        {
                            throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has a problem in the third component",
                                ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                                ILARIA_CoreError::LEVEL_ADMIN);
                        }
                    }
                    else
                    {
                        throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has only three components, which are not either feets, inches or centimeters",
                            ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                            ILARIA_CoreError::LEVEL_ADMIN);
                    }
                    break;
                default:
                    echo "unknown number of arguments<br />";
                    throw new ILARIA_CoreError("The size \"" . trim($text) . "\" has not a valid number of components",
                        ILARIA_CoreError::GEN_PARSE_SIZE_FAILED,
                        ILARIA_CoreError::LEVEL_ADMIN);
            }
            return number_format(floatval($size), 3, ".", "");
        }
    }
}