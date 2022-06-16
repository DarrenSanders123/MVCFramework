<?php
//Require libraries from folder libraries
use eftec\ValidationOne;

require_once '../../vendor/autoload.php';
require_once 'libraries/Core.php';
require_once 'libraries/Controller.php';
require_once 'libraries/Database.php';

require_once 'config/config.php';

function getVal($prefix=''): ValidationOne
{
    global $validation;
    if ($validation===null) {
        $validation=new ValidationOne($prefix);
    }
    return $validation;
}

//Instantiate core class
$init = new Core();
