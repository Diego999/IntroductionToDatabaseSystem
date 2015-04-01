<?php

class ILARIA_ConfigurationLocal
{
    // #################################################################################################################
    // ##                        DECLARE CONST VALUES => POSSIBLE VALUES FOR LOCAL PARAMETERS                         ##
    // #################################################################################################################

    // Server mode management, to have multiple configurations
    const SERVER_MODE_DEVELOPMENT = 'e1583ddc64a0eabc682069491cae66a3ee604ee6';
    const SERVER_MODE_TEST = '26e245b9057902d67e82b9da0028a33c15f6938b';
    const SERVER_MODE_PRODUCTION = '8d2ffda2967dde15bd7e83dadac9602781c89e49';

    // #################################################################################################################
    // ##                                           DECLARE LOCAL PARAMETERS                                          ##
    // #################################################################################################################

    // Server mode management, to have multiple configurations
    private static $serverMode = self::SERVER_MODE_DEVELOPMENT;

    // #################################################################################################################
    // ##                                         GETTERS FOR LOCAL PARAMETERS                                        ##
    // #################################################################################################################

    // Server mode management, to have multiple configurations
    public static function getServerMode() { return self::$serverMode; }
}