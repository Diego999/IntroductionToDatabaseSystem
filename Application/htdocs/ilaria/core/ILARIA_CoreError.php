<?php

class ILARIA_CoreError extends Exception
{
    // #################################################################################################################
    // ##                                                 ERROR TYPES                                                 ##
    // #################################################################################################################

    const LEVEL_USER = 'eed68e4c4e22f827268223ed324bfc77c4795dd1';
    const LEVEL_ADMIN = '0e52949b640aecce17b4ade351f4fcb50ae279ff';
    const LEVEL_SERVER = 'c11a36cbbdfe99772fa7b1c8345f8094e0c6c6b5';
    const LEVEL_KERNEL = 'badbd2ebbd81b02b6df51218ef7551321f50bc91';

    const GEN_FILE_NOT_FOUND = 'd7ad60b2f1af9434649e08c85de4e46794b57a24';
    const GEN_CLASS_NOT_FOUND = 'd306a79e922e76aa784706e04f3f247e36230a4c';
    const GEN_ACTION_NOT_FOUND = '30ef659312d6b5759ee4cd2e4e94cd060cb7f1f2';
    const GEN_MODULE_NOT_FOUND = '81fe09cfd58107864f6f909c0b5ce69577dbe5c4';
    const GEN_MODULE_UNKNOWN_KEY = '8ea8e38b8920531312e5a5ce588cef8761eb7ec3';
    const GEN_INCORRECT_GET_ARG = 'fd2b93cabc22211a5387a96c2d14cbf89d02bab0';
    const GEN_NOT_REG_GET_ARG = '751dfa21821636f8d6fb9d223b5038441bf2f62f';
    const GEN_NOT_REG_POST_ARG = '304ec8b620ea3ac77eec133ff67d4e80679d020a';
    const GEN_NOT_REG_FILE_ARG = '7064d3e6bbc83f662eabaa1a42d10ef4a877e462';
    const GEN_PERMISSION_DENIED = 'b556be7f8615e528a78e6f8df32f319259db0478';
    const GEN_VIEW_UNLOADABLE = '48a5c20542380332d68af1113959d60b588ee107';
    const GEN_MODEL_UNLOADABLE = '3ba935253d125e0017fe8f55b162afb497cff011';
    const GEN_TEMPLATE_UNLOADABLE = '91cb73686a77f19a3a58cc36e756462a0b19f86e';
    const GEN_MENU_UNLOADABLE = 'a4212e0692a595e972ed520d1aea9f13f295d10a';
    const GEN_DB_FAILED_OPEN = 'c84334a4267ecc2c5411ab1388ac6b26f18823fe';
    const GEN_DB_QUERY_FAILED = 'c4509663fb1f87ba701a0f3ba51fb8d1202cbec4';
    const GEN_PARSE_DATE_FAILED = '35b3e7d578be3c71cd267f24f0b57372e0606891';
    const GEN_PARSE_NAME_FAILED = '016578bffdc7fc1ef726157dbdd8d4a2a6e9f204';
    const GEN_PARSE_SIZE_FAILED = 'd8f4a06e462a076245420586905d409fbd785456';

    // #################################################################################################################
    // ##                                              ERRORS MANAGEMENT                                              ##
    // #################################################################################################################

    private $ilaria_message = NULL;
    private $ilaria_type = NULL;
    private $ilaria_level = NULL;

    public function __construct($message, $type, $level)
    {
        // Register settings locally
        $this->ilaria_message = $message;
        $this->ilaria_type = $type;
        $this->ilaria_level = $level;
    }

    public function writeToLog()
    {
        $logMsg = '';
        switch (ILARIA_ConfigurationGlobal::getErrorMode())
        {
            case ILARIA_ConfigurationGlobal::ERROR_MODE_VERBOSE:
                $logMsg = '[' . strtoupper($this->getLevelString($this->ilaria_level)) . ':' . $this->getTypeString($this->ilaria_type) . '] ' . $this->ilaria_message;
                break;
            case ILARIA_ConfigurationGlobal::ERROR_MODE_MINIMAL:
                switch ($this->ilaria_level)
                {
                    case self::LEVEL_USER:
                    case self::LEVEL_ADMIN:
                        $logMsg = $this->ilaria_message;
                        break;
                    default:
                        break;
                }
                break;
            case ILARIA_ConfigurationGlobal::ERROR_MODE_HIDDEN:
                break;
            default:
                break;
        }
        ILARIA_LogManager::getInstance()->getWriterErrors()->write($logMsg);
    }

    public function changeType($type) { $this->ilaria_type = $type; }

    public function getMsg() { return $this->ilaria_message; }
    public function getType() { return $this->ilaria_type; }
    public function getLevel() { return $this->ilaria_level; }

    public function getLevelString($level)
    {
        switch ($level)
        {
            case self::LEVEL_USER: return 'user';
            case self::LEVEL_ADMIN: return 'admin';
            case self::LEVEL_SERVER: return 'server';
            case self::LEVEL_KERNEL: return 'kernel';
            default: return 'unknown';
        }
    }

    public function getTypeString($type)
    {
        switch ($type)
        {
            case self::GEN_FILE_NOT_FOUND: return 'gen_file_not_found';
            case self::GEN_CLASS_NOT_FOUND: return 'gen_class_not_found';
            case self::GEN_ACTION_NOT_FOUND: return 'gen_action_not_found';
            case self::GEN_MODULE_NOT_FOUND: return 'gen_module_not_found';
            case self::GEN_MODULE_UNKNOWN_KEY: return 'gen_module_unknown_key';
            case self::GEN_INCORRECT_GET_ARG: return 'gen_incorrect_get_arg';
            case self::GEN_NOT_REG_GET_ARG: return 'gen_not_reg_get_arg';
            case self::GEN_NOT_REG_POST_ARG: return 'gen_not_reg_post_arg';
            case self::GEN_NOT_REG_FILE_ARG: return 'gen_not_reg_file_arg';
            case self::GEN_PERMISSION_DENIED: return 'gen_permission_denied';
            case self::GEN_VIEW_UNLOADABLE: return 'gen_view_unloadable';
            case self::GEN_MODEL_UNLOADABLE: return 'gen_view_unloadable';
            case self::GEN_TEMPLATE_UNLOADABLE: return 'gen_template_unloadable';
            case self::GEN_MENU_UNLOADABLE: return 'gen_menu_unloadable';
            case self::GEN_DB_FAILED_OPEN: return 'gen_db_failed_open';
            case self::GEN_DB_QUERY_FAILED: return 'gen_db_query_failed';
            case self::GEN_PARSE_DATE_FAILED: return 'gen_parse_date_failed';
            case self::GEN_PARSE_NAME_FAILED: return 'gen_parse_name_failed';
            case self::GEN_PARSE_SIZE_FAILED: return 'gen_parse_size_failed';
            default: return 'unknown';
        }
    }
}

ILARIA_LogManager::getInstance()->getWriterDebug()->write('[ILARIA_CoreError.php] class loaded');