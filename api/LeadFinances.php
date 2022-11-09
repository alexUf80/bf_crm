<?php

class LeadFinances implements ApiInterface
{
    protected static $link = 'https://api.gate.leadfinances.com/v1/lead/add';
    protected static $token = 'b788d4d7041148fc9c63d6144720b5ad';


    public static function get_request($request)
    {
        $user = UsersORM::with(['regAddress', 'factAddress'])->find(18443)->toArray();
        list($passport_serial, $passport_number) = explode('-', $user['passport_serial']);

        $params =
            [
                'token'               => self::$token,
                'first_name'          => $user['firstname'],
                'middle_name'         => $user['patronymic'] ?? '',
                'last_name'           => $user['lastname'],
                'city_fact'           => $user['reg_address']['region'],
                'region_fact'         => $user['reg_address']['region'],
                'series_passport'     => $passport_serial,
                'number_passport'     => $passport_number,
                'date_issue_passport' => date('Y-m-d', strtotime($user['passport_date'])),
                'issued_by_passport'  => $user['passport_issued'],
                'channel_id'          => 1,
                'channel_name'        => 'otkaz'
            ];

        return self::curl($params);
    }

    public static function curl($params)
    {
        $headers =
            [
                'Content-Type: multipart/form-data'
            ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::$link,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_CUSTOMREQUEST => 'POST'
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return self::response($response);
    }

    public static function response($response)
    {
        $response = json_decode($response, true);
        return $response;
    }
}