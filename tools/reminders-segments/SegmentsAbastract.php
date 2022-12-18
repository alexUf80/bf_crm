<?php

abstract class SegmentsAbastract
{
    abstract static function sendReminder($request);

    public static function send($send)
    {
        $api_code = 'CEC47EEB-DA21-5CDB-9431-7E53B513FAA5';

        $smsru = new Smsru($api_code);

        $data = new stdClass();
        $data->to = $send['phone'];
        $data->text = $send['msg'];

        $sms = $smsru->send_one($data);

        if ($sms->status == "OK")
            $resp = "Сообщение отправлено успешно";
        else
            $resp = "Текст ошибки: $sms->status_text" . ' Телефон: ' . $send['phone'] . ' Сообщение: ' . $send['msg'];

        $log =
            [
                'className' => self::class,
                'log' => $resp
            ];

        LogsORM::insert($log);

        return $resp;
    }
}