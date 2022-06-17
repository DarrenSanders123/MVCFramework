<?php
//Require libraries from folder libraries
use eftec\ValidationOne;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;

require_once 'config/config.php';

require_once '../../vendor/autoload.php';
require_once 'libraries/Core.php';
require_once 'libraries/Controller.php';
require_once 'libraries/Database.php';

/**
 * Get the validator
 * @param $prefix
 * @return ValidationOne
 */
function getVal($prefix=''): ValidationOne
{
    global $validation;
    if ($validation===null) {
        $validation=new ValidationOne($prefix);
    }
    return $validation;
}

/**
 * Get the logger or create a new one if non exist
 * @param $name
 * @return \Monolog\Logger
 */
function getLog($name=''): Monolog\Logger {
    global $logger;
    if ($logger===null) {
        $logger=new \Monolog\Logger($name);
        $logger->pushHandler(new StreamHandler(APPROOT.'/logs/log.log'));
        $logger->pushHandler(new FirePHPHandler());
    }
    return $logger;
}

//Instantiate core class
$init = new Core();
