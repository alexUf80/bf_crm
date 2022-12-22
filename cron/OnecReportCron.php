<?php
error_reporting(-1);
ini_set('display_errors', 'On');
chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class OnecReportCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        $startTime = date('Y-m-d 00:00:00', strtotime('-1 days'));
        $endTime   = date('Y-m-d 23:59:59', strtotime('-1 days'));

        $contracts = ContractsORM::with('user')->whereBetween('inssuance_date', [$startTime, $endTime])->get();

        if(empty($contracts))
            exit;
        else
            Onec::request($contracts);
    }
}

new OnecReportCron();