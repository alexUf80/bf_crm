<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
    {

        $contract = $this->contracts->get_contract(3713);
        $user = $this->users->get_user($contract->user_id);
        $address = $this->Addresses->get_address($user->regaddress_id);

        $sas = $this->insurances->get_insurance_cost(7000,$address->id);
        var_dump($sas);

        $sas = $this->reject_amount($address->id);
        var_dump($sas);
        
        exit;
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