<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * отправляем в 1c 
 */
class SendProlongationInsurancesToOneC extends Core
{
    public function __construct()
    {
    	parent::__construct();
    }
    
    public function run()
    {
        exit;
        $filter = [
            'date_from' => date('Y-m-d', strtotime('2022-01-03')), //todo
            'date_to' => date('Y-m-d', strtotime('2022-08-20')), //todo
        ];

        if (!empty($filter['date_from']))
            $date_from_filter = $this->db->placehold("AND DATE(op.created) >= ?", $filter['date_from']);

        if (!empty($filter['date_to']))
            $date_to_filter = $this->db->placehold("AND DATE(op.created) <= ?", $filter['date_to']);

        $query = $this->db->placehold("
        select tr.prolongation, 
        tr.loan_body_summ as loan_body_summ, 
        tr.loan_percents_summ as loan_percents_summ,
        tr.loan_charge_summ as loan_charge_summ,
        tr.loan_peni_summ as loan_peni_summ,
        op.loan_body_summ as op_loan_body_summ, 
        op.loan_percents_summ as op_loan_percents_summ,
        tr.sector,
        op.*
        from s_operations as op
        join s_transactions as tr on tr.id = op.transaction_id 
        where op.type = 'INSURANCE'
        and tr.prolongation = 1
        and op.sent_status = 0
        $date_from_filter
        $date_to_filter
        limit 9
        ");

        $this->db->query($query);

        $operations = $this->db->results();

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(count($operations));echo '</pre><hr />';
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

            $order = $this->orders->get_order($operation->order_id);

            //if (empty($order->id_deal)) {
            //    continue;
            //}
            
            if ($operation->sent_status != 0) {
                continue;
            }

            $resp = $this->onec->send_prolongation_insurance($operation);
        
            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operation->id, $resp);echo '</pre><hr />';

            //exit;

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
        (new SendProlongationInsurancesToOneC())->run();
        file_put_contents($lockfile, getmypid());
    } else {
        exit('Another instance of the script is already running.');
    }
}

