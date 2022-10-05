<?php
error_reporting(-1);
ini_set('display_errors', 'On');


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * Payment1cCron 
 * 
 * импортирует из 1с оплаты, которые были не в црм
 * 
 * @author Ruslan Kopyl
 * @copyright 2021
 * @version $Id$
 * @access public
 */
class Payment1cCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
    
        $this->run();
    }
    
    private function run()
    {
//        $from = date('Y-m-d', strtotime('- 1 days'));
//        $to = date('Y-m-d', strtotime('- 1 days'));

        $from = date('Y-m-d', time() - 432000);
        $to = date('Y-m-d');

//        $from = '2021-07-13';
//        $to = '2021-07-13';
    
        $payments = $this->soap1c->get_payments1c($from, $to);
        if (!empty($payments))
        {
            foreach ($payments as $item)
            {
                $pay_numbers = explode('-', $item->НомерОплаты);
                if (strlen($pay_numbers[1]) != 6)
                {
                    if (!($operation = $this->operations->get_onec_operation($item->НомерОплаты)))
                    {
                        if ($contract = $this->contracts->get_number_contract($item->НомерЗайма))
                        {
                            $closed = 0;
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract, $item);echo '</pre><hr />';

                            $date = DateTime::createFromFormat('YmdHis', $item->ДатаОплаты);
                            $format_date = $date->format('Y-m-d H:i:s');
                            $amount = $item->СуммаОД + $item->СуммаПроцентов + $item->СуммаПени + $item->СуммаОтветственности;

                            $transaction = array(
                                'user_id' => $contract->user_id,
                                'amount' => $amount * 100,
                                'sector' => 0,
                                'register_id' => 0,
                                'reference' => $item->НомерОплаты,
                                'description' => 'Импорт оплаты из 1С',
                                'created' => $format_date,
                                'operation' => 0,
                                'reason_code' => 1,
                                'body' => serialize($item),
                                'callback_response' => '',
                                'sms' => 0,
                                'prolongation' => (int)$item->Пролонгация,
                                'insurance_id' => '',
                                'loan_body_summ' => $item->СуммаОД,
                                'loan_percents_summ' => $item->СуммаПроцентов,
                                'loan_charge_summ' => $item->СуммаОтветственности,
                                'loan_peni_summ' => $item->СуммаПени,
                                'commision_summ' => 0,
                            );
                            $transaction_id = $this->transactions->add_transaction($transaction);
                            
                            $operation = array(
                                'contract_id' => $contract->id,
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'transaction_id' => $transaction_id,
                                'type' => 'PAY',
                                'amount' => $amount,
                                'created' => $format_date,
                                'sent_status' => 3,
                                'sent_date' => $format_date,
                                'loan_body_summ' => 0,
                                'loan_percents_summ' => 0,
                                'loan_charge_summ' => 0,
                                'loan_peni_summ' => 0, 
                                'number_onec' => $item->НомерОплаты
                            );
                            $operation_id = $this->operations->add_operation($operation);
                            
                            $update_contract = array(
                                'loan_body_summ' => $contract->loan_body_summ - $item->СуммаОД,
                                'loan_percents_summ' => $contract->loan_percents_summ - $item->СуммаПроцентов,
                                'loan_charge_summ' => $contract->loan_charge_summ - $item->СуммаОтветственности,
                                'loan_peni_summ' => $contract->loan_peni_summ - $item->СуммаПени,
                                
                                
                            );
                            if (intval($item->Пролонгация))
                            {
                                $update_contract['prolongation'] = $contract->prolongation + intval($item->Пролонгация);
                                $update_contract['return_date'] = date('Y-m-d H:i:s', strtotime('+ 14 days'));
                                $update_contract['status'] = 2;
                                $update_contract['collection_status'] = 0;
                                $update['collection_manager_id'] = 0;
                                $update['collection_workout'] = 0;
                            }
                            
                            if ($update_contract['loan_body_summ'] <= 0 && $update_contract['loan_percents_summ'] <= 0 && $update_contract['loan_peni_summ'] <= 0 )
                            {
                                $update_contract['close_date'] = $format_date;
                                $update_contract['status'] = 3;
                                $update_contract['loan_body_summ'] = 0;
                                $update_contract['loan_percents_summ'] = 0;
                                $update_contract['loan_charge_summ'] = 0;
                                $update_contract['loan_peni_summ'] = 0;
                                $update_contract['collection_status'] = 0;
                                $update_contract['collection_manager_id'] = 0;
                                $update_contract['collection_workout'] = 0;
                                
                                $closed = 1;
                            
                                $this->orders->update_order($contract->order_id, array('status' => 7));
                            }

                            $this->contracts->update_contract($contract->id, $update_contract);
                            
                            if (!empty($contract->collection_manager_id))
                            {
                                $add_collection = array(
                                    'transaction_id' => $transaction_id,
                                    'manager_id' => $contract->collection_manager_id,
                                    'contract_id' => $contract->id,
                                    'created' => $format_date,
                                    'body_summ' => $item->СуммаОД,
                                    'percents_summ' => $item->СуммаПроцентов,
                                    'charge_summ' => $item->СуммаОтветственности,
                                    'peni_summ' => $item->СуммаПени,
                                    'commision_summ' => 0,
                                    'closed' => $closed,
                                    'prolongation' => (int)$item->Пролонгация,
                                    'collection_status' => $contract->collection_status,
                                );
                                $this->collections->add_collection($add_collection);
                            }
                            
                        }
                        else
                        {
                            echo 'Договор не найден '.$item->НомерЗайма;
                        }
                    }
                }
            }
        }
    }    
    
}

$cron = new Payment1cCron();
