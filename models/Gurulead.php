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
}