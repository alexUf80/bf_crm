<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

class ExchangeCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        for ($i = 0; $i < 5; $i++)
            $this->send_contracts();
            
        for ($i = 0; $i < 5; $i++)
            $this->send_comments();
            
            // отключил метод, дублируется с send_percents и send_charges
            //$this->send_operations();

        for ($i = 0; $i < 5; $i++)
            $this->send_percents();
        for ($i = 0; $i < 5; $i++)
            $this->send_charges();
        for ($i = 0; $i < 5; $i++)
            $this->send_payments();
//echo '<meta http-equiv="refresh" content="3">';
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
            'limit' => 10000, 
            'type' => array('PERCENTS'), 
            'sent_status' => 0,
//            'date_from' => '2021-07-28',
//            'date_to' => '2021-07-28',
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

    /**
     * ExchangeCron::send_operations()
     * отправляем начисление процентов, пеней
     * 
     * @return void
     */
    private function send_operations()
    {
//        $date = '2021-05-06';
        if ($operations = $this->operations->get_operations(array('limit' => 100, 'type'=>array('PERCENTS', 'CHARGE', 'PENI'), 'sent_status' => 0, /*'date_from'=>$date,'date_to'=>$date*/)))
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
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($result);echo '</pre><hr />';
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operations);echo '</pre><hr />';            
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
    
    /**
     * ExchangeCron::send_payments()
     * Отправляем оплаты
     * @return void
     */
    private function send_payments()
    {
        if ($operations = $this->operations->get_operations(array(/*'page' => 2,*/ 'limit' => 5, 'type'=>array('PAY'), 'sent_status' => 0, 'sort'=>'id_desc')))
        {
            foreach ($operations as $operation)
            {
                $operation->contract = $this->contracts->get_contract($operation->contract_id);
                $operation->transaction = $this->transactions->get_transaction($operation->transaction_id);
                if ($operation->transaction->insurance_id)
                    $operation->transaction->insurance = $this->insurances->get_insurance($operation->transaction->insurance_id);
            }
            
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
            else
            {
                foreach ($operations as $operation)
                {
                    $this->operations->update_operation($operation->id, array(
//                        'sent_date' => date('Y-m-d H:i:s'),
//                        'sent_status' => 6
                    ));
                }
                
            }

echo __FILE__.' '.__LINE__.'<br /><pre>';echo date('H:i:s').PHP_EOL;var_dump($result);echo '</pre><hr />';            
        }
        
        
    }
    
    /**
     * ExchangeCron::send_contracts()
     * Отправляем выданные займы
     * @return void
     */
    private function send_contracts()
    {
        $border_date = date('Y-m-d H:i:s', time() - 600);
        
        $contract_filter_params = array(
            'sent_status' => 0, 
            'status' => array(2, 3, 4), 
            'limit' => 5,
            'inssuance_datetime_to' => $border_date
        );
        if ($contracts = $this->contracts->get_contracts($contract_filter_params))
        {
            foreach ($contracts as $contract)
            {
                $contract->user = $this->users->get_user((int)$contract->user_id);
                $contract->order = $this->orders->get_order((int)$contract->order_id);
                $contract->p2pcredit = $this->best2pay->get_contract_p2pcredit($contract->id);
                if (!empty($contract->insurance_id))
                {
                    $contract->insurance = $this->insurances->get_insurance($contract->insurance_id);
                    $contract->insurance->operation = $this->operations->get_operation($contract->insurance->operation_id);
                    $contract->insurance->transaction = $this->transactions->get_transaction($contract->insurance->operation->transaction_id);
                }
            }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contracts);echo '</pre><hr />';

            
            $result = $this->soap1c->send_contracts($contracts);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($result);echo '</pre><hr />';
            if (isset($result->return) && $result->return == 'OK')
            {
                foreach ($contracts as $contract)
                {
                    $this->contracts->update_contract($contract->id, array(
                        'sent_date' => date('Y-m-d H:i:s'),
                        'sent_status' => 2
                    ));
                }
            }

        }
    }
    
    /**
     * ExchangeCron::send_comments()
     * Отправка комментариев в 1с (отправляем комментарии только по тем заявкам где есть выданные кредиты)
     * 
     * @return void
     */
    private function send_comments()
    {
        if ($comments = $this->comments->get_comments(array('not_sent'=>1)))
        {
            $send_comments = array();
            foreach ($comments as $comment)
            {
                if (!empty($comment->contactperson_id))
                {
                    $comment->contactperson = $this->contactpersons->get_contactperson((int)$comment->contactperson_id);
                }
                
                $comment_contract = $this->contracts->get_order_contract($comment->order_id);
                if (empty($comment_contract))
                {
                    if ($comment->status == 1)
                        $this->comments->update_comment($comment->id, array('status' => 3));
                    else
                        $this->comments->update_comment($comment->id, array('status' => 1));
                }
                else
                {
                    // 2 => 'Выдан', 3 => 'Закрыт', 4 => 'Просрочен',
                    if (in_array($comment_contract->status, array(2, 3, 4))) 
                    {
                        $comment->contract = $comment_contract;
                        $send_comments[] = $comment;                        
                    }
                    // 5 => 'Истек срок подписания', 6 => 'Не удалось выдать займ',
                    elseif (in_array($comment_contract->status, array(5, 6)))
                    {
                        $this->comments->update_comment($comment->id, array('status' => 3));
                    }
                }
                
            }
            
            if (!empty($send_comments))
            {
                $result = $this->soap1c->send_comments($send_comments);
                if (isset($result->return) && $result->return == 'OK')
                {
                    foreach ($send_comments as $comment)
                    {
                        $this->comments->update_comment($comment->id, array(
                            'sent' => date('Y-m-d H:i:s'),
                            'status' => 2
                        ));
                    }
                }
            }
        }
    }


}
new ExchangeCron();