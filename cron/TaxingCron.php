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

    private function run()
    {
        //Перевод в просрочку всех у кого подошел срок
        $this->contracts->check_expiration_contracts();

        //Начисления
        $contracts = ContractsORM::query()
            ->whereIn('status', [2,4])
            ->where('type', '=', 'base')
            ->where('stop_profit', '=', 0)
            ->where('is_restructed', '=', 0)
            ->get();
        foreach ($contracts as $contract) {
            $amount = $contract->loan_body_summ;
            $taxing_limit = $amount * 2.5;
            $current_summ = $contract->loan_body_summ + $contract->loan_percents_summ + $contract->loan_charge_summ + $contract->loan_peni_summ;
            if ($current_summ >= $taxing_limit) {
                $this->contracts->update_contract($contract->id, array(
                    'stop_profit' => 1
                ));
                echo "\r\nCS $current_summ > TL $taxing_limit = stop profit\r\n";
                exit();
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
                exit();
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
                'created' => date('Y-m-d H:i:s'),
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
                    exit();
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
                    'created' => date('Y-m-d H:i:s'),
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
