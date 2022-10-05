<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class ExchangePaymentCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->send_payments();
    }
    
    
    /**
     * ExchangeCron::send_payments()
     * Отправляем оплаты
     * @return void
     */
    private function send_payments()
    {
        if ($operations = $this->operations->get_operations(array('type'=>array('PAY'), 'sent_status' => 0)))
        {
            foreach ($operations as $operation)
            {
                $operation->contract = $this->contracts->get_contract($operation->contract_id);
                $operation->transaction = $this->transactions->get_transaction($operation->transaction_id);
                if ($operation->transaction->insurance_id)
                    $operation->transaction->insurance = $this->insurances->get_insurance($operation->transaction->insurance_id);
            }
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operations);echo '</pre><hr />';            
            $result = $this->soap1c->send_payments($operations);
            
            if (isset($result->return) && $result->return == 'OK')
            {
                foreach ($operations as $operation)
                {
                    $this->operations->update_operation($operation->id, array(
                        'sent_date' => date('Y-m-d H:i:s'),
                        'sent_status' => 2
                    ));
                }
            }

        }
        
        
    }
    


}
new ExchangePaymentCron();