<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('max_execution_time', 180);

chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * отправляем в 1с заявки и займы
 */
class SendIssuedLoansToOneC extends Core
{
    public function __construct()
    {
    	parent::__construct();
    }
    
    public function run()
    {
        $contracts = $this->contracts->get_contracts([
            'inssuance_date_from' => '2022-06-06',
            'status' => [2, 3, 4],
            'sent_status' => 0,
            'limit' => 30,
        ]);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contracts);echo '</pre><hr />';
        //exit;

        if (empty($contracts)) {
            return false;
        }

        foreach ($contracts as $contract) {
            $order_id = $contract->order_id;
            $contract_id = $contract->id;

            $response = $this->onec->send_order($order_id);

            if (empty($response)) {
                $this->contracts->update_contract($contract_id, [
                    'sent_status' => 3,
                    'sent_date' => date('Y-m-d H:i:s'),
                ]);

                continue;
            }

            if (!empty($response->IDDeal) && !empty($response->IDClient) && $response->result == 1) {
                // номер сделки и ид клиента
                $this->contracts->update_contract($contract_id, [
                    'sent_status' => 2,
                    'sent_date' => date('Y-m-d H:i:s'),
                ]);

                $resp = $this->onec->send_pdn($order_id);

                echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
            } elseif ($response->result == 0 && $response->description == 'Контрагент с такими данными уже имеется в базе.') {
                $this->contracts->update_contract($contract_id, [
                    'sent_status' => 3,
                    'sent_date' => date('Y-m-d H:i:s'),
                ]);
            } elseif ($response->result == 0) {
                $this->contracts->update_contract($contract_id, [
                    'sent_status' => 3,
                    'sent_date' => date('Y-m-d H:i:s'),
                ]);
            }
    
            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($response);echo '</pre><hr />';
        }
    }
}

(new SendIssuedLoansToOneC())->run();
