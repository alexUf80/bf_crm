<?php
error_reporting(-1);
ini_set('display_errors', 'On');
chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class ReoprtContractsNbki extends Core
{
    public function __construct()
    {
        parent::__construct();

        $this->run();
    }

    public static function date_format($date_string, $format = 'd.m.Y') {
        try {
            return date($format, strtotime($date_string));
        } catch (Exception $exception) {
            return $date_string;
        }
    }

    private function run()
    {
        $i = 0;
        $contracts = ContractsORM::whereIn('status', [2, 3, 4, 7])->get();
        foreach ($contracts as $contract) {
            $nbkiScor = ScoringsORM::query()->where('order_id', '=', $contract->order_id)->where('type', '=', 'nbki')->first();
            
            if ($nbkiScor) {
                $nbkiParams = unserialize($nbkiScor->body);
                
                if (isset($nbkiParams['report_url'])) {
                    $data = file_get_contents(str_replace('log_report','log_xml', $nbkiParams['report_url']));
                    $xml = simplexml_load_string($data);
                    $temp = json_decode(json_encode($xml), true);
                    if (isset($temp['preply']['report'])) {
                        $nbkiParams['json'] = $temp['preply']['report'];
                    }
                }
                
                
                if (!empty($nbkiParams)) {
                    $reoprt_contracts_nbkis = ReoprtContractsNbkiORM::where('order_id', '=', $contract->order_id)->first();
                    // $reoprt_contracts_nbkis = $this->ReoprtContractsNbki->get_reoprt_nbkis(array('order_id' => $contract->order_id));
                    
                    if (!is_null($reoprt_contracts_nbkis)) {
                        continue;
                    }

                    $i++;

                    $activeProduct = 0;
                    $doneProduct = 0;
                    $summ = 0;
                    $totalAmtOutstanding = 0;
                    $totalAmtOutstandingDone = 0;
                    $totalAverPaymtAmt = 0;
                    $dolg = 0;
                    $mkk = 0;
                    $mkkSumm = 0;
                    if (isset($nbkiParams['json']['AccountReplyRUTDF'])) {
                        foreach ($nbkiParams['json']['AccountReplyRUTDF'] as $reply) {
    

                            if (isset($reply['pastdueArrear'])) {
                                $keys = array_keys($reply['pastdueArrear']);
                                // Если массив ассциативный
                                if ($keys !== array_keys($keys)) {
                                    $pastdueArrear = $reply['pastdueArrear'];
                                    $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
                                    if ($past !== '0.00') {
                                        $summ = floatval($past);
                                    }
                                } else {
                                    foreach ($reply['pastdueArrear'] as $pastdueArrear) {
                                        $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
                                        if ($past !== '0.00') {
                                            $summ = floatval($past);
                                        }
                                    }
                                }
                            }
    
    
                            if (isset($reply['reportingDt'])) {
                                $curentDateDiff = date_diff(new DateTime(), new DateTime($reply['reportingDt']));
                            }
                            $status = [
                                'name' => 'Активный',
                                'color' => 'black',
                            ];
                            if (
                                (isset($reply['holdCode']) && $reply['holdCode'] == 1) ||
                                $curentDateDiff->days > 33 ||
                                (isset($reply['loanIndicator']) && $reply['loanIndicator'] != 1)
                            ) {
                                $status = [
                                    'name' => 'Не определен',
                                    'color' => 'silver',
                                ];
                            }
                            if($curentDateDiff->days > 180) {
                                $status = [
                                    'name' => 'Архив',
                                    'color' => 'silver',
                                ];
                            }
    
                            if($summ > 0) {
                                $status = [
                                    'name' => 'Просрочен',
                                    'color' => 'red',
                                ];
                            }
    
                            if(isset($reply['loanIndicator']) && in_array($reply['loanIndicator'], [3,11])) {
                                $status = [
                                    'name' => 'Прощение долга',
                                    'color' => 'red',
                                ];
                            }
    
                            if(isset($reply['submitHold']['holdCode']) && $reply['submitHold']['holdCode'] == 3) {
                                $status = [
                                    'name' => 'Списан',
                                    'color' => 'red',
                                ];
                            }
    
                            if(
                                (isset($reply['loanIndicator']) && ($reply['loanIndicator'] == 2 || $reply['loanIndicator'] == 1)) ||
                                (isset($reply['sbLoanIndicator']) && $reply['sbLoanIndicator'] == 1) ||
                                (isset($reply['collatRepay']) && $reply['collatRepay'] == 1)
                            ) {
                                $status = [
                                    'name' => 'Счет закрыт',
                                    'color' => 'green',
                                ];
                            }
                            if (isset($reply['businessCategory']) && $reply['businessCategory'] == 'MKK') {
                                $openDt = false;
                                if (isset($reply['trade'])) {
                                    $keys = array_keys($reply['trade']);
                                    // Если массив ассциативный
                                    if ($keys !== array_keys($keys)) {
                                        if (isset($reply['trade']['openedDt'])) 
                                            $openDt = self::date_format($reply['trade']['openedDt']);
                                    } else {
                                        if (isset($reply['trade'][0]['openedDt'])) 
                                            $openDt = self::date_format($reply['trade'][0]['openedDt']);
                                    }
                                }
                                $time = time() - (86400 * 92);
                                $dateMonth = date('d.m.Y', $time);
                                if ($openDt > $dateMonth) {
                                    $mkk++;
                                }
                                if ($status['name'] == 'Активный' || $status['name'] == 'Просрочен' || $status['name'] == 'Не определен') {
                                    $mkkSumm++;
                                }
                            }
                            if ($status['name'] == 'Активный' || $status['name'] == 'Просрочен' || $status['name'] == 'Не определен') {
                                $activeProduct++;
    
                                if (isset($reply['paymtCondition'])) {
                                    $keys = array_keys($reply['paymtCondition']);
                                    if ($keys !== array_keys($reply['paymtCondition'])) {
                                        if (isset($reply['paymtCondition']['principalTermsAmt']) && isset($reply['paymtCondition']['interestTermsAmt'])) {
                                            $totalAverPaymtAmt += floatval($reply['paymtCondition']['principalTermsAmt']) + floatval($reply['paymtCondition']['interestTermsAmt']);;
                                        }
                                    } else {
                                        $condition = end($reply['paymtCondition']);
                                        if (isset($condition['principalTermsAmt']) && isset($condition['interestTermsAmt'])) {
                                            $totalAverPaymtAmt += floatval($condition['principalTermsAmt']) + floatval($condition['interestTermsAmt']);
                                        }
                                    }
                                } else {
                                    if (isset($reply['monthAverPaymt'])) {
                                        $totalAverPaymtAmt += floatval($reply['monthAverPaymt']['averPaymtAmt'] ?? 0);
                                    }
                                }
    
                                if (isset($reply['accountAmt'])) {
                                    $keys = array_keys($reply['accountAmt']);
                                    // Если массив ассциативный
                                    if ($keys !== array_keys($keys)) {
                                        if ($status['name'] != 'Активный') {
                                            $dolg += floatval($reply['accountAmt']['creditLimit'] ?? 0);
                                        }
                                        $totalAmtOutstanding += floatval($reply['accountAmt']['creditLimit'] ?? 0);
                                    } else {
                                        foreach ($reply['accountAmt'] as $arrear) {
                                            if ($status['name'] != 'Активный') {
                                                $dolg += floatval($arrear['creditLimit'] ?? 0);
                                            }
                                            $totalAmtOutstanding += floatval($arrear['creditLimit'] ?? 0);
                                        }
                                    }
                                }
                            } else {
                                $doneProduct++;
                                if (isset($reply['accountAmt'])) {
                                    $keys = array_keys($reply['accountAmt']);
                                    // Если массив ассциативный
                                    if ($keys !== array_keys($keys)) {
                                        $totalAmtOutstandingDone += floatval($reply['accountAmt']['creditLimit'] ?? 0);
                                    } else {
                                        foreach ($reply['accountAmt'] as $arrear) {
                                            $totalAmtOutstandingDone += floatval($arrear['creditLimit'] ?? 0);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $nbki_arr = array(
                        'activeProduct' => $activeProduct,
                        'totalAmtOutstanding' => $totalAmtOutstanding,
                        'doneProduct' => $doneProduct,
                        'totalAmtOutstandingDone' => $totalAmtOutstandingDone,
                        'totalAverPaymtAmt' => $totalAverPaymtAmt,
                        'dolg' => $dolg,
                        'mkk' => $mkk,
                        'mkkSumm' => $mkkSumm
                    );
                    $json = json_encode($nbki_arr);

                    $add = array(
                        'order_id' => $contract->order_id,
                        'variables' => $json,
                    );

                    // $reoprt_contracts_nbki_id = $this->ReoprtContractsNbki->add_reoprt_nbki($add);
                    ReoprtContractsNbkiORM::insert($add);

                    echo $contract->id.'<hr>';
                    if ($i > 10) {
                        return;
                    }

                }
            }
        }
    }


}

new ReoprtContractsNbki();