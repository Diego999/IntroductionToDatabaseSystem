<?php

class ILARIA_LogWriter
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $messages = array();
    private $outputMode = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct($outputMode)
    {
        $this->outputMode = $outputMode;
    }

    public function write($message)
    {
        if ($message != '')
        {
            $this->messages[] = $message;
        }
    }

    public function read()
    {
        return $this->messages;
    }

    public function output($file)
    {
        switch ($this->outputMode)
        {
            case ILARIA_ConfigurationGlobal::LOG_OUTPUT_NONE:
                break;
            case ILARIA_ConfigurationGlobal::LOG_OUTPUT_FILE_APPEND:
                $fp = fopen(ILARIA_ConfigurationGlobal::getFsLogs() . DS . $file, "a");
                foreach ($this->messages as $message)
                {
                    fwrite($fp, $message . "\n");
                }
                fclose($fp);
                break;
            case ILARIA_ConfigurationGlobal::LOG_OUTPUT_FILE_ERASE:
                $fp = fopen(ILARIA_ConfigurationGlobal::getFsLogs() . DS . $file, "w");
                foreach ($this->messages as $message)
                {
                    fwrite($fp, $message . "\n");
                }
                fclose($fp);
                break;
        }
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}