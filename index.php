<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

session_start();

require 'autoload.php';
require 'vendor/autoload.php';


try {
    $view = new IndexController();

    /*if (1 || empty($view->is_developer))
    {
        echo '<h1 style="color:red;text-align:center;margin-top:10%">На сайте проводятся технические работы. <br />Ориентировочное время окончания 17:00 11.05.2023<br />Приносим извинения за неудобства</h1>';    
        exit;
    }*/

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
