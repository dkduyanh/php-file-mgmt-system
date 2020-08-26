<?php
/********************
 * GLOBAL CONSTANTS *
 ********************/

#Defines separators
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

#Sets the default timezone used by all date/time functions in a script
defined('DEFAULT_TIMEZONE') || define('DEFAULT_TIMEZONE', 'Asia/Ho_Chi_Minh');

#Defines root path
define('ROOT_DIR', str_replace(DS, '/', dirname(dirname(__DIR__))));

#Defines path to system directories
define('APPLICATION_DIR', realpath(ROOT_DIR.'/application'));
define('DATA_DIR', realpath(ROOT_DIR.'/data'));
define('VENDOR_DIR', realpath(ROOT_DIR.'/vendor'));
define('PUBLIC_DIR', ROOT_DIR.'/public');

#Defines domain name and protocols
define('DOMAIN', isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:gethostname());

#Defines application base url
defined('BASE_URL') || define('BASE_URL', 'http://'.DOMAIN);
defined('BASE_URL_SECURE') || define('BASE_URL_SECURE', 'https://'.DOMAIN);

defined('CACHE_DEFAULT_TIMEOUT') || define('CACHE_DEFAULT_TIMEOUT', getenv('CACHE_DEFAULT_TIMEOUT') ? : '3600');

/**************************
 * USER DEFINED CONSTANTS *
 **************************/

//DuyAnh :: load .env if available
if(is_file(ROOT_DIR.'/.env')){
    $dotenv = Dotenv\Dotenv::create(ROOT_DIR);
    $dotenv->load();
}

#Specifies whether the application is running in debug mode. Set "TRUE" to enable.
defined('DEBUG') || define('DEBUG', getenv('DEBUG') ? : TRUE);

#Specifies which environment the application is running in. (prod or dev)
defined('ENV') || define('ENV', getenv('ENV') ? : 'dev');

#Set Yii app environment
defined('YII_DEBUG') or define('YII_DEBUG', DEBUG);
defined('YII_ENV') or define('YII_ENV', ENV);

define('APP_NAME', getenv('APP_NAME') ? : 'MY YII APPLICATION');
define('APP_ID', strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", APP_NAME)));

define('IMAGES_URL', getenv('IMAGES_URL') ? : BASE_URL.'/uploads/images');
define('IMAGES_UPLOAD_URL', getenv('IMAGES_UPLOAD_URL') ? : BASE_URL.'/uploads/images/upload.php');

define('UPLOAD_TOKEN', getenv('UPLOAD_TOKEN'));
define('MAX_FILE', getenv('MAX_FILE'));
define('MAX_FILE_SIZE', getenv('MAX_FILE_SIZE'));

define('DB_HOST', getenv('DB_HOST') ? : 'localhost');
define('DB_PORT', getenv('DB_PORT') ? : '3306');
define('DB_USER', getenv('DB_USER') ? : 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ? : '');
define('DB_NAME', getenv('DB_NAME') ? : 'default');

define('MAILER_SMTP_HOST', getenv('MAILER_SMTP_HOST') ? : 'smtp.gmail.com');
define('MAILER_SMTP_USER', getenv('MAILER_SMTP_USER') ? : 'user');
define('MAILER_SMTP_PASSWORD', getenv('MAILER_SMTP_PASSWORD') ? : '');
define('MAILER_SMTP_PORT', getenv('MAILER_SMTP_PORT') ? : '587');
define('MAILER_SMTP_ENCRYPTION', getenv('MAILER_SMTP_ENCRYPTION') ? : 'tls');
define('MAILER_DEFAULT_SENDER_EMAIL', getenv('MAILER_DEFAULT_SENDER_EMAIL') ? : '');
define('MAILER_DEFAULT_SUBJECT_PREFIX', getenv('MAILER_DEFAULT_SUBJECT_PREFIX') ? : '['.APP_NAME.']');

define('MAX_FAILED_LOGIN_ATTEMPTS', getenv('MAX_FAILED_LOGIN_ATTEMPTS') ? : 5);