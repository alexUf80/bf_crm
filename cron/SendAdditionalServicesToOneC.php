<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * отправляем в 1c 
 */
class SendAdditionalServicesToOneC extends Core
{
    public function __construct()
    {
    	parent::__construct();
    }
    
    public function run()
    {
        //exit;
        $operations = $this->operations->get_operations([
            'type' => ['BUD_V_KURSE'],
            'date_from' => date('Y-m-d', strtotime('2022-01-03')), //todo
            'limit' => 9, 
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
            $resp = $this->onec->send_bud_v_kurse($operation);

            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operation->id, $resp);echo '</pre><hr />';

            if (!empty($resp) && $resp == 'OK') {
                $this->operations->update_operation($operation->id, [
                    'sent_status' => 2,
                    'sent_date' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->operations->update_operation($operation->id, [
                    'sent_status' => 3,
                ]);
            }
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
        (new SendAdditionalServicesToOneC())->run();
        file_put_contents($lockfile, getmypid());
    } else {
        exit('Another instance of the script is already running.');
    }
}

