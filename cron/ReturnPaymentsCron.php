<?php
error_reporting(-1);
ini_set('display_errors', 'On');


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class ReturnPaymentsCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $contract = $this->contracts->get_contract(4534);
        $this->create_document('IND_USLOVIYA_NL', $contract);
        return;
        $this->run();
    }

    public function create_document($document_type, $contract)
    {
        $ob_date = new DateTime();
        $ob_date->add(DateInterval::createFromDateString($contract->period . ' days'));
        $return_date = $ob_date->format('Y-m-d H:i:s');

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $this->orders->get_order((int)$contract->order_id);

        $insurance_cost = $this->insurances->get_insurance_cost($contract_order);

        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'number' => $contract->number,
            'contract_date' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
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
            'insurance_summ' => $insurance_cost,
        );

        $params['user'] = $this->users->get_user($contract->user_id);
        $params['order'] = $this->orders->get_order($contract->order_id);
        $params['contract'] = $contract;


        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => json_encode($params),
        ));

    }


    private function run()
    {
        $date = '2023.05.10 19:50:24';
        $ids = [
            4312 => 1318.10,
            4101 => 2083.80,
            4093 => 2201.60,
            3971 => 2324.20,
            4365 => 1023.60,
            4132 => 10446.40,
            4439 => 6302.30,
            4299 => 7068.00,
            4160 => 10257.00,
            4339 => 9152.40,
            4399 => 8521.20,
            4084 => 7185.80
        ];
        foreach ($ids as $id => $sum) {
            $operations = OperationsORM::query()->where('contract_id', '=', $id)->where('type', '=', 'PENI')->get();
            foreach ($operations as $operation) {
                $contract = ContractsORM::query()->where('id', '=', $id)->first();
                if ($contract) {
                    $contract->update(
                        [
                            'loan_peni_summ' => $contract->loan_peni_summ - $operation->amount,
                        ]
                    );
                    $operation->delete();
                }
            }
        }
    }

}

$cron = new ReturnPaymentsCron();
