<?php

class TestController extends Controller
{
    public function fetch()
    {
        $order = $this->orders->get_order(257538);
        $fio = "$order->lastname $order->firstname $order->patronymic";

        $this->db->query("
            SELECT id 
            FROM __blacklist
            WHERE fio = ?
            ", $fio);

        $result = $this->db->result('id');

        var_dump($result);
        exit;
    }

    private function parse_cvs()
    {
        if ($this->request->post('run')) {
            $uploaded_name = $this->request->files("import_file", "tmp_name");

            $file = file($uploaded_name);

            foreach ($file as $user) {
                $user = str_getcsv($user, ';');

                $user[0] = mb_convert_encoding($user[0], 'UTF-8', 'Windows-1251');
                $fio = explode(' ', $user[0]);
                $fio = array_filter($fio);
                $fio = array_values($fio);
                $user[1] = str_replace(' ', '', $user[1]);
                $user[2] = str_replace(' ', '', $user[2]);

                $query = $this->db->placehold("
                SELECT id
                FROM s_users
                WHERE lastname = ?
                and firstname = ?
                and patronymic = ?
                ", (string)$fio[0], (string)$fio[1], (string)$fio[2]);

                $this->db->query($query);
                $user_id = $this->db->result('id');

                $query = $this->db->placehold("
                SELECT id
                FROM s_orders
                WHERE user_id = ?
                and `status` = 5
                ", $user_id);

                $this->db->query($query);
                $order_id = $this->db->result('id');

                $query = $this->db->placehold("
                UPDATE s_users
                SET loan_body_summ = ?, loan_percents_summ = ?
                WHERE order_id = ?
                ", (int)$user[1], (int)$user[2], $order_id);

                $this->db->query($query);
            }
        }

        return $this->design->fetch('test.tpl');
    }

    private function create_polis()
    {
        $contract_order = $this->orders->get_order(214232);

        $regaddress_full = empty($contract_order->Regindex) ? '' : $contract_order->Regindex . ', ';
        $regaddress_full .= trim($contract_order->Regregion . ' ' . $contract_order->Regregion_shorttype);
        $regaddress_full .= empty($contract_order->Regcity) ? '' : trim(', ' . $contract_order->Regcity . ' ' . $contract_order->Regcity_shorttype);
        $regaddress_full .= empty($contract_order->Regdistrict) ? '' : trim(', ' . $contract_order->Regdistrict . ' ' . $contract_order->Regdistrict_shorttype);
        $regaddress_full .= empty($contract_order->Reglocality) ? '' : trim(', ' . $contract_order->Reglocality . ' ' . $contract_order->Reglocality_shorttype);
        $regaddress_full .= empty($contract_order->Reghousing) ? '' : ', д.' . $contract_order->Reghousing;
        $regaddress_full .= empty($contract_order->Regbuilding) ? '' : ', стр.' . $contract_order->Regbuilding;
        $regaddress_full .= empty($contract_order->Regroom) ? '' : ', к.' . $contract_order->Regroom;

        $transaction = $this->transactions->get_transaction(60201);
        $contract = $this->contracts->get_contract(107989);
        $protection = 0;

        $document_params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'birth' => $contract_order->birth,
            'phone' => $contract_order->phone_mobile,
            'regaddress_full' => $regaddress_full,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $transaction->sms,
            'created' => date('Y-m-d H:i:s'),
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'number' => $contract->number,
        );

        $return_amount = round($contract->loan_body_summ + $contract->loan_body_summ * $contract->base_percent * 30 / 100, 2);

        $return_amount_percents = round($contract->loan_body_summ * $contract->base_percent * 30 / 100, 2);
        $document_params['return_amount'] = $return_amount;
        $document_params['return_amount_percents'] = $return_amount_percents;
        $document_params['amount'] = $contract->loan_body_summ;

        $insurance_id = $this->insurances->add_insurance(array(
            'number' => '',
            'amount' => $this->settings->prolongation_amount,
            'user_id' => $contract->user_id,
            'create_date' => date('Y-m-d 00:00:00', strtotime('2022-04-21 22:12:38')),
            'start_date' => date('Y-m-d 00:00:00', strtotime('2022-04-21 22:12:38')),
            'end_date' => date('Y-m-d 00:00:00', strtotime('2022-05-21 22:12:38')),
            'operation_id' => 332943,
            'protection' => $protection
        ));

        $this->transactions->update_transaction($transaction->id, array('insurance_id' => $insurance_id));

        $document_params['insurance'] = $this->insurances->get_insurance($insurance_id);

        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => 'POLIS_STRAHOVANIYA',
            'params' => $document_params
        ));
    }

    private function pars_csv_by_number()
    {
        if ($this->request->post('run')) {
            $uploaded_name = $this->request->files("import_file", "tmp_name");

            $file = file($uploaded_name);

            foreach ($file as $user) {
                $user = str_getcsv($user, ';');

                $number = $user[0];
                $loan_od = $user[1];
                $loan_prc = $user[2];
                $count_prolongation = $user[3];

                $query = $this->db->placehold("
                UPDATE s_contracts
                SET `loan_body_summ` = ?,
                `loan_percents_summ` = ?,
                `prolongation` = ?
                WHERE `number` = ?
                ", (int)$loan_od, (int)$loan_prc, (int)$count_prolongation, (string)$number);

                $this->db->query($query);
            }
        }

        return $this->design->fetch('test.tpl');
    }

    private function create_document($document_type, $contract)
    {

        /*
        $contract = $this->contracts->get_contract(103895);

        $this->create_document('IND_USLOVIYA_NL', $contract);
        $this->create_document('ANKETA_PEP', $contract);
        $this->create_document('SOLGLASHENIE_PEP', $contract);
        $this->create_document('SOGLASIE_VZAIMODEYSTVIE', $contract);
        $this->create_document('SOGLASIE_MEGAFON', $contract);
        $this->create_document('SOGLASIE_SCORING', $contract);
        $this->create_document('SOGLASIE_SPISANIE', $contract);

        */

        $return_date = date('Y-m-d H:i:s', strtotime('17.03.2021'));
        $created = date('Y-m-d H:i:s', strtotime('17.01.2021'));

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $this->orders->get_order((int)$contract->order_id);
        $contract_user = $this->users->get_user((int)$contract->user_id);

        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'gender' => $contract_order->gender,
            'number' => $contract->number,
            'contract_date' => $created,
            'created' => $created,
            'return_date' => $return_date,
            'return_date_day' => date('d', strtotime($return_date)),
            'return_date_month' => date('m', strtotime($return_date)),
            'return_date_year' => date('Y', strtotime($return_date)),
            'return_amount' => $return_amount,
            'return_amount_rouble' => $return_amount_rouble,
            'return_amount_kop' => $return_amount_kop,
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'period' => $contract->period,
            'return_amount_percents' => round($contract->amount * $contract->base_percent * $contract->period / 100, 2),
            'passport_serial' => $contract_order->passport_serial,
            'passport_date' => $contract_order->passport_date,
            'subdivision_code' => $contract_order->subdivision_code,
            'passport_issued' => $contract_order->passport_issued,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $contract->accept_code,
            'insurance_summ' => $this->insurances->get_insurance_cost($contract->amount),
            'passport_code' => $contract_user->subdivision_code,
            'regindex' => $contract_user->Regindex,
            'regregion' => $contract_user->Regregion,
            'regcity' => $contract_user->Regcity,
            'regstreet' => $contract_user->Regstreet,
            'reghousing' => $contract_user->Reghousing,
            'regbuilding' => $contract_user->Regbuilding,
            'regroom' => $contract_user->Regroom,
            'faktindex' => $contract_user->Faktindex,
            'faktregion' => $contract_user->Faktregion,
            'faktcity' => $contract_user->Faktcity,
            'faktstreet' => $contract_user->Faktstreet,
            'fakthousing' => $contract_user->Fakthousing,
            'faktbuilding' => $contract_user->Faktbuilding,
            'faktroom' => $contract_user->Faktroom,
            'profession' => $contract_user->profession,
            'workplace' => $contract_user->workplace,
            'workphone' => $contract_user->workphone,
            'chief_name' => $contract_user->chief_name,
            'chief_position' => $contract_user->chief_position,
            'chief_phone' => $contract_user->chief_phone,
            'income' => $contract_user->income,
            'expenses' => $contract_user->expenses,
            'create_date' => date('Y-m-d H:i:s'),
        );
        $regaddress_full = empty($contract_order->Regindex) ? '' : $contract_order->Regindex . ', ';
        $regaddress_full .= trim($contract_order->Regregion . ' ' . $contract_order->Regregion_shorttype);
        $regaddress_full .= empty($contract_order->Regcity) ? '' : trim(', ' . $contract_order->Regcity . ' ' . $contract_order->Regcity_shorttype);
        $regaddress_full .= empty($contract_order->Regdistrict) ? '' : trim(', ' . $contract_order->Regdistrict . ' ' . $contract_order->Regdistrict_shorttype);
        $regaddress_full .= empty($contract_order->Reglocality) ? '' : trim(', ' . $contract_order->Reglocality . ' ' . $contract_order->Reglocality_shorttype);
        $regaddress_full .= empty($contract_order->Reghousing) ? '' : ', д.' . $contract_order->Reghousing;
        $regaddress_full .= empty($contract_order->Regbuilding) ? '' : ', стр.' . $contract_order->Regbuilding;
        $regaddress_full .= empty($contract_order->Regroom) ? '' : ', к.' . $contract_order->Regroom;

        $params['regaddress_full'] = $regaddress_full;

        if (!empty($contract->insurance_id)) {
            $params['insurance'] = $this->insurances->get_insurance($contract->insurance_id);
        }


        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => $params,
        ));

    }

    private function add_receipts()
    {

        $this->db->query("
        SELECT *
        FROM s_cards
        ");
        $cards = $this->db->results();

        foreach ($cards as $card) {

            $receipt =
                [
                    'title' => 'Услуга "Привязка карты"',
                    'operation_id' => $card->operation,
                    'amount' => 1,
                ];

            $res = $this->Ekam->send_receipt($receipt);
            $res = json_decode($res);

            $data =
                [
                    'user_id' => $card->user_id,
                    'name' => $receipt['title'],
                    'receipt_url' => $res->online_cashier_url,
                    'response' => json_encode($res),
                    'created' => date('Y-m-d H:i:s', strtotime($card->created))
                ];

            $this->Receipts->add_receipt($data);

            usleep(300000);
        }
    }

    private function send_approve_postback_c2m($order_id)
    {
        $order = $this->orders->get_order($order_id);
        var_dump($this->Leadgens->send_approved_postback_click2money($order_id, $order));
        exit;
    }

    private function parse_xls()
    {
        if ($this->request->post('run')) {
            $uploaded_name = $this->request->files("import_file", "tmp_name");

            $file = file($uploaded_name);

            foreach ($file as $order){
                $order = str_getcsv($order, ';');
                $this->OrdersRecovery->add(['order_id' => $order[0]]);
            }
        }

        return $this->design->fetch('test.tpl');
    }

    private function create_polis_zakritie()
    {
        $contract = $this->contracts->get_contract(103996);
        $order    = $this->orders->get_order(205409);

        $document_params =
            [
                'lastname' => $order->lastname,
                'firstname' => $order->firstname,
                'patronymic' => $order->patronymic,
                'birth' => $order->birth,
                'phone_mobile' => $order->phone_mobile,
                'email' => $order->email,
                'now_date' => date('Y-m-d', strtotime('2021-11-24')),
                'amount' => $order->amount,
                'sum_correct' => 200
            ];


        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => 'POLIS_ZAKRITIE',
            'params' => $document_params
        ));
    }
}