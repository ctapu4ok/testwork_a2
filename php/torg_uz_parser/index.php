<?php
set_time_limit(0);
define(CLASSES_PATH, 'classes');
define(CLASS_POSTFIX, '.class');
define(CLASS_EXT, '.php');
define(DIRECTORY_SEPARATOR, '/');

$config = array();

$autoloader = CLASSES_PATH.DIRECTORY_SEPARATOR.'autoload.php';
$configFile = 'config'.DIRECTORY_SEPARATOR.'main.php';

if(file_exists($configFile))
{
    require_once $configFile;
}

if(file_exists($autoloader))
{
    require_once $autoloader;
}

Autoload::_autoload($config);