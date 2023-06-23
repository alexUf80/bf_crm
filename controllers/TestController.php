<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
    {

        // $inssuance_date1 = new DateTime('2023-04-20');
        // $days_to_add_taxing1 = $inssuance_date1->diff(new DateTime);
        // var_dump($days_to_add_taxing1->days);




        // $this->db->query("
        //         SELECT
        //         id,
        //         user_id,
        //         amount,
        //         register_id
        //         FROM s_transactions
        //         WHERE `description` = 'Привязка карты'
        //         AND reason_code = 1
        //         and checked = 0
        //         and user_id = ?
        //         order by id desc
        //         ", 27611);

        // $transaction = $this->db->result();

        // $res = $this->Best2pay->completeCardEnroll($transaction);

        // var_dump($res);

        // $token = "5833867523:AAFvftA-3DbWoc4I-SnJsG-nS8OCLB7covE";
        // $chat_id = -614839882;




        // !!!!!!!!!!!!!!!!!!!!!!!!!!
        
        $text = '<b>Превышения в 1.5 раза</b>';
        echo '<b>Превышения в 1.5 раза</b><br>';
        $text .= PHP_EOL;

        $orders = $this->orders->get_orders(array('status' => 5));
        
        $i = 0;
        foreach ($orders as $order) {
            if($order->order_id !=  34167)
                continue;
            
            $contract = $this->contracts->get_contract($order->contract_id);

            if (!isset($contract->amount)) 
                continue;

            // $contract_amount = $contract->amount;

            $amount = 0;
            // $operations = $this->operations->get_operations(array('order_id' => $order->order_id, 'type' => 'PERCENTS'));
            $this->db->query("
            SELECT *
            FROM __operations
            WHERE (type = 'PERCENTS' || type = 'PENI') 
            #WHERE (type = 'PERCENTS') 
            AND order_id =?
            group by created desc, type asc",$order->order_id);
            $operations = $this->db->results();

            $PER = 0;
            foreach ($operations as $operation) {
                $amount += $operation->amount;
                if ($operation->type == 'PERCENTS' && $operation->amount>0) {
                    $PER = $operation->amount;
                }
            }

            foreach ($operations as $operation) {
                if ($operation->type == 'PERCENTS' && $operation->amount>0) {
                    var_dump($operation->id,$operation->created,$operation->amount);
                    break;
                }
            }
            
            $amountP2P = 0;
            $operationsP2P = $this->operations->get_operations(array('order_id' => $order->order_id, 'type' => 'P2P'));
            foreach ($operationsP2P as $operationP2P) {
                $amountP2P += $operationP2P->amount;
            }

            // $amountP2P = $contract_amount;

            $j = 0;
            if($amountP2P*1.5 < $amount){
                $i++;
                $j++;
                // var_dump($order->order_id, $amountP2P, $amountP2P*1.5, $amount, $amount - $amountP2P*1.5);
                // echo '<hr>';

                // if ($PER<round($amount - $amountP2P*1.5, 2)) {
                    $text .= $i.' <a href="'.$this->config->back_url.'/order/'.$order->order_id . '">' . $order->order_id . ' - '. round($amount - $amountP2P*1.5, 2).'</a>';
                    echo $i.' <a href="'.$this->config->back_url.'/order/'.$order->order_id . '">' . $order->order_id . ' - '. round($amount - $amountP2P*1.5, 2).'</a> '.($PER>round($amount - $amountP2P*1.5, 2)?'<span style="color:red">!!!</span>':'').'('.$PER.')'.$amount .', ' . $amountP2P . '\\\stop-'.$contract->stop_profit.'<br>';
                    $text .= PHP_EOL;
                // }
            }
        }

        if($i == 0){
            $text = '<b>Ошибок превышения в 1.5 раза нет</b>';
            echo'<b>Ошибок превышения в 1.5 раза нет</b><br>';
        }

        // // $this->send_message($token, $chat_id, $text);




        // // Не начислено
        // $text = '<b>Не хватает начислений</b>';
        // echo  '<br><b>Не хватает начислений</b><br>';
        // $text .= PHP_EOL;

        // $j = 0;

        // foreach ($orders as $order) {

        //     // if($order->order_id == 42499 || $order->order_id == 40968 || $order->order_id ==  45756 || $order->order_id == 40913)
        //     //     continue;
            
        //     $contract = $this->contracts->get_contract($order->contract_id);

        //     if (!isset($contract->amount)) 
        //         continue;

        //     $contract_amount = $contract->amount;


        //     $inssuance_date = new DateTime(date('Y-m-d', strtotime($contract->inssuance_date)));
        //     // var_dump($inssuance_date->date);
        //     // var_dump($contract->inssuance_date);
        //     $days_to_add_taxing = $inssuance_date->diff(new DateTime(date('Y-m-d')));


        //     $amount = 0;
        //     // $operations = $this->operations->get_operations(array('order_id' => $order->order_id, 'type' => 'PERCENTS'));

        //     $this->db->query("
        //     SELECT *
        //     FROM __operations 
        //     WHERE order_id = ?
        //     # AND (type = 'PERCENTS' OR type = 'PENI')
        //     AND (type = 'PERCENTS')
        //     ",$order->order_id);

        //     $operations = $this->db->results();
            
        //     usort($operations, function ($a, $b) {
        //         return strtotime($a->created) - strtotime($b->created);
        //     });

        //     $no_taxing_days = [];


        //     // $contract_inssuance_date = date('Y-m-d', strtotime($contract->inssuance_date));
        //     // var_dump($contract_inssuance_date);
        //     // $today = date('Y-m-d');
        //     // while ($contract_inssuance_date <= $today) {
        //     //     $contract_inssuance_date = date('Y-m-d', strtotime('+1 day',strtotime($contract_inssuance_date)));
        //     //     var_dump($contract_inssuance_date);
        //     // }
        //     // die;
            
        //     foreach ($operations as $operation) {
        //         $amount += $operation->amount;
        //     }

        //     $amountP2P = 0;
        //     $operationsP2P = $this->operations->get_operations(array('order_id' => $order->order_id, 'type' => 'P2P'));
        //     foreach ($operationsP2P as $operationP2P) {
        //         $amountP2P += $operationP2P->amount;
        //     }
            
        //     if($days_to_add_taxing->days == 0){
        //         continue;
        //     }

        //     $persents_sum = $amountP2P / 100 * $contract->base_percent;

        //     if(round($days_to_add_taxing->days*$persents_sum-$amount,2) > 0 && $days_to_add_taxing->days <= 150){
        //         if (1) {
        //             $j++;
    
        //             $text .= $j.' <a href="'.$this->config->back_url.'/order/'.$order->order_id . '">' . $order->order_id . ' - '. $days_to_add_taxing->days. '*'. $persents_sum.'='. $days_to_add_taxing->days*$persents_sum.' /// '. $amount.'('.round($days_to_add_taxing->days*$persents_sum-$amount,2).')</a>';
        //             echo $j.' <a href="'.$this->config->back_url.'/order/'.$order->order_id . '">' . $order->order_id . ' - '. $days_to_add_taxing->days. ' * '. $persents_sum.'='. $days_to_add_taxing->days*$persents_sum.' /// '. $amount.'('.round($days_to_add_taxing->days*$persents_sum-$amount,2).') /// '.$amount.' = '. $amountP2P.'('. $amountP2P*1.5 .'</a><br>';
        //             $text .= PHP_EOL;
        //             // die;
        //         }
        //     }

        // }

        // if($j == 0){
        //     $text = '<b>Ошибок отсутствия начислений нет</b>';
        //     echo '<b>Ошибок отсутствия начислений нет</b>';
        // }

        // // $this->send_message($token, $chat_id, $text);







        // // Двойные начисления
        // $this->db->query("
        // SELECT 
        //     o.contract_id, 
        //     o.user_id, 
        //     o.order_id, 
        //     o.type,
        //     date_format(o.created,'%d.%m.%Y') as cr, 
        //     count(*) as cnt
        // FROM __operations AS o
        // JOIN __contracts AS c
        // ON o.contract_id = c.id
        // WHERE (o.type='PERCENTS' OR o.type='PENI')
        // AND o.amount>0
        // AND (c.status = 2 OR c.status = 4)
        // GROUP BY 
        //     o.contract_id, 
        //     o.user_id, 
        //     o.order_id, 
        //     o.type,
        //     date_format(o.created,'%d.%m.%Y')
        // HAVING count(*) > 1");

        // $results = $this->db->results();

        // $i = 0;
        // $j = 0;
        // $text = "<b>Двойные начисления</b>";
        // echo "<br><br><b>Двойные начисления</b><br>";
        // $text .= PHP_EOL;
        // foreach ($results as $result) {
        //     $i++;
        //     $j++;
        //     $text .= $j.' <a href="'.$this->config->back_url.'/order/'.$result->order_id . '">' . $result->order_id . ' - '. $result->type . ' - '. $result->cr . ' - '. $result->cnt.'</a>';
        //     echo $j.' <a href="'.$this->config->back_url.'/order/'.$result->order_id . '">' . $result->order_id . ' - '. $result->type . ' - '. $result->cr . ' - '. $result->cnt.'<a><br>';
        //     $text .= PHP_EOL;
        //     if ($i>99) {
        //         // $this->send_message($token, $chat_id, $text);
        //         $i = 0;
        //         $text = "";
        //     }
        // }

        // if($j == 0){
        //     $text = '<b>Двойных начислений нет</b>';
        //     echo '<b>Двойных начислений нет</b><br>';
        // }


        // // $this->send_message($token, $chat_id, $text);





        // // Проверка сумм долга, процентов и пени в операциях

        // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // // $orders = $this->orders->get_orders(array('status' => 5));
        // // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        // $text = '<b>Обратить внимание на начисления</b>';
        // echo  '<br><b>Обратить внимание на начисления</b><br>';
        // $text .= PHP_EOL;
        // $i = 0;
        // foreach ($orders as $order) {
            
        //     // if ($order->order_id !=   36252){
        //     //     continue;
        //     // }

        //     $contract = $this->contracts->get_contract((int)$order->contract_id);
        //     if(!isset($contract)){
        //         continue;
        //     }
        //     if($contract->status == 11){
        //         continue;
        //     }

        //     $this->db->query("
        //     SELECT *
        //     FROM __operations
        //     WHERE (type = 'PERCENTS' || type = 'PAY') 
        //     AND order_id =?
        //     group by created, type asc",$order->order_id);

        //     $operations = $this->db->results();
            
        //     $PAY = false;
        //     $PERCENTS = 0;
        //     $PERCENTS_SUMM = 0;
        //     $PER = 0;

        //     $cou = 0;
        //     $last = 0;
        //     $op = '';
        //     if (count($operations) > 0) {
        //         foreach ($operations as $operation) {
        //              if($operation->type == 'PERCENTS'){
        //                 if ($operation->amount !=0 ) {
        //                     $PERCENTS += $operation->amount;
        //                     $PER = $operation->amount;
        //                     $PERCENTS_SUMM = $operation->loan_percents_summ;
        //                     $cou +=1;
        //                     $last = 'PERCENTS';
        //                     $op = $operation;
        //                     // var_dump($operation->created, $operation->loan_percents_summ, $operation->amount);
        //                     // echo '<hr>';
        //                 }
        //             }
        //             else if($operation->type == 'PAY'){
        //                 $PAY = true;
        //                 $PERCENTS -= $operation->amount;
        //                 $last = 'PAY';
        //                 // $PERCENTS = $operation->loan_percents_summ;

        //             }
                    
        //         }
        //     }
        //     if ((round($PERCENTS,2) != round($PERCENTS_SUMM,2)) || (round($PERCENTS,2) != round($contract->loan_percents_summ,2))){
        //             $i++;
        //             echo $i . ') <a href="'.$this->config->back_url.'/order/'.$order->order_id . '">'.$order->order_id . '</a> Сумма всех операций - '.round($PERCENTS,2) . '; Сумма в последней операции - '. $PERCENTS_SUMM . ' (<b> Разность - ' . round($PERCENTS_SUMM-$PERCENTS,2) . '</b>)' . ' Процентов у контракта -  <span '.($contract->loan_percents_summ<0 ? 'style="color:red"' : '').'>'. $contract->loan_percents_summ. '</span> (<b>' . round($contract->loan_percents_summ-$PERCENTS,2) . '</b>) \\\ last-'.$last.' \\\stop-'.$contract->stop_profit;
        //             // echo $i . ') <a href="'.$this->config->back_url.'/order/'.$order->order_id . '">'.$order->order_id . '</a> - '.round($PERCENTS,2) . ' -- '. $PERCENTS_SUMM . '('.$cou.') (<b>' . round($PERCENTS_SUMM-$PERCENTS,2) . '</b> - <i '.($contract->loan_percents_summ<0 ? 'style="color:orange"' : '').'>' . $PER . '</i>!!!<u>'.round(($PERCENTS_SUMM-$PERCENTS)/$PER,2).'</u>)' . ' --!!-- <span '.($contract->loan_percents_summ<0 ? 'style="color:red"' : '').'>'. $contract->loan_percents_summ. '</span> (<b>' . round($contract->loan_percents_summ-$PERCENTS,2) . '</b>) \\\ last-'.$last.' \\\stop-'.$contract->stop_profit;
        //             echo '<hr>';
                    
        //             // if ($PERCENTS>=0) {
        //             //     $this->operations->update_operation($op->id, ['loan_percents_summ' => $PERCENTS]);
        //             // }
        //             // var_dump($op);

        //             // if ($PERCENTS<0) {
        //             //     $PERCENTS = 0;
        //             // }
        //             // $this->contracts->update_contract($contract->id, ['loan_percents_summ' => $PERCENTS]);
        //         // }
        //     }
            
        // }

        // if($i == 0){
        //     $text = '<b>Ошибок начислений нет</b>';
        //     echo '<br><b>Ошибок начислений нет</b><br>';
        // }



        // $contract = $this->contracts->get_contract(3774);
        // // var_dump($contract);
        // echo '<br><hr>';

        // $amount = $contract->loan_body_summ;
        // $taxing_limit = $amount * 2.5;

        // $this->db->query("
        //     select sum(amount) as sum_taxing
        //     from s_operations
        //     where contract_id = ?
        //     and `type` in ('PERCENTS', 'PENI')
        //     ", $contract->id);
        // $sum_taxing = $this->db->result()->sum_taxing;

        // $current_summ = $contract->loan_body_summ + $sum_taxing;
        // if ($current_summ >= $taxing_limit) {
        //     echo 'sas1';
        // }
        
        
        // $percents_summ = round($contract->loan_body_summ / 100 * $contract->base_percent, 2);

        // if($current_summ + $percents_summ > $taxing_limit)
        // {
        //     echo 'sas2';
        // }


        // if ($contract->status == 4) {
        //     $peni_summ = round((0.05 / 100) * $contract->loan_body_summ, 2);
        //     if($current_summ + $peni_summ + $percents_summ > $taxing_limit)
        //     {
        //         echo 'sas3';
        //     }
        // }




        exit;
    }

    public function send_message($token, $chat_id, $text)
	{
		$getQuery = array(
            "chat_id" 	=> $chat_id,
            "text"  	=> $text,
            "parse_mode" => "html",
        );
        $ch = curl_init("https://api.telegram.org/bot". $token ."/sendMessage?" . http_build_query($getQuery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $resultQuery = curl_exec($ch);
        curl_close($ch);

        echo $resultQuery;
    }

    public function run_scoring($scoring_id)
    {
        $scoring = $this->scorings->get_scoring($scoring_id);
        $order = $this->orders->get_order((int)$scoring->order_id);

        $person =
            [
                'personLastName' => $order->lastname,
                'personFirstName' => $order->firstname,
                'phone' => preg_replace('/[^0-9]/', '', $order->phone_mobile),
                'personBirthDate' => date('d.m.Y', strtotime($order->birth))
            ];

        if (!empty($order->patronymic))
            $person['personMidName'] = $order->patronymic;

        $score = $this->IdxApi->search($person);

        if (empty($score)) {

            $update =
                [
                    'status' => 'error',
                    'body' => '',
                    'success' => 0,
                    'string_result' => 'Ошибка запроса'
                ];

            $this->scorings->update_scoring($scoring_id, $update);
            $this->logging($person, $score);
            return $update;
        }

        if ($score['operationResult'] == 'fail') {
            $update =
                [
                    'status' => 'completed',
                    'body' => '',
                    'success' => 0,
                    'string_result' => 'Клиент не найден в списке'
                ];

            $this->scorings->update_scoring($scoring_id, $update);
            $this->logging($person, $score);
            return $update;
        }

        $update =
            [
                'status' => 'completed',
                'body' => $score['validationScorePhone'],
                'success' => 1,
                'string_result' => 'Пользователь найден: ' . $this->IdxApi->result[$score['validationScorePhone']]
            ];

        $this->scorings->update_scoring($scoring_id, $update);
        return $this->logging($person, $score);
    }

    private function logging($request, $response, $filename = 'idxLog.txt')
    {
        echo 1;


        $log_filename = $this->config->root_dir.'logs/'. $filename;

        if (date('d', filemtime($log_filename)) != date('d')) {
            $archive_filename = $this->config->root_dir.'logs/' . 'archive/' . date('ymd', filemtime($log_filename)) . '.' . $filename;
            rename($log_filename, $archive_filename);
            file_put_contents($log_filename, "\xEF\xBB\xBF");
        }


        $str = PHP_EOL . '===================================================================' . PHP_EOL;
        $str .= date('d.m.Y H:i:s') . PHP_EOL;
        $str .= var_export($request, true) . PHP_EOL;
        $str .= var_export($response, true) . PHP_EOL;
        $str .= 'END' . PHP_EOL;

        file_put_contents($this->config->root_dir.'logs/' . $filename, $str, FILE_APPEND);

        return 1;
    }

    private function restrDocs()
    {
        $contract = ContractsORM::find(2141);
        $user = UsersORM::find(20473);

        $paymentSchedules = PaymentsSchedulesORM::find(28);
        $paymentSchedules = json_decode($paymentSchedules->payment_schedules, true);

        $schedule = new stdClass();
        $schedule->order_id = 22984;
        $schedule->user_id = 20473;
        $schedule->contract_id = 2141;
        $schedule->init_od = $contract->loan_body_summ;
        $schedule->init_prc = $contract->loan_percents_summ;
        $schedule->init_peni = $contract->loan_peni_summ;
        $schedule->actual = 1;
        $schedule->payment_schedules = json_encode($paymentSchedules);

        $params = [
            'contract' => $contract,
            'user' => $user,
            'schedules' => $schedule
        ];

        var_dump(json_encode($params));
        exit;
    }
}