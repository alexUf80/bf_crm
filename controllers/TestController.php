<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
    {
        $contract = ContractsORM::find(2663);
        $user = UsersORM::find(15195);

        $paymentSchedules = PaymentsSchedulesORM::find(24);

        $schedule = new PaymentsSchedulesORM();
        $schedule->order_id = $contract->order_id;
        $schedule->user_id = 15195;
        $schedule->contract_id = 2663;
        $schedule->init_od = $contract->loan_body_summ;
        $schedule->init_prc = $contract->loan_percents_summ;
        $schedule->init_peni = $contract->loan_peni_summ;
        $schedule->actual = 1;
        $schedule->payment_schedules = $paymentSchedules->payment_schedules;

        $params = [
            'contract' => $contract,
            'user' => $user,
            'schedules' => $schedule
        ];

        echo json_encode($params);

        exit;
    }
}