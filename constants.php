<?php

define('IS_ADMIN', basename(realpath('')) == 'admin' ? true : false);
define('ENVIRONMENT', IS_ADMIN ? 'admin' : 'catalog');

//get domain name
define('DOMAINNAME', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null);

//define core directory
define('DIR_CORE', realpath(DIR_ROOT . '/core/'));

$base_url = DOMAINNAME . $_SERVER['SCRIPT_NAME'];
$base_url = str_replace(['/core/admin/', '/core', '/index.php'], '/', $base_url);
define('BASEURL', $base_url);

// HTTP
define('HTTP_SERVER', 'http://' . BASEURL . 'core/admin/');
define('HTTP_CATALOG', 'http://' . BASEURL);

// HTTPS
define('HTTPS_SERVER', 'https://' . BASEURL . 'core/admin/');
define('HTTPS_CATALOG', 'http://' . BASEURL);

// DIR
if(IS_ADMIN) {
    define('DIR_TEMPLATE', DIR_CORE . '/' . ENVIRONMENT . '/view/template/');
} else {
    define('DIR_TEMPLATE', DIR_ROOT . '/theme/');
}

define('DIR_APPLICATION', DIR_CORE . '/' . ENVIRONMENT . '/');
define('DIR_SYSTEM', DIR_CORE . '/system/');
define('DIR_STORAGE', DIR_ROOT . '/storage');
define('DIR_LANGUAGE', DIR_CORE . '/' . ENVIRONMENT . '/language/');
define('DIR_CONFIG', DIR_CORE . '/config/');
define('DIR_IMAGE', DIR_STORAGE . '/image/');
define('DIR_CACHE', DIR_STORAGE . '/cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . '/download/');
define('DIR_UPLOAD', DIR_STORAGE . '/upload/');
define('DIR_MODIFICATION', DIR_STORAGE . '/modification/');
define('DIR_LOGS', DIR_STORAGE . '/logs/');
define('DIR_CATALOG', DIR_CORE . '/catalog/');