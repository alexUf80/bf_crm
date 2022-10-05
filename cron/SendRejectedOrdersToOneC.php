<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('max_execution_time', 180);

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * отправляем в 1с отказы
 */
class SendRejectedOrdersToOneC extends Core
{
    public function __construct()
    {
    	parent::__construct();
    }
    
    public function run()
    {
        $orders = $this->orders->get_orders([
                'status' => 
                [
                    3, //'Отказ',
                    //5, //'Займ выдан',
                    6, //'Не удалось выдать',
                    8, //'Отказ клиента',
                ],
                'date_from' => '2022-06-06',//date('Y-m-d'), //todo
                //'date_to' => '2022-06-06',//date('Y-m-d'), //todo
                'limit' => 100,
                'sent_1c' => [0]
            ]);
        
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($orders);echo '</pre><hr />';
        //exit;

        if (empty($orders)) {
            return false;
        }

        foreach ($orders as $order) {
            $order_id = $order->order_id;
            $response = $this->onec->send_order($order_id);
    
            $resp = $this->onec->send_pdn($order_id);

            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';

            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($response);echo '</pre><hr />';
        }
    }
}

(new SendRejectedOrdersToOneC())->run();
