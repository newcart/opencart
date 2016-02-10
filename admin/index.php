<?php

define('DIR_ROOT', realpath(__DIR__ . '/../../'));

// Load
if (is_file(DIR_ROOT . '/load.php')) {
    require_once(DIR_ROOT . '/load.php');
} else {
    throw new Exception("Load file required");
}

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();