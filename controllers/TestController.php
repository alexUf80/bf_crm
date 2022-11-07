<?php
error_reporting(-1);
ini_set('display_errors', 'Off');

class TestController extends Controller
{
    public function fetch()
    {
        $transaction = $this->transactions->get_transaction(129228);

        $contract_order = $this->orders->get_order((int)26341);

        $user = $this->users->get_user($contract_order->user_id);

        $regaddress = $this->Addresses->get_address($user->regaddress_id);
        $regaddress_full = $regaddress->adressfull;

        $passport_series = substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4);
        $passport_number = substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6);
        $subdivision_code = $contract_order->subdivision_code;
        $passport_issued = $contract_order->passport_issued;
        $passport_date = $contract_order->passport_date;

        $document_params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'birth' => $contract_order->birth,
            'phone' => $contract_order->phone_mobile,
            'regaddress_full' => $regaddress_full,
            'passport_series' => $passport_series,
            'passport_number' => $passport_number,
            'passport_serial' => $contract_order->passport_serial,
            'subdivision_code' => $subdivision_code,
            'passport_issued' => $passport_issued,
            'passport_date' => $passport_date,
            'asp' => $transaction->sms,
            'created' => date('Y-m-d H:i:s', strtotime('2022-11-06 15:06:27')),
            'base_percent' => 0.5,
            'amount' => 8511.40,
            'number' => '1/0011226',
            'order_created' => $contract_order->date,

        );

        $return_amount = round(8511.40 * 0.5 * $this->settings->prolongation_period / 100, 2);
        $return_amount_percents = round(8511.40 * 0.5 * $this->settings->prolongation_period / 100, 2);

        $document_params['return_amount'] = $return_amount;
        $document_params['return_amount_percents'] = $return_amount_percents;

        $document_params['amount'] = 8511.40;

        // дополнительное соглашение
        $this->documents->create_document(array(
            'user_id' => 14914,
            'order_id' => 26341,
            'contract_id' => 2515,
            'type' => 'DOP_SOGLASHENIE',
            'created' => date('Y-m-d H:i:s', strtotime('2022-11-06 15:06:27')),
            'params' => json_encode($document_params)
        ));

        $document_params['insurance'] = $this->insurances->get_insurance(10009);

        $this->documents->create_document(array(
            'user_id' => 14914,
            'order_id' => 26341,
            'contract_id' => 2515,
            'type' => 'POLIS_PROLONGATION',
            'created' => date('Y-m-d H:i:s', strtotime('2022-11-06 15:06:27')),
            'params' => json_encode($document_params)
        ));
    }
}