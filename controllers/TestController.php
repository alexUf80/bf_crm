<?php
error_reporting(-1);
ini_set('display_errors', 'Off');

class TestController extends Controller
{
    public function fetch()
    {
        $order = OrdersORM::find(15222);

        echo '<pre>';
        var_dump($order->user);
        exit;
    }
}