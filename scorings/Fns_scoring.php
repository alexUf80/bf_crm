<?php

class Fns_scoring extends Core
{
    private $user_id;
    private $order_id;
    private $audit_id;
    private $type;
    
    private $url = "https://service.nalog.ru/inn-proc.do";
    


    public function run_scoring($scoring_id)
    {
        $update = array();
        
    	$scoring_type = $this->scorings->get_type('fns');
        
        if ($scoring = $this->scorings->get_scoring($scoring_id))
        {
            if ($order = $this->orders->get_order((int)$scoring->order_id))
            {
                if (!empty($order->inn) && $order->inn != '#NULL!')
                {
                    $update = array(
                        'status' => 'completed',
                        'body' => 'ИНН уже указан у клиента',
                        'success' => 1,
                        'string_result' => $order->inn
                    );
                    
                }
                else
                {
                    if (empty($order->lastname) || empty($order->firstname) || empty($order->patronymic) || empty($order->passport_serial) || empty($order->passport_date) || empty($order->birth))
                    {
                        $update = array(
                            'status' => 'error',
                            'string_result' => 'в заявке не достаточно данных для проведения скоринга'
                        );
                    }
                    else
                    {
                        list($passportSerial, $passportNumber) = explode('-', $order->passport_serial);
                        $params =
                            [
                                'UserID' => 'barents',
                                'Password' => 'uW5q+jXE',
                                'sources' => 'fns',
                                'PersonReq' => [
                                    'first' => $order->firstname,
                                    'middle' => $order->patronymic,
                                    'paternal' => $order->lastname,
                                    'birthDt' => date('Y-m-d', strtotime($order->birth)),
                                    'passport_series' => $passportSerial,
                                    'passport_number' => $passportNumber,
                                ]
                            ];

                        $request = $this->send_request($params);

                        $inn = 0;

                        if(isset($request['Source']['@attributes']['checktype']) && $request['Source']['@attributes']['checktype'] != 'fns_inn')
                            return $inn;

                        foreach ($request['Source'] as $sources) {
                            if ($sources['@attributes']['checktype'] == 'fns_inn') {
                                foreach ($sources['Record'] as $fields) {
                                    foreach ($fields as $field) {
                                        if ($field['FieldName'] == 'INN')
                                            $inn = $field['FieldValue'];
                                    }
                                }
                            }
                        }

                        if ($inn == 0 && $scoring->repeat_count < 2)
                        {
                            $update = array(
                                'status' => 'repeat',
                                'body' => serialize($request),
                                'string_result' => 'ПОВТОРНЫЙ ЗАПРОС',
                                'repeat_count' => $scoring->repeat_count + 1,
                            );
                            
                        }
                        else
                        {
                            $update = array(
                                'status' => 'completed',
                                'body' => serialize($request),
                                'success' => $inn ? 1 : 0,
                                'string_result' => $inn == 0 ? 'ИНН не найден' : $inn
                            );
                            
                            if ($inn != 0)
                            {
                                $this->users->update_user($order->user_id, array('inn' => $inn));
                            }
                        }
                    }
                }
            }
            else
            {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найдена заявка'
                );
            }
            
            if (!empty($update))
                $this->scorings->update_scoring($scoring_id, $update);
            
            return $update;
        }
    }
    


    public function run($audit_id, $user_id, $order_id)
    {
        $this->user_id = $user_id;
        $this->audit_id = $audit_id;
        $this->order_id = $order_id;
        
        $this->type = $this->scorings->get_type('fns');
    	
        $user = $this->users->get_user((int)$user_id);

        return $this->scoring($user);
    }
    
    public function scoring($user)
    {
        $birthday = date('d.m.Y', strtotime($user->birth));
        $passportdate = date('d.m.Y', strtotime($user->passport_date));

        list($passportSerial, $passportNumber) = explode('-', $user->passport_serial);
        $params =
            [
                'UserID' => 'barents',
                'Password' => 'uW5q+jXE',
                'sources' => 'fns',
                'PersonReq' => [
                    'first' => $user->firstname,
                    'middle' => $user->patronymic,
                    'paternal' => $user->lastname,
                    'birthDt' => date('Y-m-d', strtotime($user->birth)),
                    'passport_series' => $passportSerial,
                    'passport_number' => $passportNumber,
                ]
            ];

        $request = $this->send_request($params);

        $inn = 0;

        if(isset($request['Source']['@attributes']['checktype']) && $request['Source']['@attributes']['checktype'] != 'fns_inn')
            return $inn;

        foreach ($request['Source'] as $sources) {
            if ($sources['@attributes']['checktype'] == 'fns_inn') {
                foreach ($sources['Record'] as $fields) {
                    foreach ($fields as $field) {
                        if ($field['FieldName'] == 'INN')
                            $inn = $field['FieldValue'];
                    }
                }
            }
        }
        if ($inn != 0)
        {
            $scoring = array(
                'user_id' => $user->id,
                'audit_id' => $this->audit_id,
                'type' => 'fns',
                'body' => $inn,
                'success' => 1,
                'scorista_id' => '',
                'string_result' => 'ИНН найден'
            );
            $this->scorings->add_scoring($scoring);


        }
        else
        {
            $scoring = array(
                'user_id' => $user->id,
                'audit_id' => $this->audit_id,
                'type' => 'fns',
                'body' => '',
                'success' => 0,
                'scorista_id' => '',
                'string_result' => 'ИНН не найден'
            );
            $this->scorings->add_scoring($scoring);
        }

    }
    public function get_inn($surname, $name, $patronymic, $birthdate, $doctype, $docnumber, $docdate)
    {
        $docnumber_clear = str_replace(array('-', ' ', ' '), '', $docnumber);
        $docno = substr($docnumber_clear, 0, 2).' '.substr($docnumber_clear, 2, 2).' '.substr($docnumber_clear, 4, 6);
        
        $data = array(
            "fam" => $surname,
            "nam" => $name,
            "otch" => $patronymic,
            "bdate" => $birthdate,
            "bplace" => "",
            "doctype" => $doctype,
            "docno" => $docno,
            "docdt" => $docdate,
            "c" => "innMy",
            "captcha" => "",
            "captchaToken" => ""
        );
        $options = array(
            'https' => array(
                'method'  => 'POST',
                'header'  => array(
                    'Content-type: application/x-www-form-urlencoded',
                ),
                'content' => http_build_query($data)
            ),
        );


        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $resp = curl_exec($ch);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($data, $resp);echo '</pre><hr />';
        return json_decode($resp);
    }

    private function send_request($params)
    {
        $request = $this->XMLSerializer->serialize($params);

        $ch = curl_init('https://i-sphere.ru/2.00/');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        $html = simplexml_load_string($html);
        $json = json_encode($html);
        $array = json_decode($json, TRUE);
        curl_close($ch);

        return $array;
    }
}