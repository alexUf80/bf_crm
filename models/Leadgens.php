<?php

class Leadgens extends Core
{

    // public function send_pending_postback_click2money($order_id, $order)
    // {

    //     // $base_link = 'https://c2mpbtrck.com/cpaCallback';
    //     // $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=hold&partner=finfive&lead_id=' . $order->id;

    //     // $ch = curl_init($link_lead);
    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //     // curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    //     // $res = curl_exec($ch);
    //     // curl_close($ch);

    //     $result = $this->orders->update_order($order_id, array('lead_postback_date' => date('Y-m-d H:i'), 'lead_postback_type' => 'pending'));

    //     // $this->to_log(__METHOD__, 'hold', $link_lead, $res, 'lead_click2money.txt');
    // }

    public function send_approved_postback_click2money($order_id, $order)
    {

        $order = $this->orders->get_order($order_id);

        $base_link = 'https://c2mpbtrck.com/cpaCallback';
        $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=approve&partner=barents&lead_id=' . $order_id;

        $ch = curl_init($link_lead);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);

        $result = $this->orders->update_order($order_id, array('lead_postback_date' => date('Y-m-d H:i'), 'lead_postback_type' => 'approved'));

        $this->to_log(__METHOD__, 'approved', $link_lead, $res, 'lead_click2money.txt');
    }

    public function send_cancelled_postback_click2money($order_id, $order)
    {
        $order = $this->orders->get_order($order_id);
        
        $base_link = 'https://c2mpbtrck.com/cpaCallback';
        $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=reject&partner=barents&lead_id=' . $order_id;

        $ch = curl_init($link_lead);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);

        $this->orders->update_order($order_id, array('lead_postback_date' => date('Y-m-d H:i'), 'lead_postback_type' => 'cancelled'));

        $this->to_log(__METHOD__, 'cancelled', $link_lead, $res, 'lead_click2money.txt');
    }

    public function to_log($method, $url, $request, $response, $log_filename = 'leads.txt')
    {
        $log = 1; // 1 - включить логирование, 0 - выключить

        if (empty($log))
            return false;

        $filename = $this->config->root_dir . 'logs/' . $log_filename;

        if (date('d', filemtime($filename)) != date('d')) {
            $file_basename = pathinfo($log_filename, PATHINFO_BASENAME);
            $archive_filename = $this->config->root_dir . 'logs/archive/' . $file_basename . '_' . date('ymd', filemtime($filename));
            rename($filename, $archive_filename);
            file_put_contents($filename, "\xEF\xBB\xBF");
        }


        $string = '';
        $string .= PHP_EOL . '******************************************************' . PHP_EOL;
        $string .= date('d.m.Y H:i:s') . PHP_EOL;
        $string .= $method . PHP_EOL;
        $string .= $url . PHP_EOL;

        if (!empty($_SERVER['REMOTE_ADDR']))
            $string .= PHP_EOL . 'IP: ' . $_SERVER['REMOTE_ADDR'];
        if (!empty($_SESSION['referer']))
            $string .= PHP_EOL . 'SESSION_REFERER: ' . $_SESSION['referer'];
        if (isset($_SERVER['HTTP_REFERER']))
            $string .= PHP_EOL . 'REFERER: ' . $_SERVER['HTTP_REFERER'] . PHP_EOL;
        if (isset($_SESSION['admin']))
            $string .= PHP_EOL . 'IS_ADMIN: ' . PHP_EOL;

        $string .= PHP_EOL . 'REQUEST:' . PHP_EOL;
        if (is_array($request) || is_object($request)) {
            foreach ($request as $rkey => $ritem) {
                if (is_array($ritem) || is_object($ritem)) {

                    $string .= $rkey . ' => (' . PHP_EOL;
                    foreach ($ritem as $subrkey => $subritem)
                        $string .= '    ' . $subrkey . ' => ' . strval($subritem) . PHP_EOL;

                    $string .= ')' . PHP_EOL;
                } else {
                    $string .= $rkey . ' => ' . $ritem . PHP_EOL;
                }
            }
        } else {
            $string .= $request . PHP_EOL;
        }

        $string .= PHP_EOL . 'RESPONSE:' . PHP_EOL;
        if (is_array($response) || is_object($response)) {
            foreach ($response as $key => $item) {
                if (is_array($item) || is_object($item)) {
                    $string .= $key . ' => (' . PHP_EOL;
                    foreach ($item as $subkey => $subitem) {
                        if (is_array($subitem) || is_object($subitem)) {
                            $string .= '    ' . $subkey . ' => (' . PHP_EOL;
                            foreach ($subitem as $subsubkey => $subsubitem)
                                @$string .= '        ' . $subsubkey . ' => ' . strval($subsubitem) . PHP_EOL;

                            $string .= '    )' . PHP_EOL;
                        } else {
                            $string .= '    ' . $subkey . ' => ' . strval($subitem) . PHP_EOL;
                        }
                    }
                    $string .= ')' . PHP_EOL;
                } else {
                    $string .= $key . ' => ' . $item . PHP_EOL;
                }
            }
        } else {
            $string .= $response . PHP_EOL;
        }


        $string .= PHP_EOL . 'END' . PHP_EOL;
        $string .= PHP_EOL . '******************************************************' . PHP_EOL;

        file_put_contents($filename, $string, FILE_APPEND);
    }

    // public function send_approved_postback_click2money($order_id, $order)
    // {
    //     $base_link = 'https://c2mpbtrck.com/cpaCallback';
    //     $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=approve&partner=ecozaym&lead_id=' . $order_id;

    //     $ch = curl_init($link_lead);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    //     $res = curl_exec($ch);
    //     curl_close($ch);

    //     $this->orders->update_order($order_id, array('leadcraft_postback_date' => date('Y-m-d H:i'), 'leadcraft_postback_type' => 'approved'));

    //     return $res;
    // }

    // public function send_cancelled_postback_click2money($order_id, $order)
    // {
    //     $base_link = 'https://c2mpbtrck.com/cpaCallback';
    //     $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=reject&partner=ecozaym&lead_id=' . $order_id;

    //     $ch = curl_init($link_lead);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    //     $res = curl_exec($ch);
    //     curl_close($ch);

    //     $this->orders->update_order($order_id, array('leadcraft_postback_date' => date('Y-m-d H:i'), 'leadcraft_postback_type' => 'cancelled'));

    //     return $res;
    // }


    public function sendRejectToAlians($orderId)
    {

        $order = $this->orders->get_order($orderId);
        $address = $this->addresses->get_address($order->faktaddress_id);

        $address_locacity = '';
        if($address->city){
            $address_locacity = $address->city;
        }
        else{
            $address_locacity = $address->locality_type . '.' . $address->locality;
        }

        $link = "https://alianscpa.ru/api/contacts?phone=$order->phone_mobile&token=cf9eaf664759eb5e6a1d93b41edf85b4&email=$order->email&name=$order->firstname&surname=$order->lastname&patronymic=$order->patronymic&date_birthday=$order->birth&geo=$address_locacity";

        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);

        return 1;
    }

    public function sendPendingPostbackToAlians($orderId, $status)
    {

        $order = $this->orders->get_order($orderId);
        $goal = 'loan';
        $click_id = $order->click_hash;
        $sub1 = $order->utm_sub_id;
        $status = $status;
        $amount = $order->amount;

        $link = "https://alianscpa.ru/postback/get/partners?token=64bc380cb551e14443513654fe3ad37b&from=barens&status=$status&click_id=$click_id&sub1=$sub1";

        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        curl_close($ch);


        $this->logging_(__METHOD__, 'Leadstech', $link, 'ok', 'alianscpa.txt', 'logs/');

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

    public function sendPendingPostbackLeadstech($orderId, $user_id, $goal, $status)
    {

        $order = $this->orders->get_order($orderId);
        $click_id = $order->click_hash;
        $status = $status;


        $link = "https://offers.leads.tech/add-conversion/?click_id=$click_id&goal_id=$goal&status=$status&transaction_id=$orderId&sumConfirm=$order->amount";

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

        $this->logging_(__METHOD__, 'leadstech', $link, 'ok', 'leads-tech.txt', 'logs/');

        return 1;
    }
}