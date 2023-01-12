<?php
error_reporting(-1);
ini_set('display_errors', 'On');
chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class DistributiorCollectorsCron extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->run();
    }

    private function run()
    {
        $expiredContracts = ContractsORM::where('status', 4)->get();

        foreach ($expiredContracts as $contract) {
            $collectorsPeriod = $contract->collection_status + 1;

            ContractsORM::where('id', $contract->id)->update(['collection_status' => $collectorsPeriod]);
        }
    }
}

new DistributiorCollectorsCron();