<?php

class ILARIA_SecurityAccess
{
    // #################################################################################################################
    // ##                             CONST VALUES FOR KEY ACCESS INTO MODULE IMPLEMENTATIONS                         ##
    // #################################################################################################################

    const MOD_USER_TOKEN = 'c13983fafaae5ac4b147a3a9f8fba4338e37a023';
    const MOD_LOGIN_WRAP = 'e59467c7d21e6fa0ef2cd70617cb0a8ffc0e1274';
    const MOD_LOGOUT_WRAP = '6664f5e8bd7388463c499be693a9e3b93cd03fe1';

    // #################################################################################################################
    // ##                                                 ATTRIBUTES                                                  ##
    // #################################################################################################################

    private $module = NULL;

    // #################################################################################################################
    // ##                                              PUBLIC FUNCTIONS                                               ##
    // #################################################################################################################

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function getUserToken()
    {
        return $this->module->call(self::MOD_USER_TOKEN, NULL);
    }

    public function login($infos)
    {
        return $this->module->call(self::MOD_LOGIN_WRAP, $infos);
    }

    public function logout()
    {
        return $this->module->call(self::MOD_LOGOUT_WRAP, NULL);
    }

    // #################################################################################################################
    // ##                                              PRIVATE FUNCTIONS                                              ##
    // #################################################################################################################
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_SecurityAccess.php] class loaded');