<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');
error_reporting(0);
ini_set('display_errors', 'Off');

class TestController extends Controller
{
    public function fetch()
    {

        // $contract = $this->contracts->get_contract(3713);
        // $user = $this->users->get_user($contract->user_id);
        // $address = $this->Addresses->get_address($user->regaddress_id);

        // $sas = $this->insurances->get_insurance_cost(7000,$address->id);
        // var_dump($sas);

        // $sas = $this->reject_amount($address->id);
        // var_dump($sas);

        // $activeProduct = 0;
        // $query = $this->db->placehold("
        //     SELECT *
        //     FROM __orders
        //     WHERE status=5 OR status=7
        //     ORDER BY id DESC
        // ");
        // $this->db->query($query);
        // $orders = $this->db->results();
        // foreach ($orders as $order) {
        //     $nbki = $this->scorings->get_type_scoring($order->id, 'nbki');
        //     $nbki = unserialize($nbki->body)['number_of_active'];
        //     if(isset($nbki)){
        //         if ($nbki < 3) {
        //             echo $order->id.'<hr>';
        //         }
        //     }
        // }








        // $order = $this->orders->get_order(61556);
        // $nbkiScor = ScoringsORM::query()->where('order_id', '=', $order->order_id)->where('type', '=', 'nbki')->first();
        
        // if ($nbkiScor) {
        //     $nbkiParams = unserialize($nbkiScor->body);
        //     $data = file_get_contents(str_replace('log_report','log_xml', $nbkiParams['report_url']));
        //     $xml = simplexml_load_string($data);
        //     $nbkiParams['json'] = json_decode(json_encode($xml), true)['preply']['report'];

        // }

        // if (!empty($nbkiParams)) {
        //     $cou = 0;
        //     $act = 0;
        //     $arch = 0;
        //     $no = 0;
        //     $close = 0;
        //     $totalAmtOutstandingDone = 0;
        //     foreach ($nbkiParams['json']['AccountReplyRUTDF'] as $reply) {
        //         $cou++;

        //         if (isset($reply['reportingDt'])) {
        //             $curentDateDiff = date_diff(new DateTime(), new DateTime($reply['reportingDt']));
        //         }
        //         if (isset($reply['trade']['openedDt'])) {
        //             $beginDateDiff = date_diff(new DateTime(), new DateTime($reply['trade']['openedDt']));
        //             if ($beginDateDiff->days <= 30) 
        //                 $beginDateDiffCount1Month++;
        //             if ($beginDateDiff->days <= 91) 
        //                 $beginDateDiffCount3Month++;
        //         }
        //         $status = [
        //             'name' => 'Активный',
        //             'color' => 'black',
        //         ];
        //         $act++;
                
        //         if (
        //             (isset($reply['holdCode']) && $reply['holdCode'] == 1) ||
        //             $curentDateDiff->days > 33 ||
        //             (isset($reply['loanIndicator']) && $reply['loanIndicator'] != 1)
        //         ) {
        //             $status = [
        //                 'name' => 'Не определен',
        //                 'color' => 'silver',
        //             ];
        //             $no++;
        //             $act--;
        //         }
                
        //         if($curentDateDiff->days > 180) {
        //             $status = [
        //                 'name' => 'Архив',
        //                 'color' => 'silver',
        //             ];
        //             $arch++;
        //             $act--;
        //         }

        //         if(isset($reply['loanIndicator']) && in_array($reply['loanIndicator'], [3,11])) {
        //             $status = [
        //                 'name' => 'Прощение долга',
        //                 'color' => 'red',
        //             ];
        //             $close++;
        //         }

        //         if(isset($reply['submitHold']['holdCode']) && $reply['submitHold']['holdCode'] == 3) {
        //             $status = [
        //                 'name' => 'Списан',
        //                 'color' => 'red',
        //             ];
        //             $close++;
        //         }

        //         if(
        //             (isset($reply['loanIndicator']) && ($reply['loanIndicator'] == 2 || $reply['loanIndicator'] == 1)) ||
        //             (isset($reply['sbLoanIndicator']) && $reply['sbLoanIndicator'] == 1) ||
        //             (isset($reply['collatRepay']) && $reply['collatRepay'] == 1)
        //         ) {
        //             $status = [
        //                 'name' => 'Счет закрыт',
        //                 'color' => 'green',
        //             ];
        //             $close++;
        //         }


        //         if ($status['name'] == 'Активный' || $status['name'] == 'Просрочен' || $status['name'] == 'Не определен') {
        //             $activeProduct++;

        //             if (isset($reply['paymtCondition'])) {
        //                 $keys = array_keys($reply['paymtCondition']);
        //                 if ($keys !== array_keys($reply['paymtCondition'])) {
        //                     if (isset($reply['paymtCondition']['principalTermsAmt']) && isset($reply['paymtCondition']['interestTermsAmt'])) {
        //                         $totalAverPaymtAmt += floatval($reply['paymtCondition']['principalTermsAmt']) + floatval($reply['paymtCondition']['interestTermsAmt']);;
        //                     }
        //                 } else {
        //                     $condition = end($reply['paymtCondition']);
        //                     if (isset($condition['principalTermsAmt']) && isset($condition['interestTermsAmt'])) {
        //                         $totalAverPaymtAmt += floatval($condition['principalTermsAmt']) + floatval($condition['interestTermsAmt']);
        //                     }
        //                 }
        //             } else {
        //                 if (isset($reply['monthAverPaymt'])) {
        //                     $totalAverPaymtAmt += floatval($reply['monthAverPaymt']['averPaymtAmt'] ?? 0);
        //                 }
        //             }

        //             if (isset($reply['accountAmt'])) {
        //                 $keys = array_keys($reply['accountAmt']);
        //                 // Если массив ассциативный
        //                 if ($keys !== array_keys($keys)) {
        //                     if ($status['name'] != 'Активный') {
        //                         $dolg += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                     }
        //                     $totalAmtOutstanding += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                 } else {
        //                     foreach ($reply['accountAmt'] as $arrear) {
        //                         if ($status['name'] != 'Активный') {
        //                             $dolg += floatval($arrear['creditLimit'] ?? 0);
        //                         }
        //                         $totalAmtOutstanding += floatval($arrear['creditLimit'] ?? 0);
        //                     }
        //                 }
        //             }
        //         } else {
        //             $doneProduct++;
        //             if (isset($reply['accountAmt'])) {
        //                 $keys = array_keys($reply['accountAmt']);
        //                 // Если массив ассциативный
        //                 if ($keys !== array_keys($keys)) {
        //                     $totalAmtOutstandingDone += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                 } else {
        //                     foreach ($reply['accountAmt'] as $arrear) {
        //                         $totalAmtOutstandingDone += floatval($arrear['creditLimit'] ?? 0);
        //                     }
        //                 }
        //             }
        //         }
        //     }
        //     var_dump($cou);
        //     var_dump($act);
        //     var_dump($arch);
        //     var_dump($no);
        //     var_dump($close);
        //     echo '<hr>';
        //     var_dump($totalAmtOutstandingDone);
        // }






        // $nbkiScor = ScoringsORM::query()->where('order_id', '=', 61556)->where('type', '=', 'nbki')->first();
                        
        // if ($nbkiScor) {
        //     $nbkiParams = unserialize($nbkiScor->body);

        //     if (isset($nbkiParams['report_url'])) {
        //         $data = file_get_contents(str_replace('log_report','log_xml', $nbkiParams['report_url']));
        //         $xml = simplexml_load_string($data);
        //         $nbkiParams['json'] = json_decode(json_encode($xml), true)['preply']['report'];
        //     }

        //     if (!empty($nbkiParams)) {
        //         $activeProduct = 0;
        //         $doneProduct = 0;
        //         $summ = 0;
        //         $totalAmtOutstanding = 0;
        //         $totalAmtOutstandingDone = 0;
        //         $totalAverPaymtAmt = 0;
        //         $dolg = 0;
        //         $mkk = 0;
        //         $mkkSumm = 0;
        //         foreach ($nbkiParams['json']['AccountReplyRUTDF'] as $reply) {

        //             $keys = array_keys($reply['pastdueArrear']);
        //             // Если массив ассциативный
        //             if ($keys !== array_keys($keys)) {
        //                 $pastdueArrear = $reply['pastdueArrear'];
        //                 $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
        //                 if ($past !== '0.00') {
        //                     $summ = floatval($past);
        //                 }
        //             } else {
        //                 foreach ($reply['pastdueArrear'] as $pastdueArrear) {
        //                     $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
        //                     if ($past !== '0.00') {
        //                         $summ = floatval($past);
        //                     }
        //                 }
        //             }


        //             if (isset($reply['reportingDt'])) {
        //                 $curentDateDiff = date_diff(new DateTime(), new DateTime($reply['reportingDt']));
        //             }
        //             $status = [
        //                 'name' => 'Активный',
        //                 'color' => 'black',
        //             ];
        //             if (
        //                 (isset($reply['holdCode']) && $reply['holdCode'] == 1) ||
        //                 $curentDateDiff->days > 33 ||
        //                 (isset($reply['loanIndicator']) && $reply['loanIndicator'] != 1)
        //             ) {
        //                 $status = [
        //                     'name' => 'Не определен',
        //                     'color' => 'silver',
        //                 ];
        //             }
        //             if($curentDateDiff->days > 180) {
        //                 $status = [
        //                     'name' => 'Архив',
        //                     'color' => 'silver',
        //                 ];
        //             }

        //             if($summ > 0) {
        //                 $status = [
        //                     'name' => 'Просрочен',
        //                     'color' => 'red',
        //                 ];
        //             }

        //             if(isset($reply['loanIndicator']) && in_array($reply['loanIndicator'], [3,11])) {
        //                 $status = [
        //                     'name' => 'Прощение долга',
        //                     'color' => 'red',
        //                 ];
        //             }

        //             if(isset($reply['submitHold']['holdCode']) && $reply['submitHold']['holdCode'] == 3) {
        //                 $status = [
        //                     'name' => 'Списан',
        //                     'color' => 'red',
        //                 ];
        //             }

        //             if(
        //                 (isset($reply['loanIndicator']) && ($reply['loanIndicator'] == 2 || $reply['loanIndicator'] == 1)) ||
        //                 (isset($reply['sbLoanIndicator']) && $reply['sbLoanIndicator'] == 1) ||
        //                 (isset($reply['collatRepay']) && $reply['collatRepay'] == 1)
        //             ) {
        //                 $status = [
        //                     'name' => 'Счет закрыт',
        //                     'color' => 'green',
        //                 ];
        //             }
        //             if (isset($reply['businessCategory']) && $reply['businessCategory'] == 'MKK') {
        //                 $openDt = false;
        //                 if (isset($reply['trade'])) {
        //                     $keys = array_keys($reply['trade']);
        //                     // Если массив ассциативный
        //                     if ($keys !== array_keys($keys)) {
        //                         $openDt = self::date_format($reply['trade']['openedDt']);
        //                     } else {
        //                         $openDt = self::date_format($reply['trade'][0]['openedDt']);
        //                     }
        //                 }
        //                 $time = time() - (86400 * 92);
        //                 $dateMonth = date('d.m.Y', $time);
        //                 if ($openDt > $dateMonth) {
        //                     $mkk++;
        //                 }
        //                 if ($status['name'] == 'Активный' || $status['name'] == 'Просрочен' || $status['name'] == 'Не определен') {
        //                     $mkkSumm++;
        //                 }
        //             }
        //             if ($status['name'] == 'Активный' || $status['name'] == 'Просрочен' || $status['name'] == 'Не определен') {
        //                 $activeProduct++;

        //                 if (isset($reply['paymtCondition'])) {
        //                     $keys = array_keys($reply['paymtCondition']);
        //                     if ($keys !== array_keys($reply['paymtCondition'])) {
        //                         if (isset($reply['paymtCondition']['principalTermsAmt']) && isset($reply['paymtCondition']['interestTermsAmt'])) {
        //                             $totalAverPaymtAmt += floatval($reply['paymtCondition']['principalTermsAmt']) + floatval($reply['paymtCondition']['interestTermsAmt']);;
        //                         }
        //                     } else {
        //                         $condition = end($reply['paymtCondition']);
        //                         if (isset($condition['principalTermsAmt']) && isset($condition['interestTermsAmt'])) {
        //                             $totalAverPaymtAmt += floatval($condition['principalTermsAmt']) + floatval($condition['interestTermsAmt']);
        //                         }
        //                     }
        //                 } else {
        //                     if (isset($reply['monthAverPaymt'])) {
        //                         $totalAverPaymtAmt += floatval($reply['monthAverPaymt']['averPaymtAmt'] ?? 0);
        //                     }
        //                 }

        //                 if (isset($reply['accountAmt'])) {
        //                     $keys = array_keys($reply['accountAmt']);
        //                     // Если массив ассциативный
        //                     if ($keys !== array_keys($keys)) {
        //                         if ($status['name'] != 'Активный') {
        //                             $dolg += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                         }
        //                         $totalAmtOutstanding += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                     } else {
        //                         foreach ($reply['accountAmt'] as $arrear) {
        //                             if ($status['name'] != 'Активный') {
        //                                 $dolg += floatval($arrear['creditLimit'] ?? 0);
        //                             }
        //                             $totalAmtOutstanding += floatval($arrear['creditLimit'] ?? 0);
        //                         }
        //                     }
        //                 }
        //             } else {
        //                 $doneProduct++;
        //                 if (isset($reply['accountAmt'])) {
        //                     $keys = array_keys($reply['accountAmt']);
        //                     // Если массив ассциативный
        //                     if ($keys !== array_keys($keys)) {
        //                         $totalAmtOutstandingDone += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                     } else {
        //                         foreach ($reply['accountAmt'] as $arrear) {
        //                             $totalAmtOutstandingDone += floatval($arrear['creditLimit'] ?? 0);
        //                         }
        //                     }
        //                 }
        //             }
        //         }

        //         var_dump($totalAmtOutstandingDone);
        //     }
        // }




        $contracts = $this->contracts->get_contracts(array('user_id' => 20465, 'status' => 3));
        // $contracts = $this->contracts->get_contracts(array('user_id' => 16990, 'status' => 3));
        
        $contract_close_date = '';
        $count_contracts_3000_500_0 = 0;
        $all_percents_summ = 0;
        $all_peni_summ = 0;
        $period_peni_biggest = 0;
        $period_peni_last = 0;

        foreach ($contracts as $contract) {

            // Кол-во дней с даты погашения последнего займа 
            // во внутренней кредитной истории для данного клиента
            if (!is_null($contract->close_date)) {
                if ($contract_close_date < $contract->close_date) 
                    $contract_close_date = $contract->close_date;
            }
            else{
                if ($contract_close_date < $contract->close_date) 
                    $contract_close_date = $contract->return_date;
            }

            // Кол-во займов во внутренней кредитной истории для данного клиента, 
            // у которых сумма займа>=3000 руб И сумма погашенных процентов>=500 руб
            // И срок просрочки по займу=0
            if ($contract->amount >= 3000) {
                $operations = $this->operations->get_operations(array('type' => 'PAY', 'contract_id' => $contract->id));

                foreach ($operations as $operation) {
                    $contract_loan_percents_summ = 0;

                    $transaction = $this->transactions->get_transaction($operation->transaction_id);
                    $contract_loan_percents_summ += $transaction->loan_percents_summ;
                }
                if ($contract_loan_percents_summ > 500) {
                    $contract_count_peni = 0;

                    $operations = $this->operations->get_operations(array('type' => 'PENI', 'contract_id' => $contract->id));
                    foreach ($operations as $operation) {
                        $contract_count_peni++;
                    }
                    if ($contract_count_peni == 0) {
                        $count_contracts_3000_500_0++;
                    }
                }
            }

            // Сумма погашенных процентов по всем займам 
            // во внутренней кредитной истории для данного клиента
            $operations = $this->operations->get_operations(array('type' => 'PAY', 'contract_id' => $contract->id));

            foreach ($operations as $operation) {
                $transaction = $this->transactions->get_transaction($operation->transaction_id);
                $all_percents_summ += $transaction->loan_percents_summ;
            }

            // Максимальный срок просрочки по всем займам 
            // во внутренней кредитной истории для данного клиента
            $operations = $this->operations->get_operations(array('type' => 'PENI', 'contract_id' => $contract->id));
            $prew_date_peni = '';
            $period_peni = 0;
            $period_peni_last = 0;

            foreach ($operations as $operation) {
                $date1 = new DateTime(date('Y-m-d', strtotime($prew_date_peni)));
                $date2 = new DateTime(date('Y-m-d', strtotime($operation->created)));

                $prew_date_peni = $operation->created;
                $diff = $date2->diff($date1)->days;

                if ($diff == 1) {
                    $period_peni++;
                    $period_peni_last++;
                    if ($period_peni_biggest < $period_peni) 
                        $period_peni_biggest = $period_peni;
                }
                else{
                    $period_peni = 1;
                    $period_peni_last = 1;
                    if ($period_peni_biggest < $period_peni) 
                        $period_peni_biggest = $period_peni;
                }

                $transaction = $this->transactions->get_transaction($operation->transaction_id);
                $all_peni_summ += $transaction->loan_peni_summ;
            }

        }
        
        $date1 = new DateTime(date('Y-m-d', strtotime($contract_close_date)));
        $date2 = new DateTime(date('Y-m-d'));

        $diff = $date2->diff($date1);
        $delay_last_contract = $diff->days;
        var_dump($delay_last_contract);
        echo '<hr>';
        var_dump($count_contracts_3000_500_0);
        echo '<hr>';
        var_dump($all_percents_summ);
        echo '<hr>';
        var_dump($all_peni_summ);
        echo '<hr>';
        var_dump($period_peni_biggest);
        echo '<hr>';
        var_dump($period_peni_last);
        echo '<hr>';


        exit;
    }

