<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * отправляем в 1c 
 */
class SendRubToOneC extends Core
{
    public function __construct()
    {
    	parent::__construct();
    }
    
    public function run()
    {
        //exit;
        $filter = [
            'date_from' => date('Y-m-d', strtotime('2022-01-03')), //todo
            'date_to' => date('Y-m-d'), //todo
        ];

        //$transactions = $this->transactions->get_transactions_cards($filter);

        $query = $this->db->placehold("
            SELECT * 
            FROM __cards
            WHERE 1
            AND DATE(operation_date) <= ?
            AND DATE(operation_date) >= ?
            AND sent_status = 0
            ORDER BY id ASC 
            LIMIT 9
        ", $filter['date_to'], $filter['date_from']);
        $this->db->query($query);
        $cards = $this->db->results();
        

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(count($cards));echo '</pre><hr />';
        //exit;

        if (empty($cards)) {
            return false;
        }

        foreach ($cards as $card) {
            //$order = $this->orders->get_order($operation->order_id);
            //if(!$order->id_deal) {
            //    //continue;
            //}
            //$transaction = $this->transactions->get_transaction($operation->transaction_id);
            //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operation->id, $transaction->prolongation);echo '</pre><hr />';

            //exit;
                        
            if ($card->sent_status != 0) {
                continue;
            }

            $resp = $this->onec->send_rub($card);
        
            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($card->id, $resp);echo '</pre><hr />';

            //exit;

            if (!empty($resp) && $resp == 'OK') {
                $this->cards->update_card($card->id, [
                    'sent_status' => 2,
                    'sent_date' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->cards->update_card($card->id, [
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
        (new SendRubToOneC())->run();
        file_put_contents($lockfile, getmypid());
    } else {
        exit('Another instance of the script is already running.');
    }
}

