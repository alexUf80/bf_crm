<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('max_execution_time', 12000);


//chdir('/home/v/vse4etkoy2/nalic_eva-p_ru/public_html/');
chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

/**
 * IssuanceCron
 *
 * Скрипт производит начисление процентов, просрочек, пеней
 *
 * @author Ruslan Kopyl
 * @copyright 2021
 * @version $Id$
 * @access public
 */
class TaxingCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    /**
     * @return void
     * @throws Exception
     * Скриптом раздал косяковые проценты, не используется но может пригодится
     */
    private function pre_pay() {
        $contracts = ContractsORM::query()
            ->whereIn('status', [2,4])
            ->where('type', '=', 'base')
            ->orderBy('id', 'DESC')
            ->get();
        foreach ($contracts as $contract) {
            $lastOperation = OperationsORM::query()
                ->where('contract_id', '=', $contract->id)
                ->where('type', '=', 'PERCENTS')->orderBy('id', 'DESC')->first();
            $lastDate = new DateTime(date('d.m.Y', strtotime($lastOperation->created)));
            if ($lastDate) {
                if ($lastDate->format('d.m.Y') == '25.04.2023') {
                    print_r($contract->order_id.PHP_EOL);
                    continue;
                    $this->contracts->update_contract($contract->id, array(
                        'stop_profit' => 0
                    ));
                    $contract = ContractsORM::find($contract->id);
                    $amount = $contract->loan_body_summ;
                    $taxing_limit = $amount * 2.5;
                    $current_summ = $contract->loan_body_summ + $contract->loan_percents_summ + $contract->loan_charge_summ + $contract->loan_peni_summ;
                    if ($current_summ >= $taxing_limit) {
                        $this->contracts->update_contract($contract->id, array(
                            'stop_profit' => 1
                        ));
                        echo "\r\nCS $current_summ > TL $taxing_limit = stop profit\r\n";
                        break;
                    }

                    echo "\r\n Calc percents\r\n";
                    //Начисление процентов
                    $percents_summ = round($contract->loan_body_summ / 100 * $contract->base_percent, 2);

                    if($current_summ + $percents_summ > $taxing_limit)
                    {
                        $this->contracts->update_contract($contract->id, array(
                            'stop_profit' => 1
                        ));
                        echo "\r\nCS $current_summ + PS $percents_summ > TL $taxing_limit = stop profit\r\n";
                        break;
                    }
                    echo "\r\n Save percents\r\n ".$contract->loan_percents_summ + $percents_summ;
                    $this->contracts->update_contract($contract->id, array(
                        'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                    ));
                    $this->operations->add_operation(array(
                        'contract_id' => $contract->id,
                        'user_id' => $contract->user_id,
                        'order_id' => $contract->order_id,
                        'type' => 'PERCENTS',
                        'amount' => $percents_summ,
                        'created' => '2023-04-26 00:00:01',
                        'loan_body_summ' => $contract->loan_body_summ,
                        'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                        'loan_charge_summ' => $contract->loan_charge_summ,
                        'loan_peni_summ' => $contract->loan_peni_summ,
                    ));

                    //Начисление пени, если просрочен займ
                    if ($contract->status == 4) {
                        echo "\r\n Contract expored calc peni\r\n";
                        $peni_summ = round((0.05 / 100) * $contract->loan_body_summ, 2);
                        if($current_summ + $peni_summ + $percents_summ > $taxing_limit)
                        {
                            $this->contracts->update_contract($contract->id, array(
                                'stop_profit' => 1
                            ));
                            echo "\r\nCS $current_summ + PS $peni_summ + PS $percents_summ > TL $taxing_limit = stop profit\r\n";
                            break;
                        }
                        echo "\r\n Save peni\r\n ".$contract->loan_peni_summ + $peni_summ;
                        $this->contracts->update_contract($contract->id, array(
                            'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ
                        ));
                        $this->operations->add_operation(array(
                            'contract_id' => $contract->id,
                            'user_id' => $contract->user_id,
                            'order_id' => $contract->order_id,
                            'type' => 'PENI',
                            'amount' => $peni_summ,
                            'created' => '2023-04-26 00:00:01',
                            'loan_body_summ' => $contract->loan_body_summ,
                            'loan_percents_summ' => $contract->loan_percents_summ,
                            'loan_charge_summ' => $contract->loan_charge_summ,
                            'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ,
                        ));
                    }
                    echo "\r\n End script\r\n";
                }
            }
        }
    }

    /**
     * @return void
     * @throws Exception
     * Раскидал проценты, метод не используется
     */
    private function tax_contracts() {
        $contracts = ContractsORM::query()
            ->whereIn('status', [2,4])
            ->where('type', '=', 'base')
            ->orderBy('id', 'DESC')
            ->get();
        foreach ($contracts as $contract) {
            while (true) {
                $this->contracts->update_contract($contract->id, array(
                    'stop_profit' => 0
                ));
                $contract = ContractsORM::find($contract->id);
                $amount = $contract->loan_body_summ;
                $taxing_limit = $amount * 2.5;
                $current_summ = $contract->loan_body_summ + $contract->loan_percents_summ + $contract->loan_charge_summ + $contract->loan_peni_summ;
                if ($current_summ >= $taxing_limit) {
                    $this->contracts->update_contract($contract->id, array(
                        'stop_profit' => 1
                    ));
                    echo "\r\nCS $current_summ > TL $taxing_limit = stop profit\r\n";
                    break;
                }
                echo "\r\nContinue\r\n";
                $lastOperation = OperationsORM::query()
                    ->where('contract_id', '=', $contract->id)
                    ->where('type', '=', 'PERCENTS')->orderBy('id', 'DESC')->first();
                if ($lastOperation) {
                    $lastDate = new DateTime(date('d.m.Y', strtotime($lastOperation->created)));
                } else {
                    $lastDate = new DateTime(date('d.m.Y', time() - 86400));
                }
                $nextDay = $lastDate->add(new DateInterval('P1D'));
                if ($nextDay->format('d.m.Y') == date('d.m.Y')) {
                    break;
                }
                echo "\r\n Calc percents\r\n";
                //Начисление процентов
                $percents_summ = round($contract->loan_body_summ / 100 * $contract->base_percent, 2);

                if($current_summ + $percents_summ > $taxing_limit)
                {
                    $this->contracts->update_contract($contract->id, array(
                        'stop_profit' => 1
                    ));
                    echo "\r\nCS $current_summ + PS $percents_summ > TL $taxing_limit = stop profit\r\n";
                    break;
                }
                echo "\r\n Save percents\r\n ".$contract->loan_percents_summ + $percents_summ;
                $this->contracts->update_contract($contract->id, array(
                    'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                ));
                $this->operations->add_operation(array(
                    'contract_id' => $contract->id,
                    'user_id' => $contract->user_id,
                    'order_id' => $contract->order_id,
                    'type' => 'PERCENTS',
                    'amount' => $percents_summ,
                    'created' => $nextDay->format('Y-m-d 00:00:01'),
                    'loan_body_summ' => $contract->loan_body_summ,
                    'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                    'loan_charge_summ' => $contract->loan_charge_summ,
                    'loan_peni_summ' => $contract->loan_peni_summ,
                ));

                //Начисление пени, если просрочен займ
                if ($contract->status == 4) {
                    echo "\r\n Contract expored calc peni\r\n";
                    $peni_summ = round((0.05 / 100) * $contract->loan_body_summ, 2);
                    if($current_summ + $peni_summ + $percents_summ > $taxing_limit)
                    {
                        $this->contracts->update_contract($contract->id, array(
                            'stop_profit' => 1
                        ));
                        echo "\r\nCS $current_summ + PS $peni_summ + PS $percents_summ > TL $taxing_limit = stop profit\r\n";
                        break;
                    }
                    echo "\r\n Save peni\r\n ".$contract->loan_peni_summ + $peni_summ;
                    $this->contracts->update_contract($contract->id, array(
                        'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ
                    ));
                    $this->operations->add_operation(array(
                        'contract_id' => $contract->id,
                        'user_id' => $contract->user_id,
                        'order_id' => $contract->order_id,
                        'type' => 'PENI',
                        'amount' => $peni_summ,
                        'created' => $nextDay->format('Y-m-d 00:00:01'),
                        'loan_body_summ' => $contract->loan_body_summ,
                        'loan_percents_summ' => $contract->loan_percents_summ,
                        'loan_charge_summ' => $contract->loan_charge_summ,
                        'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ,
                    ));
                }
                echo "\r\n End script\r\n";
            }
        }
    }

    /**
     * Ищем где слетел процент и возвращаем
     * @return void
     */
    private function searchBugs() {
        $contracts = ContractsORM::query()->whereIn('status',[4,2])->get();
        foreach ($contracts as $contract) {
            $peni = OperationsORM::query()
                ->where('contract_id', '=', $contract->id)
                ->where('type', '=', 'PENI')->first();
            if ($peni) {
                $operations = OperationsORM::query()
                    ->where('contract_id', '=', $contract->id)
                    ->where('type', '=', 'PERCENTS')
                    ->where('created', '>', $peni->created)
                    ->get();
                foreach ($operations as $operation) {
                    $openi = OperationsORM::query()
                        ->where('contract_id', '=', $contract->id)
                        ->where('type', '=', 'PENI')
                        ->where('created', '=', $operation->created)
                        ->first();
                    if (!$openi) {
                        $peni_summ = round((0.05 / 100) * $contract->loan_body_summ, 2);
                        $this->contracts->update_contract($contract->id, array(
                            'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ
                        ));
                        $this->operations->add_operation(array(
                            'contract_id' => $contract->id,
                            'user_id' => $contract->user_id,
                            'order_id' => $contract->order_id,
                            'type' => 'PENI',
                            'amount' => $peni_summ,
                            'created' => $operation->created,
                            'loan_body_summ' => $contract->loan_body_summ,
                            'loan_percents_summ' => $contract->loan_percents_summ,
                            'loan_charge_summ' => $contract->loan_charge_summ,
                            'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ,
                        ));
                    }
                }
            }
        }
        /*$query = OperationsORM::query();
        $query->where('amount', '>', '1');
        $query->where('amount', '<', '10');
        $query->where('type', '=', 'PERCENTS');
        $query->groupBy('contract_id');
        $operations = $query->get();
        foreach ($operations as $operation) {
            $firstOperation = OperationsORM::query()
                ->where('contract_id', '=', $operation->contract_id)
                ->where('type', '=', 'PERCENTS')
                ->orderBy('id', 'DESC')->first();
            if ($firstOperation->amount != $operation->amount) {
                $newAmount = $firstOperation->amount - $operation->amount;
                $operation->update([
                    'amount' => $firstOperation->amount,
                ]);
                $contract = ContractsORM::query()->where('id', '=', $operation->contract_id)->first();
                $contract->update([
                    'loan_percents_summ' => $contract->loan_percents_summ + $newAmount,
                ]);
            }
        }*/
    }

    private function run()
    {

        /*$operations = OperationsORM::query()
            ->whereIn('type', ['PERCENTS', 'PENI'])
            ->where('created', '>', '2023-06-06')
            ->get();
        foreach ($operations as $operation) {
            $contract = ContractsORM::query()->where('id', '=', $operation->contract_id)->first();
            if ($contract) {
                if ($operation->type == 'PERCENTS') {
                    $contract->update([
                        'loan_percents_summ' => $contract->loan_percents_summ - $operation->amount,
                    ]);
                }
                if ($operation->type == 'PENI') {
                    $contract->update([
                        'loan_peni_summ' => $contract->loan_peni_summ + $operation->amount
                    ]);
                }
            }
            $operation->delete();
        }
        return;*/
        //Перевод в просрочку всех у кого подошел срок
        $this->contracts->check_expiration_contracts();

        //Начисления
        $contracts = ContractsORM::query()
            ->whereIn('status', [2,4])
            ->where('type', '=', 'base')
            ->where('stop_profit', '=' , 0)
            ->where('is_restructed', '=', 0)
            ->get();
        //$current_date = '2023.05.03 00:00:01';
        $current_date = date('Y.m.d H:i:s');
        foreach ($contracts as $contract) {
            $p2p = OperationsORM::query()->where('contract_id', '=', $contract->id)->where('type', '=', 'P2P')->first();
            if ($p2p) {
                $date = date('Y.m.d H:i:s', strtotime($p2p->created));
                if ($date >= $current_date) {
                    print_r('test');
                    continue;
                }
            } else {
                continue;
            }
            $amount = $contract->loan_body_summ;
            $taxing_limit = $amount * 2.5;

            $this->db->query("
                select sum(amount) as sum_taxing
                from s_operations
                where contract_id = ?
                and `type` in ('PERCENTS', 'PENI')
                ", $contract->id);
            $sum_taxing = $this->db->result()->sum_taxing;

            $current_summ = $contract->loan_body_summ + $sum_taxing;
            if ($current_summ >= $taxing_limit) {
                $this->contracts->update_contract($contract->id, array(
                    'stop_profit' => 1
                ));
                echo "\r\nCS $current_summ > TL $taxing_limit = stop profit\r\n";
                continue;
            }
            echo "\r\n Calc percents\r\n";
            //Начисление процентов
            $percents_summ = round($contract->loan_body_summ / 100 * $contract->base_percent, 2);

            if($current_summ + $percents_summ > $taxing_limit)
            {
                $this->contracts->update_contract($contract->id, array(
                    'stop_profit' => 1
                ));
                echo "\r\nCS $current_summ + PS $percents_summ > TL $taxing_limit = stop profit\r\n";
                continue;
            }
            echo "\r\n Save percents\r\n ".$contract->loan_percents_summ + $percents_summ;
            $this->contracts->update_contract($contract->id, array(
                'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
            ));
            $this->operations->add_operation(array(
                'contract_id' => $contract->id,
                'user_id' => $contract->user_id,
                'order_id' => $contract->order_id,
                'type' => 'PERCENTS',
                'amount' => $percents_summ,
                'created' => $current_date,
                'loan_body_summ' => $contract->loan_body_summ,
                'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ,
                'loan_charge_summ' => $contract->loan_charge_summ,
                'loan_peni_summ' => $contract->loan_peni_summ,
            ));

            //Начисление пени, если просрочен займ
            if ($contract->status == 4) {
                echo "\r\n Contract expored calc peni\r\n";
                $peni_summ = round((0.05 / 100) * $contract->loan_body_summ, 2);
                if($current_summ + $peni_summ + $percents_summ > $taxing_limit)
                {
                    $this->contracts->update_contract($contract->id, array(
                        'stop_profit' => 1
                    ));
                    echo "\r\nCS $current_summ + PS $peni_summ + PS $percents_summ > TL $taxing_limit = stop profit\r\n";
                    continue;
                }
                echo "\r\n Save peni\r\n ".$contract->loan_peni_summ + $peni_summ;
                $this->contracts->update_contract($contract->id, array(
                    'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ
                ));
                $this->operations->add_operation(array(
                    'contract_id' => $contract->id,
                    'user_id' => $contract->user_id,
                    'order_id' => $contract->order_id,
                    'type' => 'PENI',
                    'amount' => $peni_summ,
                    'created' => $current_date,
                    'loan_body_summ' => $contract->loan_body_summ,
                    'loan_percents_summ' => $contract->loan_percents_summ,
                    'loan_charge_summ' => $contract->loan_charge_summ,
                    'loan_peni_summ' => $contract->loan_peni_summ + $peni_summ,
                ));
            }
            echo "\r\n End script\r\n";
        }
    }


}

$cron = new TaxingCron();
