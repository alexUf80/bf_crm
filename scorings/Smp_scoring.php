<?php

class Smp_scoring extends Core
{

    public $scoring_id;
    public function __construct()
    {
        parent::__construct();
    }

    public function run_scoring($scoring_id)
    {
        if ($scoring = $this->scorings->get_scoring($scoring_id)) {

            if ($user = $this->users->get_user((int)$scoring->user_id)) {
                $this->scoring_id = $scoring->id;
                if ($order = $this->orders->get_order($scoring->order_id)) {
                
                    return $this->scoring(
                        $user->firstname,
                        $user->patronymic,
                        $user->lastname,
                        $user->birth,
                        $user->passport_serial,
                        $user->passport_date,
                        $order
                    );
                } else {
                    $update = array(
                        'status' => 'error',
                        'string_result' => 'не найдена заявка'
                    );
                    $this->scorings->update_scoring($scoring_id, $update);
                    return $update;
                }
            } else {
                $update = array(
                    'status' => 'error',
                    'string_result' => 'не найден пользователь'
                );
                $this->scorings->update_scoring($scoring_id, $update);
                return $update;
            }
        }
    }

    public function scoring(
        $firstname,
        $patronymic,
        $lastname,
        $birth,
        $passport_serial,
        $passport_date,
        $order

    ) {
        $json = '{
            "inquiry": {
                "id": "' . $order->order_id . '",
                "amount": ' . $order->amount . ',
                "request_type": "2"
            },
            "user": {
                "passport": {
                    "series": "' . substr($passport_serial, 0, 4) . '",
                    "number": "' . substr($passport_serial, 5) . '",
                    "issue_date": "' . date('Y-m-d', strtotime($passport_date)) . '"
                },
                "person": {
                    "last_name": "' . addslashes($lastname) . '",
                    "first_name": "' . addslashes($firstname) . '",
                    "middle_name": "' . addslashes($patronymic) . '",
                    "date_of_birth": "' . date('Y-m-d', strtotime($birth)) . '"
                }
            },
            "consent": {
                "issue_date": "' . date('Y-m-d', strtotime($order->date)) . '",
                "validity_code": "1",
                "purpose_code": [
                "2"
                ]},

            "requisites": {
                "member_code": "9R01SS000000",
                "taxpayer_number": "9723120835",
                "registration_number": "1217700350812",
                "full_name": "ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ МИКРОКРЕДИТНАЯ КОМПАНИЯ \"БАРЕНЦ ФИНАНС\"",
                "short_name": "ООО МКК \"БАРЕНЦ ФИНАНС\"",
                "password": "12345678"
        }
    }';

    var_dump($json);
    echo '<hr><hr>';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://185.182.111.110:9010/api/v2/history/amp/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $result = json_decode($response, true);
        var_dump($response);
        echo '<hr>';
        var_dump($result);
        curl_close($curl);
        $body = [];
        $body['result'] = $result;
        $body['response'] = $response;

        $add_scoring = array(
            'status' => 'completed',
            'body' => serialize($body),
            'success' => 1,
            'string_result' => 'Получены данные'
        );

        $this->scorings->update_scoring($this->scoring_id, $add_scoring);


        return $result;
    }
}
