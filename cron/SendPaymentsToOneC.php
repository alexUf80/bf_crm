<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * отправляем в 1c оплаты
 */
class SendPaymentsToOneC extends Core
{
    public function __construct()
    {
    	parent::__construct();
    }
    
    public function run()
    {
        $operations = $this->operations->get_operations([
            'type' => ['PAY'],
            'date_from' => date('Y-m-d'), //todo
            'limit' => 13, 
            'sent_status' => 0,
            'sort' => 'id_asc'
        ]);
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operations);echo '</pre><hr />';
        //exit;

        if (empty($operations)) {
            return false;
        }

        foreach ($operations as $operation) {
            //$order = $this->orders->get_order($operation->order_id);
            //if(!$order->id_deal) {
            //    //continue;
            //}
            //$transaction = $this->transactions->get_transaction($operation->transaction_id);
            //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operation->id, $transaction->prolongation);echo '</pre><hr />';

            //exit;
            $resp = $this->onec->send_payment($operation);

            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
            //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operation);echo '</pre><hr />';
            //exit;
        }
    }
}

$lockfile = sys_get_temp_dir() . '/' . md5(__FILE__) . '.lock';
$pid = file_exists($lockfile) ? trim(file_get_contents($lockfile)) : null;

if (isset($argv[1])) {
    parse_str($argv[1], $params);
}

if ((isset($params['code']) && $params['code'] == 'f45beT4Hs') || (isset($_GET['code']) && $_GET['code'] == 'f45beT4Hs')) {
    if (is_null($pid) || posix_getsid($pid) === false) {
        (new SendPaymentsToOneC())->run();
        file_put_contents($lockfile, getmypid());
    } else {
        exit('Another instance of the script is already running.');
    }
}

