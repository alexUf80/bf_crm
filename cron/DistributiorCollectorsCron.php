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
        $expiredContracts = ContractsORM::where('status', 4)
            ->where('return_date', '<', date('Y-m-d'))
            ->orderByRaw('SUM(loan_body_summ, loan_percents_summ, loan_charge_summ, loan_peni_summ) DESC')
            ->get();

        $periodsId = CollectorPeriodsORM::select('id')
            ->get()
            ->toArray();

        foreach ($periodsId as $id) {
            $collectorsMove = CollectorsMoveGroupORM::where('period_id', $id)->first();

            $collectorsMoveId = json_decode($collectorsMove->collectors_id, true);

            $collectorsId = ManagerORM::select('id')
                ->where('role', 'collector')
                ->where('collection_status_id', $id)
                ->get()
                ->toArray();

            if (count($collectorsMoveId) < count($collectorsId)) {
                $diff = array_diff($collectorsMoveId, $collectorsId);
                array_push($collectorsMoveId, $diff);
            }

            CollectorsMoveGroupORM::where('id', $collectorsMove->id)->update([json_encode($collectorsMoveId)]);
        }

        foreach ($expiredContracts as $contract) {

            $returnDate = new DateTime(date('Y-m-d', strtotime($contract->return_date)));
            $now = new DateTime(date('Y-m-d'));

            $dateDiff = date_diff($returnDate, $now)->days;

            $thisPeriod = CollectorPeriodsORM::where('period_from', '>=', $dateDiff)
                ->where('period_from', '<=', $dateDiff)
                ->first();

            if ($contract->collection_status == $thisPeriod->id && !empty($contract->collection_manager_id))
                continue;

            $collectorsMove = CollectorsMoveGroupORM::where('period_id', $thisPeriod->id)->first();
            $collectorsMoveId = json_decode($collectorsMove->collectors_id, true);

            $lastCollectorId = array_shift($collectorsMoveId);
            array_push($collectorsMoveId, $lastCollectorId);

            ContractsORM::where('id', $contract->id)->update(['collection_status' => $thisPeriod->id, 'collection_manager_id' => $lastCollectorId]);
            CollectorsMoveGroupORM::where('id', $collectorsMove->id)->update([json_encode($collectorsMoveId)]);
        }
    }
}

new DistributiorCollectorsCron();