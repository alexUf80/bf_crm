<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(-1);
ini_set('display_errors', 'On');
// error_reporting(0);
// ini_set('display_errors', 'Off');

class TestController1 extends Controller
{
    public function fetch()
    {


        $query = $this->db->placehold("
            SELECT * FROM `s_contracts` WHERE `id` in 
            (SELECT contract_id FROM `s_operations` 
            WHERE `created`>'2024-06-09' and `created`<'2024-06-10' 
            and type='PERCENTS') AND status in (2,4)
        ");
        $this->db->query($query);
        $contracts = $this->db->results();

        foreach ($contracts as $contract) {
            $query = $this->db->placehold("
                SELECT contract_id FROM `s_operations` 
                WHERE `created`>'2024-06-09' and `created`<'2024-06-10' 
                and type='PERCENTS' and contract_id=?
            ",$contract->id);
            $this->db->query($query);
            $operations = $this->db->results();
            
            var_dump($contract->order_id, count($operations));
            
            if (count($operations) == 2) {
                echo '---';
            }
            echo '<hr>';
            
            // die;
        }

        die;

        $query = $this->db->placehold("
        SELECT 
        s.id, s.order_id, o.id as o_id, o.user_id as u_id
        FROM s_scorings s
        RIGHT JOIN s_orders o
        ON s.order_id=o.id AND s.type='nbki'
        WHERE o.id in (
            77964
            #'78338', '78312', '78301', '78300', '77964', '77948', '77886', '77879', '77817', '77781', '77766', '77763', '77710', '77655', '77629', '77521', '77485', '77468', '77398', '77347', '77324', '77291', '77289', '76982', '76942', '76926', '76925', '76917', '76885', '76786', '76717', '76678', '76668', '76662', '76634', '76626', '76563', '76557', '76496', '76472', '76455', '76403', '76391', '76366', '76255', '75856', '75755', '75736', '75660', '75636', '75613', '75576', '75571', '75454', '75427', '75410', '75395', '75380', '75346', '75215', '75208', '75205', '75203', '75202', '75192', '75191', '75188', '75187', '75174', '75169', '75166', '75142', '75140', '75131', '75130', '75121', '74965', '74895', '74834', '74774', '74612', '74448', '74440', '74393', '74382', '74302', '74201', '74026', '73859', '73848', '73781', '73753', '73721', '73689', '73640', '73638', '73368', '73267', '73231', '72987', '72938', '72850', '72750', '72679', '72627', '72573', '72521', '72512', '72504', '72447', '72423', '72400', '72369', '72293', '72161', '72147', '72136', '72104', '72098', '72083', '72080', '72012', '72006', '71926', '71880', '71807', '71791', '71766', '71759', '71753', '71740', '71739', '71727', '71714', '71681', '71658', '71612', '71555', '71542', '71529', '71275', '71274', '71238', '71185', '71145', '71125', '71114', '71103', '71036', '70997', '70941', '70929', '70927', '70913', '70905', '70888', '70866', '70845', '70808', '70760', '70751', '70697', '70695', '70589', '70559', '70502', '70494', '70443', '70432', '70431', '70414', '70371', '70200', '70193', '70061', '70033', '70008', '69988', '69970', '69896', '69839', '69805', '69631', '69627', '69467', '69411', '69408', '69371', '69357', '69342', '69318', '69312', '69308', '69274', '69271', '69265', '69233', '69193', '69179', '69127', '69111', '69011', '69005', '68944', '68933', '68910', '68895', '68889', '68882', '68779', '68742', '68676', '68639', '68613', '68582', '68541', '68354', '68343', '68295', '68278', '68256', '68203', '68102', '68099', '68041', '68012', '67893', '67885', '67877', '67876', '67873', '67861', '67823', '67734', '67732', '67642', '67598', '67594', '67538', '67422', '67402', '67400', '67370', '67350', '67325', '67305', '67298', '67183', '67182', '67177', '67132', '67108', '67056', '67036', '67032', '67007', '66992', '66966', '66951', '66918', '66912', '66897', '66888', '66826', '66811', '66810', '66803', '66742', '66705', '66695', '66694', '66688', '66664', '66618', '66593', '66548', '66536', '66533', '66519', '66201', '66183', '66154', '66146', '66138', '66002', '65992', '65893', '65873', '65831', '65760', '65747', '65730', '65721', '65630', '65616', '65582', '65509', '65486', '65469', '65456', '65431', '65399', '65381', '65374', '65368', '65313', '65229', '65160', '65140', '65130', '65102', '65034', '65024', '64971', '64947', '64886', '64859', '64848', '64844', '64841', '64826'
            )

        ");
        $this->db->query($query);
        $scorings = $this->db->results();

        var_dump(count($scorings));

        foreach ($scorings as $scoring) {

            var_dump($scoring);
            $this->nbki_extra_scorings($scoring->o_id, $scoring->id, $scoring->u_id);
            echo $scoring->o_id.' - '.$scoring->id.'<br>';
        }
        

        

        exit;
    }






    private function nbki_extra_scorings($order_id, $scoring_id, $user_id)
    {
        var_dump($order_id, '!!!!!!!!!!!!!!!!!!!!!!');
        if (is_null($scoring_id)) {
            $query = $this->db->placehold("
            SELECT 
            s.id, s.order_id, o.id as o_id, o.user_id as u_id
            FROM s_scorings s
            RIGHT JOIN s_orders o
            ON s.order_id=o.id AND s.type='nbki'
            WHERE o.user_id in (
                ?
                )
            ORDER BY s.created
            ", $user_id);
            $this->db->query($query);
            $scorings = $this->db->results();

            foreach ($scorings as $scoring) {
                if (!is_null($scoring->order_id)) {
                    // $order_id = $scoring->order_id;
                    $scoring_id = $scoring->id;
                    echo '<br><br>';
                    var_dump($scoring_id, $order_id);
                    echo '<br><br>';
                    break;
                }
            }
        }
        // echo '<hr>---';
        // var_dump($scoring_id);
        // echo '---<hr>';
        if (is_null($scoring_id)) {
            exit;
        }


        $nbkiScor = $this->scorings->get_scoring($scoring_id);

        $nbki_extra_scoring = $this->NbkiExtraScorings->get($order_id);
            
        // if ($nbki_extra_scoring == 0){

            $add_nbki = [];

            $add_nbki['order_id'] = $order_id;

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
                if (!isset($add_nbki['active_loans_credit_limit_sum']) || is_null($add_nbki['active_loans_credit_limit_sum'])){
                    $add_nbki['active_loans_credit_limit_sum'] = 0;
                }
                if (!isset($add_nbki['closed_loans_credit_limit_sum']) || is_null($add_nbki['closed_loans_credit_limit_sum'])){
                    $add_nbki['closed_loans_credit_limit_sum'] = 0;
                }
                if (!isset($add_nbki['monthly_active_loans_payment_sum']) || is_null($add_nbki['monthly_active_loans_payment_sum'])){
                    $add_nbki['monthly_active_loans_payment_sum'] = 0;
                }
                if (!isset($add_nbki['monthly_active_loans_payment_sum']) || is_null($add_nbki['monthly_active_loans_payment_sum'])){
                    $add_nbki['overdue_amount_sum'] = 0;
                }
                if (!isset($add_nbki['current_year_max_overdue_amount']) || is_null($add_nbki['current_year_max_overdue_amount'])){
                    $add_nbki['current_year_max_overdue_amount'] = 0;
                }
                if (!isset($add_nbki['microloans_over_last_90_days_count']) || is_null($add_nbki['microloans_over_last_90_days_count'])){
                    $add_nbki['microloans_over_last_90_days_count'] = 0;
                }
                if (!isset($add_nbki['active_microloan_count']) || is_null($add_nbki['active_microloan_count'])){
                    $add_nbki['active_microloan_count'] = 0;
                }
                if (!isset($add_nbki['active_pay_day_loans_count']) || is_null($add_nbki['active_pay_day_loans_count'])){
                    $add_nbki['active_pay_day_loans_count'] = 0;
                }
                if (!isset($add_nbki['active_loans_credit_limit_sum']) || is_null($add_nbki['active_loans_credit_limit_sum'])){
                    $add_nbki['active_loans_credit_limit_sum'] = 0;
                }
                if (!isset($add_nbki['active_credit_lines_count']) || is_null($add_nbki['active_credit_lines_count'])){
                    $add_nbki['active_credit_lines_count'] = 0;
                }
                if (!isset($add_nbki['active_microloans_with_wrong_term_days_count']) || is_null($add_nbki['active_microloans_with_wrong_term_days_count'])){
                    $add_nbki['active_microloans_with_wrong_term_days_count'] = 0;
                }
                if (!isset($add_nbki['active_credit_cards_count']) || is_null($add_nbki['active_credit_cards_count'])){
                    $add_nbki['active_credit_cards_count'] = 0;
                }
                if (!isset($add_nbki['active_other_loans_count']) || is_null($add_nbki['active_other_loans_count'])){
                    $add_nbki['active_other_loans_count'] = 0;
                }
                if (!isset($add_nbki['pdl_overdue_count']) || is_null($add_nbki['pdl_overdue_count'])){
                    $add_nbki['pdl_overdue_count'] = 0;
                }
                if (!isset($add_nbki['pdl_npl_limit_share']) || is_null($add_nbki['pdl_npl_limit_share'])){
                    $add_nbki['pdl_npl_limit_share'] = 0;
                }
                if (!isset($add_nbki['pdl_npl_90_limit_share']) || is_null($add_nbki['pdl_npl_90_limit_share'])){
                    $add_nbki['pdl_npl_90_limit_share'] = 0;
                }
                if (!isset($add_nbki['pdl_current_limit_max']) || is_null($add_nbki['pdl_current_limit_max'])){
                    $add_nbki['pdl_current_limit_max'] = 0;
                }
                if (!isset($add_nbki['pdl_last_3m_limit']) || is_null($add_nbki['pdl_last_3m_limit'])){
                    $add_nbki['pdl_last_3m_limit'] = 0;
                }
                if (!isset($add_nbki['pdl_last_good_max_limit']) || is_null($add_nbki['pdl_last_good_max_limit'])){
                    $add_nbki['pdl_last_good_max_limit'] = 0;
                }
                if (!isset($add_nbki['pdl_good_limit']) || is_null($add_nbki['pdl_good_limit'])){
                    $add_nbki['pdl_good_limit'] = 0;
                }
                if (!isset($add_nbki['pdl_prolong_3m_limit']) || is_null($add_nbki['pdl_prolong_3m_limit'])){
                    $add_nbki['pdl_prolong_3m_limit'] = 0;
                }
                if (!isset($add_nbki['consum_current_limit_max']) || is_null($add_nbki['consum_current_limit_max'])){
                    $add_nbki['consum_current_limit_max'] = 0;
                }
                if (!isset($add_nbki['consum_good_limit']) || is_null($add_nbki['consum_good_limit'])){
                    $add_nbki['consum_good_limit'] = 0;
                }
                if (!isset($add_nbki['days_from_last_closed']) || is_null($add_nbki['days_from_last_closed'])){
                    $add_nbki['days_from_last_closed'] = 0;
                }
                if (!isset($add_nbki['prev_3000_500_paid_count_wo_del']) || is_null($add_nbki['prev_3000_500_paid_count_wo_del'])){
                    $add_nbki['prev_3000_500_paid_count_wo_del'] = 0;
                }
                if (!isset($add_nbki['prev_paid_percent_sum']) || is_null($add_nbki['prev_paid_percent_sum'])){
                    $add_nbki['prev_paid_percent_sum'] = 0;
                }
                if (!isset($add_nbki['prev_max_delay']) || is_null($add_nbki['prev_max_delay'])){
                    $add_nbki['prev_max_delay'] = 0;
                }
                if (!isset($add_nbki['last_credit_delay']) || is_null($add_nbki['last_credit_delay'])){
                    $add_nbki['last_credit_delay'] = 0;
                }
                if (!isset($add_nbki['current_overdue_sum']) || is_null($add_nbki['current_overdue_sum'])){
                    $add_nbki['current_overdue_sum'] = 0;
                }
                if (!isset($add_nbki['closed_to_total_credits_count_share']) || is_null($add_nbki['closed_to_total_credits_count_share'])){
                    $add_nbki['closed_to_total_credits_count_share'] = 0;
                }

                $scoreball_mfo_2_nbki = null;
                $nbkiScor = ScoringsORM::query()->where('order_id', '=', $nbkiScor->order_id)->where('type', '=', 'nbki')->first();
                if ($nbkiScor) {
                    $nbkiParams = unserialize($nbkiScor->body);
                    if (isset($nbkiParams['score'])){
                        $scoreball_mfo_2_nbki = $nbkiParams['score'];
                    }
                }

                $add_nbki['scoreball_mfo_2_nbki'] = $scoreball_mfo_2_nbki;
            
            }

            $order_scoreballs = $this->NbkiScoreballs->get($nbkiScor->order_id);

            if (empty($order_scoreballs)) {
                $order_scoreballs['ball'] = null;
            }
            else{
                $order_scoreballs->variables = json_decode($order_scoreballs->variables, true);
                $order_scoreballs->variables['ball'] = $order_scoreballs->ball;
                $order_scoreballs = $order_scoreballs->variables;
            }
            $add_nbki['scoreball'] = $order_scoreballs['ball'];

            var_dump($add_nbki['scoreball_mfo_2_nbki']);
            var_dump($add_nbki['scoreball']);


            $contracts = $this->contracts->get_contracts(array('user_id' => $nbkiScor->user_id, 'status' => 3));
                
            $contract_close_date = '';
            $count_contracts_3000_500_0 = 0;
            $all_percents_summ = 0;
            $all_peni_summ = 0;
            $period_peni_biggest = 0;
            $period_peni_last = 0;


            foreach ($contracts as $contract) {
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

            $add_nbki['days_last_loan'] = $delay_last_contract;
            $add_nbki['loans_3000_500_0'] = $count_contracts_3000_500_0;
            $add_nbki['loans_percents_payd'] = $all_percents_summ;
            $add_nbki['max_delay_days'] = $period_peni_biggest;
            $add_nbki['last_delay_days'] = $period_peni_last;

            echo '<hr>';
            echo '<hr>';
            var_dump($add_nbki);
            echo '<hr>';
            var_dump($order_id);
            echo '<hr>';
            $nbki_extra_scoring_add = $this->NbkiExtraScorings->update($order_id, $add_nbki);
            // $nbki_extra_scoring_add = $this->NbkiExtraScorings->add($add_nbki);

        // }
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