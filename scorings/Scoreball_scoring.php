<?php

class Scoreball_scoring extends Core
{
    private $scoring_id;
    private $error = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function run_scoring($scoring_id)
    {
        if ($scoring = $this->scorings->get_scoring($scoring_id)) {
            $this->scoring_id = $scoring_id;

            if ($user = $this->users->get_user((int)$scoring->user_id)) {

                $regaddress = $this->Addresses->get_address($user->regaddress_id);

                if ($regaddress->district) {
                    $city = $regaddress->district;
                } elseif ($regaddress->locality) {
                    $city = $regaddress->locality;
                } else {
                    $city = $regaddress->city;
                }

                return $this->scoring(
                    $user->firstname,
                    $user->patronymic,
                    $user->lastname,
                    $city,
                    $regaddress->street,
                    $user->birth,
                    $user->birth_place,
                    $user->passport_serial,
                    $user->passport_date,
                    $user->passport_issued,
                    $user->gender,
                    $user->client_status,
                    $user->inn,
                    $user->subdivision_code,
                    $scoring_id
                );
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
        $Regcity,
        $Regstreet,
        $birth,
        $birth_place,
        $passport_serial,
        $passport_date,
        $passport_issued,
        $gender,
        $client_status,
        $inn,
        $subdivision_code,
        $scoring_id
    )
    {
        $genderArr = [
            'male' => 1,
            'female' => 2
        ];
        if (empty($inn)) {
            $inn = '000000000000';
        }
        $json = '{
            "user": {
                "passport": {
                    "series": "'. substr($passport_serial, 0, 4) .'",
                    "number": "'. substr($passport_serial, 5) .'",
                    "issued_date": "' . date('Y-m-d', strtotime($passport_date)) . '",
                    "issued_by": "' . addslashes($passport_issued) . '",
                    "issued_city": "' . addslashes($Regcity) . '",
                    "division_code": "' . addslashes($subdivision_code).'"
                },
                "person": {
                    "last_name": "' . addslashes($lastname) . '",
                    "first_name": "' . addslashes($firstname) . '",
                    "middle_name": "' . addslashes($patronymic) . '",
                    "birthday": "' . date('Y-m-d', strtotime($birth)) . '",
                    "birthday_city": "' . addslashes($birth_place) . '",
                    "gender": ' . addslashes($genderArr[$gender]) . '
                },
                "registration_address": {
                    "city": "' . addslashes($Regcity) . '",
                    "street": "' . addslashes($Regstreet) . '"
                },
                "registration_numbers": {
                    "taxpayer_number": "'.$inn.'"
                }
            },
            "requisites": {
                "member_code": "9R01SS000000",
                "user_id": "9R01SS000002",
                "password": "Gpj895RP"
            },
            "retail_score_details": {
                "product_addon_code": "M92N"
            },
            "loan_details": {
                "purpose_code": "2"
            },
            "extra_parameters": {}
        }';

        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://185.182.111.110:9009/api/v2/scoring/sign/barents/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        
        $result = json_decode($response, true);
        if (!$result) {
            $add_scoring = array(
                'status' => 'error',
                'body' => '',
                'success' => (int)$result,
                'string_result' => 'Ошибка запроса'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }

        if ($result['status'] == 'error') {
            if (json_encode($result['data']) == "No subject found for this inquiry") {
                $add_scoring = array(
                    'body' => '',
                    'status' => 'error',
                    'success' => (int)true,
                    'string_result' => 'Неуспешный ответ: ' . 'субъект не найден',
                );
            } else {
                $add_scoring = array(
                    'body' => '',
                    'status' => 'error',
                    'success' => (int)false,
                    'string_result' => 'Неуспешный ответ: ' . json_encode($result['data'], JSON_UNESCAPED_UNICODE)
                );
            }

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }

        $scoring_type = $this->scorings->get_type('scoreball');
        $scoreball = $scoring_type->params['scoreball'];

        $scoring_type_location = $this->scorings->get_type('location');

        if ($result['score'] < $scoreball) {
            $add_scoring = array(
                'status' => 'completed',
                'body' => serialize($result),
                'success' => 0,
                'string_result' => 'Скорбалл ниже установленного порога'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }

        $scoring = $this->scorings->get_scoring($scoring_id);
        if (!$order = $this->orders->get_order($scoring->order_id)){
             $add_scoring = array(
                'status' => 'error',
                'body' => serialize($result),
                'success' => 0,
                'string_result' => 'не найдена заявка'
            );
            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            return $add_scoring;
        }
            

        $add_scoring = array(
            'status' => 'completed',
            'body' => serialize($result),
            'success' => 1,
            'string_result' => 'Проверки пройдены'
        );

        $this->scorings->update_scoring($this->scoring_id, $add_scoring);
        return $add_scoring;
    }
}