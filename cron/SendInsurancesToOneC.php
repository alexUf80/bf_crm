<?php
error_reporting(-1);
ini_set('display_errors', 'On');

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * отправляем в 1c 
 */
class SendInsurancesToOneC extends Core
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
        select 
        op.*,
        cr.inssuance_date,
        cr.close_date,
        op.created,
        op.`type`
        from s_operations as op
        join s_contracts as cr on cr.id = op.contract_id 
        where op.type = 'INSURANCE'
        and op.sent_status = 0
        $date_from_filter
        $date_to_filter
        limit 9
        ");

        $this->db->query($query);

        $operations = $this->db->results();
        #

        if (empty($operations)) {
            return false;
        }

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(count($operations));echo '</pre><hr />';
        //exit;
        #todo
        $operations_insurance_inssuance = [];
        $operations_insurance_close = [];

        foreach ($operations as $operation) {
            $date = date('Y-m-d', strtotime($operation->created));

            $order = $this->orders->get_order($operation->order_id);

            //if (empty($order->id_deal)) {
            //    continue;
            //}
            
            if ($operation->close_date) {
                $close_date = date('Y-m-d', strtotime($operation->close_date));

                if ($date == $close_date && ($operation->amount == 200 || $operation->amount == 400)) {

                    $operations_insurance_close[] = $operation;

                    if ($operation->sent_status != 0) {
                        continue;
                    }

                    $resp = $this->onec->send_closing_insurance($operation);
        
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
                }
            }

            if ($operation->inssuance_date ) {

                $inssuance_date = date('Y-m-d', strtotime($operation->inssuance_date));

                if ($date == $inssuance_date && $operation->type == 'INSURANCE') {
                    
                    $operations_insurance_inssuance[] = $operation;

                    if ($operation->sent_status != 0) {
                        continue;
                    }
                    
                    $resp = $this->onec->send_issuance_insurance($operation);

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
                }
            }
        }
        #

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(count($operations_insurance_inssuance), count($operations_insurance_close));echo '</pre><hr />';
    }
}

$lockfile = sys_get_temp_dir() . '/' . md5(__FILE__) . '.lock';
$pid = file_exists($lockfile) ? trim(file_get_contents($lockfile)) : null;

if (isset($argv[1])) {
    parse_str($argv[1], $params);
}

if ((isset($params['code']) && $params['code'] == 'f45beT4Hs') || (isset($_GET['code']) && $_GET['code'] == 'f45beT4Hs')) {
    if (is_null($pid) || posix_getsid($pid) === false) {
        (new SendInsurancesToOneC())->run();
        file_put_contents($lockfile, getmypid());
    } else {
        exit('Another instance of the script is already running.');
    }
}

