<?php

class TestController extends Controller
{
    public function fetch()
    {
        $scoring = $this->scorings->get_scoring(39);
        $scoring = unserialize($scoring->body);

        echo '<pre>';

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

        $now = new DateTime(date('Y-m-d'));

        foreach ($scoring['json']['AccountReply'] as $scor) {

            if (in_array($scor['acctType'], [16, 9, 7]) && $scor['creditLimit'] <= 30000) {

                $pdlCreditLimit += $scor['creditLimit'];

                if ($scor['amtPastDue'] > 0)
                    $pdl_overdue_count++;

                $scor['paymtPat'] = preg_replace('/[^0-9]/', '', $scor['paymtPat']);

                if (!empty($scor['paymtPat'])) {
                    $scor['paymtPat'] = str_split($scor['paymtPat']);

                    foreach ($scor['paymtPat'] as $value) {
                        if ($value >= 2 || $scor['amtPastDue'] > 0) {
                            $nplCreditLimit += $scor['creditLimit'];
                            if (isset($openedDt) && $openedDt < new DateTime(date('Y-m-d', strtotime($scor['openedDt']))))
                                $Last_npl_opened = $scor['openedDt'];

                            break;
                        }
                    }
                }

                if (!empty($scor['accountRating'])) {
                    if ($scor['accountRating'] != 13 && $scor['creditLimit'] > $pdl_current_limit_max)
                        $pdl_current_limit_max = $scor['creditLimit'];
                }

                $openedDt = new DateTime(date('Y-m-d', strtotime($scor['openedDt'])));

                if (date_diff($now, $openedDt)->days <= 90)
                    $pdl_last_3m_limit += $scor['creditLimit'];
            }
        }

        foreach ($scoring['json']['AccountReply'] as $scor) {

            if (in_array($scor['acctType'], [16, 9, 7]) && $scor['creditLimit'] <= 30000) {

                $scor['paymtPat'] = preg_replace('/[^0-9]/', '', $scor['paymtPat']);

                if (!empty($scor['paymtPat'])) {
                    $scor['paymtPat'] = str_split($scor['paymtPat']);

                    foreach ($scor['paymtPat'] as $value) {
                        if ($value >= 4) {
                            $npl90CreditLimit += $scor['creditLimit'];
                            break;
                        }
                    }
                }
            }
        }

        foreach ($scoring['json']['AccountReply'] as $scor) {

            if (in_array($scor['acctType'], [16, 9, 7]) && $scor['creditLimit'] <= 30000) {

                $scor['paymtPat'] = preg_replace('/[^0-9]/', '', $scor['paymtPat']);

                if (!empty($scor['paymtPat'])) {
                    $scor['paymtPat'] = str_split($scor['paymtPat']);

                    foreach ($scor['paymtPat'] as $value) {
                        if ($value >= 2 || $scor['amtPastDue'] > 0) {
                            $openedDt = new DateTime(date('Y-m-d', strtotime($scor['openedDt'])));
                            if ($openedDt > $Last_npl_opened && $scor['creditLimit'] > $pdl_last_good_max_limit) {
                                $pdl_last_good_max_limit = $scor['creditLimit'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        foreach ($scoring['json']['AccountReply'] as $scor) {

            if (in_array($scor['acctType'], [16, 9, 7]) && $scor['creditLimit'] <= 30000) {

                $scor['paymtPat'] = preg_replace('/[^0-9]/', '', $scor['paymtPat']);

                if (!empty($scor['paymtPat'])) {
                    $scor['paymtPat'] = str_split($scor['paymtPat']);

                    foreach ($scor['paymtPat'] as $value) {
                        if ($value >= 2 || $scor['amtPastDue'] != 0) {
                            continue;
                        } else {
                            $pdl_good_limit += $scor['creditLimit'];
                            break;
                        }
                    }
                }
            }
        }

        foreach ($scoring['json']['AccountReply'] as $scor) {

            if (in_array($scor['acctType'], [16]) && $scor['creditLimit'] <= 30000 && $scor['fact_term_m'] >= 3) {

                $scor['paymtPat'] = preg_replace('/[^0-9]/', '', $scor['paymtPat']);

                if (!empty($scor['paymtPat'])) {
                    $scor['paymtPat'] = str_split($scor['paymtPat']);

                    foreach ($scor['paymtPat'] as $value) {
                        if ($value >= 2 || $scor['amtPastDue'] != 0) {
                            continue;
                        } else {
                            $pdl_prolong_3m_limit += $scor['creditLimit'];
                            break;
                        }
                    }
                }
            }
        }

        foreach ($scoring['json']['AccountReply'] as $scor) {

            if (in_array($scor['acctType'], [16, 9, 7]) && $scor['creditLimit'] > 30000) {

                if ($scor['amtPastDue'] != 13  && $scor['amtPastDue'] == 0 && $scor['creditLimit'] > $consum_current_limit_max) {
                    $consum_current_limit_max = $scor['creditLimit'];
                    break;
                }
            }
        }

        foreach ($scoring['json']['AccountReply'] as $scor) {

            if (in_array($scor['acctType'], [16, 9, 7]) && $scor['creditLimit'] > 30000) {

                $scor['paymtPat'] = preg_replace('/[^0-9]/', '', $scor['paymtPat']);

                if (!empty($scor['paymtPat'])) {
                    $scor['paymtPat'] = str_split($scor['paymtPat']);

                    foreach ($scor['paymtPat'] as $value) {
                        if ($value >= 2 || $scor['amtPastDue'] != 0) {
                            continue;
                        } else {
                            $consum_good_limit += $scor['creditLimit'];
                            break;
                        }
                    }
                }
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

        if ($pdl_npl_limit_share < 10)
            $nbki_score += 30;
        elseif ($pdl_npl_limit_share >= 10 && $pdl_npl_limit_share < 20)
            $nbki_score += 20;
        elseif ($pdl_npl_limit_share >= 20 && $pdl_npl_limit_share < 30)
            $nbki_score -= 9;
        elseif ($pdl_npl_limit_share >= 30 && $pdl_npl_limit_share < 50)
            $nbki_score -= 42;
        elseif ($pdl_npl_limit_share >= 50)
            $nbki_score -= 128;

        if ($pdl_npl_90_limit_share < 10)
            $nbki_score += 57;
        elseif ($pdl_npl_90_limit_share >= 10 && $pdl_npl_90_limit_share < 20)
            $nbki_score += 1;
        elseif ($pdl_npl_90_limit_share >= 20 && $pdl_npl_90_limit_share < 30)
            $nbki_score -= 66;
        elseif ($pdl_npl_90_limit_share >= 30 && $pdl_npl_90_limit_share < 50)
            $nbki_score -= 137;
        elseif ($pdl_npl_90_limit_share >= 50)
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
            $nbki_score -= 25;
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
            $nbki_score += 5;

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
            $nbki_score -= 38;
        elseif ($consum_current_limit_max >= 100000 && $consum_current_limit_max < 300000)
            $nbki_score += 56;
        elseif ($consum_current_limit_max >= 300000)
            $nbki_score += 77;

        if ($consum_good_limit < 1)
            $nbki_score -= 28;
        elseif ($consum_good_limit >= 1 && $consum_good_limit < 100000)
            $nbki_score -= 45;
        elseif ($consum_good_limit >= 100000 && $consum_good_limit < 400000)
            $nbki_score += 61;
        elseif ($consum_good_limit >= 400000)
            $nbki_score += 88;


        var_dump($nbki_score);
        exit;
    }
}