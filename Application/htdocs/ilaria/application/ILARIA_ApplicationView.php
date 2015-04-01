<?php

abstract class ILARIA_ApplicationView
{
    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    const ALERT_TYPE_SUCCESS = 'f9c90745f449314d29e674453ec2946e135c4cc0';
    const ALERT_TYPE_INFO = '3205b41c56b16b9438c65c7a6f04a874514a356d';
    const ALERT_TYPE_WARNING = '843cbbcec61351450f379d085dd0bec9f3c3df78';
    const ALERT_TYPE_DANGER = '7bf1926bc021b9f3b11c3f2875fa941fd178d6fc';

    const ALERT_TYPE = '6024b2ec0353ce4dda830cc498939e1c8d02ef1c';
    const ALERT_MESSAGE = '757b2b813cddb714238b6ef1290eaa5e5b06d2ad';

    private $output = array();
    private $templateName = NULL;
    private $alerts = array();

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct() {}

    public function display()
    {
        foreach ($this->output as $line)
        {
            echo $line . "\n";
        }
    }

    public function setTemplateName($name)
    {
        $this->templateName = $name;
    }

    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function addAlert($type, $message)
    {
        $this->alerts[] = array(
            self::ALERT_TYPE => $type,
            self::ALERT_MESSAGE => $message,
        );
    }

    public function getAlerts()
    {
        return $this->alerts;
    }

    public function getAlertType($alert)
    {
        switch ($alert[self::ALERT_TYPE])
        {
            case self::ALERT_TYPE_SUCCESS:
                return 'success';
            case self::ALERT_TYPE_INFO:
                return 'info';
            case self::ALERT_TYPE_WARNING:
                return 'warning';
            case self::ALERT_TYPE_DANGER:
                return 'danger';
            default:
                return 'danger';
        }
    }

    abstract public function prepare($data);

    // #################################################################################################################
    // ##                                             PROTECTED FUNCTIONS                                             ##
    // #################################################################################################################

    protected function output($line)
    {
        $this->output[] = $line;
    }
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_ApplicationView.php] class loaded');