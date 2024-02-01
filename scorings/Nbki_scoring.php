<?php

class Nbki_scoring extends Core
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
                
                sleep(1);
                $this->nbki_extra_scorings();
                
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
        "password": "Gpj896RP"
    }
}';

//var_dump($json);
        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($json);echo '</pre><hr />';
//exit;
        $curl = curl_init();


        curl_setopt_array($curl, array(
            // CURLOPT_URL => 'http://185.182.111.110:9009/api/v2/history/sign/',
            // CURLOPT_URL => 'http://185.182.111.110:9009/api/v2/history/sign/barents/',
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

            sleep(1);
            $this->nbki_extra_scorings();

            return $add_scoring;
        }

        if (empty($result['data']) || isset($result['status'])) {
            if (str_contains(json_encode($result['data']), "No subject found for this inquiry")) {
                $add_scoring = array(
                    'body' => serialize($result),
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
            
            sleep(1);
            $this->nbki_extra_scorings();

            return $add_scoring;
        }

        $scoring_type = $this->scorings->get_type('nbki');
        // $max_number_of_active = $scoring_type->params['nk']['nbki_number_of_active'];
        // $max_share_of_overdue_by_closed = $scoring_type->params['nk']['open_to_close_ratio'];
        // $max_share_of_overdue_by_active = $scoring_type->params['nk']['open_to_active_ratio'];
        $nbki_green = $scoring_type->params['nk']['nbki_green'];
        $nbki_yellow = $scoring_type->params['nk']['nbki_yellow'];
        $nbki_red = $scoring_type->params['nk']['nbki_red'];

        $scoring_type_location = $this->scorings->get_type('location');

        /*
        if ($result['number_of_active'] >= $max_number_of_active) {
            $add_scoring = array(
                'status' => 'completed',
                'body' => serialize($result),
                'success' => 0,
                'string_result' => 'превышен допустимый порог активных займов'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            sleep(1);
            $this->nbki_extra_scorings();

            return $add_scoring;
        }

        var_dump((int)$result['share_of_overdue_by_active'], $max_share_of_overdue_by_active);
        if ($result['share_of_overdue_by_active'] >= $max_share_of_overdue_by_active) {
            $add_scoring = array(
                'status' => 'completed',
                'body' => serialize($result),
                'success' => 0,
                'string_result' => 'превышен допустимый порог доли просроченных к активным'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            sleep(1);
            $this->nbki_extra_scorings();

            return $add_scoring;
        }

        if ($result['share_of_overdue_by_closed'] >= $max_share_of_overdue_by_closed) {
            $add_scoring = array(
                'status' => 'completed',
                'body' => serialize($result),
                'success' => 0,
                'string_result' => 'превышен допустимый порог доли просроченных к закрытым'
            );

            $this->scorings->update_scoring($this->scoring_id, $add_scoring);

            sleep(1);
            $this->nbki_extra_scorings();

            return $add_scoring;
        }
        */

        $scoring = $this->scorings->get_scoring($scoring_id);
        if ($order = $this->orders->get_order($scoring->order_id)){
            $faktaddress = $this->Addresses->get_address($order->faktaddress_id);
            $order->Regregion = $faktaddress->region;

            if (empty($order->Regregion))
            {
                $add_scoring = array(
                    'status' => 'error',
                    'body' => serialize($result),
                    'success' => 0,
                    'string_result' => 'в заявке не указан регион регистрации'
                );
                $this->scorings->update_scoring($this->scoring_id, $add_scoring);

                sleep(1);
                $this->nbki_extra_scorings();

                return $add_scoring;
            }
            else
            {
                $order->Regregion = trim($order->Regregion);
                $order_Regregion = $order->Regregion;
                if(mb_substr($order->Regregion, -2) == " г" ||
                mb_substr($order->Regregion, 0, 2) == "г " ||
                mb_substr($order->Regregion, -4) == " обл" ||
                mb_substr($order->Regregion, -5) == " обл." ||
                mb_substr($order->Regregion, -8) == " область" ||
                mb_substr($order->Regregion, -8) == " ОБЛАСТЬ" ||
                mb_substr($order->Regregion, -5) == " край" ||
                mb_substr($order->Regregion, -5) == " Край" ||
                mb_substr($order->Regregion, -11) == " республика" ||
                mb_substr($order->Regregion, -11) == " Республика" ||
                mb_substr($order->Regregion, -5) == " Респ" ||
                mb_substr($order->Regregion, 0, 5) == "Респ " ||
                mb_substr($order->Regregion, 0, 11) == "Республика " ){
                    $order_Regregion = str_replace(["г ", " г", " область", " ОБЛАСТЬ", " обл.", " обл", " край", " Край", " республика", " Республика", " Респ", "Респ ", "Республика "], "", $order->Regregion);
                }
                $exception_regions = array_map('trim', explode(',', $scoring_type_location->params['regions']));
                $order->Regregion = $order_Regregion;
                // if(isset(explode(' ', $order->Regregion)[1]) && mb_strtolower(explode(' ', $order->Regregion)[1]) == 'обл'){
                //     $order->Regregion = explode(' ', $order->Regregion)[0];
                // }
            
                $score = !in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $exception_regions);
                
                $red_regions = array_map('trim', explode(',', $scoring_type_location->params['red-regions']));
                $red = in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $red_regions);

                $yellow_regions = array_map('trim', explode(',', $scoring_type_location->params['yellow-regions']));
                $yellow = in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $yellow_regions);
                
                $gray_regions = array_map('trim', explode(',', $scoring_type_location->params['gray-regions']));
                $gray = in_array(mb_strtolower(trim($order->Regregion), 'utf8'), $gray_regions);

                $mm_scoring = 
                $zone_result = '';
                if ($score){
                    $update['string_result'] = 'Допустимый регион: '.$order->Regregion;
                    if($yellow){
                        if ($result['score'] < $nbki_yellow){
                            $string_result = "Дно. ЖЕЛТАЯ ЗОНА";
                            $success = 0;
                        }
                        else{
                            $string_result = "Проверки пройдены. ЖЕЛТАЯ ЗОНА";
                            $success = 1;
                        }
                    }
                    elseif ($red) {
                        if ($result['score'] < $nbki_red) {
                            $string_result = "Дно. КРАСНАЯ ЗОНА";
                            $success = 0;
                        }
                        else{
                            $string_result = "Проверки пройдены. КРАСНАЯ ЗОНА";
                            $success = 1;
                        }
                    }
                    elseif ($gray) {
                        if ($result['score'] < $nbki_green) {
                            $string_result = "Дно. СЕРАЯ ЗОНА";   
                            $success = 0;
                        }
                        else{
                            $string_result = "Проверки пройдены. СЕРАЯ ЗОНА";
                            $success = 1;
                        }
                    }
                    else {
                        if ($result['score'] < $nbki_green) {
                            $string_result = "Дно. ЗЕЛЕНАЯ ЗОНА";
                            $success = 0;
                        }
                        else{
                            $string_result = "Проверки пройдены. ЗЕЛЕНАЯ ЗОНА";
                            $success = 1;
                        }
                    }

                }
                else{
                    $string_result = "Проверки пройдены. ОЧЕНЬ КРАСНАЯ ЗОНА ".$score;
                }
                
                $add_scoring = array(
                    'status' => 'completed',
                    'body' => serialize($result),
                    'success' => $success,
                    'string_result' => $string_result
                );
                $this->scorings->update_scoring($this->scoring_id, $add_scoring);

                sleep(1);
                $this->nbki_extra_scorings();
                
                return $add_scoring;

            }
            
        }
        else
        {
            $add_scoring = array(
                'status' => 'error',
                'body' => serialize($result),
                'success' => 0,
                'string_result' => 'не найдена заявка'
            );
            $this->scorings->update_scoring($this->scoring_id, $add_scoring);
            
            sleep(1);
            $this->nbki_extra_scorings();

            return $add_scoring;
        }
            

        $add_scoring = array(
            'status' => 'completed',
            'body' => serialize($result),
            'success' => 1,
            'string_result' => 'Проверки пройдены'
        );

        $this->scorings->update_scoring($this->scoring_id, $add_scoring);

        sleep(1);
        $this->nbki_extra_scorings();

        return $add_scoring;
    }

    private function nbki_extra_scorings()
    {
        $nbkiScor = $this->scorings->get_scoring($this->scoring_id);

        $nbki_extra_scoring = $this->NbkiExtraScorings->get($nbkiScor->order_id);
            
        if ($nbki_extra_scoring == 0){

            $add_nbki = [];

            $add_nbki['order_id'] = $nbkiScor->order_id;

            $add_nbki['score_id'] = 0;
            if (!is_null($nbkiScor->id)) {
                $add_nbki['score_id'] = $nbkiScor->id;
            }
            
            if ($nbkiScor) {
                $nbkiParams = unserialize($nbkiScor->body);
                
                if (isset($nbkiParams['number_of_active'])) {
                    if (is_array($nbkiParams['number_of_active'])) 
                        $add_nbki['number_of_active'] = $nbkiParams['number_of_active'][0];
                    else
                        $add_nbki['number_of_active'] = $nbkiParams['number_of_active'];
                }
                if (is_null($add_nbki['number_of_active'])){
                    $add_nbki['number_of_active'] = 0;
                }

                if (isset($nbkiParams['count_of_closed'])) {
                    if (is_array($nbkiParams['count_of_closed'])) 
                        $add_nbki['count_of_closed'] = $nbkiParams['count_of_closed'][0];
                    else
                        $add_nbki['count_of_closed'] = $nbkiParams['count_of_closed'];
                }
                if (is_null($add_nbki['count_of_closed'])){
                    $add_nbki['count_of_closed'] = 0;
                }
                
                if (isset($nbkiParams['count_of_overdue'])) {
                    if (is_array($nbkiParams['count_of_overdue'])) 
                        $add_nbki['count_of_overdue'] = $nbkiParams['count_of_overdue'][0];
                    else
                        $add_nbki['count_of_overdue'] = $nbkiParams['count_of_overdue'];
                }
                if (is_null($add_nbki['count_of_overdue'])){
                    $add_nbki['count_of_overdue'] = 0;
                }

                if (isset($nbkiParams['share_of_overdue_by_active']) && !is_null($nbkiParams['share_of_overdue_by_active'])){
                    if (is_array($nbkiParams['share_of_overdue_by_active'])) 
                        $add_nbki['share_of_overdue_by_active'] = $nbkiParams['share_of_overdue_by_active'][0];
                    else
                        $add_nbki['share_of_overdue_by_active'] = $nbkiParams['share_of_overdue_by_active'];
                }
                if (is_null($add_nbki['share_of_overdue_by_active'])){
                    $add_nbki['share_of_overdue_by_active'] = 0;
                }
                
                if (isset($nbkiParams['extra_scoring']['active_loans_credit_limit_sum'])) 
                    $add_nbki['active_loans_credit_limit_sum'] = $nbkiParams['extra_scoring']['active_loans_credit_limit_sum'];
                if (isset($nbkiParams['extra_scoring']['closed_loans_credit_limit_sum'])) 
                    $add_nbki['closed_loans_credit_limit_sum'] = $nbkiParams['extra_scoring']['closed_loans_credit_limit_sum'];
                if (isset($nbkiParams['extra_scoring']['monthly_active_loans_payment_sum'])) 
                    $add_nbki['monthly_active_loans_payment_sum'] = $nbkiParams['extra_scoring']['monthly_active_loans_payment_sum'];
                if (isset($nbkiParams['extra_scoring']['overdue_amount_sum'])) 
                    $add_nbki['overdue_amount_sum'] = $nbkiParams['extra_scoring']['overdue_amount_sum'];
                if (isset($nbkiParams['extra_scoring']['current_year_max_overdue_amount'])) 
                    $add_nbki['current_year_max_overdue_amount'] = $nbkiParams['extra_scoring']['current_year_max_overdue_amount'];
                if (isset($nbkiParams['extra_scoring']['microloans_over_last_90_days_count'])) 
                    $add_nbki['microloans_over_last_90_days_count'] = $nbkiParams['extra_scoring']['microloans_over_last_90_days_count'];
                if (isset($nbkiParams['extra_scoring']['active_microloan_count'])) 
                    $add_nbki['active_microloan_count'] = $nbkiParams['extra_scoring']['active_microloan_count'];
                
                if (isset($nbkiParams['extra_scoring']['active_pay_day_loans_count'])) 
                    $add_nbki['active_pay_day_loans_count'] = $nbkiParams['extra_scoring']['active_pay_day_loans_count'];
                if (isset($nbkiParams['extra_scoring']['active_pay_day_loans_with_extension_count'])) 
                    $add_nbki['active_pay_day_loans_with_extension_count'] = $nbkiParams['extra_scoring']['active_pay_day_loans_with_extension_count'];
                if (isset($nbkiParams['extra_scoring']['active_credit_lines_count'])) 
                    $add_nbki['active_credit_lines_count'] = $nbkiParams['extra_scoring']['active_credit_lines_count'];
                if (isset($nbkiParams['extra_scoring']['active_microloans_with_wrong_term_days_count'])) 
                    $add_nbki['active_microloans_with_wrong_term_days_count'] = $nbkiParams['extra_scoring']['active_microloans_with_wrong_term_days_count'];

                if (isset($nbkiParams['extra_scoring']['active_credit_cards_count'])) 
                    $add_nbki['active_credit_cards_count'] = $nbkiParams['extra_scoring']['active_credit_cards_count'];
                if (isset($nbkiParams['extra_scoring']['active_other_loans_count'])) 
                    $add_nbki['active_other_loans_count'] = $nbkiParams['extra_scoring']['active_other_loans_count'];
                
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_overdue_count'])) 
                    $add_nbki['pdl_overdue_count'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_overdue_count'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_limit_share'])) 
                    $add_nbki['pdl_npl_limit_share'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_limit_share'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_90_limit_share'])) 
                    $add_nbki['pdl_npl_90_limit_share'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_90_limit_share'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_current_limit_max'])) 
                    $add_nbki['pdl_current_limit_max'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_current_limit_max'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_3m_limit'])) 
                    $add_nbki['pdl_last_3m_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_3m_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_good_max_limit'])) 
                    $add_nbki['pdl_last_good_max_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_good_max_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_good_limit'])) 
                    $add_nbki['pdl_good_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_good_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_prolong_3m_limit'])) 
                    $add_nbki['pdl_prolong_3m_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_prolong_3m_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['consum_current_limit_max'])) 
                    $add_nbki['consum_current_limit_max'] = $nbkiParams['barents_scoring']['client_scoring_data']['consum_current_limit_max'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['consum_good_limit'])) 
                    $add_nbki['consum_good_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['consum_good_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['days_from_last_closed'])) 
                    $add_nbki['days_from_last_closed'] = $nbkiParams['barents_scoring']['client_scoring_data']['days_from_last_closed'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_3000_500_paid_count_wo_del'])) 
                    $add_nbki['prev_3000_500_paid_count_wo_del'] = $nbkiParams['barents_scoring']['client_scoring_data']['prev_3000_500_paid_count_wo_del'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_paid_percent_sum'])) 
                    $add_nbki['prev_paid_percent_sum'] = $nbkiParams['barents_scoring']['client_scoring_data']['prev_paid_percent_sum'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_max_delay'])) 
                    $add_nbki['prev_max_delay'] = $nbkiParams['barents_scoring']['client_scoring_data']['prev_max_delay'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['last_credit_delay'])) 
                    $add_nbki['last_credit_delay'] = $nbkiParams['barents_scoring']['client_scoring_data']['last_credit_delay'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['current_overdue_sum'])) 
                    $add_nbki['current_overdue_sum'] = $nbkiParams['barents_scoring']['client_scoring_data']['current_overdue_sum'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['closed_to_total_credits_count_share'])) 
                    $add_nbki['closed_to_total_credits_count_share'] = $nbkiParams['barents_scoring']['client_scoring_data']['closed_to_total_credits_count_share'];

                // ------
                if (is_null($add_nbki['active_loans_credit_limit_sum'])){
                    $add_nbki['active_loans_credit_limit_sum'] = 0;
                }
                if (is_null($add_nbki['closed_loans_credit_limit_sum'])){
                    $add_nbki['closed_loans_credit_limit_sum'] = 0;
                }
                if (is_null($add_nbki['monthly_active_loans_payment_sum'])){
                    $add_nbki['monthly_active_loans_payment_sum'] = 0;
                }
                if (is_null($add_nbki['overdue_amount_sum'])){
                    $add_nbki['overdue_amount_sum'] = 0;
                }
                if (is_null($add_nbki['current_year_max_overdue_amount'])){
                    $add_nbki['current_year_max_overdue_amount'] = 0;
                }
                if (is_null($add_nbki['microloans_over_last_90_days_count'])){
                    $add_nbki['microloans_over_last_90_days_count'] = 0;
                }
                if (is_null($add_nbki['active_microloan_count'])){
                    $add_nbki['active_microloan_count'] = 0;
                }
                if (is_null($add_nbki['active_pay_day_loans_count'])){
                    $add_nbki['active_pay_day_loans_count'] = 0;
                }
                if (is_null($add_nbki['active_loans_credit_limit_sum'])){
                    $add_nbki['active_loans_credit_limit_sum'] = 0;
                }
                if (is_null($add_nbki['active_credit_lines_count'])){
                    $add_nbki['active_credit_lines_count'] = 0;
                }
                if (is_null($add_nbki['active_microloans_with_wrong_term_days_count'])){
                    $add_nbki['active_microloans_with_wrong_term_days_count'] = 0;
                }
                if (is_null($add_nbki['active_credit_cards_count'])){
                    $add_nbki['active_credit_cards_count'] = 0;
                }
                if (is_null($add_nbki['active_other_loans_count'])){
                    $add_nbki['active_other_loans_count'] = 0;
                }
                if (is_null($add_nbki['pdl_overdue_count'])){
                    $add_nbki['pdl_overdue_count'] = 0;
                }
                if (is_null($add_nbki['pdl_npl_limit_share'])){
                    $add_nbki['pdl_npl_limit_share'] = 0;
                }
                if (is_null($add_nbki['pdl_npl_90_limit_share'])){
                    $add_nbki['pdl_npl_90_limit_share'] = 0;
                }
                if (is_null($add_nbki['pdl_current_limit_max'])){
                    $add_nbki['pdl_current_limit_max'] = 0;
                }
                if (is_null($add_nbki['pdl_last_3m_limit'])){
                    $add_nbki['pdl_last_3m_limit'] = 0;
                }
                if (is_null($add_nbki['pdl_last_good_max_limit'])){
                    $add_nbki['pdl_last_good_max_limit'] = 0;
                }
                if (is_null($add_nbki['pdl_good_limit'])){
                    $add_nbki['pdl_good_limit'] = 0;
                }
                if (is_null($add_nbki['pdl_prolong_3m_limit'])){
                    $add_nbki['pdl_prolong_3m_limit'] = 0;
                }
                if (is_null($add_nbki['consum_current_limit_max'])){
                    $add_nbki['consum_current_limit_max'] = 0;
                }
                if (is_null($add_nbki['consum_good_limit'])){
                    $add_nbki['consum_good_limit'] = 0;
                }
                if (is_null($add_nbki['days_from_last_closed'])){
                    $add_nbki['days_from_last_closed'] = 0;
                }
                if (is_null($add_nbki['prev_3000_500_paid_count_wo_del'])){
                    $add_nbki['prev_3000_500_paid_count_wo_del'] = 0;
                }
                if (is_null($add_nbki['prev_paid_percent_sum'])){
                    $add_nbki['prev_paid_percent_sum'] = 0;
                }
                if (is_null($add_nbki['prev_max_delay'])){
                    $add_nbki['prev_max_delay'] = 0;
                }
                if (is_null($add_nbki['last_credit_delay'])){
                    $add_nbki['last_credit_delay'] = 0;
                }
                if (is_null($add_nbki['current_overdue_sum'])){
                    $add_nbki['current_overdue_sum'] = 0;
                }
                if (is_null($add_nbki['closed_to_total_credits_count_share'])){
                    $add_nbki['closed_to_total_credits_count_share'] = 0;
                }

                $scoreball_mfo_2_nbki = null;
                $nbkiScor = ScoringsORM::query()->where('order_id', '=', $nbkiScor->order_id)->where('type', '=', 'nbki')->orderBy('id', 'DESC')->first();
                if ($nbkiScor) {
                    $nbkiParams = unserialize($nbkiScor->body);
                    if (isset($nbkiParams['score'])){
                        $scoreball_mfo_2_nbki = $nbkiParams['score'];
                    }
                }

                $add_nbki['scoreball_mfo_2_nbki'] = $scoreball_mfo_2_nbki;
            
            }

            $order_scoreballs = $this->NbkiScoreballs->get($nbkiScor->order_id);

            if (empty($order_scoreballs)) {
                $order_scoreballs['ball'] = null;
            }
            else{
                $order_scoreballs->variables = json_decode($order_scoreballs->variables, true);
                $order_scoreballs->variables['ball'] = $order_scoreballs->ball;
                $order_scoreballs = $order_scoreballs->variables;
            }
            $add_nbki['scoreball'] = $order_scoreballs['ball'];

            var_dump($add_nbki['scoreball_mfo_2_nbki']);
            var_dump($add_nbki['scoreball']);


            $contracts = $this->contracts->get_contracts(array('user_id' => $nbkiScor->user_id, 'status' => 3));
                
            $contract_close_date = '';
            $count_contracts_3000_500_0 = 0;
            $all_percents_summ = 0;
            $all_peni_summ = 0;
            $period_peni_biggest = 0;
            $period_peni_last = 0;


            foreach ($contracts as $contract) {
                // Кол-во дней с даты погашения последнего займа 
                // во внутренней кредитной истории для данного клиента
                if (!is_null($contract->close_date)) {
                    if ($contract_close_date < $contract->close_date) 
                        $contract_close_date = $contract->close_date;
                }
                else{
                    if ($contract_close_date < $contract->close_date) 
                        $contract_close_date = $contract->return_date;
                }

                // Кол-во займов во внутренней кредитной истории для данного клиента, 
                // у которых сумма займа>=3000 руб И сумма погашенных процентов>=500 руб
                // И срок просрочки по займу=0
                if ($contract->amount >= 3000) {
                    $operations = $this->operations->get_operations(array('type' => 'PAY', 'contract_id' => $contract->id));

                    foreach ($operations as $operation) {
                        $contract_loan_percents_summ = 0;

                        $transaction = $this->transactions->get_transaction($operation->transaction_id);
                        $contract_loan_percents_summ += $transaction->loan_percents_summ;
                    }
                    if ($contract_loan_percents_summ > 500) {
                        $contract_count_peni = 0;

                        $operations = $this->operations->get_operations(array('type' => 'PENI', 'contract_id' => $contract->id));
                        foreach ($operations as $operation) {
                            $contract_count_peni++;
                        }
                        if ($contract_count_peni == 0) {
                            $count_contracts_3000_500_0++;
                        }
                    }
                }

                // Сумма погашенных процентов по всем займам 
                // во внутренней кредитной истории для данного клиента
                $operations = $this->operations->get_operations(array('type' => 'PAY', 'contract_id' => $contract->id));

                foreach ($operations as $operation) {
                    $transaction = $this->transactions->get_transaction($operation->transaction_id);
                    $all_percents_summ += $transaction->loan_percents_summ;
                }

                // Максимальный срок просрочки по всем займам 
                // во внутренней кредитной истории для данного клиента
                $operations = $this->operations->get_operations(array('type' => 'PENI', 'contract_id' => $contract->id));
                $prew_date_peni = '';
                $period_peni = 0;
                $period_peni_last = 0;

                foreach ($operations as $operation) {
                    $date1 = new DateTime(date('Y-m-d', strtotime($prew_date_peni)));
                    $date2 = new DateTime(date('Y-m-d', strtotime($operation->created)));

                    $prew_date_peni = $operation->created;
                    $diff = $date2->diff($date1)->days;

                    if ($diff == 1) {
                        $period_peni++;
                        $period_peni_last++;
                        if ($period_peni_biggest < $period_peni) 
                            $period_peni_biggest = $period_peni;
                    }
                    else{
                        $period_peni = 1;
                        $period_peni_last = 1;
                        if ($period_peni_biggest < $period_peni) 
                            $period_peni_biggest = $period_peni;
                    }

                    $transaction = $this->transactions->get_transaction($operation->transaction_id);
                    $all_peni_summ += $transaction->loan_peni_summ;
                }

            }
            
            if ($contract_close_date != '') {
                $date1 = new DateTime(date('Y-m-d', strtotime($contract_close_date)));
                $date2 = new DateTime(date('Y-m-d'));

                $diff = $date2->diff($date1);
                $delay_last_contract = $diff->days;
            }
            else{
                $delay_last_contract = 0;
            }

            $add_nbki['days_last_loan'] = $delay_last_contract;
            $add_nbki['loans_3000_500_0'] = $count_contracts_3000_500_0;
            $add_nbki['loans_percents_payd'] = $all_percents_summ;
            $add_nbki['max_delay_days'] = $period_peni_biggest;
            $add_nbki['last_delay_days'] = $period_peni_last;


            
            
            $order = $this->orders->get_order($nbkiScor->order_id);


            $c = $this->contracts->get_contract($order->contract_id);
            $type_pk = [];
            $add_nbki['type_pk'] = null;
            if (!is_null($c) && !is_null($c->id)) {
                $type_pk = $this->contracts->type_pk_contract($c);
                $add_nbki['type_pk'] = $type_pk;
            }
            else{
                $type_pk = $this->contracts->type_pk_order($order);
                $add_nbki['type_pk'] = $type_pk;
            }

            if ($order->utm_source == 'kpk' || $order->utm_source == 'part1') {
                $add_nbki['utm_source'] = $order->utm_source;
            }

            echo '<hr>';
            $nbki_extra_scoring_add = $this->NbkiExtraScorings->add($add_nbki);

        }
    }
}