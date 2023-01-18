<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
    {
        $this->onec();
        exit;
    }

    private function onec()
    {
        $contracts = ContractsORM::with('user.regAddress', 'user.factAddress')
            ->whereIn('status', [2,3,4,11])
            ->whereBetween('inssuance_date', [date('Y-m-d 00:00:00', strtotime('2022-11-01')), date('Y-m-d 23:59:59', strtotime('2023-01-16'))])
            ->get();

        Onec::request($contracts);
        exit;
    }

    private function restrDocs()
    {
        $contract = ContractsORM::find(2141);
        $user = UsersORM::find(20473);

        $paymentSchedules = PaymentsSchedulesORM::find(28);
        $paymentSchedules = json_decode($paymentSchedules->payment_schedules, true);

        $schedule = new stdClass();
        $schedule->order_id = 22984;
        $schedule->user_id = 20473;
        $schedule->contract_id = 2141;
        $schedule->init_od = $contract->loan_body_summ;
        $schedule->init_prc = $contract->loan_percents_summ;
        $schedule->init_peni = $contract->loan_peni_summ;
        $schedule->actual = 1;
        $schedule->payment_schedules = json_encode($paymentSchedules);

        $params = [
            'contract' => $contract,
            'user' => $user,
            'schedules' => $schedule
        ];

        var_dump(json_encode($params));
        exit;
    }
}