<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');
error_reporting(0);
ini_set('display_errors', 'Off');

class TestController extends Controller
{
    public function fetch()
    {

        // $i = 0;
        // $contracts = ContractsORM::whereIn('status', [2, 3, 4, 7])->get();
        // foreach ($contracts as $contract) {
        //     $nbkiScor = ScoringsORM::query()->where('order_id', '=', $contract->order_id)->where('type', '=', 'nbki')->first();
            
        //     if ($nbkiScor) {
        //         $nbkiParams = unserialize($nbkiScor->body);

        //         if (isset($nbkiParams['report_url'])) {
        //             $data = file_get_contents(str_replace('log_report','log_xml', $nbkiParams['report_url']));
        //             $xml = simplexml_load_string($data);
        //             $nbkiParams['json'] = json_decode(json_encode($xml), true)['preply']['report'];
        //         }


        //         if (!empty($nbkiParams)) {
        //             $reoprt_contracts_nbkis = $this->ReoprtContractsNbki->get_reoprt_nbkis(array('order_id' => $contract->order_id));

        //             if (count($reoprt_contracts_nbkis) != 0) {
        //                 continue;
        //             }

        //             $i++;

        //             $activeProduct = 0;
        //             $doneProduct = 0;
        //             $summ = 0;
        //             $totalAmtOutstanding = 0;
        //             $totalAmtOutstandingDone = 0;
        //             $totalAverPaymtAmt = 0;
        //             $dolg = 0;
        //             $mkk = 0;
        //             $mkkSumm = 0;
        //             foreach ($nbkiParams['json']['AccountReplyRUTDF'] as $reply) {

        //                 $keys = array_keys($reply['pastdueArrear']);
        //                 // Если массив ассциативный
        //                 if ($keys !== array_keys($keys)) {
        //                     $pastdueArrear = $reply['pastdueArrear'];
        //                     $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
        //                     if ($past !== '0.00') {
        //                         $summ = floatval($past);
        //                     }
        //                 } else {
        //                     foreach ($reply['pastdueArrear'] as $pastdueArrear) {
        //                         $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
        //                         if ($past !== '0.00') {
        //                             $summ = floatval($past);
        //                         }
        //                     }
        //                 }


        //                 if (isset($reply['reportingDt'])) {
        //                     $curentDateDiff = date_diff(new DateTime(), new DateTime($reply['reportingDt']));
        //                 }
        //                 $status = [
        //                     'name' => 'Активный',
        //                     'color' => 'black',
        //                 ];
        //                 if (
        //                     (isset($reply['holdCode']) && $reply['holdCode'] == 1) ||
        //                     $curentDateDiff->days > 33 ||
        //                     (isset($reply['loanIndicator']) && $reply['loanIndicator'] != 1)
        //                 ) {
        //                     $status = [
        //                         'name' => 'Не определен',
        //                         'color' => 'silver',
        //                     ];
        //                 }
        //                 if($curentDateDiff->days > 180) {
        //                     $status = [
        //                         'name' => 'Архив',
        //                         'color' => 'silver',
        //                     ];
        //                 }

        //                 if($summ > 0) {
        //                     $status = [
        //                         'name' => 'Просрочен',
        //                         'color' => 'red',
        //                     ];
        //                 }

        //                 if(isset($reply['loanIndicator']) && in_array($reply['loanIndicator'], [3,11])) {
        //                     $status = [
        //                         'name' => 'Прощение долга',
        //                         'color' => 'red',
        //                     ];
        //                 }

        //                 if(isset($reply['submitHold']['holdCode']) && $reply['submitHold']['holdCode'] == 3) {
        //                     $status = [
        //                         'name' => 'Списан',
        //                         'color' => 'red',
        //                     ];
        //                 }

        //                 if(
        //                     (isset($reply['loanIndicator']) && ($reply['loanIndicator'] == 2 || $reply['loanIndicator'] == 1)) ||
        //                     (isset($reply['sbLoanIndicator']) && $reply['sbLoanIndicator'] == 1) ||
        //                     (isset($reply['collatRepay']) && $reply['collatRepay'] == 1)
        //                 ) {
        //                     $status = [
        //                         'name' => 'Счет закрыт',
        //                         'color' => 'green',
        //                     ];
        //                 }
        //                 if (isset($reply['businessCategory']) && $reply['businessCategory'] == 'MKK') {
        //                     $openDt = false;
        //                     if (isset($reply['trade'])) {
        //                         $keys = array_keys($reply['trade']);
        //                         // Если массив ассциативный
        //                         if ($keys !== array_keys($keys)) {
        //                             $openDt = self::date_format($reply['trade']['openedDt']);
        //                         } else {
        //                             $openDt = self::date_format($reply['trade'][0]['openedDt']);
        //                         }
        //                     }
        //                     $time = time() - (86400 * 92);
        //                     $dateMonth = date('d.m.Y', $time);
        //                     if ($openDt > $dateMonth) {
        //                         $mkk++;
        //                     }
        //                     if ($status['name'] == 'Активный' || $status['name'] == 'Просрочен' || $status['name'] == 'Не определен') {
        //                         $mkkSumm++;
        //                     }
        //                 }
        //                 if ($status['name'] == 'Активный' || $status['name'] == 'Просрочен' || $status['name'] == 'Не определен') {
        //                     $activeProduct++;

        //                     if (isset($reply['paymtCondition'])) {
        //                         $keys = array_keys($reply['paymtCondition']);
        //                         if ($keys !== array_keys($reply['paymtCondition'])) {
        //                             if (isset($reply['paymtCondition']['principalTermsAmt']) && isset($reply['paymtCondition']['interestTermsAmt'])) {
        //                                 $totalAverPaymtAmt += floatval($reply['paymtCondition']['principalTermsAmt']) + floatval($reply['paymtCondition']['interestTermsAmt']);;
        //                             }
        //                         } else {
        //                             $condition = end($reply['paymtCondition']);
        //                             if (isset($condition['principalTermsAmt']) && isset($condition['interestTermsAmt'])) {
        //                                 $totalAverPaymtAmt += floatval($condition['principalTermsAmt']) + floatval($condition['interestTermsAmt']);
        //                             }
        //                         }
        //                     } else {
        //                         if (isset($reply['monthAverPaymt'])) {
        //                             $totalAverPaymtAmt += floatval($reply['monthAverPaymt']['averPaymtAmt'] ?? 0);
        //                         }
        //                     }

        //                     if (isset($reply['accountAmt'])) {
        //                         $keys = array_keys($reply['accountAmt']);
        //                         // Если массив ассциативный
        //                         if ($keys !== array_keys($keys)) {
        //                             if ($status['name'] != 'Активный') {
        //                                 $dolg += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                             }
        //                             $totalAmtOutstanding += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                         } else {
        //                             foreach ($reply['accountAmt'] as $arrear) {
        //                                 if ($status['name'] != 'Активный') {
        //                                     $dolg += floatval($arrear['creditLimit'] ?? 0);
        //                                 }
        //                                 $totalAmtOutstanding += floatval($arrear['creditLimit'] ?? 0);
        //                             }
        //                         }
        //                     }
        //                 } else {
        //                     $doneProduct++;
        //                     if (isset($reply['accountAmt'])) {
        //                         $keys = array_keys($reply['accountAmt']);
        //                         // Если массив ассциативный
        //                         if ($keys !== array_keys($keys)) {
        //                             $totalAmtOutstandingDone += floatval($reply['accountAmt']['creditLimit'] ?? 0);
        //                         } else {
        //                             foreach ($reply['accountAmt'] as $arrear) {
        //                                 $totalAmtOutstandingDone += floatval($arrear['creditLimit'] ?? 0);
        //                             }
        //                         }
        //                     }
        //                 }
        //             }

        //             $nbki_arr = array(
        //                 'activeProduct' => $activeProduct,
        //                 'totalAmtOutstanding' => $totalAmtOutstanding,
        //                 'doneProduct' => $doneProduct,
        //                 'totalAmtOutstandingDone' => $totalAmtOutstandingDone,
        //                 'totalAverPaymtAmt' => $totalAverPaymtAmt,
        //                 'dolg' => $dolg,
        //                 'mkk' => $mkk,
        //                 'mkkSumm' => $mkkSumm
        //             );
        //             $json = json_encode($nbki_arr);

        //             $add = array(
        //                 'order_id' => $contract->order_id,
        //                 'variables' => $json,
        //             );

        //             $reoprt_contracts_nbki_id = $this->ReoprtContractsNbki->add_reoprt_nbki($add);

        //             if ($i > 0) {
        //                 return;
        //             }

        //         }
        //     }
        // }

        $this->db->query('
            SELECT * FROM `s_nbki_scoreballs` WHERE `variables` LIKE\'%{"pdl_overdue_count":0,"pdl_npl_limit_share":0,"pdl_npl_90_limit_share":0,"pdl_current_limit_max":0,"pdl_last_3m_limit":0,"pdl_last_good_max_limit":0,"pdl_good_limit":0,"pdl_prolong_3m_limit":0,"consum_current_limit_max":0,"consum_good_limit":0,"limit":%\' 
            AND 
            ball!=-558
            ORDER BY id
        ');
        $wrongs = $this->db->results();
        $i = 0;
        foreach ($wrongs as $wrong) {
            // $i++;
            // var_dump($i,'!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
            


            $scoring = $this->scorings->get_scoring($wrong->score_id);
            // $scoring = $this->scorings->get_scoring(3402/63);

            var_dump($scoring->order_id);
            echo '<hr>';

            $this->db->query("
            SELECT *
            FROM s_scorings
            WHERE order_id = ?
            and `type` = 'nbki'
            and `status` = 'completed'
            LIMIT 1", $scoring->order_id);

            $nbki = $this->db->result();
            $error = 0;

            if (empty($nbki)) {
                $error = 1;
            } else {
                $nbki = unserialize($nbki->body);

                if ($nbki == false)
                    $error = 1;
            }

            // if ($error == 1) {
            //     $update = [
            //         'status' => 'completed',
            //         'body' => 'Скоринг НБКИ пуст',
            //         'success' => 1,
            //         'string_result' => 'Скоринг НБКИ пуст'
            //     ];

            //     $this->scorings->update_scoring($scoring_id, $update);
            //     return $update;
            // }

            $order = OrdersORM::find($scoring->order_id);
            if (in_array($order->client_status, ['nk', 'rep'])){
                var_dump('new');
                echo '<hr>';
                // return $this->newClient($nbki, $scoring);
                $this->newClient($nbki, $scoring);
            }
            else{
                var_dump('old');
                echo '<hr>';
                // return $this->oldClient($nbki, $scoring);
                $this->oldClient($nbki, $scoring);

                $i++;
                var_dump($i,'!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
            }
            


                
            var_dump($wrong->score_id);
            if ($i > 20) {
                break;
            }
        }
        var_dump(count($wrong));
        echo '<hr>';
        var_dump('ok');
        die;



        

        exit;
    }






    private function newClient($nbki, $scoring)
    {
        $nbki_score = 193;
        $pdl_overdue_count = 0;
        $pdlCreditLimit = 0;
        $npl90CreditLimit = 0;
        $nplCreditLimit = 0;
        $pdl_npl_limit_share = 0;
        $pdl_npl_90_limit_share = 0;
        $pdl_current_limit_max = 0;
        $pdl_last_3m_limit = 0;
        $pdl_last_good_max_limit = 0;
        $Last_npl_opened = null;
        $pdl_good_limit = 0;
        $pdl_prolong_3m_limit = 0;
        $consum_current_limit_max = 0;
        $consum_good_limit = 0;

        $loanIndicator = false;
        $countPayments = 0;


        $now = new DateTime(date('Y-m-d'));

        
        $data = file_get_contents(str_replace('log_report','log_xml', $nbki['report_url']));
        $xml = simplexml_load_string($data);
        if (isset(json_decode(json_encode($xml), true)['product'])) {
            $nbki['json'] = json_decode(json_encode($xml), true)['product']['preply']['report'];
        }
        else{
            $nbki['json'] = json_decode(json_encode($xml), true)['preply']['report'];
        }

        foreach ($nbki['json']['AccountReplyRUTDF'] as $reply) {

            $loanKindCode = 3;
            $pasDue = 0;

            if (isset($reply['loanIndicator'])) {
                $loanIndicator = $reply['loanIndicator'];
            }

            if (isset($reply['payment'])) {
                $keys = array_keys($reply['trade']);
                if ($keys !== array_keys($keys)) {
                    $countPayments = 1;
                } else {
                    $countPayments = count($reply['payment']);
                }
            }

            if (isset($reply['trade'])) {
                $keys = array_keys($reply['trade']);
                if ($keys !== array_keys($keys)) {
                    $loanKindCode = $reply['trade']['loanKindCode'];

                    if (isset($openedDt) && $openedDt < new DateTime(date('Y-m-d', strtotime($reply['trade']['openedDt']))))
                        $Last_npl_opened = $reply['trade']['openedDt'];

                    $openedDt = new DateTime(date('Y-m-d', strtotime($reply['trade']['openedDt'])));

                    if (date_diff($now, $openedDt)->days <= 90)
                        $pdl_last_3m_limit += floatval($reply['accountAmt']['creditLimit']);

                } else {
                    $loanKindCode = $reply['trade'][0]['loanKindCode'];

                    if (isset($openedDt) && $openedDt < new DateTime(date('Y-m-d', strtotime($reply['trade'][0]['openedDt']))))
                        $Last_npl_opened = $reply['trade'][0]['openedDt'];

                    $openedDt = new DateTime(date('Y-m-d', strtotime($reply['trade'][0]['openedDt'])));

                    if (date_diff($now, $openedDt)->days <= 90)
                        $pdl_last_3m_limit += floatval($reply['accountAmt']['creditLimit']);
                }
            }

            if (isset($reply['pastdueArrear'])) {
                $keys = array_keys($reply['pastdueArrear']);
                if ($keys !== array_keys($keys)) {
                    $pastdueArrear = $reply['pastdueArrear'];
                    $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
                    if ($past !== '0.00') {
                        $pasDue = floatval($past);
                    }
                } else {
                    foreach ($reply['pastdueArrear'] as $pastdueArrear) {
                        $past = str_replace(',', '.', $pastdueArrear['amtPastDue'] ?? '');
                        if ($past !== '0.00') {
                            $pasDue = floatval($past);
                        }
                    }
                }
            }

            if ($loanKindCode == 3) {
                $pdlCreditLimit += floatval($reply['accountAmt']['creditLimit']);
                if ($pasDue > 0) {

                    if (isset($reply['trade'])) {
                        $keys = array_keys($reply['trade']);
                        if ($keys !== array_keys($keys)) {
                            if (isset($openedDt) && $openedDt < new DateTime(date('Y-m-d', strtotime($reply['trade']['openedDt']))))
                                $Last_npl_opened = $reply['trade']['openedDt'];
                        } else {
                            if (isset($openedDt) && $openedDt < new DateTime(date('Y-m-d', strtotime($reply['trade'][0]['openedDt']))))
                                $Last_npl_opened = $reply['trade'][0]['openedDt'];
                        }
                    }
                    $nplCreditLimit += floatval($reply['accountAmt']['creditLimit']);
                    $pdl_last_good_max_limit += floatval($reply['accountAmt']['creditLimit']);

                    $npl90CreditLimit += floatval($reply['accountAmt']['creditLimit']); //TODO: уточнить эту информацию
                    $pdl_overdue_count++;
                }
                if ($pasDue == 0) {
                    $consum_good_limit += floatval($reply['accountAmt']['creditLimit']);
                    $pdl_good_limit += floatval($reply['accountAmt']['creditLimit']);
                }
                if ($countPayments >= 3) {
                    $pdl_prolong_3m_limit += floatval($reply['accountAmt']['creditLimit']);
                }
                if ($loanIndicator != 1) {
                    $pdl_current_limit_max = floatval($reply['accountAmt']['creditLimit']);
                }
            }

            if ($loanKindCode == 1 && $loanIndicator != 1 && $pasDue = 0) {
                $consum_current_limit_max += floatval($reply['accountAmt']['creditLimit']);
            }
        }
        if ($pdl_overdue_count < 1)
            $nbki_score += 100;
        elseif ($pdl_overdue_count == 1)
            $nbki_score -= 19;
        elseif ($pdl_overdue_count == 2)
            $nbki_score -= 97;
        elseif ($pdl_overdue_count == 3)
            $nbki_score -= 203;
        elseif ($pdl_overdue_count >= 4)
            $nbki_score -= 497;

        if ($pdlCreditLimit != 0) {
            $pdl_npl_limit_share = $nplCreditLimit / $pdlCreditLimit;
            $pdl_npl_90_limit_share = $npl90CreditLimit / $pdlCreditLimit;
        }
        if ($pdl_npl_limit_share < (10 / 100))
            $nbki_score += 30;
        elseif ($pdl_npl_limit_share >= (10 / 100) && $pdl_npl_limit_share < (20 / 100))
            $nbki_score += 20;
        elseif ($pdl_npl_limit_share >= (20 / 100) && $pdl_npl_limit_share < (30 / 100))
            $nbki_score -= 9;
        elseif ($pdl_npl_limit_share >= (30 / 100) && $pdl_npl_limit_share < (50 / 100))
            $nbki_score -= 42;
        elseif ($pdl_npl_limit_share >= (50 / 100))
            $nbki_score -= 128;

        if ($pdl_npl_90_limit_share < (10 / 100))
            $nbki_score += 57;
        elseif ($pdl_npl_90_limit_share >= (10 / 100) && $pdl_npl_90_limit_share < (20 / 100))
            $nbki_score += 1;
        elseif ($pdl_npl_90_limit_share >= (20 / 100) && $pdl_npl_90_limit_share < (30 / 100))
            $nbki_score -= 66;
        elseif ($pdl_npl_90_limit_share >= (30 / 100) && $pdl_npl_90_limit_share < (50 / 100))
            $nbki_score -= 137;
        elseif ($pdl_npl_90_limit_share >= (30 / 100))
            $nbki_score -= 291;

        if ($pdl_current_limit_max < 2500)
            $nbki_score -= 170;
        elseif ($pdl_current_limit_max >= 2500 && $pdl_current_limit_max < 5000)
            $nbki_score -= 75;
        elseif ($pdl_current_limit_max >= 5000 && $pdl_current_limit_max < 10000)
            $nbki_score -= 36;
        elseif ($pdl_current_limit_max >= 10000 && $pdl_current_limit_max < 20000)
            $nbki_score += 38;
        elseif ($pdl_current_limit_max >= 20000)
            $nbki_score += 72;

        if ($pdl_last_3m_limit < 10000)
            $nbki_score -= 355;
        elseif ($pdl_last_3m_limit >= 10000 && $pdl_last_3m_limit < 20000)
            $nbki_score -= 97;
        elseif ($pdl_last_3m_limit >= 20000 && $pdl_last_3m_limit < 50000)
            $nbki_score += 25;
        elseif ($pdl_last_3m_limit >= 50000 && $pdl_last_3m_limit < 100000)
            $nbki_score += 132;
        elseif ($pdl_last_3m_limit >= 100000)
            $nbki_score += 183;

        if ($pdl_last_good_max_limit < 3000)
            $nbki_score -= 86;
        elseif ($pdl_last_good_max_limit >= 3000 && $pdl_last_good_max_limit < 6000)
            $nbki_score -= 35;
        elseif ($pdl_last_good_max_limit >= 6000 && $pdl_last_good_max_limit < 10000)
            $nbki_score -= 12;
        elseif ($pdl_last_good_max_limit >= 10000 && $pdl_last_good_max_limit < 20000)
            $nbki_score += 3;
        elseif ($pdl_last_good_max_limit >= 20000)
            $nbki_score += 15;

        if ($pdl_good_limit < 20000)
            $nbki_score -= 143;
        elseif ($pdl_good_limit >= 20000 && $pdl_good_limit < 40000)
            $nbki_score -= 45;
        elseif ($pdl_good_limit >= 40000 && $pdl_good_limit < 80000)
            $nbki_score -= 7;
        elseif ($pdl_good_limit >= 80000 && $pdl_good_limit < 150000)
            $nbki_score += 21;
        elseif ($pdl_good_limit >= 150000 && $pdl_good_limit < 300000)
            $nbki_score += 38;
        elseif ($pdl_good_limit >= 300000)
            $nbki_score += 51;

        if ($pdl_prolong_3m_limit < 5000)
            $nbki_score -= 89;
        elseif ($pdl_prolong_3m_limit >= 5000 && $pdl_prolong_3m_limit < 10000)
            $nbki_score -= 24;
        elseif ($pdl_prolong_3m_limit >= 10000 && $pdl_prolong_3m_limit < 20000)
            $nbki_score += 23;
        elseif ($pdl_prolong_3m_limit >= 20000 && $pdl_prolong_3m_limit < 40000)
            $nbki_score += 51;
        elseif ($pdl_prolong_3m_limit >= 40000 && $pdl_prolong_3m_limit < 80000)
            $nbki_score += 72;
        elseif ($pdl_prolong_3m_limit >= 80000)
            $nbki_score += 99;

        if ($consum_current_limit_max < 10000)
            $nbki_score -= 66;
        elseif ($consum_current_limit_max >= 10000 && $consum_current_limit_max < 100000)
            $nbki_score += 38;
        elseif ($consum_current_limit_max >= 100000 && $consum_current_limit_max < 300000)
            $nbki_score += 56;
        elseif ($consum_current_limit_max >= 300000)
            $nbki_score += 77;

        if ($consum_good_limit < 1)
            $nbki_score -= 28;
        elseif ($consum_good_limit >= 1 && $consum_good_limit < 100000)
            $nbki_score += 45;
        elseif ($consum_good_limit >= 100000 && $consum_good_limit < 400000)
            $nbki_score += 61;
        elseif ($consum_good_limit >= 400000)
            $nbki_score += 88;


        if (isset($nbki['barents_scoring'])) {
            $nbki_score = $nbki['barents_scoring']['new_client_result'];
        }

        if ($nbki_score < 300)
            $limit = 0;
        elseif ($nbki_score >= 300 && $nbki_score < 799)
            $limit = 3000;
        elseif ($nbki_score >= 800 && $nbki_score < 899)
            $limit = 5000;
        elseif ($nbki_score >= 900)
            $limit = 7000;


        if ($nbki_score < 300)
            $update = [
                'status' => 'completed',
                'body' => 'Проверка не пройдена',
                'success' => 0,
                'string_result' => 'Отказ',
                'scorista_ball' => $nbki_score
            ];
        else
            $update = [
                'status' => 'completed',
                'body' => 'Проверка пройдена',
                'success' => 1,
                'string_result' => 'Лимит: ' . $limit,
                'scorista_ball' => $nbki_score
            ];

        $variables =
            [
                'pdl_overdue_count' => $pdl_overdue_count,
                'pdl_npl_limit_share' => $pdl_npl_limit_share,
                'pdl_npl_90_limit_share' => $pdl_npl_90_limit_share,
                'pdl_current_limit_max' => $pdl_current_limit_max,
                'pdl_last_3m_limit' => $pdl_last_3m_limit,
                'pdl_last_good_max_limit' => $pdl_last_good_max_limit,
                'pdl_good_limit' => $pdl_good_limit,
                'pdl_prolong_3m_limit' => $pdl_prolong_3m_limit,
                'consum_current_limit_max' => $consum_current_limit_max,
                'consum_good_limit' => $consum_good_limit,
                'limit' => (isset($limit)) ? $limit : 0
            ];

            if ($nbki_score == -557) {
                $nbki_score = -558;
            }
        $nbkiScoreBalls =
            [
                'order_id' => $scoring->order_id,
                'score_id' => $scoring->id,
                'ball' => $nbki_score,
                'variables' => json_encode($variables)
            ];

           
        $this->NbkiScoreballs->update_by_scoring($scoring->id, $nbkiScoreBalls);
        // $this->NbkiScoreballs->add($nbkiScoreBalls);

        // $this->scorings->update_scoring($scoring->id, $update);
        // die;
        return $update;
    }

    private function oldClient($nbki, $scoring)
    {
        $nbki_score = 456;
        $prev_3000_500_paid_count_wo_del = 0;
        $sumPayedPercents = 0;
        $sumPayedPercents3000 = 0;
        $prev_max_delay = 0;
        $current_overdue_sum = 0;
        $closed_to_total_credits_count_share = 0;
        $sumAccountRate13 = 0;
        $sumAccountRate = 0;
        $pdl_overdue_count = 0;
        $pdl_npl_90_limit_share = 0;
        $sumAllPdl = 0;
        $sumPdl90 = 0;

        $lastContract = ContractsORM::where('user_id', $scoring->user_id)->orderBy('id', 'desc')->first();
        $allContracts = ContractsORM::where('user_id', $scoring->user_id)->get();

        $now = new DateTime(date('Y-m-d'));
        $returnDateLastContract = new DateTime(date('Y-m-d', strtotime($lastContract->return_date)));
        $days_from_last_closed = date_diff($now, $returnDateLastContract)->days;
        $last_credit_delay = $lastContract->count_expired_days;

        foreach ($allContracts as $contract) {
            $operations = OperationsORM::where('order_id', $contract->order_id)->where('type', 'PAY')->get();

            foreach ($operations as $operation) {
                $transaction = TransactionsORM::find($operation->transaction_id);

                $sumPayedPercents += $transaction->loan_percents_summ;

                if ($contract->amount >= 3000)
                    $sumPayedPercents3000 += $transaction->loan_percents_summ;
            }

            if ($sumPayedPercents3000 >= 500 && $contract->count_expired_days == 0)
                $prev_3000_500_paid_count_wo_del++;

            if ($contract->count_expired_days > $prev_max_delay)
                $prev_max_delay = $contract->count_expired_days;
        }

        $data = file_get_contents(str_replace('log_report','log_xml', $nbki['report_url']));
        $xml = simplexml_load_string($data);
        if (isset(json_decode(json_encode($xml), true)['product'])) {
            $nbki['json'] = json_decode(json_encode($xml), true)['product']['preply']['report'];
        }
        else{
            $nbki['json'] = json_decode(json_encode($xml), true)['preply']['report'];
        }

        foreach ($nbki['json']['AccountReply'] as $scor) {
            $current_overdue_sum += $scor['amtPastDue'];
            $sumAccountRate += $scor['creditLimit'];

            if (!empty($scor['accountRating'])) {
                if ($scor['accountRating'] == 13)
                    $sumAccountRate13 += $scor['creditLimit'];
            }

            if (in_array($scor['acctType'], [16, 9, 7]) && $scor['creditLimit'] <= 30000) {

                $sumAllPdl += $scor['creditLimit'];

                if($scor['amtPastDue'] > 0)
                    $pdl_overdue_count ++;

                $scor['paymtPat'] = preg_replace('/[^0-9]/', '', $scor['paymtPat']);

                if (!empty($scor['paymtPat'])) {
                    $scor['paymtPat'] = str_split($scor['paymtPat']);

                    foreach ($scor['paymtPat'] as $value) {
                        if ($value >= 4) {
                            $sumPdl90 += $scor['creditLimit'];
                        }
                    }
                }
            }
        }

        if($sumAllPdl != 0)
            $pdl_npl_90_limit_share = $sumAllPdl / $sumPdl90;

        if($sumAccountRate != 0)
            $closed_to_total_credits_count_share =  $sumAccountRate13 / $sumAccountRate;


        if ($days_from_last_closed < 1)
            $nbki_score += 7;
        elseif ($days_from_last_closed >= 1 && $days_from_last_closed < 2)
            $nbki_score += 31;
        elseif ($days_from_last_closed >= 2 && $days_from_last_closed < 15)
            $nbki_score += 44;
        elseif ($days_from_last_closed >= 15 && $days_from_last_closed < 30)
            $nbki_score += 33;
        elseif ($days_from_last_closed >= 30 && $days_from_last_closed < 60)
            $nbki_score += 2;
        elseif ($days_from_last_closed >= 60 && $days_from_last_closed < 90)
            $nbki_score -= 20;
        elseif ($days_from_last_closed >= 90)
            $nbki_score -= 51;

        if ($prev_3000_500_paid_count_wo_del < 2)
            $nbki_score -= 21;
        elseif ($prev_3000_500_paid_count_wo_del >= 2 && $prev_3000_500_paid_count_wo_del < 4)
            $nbki_score += 31;
        elseif ($prev_3000_500_paid_count_wo_del >= 4 && $prev_3000_500_paid_count_wo_del < 6)
            $nbki_score += 69;
        elseif ($prev_3000_500_paid_count_wo_del >= 6 && $prev_3000_500_paid_count_wo_del < 8)
            $nbki_score += 101;
        elseif ($prev_3000_500_paid_count_wo_del >= 8)
            $nbki_score += 154;

        if ($sumPayedPercents < 2000)
            $nbki_score -= 14;
        elseif ($sumPayedPercents >= 2000 && $sumPayedPercents < 4000)
            $nbki_score += 11;
        elseif ($sumPayedPercents >= 4000 && $sumPayedPercents < 8000)
            $nbki_score += 38;
        elseif ($sumPayedPercents >= 8000 && $sumPayedPercents < 20000)
            $nbki_score += 73;
        elseif ($sumPayedPercents >= 20000 && $sumPayedPercents < 40000)
            $nbki_score += 107;
        elseif ($sumPayedPercents >= 40000)
            $nbki_score += 141;

        if ($prev_max_delay < 30)
            $nbki_score -= 7;
        elseif ($prev_max_delay >= 30 && $prev_max_delay < 60)
            $nbki_score -= 36;
        elseif ($prev_max_delay >= 60)
            $nbki_score -= 98;

        if ($last_credit_delay < 10)
            $nbki_score += 45;
        elseif ($last_credit_delay >= 10 && $last_credit_delay < 20)
            $nbki_score += 25;
        elseif ($last_credit_delay >= 20 && $last_credit_delay < 30)
            $nbki_score -= 12;
        elseif ($last_credit_delay >= 30 && $last_credit_delay < 60)
            $nbki_score -= 93;
        elseif ($last_credit_delay >= 60)
            $nbki_score -= 264;

        if ($current_overdue_sum < 10000)
            $nbki_score += 26;
        elseif ($current_overdue_sum >= 10000 && $current_overdue_sum < 50000)
            $nbki_score += 17;
        elseif ($current_overdue_sum >= 50000 && $current_overdue_sum < 100000)
            $nbki_score -= 2;
        elseif ($current_overdue_sum >= 100000 && $current_overdue_sum < 200000)
            $nbki_score -= 25;
        elseif ($current_overdue_sum >= 200000)
            $nbki_score -= 55;

        if ($closed_to_total_credits_count_share < 0.7)
            $nbki_score -= 58;
        elseif ($closed_to_total_credits_count_share >= 0.7 && $closed_to_total_credits_count_share < 0.8)
            $nbki_score -= 28;
        elseif ($closed_to_total_credits_count_share >= 0.8 && $closed_to_total_credits_count_share < 0.85)
            $nbki_score -= 4;
        elseif ($closed_to_total_credits_count_share >= 0.85 && $closed_to_total_credits_count_share < 0.9)
            $nbki_score += 32;
        elseif ($closed_to_total_credits_count_share >= 0.9 && $closed_to_total_credits_count_share < 0.95)
            $nbki_score += 74;
        elseif ($closed_to_total_credits_count_share >= 0.95 && $closed_to_total_credits_count_share <= 1)
            $nbki_score += 133;

        if ($pdl_overdue_count < 3)
            $nbki_score += 15;
        elseif ($pdl_overdue_count >= 3 && $pdl_overdue_count < 5)
            $nbki_score -= 15;
        elseif ($pdl_overdue_count >= 5 && $pdl_overdue_count < 7)
            $nbki_score -= 37;
        elseif ($pdl_overdue_count >= 7 && $pdl_overdue_count < 10)
            $nbki_score -= 73;
        elseif ($pdl_overdue_count >= 10)
            $nbki_score -= 122;

        if ($pdl_npl_90_limit_share < 1)
            $nbki_score += 22;
        elseif ($pdl_npl_90_limit_share >= 1 && $pdl_npl_90_limit_share < 5)
            $nbki_score += 13;
        elseif ($pdl_npl_90_limit_share >= 5 && $pdl_npl_90_limit_share < 10)
            $nbki_score -= 4;
        elseif ($pdl_npl_90_limit_share >= 10 && $pdl_npl_90_limit_share < 15)
            $nbki_score -= 19;
        elseif ($pdl_npl_90_limit_share >= 15 && $pdl_npl_90_limit_share < 20)
            $nbki_score -= 31;
        elseif ($pdl_npl_90_limit_share >= 20)
            $nbki_score -= 47;

        if (isset($nbki['barents_scoring'])) {
            $nbki_score = $nbki['barents_scoring']['old_client_result'];
        }

        $limit = 0;

        if ($nbki_score >= 0 && $nbki_score <= 299)
            $limit = 3000;
        elseif ($nbki_score >= 300 && $nbki_score <= 499)
            $limit = 5000;
        elseif ($nbki_score >= 500 && $nbki_score <= 549)
            $limit = 7000;
        elseif ($nbki_score >= 550 && $nbki_score <= 599)
            $limit = 10000;
        elseif ($nbki_score >= 600 && $nbki_score <= 699)
            $limit = 15000;
        elseif ($nbki_score >= 700)
            $limit = 20000;


        if ($nbki_score < 0)
            $update = [
                'status' => 'completed',
                'body' => 'Проверка не пройдена',
                'success' => 0,
                'string_result' => 'Отказ',
                'scorista_ball' => $nbki_score
            ];
        else
            $update = [
                'status' => 'completed',
                'body' => 'Проверка пройдена',
                'success' => 1,
                'string_result' => 'Лимит: ' . $limit,
                'scorista_ball' => $nbki_score
            ];

        $variables =
            [
                'days_from_last_closed' => $days_from_last_closed,
                'prev_3000_500_paid_count_wo_del' => $prev_3000_500_paid_count_wo_del,
                'sumPayedPercents' => $sumPayedPercents,
                'prev_max_delay' => $prev_max_delay,
                'last_credit_delay' => $last_credit_delay,
                'current_overdue_sum' => $current_overdue_sum,
                'closed_to_total_credits_count_share' => $closed_to_total_credits_count_share,
                'pdl_overdue_count' => $pdl_overdue_count,
                'pdl_npl_90_limit_share' => $pdl_npl_90_limit_share,
                'limit' => $limit
            ];

            if ($nbki_score == -557) {
                $nbki_score = -558;
            }
        $nbkiScoreBalls =
            [
                'order_id' => $scoring->order_id,
                'score_id' => $scoring->id,
                'ball' => $nbki_score,
                'variables' => json_encode($variables)
            ];
            var_dump($scoring->id);
            echo '<br>';
            echo '<br>';
            var_dump($update);
            echo '<hr>';

        $this->NbkiScoreballs->update_by_scoring($scoring->id, $nbkiScoreBalls);
        // $this->NbkiScoreballs->add($nbkiScoreBalls);

        // $this->scorings->update_scoring($scoring->id, $update);
        // die;
        return $update;
    }









    
    // public static function date_format($date_string, $format = 'd.m.Y') {
    //     try {
    //         return date($format, strtotime($date_string));
    //     } catch (Exception $exception) {
    //         return $date_string;
    //     }
    // }

    // private function reject_amount($address_id)
    // {

    //     $address = $this->Addresses->get_address($address_id);
        
    //     $scoring_type = $this->scorings->get_type('location');
        
    //     $reg='green-regions';
    //     $yellow_regions = array_map('trim', explode(',', $scoring_type->params['yellow-regions']));
    //     if(in_array(mb_strtolower(trim($address->region), 'utf8'), $yellow_regions)){
    //         $reg = 'yellow-regions';
    //     }
    //     $red_regions = array_map('trim', explode(',', $scoring_type->params['red-regions']));
    //     if(in_array(mb_strtolower(trim($address->region), 'utf8'), $red_regions)){
    //         $reg = 'red-regions';
    //     }
    //     $exception_regions = array_map('trim', explode(',', $scoring_type->params['regions']));
    //     if(in_array(mb_strtolower(trim($address->region), 'utf8'), $exception_regions)){
    //         $reg = 'regions';
    //     }

    //     $contract_operations = $this->ServicesCost->gets(array('region' => $reg));
    //     if (isset($contract_operations[0]->reject_reason_cost)) {
    //         return (float)$contract_operations[0]->reject_reason_cost;
    //     }
    //     else{
    //         return 19;
    //     }
    // }

    // // Сжать изображение 
    // public function compressImage($source, $destination, $quality) {

    //     $info = getimagesize($source);

    //     if ($info['mime'] == 'image/jpeg') 
    //     $image = imagecreatefromjpeg($source);

    //     elseif ($info['mime'] == 'image / gif') 
    //     $image = imagecreatefromgif($source);

    //     elseif ($info['mime'] == 'image/png') 
    //     $image = imagecreatefrompng($source);

    //     imagejpeg($image, $destination, $quality);

    // }

    // public function send_message($token, $chat_id, $text)
	// {
	// 	$getQuery = array(
    //         "chat_id" 	=> $chat_id,
    //         "text"  	=> $text,
    //         "parse_mode" => "html",
    //     );
    //     $ch = curl_init("https://api.telegram.org/bot". $token ."/sendMessage?" . http_build_query($getQuery));
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_HEADER, false);

    //     $resultQuery = curl_exec($ch);
    //     curl_close($ch);

    //     echo $resultQuery;
    // }

    // public function run_scoring($scoring_id)
    // {
    //     $scoring = $this->scorings->get_scoring($scoring_id);
    //     $order = $this->orders->get_order((int)$scoring->order_id);

    //     $person =
    //         [
    //             'personLastName' => $order->lastname,
    //             'personFirstName' => $order->firstname,
    //             'phone' => preg_replace('/[^0-9]/', '', $order->phone_mobile),
    //             'personBirthDate' => date('d.m.Y', strtotime($order->birth))
    //         ];

    //     if (!empty($order->patronymic))
    //         $person['personMidName'] = $order->patronymic;

    //     $score = $this->IdxApi->search($person);

    //     if (empty($score)) {

    //         $update =
    //             [
    //                 'status' => 'error',
    //                 'body' => '',
    //                 'success' => 0,
    //                 'string_result' => 'Ошибка запроса'
    //             ];

    //         $this->scorings->update_scoring($scoring_id, $update);
    //         $this->logging($person, $score);
    //         return $update;
    //     }

    //     if ($score['operationResult'] == 'fail') {
    //         $update =
    //             [
    //                 'status' => 'completed',
    //                 'body' => '',
    //                 'success' => 0,
    //                 'string_result' => 'Клиент не найден в списке'
    //             ];

    //         $this->scorings->update_scoring($scoring_id, $update);
    //         $this->logging($person, $score);
    //         return $update;
    //     }

    //     $update =
    //         [
    //             'status' => 'completed',
    //             'body' => $score['validationScorePhone'],
    //             'success' => 1,
    //             'string_result' => 'Пользователь найден: ' . $this->IdxApi->result[$score['validationScorePhone']]
    //         ];

    //     $this->scorings->update_scoring($scoring_id, $update);
    //     return $this->logging($person, $score);
    // }

    // private function logging($request, $response, $filename = 'idxLog.txt')
    // {
    //     echo 1;


    //     $log_filename = $this->config->root_dir.'logs/'. $filename;

    //     if (date('d', filemtime($log_filename)) != date('d')) {
    //         $archive_filename = $this->config->root_dir.'logs/' . 'archive/' . date('ymd', filemtime($log_filename)) . '.' . $filename;
    //         rename($log_filename, $archive_filename);
    //         file_put_contents($log_filename, "\xEF\xBB\xBF");
    //     }


    //     $str = PHP_EOL . '===================================================================' . PHP_EOL;
    //     $str .= date('d.m.Y H:i:s') . PHP_EOL;
    //     $str .= var_export($request, true) . PHP_EOL;
    //     $str .= var_export($response, true) . PHP_EOL;
    //     $str .= 'END' . PHP_EOL;

    //     file_put_contents($this->config->root_dir.'logs/' . $filename, $str, FILE_APPEND);

    //     return 1;
    // }

    // private function restrDocs()
    // {
    //     $contract = ContractsORM::find(2141);
    //     $user = UsersORM::find(20473);

    //     $paymentSchedules = PaymentsSchedulesORM::find(28);
    //     $paymentSchedules = json_decode($paymentSchedules->payment_schedules, true);

    //     $schedule = new stdClass();
    //     $schedule->order_id = 22984;
    //     $schedule->user_id = 20473;
    //     $schedule->contract_id = 2141;
    //     $schedule->init_od = $contract->loan_body_summ;
    //     $schedule->init_prc = $contract->loan_percents_summ;
    //     $schedule->init_peni = $contract->loan_peni_summ;
    //     $schedule->actual = 1;
    //     $schedule->payment_schedules = json_encode($paymentSchedules);

    //     $params = [
    //         'contract' => $contract,
    //         'user' => $user,
    //         'schedules' => $schedule
    //     ];

    //     var_dump(json_encode($params));
    //     exit;
    // }
}