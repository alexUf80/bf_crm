<?php

class LeadFinancesPostbacks implements ApiInterface
{
    protected static $link_head = 'https://offers.leads.tech/add-conversion/';

    public static function sendRequest($request)
    {
        $status = $request->status;
        $clickHash = $request->click_hash;
        $goalId = $request->goalId;
        $amount = $request->amount;

        $link = self::$link_head . '?click_id=' . $clickHash . '&goal_id=' . $goalId . '&status=' . $status . '&transaction_id='.rand(0, 999999).'&sumConfirm=' . $amount;

        return self::curl($link);
    }

    public static function curl($link)
    {
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $res = curl_exec($ch);
        curl_close($ch);

        return self::response($res);
    }

    public static function response($response)
    {
        self::toLogs($response);
        $response = json_decode($response, true);
        return $response;
    }

    public static function toLogs($log)
    {
        $insert =
            [
                'className' => self::class,
                'log' => $log
            ];

        LogsORM::insert($insert);
    }
}