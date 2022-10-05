<?php

class Onec extends Core
{
//    private $auth_user = 'Administrator';
//    private $auth_pswd = '6WgGh1wnio';
    private $auth_user = 'Администратор';
    private $auth_pswd = '';
    
    //private $host =   'http://45.137.152.39/FinAspect/hs/';
    private $host = 'http://45.137.152.39/aspectsql/hs/';

    private $log = 1;
    private $log_dir  = 'logs/';
    
    /**
        $card->transaction
        $card->user
    */
    public function send_card($card)
    {
    	$request = new StdClass();
        $request->aid = $card->user->UID;
        $request->OrderID = $card->transaction->register_id;
        $request->OperationID = $card->transaction->operation;
        $request->Date = date('YmdHis', strtotime($card->created));
        $request->Amount = $card->transaction->amount / 100;

		$response = $this->send('WebCRM', 'LinkCard', $request, 1, 'cards.txt');
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($response, $request, $card);echo '</pre><hr />';
		return empty($response->return) ? NULL : $response->return;
    	
    }

    public function send_pdn($order_id)
    {
        $order = $this->orders->get_order($order_id);
        if (empty($order)) {
            return false;
        }

        $user = $this->users->get_user($order->user_id);
        if (!empty($order->contract_id))
            $contract = $this->contracts->get_contract($order->contract_id);
            
        if (empty($user)) {
            return false;
        }

        $request = new StdClass();

        $request->uid_deal = empty($contract->uid) ? $order->uid : $contract->uid;
        $request->pdn = $user->pdn;

        $json_response = $this->send('FA_PDN', $request);
        //$response = json_decode($json_response);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }

    // Сервис FA_1Rub , параметры в джейсоне ID - ид операции
    // OrderID 
    // OperationID
    // Date -формат ггггММддЧЧммсс
    public function send_rub($card)
    {
        if (empty($card)) {
            return false;
        }

        if (!empty($card->sent_status)) {
            return false;
        }

        $transaction = $this->transactions->get_transaction($card->transaction_id);

        if (empty($transaction)) {
            return false;
        }

        $request = new StdClass();

        $request->ID = $card->id;
        $request->OrderID = $card->register_id;
        $request->OperationID = $transaction->operation;
        $request->date = date('YmdHis', strtotime($card->operation_date));

        $json_response = $this->send('FA_1Rub', $request);
        //$response = json_decode($json_response);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }

    //услуги СМС
    public function send_bud_v_kurse($operation)
    {
        if (empty($operation)) {
            return false;
        }

        if ($operation->type != 'BUD_V_KURSE') {
            return false;
        }

        if (!empty($operation->sent_status)) {
            return false;
        }

        $order = $this->orders->get_order($operation->order_id);

        if (empty($order)) {
            return false;
        }

        $contract = $this->contracts->get_contract($operation->contract_id);

        if (empty($contract)) {
            return false;
        }
        
        if ($order->id_deal) {
            $uid = $contract->uid;
        } else {
            $uid = $order->uid;
        }

        if (empty($uid)) {
            $uid = $order->uid;
        }

        $request = new StdClass();

        $request->uid_deal = $uid;
        $request->id_Operaion = $operation->id;
        $request->amount = $operation->amount;
        $request->date = date('YmdHis', strtotime($operation->created));

        $request->id_Service = 'wZzkAKK2Ew';


        $json_response = $this->send('FA_Services', $request);
        //$response = json_decode($json_response);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }

    //REJECT_REASON
    public function send_reject_reason($operation)
    {
        if (empty($operation)) {
            return false;
        }

        if ($operation->type != 'REJECT_REASON') {
            return false;
        }

        if (!empty($operation->sent_status)) {
            return false;
        }

        $order = $this->orders->get_order($operation->order_id);

        if (empty($order)) {
            return false;
        }

        $contract = $this->contracts->get_contract($operation->contract_id);

        if (empty($contract)) {
            return false;
        }
        
        if ($order->id_deal) {
            $uid = $contract->uid;
        } else {
            $uid = $order->uid;
        }

        if (empty($uid)) {
            $uid = $order->uid;
        }

        $request = new StdClass();

        $request->uid_deal = $uid;
        $request->id_Operaion = $operation->id;
        $request->amount = $operation->amount;
        $request->date = date('YmdHis', strtotime($operation->created));

        $request->id_Service = 'tr56vbrem3k';


        $json_response = $this->send('FA_Services', $request);
        //$response = json_decode($json_response);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }

    //Услуги страхования при вы­даче
    public function send_issuance_insurance($operation)
    {
        if (empty($operation)) {
            return false;
        }

        if (!empty($operation->sent_status)) {
            return false;
        }

        if ($operation->type != 'INSURANCE') {
            return false;
        }

        $order = $this->orders->get_order($operation->order_id);

        if (empty($order)) {
            return false;
        }

        $insurance = $this->insurances->get_operation_insurance($operation->id);

        if (empty($insurance)) {
            return false;
        }

        $request = new StdClass();

        $contract = $this->contracts->get_contract($operation->contract_id);

        if (empty($contract)) {
            return false;
        }
        
        if ($order->id_deal) {
            $uid = $contract->uid;
        } else {
            $uid = $order->uid;
        }

        $request->uid_deal = $uid;
        $request->id_Operaion = $operation->id;
        $request->amount = $operation->amount;
        $request->date = date('YmdHis', strtotime($operation->created));

        $request->id_Service = '2vdehL8hmm';
        $request->NumberInsurance1 = $insurance->number;

        $json_response = $this->send('FA_Services', $request);
        //$response = json_decode($json_response);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }

    //Услу­ги страхования при пролонгации
    public function send_prolongation_insurance($operation)
    {
        if (empty($operation)) {
            return false;
        }

        if ($operation->type != 'INSURANCE') {
            return false;
        }

        if (!empty($operation->sent_status)) {
            return false;
        }

        $order = $this->orders->get_order($operation->order_id);

        if (empty($order)) {
            return false;
        }

        $insurance = $this->insurances->get_operation_insurance($operation->id);

        if (empty($insurance)) {
            return false;
        }

        $contract = $this->contracts->get_contract($operation->contract_id);

        if (empty($contract)) {
            return false;
        }
        
        if ($order->id_deal) {
            $uid = $contract->uid;
        } else {
            $uid = $order->uid;
        }

        $request = new StdClass();

        $request->uid_deal = $uid;
        $request->id_Operaion = $operation->id;
        $request->amount = $operation->amount;
        $request->date = date('YmdHis', strtotime($operation->created));

        $request->id_Service = 'rh1EakbiM1';
        $request->NumberInsurance2 = $insurance->number;

        $json_response = $this->send('FA_Services', $request);
        //$response = json_decode($json_response);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }

    //Услу­ги страхования при закрытии
    public function send_closing_insurance($operation)
    {
        if (empty($operation)) {
            return false;
        }

        if ($operation->type != 'INSURANCE') {
            return false;
        }

        if (!empty($operation->sent_status)) {
            return false;
        }

        $order = $this->orders->get_order($operation->order_id);

        if (empty($order)) {
            return false;
        }

        $insurance = $this->insurances->get_operation_insurance($operation->id);

        if (empty($insurance)) {
            return false;
        }

        $contract = $this->contracts->get_contract($operation->contract_id);

        if (empty($contract)) {
            return false;
        }
        
        if ($order->id_deal) {
            $uid = $contract->uid;
        } else {
            $uid = $order->uid;
        }

        $request = new StdClass();

        $request->uid_deal = $uid;
        $request->id_Operaion = $operation->id;
        $request->amount = $operation->amount;
        $request->date = date('YmdHis', strtotime($operation->created));

        $request->id_Service = 'S54DHHEuIY';
        $request->NumberInsurance3 = $insurance->number;

        $json_response = $this->send('FA_Services', $request);
        //$response = json_decode($json_response);

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }

    public function send_payment($operation)
    {
        if (empty($operation)) {
            return false;
        }

        if (!empty($operation->sent_status)) {
            return false;
        }

        $transaction = $this->transactions->get_transaction($operation->transaction_id);

        if (empty($transaction)) {
            return false;
        }

        $order = $this->orders->get_order($operation->order_id);

        if (empty($order)) {
            return false;
        }

        $contract = $this->contracts->get_contract($operation->contract_id);

        if (empty($contract)) {
            return false;
        }

        $request = new StdClass();
    
        if ($order->id_deal) {
            $uid = $contract->uid;
        } else {
            $uid = $order->uid;
        }

        $request->uid_deal = $uid;
        $request->uid_payment = $transaction->operation;
        $request->date = date('YmdHis', strtotime($operation->created));
        $request->amount = $operation->amount;
        $request->Код = $order->id_client;
        $request->Номер = $order->id_deal;
        
        if ($transaction->prolongation) {
            $request->date_return = date('YmdHis', strtotime($contract->return_date));
            $request->prolongation = 1;
        } else {
            $request->prolongation = 0;
        }

        if ($operation->contract_is_closed === '1' || $operation->contract_is_closed === '0' ) {
            $request->closure = $operation->contract_is_closed;
        } else {
            return false;
        }

        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operation->contract_is_closed, $request->closure);echo '</pre><hr />';
        //exit;

        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order->firstname);echo '</pre><hr />';
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order->lastname);echo '</pre><hr />';
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order->patronymic);echo '</pre><hr />';

        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($request);echo '</pre><hr />';
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($transaction->prolongation);echo '</pre><hr />';
        //exit;

        $json_response = $this->send('FA_Payment', $request);
        //$response = json_decode($json_response);


        if (!empty($json_response) && $json_response == 'OK') {
            $this->operations->update_operation($operation->id, [
                'sent_status' => 2,
                'sent_date' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->operations->update_operation($operation->id, [
                'sent_status' => 3,
            ]);
        }

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json_response);echo '</pre><hr />';

        return $json_response;
    }
    


    public function send_order($order_id)
    {
        if (!($order = $this->orders->get_order($order_id)))
            return false;
        
        if (!($user = $this->users->get_user($order->user_id)))
            return false;

        if (!empty($order->contract_id))
            $contract = $this->contracts->get_contract($order->contract_id);

        if ($order->sent_1c != 0) {
            return false;
        }

        if ($order->status != 3 && $order->status != 8 && $order->status != 6 && $order->status != 5)
        {
            return false;
        }
        
        $clear_passport = trim(str_replace(array(' ', '-', '_'), '', $user->passport_serial));
        $passport_series = substr($clear_passport, 0, 4);
        $passport_number = substr($clear_passport, 4, 6);
        
        $request = array(
          "ID" => "KreditAPI", 
          "creditproduct" => "ОНЛАЙН-ЗАЙМ ДО 30 ДНЕЙ", 
          "last_name" => trim($user->lastname), 
          "first_name" => trim($user->firstname), 
          "middle_name" => trim($user->patronymic), 
          "phone" => $user->phone_mobile,  
          "birthday" => date('Y-m-d', strtotime($user->birth)), 
          "email" => $user->email,  
          "amount" => empty($contract) ? $order->amount : $contract->amount, 
          "date" => empty($contract) ? date('Y-m-d', strtotime($order->date)) : date('Y-m-d', strtotime($contract->inssuance_date)), 
          "data_request" => empty($contract) ? date('Y-m-d', strtotime($order->date)) : date('Y-m-d', strtotime($contract->inssuance_date)), 
          "uid" => empty($contract->uid) ? $order->uid : $contract->uid, 
          "period" => empty($contract) ? date('Y-m-d', strtotime($order->date + $order->period * 86400)) : date('Y-m-d', strtotime($contract->return_date)), 
          "id_sex" => $user->gender == 'male' ? 'мужской' : 'женский',  
          "passport_series" => $passport_series,  
          "passport_number" => $passport_number,  
          "passport_date_of_issue" => date('Y-m-d', strtotime($user->passport_date)),  
          "birthplace" => $user->birth_place,  
          "passport_org" => $user->passport_issued,  
          "passport_code" => $user->subdivision_code,  
          "incoming" => (integer) $user->income,  
          "work_name" => $user->workplace,  
          "work_phone" => $user->workphone,  
          
          "NewLoanFromLK" => $order->client_status == 'nk' ? 0 : 1, 
          "nomerrashodnika" => "Дистанционно", 
          "service" => "БАНКОВСКАЯ КАРТА",  
            
          "residential_index" => '',  
          "residential_region" => '',  
          "residential_city" => '',  
          "residential_street" => '',  
          "residential_house" => '',  
          "residential_apartment" => '',
            
          "registration_index" => '',  
          "registration_region" => '',  
          "registration_city" => '',  
          "registration_street" => '',  
          "registration_house" => '',  
          "registration_apartment" => '', 
          
          "status" => '', 
          "decision" => '',
        );

        if (empty($request['middle_name'])) {
            $request['middle_name'] = '-';
        }
        
        if ($order->status == 3 || $order->status == 8 || $order->status == 6)
        {
            if ($order->status == 8)
                $request['decision'] = 'Отказался';
            else
                $request['decision'] = 'Отрицательное';
            $request['status'] = 'Отказ';
        }
        elseif ($contract->status == 2)
        {
            $request['decision'] = 'Положительное';
            $request['status'] = 'Выдан';
        }
        // факт адрес
        $request['residential_index'] = $user->Faktindex;
        if (!empty($user->Faktregion))
            $request['residential_region'] .= trim($user->Faktregion.' '.$user->Faktregion_shorttype);
        if (!empty($user->Faktdistrict))
            $request['residential_region'] .= trim(' '.$user->Faktdistrict.' '.$user->Faktdistrict_shorttype);
        if (!empty($user->Faktcity))
            $request['residential_city'] .= trim($user->Faktcity.' '.$user->Faktcity_shorttype);
        if (!empty($user->Faktlocality))
            $request['residential_city'] .= trim(' '.$user->Faktlocality.' '.$user->Faktlocality_shorttype);
        if (!empty($user->Faktstreet))
            $request['residential_street'] .= trim($user->Faktstreet.' '.$user->Faktstreet_shorttype);
        if (!empty($user->Fakthousing))
            $request['residential_house'] .= trim($user->Fakthousing);
        if (!empty($user->Faktbiulding))
            $request['residential_house'] .= trim(', стр.'.$user->Faktbiulding);
        if (!empty($user->Faktroom))
            $request['residential_apartment'] .= trim($user->Faktroom);

        // рег адрес
        $request['registration_index'] = $user->Regindex;
        if (!empty($user->Regregion))
            $request['registration_region'] .= trim($user->Regregion.' '.$user->Regregion_shorttype);
        if (!empty($user->Regdistrict))
            $request['registration_region'] .= trim(' '.$user->Regdistrict.' '.$user->Regdistrict_shorttype);
        if (!empty($user->Regcity))
            $request['registration_city'] .= trim($user->Regcity.' '.$user->Regcity_shorttype);
        if (!empty($user->Reglocality))
            $request['registration_city'] .= trim(' '.$user->Reglocality.' '.$user->Reglocality_shorttype);
        if (!empty($user->Regstreet))
            $request['registration_street'] .= trim($user->Regstreet.' '.$user->Regstreet_shorttype);
        if (!empty($user->Reghousing))
            $request['registration_house'] .= trim($user->Reghousing);
        if (!empty($user->Regbiulding))
            $request['registration_house'] .= trim(', стр.'.$user->Regbiulding);
        if (!empty($user->Regroom))
            $request['registration_apartment'] .= trim($user->Regroom);
          

        $json_response = $this->send('Request', $request);
        
        $response = json_decode($json_response);
        
        //"result": "1",
        //"description": "Заявка создана",
        //"IDDeal": "ФА000004924",
        //"IDClient": "000026682"
        if (!empty($response->IDDeal) && !empty($response->IDClient))
        {
            // номер сделки и ид клиента
            $this->orders->update_order($order_id, [
                'sent_1c' => 1,
                'id_deal' => $response->IDDeal,
                'id_client' => $response->IDClient,
                'onec_responce' => $response->description
            ]);
        } elseif ($response->result == 0 && $response->description == 'Контрагент с такими данными уже имеется в базе.') {
            $this->orders->update_order($order_id, [
                'sent_1c' => 3,
                'onec_responce' => $response->description
            ]);
        } elseif ($response->result == 0) {
            $this->orders->update_order($order_id, [
                'sent_1c' => 3,
                'onec_responce' => $response->description
            ]);
        }

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump('RESPONSE:', $json_response);echo '</pre><hr />';            
        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump('REQUEST:', $request);echo '</pre><hr />';            
        
        return $response;
    }
    
    
    public function test()
    {
        
        
        $this->send();
    }
    
    public function send($method, $data)
    {
        $url = $this->host.$method;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $this->auth_user . ":" . $this->auth_pswd);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $return = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        if (!empty($error))
        {
        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump('ERROR', $error, $info);echo '</pre><hr />';
        }
        curl_close($ch);

        if ($method != 'Request') {
            $path = $method . '.log';
        } else {
            $path = 'exchange.log';
        }
        $this->logging($method, $url, $data, $return, $path);

        return $return;
    }

    public function logging($local_method, $service, $request, $response, $filename = 'soap.txt')
    {
        $log_filename = $this->log_dir.$filename;
        
        if (date('d', filemtime($log_filename)) != date('d'))
        {
            $archive_filename = $this->log_dir.'archive/'.date('ymd', filemtime($log_filename)).'.'.$filename;
            rename($log_filename, $archive_filename);
            file_put_contents($log_filename, "\xEF\xBB\xBF");            
        }


        $str = PHP_EOL.'==================================================================='.PHP_EOL;
        $str .= date('d.m.Y H:i:s').PHP_EOL;
        $str .= $service.PHP_EOL;
        $str .= var_export($request, true).PHP_EOL;
        $str .= var_export($response, true).PHP_EOL;
        $str .= 'END'.PHP_EOL;
        
        //echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($str);echo '</pre><hr />';
        
        file_put_contents($this->log_dir.$filename, $str, FILE_APPEND);
    }
    
}