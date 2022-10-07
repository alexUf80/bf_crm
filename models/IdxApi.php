<?php

class IdxApi extends Core
{
    protected $accessKey = 'barents-finans-4754e180843f443f3ea7c22329edf986c382cac8';
    protected $secretKey = 'a42855fd62f5b3c0778b5809149a1ee07c9d2838';

    public function search($person)
    {
        $lastname   = $person['lastname'];
        $firstname  = $person['firstname'];
        $patronymic = $person['patronymic'];
        $birth      = $person['birth'];
        $phone      = $person['phone'];

        $person =
            [
                'personLastName'  => $lastname,
                'personFirstName' => $firstname,
                'phone'           => $phone
            ];

        if(!empty($birth))
            $person['personBirthDate'] = date('d.m.Y', strtotime($birth));

        if(!empty($patronymic))
            $person['personMidName'] = $patronymic;

        return $this->send_request($person);
    }

    private function send_request($params)
    {
        $headers =
            [
                'Content-Type: application/json',
                'Accept: application/json'
            ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.id-x.org/idx/api2/verifyPhone',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_CUSTOMREQUEST => 'POST'
        ]);

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($response);
    }
}