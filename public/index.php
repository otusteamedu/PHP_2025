<?
session_start();
require_once '../vendor/autoload.php';
require_once '../config/main.php';
$config = include __DIR__ . "/../config/config.php";


use app\engine\App;

//spl_autoload_register([new Autoload(), 'loadClass']);
try {
    App::call()->run($config);
} catch (Exception $e) {
    var_dump($e);
}