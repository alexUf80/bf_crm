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

        if ($contracts = $this->contracts->get_contracts(array('status' => [2, 4], 'type' => 'base', 'stop_profit' => 0))) {

            foreach ($contracts as $contract) {
                $issuance_date = new DateTime(date('Y-m-d', strtotime($contract->inssuance_date)));
                $now_date = new DateTime(date('Y-m-d'));

                if (date_diff($issuance_date, $now_date)->days > 150) {
                    $this->contracts->update_contract($contract->id, array(
                        'stop_profit' => 1
                    ));
                } else {
                    $percents_summ = round($contract->loan_body_summ / 100 * $contract->base_percent, 2);

                    $this->contracts->update_contract($contract->id, array(
                        'loan_percents_summ' => $contract->loan_percents_summ + $percents_summ
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
                }
            }
        }
    }


}

$cron = new TaxingCron();
