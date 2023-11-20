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

            $reoprt_contracts_nbkis = ReoprtContractsNbkiORM::whereIn('order_id', [$contract->order_id])->get();
            if(count($reoprt_contracts_nbkis) >0){
                continue;
            }

            $nbkiScor = ScoringsORM::query()->where('order_id', '=', $contract->order_id)->where('type', '=', 'nbki')->first();
            
            if ($nbkiScor) {
                $nbkiParams = unserialize($nbkiScor->body);
                
                if (isset($nbkiParams['report_url'])) {
                    $data = file_get_contents(str_replace('log_report','log_xml', $nbkiParams['report_url']));
                    $xml = simplexml_load_string($data);
                    $temp = json_decode(json_encode($xml), true);
                    // if (isset($temp['preply']['report'])) {
                    //     $nbkiParams['json'] = $temp['preply']['report'];
                    // }

                    if (isset(json_decode(json_encode($xml), true)['product'])) {
                        if (isset(json_decode(json_encode($xml), true)['product']['preply']['report'])) {
                            var_dump('1');
                            $nbkiParams['json'] = json_decode(json_encode($xml), true)['product']['preply']['report'];
                        }
                    }
                    else{
                        if (isset(json_decode(json_encode($xml), true)['preply']['report'])) {
                            $nbkiParams['json'] = json_decode(json_encode($xml), true)['preply']['report'];
                        }
                    }
            
                }
                
                if (!empty($nbkiParams)) {
                    $reoprt_contracts_nbkis = ReoprtContractsNbkiORM::where('order_id', '=', $contract->order_id)->first();
                    // $reoprt_contracts_nbkis = $this->ReoprtContractsNbki->get_reoprt_nbkis(array('order_id' => $contract->order_id));
                    
                    if (!is_null($reoprt_contracts_nbkis)) {
                        continue;
                    }

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

                }
                else{
                    $nbki_arr = array(
                        'activeProduct' => 0,
                        'totalAmtOutstanding' => 0,
                        'doneProduct' => 0,
                        'totalAmtOutstandingDone' => 0,
                        'totalAverPaymtAmt' => 0,
                        'dolg' => 0,
                        'mkk' => 0,
                        'mkkSumm' => 0
                    );
                    
                }

                $json = json_encode($nbki_arr);

                $add = array(
                    'order_id' => $contract->order_id,
                    'variables' => $json,
                );

                // $reoprt_contracts_nbki_id = $this->ReoprtContractsNbki->add_reoprt_nbki($add);
                ReoprtContractsNbkiORM::insert($add);
                echo $contract->order_id.'<hr>';


                $i++;
                if ($i > 100) {
                    return;
                }

            }
        }

        // добавление type_pk в таблицу s_nbki_extra_scorings для power_bi
        $query = $this->db->placehold("
        SELECT max(`order_id`) as max
        FROM `s_nbki_extra_scorings` 
        WHERE `type_pk` 
        IS NOT null
        ");
        $this->db->query($query);
        $max_order_id = $this->db->result()->max;

        $query = $this->db->placehold("
        SELECT *
        FROM __orders
        WHERE id >= ?
        ORDER BY id
        ",$max_order_id);
        $this->db->query($query);
        $orders = $this->db->results();

        foreach ($orders as $order) {

            $c = $this->contracts->get_contract($order->contract_id);
            $type_pk = [];
            if (!is_null($c) && !is_null($c->id)) {
                $type_pk = $this->contracts->type_pk_contract($c);
                $add_nbki['type_pk'] = $type_pk;
                $nbki_extra_scoring = $this->NbkiExtraScorings->get($order->id);
                if (is_null($nbki_extra_scoring) || !isset($nbki_extra_scoring)) {
                    echo 'add';
                    $add_nbki['order_id'] = $order->id;
                    $add_nbki['score_id'] = 0;
                    $nbki_extra_scoring_add = $this->NbkiExtraScorings->add($add_nbki);
                }
                else{
                    echo 'upd';
                    $nbki_extra_scoring_update = $this->NbkiExtraScorings->update($order->id, $add_nbki);
                }
            }

            var_dump($order->id);
            echo '<hr>';
        }
    }


}

new ReoprtContractsNbki();