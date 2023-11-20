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
        echo $query;
        die;
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
            // var_dump($type_pk);
            echo '<hr>';
        }

        

        die;
        
        $i = 0;
        foreach ($orders as $order) {

            $query = $this->db->placehold("
                SELECT *
                FROM __scorings
                WHERE order_id = ?
                AND type = 'nbki'
            ", $order->id);
            $this->db->query($query);
            $nbkiScor = $this->db->results()[0];

            $nbki_extra_scoring = $this->NbkiExtraScorings->get($order->id);
            
            //  var_dump($nbki_extra_scoring);
            if ($nbki_extra_scoring != 0) 
                continue;
                
            $i++;
            if ($i > 1) {
                break;
            }

            $add_nbki = [];

            $add_nbki['order_id'] = $order->id;

            $add_nbki['score_id'] = 0;
            if (!is_null($nbkiScor->id)) {
                $add_nbki['score_id'] = $nbkiScor->id;
            }
            
            if ($nbkiScor) {
                $nbkiParams = unserialize($nbkiScor->body);
                
                if (isset($nbkiParams['number_of_active'])) {
                    if (is_array($nbkiParams['number_of_active'])) 
                        $add_nbki['number_of_active'] = $nbkiParams['number_of_active'][0];
                    else
                        $add_nbki['number_of_active'] = $nbkiParams['number_of_active'];
                }
                if (is_null($add_nbki['number_of_active'])){
                    $add_nbki['number_of_active'] = 0;
                }

                if (isset($nbkiParams['count_of_closed'])) {
                    if (is_array($nbkiParams['count_of_closed'])) 
                        $add_nbki['count_of_closed'] = $nbkiParams['count_of_closed'][0];
                    else
                        $add_nbki['count_of_closed'] = $nbkiParams['count_of_closed'];
                }
                if (is_null($add_nbki['count_of_closed'])){
                    $add_nbki['count_of_closed'] = 0;
                }
                
                if (isset($nbkiParams['count_of_overdue'])) {
                    if (is_array($nbkiParams['count_of_overdue'])) 
                        $add_nbki['count_of_overdue'] = $nbkiParams['count_of_overdue'][0];
                    else
                        $add_nbki['count_of_overdue'] = $nbkiParams['count_of_overdue'];
                }
                if (is_null($add_nbki['count_of_overdue'])){
                    $add_nbki['count_of_overdue'] = 0;
                }

                if (isset($nbkiParams['share_of_overdue_by_active']) && !is_null($nbkiParams['share_of_overdue_by_active'])){
                    if (is_array($nbkiParams['share_of_overdue_by_active'])) 
                        $add_nbki['share_of_overdue_by_active'] = $nbkiParams['share_of_overdue_by_active'][0];
                    else
                        $add_nbki['share_of_overdue_by_active'] = $nbkiParams['share_of_overdue_by_active'];
                }
                if (is_null($add_nbki['share_of_overdue_by_active'])){
                    $add_nbki['share_of_overdue_by_active'] = 0;
                }
                
                if (isset($nbkiParams['extra_scoring']['active_loans_credit_limit_sum'])) 
                    $add_nbki['active_loans_credit_limit_sum'] = $nbkiParams['extra_scoring']['active_loans_credit_limit_sum'];
                if (isset($nbkiParams['extra_scoring']['closed_loans_credit_limit_sum'])) 
                    $add_nbki['closed_loans_credit_limit_sum'] = $nbkiParams['extra_scoring']['closed_loans_credit_limit_sum'];
                if (isset($nbkiParams['extra_scoring']['monthly_active_loans_payment_sum'])) 
                    $add_nbki['monthly_active_loans_payment_sum'] = $nbkiParams['extra_scoring']['monthly_active_loans_payment_sum'];
                if (isset($nbkiParams['extra_scoring']['overdue_amount_sum'])) 
                    $add_nbki['overdue_amount_sum'] = $nbkiParams['extra_scoring']['overdue_amount_sum'];
                if (isset($nbkiParams['extra_scoring']['current_year_max_overdue_amount'])) 
                    $add_nbki['current_year_max_overdue_amount'] = $nbkiParams['extra_scoring']['current_year_max_overdue_amount'];
                if (isset($nbkiParams['extra_scoring']['microloans_over_last_90_days_count'])) 
                    $add_nbki['microloans_over_last_90_days_count'] = $nbkiParams['extra_scoring']['microloans_over_last_90_days_count'];
                if (isset($nbkiParams['extra_scoring']['active_microloan_count'])) 
                    $add_nbki['active_microloan_count'] = $nbkiParams['extra_scoring']['active_microloan_count'];
                
                if (isset($nbkiParams['extra_scoring']['active_pay_day_loans_count'])) 
                    $add_nbki['active_pay_day_loans_count'] = $nbkiParams['extra_scoring']['active_pay_day_loans_count'];
                if (isset($nbkiParams['extra_scoring']['active_pay_day_loans_with_extension_count'])) 
                    $add_nbki['active_pay_day_loans_with_extension_count'] = $nbkiParams['extra_scoring']['active_pay_day_loans_with_extension_count'];
                if (isset($nbkiParams['extra_scoring']['active_credit_lines_count'])) 
                    $add_nbki['active_credit_lines_count'] = $nbkiParams['extra_scoring']['active_credit_lines_count'];
                if (isset($nbkiParams['extra_scoring']['active_microloans_with_wrong_term_days_count'])) 
                    $add_nbki['active_microloans_with_wrong_term_days_count'] = $nbkiParams['extra_scoring']['active_microloans_with_wrong_term_days_count'];

                if (isset($nbkiParams['extra_scoring']['active_credit_cards_count'])) 
                    $add_nbki['active_credit_cards_count'] = $nbkiParams['extra_scoring']['active_credit_cards_count'];
                if (isset($nbkiParams['extra_scoring']['active_other_loans_count'])) 
                    $add_nbki['active_other_loans_count'] = $nbkiParams['extra_scoring']['active_other_loans_count'];
                
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_overdue_count'])) 
                    $add_nbki['pdl_overdue_count'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_overdue_count'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_limit_share'])) 
                    $add_nbki['pdl_npl_limit_share'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_limit_share'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_90_limit_share'])) 
                    $add_nbki['pdl_npl_90_limit_share'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_90_limit_share'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_current_limit_max'])) 
                    $add_nbki['pdl_current_limit_max'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_current_limit_max'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_3m_limit'])) 
                    $add_nbki['pdl_last_3m_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_3m_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_good_max_limit'])) 
                    $add_nbki['pdl_last_good_max_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_good_max_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_good_limit'])) 
                    $add_nbki['pdl_good_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_good_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_prolong_3m_limit'])) 
                    $add_nbki['pdl_prolong_3m_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['pdl_prolong_3m_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['consum_current_limit_max'])) 
                    $add_nbki['consum_current_limit_max'] = $nbkiParams['barents_scoring']['client_scoring_data']['consum_current_limit_max'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['consum_good_limit'])) 
                    $add_nbki['consum_good_limit'] = $nbkiParams['barents_scoring']['client_scoring_data']['consum_good_limit'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['days_from_last_closed'])) 
                    $add_nbki['days_from_last_closed'] = $nbkiParams['barents_scoring']['client_scoring_data']['days_from_last_closed'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_3000_500_paid_count_wo_del'])) 
                    $add_nbki['prev_3000_500_paid_count_wo_del'] = $nbkiParams['barents_scoring']['client_scoring_data']['prev_3000_500_paid_count_wo_del'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_paid_percent_sum'])) 
                    $add_nbki['prev_paid_percent_sum'] = $nbkiParams['barents_scoring']['client_scoring_data']['prev_paid_percent_sum'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_max_delay'])) 
                    $add_nbki['prev_max_delay'] = $nbkiParams['barents_scoring']['client_scoring_data']['prev_max_delay'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['last_credit_delay'])) 
                    $add_nbki['last_credit_delay'] = $nbkiParams['barents_scoring']['client_scoring_data']['last_credit_delay'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['current_overdue_sum'])) 
                    $add_nbki['current_overdue_sum'] = $nbkiParams['barents_scoring']['client_scoring_data']['current_overdue_sum'];
                if (isset($nbkiParams['barents_scoring']['client_scoring_data']['closed_to_total_credits_count_share'])) 
                    $add_nbki['closed_to_total_credits_count_share'] = $nbkiParams['barents_scoring']['client_scoring_data']['closed_to_total_credits_count_share'];

                // ------
                if (is_null($add_nbki['active_loans_credit_limit_sum'])){
                    $add_nbki['active_loans_credit_limit_sum'] = 0;
                }
                if (is_null($add_nbki['closed_loans_credit_limit_sum'])){
                    $add_nbki['closed_loans_credit_limit_sum'] = 0;
                }
                if (is_null($add_nbki['monthly_active_loans_payment_sum'])){
                    $add_nbki['monthly_active_loans_payment_sum'] = 0;
                }
                if (is_null($add_nbki['overdue_amount_sum'])){
                    $add_nbki['overdue_amount_sum'] = 0;
                }
                if (is_null($add_nbki['current_year_max_overdue_amount'])){
                    $add_nbki['current_year_max_overdue_amount'] = 0;
                }
                if (is_null($add_nbki['microloans_over_last_90_days_count'])){
                    $add_nbki['microloans_over_last_90_days_count'] = 0;
                }
                if (is_null($add_nbki['active_microloan_count'])){
                    $add_nbki['active_microloan_count'] = 0;
                }
                if (is_null($add_nbki['active_pay_day_loans_count'])){
                    $add_nbki['active_pay_day_loans_count'] = 0;
                }
                if (is_null($add_nbki['active_loans_credit_limit_sum'])){
                    $add_nbki['active_loans_credit_limit_sum'] = 0;
                }
                if (is_null($add_nbki['active_credit_lines_count'])){
                    $add_nbki['active_credit_lines_count'] = 0;
                }
                if (is_null($add_nbki['active_microloans_with_wrong_term_days_count'])){
                    $add_nbki['active_microloans_with_wrong_term_days_count'] = 0;
                }
                if (is_null($add_nbki['active_credit_cards_count'])){
                    $add_nbki['active_credit_cards_count'] = 0;
                }
                if (is_null($add_nbki['active_other_loans_count'])){
                    $add_nbki['active_other_loans_count'] = 0;
                }
                if (is_null($add_nbki['pdl_overdue_count'])){
                    $add_nbki['pdl_overdue_count'] = 0;
                }
                if (is_null($add_nbki['pdl_npl_limit_share'])){
                    $add_nbki['pdl_npl_limit_share'] = 0;
                }
                if (is_null($add_nbki['pdl_npl_90_limit_share'])){
                    $add_nbki['pdl_npl_90_limit_share'] = 0;
                }
                if (is_null($add_nbki['pdl_current_limit_max'])){
                    $add_nbki['pdl_current_limit_max'] = 0;
                }
                if (is_null($add_nbki['pdl_last_3m_limit'])){
                    $add_nbki['pdl_last_3m_limit'] = 0;
                }
                if (is_null($add_nbki['pdl_last_good_max_limit'])){
                    $add_nbki['pdl_last_good_max_limit'] = 0;
                }
                if (is_null($add_nbki['pdl_good_limit'])){
                    $add_nbki['pdl_good_limit'] = 0;
                }
                if (is_null($add_nbki['pdl_prolong_3m_limit'])){
                    $add_nbki['pdl_prolong_3m_limit'] = 0;
                }
                if (is_null($add_nbki['consum_current_limit_max'])){
                    $add_nbki['consum_current_limit_max'] = 0;
                }
                if (is_null($add_nbki['consum_good_limit'])){
                    $add_nbki['consum_good_limit'] = 0;
                }
                if (is_null($add_nbki['days_from_last_closed'])){
                    $add_nbki['days_from_last_closed'] = 0;
                }
                if (is_null($add_nbki['prev_3000_500_paid_count_wo_del'])){
                    $add_nbki['prev_3000_500_paid_count_wo_del'] = 0;
                }
                if (is_null($add_nbki['prev_paid_percent_sum'])){
                    $add_nbki['prev_paid_percent_sum'] = 0;
                }
                if (is_null($add_nbki['prev_max_delay'])){
                    $add_nbki['prev_max_delay'] = 0;
                }
                if (is_null($add_nbki['last_credit_delay'])){
                    $add_nbki['last_credit_delay'] = 0;
                }
                if (is_null($add_nbki['current_overdue_sum'])){
                    $add_nbki['current_overdue_sum'] = 0;
                }
                if (is_null($add_nbki['closed_to_total_credits_count_share'])){
                    $add_nbki['closed_to_total_credits_count_share'] = 0;
                }
            
            }


            $contracts = $this->contracts->get_contracts(array('user_id' => $order->user_id, 'status' => 3));
                
            $contract_close_date = '';
            $count_contracts_3000_500_0 = 0;
            $all_percents_summ = 0;
            $all_peni_summ = 0;
            $period_peni_biggest = 0;
            $period_peni_last = 0;


            foreach ($contracts as $contract) {
                // if (date('Y-m-d', strtotime($contract->inssuance_date)) > date('Y-m-d', strtotime($from)))
                //     continue;
                // Кол-во дней с даты погашения последнего займа 
                // во внутренней кредитной истории для данного клиента
                if (!is_null($contract->close_date)) {
                    if ($contract_close_date < $contract->close_date) 
                        $contract_close_date = $contract->close_date;
                }
                else{
                    if ($contract_close_date < $contract->close_date) 
                        $contract_close_date = $contract->return_date;
                }

                // Кол-во займов во внутренней кредитной истории для данного клиента, 
                // у которых сумма займа>=3000 руб И сумма погашенных процентов>=500 руб
                // И срок просрочки по займу=0
                if ($contract->amount >= 3000) {
                    $operations = $this->operations->get_operations(array('type' => 'PAY', 'contract_id' => $contract->id));

                    foreach ($operations as $operation) {
                        $contract_loan_percents_summ = 0;

                        $transaction = $this->transactions->get_transaction($operation->transaction_id);
                        $contract_loan_percents_summ += $transaction->loan_percents_summ;
                    }
                    if ($contract_loan_percents_summ > 500) {
                        $contract_count_peni = 0;

                        $operations = $this->operations->get_operations(array('type' => 'PENI', 'contract_id' => $contract->id));
                        foreach ($operations as $operation) {
                            $contract_count_peni++;
                        }
                        if ($contract_count_peni == 0) {
                            $count_contracts_3000_500_0++;
                        }
                    }
                }

                // Сумма погашенных процентов по всем займам 
                // во внутренней кредитной истории для данного клиента
                $operations = $this->operations->get_operations(array('type' => 'PAY', 'contract_id' => $contract->id));

                foreach ($operations as $operation) {
                    $transaction = $this->transactions->get_transaction($operation->transaction_id);
                    $all_percents_summ += $transaction->loan_percents_summ;
                }

                // Максимальный срок просрочки по всем займам 
                // во внутренней кредитной истории для данного клиента
                $operations = $this->operations->get_operations(array('type' => 'PENI', 'contract_id' => $contract->id));
                $prew_date_peni = '';
                $period_peni = 0;
                $period_peni_last = 0;

                foreach ($operations as $operation) {
                    $date1 = new DateTime(date('Y-m-d', strtotime($prew_date_peni)));
                    $date2 = new DateTime(date('Y-m-d', strtotime($operation->created)));

                    $prew_date_peni = $operation->created;
                    $diff = $date2->diff($date1)->days;

                    if ($diff == 1) {
                        $period_peni++;
                        $period_peni_last++;
                        if ($period_peni_biggest < $period_peni) 
                            $period_peni_biggest = $period_peni;
                    }
                    else{
                        $period_peni = 1;
                        $period_peni_last = 1;
                        if ($period_peni_biggest < $period_peni) 
                            $period_peni_biggest = $period_peni;
                    }

                    $transaction = $this->transactions->get_transaction($operation->transaction_id);
                    $all_peni_summ += $transaction->loan_peni_summ;
                }

            }
            
            if ($contract_close_date != '') {
                $date1 = new DateTime(date('Y-m-d', strtotime($contract_close_date)));
                $date2 = new DateTime(date('Y-m-d'));

                $diff = $date2->diff($date1);
                $delay_last_contract = $diff->days;
            }
            else{
                $delay_last_contract = 0;
            }


            // var_dump($all_percents_summ);
            // echo '<hr>';

            $add_nbki['days_last_loan'] = $delay_last_contract;
            $add_nbki['loans_3000_500_0'] = $count_contracts_3000_500_0;
            $add_nbki['loans_percents_payd'] = $all_percents_summ;
            $add_nbki['max_delay_days'] = $period_peni_biggest;
            $add_nbki['last_delay_days'] = $period_peni_last;


            // var_dump($add_nbki);
            // echo '<br>';
            var_dump($order->id);
            echo '<hr>';
            $nbki_extra_scoring_add = $this->NbkiExtraScorings->add($add_nbki);
            // var_dump($nbki_extra_scoring_add);
            // die;

        }

        

        

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