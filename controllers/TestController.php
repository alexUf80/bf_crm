<?php
error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
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
        exit;
    }
}