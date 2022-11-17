<?php
error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
    {

    }

    private function individ_usloviya()
    {
        $contract = ContractsORM::where('order_id', 30699)->first();

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
            'contract_date' => date('Y-m-d H:i:s', strtotime($contract->inssuance_date)),
            'created' => date('Y-m-d H:i:s', strtotime($contract->inssuance_date)),
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

        DocumentsORM::where('id', 165)->update(['params' => json_encode($params)]);
        exit;
    }

    private function insurance()
    {
        $contracts = ContractsORM::where('service_insurance', 1)->whereIn('status', [2,4])->get();

        foreach ($contracts as $contract)
        {
            $insuranceCost = InsurancesORM::get_insurance_cost($contract->amount);

            $operationId = OperationsORM::select('id')->where(['type' => 'INSURANCE', 'order_id' => $contract->order_id])->first();

            $contract->insurance = new InsurancesORM();
            $contract->insurance->amount = $insuranceCost;
            $contract->insurance->user_id = $contract->user_id;
            $contract->insurance->order_id = $contract->order_id;
            $contract->insurance->create_date = date('Y-m-d 00:00:00' ,strtotime($contract->inssuance_date));
            $contract->insurance->start_date = date('Y-m-d 00:00:00' ,strtotime($contract->inssuance_date.'+1 days'));
            $contract->insurance->end_date = date('Y-m-d 23:59:59' ,strtotime($contract->inssuance_date.'+31 days'));
            $contract->insurance->operation_id = $operationId->id;
            $contract->insurance->save();

            $contract->insurance->number = InsurancesORM::create_number($contract->insurance->id);

            InsurancesORM::where('id', $contract->insurance->id)->update(['number' => $contract->insurance->number]);
            DocumentsORM::whereIn('type' , ['POLIS', 'KID'])->where('contract_id', $contract->id)->update(['params' => json_encode($contract)]);
        }
    }
}