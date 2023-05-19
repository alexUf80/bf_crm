<?php

class Leadgens extends Core
{

    public function send_pending_postback_click2money($order_id, $order)
    {

        // $base_link = 'https://c2mpbtrck.com/cpaCallback';
        // $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=hold&partner=finfive&lead_id=' . $order->id_1c;

        // $ch = curl_init($link_lead);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        // $res = curl_exec($ch);
        // curl_close($ch);

        $result = $this->orders->update_order($order_id, array('lead_postback_date' => date('Y-m-d H:i'), 'lead_postback_type' => 'pending'));

        // $this->to_log(__METHOD__, 'hold', $link_lead, $res, 'lead_click2money.txt');
    }

    public function send_approved_postback_click2money($order_id, $order)
    {

        var_dump('send_approved_postback_click2money');
        die;

        $counter = 1;

        $file = 'logs/lead_click2money_counter.txt';

        var_dump($order_id);
        var_dump($order);

        if (file_exists($file)) {
            $counter += file_get_contents($file);
        }

        file_put_contents($file, $counter);
        $base_link = 'https://c2mpbtrck.com/cpaCallback';
        $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=approve&partner=finfive&lead_id=' . $order->id_1c;

        $ch = curl_init($link_lead);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);

        var_dump($ch);

        $result = $this->orders->update_order($order_id, array('lead_postback_date' => date('Y-m-d H:i'), 'lead_postback_type' => 'approved'));

        var_dump($result);

        // $this->to_log(__METHOD__, 'approved', $link_lead, $res, 'lead_click2money.txt');
    }

    public function send_cancelled_postback_click2money($order_id, $order)
    {
        var_dump('send_cancelled_postback_click2money');
        die;

        $base_link = 'https://c2mpbtrck.com/cpaCallback';
        $link_lead = $base_link . '?cid=' . $order->click_hash . '&action=reject&partner=finfive&lead_id=' . $order->id_1c;

        $ch = curl_init($link_lead);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);

        $this->orders->update_order($order_id, array('lead_postback_date' => date('Y-m-d H:i'), 'lead_postback_type' => 'cancelled'));

        // $this->to_log(__METHOD__, 'cancelled', $link_lead, $res, 'lead_click2money.txt');
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
}
