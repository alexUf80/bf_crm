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

        return 1;
    }
}