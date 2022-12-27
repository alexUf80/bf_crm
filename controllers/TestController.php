<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');

class TestController extends Controller
{
    public function fetch()
    {
        $contracts = ContractsORM::with('user.regAddress', 'user.factAddress')
            ->whereIn('status', [3])
            ->where('inssuance_date', '>=', date('Y-m-d 00:00:00', strtotime('2022-11-01')))
            ->get();

        Onec::request($contracts);
        exit;
    }
}