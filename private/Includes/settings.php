<?php
/**
 * settings.php
 *
 * @author Afnan Khalid
 * @Reference De Montfort University, CF Ingrams - cfi@dmu.ac.uk & all labs.
 *
 * @package Royal Silk Leicester
 */

define('DIRSEP', DIRECTORY_SEPARATOR);
define('URLSEP', '/');

// __FILE__ = full path and filename of the current script file.
$app_file_path = realpath(dirname(__FILE__)); //htdocs
$class_file_path = $app_file_path . DIRSEP . 'classes' . DIRSEP;

//This line checks whether the current request is being made over HTTPS (secure) or not, and sets the appropriate protocol for generating URLs.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://'; //If both conditions are true → use 'https://' Otherwise → use 'http://'

//This gets the current host/domain name of the site
$document_root = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'; //$_SERVER['HTTP_HOST'] is the domain name from the request. If it's not set (e.g. when running via CLI), it falls back to 'localhost'

//This builds the full root URL to your application folder.
$app_root_path = $protocol . $document_root . dirname($_SERVER['SCRIPT_NAME']) . URLSEP;

$application_name = 'Royal Silk Leicester';
$media_path = $app_root_path . 'media' . URLSEP;
$css_path = 'css' . URLSEP;
$css_file_name = 'styles.css';

define ('CLASS_PATH', $class_file_path);
define ('APP_ROOT_PATH', $app_root_path);
define ('APP_NAME', $application_name);
define ('MEDIA_PATH', $media_path);
define ('CSS_PATH' , $css_path);
define ('CSS_FILE_NAME', $css_file_name);

/**
 * Database connection details for the mysql database of the app.
 * @return string[]
 */
function databaseConnectionDetails()
{
    $rdbms = 'mysql';
    $host = 'localhost';
    $port = '3306';
    $charset = 'utf8mb4';
    $dbname = 'dbrsl';
    $dbusername = 'rsluser';
    $dbpassword = 'rslpass';
    $dsn = "$rdbms:host=$host;port=$port;dbname=$dbname;charset=$charset";

    return [
        'dsn' => $dsn,
        'dbusername' => $dbusername,
        'dbpassword' => $dbpassword
    ];
}
