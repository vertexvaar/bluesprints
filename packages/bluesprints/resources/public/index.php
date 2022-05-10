<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use VerteXVaaR\BlueSprints\Http\Application;
use VerteXVaaR\BlueSprints\Utility\Error;

if (!defined('VXVR_BS_ROOT')) {
    define('VXVR_BS_ROOT', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR);
}

if (!class_exists(ClassLoader::class, false)) {
    if (file_exists('../../../vendor/autoload.php')) {
        // project level
        require('../../../vendor/autoload.php');
    } elseif (file_exists('../../../../autoload.php')) {
        // library level
        require('../../../../autoload.php');
    } else {
        throw new Exception('Autoloader not found', 1491561093);
    }
}
if (empty(ini_get('date.timezone'))) {
    date_default_timezone_set('UTC');
}

Error::registerErrorHandler();
(new Application())->run();
