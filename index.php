<?php
error_reporting(-1);
ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');

session_start();

use Illuminate\Database\Capsule\Manager as Capsule;

require 'autoload.php';
require 'vendor/autoload.php';

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'bf',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix' => '',
]);

// Set the event dispatcher used by Eloquent models... (optional)
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();


try {
    $view = new IndexController();
    if (($res = $view->fetch()) !== false) {
        if ($res == 403) {
            header("http/1.0 403 Forbidden");
            $_GET['page_url'] = '403';
            $_GET['module'] = 'ErrorController';
            print $view->fetch();
        } else {
            // Выводим результат
            header("Content-type: text/html; charset=UTF-8");
            print $res;

        }
    } else {
        // Иначе страница об ошибке
        header("http/1.0 404 not found");

        // Подменим переменную GET, чтобы вывести страницу 404
        $_GET['page_url'] = '404';
        $_GET['module'] = 'ErrorController';
        print $view->fetch();
    }
} catch (Exception $e) {
    echo __FILE__ . ' ' . __LINE__ . '<br /><pre>';
    var_dump($e);
    echo '</pre><hr />';
}
