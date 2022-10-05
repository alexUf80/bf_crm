<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class ExchangeOperationsCron extends Core
{
    private $date = '2021-06-01';
    
    public function __construct()
    {
    	parent::__construct();
        
        $this->send_percents();
        $this->send_charges();
    }
    
    /**
     * ExchangeCron::send_percents()
     * отправляем начисление процентов
     * 
     * @return void
     */
    private function send_percents()
    {
        // проценты
        $params = array(
            'limit' => 1000000, 
            'type' => array('PERCENTS'), 
            'sent_status' => 0,
        );
        if ($operations = $this->operations->get_operations($params))
        {            
            $contract_ids = array();
            foreach ($operations as $operation)
                $contract_ids[] = $operation->contract_id;
            
            $contracts = array();
            if (!empty($contract_ids))
            {
                foreach ($this->contracts->get_contracts(array('id' => $contract_ids)) as $cont)
                    $contracts[$cont->id] = $cont;
            }
            
            foreach ($operations as $o)
            {
                if (isset($contracts[$o->contract_id]))
                    $o->contract = $contracts[$o->contract_id];
            }
            $result = $this->soap1c->send_operations($operations);

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
    
    /**
     * ExchangeCron::send_charges()
     * отправляем начисление просрочки, пеней
     * 
     * @return void
     */
    private function send_charges()
    {
        // просрочки
        $params = array(
            'limit' => 10000, 
            'type'=>array('CHARGE', 'PENI'), 
            'sent_status' => 0,
        );
        if ($operations = $this->operations->get_operations($params))
        {            
            $contract_ids = array();
            foreach ($operations as $operation)
                $contract_ids[] = $operation->contract_id;
            
            $contracts = array();
            if (!empty($contract_ids))
            {
                foreach ($this->contracts->get_contracts(array('id' => $contract_ids)) as $cont)
                    $contracts[$cont->id] = $cont;
            }
            
            foreach ($operations as $o)
            {
                if (isset($contracts[$o->contract_id]))
                    $o->contract = $contracts[$o->contract_id];
            }
            $result = $this->soap1c->send_operations($operations);

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
    
    private function send_operations_OLD()
    {
        if ($operations = $this->operations->get_operations(array('type'=>array('PERCENTS', 'CHARGE', 'PENI'), 'sent_status' => 0)))
        {
            $send_operations = array();
            
            foreach ($operations as $operation)
            {
                $send_operations[$operation->contract_id] = $operation;
            }
            
            foreach ($send_operations as $operation)
            {
                $operation->contract = $this->contracts->get_contract($operation->contract_id);
            }
            
            
            $result = $this->soap1c->send_operations($send_operations);
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
new ExchangeOperationsCron();