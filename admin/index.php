<?php

// Root directory project
define('DIR_ROOT', realpath(__DIR__ . '/../../'));

//Composer autoload
require_once(DIR_ROOT . '/vendor/autoload.php');

// Load
require_once(DIR_ROOT . '/vendor/newcart/system/src/Newcart/System/load.php');

$config->set('environment', 'admin');

//Constants
require_once(DIR_ROOT . '/vendor/newcart/system/src/Newcart/System/constants.php');

//Bootstrap
require_once(DIR_APPLICATION . 'bootstrap.php');

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();