    public static function date_format($date_string, $format = 'd.m.Y') {
        try {
            return date($format, strtotime($date_string));
        } catch (Exception $exception) {
            return $date_string;
        }
    }

    private function reject_amount($address_id)
    {

        $address = $this->Addresses->get_address($address_id);
        
        $scoring_type = $this->scorings->get_type('location');
        
        $reg='green-regions';
        $yellow_regions = array_map('trim', explode(',', $scoring_type->params['yellow-regions']));
        if(in_array(mb_strtolower(trim($address->region), 'utf8'), $yellow_regions)){
            $reg = 'yellow-regions';
        }
        $red_regions = array_map('trim', explode(',', $scoring_type->params['red-regions']));
        if(in_array(mb_strtolower(trim($address->region), 'utf8'), $red_regions)){
            $reg = 'red-regions';
        }
        $exception_regions = array_map('trim', explode(',', $scoring_type->params['regions']));
        if(in_array(mb_strtolower(trim($address->region), 'utf8'), $exception_regions)){
            $reg = 'regions';
        }

        $contract_operations = $this->ServicesCost->gets(array('region' => $reg));
        if (isset($contract_operations[0]->reject_reason_cost)) {
            return (float)$contract_operations[0]->reject_reason_cost;
        }
        else{
            return 19;
        }
    }

    // Сжать изображение 
    public function compressImage($source, $destination, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image / gif') 
        $image = imagecreatefromgif($source);

        elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);

        imagejpeg($image, $destination, $quality);

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