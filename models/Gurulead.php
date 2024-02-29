<?php

class Gurulead extends Core
{
    public function sendPendingPostback($orderId, $user_id, $status)
    {

        $order = $this->orders->get_order($orderId);
        $click_id = $order->click_hash;
        $goal = 'loan';
        $status = $status;

        $link = "https://offers.guruleads.ru/postback?clickid=$click_id&goal=$goal&status=$status&action_id=$orderId";

        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);

        $insert =
            [
                'order_id' => $orderId,
                'status'   => $status,
                'click_id' => $click_id,
                'goal'     => $goal,
                'link'     => $link
            ];

        $this->postbacks->add($insert);

        $this->logging_(__METHOD__, 'Guruleads', $link, 'ok', 'Guruleads.txt', 'logs/');

        return 1;
    }

    public function logging_($local_method, $service, $request, $response, $filename, $log_dir)
    {
        $log_filename = $log_dir.$filename;
        
        if (date('d', filemtime($log_filename)) != date('d'))
        {
            $archive_filename = $log_dir.'archive/'.date('ymd', filemtime($log_filename)).'.'.$filename;
            rename($log_filename, $archive_filename);
            file_put_contents($log_filename, "\xEF\xBB\xBF");            
        }

        $str = PHP_EOL.'==================================================================='.PHP_EOL;
        $str .= date('d.m.Y H:i:s').PHP_EOL;
        $str .= $service.PHP_EOL;
        $str .= var_export($request, true).PHP_EOL;
        $str .= var_export($response, true).PHP_EOL;
        $str .= 'END'.PHP_EOL;
        
        file_put_contents($log_filename, $str, FILE_APPEND);
    }

    public function sendApiVitkol($order_id)
    {

        $order = $this->orders->get_order($order_id);
        $user = $this->users->get_user($order->user_id);

        $data = [
            "external_id" => strval($order_id), 
            "first_name" => $user->firstname,
            "last_name" => $user->lastname, 
            "father_name" => $user->patronymic,
            "phone" => $user->phone_mobile, 
            "datetime" => $order->reject_date,
            "refusal_datetime" => "2024-02-29 08:16:07", 
            "amount" => $order->amount,
        ];

        $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
        $curl = curl_init('http://51.250.86.237/leads?token=f83e10123a6f5f4beacbde8a5076ebbcb5c8e67bb655d2f01836d');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        // echo '<pre>';
        return($result);
    }
}