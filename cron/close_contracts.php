<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * CloseContractsCron
 * Закрывает договра с нулевым остатком долга
 * 
 * @author Kopyl Ruslan
 * @copyright 2021
 * @access public
 */
class CloseContractsCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        $this->run();
    }
    
    private function run()
    {
    	$this->db->query("
            SELECT * 
            FROM __contracts AS c
            WHERE c.type = 'base'
            AND status IN (2, 4, 7)
            AND loan_body_summ = 0
            AND loan_percents_summ = 0
            AND loan_charge_summ = 0
            AND loan_peni_summ = 0
            
        ");
        if ($results = $this->db->results())
        {
            foreach ($results as $contract)
            {
                $operations = $this->operations->get_operations(array('contract_id' => $contract->id, 'type' => 'PAY'));
                $last_operation = NULL;
                foreach ($operations as $operation)
                {
                    if (empty($last_operation) || strtotime($last_operation->created) > strtotime($operation->transaction))
                    {
                        $last_operation = $operation;
                    }
                }
                
                $this->orders->update_order($contract->order_id, array(
                    'status' => 7
                ));
                
                $this->contracts->update_contract($contract->id, array(
                    'status' => 3,
                    'close_date' => $last_operation->created,
                    'collection_status' => 0
                ));
                
                $this->comments->add_comment(array(
                    'order_id' => $contract->order_id,
                    'user_id' => $contract->user_id,
                    'contactperson_id' => 0,
                    'manager_id' => 2,
                    'text' => 'Закрыт вручную через крон. Долг по договору равен нулю',
                    'created' => date('Y-m-d H:i:s'),
                ));
            }
            
            $mail = '<html>';
            $mail .= '<body>';
            $mail .= '<h1>Закрытые договора с нулевым балансом '.date('d.m.Y').'</h1>';
            $mail .= '<table>';
            foreach ($results as $contract):
            $mail .= '<tr>';
            $mail .= '<td><a href="'.$this->config->root_url.'/order/'.$contract->order_id.'" target="_blank">'.$contract->number.'</a></td>';
            $mail .= '</tr>';
            endforeach;
            $mail .= '</table>';
            $mail .= '</body>';
            $mail .= '</html>';

            $subject = 'Закрытые договора с нулевым балансом ';
            
            $this->notify->email('alpex-s@rambler.ru', $subject, $mail, $this->settings->notify_from_email);

echo $mail;
        }
        
//        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';
    }
}
new CloseContractsCron();