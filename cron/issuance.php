<?php
error_reporting(-1);
ini_set('display_errors', 'On');


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__).'/../');

require 'autoload.php';

/**
 * IssuanceCron
 * 
 * Скрипт выдает кредиты, и списывает страховку
 * 
 * @author Ruslan Kopyl
 * @copyright 2021
 * @version $Id$
 * @access public
 */
class IssuanceCron extends Core
{
    public function __construct()
    {
    	parent::__construct();
        
        file_put_contents($this->config->root_dir.'cron/log.txt', date('d-m-Y H:i:s').' Issuance RUN'.PHP_EOL, FILE_APPEND);
        
        $i = 0;
        while ($i < 5)
        {
            $this->run();
            $i++;
        }
    }
    
    private function run()
    {
        if ($contracts = $this->contracts->get_contracts(array('status' => 1, 'limit' => 1)))
        {
            
            foreach ($contracts as $contract)
            {
                $amount = intval($contract->amount * 100);
                
                $res = $this->best2pay->pay_contract($contract->id);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump(htmlspecialchars($res));echo '</pre><hr />';                
                if ($res == 'COMPLETED')
                {
                    $ob_date = new DateTime();
                    $ob_date->add(DateInterval::createFromDateString($contract->period.' days'));
                    $return_date = $ob_date->format('Y-m-d H:i:s');

                    $this->contracts->update_contract($contract->id, array(
                        'status' => 2, 
                        'inssuance_date' => date('Y-m-d H:i:s'),
                        'loan_body_summ' => $contract->amount,
                        'loan_percents_summ' => 0,
                        'return_date' => $return_date,
                    ));
                    
                    $this->orders->update_order($contract->order_id, array('status'=>5));
                    
                    $this->operations->add_operation(array(
                        'contract_id' => $contract->id,
                        'user_id' => $contract->user_id,
                        'order_id' => $contract->order_id,
                        'type' => 'P2P',
                        'amount' => $contract->amount,
                        'created' => date('Y-m-d H:i:s'),
                    ));
                    
                    
                    //TODO: Индивидуальные условия
                    $this->create_document('IND_USLOVIYA_NL', $contract);
                    $this->create_document('ANKETA_PEP', $contract);

                    $this->create_document('SOLGLASHENIE_PEP', $contract);
                    $this->create_document('SOGLASIE_VZAIMODEYSTVIE', $contract);
                    $this->create_document('SOGLASIE_MEGAFON', $contract);
                    $this->create_document('SOGLASIE_SCORING', $contract);
                    $this->create_document('SOGLASIE_SPISANIE', $contract);
                    $this->create_document('SOGLASIE_OPD', $contract);

                    if(!empty($contract->order_id)){
                        $order = $this->orders->get_order($contract->order_id);

                        if (!empty($order->utm_source) && $order->utm_source == 'click2money') {
                            if (in_array($order->client_status, ['nk', 'rep'])) {
                                try {
                                    $this->leadgens->send_approved_postback_click2money($order->order_id, $order);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                            }
                        }

                        if (!empty($order->utm_source) && $order->utm_source == 'unicom24') {
                            if (in_array($order->client_status, ['nk', 'rep'])) {
                                try {
                                    $this->UnicomLeadgen->send_approve_postback($order->order_id, $order);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                            }
                        }
                    }

                    // Снимаем страховку если есть
                    if (!empty($contract->service_insurance))
                    {
                        $insurance_summ = $this->insurances->get_insurance_cost($contract->amount);
                        $insurance_amount = $insurance_summ * 100;
                        
                        $description = 'Страховой полис';
                        
                        $response = $this->best2pay->recurrent($contract->card_id, $insurance_amount, $description);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump('Страховой полис', htmlspecialchars($response));echo '</pre><hr />';                
                        
                        $xml = simplexml_load_string($response);
                        $status = (string)$xml->state;
                
                        if ($status == 'APPROVED')
                        {
                            $transaction = $this->transactions->get_operation_transaction($xml->order_id, $xml->id);
                            
                            $contract = $this->contracts->get_contract($contract->id);
                            
                            $payment_amount = $insurance_amount / 100;
                            
                            $operation_id = $this->operations->add_operation(array(
                                'contract_id' => $contract->id,
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'type' => 'INSURANCE',
                                'amount' => $payment_amount,
                                'created' => date('Y-m-d H:i:s'),
                                'transaction_id' => $transaction->id,
                            ));
                            
                            $close_contracts = $this->contracts->get_contracts(array('user_id' => $contract->user_id, 'status' => 3));
                            
                            $protection = 0; //count($close_contracts) == 1;
                            
                            $insurance_id = $this->insurances->add_insurance(array(
                                'amount' => $payment_amount,
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'create_date' => date('Y-m-d H:i:s'),
                                'start_date' => date('Y-m-d 00:00:00', time() + (1 * 86400)),
                                'end_date' => date('Y-m-d 23:59:59', time() + (31 * 86400)),
                                'operation_id' => $operation_id,
                                'protection' => $protection,
                            ));
                            
                            $this->contracts->update_contract($contract->id, array(
                                'insurance_id' => $insurance_id
                            ));
                            
                            $order = $this->orders->get_order((int)$contract->order_id);
                            
                            
                            $contract->insurance_id = $insurance_id;
                            //Заявление на страхование
                            $this->create_document('DOP_USLUGI_VIDACHA', $contract);

                            //Страховой полиc
                            $this->create_document('POLIS_STRAHOVANIYA', $contract);

                            
                            //Отправляем чек по страховке
                            $this->ekam->send_insurance($operation_id);
                            $this->operations->update_operation($operation_id, array('sent_receipt'=>1));
                            
                            
                        }
                        else
                        {
                            
                        }
                    }
                    
                    // Снимаем будь в курсе
                    if (!empty($contract->service_sms))
                    {
                        $service_summ = $this->settings->service_sms_cost;
                        $service_amount = $service_summ * 100;
                        
                        $description = 'Услуга "Будь в курсе"';
                        
                        $response = $this->best2pay->recurrent($contract->card_id, $service_amount, $description);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump('Услуга "Будь в курсе"', htmlspecialchars($response));echo '</pre><hr />';                
                        
                        $xml = simplexml_load_string($response);
                        $status = (string)$xml->state;
                
                        if ($status == 'APPROVED')
                        {
                            $transaction = $this->transactions->get_operation_transaction($xml->order_id, $xml->id);
                            
                            $contract = $this->contracts->get_contract($contract->id);
                            
                            $payment_amount = $service_amount / 100;
                            
                            $operation_id = $this->operations->add_operation(array(
                                'contract_id' => $contract->id,
                                'user_id' => $contract->user_id,
                                'order_id' => $contract->order_id,
                                'type' => 'BUD_V_KURSE',
                                'amount' => $payment_amount,
                                'created' => date('Y-m-d H:i:s'),
                                'transaction_id' => $transaction->id,
                            ));
                            
                            
                            $order = $this->orders->get_order((int)$contract->order_id);
                            
                            //Отправляем чек по страховке
                            $this->ekam->send_bud_v_kurse($contract->order_id);
                            $this->operations->update_operation($operation_id, array('sent_receipt'=>1));
                            
                        }
                        else
                        {
                            
                        }
                    }

                }
                else
                {
                    $this->contracts->update_contract($contract->id, array('status' => 6));

                    $this->orders->update_order($contract->order_id, array('status' => 6)); // статус 6 - не удалосб выдать
                
                }
                
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($res);echo '</pre><hr />';                
            }
        }
    }
    
    public function create_document($document_type, $contract)
    {
        $ob_date = new DateTime();
        $ob_date->add(DateInterval::createFromDateString($contract->period.' days'));
        $return_date = $ob_date->format('Y-m-d H:i:s');

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $this->orders->get_order((int)$contract->order_id);
        $contract_user = $this->users->get_user((int)$contract->user_id);
        
        
        $passport = str_replace([' ','-'], '', $contract_order->passport_serial);
        $passport_serial = substr($passport, 0, 4);
        $passport_number = substr($passport, 4, 6);
        
        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'gender' => $contract_order->gender,
            'number' => $contract->number,
            'contract_date' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
            'return_date' => $return_date,
            'return_date_day' => date('d', strtotime($return_date)),
            'return_date_month' => date('m', strtotime($return_date)),
            'return_date_year' => date('Y', strtotime($return_date)),
            'return_amount' => $return_amount,
            'return_amount_rouble' => $return_amount_rouble,
            'return_amount_kop' => $return_amount_kop,
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'period' => $contract->period,
            'return_amount_percents' => round($contract->amount * $contract->base_percent * $contract->period / 100, 2),
            'passport_serial' => $contract_order->passport_serial,
            'passport_date' => $contract_order->passport_date,
            'subdivision_code' => $contract_order->subdivision_code,
            'passport_issued' => $contract_order->passport_issued,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $contract->accept_code,
            'insurance_summ' => $this->insurances->get_insurance_cost($contract->amount),

            'passport_serial' => $passport_serial,
            'passport_number' => $passport_number,
            'passport_date' => $contract_user->passport_date,
            'passport_code' => $contract_user->subdivision_code,
            'passport_issued' => $contract_user->passport_issued,
            
            'regindex' => $contract_user->Regindex,
            'regregion' => $contract_user->Regregion,
            'regcity' => $contract_user->Regcity,
            'regstreet' => $contract_user->Regstreet,
            'reghousing' => $contract_user->Reghousing,
            'regbuilding' => $contract_user->Regbuilding,
            'regroom' => $contract_user->Regroom,
            'faktindex' => $contract_user->Faktindex,
            'faktregion' => $contract_user->Faktregion,
            'faktcity' => $contract_user->Faktcity,
            'faktstreet' => $contract_user->Faktstreet,
            'fakthousing' => $contract_user->Fakthousing,
            'faktbuilding' => $contract_user->Faktbuilding,
            'faktroom' => $contract_user->Faktroom,

            'profession' => $contract_user->profession,
            'workplace' => $contract_user->workplace,
            'workphone' => $contract_user->workphone,
            'chief_name' => $contract_user->chief_name,
            'chief_position' => $contract_user->chief_position,
            'chief_phone' => $contract_user->chief_phone,
            'income' => $contract_user->income,
            'expenses' => $contract_user->expenses,
            
            'create_date' => date('Y-m-d H:i:s'),
        );
        $regaddress_full = empty($contract_order->Regindex) ? '' : $contract_order->Regindex.', ';
        $regaddress_full .= trim($contract_order->Regregion.' '.$contract_order->Regregion_shorttype);
        $regaddress_full .= empty($contract_order->Regcity) ? '' : trim(', '.$contract_order->Regcity.' '.$contract_order->Regcity_shorttype);
        $regaddress_full .= empty($contract_order->Regdistrict) ? '' : trim(', '.$contract_order->Regdistrict.' '.$contract_order->Regdistrict_shorttype);
        $regaddress_full .= empty($contract_order->Reglocality) ? '' : trim(', '.$contract_order->Reglocality.' '.$contract_order->Reglocality_shorttype);
        $regaddress_full .= empty($contract_order->Reghousing) ? '' : ', д.'.$contract_order->Reghousing;
        $regaddress_full .= empty($contract_order->Regbuilding) ? '' : ', стр.'.$contract_order->Regbuilding;
        $regaddress_full .= empty($contract_order->Regroom) ? '' : ', к.'.$contract_order->Regroom;

        $params['regaddress_full'] = $regaddress_full;

        if (!empty($contract->insurance_id))
        {
            $params['insurance'] = $this->insurances->get_insurance($contract->insurance_id);
        }
        

        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => $params,                
        ));

    }
    
}

$cron = new IssuanceCron();
