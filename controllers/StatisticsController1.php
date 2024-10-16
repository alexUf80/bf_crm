<?php

ini_set('max_execution_time', 100000);

error_reporting(0);

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StatisticsController1 extends Controller
{
    public function fetch()
    {
        switch ($this->request->get('action', 'string')):

            case 'main':
                return $this->action_main();
                break;

            case 'report':
                return $this->action_report();
                break;

            case 'conversion':
                return $this->action_conversion();
                break;

            case 'expired':
                return $this->action_expired();
                break;

            case 'prolongation_contracts':
                return $this->action_prolongation_contracts();
                break;

            case 'free_pk':
                return $this->action_free_pk();
                break;

            case 'scorista_rejects':
                return $this->action_scorista_rejects();
                break;

            case 'contracts':
                return $this->action_contracts();
                break;

            case 'payments':
                return $this->action_payments();
                break;

            case 'eventlogs':
                return $this->action_eventlogs();
                break;

            case 'penalties':
                return $this->action_penalties();
                break;

            case 'dailyreports':
                return $this->action_dailyreports();
                break;

            case 'adservices':
                return $this->action_adservices();
                break;

            case 'sources':
                return $this->action_sources();
                break;

            case 'conversions':
                return $this->action_conversions();
                break;

            case 'orders':
                return $this->action_orders();
                break;

            case 'leadgens':
                return $this->action_leadgens();
                break;

            case 'ip_rejects':
                return $this->action_ip_rejects();
                break;

            case 'requests_contracts':
                return $this->action_requests_contracts();
                break;

            default:
                return false;

        endswitch;

    }

    private function action_main()
    {
        return $this->design->fetch('statistics/main.tpl');
    }

    private function action_report()
    {
        $this->statistics->get_operative_report('2021-05-01', '2021-05-30');

        return $this->design->fetch('statistics/report.tpl');
    }

    private function action_conversion()
    {
        return $this->design->fetch('statistics/conversion.tpl');
    }

    private function action_expired()
    {
        $count_days = 5;
        $this->design->assign('count_days', $count_days);

        $this->db->query("
            SELECT *
            FROM __contracts AS c
            WHERE status IN (2, 4)
            AND DATE(c.return_date) < ?
            ORDER BY c.return_date DESC
        ", date('Y-m-d'));

        $contracts = array();
        $user_ids = array();
        $order_ids = array();
        foreach ($this->db->results() as $c) {
            $user_ids[] = $c->user_id;
            $order_ids[] = $c->order_id;


            $c->expired_period = intval((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime($c->return_date)))) / 86400);

            $contracts[$c->id] = $c;
        }

        $users = array();
        if (!empty($user_ids)) {
            foreach ($this->users->get_users(array('id' => $user_ids, 'limit' => 10000)) as $user) {
                $user_age = date_diff(date_create(date('Y-m-d', strtotime($user->birth))), date_create(date('Y-m-d')));
                $user->age = $user_age->y;
                $user->Regcode = $this->helpers->get_region_code($user->Regregion);

                $users[$user->id] = $user;
            }
        }

        $orders = array();
        if (!empty($order_ids)) {
            foreach ($this->orders->get_orders(array('id' => $order_ids, 'limit' => 10000)) as $order)
                $orders[$order->order_id] = $order;
        }

        $contract_payments = array();
        if ($operations = $this->operations->get_operations(array('type' => 'PAY', 'contract_id' => array_keys($contracts), 'date_from' => date('Y-m-01')))) {
            foreach ($operations as $op) {
                if (!isset($contract_payments[$op->contract_id]))
                    $contract_payments[$op->contract_id] = array();
                $contract_payments[$op->contract_id][] = $op;
            }
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract_payments);echo '</pre><hr />';
//exit;

        foreach ($contracts as $contract) {
            if (isset($users[$contract->user_id])) {
                $contract->user = $users[$contract->user_id];

                $contract->user->regAddr = AdressesORM::find($contract->user->regaddress_id);
                $contract->user->faktAddr = AdressesORM::find($contract->user->faktaddress_id);

                $this->design->assign('regAddr', $contract->user->regAddr->adressfull);
                $this->design->assign('faktAddr', $contract->user->faktAddr->adressfull);
            }
            if (isset($orders[$contract->order_id]))
                $contract->order = $orders[$contract->order_id];

            if ($contract->order->client_status == 'nk')
                $contract->client_status = 'НК';
            elseif ($contract->order->client_status == 'pk')
                $contract->client_status = 'ПК';
            elseif ($contract->order->client_status == 'rep')
                $contract->client_status = 'НК';
            else
                $contract->client_status = 'н/д';

            $contract->payment_last_month = 0;
            if (isset($contract_payments[$contract->id])) {
                $contract->contract_payments = $contract_payments[$contract->id];
                foreach ($contract_payments[$contract->id] as $contract_payment)
                    $contract->payment_last_month += $contract_payment->amount;
            }

            $this->db->query("
            SELECT created,
            amount
            FROM s_operations
            WHERE order_id = ?
            AND `type` = 'PAY'
            ORDER BY created DESC 
            LIMIT 1
            ", $contract->order_id);

            $contract->last_operation = $this->db->result();
        }

        $this->design->assign('contracts', $contracts);

        if ($this->request->get('download') == 'excel') {
            $filename = 'files/reports/expired.xls';
            require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

            $excel = new PHPExcel();

            $excel->setActiveSheetIndex(0);
            $active_sheet = $excel->getActiveSheet();

            $active_sheet->setTitle('Просроченные займы ');

            $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $active_sheet->getColumnDimension('A')->setWidth(15);
            $active_sheet->getColumnDimension('B')->setWidth(20);
            $active_sheet->getColumnDimension('C')->setWidth(20);
            $active_sheet->getColumnDimension('D')->setWidth(20);
            $active_sheet->getColumnDimension('E')->setWidth(20);
            $active_sheet->getColumnDimension('F')->setWidth(20);
            $active_sheet->getColumnDimension('G')->setWidth(10);
            $active_sheet->getColumnDimension('H')->setWidth(20);
            $active_sheet->getColumnDimension('I')->setWidth(15);
            $active_sheet->getColumnDimension('J')->setWidth(15);
            $active_sheet->getColumnDimension('K')->setWidth(15);
            $active_sheet->getColumnDimension('L')->setWidth(15);
            $active_sheet->getColumnDimension('M')->setWidth(15);
            $active_sheet->getColumnDimension('N')->setWidth(15);
            $active_sheet->getColumnDimension('O')->setWidth(15);
            $active_sheet->getColumnDimension('P')->setWidth(15);
            $active_sheet->getColumnDimension('Q')->setWidth(15);
            $active_sheet->getColumnDimension('R')->setWidth(15);
            $active_sheet->getColumnDimension('S')->setWidth(15);
            $active_sheet->getColumnDimension('T')->setWidth(15);
            $active_sheet->getColumnDimension('U')->setWidth(15);
            $active_sheet->getColumnDimension('V')->setWidth(15);
            $active_sheet->getColumnDimension('W')->setWidth(15);
            $active_sheet->getColumnDimension('X')->setWidth(15);
            $active_sheet->getColumnDimension('Y')->setWidth(15);
            $active_sheet->getColumnDimension('Z')->setWidth(15);
            $active_sheet->getColumnDimension('AA')->setWidth(15);
            $active_sheet->getColumnDimension('AB')->setWidth(15);
            $active_sheet->getColumnDimension('AС')->setWidth(15);
            $active_sheet->getColumnDimension('AD')->setWidth(15);
            $active_sheet->getColumnDimension('AE')->setWidth(15);
            $active_sheet->getColumnDimension('AG')->setWidth(15);

            $active_sheet->setCellValue('A1', 'Отказ от взаимодействия');
            $active_sheet->setCellValue('B1', 'ID договора');
            $active_sheet->setCellValue('C1', 'Дата выдачи');
            $active_sheet->setCellValue('D1', 'ФИО');
            $active_sheet->setCellValue('E1', 'Телефон');
            $active_sheet->setCellValue('F1', 'Регион');//---
            $active_sheet->setCellValue('G1', 'Город');
            $active_sheet->setCellValue('H1', 'Адрес регистрации');//---
            $active_sheet->setCellValue('I1', 'Адрес фактического местонахождения');//---
            $active_sheet->setCellValue('J1', 'e-mail');//---
            $active_sheet->setCellValue('K1', 'Сумма займа');//---
            $active_sheet->setCellValue('L1', 'Дата платежа');//---
            $active_sheet->setCellValue('M1', 'Срок');//---
            $active_sheet->setCellValue('N1', 'Срок просрочки (дни)');//---
            $active_sheet->setCellValue('O1', 'Остаток ОД');//---
            $active_sheet->setCellValue('P1', 'Начисленные проценты');//---
            $active_sheet->setCellValue('Q1', 'К погашению');//---
            $active_sheet->setCellValue('R1', 'Наличие погашений');//---
            $active_sheet->setCellValue('S1', 'Возраст');//---
            $active_sheet->setCellValue('T1', 'День рождения');//---
            $active_sheet->setCellValue('U1', 'Оплата в текущем месяце');//---
            $active_sheet->setCellValue('V1', 'Новый или повторный');//---
            $active_sheet->setCellValue('W1', 'Номер региона');//---
            $active_sheet->setCellValue('X1', 'Контактное лицо ФИО');//---
            $active_sheet->setCellValue('Y1', 'Телефон');//---
            $active_sheet->setCellValue('Z1', 'Работодатель');//---
            $active_sheet->setCellValue('AA1', 'Адрес работодателя');//---
            $active_sheet->setCellValue('AB1', 'Телефон работодателя');//---
            $active_sheet->setCellValue('AC1', 'Дата последнего платежа');//---
            $active_sheet->setCellValue('AD1', 'Сумма платеж');//---
            $active_sheet->setCellValue('AE1', 'Номер заявки');//---
            $active_sheet->setCellValue('AF1', 'Указанный клиентом доход');//---
            $active_sheet->setCellValue('AG1', 'ИНН');//---

            $i = 2;
            foreach ($contracts as $contract) {
                $active_sheet->setCellValue('A' . $i, '');
                $active_sheet->setCellValue('B' . $i, $contract->number);
                $active_sheet->setCellValue('C' . $i, $contract->inssuance_date);
                $active_sheet->setCellValue('D' . $i, $contract->user->lastname . ' ' . $contract->user->firstname . ' ' . $contract->user->patronymic);
                $active_sheet->setCellValue('E' . $i, $contract->user->phone_mobile);
                $active_sheet->setCellValue('F' . $i, $contract->user->Regregion);
                $active_sheet->setCellValue('G' . $i, $contract->user->Regcity);
                $active_sheet->setCellValue('H' . $i, $contract->user->regAddr->adressfull);
                $active_sheet->setCellValue('I' . $i, $contract->user->faktAddr->adressfull);
                $active_sheet->setCellValue('J' . $i, $contract->user->email);
                $active_sheet->setCellValue('K' . $i, $contract->amount);//---
                $active_sheet->setCellValue('L' . $i, date('d.m.Y', strtotime($contract->return_date)));//---
                $active_sheet->setCellValue('M' . $i, $contract->period);//---
                $active_sheet->setCellValue('N' . $i, $contract->expired_period);//---
                $active_sheet->setCellValue('O' . $i, $contract->loan_body_summ);//---
                $active_sheet->setCellValue('P' . $i, $contract->loan_percents_summ);//---
                $active_sheet->setCellValue('Q' . $i, $contract->loan_body_summ + $contract->loan_percents_summ);//---
                $active_sheet->setCellValue('R' . $i, $contract->allready_paid);//---Наличие погашений
                $active_sheet->setCellValue('S' . $i, $contract->user->age);//---
                $active_sheet->setCellValue('T' . $i, $contract->user->birth);//---
                $active_sheet->setCellValue('U' . $i, $contract->payment_last_month);//---Оплата в текущем месяце
                $active_sheet->setCellValue('V' . $i, $contract->client_status);//--
                $active_sheet->setCellValue('W' . $i, $contract->user->Regcode);//---
                $active_sheet->setCellValue('X' . $i, $contract->user->contact_person_name);//---
                $active_sheet->setCellValue('Y' . $i, $contract->user->contact_person_phone);//---
                $active_sheet->setCellValue('Z' . $i, $contract->user->workplace);//---
                $active_sheet->setCellValue('AA' . $i, $contract->user->workaddress);//---
                $active_sheet->setCellValue('AB' . $i, $contract->user->workphone);//---
                if (!empty($contract->last_operation)) {
                    $active_sheet->setCellValue('AC' . $i, $contract->last_operation->created);//---
                    $active_sheet->setCellValue('AD' . $i, $contract->last_operation->amount);//---
                } else {
                    $active_sheet->setCellValue('AC' . $i, 'Оплат не поступало');//---
                }
                $active_sheet->setCellValue('AE' . $i, $contract->order_id);//---
                $active_sheet->setCellValue('AF' . $i, $contract->user->income);//---
                $active_sheet->setCellValue('AG' . $i, $contract->user->inn);//---


                $i++;
            }

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

            $objWriter->save($this->config->root_dir . $filename);

            header('Location:' . $this->config->root_url . '/' . $filename);
            exit;
        }

        return $this->design->fetch('statistics/expired.tpl');
    }

    private function action_prolongation_contracts()
    {
        $count_days = 5;
        $this->design->assign('count_days', $count_days);

        $this->db->query("
            SELECT *
            FROM __contracts AS c
            WHERE status IN (2, 4)
            AND DATE(c.return_date) <= ?
            AND DATE(c.return_date) >= ?
            ORDER BY c.return_date ASC
        ", date('Y-m-d', time() + $count_days * 86400), date('Y-m-d'));

        $contracts = array();
        $user_ids = array();
        $order_ids = array();
        foreach ($this->db->results() as $c) {
            $user_ids[] = $c->user_id;
            $order_ids[] = $c->order_id;

            $contracts[$c->id] = $c;
        }

        $users = array();
        if (!empty($user_ids)) {
            foreach ($this->users->get_users(array('id' => $user_ids, 'limit' => 10000)) as $user)
                $users[$user->id] = $user;
        }

        $orders = array();
        if (!empty($order_ids)) {
            foreach ($this->orders->get_orders(array('id' => $order_ids, 'limit' => 10000)) as $order)
                $orders[$order->order_id] = $order;
        }

        foreach ($contracts as $contract) {
            if (isset($users[$contract->user_id]))
                $contract->user = $users[$contract->user_id];
            if (isset($orders[$contract->order_id]))
                $contract->order = $orders[$contract->order_id];


//            $contract->prolongation_summ;
//            $contract->close_summ ;
        }

        $this->design->assign('contracts', $contracts);

        if ($this->request->get('download') == 'excel') {
            $filename = 'files/reports/prolongation_contracts.xls';
            require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

            $excel = new PHPExcel();

            $excel->setActiveSheetIndex(0);
            $active_sheet = $excel->getActiveSheet();

            $active_sheet->setTitle('К оплате в ближайшие ' . $count_days . ' дней');

            $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
            $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $active_sheet->getColumnDimension('A')->setWidth(15);
            $active_sheet->getColumnDimension('B')->setWidth(20);
            $active_sheet->getColumnDimension('C')->setWidth(20);
            $active_sheet->getColumnDimension('D')->setWidth(20);
            $active_sheet->getColumnDimension('E')->setWidth(20);
            $active_sheet->getColumnDimension('F')->setWidth(20);
            $active_sheet->getColumnDimension('G')->setWidth(10);
            $active_sheet->getColumnDimension('H')->setWidth(20);
            $active_sheet->getColumnDimension('I')->setWidth(15);
            $active_sheet->getColumnDimension('J')->setWidth(15);


            $active_sheet->setCellValue('A1', 'Дата платежа');
            $active_sheet->setCellValue('B1', 'Фамилия');
            $active_sheet->setCellValue('C1', 'Имя');
            $active_sheet->setCellValue('D1', 'Отчество');
            $active_sheet->setCellValue('E1', 'Номер телефона');
            $active_sheet->setCellValue('F1', 'Город');//---
            $active_sheet->setCellValue('G1', 'Всего продлений сделано');
            $active_sheet->setCellValue('H1', 'ID договора');//---
            $active_sheet->setCellValue('I1', 'Сумма к погашению');//---
            $active_sheet->setCellValue('J1', 'Сумма к продлению');//---

            $i = 2;
            foreach ($contracts as $contract) {
                $active_sheet->setCellValue('A' . $i, date('d.m.Y', strtotime($contract->return_date)));
                $active_sheet->setCellValue('B' . $i, $contract->user->lastname);
                $active_sheet->setCellValue('C' . $i, $contract->user->firstname);
                $active_sheet->setCellValue('D' . $i, $contract->user->patronymic);
                $active_sheet->setCellValue('E' . $i, $contract->user->phone_mobile);
                $active_sheet->setCellValue('F' . $i, $contract->user->Regregion);
                $active_sheet->setCellValue('G' . $i, $contract->prolongation);
                $active_sheet->setCellValue('H' . $i, $contract->number);
                $active_sheet->setCellValue('I' . $i, $contract->loan_body_summ + $contract->loan_percents_summ);
                $active_sheet->setCellValue('J' . $i, $contract->loan_percents_summ + $this->settings->prolongation_amount);


                $i++;
            }

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

            $objWriter->save($this->config->root_dir . $filename);

            header('Location:' . $this->config->root_url . '/' . $filename);
            exit;
        }

        return $this->design->fetch('statistics/prolongation_contracts.tpl');
    }

    private function action_free_pk()
    {
        return $this->design->fetch('statistics/free_pk.tpl');
    }

    public function type_pk_order1($c)
    {
    	$query = $this->db->placehold("
        SELECT * 
        FROM __contracts 
        WHERE user_id = ? 
        AND create_date < ?
        AND status = 3
        ORDER BY id ASC
        LIMIT 3", 
        $c->user_id, $c->date);

        $this->db->query($query);
        $prev_contracts =  $this->db->results();

        $c_type_pk = NULL;
        if(count($prev_contracts)>0){

            $prev_contract_type_pk = [0, 0, 0];
            $i = 0;
            $prolo = 0;

            
            foreach ($prev_contracts as $prev_contract) {
                $date1 = new DateTime(date('Y-m-d', strtotime($prev_contract->close_date)));
                $date2 = new DateTime(date('Y-m-d', strtotime($prev_contract->return_date)));
                $diff = $date2->diff($date1);

                if ($prev_contract->close_date < $prev_contract->return_date || $diff->days < 5) {
                    $prev_contract_type_pk[$i] = 1;
                }

                // Если начислялись пени более 5 раз
                $contracts_peni = count($this->operations->get_operations((array('contract_id'=>$prev_contract->id, 'type'=>'PENI'))));
                if ($contracts_peni >= 5) {
                    $prev_contract_type_pk[$i] = 0;
                }
                else{
                    $contracts_payments = $this->operations->get_operations((array('contract_id'=>$prev_contract->id, 'type'=>'PAY')));
                    
                    foreach ($contracts_payments as $contracts_payment) {
                        $transaction = $this->transactions->get_transaction($contracts_payment->transaction_id);
                        if (!is_null($transaction)) {
                            $prolo += $transaction->prolongation;
                        }
                    }
                }
                $i++;
            }

            if ($prev_contract_type_pk[0] == 0) 
                $c_type_pk = 0;
            elseif($prev_contract_type_pk[1] == 0)
                $c_type_pk = 1;
            elseif($prev_contract_type_pk[2] == 0)
                $c_type_pk = 2;
            else
                $c_type_pk = 3;

            $c_type_pk .= " - ".implode(",", $prev_contract_type_pk);
            // $c_type_pk .= ' --- '.$prolo;
            if ($prolo > 0 && $c_type_pk < 3) {
                $c_type_pk++;
            }

        }

        return $c_type_pk;
    }
    
    private function action_scorista_rejects()
    {
        $reasons = array();
        foreach ($this->reasons->get_reasons() as $reason)
            $reasons[$reason->id] = $reason;
        $this->design->assign('reasons', $reasons);


        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            $query_reason = '';
            if ($filter_reason = $this->request->get('reason_id')) {
                if ($filter_reason != 'all') {
                    $query_reason = $this->db->placehold("AND o.reason_id = ?", (int)$filter_reason);
                }

                $this->design->assign('filter_reason', $filter_reason);
            }

            $query = $this->db->placehold("
                SELECT
                    o.id AS order_id,
                    o.date,
                    o.reason_id,
                    o.reject_reason,
                    o.user_id,
                    o.manager_id,
                    o.utm_source,
                    o.client_status,
                    u.lastname,
                    u.firstname,
                    u.patronymic,
                    u.phone_mobile,
                    u.email,
                    u.regaddress_id,
                    u.faktaddress_id,
                    u.birth,
                    u.inn,
                    u.passport_serial,
                    ts.operation, 
                    ts.checked,
                    o.promocode_id
                FROM __orders AS o
                LEFT JOIN __users AS u ON u.id = o.user_id
                LEFT JOIN s_transactions AS ts ON u.id = ts.user_id
                WHERE o.status IN (3, 8)
                $query_reason
                AND DATE(o.date) >= ?
                AND DATE(o.date) <= ?
                AND `description` = 'Привязка карты'
                AND reason_code = 1
                GROUP BY order_id
            ", $date_from, $date_to);
            $this->db->query($query);

            $orders = array();
            foreach ($this->db->results() as $o){
                $orders[$o->order_id] = $o;
                $promocode = $this->promocodes->get($o->promocode_id);
                $orders[$o->order_id]->promocode = $promocode->code;
            }

            foreach ($orders as $order) {
                $faktaddress = $this->Addresses->get_address($order->faktaddress_id);
                $order->region = $faktaddress->region;
                $order->zone = $faktaddress->zone;
                if($order->reason_id == 36){
                    $scorings = $this->scorings->get_scorings(array('order_id' => $order->order_id, 'type' => 'fssp'))[0];
                    $body = unserialize($scorings->body)['expSum'];
                    $order->fssp_summ = $body;
                }

                // $order->type_pk = $this->contracts->type_pk_contract($order->contract_id);
                $order->type_pk = $this->contracts->type_pk_order($order);

                $order_scorings = $this->scorings->get_scorings(array('order_id' => $order->order_id, 'type' => 'nbki'));
                foreach ($order_scorings as $order_scoring) {
                    $order_scoring_body = unserialize($order_scoring->body);
                    $order->score = $order_scoring_body['score'];
                    $order->number_of_active = $order_scoring_body['number_of_active'];
                    $order->overdue_amount_sum = $order_scoring_body['extra_scoring']['overdue_amount_sum'];
                    $order->active_loans_credit_limit_sum = $order_scoring_body['extra_scoring']['active_loans_credit_limit_sum'];
                    $order->monthly_active_loans_payment_sum = $order_scoring_body['extra_scoring']['monthly_active_loans_payment_sum'];
                }
            }

            if (!empty($orders))
                if ($scorings = $this->scorings->get_scorings(array('order_id' => array_keys($orders), 'type' => 'scorista')))
                    foreach ($scorings as $scoring)
                        $orders[$scoring->order_id]->scoring = $scoring;


            switch ($this->request->get('scoring')):

                case '499-':
                    foreach ($orders as $key => $order)
                        if (empty($order->scoring->scorista_ball) || $order->scoring->scorista_ball > 499)
                            unset($orders[$key]);
                    break;

                case '500-549':
                    foreach ($orders as $key => $order)
                        if (empty($order->scoring->scorista_ball) || $order->scoring->scorista_ball < 500 || $order->scoring->scorista_ball > 549)
                            unset($orders[$key]);
                    break;

                case '550+':
                    foreach ($orders as $key => $order)
                        if (empty($order->scoring->scorista_ball) || $order->scoring->scorista_ball < 550)
                            unset($orders[$key]);
                    break;

            endswitch;
            $this->design->assign('filter_scoring', $this->request->get('scoring'));


            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m)
                    $managers[$m->id] = $m;

                $filename = 'files/reports/orders.xls';
                require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Выдачи " . $from . "-" . $to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(45);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(15);
                $active_sheet->getColumnDimension('G')->setWidth(10);
                $active_sheet->getColumnDimension('H')->setWidth(10);
                $active_sheet->getColumnDimension('I')->setWidth(15);
                $active_sheet->getColumnDimension('J')->setWidth(10);
                $active_sheet->getColumnDimension('K')->setWidth(20);
                $active_sheet->getColumnDimension('L')->setWidth(20);
                $active_sheet->getColumnDimension('M')->setWidth(20);
                $active_sheet->getColumnDimension('N')->setWidth(20);
                $active_sheet->getColumnDimension('O')->setWidth(30);
                $active_sheet->getColumnDimension('P')->setWidth(15);
                $active_sheet->getColumnDimension('Q')->setWidth(15);
                $active_sheet->getColumnDimension('R')->setWidth(15);
                $active_sheet->getColumnDimension('S')->setWidth(15);

                $active_sheet->setCellValue('A1', 'Дата');
                $active_sheet->setCellValue('B1', 'Заявка');
                $active_sheet->setCellValue('C1', 'ФИО');
                $active_sheet->setCellValue('D1', 'Адрес регистрации');
                $active_sheet->setCellValue('E1', 'Адрес проживания');
                $active_sheet->setCellValue('F1', 'Телефон');
                $active_sheet->setCellValue('G1', 'Email');
                $active_sheet->setCellValue('H1', 'Дата рождения');
                $active_sheet->setCellValue('I1', 'ИНН');
                $active_sheet->setCellValue('J1', 'Паспорт');
                $active_sheet->setCellValue('K1', 'Регион выдачи');
                $active_sheet->setCellValue('L1', 'Зона качества');
                $active_sheet->setCellValue('M1', 'ПК/НК');
                $active_sheet->setCellValue('N1', 'Тип ПК');
                $active_sheet->setCellValue('O1', 'Менеджер');//---
                $active_sheet->setCellValue('P1', 'Причина');
                $active_sheet->setCellValue('Q1', 'Источник');//---
                $active_sheet->setCellValue('R1', 'Операция');//---
                $active_sheet->setCellValue('S1', 'Промокод');//---

                $i = 2;
                foreach ($orders as $contract) {

                    if ($contract->client_status == 'pk')
                        $client_status = 'ПК';
                    elseif ($contract->client_status == 'nk')
                        $client_status = 'НК';
                    elseif ($contract->client_status == 'crm')
                        $client_status = 'ПК CRM';
                    elseif ($contract->client_status == 'rep')
                        $client_status = 'НК';
                    else
                        $client_status = '';

                    $successTransaction = empty($contract->checked) ? ' (провал)' : ' успех';

                    $active_sheet->setCellValue('A' . $i, date('d.m.Y', strtotime($contract->date)));
                    $active_sheet->setCellValue('B' . $i, $contract->order_id);
                    $active_sheet->setCellValue('C' . $i, $contract->lastname . ' ' . $contract->firstname . ' ' . $contract->patronymic);

                    $regaddress = $this->addresses->get_address($contract->regaddress_id)->adressfull;
                    $active_sheet->setCellValue('D' . $i, $regaddress);

                    $faktaddress = $this->addresses->get_address($contract->faktaddress_id)->adressfull;
                    $active_sheet->setCellValue('E' . $i, $faktaddress);
                    $active_sheet->setCellValue('F' . $i, $contract->phone_mobile);
                    $active_sheet->setCellValue('G' . $i, $contract->email);
                    $active_sheet->setCellValue('H' . $i, $contract->birth);
                    $active_sheet->setCellValue('I' . $i, $contract->inn);
                    $active_sheet->setCellValue('J' . $i, $contract->passport_serial);
                    $active_sheet->setCellValue('K' . $i, $contract->region);
                    $active_sheet->setCellValue('L' . $i, $contract->zone);
                    $active_sheet->setCellValue('M' . $i, $client_status);
                    $active_sheet->setCellValue('N' . $i, $contract->type_pk);

                    $active_sheet->setCellValue('O' . $i, $managers[$contract->manager_id]->name);
                    $active_sheet->setCellValue('P' . $i, ($contract->reason_id ? $reasons[$contract->reason_id]->admin_name : $contract->reject_reason));
                    $active_sheet->setCellValue('Q' . $i, $contract->utm_source);
                    $active_sheet->setCellValue('R' . $i, $contract->operation . $successTransaction);
                    $active_sheet->setCellValue('S' . $i, $contract->promocode);

                    if ($contract->utm_source == 'kpk' || $contract->utm_source == 'part1') {
                        $active_sheet->getStyle('A'.$i.':S'.$i)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'e1e1e1')
                                    )
                                )
                            );
                    }

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir . $filename);

                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }


            $this->design->assign('orders', $orders);
        }

        return $this->design->fetch('statistics/scorista_rejects.tpl');
    }

    private function action_contracts()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            $nbki = $this->request->get('nbki');

            $query = $this->db->placehold("
                SELECT
                    c.id AS contract_id,
                    c.order_id AS order_id,
                    c.number,
                    c.inssuance_date AS date,
                    c.amount,
                    c.user_id,
                    c.status,
                    c.collection_status,
                    c.sold,
                    c.return_date,
                    c.close_date,
                    c.create_date,
                    c.prolongation,
                    o.client_status,
                    o.date AS order_date,
                    o.manager_id,
                    o.period,
                    o.utm_source,
                    u.lastname,
                    u.firstname,
                    u.patronymic,
                    u.phone_mobile,
                    u.email,
                    u.birth,
                    u.pdn,
                    u.UID AS uid,
                    u.regaddress_id,
                    u.faktaddress_id,
                    u.workplace,
                    u.profession,
                    o.promocode_id,
                    o.id as order_id
                FROM __contracts AS c
                LEFT JOIN __users AS u
                ON u.id = c.user_id
                LEFT JOIN __orders AS o
                ON o.id = c.order_id
                WHERE c.status IN (2, 3, 4, 7)
                AND c.type = 'base'
                AND DATE(c.inssuance_date) >= ?
                AND DATE(c.inssuance_date) <= ?
                ORDER BY inssuance_date
            ", $date_from, $date_to);
            $this->db->query($query);

            $contracts = array();
            foreach ($this->db->results() as $c){
                $contracts[$c->contract_id] = $c;

                $contracts[$c->contract_id]->promocode = '';
                if($c->promocode_id != '0'){
                    $query = $this->db->placehold("
                    SELECT * 
                    FROM __promocodes
                    where id = ?
                    ", $c->promocode_id);
                    $this->db->query($query);
                    $promocode =  $this->db->result();
                    $contracts[$c->contract_id]->promocode = $promocode->code;
                }

                $regAddress = AdressesORM::find($c->regaddress_id);
                $c->RegAddr = $regAddress->adressfull;
                $faktAddress = AdressesORM::find($c->faktaddress_id);
                $c->FaktAddr = $faktAddress->adressfull;

                $c->type_pk = $this->contracts->type_pk_contract($c);

                // сумма выдачи
                $p2p_amount = 0;
                $contract_operations = $this->operations->get_operations(array('contract_id' => $c->contract_id, 'type' => 'P2P'));
                foreach ($contract_operations as $contract_operation) {
                    $p2p_amount += $contract_operation->amount;
                }
                $c->p2p_amount = $p2p_amount;
                
                // сумма пролонгаций
                $prolongations_amount = 0;
                $contract_operations = $this->operations->get_operations(array('contract_id' => $c->contract_id, 'type' => 'PAY'));
                foreach ($contract_operations as $contract_operation) {
                    $contract_operation_transaction = $this->transactions->get_transaction($contract_operation->transaction_id);
                    if ($contract_operation_transaction->prolongation == 1) {
                        $prolongations_amount += $contract_operation->amount;
                    }
                }
                $c->prolongations_amount = $prolongations_amount;

                // скорбалл МФО2НБКИ и скорбалл МАНИМАЭН
                $order_scorings = $this->scorings->get_scorings(array('order_id' => $c->order_id, 'type' => 'nbki'));
                foreach ($order_scorings as $order_scoring) {
                    $order_scoring_body = unserialize($order_scoring->body);

                    if($c->client_status == 'nk'){
                        $c->maniman = $order_scoring_body['barents_scoring']['new_client_result'];
                    }
                    else{
                        $c->maniman = $order_scoring_body['barents_scoring']['old_client_result'];
                        
                    }
                    $c->score_mf0_2_nbki = $order_scoring_body['score'];
                }

                // Зона качества
                $faktaddress = $this->Addresses->get_address($c->faktaddress_id);
                $c->region = $faktaddress->region;
                $c->zone = $faktaddress->zone;
            }

            foreach ($contracts as $c) {
                if (empty($c->client_status)) {
                    $client_contracts = $this->contracts->get_contracts(array(
                        'user_id' => $c->user_id,
                        'status' => 3,
                        'close_date_to' => $c->date
                    ));
                    if (!empty($client_contracts)) {
                        $this->orders->update_order($c->order_id, array('client_status' => 'crm'));
                    } else {
                        if (empty($have_close_loans)) {
                            $have_old_orders = 0;
                            $orders = $this->orders->get_orders(array('user_id' => $c->user_id, 'date_to' => $c->date));
                            foreach ($orders as $order) {
                                if ($order->order_id != $c->order_id) {
                                    $have_old_orders = 1;
                                }
                            }

                            if (empty($have_old_orders)) {
                                $this->orders->update_order($c->order_id, array('client_status' => 'nk'));
                            } else {
                                $this->orders->update_order($c->order_id, array('client_status' => 'rep'));
                            }
                        }
                    }
                }
                $c->sumPayed = OperationsORM::where('order_id', $c->order_id)->where('type', 'PAY')->sum('amount');
                
                // количество пролонгаций
                $operations = OperationsORM::query()
                ->where('contract_id', '=', $c->contract_id)
                ->where('type', '=', 'PAY')->get();

                $count_prolongation = 0;
                foreach ($operations as $operation) {
                    if ($operation->transaction_id) {
                        $transaction = $this->transactions->get_transaction($operation->transaction_id);
                        // $transaction = TransactionsORM::query()->where('id', '=', $operation->transaction_id)->first();
                        if ($transaction && $transaction->prolongation) {
                            $count_prolongation++;
                        }
                    }
                }
                $c->count_prolongation = $count_prolongation;
            }

            $statuses = $this->contracts->get_statuses();
            $this->design->assign('statuses', $statuses);

            $collection_statuses = $this->contracts->get_collection_statuses();
            $this->design->assign('collection_statuses', $collection_statuses);


            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m)
                    $managers[$m->id] = $m;

                $filename = 'files/reports/contracts.xls';
                require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Выдачи " . $from . "-" . $to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(45);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(45);
                $active_sheet->getColumnDimension('G')->setWidth(45);
                $active_sheet->getColumnDimension('H')->setWidth(45);
                $active_sheet->getColumnDimension('I')->setWidth(20);
                $active_sheet->getColumnDimension('J')->setWidth(10);
                $active_sheet->getColumnDimension('K')->setWidth(10);
                $active_sheet->getColumnDimension('L')->setWidth(10);
                $active_sheet->getColumnDimension('M')->setWidth(30);
                $active_sheet->getColumnDimension('N')->setWidth(10);
                $active_sheet->getColumnDimension('O')->setWidth(10);
                $active_sheet->getColumnDimension('P')->setWidth(10);
                $active_sheet->getColumnDimension('Q')->setWidth(10);
                $active_sheet->getColumnDimension('R')->setWidth(10);
                $active_sheet->getColumnDimension('S')->setWidth(10);
                $active_sheet->getColumnDimension('T')->setWidth(10);
                $active_sheet->getColumnDimension('U')->setWidth(10);
                $active_sheet->getColumnDimension('V')->setWidth(10);
                $active_sheet->getColumnDimension('W')->setWidth(10);
                $active_sheet->getColumnDimension('X')->setWidth(10);
                $active_sheet->getColumnDimension('Y')->setWidth(10);
                $active_sheet->getColumnDimension('Z')->setWidth(10);
                $active_sheet->getColumnDimension('AA')->setWidth(10);
                $active_sheet->getColumnDimension('AB')->setWidth(10);
                $active_sheet->getColumnDimension('AC')->setWidth(10);
                $active_sheet->getColumnDimension('AD')->setWidth(10);
                if ($nbki == 1) {
                    $active_sheet->getColumnDimension('AE')->setWidth(10);
                    $active_sheet->getColumnDimension('AF')->setWidth(10);
                    $active_sheet->getColumnDimension('AG')->setWidth(10);
                    $active_sheet->getColumnDimension('AH')->setWidth(10);
                    $active_sheet->getColumnDimension('AI')->setWidth(10);
                    $active_sheet->getColumnDimension('AJ')->setWidth(10);
                    $active_sheet->getColumnDimension('AK')->setWidth(10);
                    $active_sheet->getColumnDimension('AL')->setWidth(10);
                    $active_sheet->getColumnDimension('AM')->setWidth(10);
                }
                $active_sheet->getColumnDimension('AN')->setWidth(10);
                $active_sheet->getColumnDimension('AO')->setWidth(10);
                $active_sheet->getColumnDimension('AP')->setWidth(10);

                $active_sheet->setCellValue('A1', 'ID заявки');
                $active_sheet->setCellValue('B1', 'ID клиента');
                $active_sheet->setCellValue('C1', 'Дата');
                $active_sheet->setCellValue('D1', 'Договор');
                $active_sheet->setCellValue('E1', 'ФИО');
                $active_sheet->setCellValue('F1', 'Дата рождения');
                $active_sheet->setCellValue('G1', 'Телефон');
                $active_sheet->setCellValue('H1', 'Адрес регистрации');
                $active_sheet->setCellValue('I1', 'Адрес проживания');
                $active_sheet->setCellValue('J1', 'Место работы, должность');
                $active_sheet->setCellValue('K1', 'Почта');
                $active_sheet->setCellValue('L1', 'Сумма');
                $active_sheet->setCellValue('M1', 'Пролонгация');
                $active_sheet->setCellValue('N1', 'Сумма пролонгаций');
                $active_sheet->setCellValue('O1', 'ПК/НК');
                $active_sheet->setCellValue('P1', 'Тип ПК');
                $active_sheet->setCellValue('Q1', 'Менеджер');
                $active_sheet->setCellValue('R1', 'Статус');
                $active_sheet->setCellValue('S1', 'Дата возврата');
                $active_sheet->setCellValue('T1', 'ПДН');
                $active_sheet->setCellValue('U1', 'Дней займа');
                $active_sheet->setCellValue('V1', 'Дата факт возврата');
                $active_sheet->setCellValue('W1', 'Сумма выплачено');
                $active_sheet->setCellValue('X1', 'Источник');
                $active_sheet->setCellValue('Y1', 'ID заявки');
                $active_sheet->setCellValue('Z1', 'ID клиента');
                $active_sheet->setCellValue('AA1', 'Промокод');
                $active_sheet->setCellValue('AB1', 'Общая сумма активных долгов');
                $active_sheet->setCellValue('AC1', 'Количество активных долгов');
                $active_sheet->setCellValue('AD1', 'Наличие 46ой статьи');
                if ($nbki == 1) {
                    $active_sheet->setCellValue('AE1', 'Всего активных кредитов количество');
                    $active_sheet->setCellValue('AF1', 'Всего активных кредитов сумма');
                    $active_sheet->setCellValue('AG1', 'Всего погашено кредитов количество');
                    $active_sheet->setCellValue('AH1', 'Всего погашено кредитов сумма');
                    $active_sheet->setCellValue('AI1', 'Ежемесячный платеж по кредитам');
                    $active_sheet->setCellValue('AJ1', 'Размер просроченной задолженности на сегодня');
                    $active_sheet->setCellValue('AK1', 'Максимальная просрочка за последний год');
                    $active_sheet->setCellValue('AL1', 'Количество микрозаймов за последние 3 месяца');
                    $active_sheet->setCellValue('AM1', 'Количество активных микрозаймов');
                }
                $active_sheet->setCellValue('AN1', 'МФО2НБКИ');
                $active_sheet->setCellValue('AO1', 'МАНИМАЭН');
                $active_sheet->setCellValue('AP1', 'Зона качества');

                $i = 2;
                foreach ($contracts as $contract) {

                    if ($contract->utm_source == 'kpk' || $contract->utm_source == 'part1') {
                        $active_sheet->getStyle('A'.$i.':AM'.$i)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'e1e1e1')
                                    )
                                )
                            );
                    }

                    if ($contract->client_status == 'pk')
                        $client_status = 'ПК';
                    elseif ($contract->client_status == 'nk')
                        $client_status = 'НК';
                    elseif ($contract->client_status == 'crm')
                        $client_status = 'ПК CRM';
                    elseif ($contract->client_status == 'rep')
                        $client_status = 'НК';
                    else
                        $client_status = '';

                    $status = $statuses[$contract->status] ?? "Не определён";

                    $active_sheet->setCellValue('A' . $i, $contract->order_id); 
                    $active_sheet->setCellValue('B' . $i, $contract->user_id); 
                    $active_sheet->setCellValue('C' . $i, date('d.m.Y', strtotime($contract->date)));
                    $active_sheet->setCellValue('D' . $i, $contract->number); 
                    $active_sheet->setCellValue('E' . $i, $contract->lastname . ' ' . $contract->firstname . ' ' . $contract->patronymic . ' ' . $contract->birth);
                    $active_sheet->setCellValue('F' . $i, $contract->birth);
                    $active_sheet->setCellValue('G' . $i, $contract->phone_mobile);
                    $active_sheet->setCellValue('H' . $i, $contract->RegAddr);
                    $active_sheet->setCellValue('I' . $i, $contract->FaktAddr);
                    $active_sheet->setCellValue('J' . $i, $contract->workplace.', '.$contract->profession);
                    $active_sheet->setCellValue('K' . $i, $contract->email);
                    $active_sheet->setCellValue('L' . $i, $contract->p2p_amount * 1);
                    $active_sheet->setCellValue('M' . $i, $contract->count_prolongation);
                    $active_sheet->setCellValue('N' . $i, $contract->prolongations_amount);
                    $active_sheet->setCellValue('O' . $i, $client_status);
                    $active_sheet->setCellValue('P' . $i, $contract->type_pk);
                    $active_sheet->setCellValue('Q' . $i, $managers[$contract->manager_id]->name);
                    $active_sheet->setCellValue('R' . $i, $status);
                    $active_sheet->setCellValue('S' . $i, date('d.m.Y', strtotime($contract->return_date)));
                    $active_sheet->setCellValue('T' . $i, $contract->pdn);
                    $active_sheet->setCellValue('U' . $i, $contract->period);
                    $active_sheet->setCellValue('V' . $i, date('d.m.Y', strtotime($contract->close_date)));
                    $active_sheet->setCellValue('W' . $i, $contract->sumPayed);
                    $active_sheet->setCellValue('X' . $i, $contract->utm_source);
                    $active_sheet->setCellValue('Y' . $i, $contract->order_id);
                    $active_sheet->setCellValue('Z' . $i, $contract->user_id);
                    $active_sheet->setCellValue('AA' . $i, $contract->promocode);

                    $fsspScor = ScoringsORM::query()->where('order_id', '=', $contract->order_id)->where('type', '=', 'fssp')->first();
                    
                    if ($fsspScor) {
                        $body = unserialize($fsspScor->body);
                        if (isset($body['expSum'])) {
                            $active_sheet->setCellValue('AB' . $i, $body['expSum']);
                            $active_sheet->setCellValue('AC' . $i, $body['expCount']);
                            $active_sheet->setCellValue('AD' . $i, $body['article'] ? 'Да' : 'Нет');
                        } else {
                            $active_sheet->setCellValue('AB' . $i, "0");
                            $active_sheet->setCellValue('AC' . $i, "0");
                            $active_sheet->setCellValue('AD' . $i, "Нет");
                        }
                    }
                    
                    if ($nbki == 1) {
                        $nbkiScor = ScoringsORM::query()->where('order_id', '=', $contract->order_id)->where('type', '=', 'nbki')->orderBy('id', 'desc')->first();
                        $nbkiScorBody = unserialize($nbkiScor->body);

                        $reoprt_contracts_nbkis = $this->ReoprtContractsNbki->get_reoprt_nbkis(array('order_id' => $contract->order_id));
                        $variables_arr = json_decode($reoprt_contracts_nbkis[0]->variables);

                        if ($nbkiScorBody && !is_null($nbkiScorBody['number_of_active'])) {
                            $active_sheet->setCellValue('AE' . $i, $nbkiScorBody['number_of_active']);
                        }
                        else{
                            $active_sheet->setCellValue('AE' . $i, $variables_arr->activeProduct);
                        }

                        if ($nbkiScorBody && !is_null($nbkiScorBody['extra_scoring']['active_loans_credit_limit_sum'])) {
                            $active_sheet->setCellValue('AF' . $i, $nbkiScorBody['extra_scoring']['active_loans_credit_limit_sum']);
                        }
                        else{
                            $active_sheet->setCellValue('AF' . $i, $variables_arr->totalAmtOutstanding);
                        }

                        if ($nbkiScorBody && !is_null(nbkiScorBody['count_of_closed'])) {
                            $active_sheet->setCellValue('AG' . $i, $nbkiScorBody['count_of_closed']);
                        }
                        else{
                            $active_sheet->setCellValue('AG' . $i, $variables_arr->doneProduct);
                        }

                        if ($nbkiScorBody && !is_null($nbkiScorBody['extra_scoring']['closed_loans_credit_limit_sum'])) {
                            $active_sheet->setCellValue('AH' . $i, $nbkiScorBody['extra_scoring']['closed_loans_credit_limit_sum']);
                        }
                        else{
                            $active_sheet->setCellValue('AH' . $i, $variables_arr->totalAmtOutstandingDone);
                        }

                        if ($nbkiScorBody && !is_null($nbkiScorBody['extra_scoring']['monthly_active_loans_payment_sum'])) {
                            $active_sheet->setCellValue('AI' . $i, $nbkiScorBody['extra_scoring']['monthly_active_loans_payment_sum']);
                        }
                        else{
                            $active_sheet->setCellValue('AI' . $i, $variables_arr->totalAverPaymtAmt);
                        }

                        if ($nbkiScorBody && !is_null($nbkiScorBody['extra_scoring']['overdue_amount_sum'])) {
                            $active_sheet->setCellValue('AJ' . $i, $nbkiScorBody['extra_scoring']['overdue_amount_sum']);
                        }
                        else{
                            $active_sheet->setCellValue('AJ' . $i, $variables_arr->dolg);
                        }

                        if ($nbkiScorBody && !is_null($nbkiScorBody['extra_scoring']['current_year_max_overdue_amount'])) {
                            $active_sheet->setCellValue('AK' . $i, $nbkiScorBody['extra_scoring']['current_year_max_overdue_amount']);
                        }
                        else{
                            $active_sheet->setCellValue('AK' . $i, $variables_arr->dolg);
                        }

                        if ($nbkiScorBody && !is_null($nbkiScorBody['extra_scoring']['microloans_over_last_90_days_count'])) {
                            $active_sheet->setCellValue('AL' . $i, $nbkiScorBody['extra_scoring']['microloans_over_last_90_days_count']);
                        }
                        else{
                            $active_sheet->setCellValue('AL' . $i, $variables_arr->mkk);
                        }

                        if ($nbkiScorBody && !is_null($nbkiScorBody['extra_scoring']['active_microloan_count'])) {
                            $active_sheet->setCellValue('AM' . $i, $nbkiScorBody['extra_scoring']['active_microloan_count']);
                        }
                        else{
                            $active_sheet->setCellValue('AM' . $i, $variables_arr->mkkSumm);
                        }
                    }
                    $active_sheet->setCellValue('AN' . $i, $contract->score_mf0_2_nbki);
                    $active_sheet->setCellValue('AO' . $i, $contract->maniman);
                    $active_sheet->setCellValue('AP' . $i, $contract->zone);

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir . $filename);

                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }

            $this->design->assign('contracts', $contracts);
        }

        return $this->design->fetch('statistics/contracts.tpl');
    }

    public static function date_format($date_string, $format = 'd.m.Y') {
        try {
            return date($format, strtotime($date_string));
        } catch (Exception $exception) {
            return $date_string;
        }
    }

    private function action_payments()
    {
        if ($operation_id = $this->request->get('operation_id', 'integer')) {
            if ($operation = $this->operations->get_operation($operation_id)) {
                $operation->contract = $this->contracts->get_contract($operation->contract_id);
                $operation->transaction = $this->transactions->get_transaction($operation->transaction_id);
                if ($operation->transaction->insurance_id)
                    $operation->transaction->insurance = $this->insurances->get_insurance($operation->transaction->insurance_id);

                /*
                if ($operation->type == 'REJECT_REASON')
                {
                    $result = $this->soap1c->send_reject_reason($operation);
                    if (!((isset($result->return) && $result->return == 'OK') || $result == 'OK'))
                    {
                        $order = $this->orders->get_order($operation->order_id);
                        $this->soap1c->send_order($order);
                        $result = $this->soap1c->send_reject_reason($operation);
                    }
                }
                else
                {
                    $result = $this->soap1c->send_payments(array($operation));
                }
                */
                if ((isset($result->return) && $result->return == 'OK') || $result == 'OK') {
                    $this->operations->update_operation($operation->id, array(
                        'sent_date' => date('Y-m-d H:i:s'),
                        'sent_status' => 2
                    ));
                    $this->json_output(array('success' => 'Операция отправлена'));
                } else {
                    $this->json_output(array('error' => 'Ошибка при отправке'));
                }

            } else {
                $this->json_output(array('error' => 'Операция не найдена'));
            }
        } elseif ($daterange = $this->request->get('daterange')) {
            $search_filter = '';

            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            if ($search = $this->request->get('search')) {
                if (!empty($search['created']))
                    $search_filter .= $this->db->placehold(' AND DATE(t.created) = ?', date('Y-m-d', strtotime($search['created'])));
                if (!empty($search['number']))
                    $search_filter .= $this->db->placehold(' AND c.number LIKE "%' . $this->db->escape($search['number']) . '%"');
                if (!empty($search['fio']))
                    $search_filter .= $this->db->placehold(' AND (u.lastname LIKE "%' . $this->db->escape($search['fio']) . '%" OR u.firstname LIKE "%' . $this->db->escape($search['fio']) . '%" OR u.patronymic LIKE "%' . $this->db->escape($search['fio']) . '%")');
                if (!empty($search['amount']))
                    $search_filter .= $this->db->placehold(' AND t.amount = ?', $search['amount'] * 100);
                if (!empty($search['card']))
                    $search_filter .= $this->db->placehold(' AND t.callback_response LIKE "%' . $this->db->escape($search['card']) . '%"');
                if (!empty($search['register_id']))
                    $search_filter .= $this->db->placehold(' AND t.register_id LIKE "%' . $this->db->escape($search['register_id']) . '%"');
                if (!empty($search['operation']))
                    $search_filter .= $this->db->placehold(' AND t.operation LIKE "%' . $this->db->escape($search['operation']) . '%"');
                if (!empty($search['description']))
                    $search_filter .= $this->db->placehold(' AND t.description LIKE "%' . $this->db->escape($search['description']) . '%"');

            }

            $query = $this->db->placehold("
                SELECT
                    `o`.id,
                    `o`.user_id,
                    `o`.contract_id,
                    `o`.order_id,
                    `o`.transaction_id,
                    `o`.type,
                    `o`.amount,
                    `t`.created,
                    `o`.sent_date,
                    `c`.number AS contract_number,
                    `c`.return_date,
                    `c`.inssuance_date,
                    `c`.amount as contractAmount,
                    `c`.collection_manager_id,
                    `u`.lastname,
                    `u`.firstname,
                    `u`.patronymic,
                    `u`.birth,
                    `t`.register_id,
                    `t`.operation,
                    `t`.prolongation,
                    `t`.insurance_id,
                    `t`.description,
                    `t`.callback_response,
                    `i`.number AS insurance_number,
                    `i`.amount AS insurance_amount,
                    `t`.sector,
                    `o`.type_payment,
                    `o`.expired_period
                FROM __operations        AS `o`
                LEFT JOIN __contracts    AS `c` ON `c`.id = `o`.contract_id
                LEFT JOIN __users        AS `u` ON `u`.id = `o`.user_id
                LEFT JOIN __transactions AS `t` ON `t`.id = `o`.transaction_id
                LEFT JOIN __insurances   AS `i` ON `i`.id = `t`.insurance_id
                WHERE `o`.type != 'INSURANCE'
                $search_filter
                AND DATE(`t`.created) >= ?
                AND DATE(`t`.created) <= ?
                ORDER BY `t`.created
            ", $date_from, $date_to);
            $this->db->query($query);

            $operations = array();
            foreach ($this->db->results() as $op) {
                $operations[$op->id] = $op;
            }


            $statuses = $this->contracts->get_statuses();
            $this->design->assign('statuses', $statuses);

            $collection_statuses = $this->contracts->get_collection_statuses();
            $this->design->assign('collection_statuses', $collection_statuses);


            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m)
                    $managers[$m->id] = $m;

                $filename = 'files/reports/payments.xls';
                require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Выдачи " . $from . "-" . $to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(45);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(20);
                $active_sheet->getColumnDimension('G')->setWidth(20);
                $active_sheet->getColumnDimension('H')->setWidth(10);
                $active_sheet->getColumnDimension('I')->setWidth(10);
                $active_sheet->getColumnDimension('J')->setWidth(30);
                $active_sheet->getColumnDimension('K')->setWidth(10);
                $active_sheet->getColumnDimension('L')->setWidth(15);
                $active_sheet->getColumnDimension('M')->setWidth(15);

                $active_sheet->setCellValue('A1', 'Дата');
                $active_sheet->setCellValue('B1', 'Договор');
                $active_sheet->setCellValue('C1', 'ФИО');
                $active_sheet->setCellValue('D1', 'Дата выдачи');
                $active_sheet->setCellValue('E1', 'Сумма займа');
                $active_sheet->setCellValue('F1', 'Сумма');
                $active_sheet->setCellValue('G1', 'Карта');
                $active_sheet->setCellValue('H1', 'Описание');
                $active_sheet->setCellValue('I1', 'B2P OrderID');
                $active_sheet->setCellValue('J1', 'B2P OperationID');
                $active_sheet->setCellValue('K1', 'Страховка');
                $active_sheet->setCellValue('L1', 'Дата возврата');
                $active_sheet->setCellValue('M1', 'Срок просрочки (дни)');
                $active_sheet->setCellValue('N1', 'Был у коллекшина');

                $i = 2;
                foreach ($operations as $contract) {

                    $active_sheet->setCellValue('A' . $i, date('d.m.Y', strtotime($contract->created)));
                    $active_sheet->setCellValue('B' . $i, $contract->contract_number . ' ' . ($contract->sector == '7036' ? 'ЮК' : 'МКК'));
                    $active_sheet->setCellValue('C' . $i, $contract->lastname . ' ' . $contract->firstname . ' ' . $contract->patronymic . ' ' . $contract->birth);
                    $active_sheet->setCellValue('D' . $i, $contract->inssuance_date);
                    $active_sheet->setCellValue('E' . $i, $contract->contractAmount);

                    $query = $this->db->placehold("
                        SELECT created,
                        body_summ,
                        percents_summ,
                        peni_summ
                        FROM __collections
                        WHERE contract_id = ?
                        AND DATE(created) >= ?
                        AND DATE(created) <= ?
                        ORDER BY id DESC 
                    ",$contract->contract_id, $date_from, $date_to);
                    $this->db->query($query);
                    $results = $this->db->results();
                    
                    $was_collection = false;

                    foreach ($results as $result) {
                        if (date('Y-m-d', strtotime($contract->created)) == date('Y-m-d', strtotime($result->created))) {

                            $summ_all = $result->body_summ + $result->percents_summ + $result->peni_summ;
                            // $summ_all = $result->body_summ + $result->percents_summ;
        
                            $was_collection = true;
                            if ($summ_all == 0 || date('Y-m-d', strtotime($contract->created)) != date('Y-m-d', strtotime($result->created))) {
                                $was_collection = false;
                            }

                        }
                    }

                    $active_sheet->setCellValue('F' . $i, $contract->amount);
                    // $active_sheet->setCellValue('F' . $i, $summ_all);
                    $active_sheet->setCellValue('G' . $i, $contract->pan);
                    $active_sheet->setCellValue('H' . $i, $contract->description . ' ' . ($contract->prolongation ? '(пролонгация)' : ''));
                    $active_sheet->setCellValue('I' . $i, $contract->register_id);
                    $active_sheet->setCellValue('J' . $i, $contract->operation);//--
                    $active_sheet->setCellValue('K' . $i, $contract->insurance_number . ' ' . ($contract->insurance_amount ? $contract->insurance_amount . ' руб' : ''));
                    $active_sheet->setCellValue('L' . $i, $contract->return_date);
                    $active_sheet->setCellValue('M' . $i, $contract->expired_period);
                    // $active_sheet->setCellValue('N' . $i, $contract->collection_manager_id == 0 ? 'не был': 'БЫЛ');
                    $active_sheet->setCellValue('N' . $i, $was_collection == false ? 'не был': 'БЫЛ');

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir . $filename);

                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }

            $this->design->assign('operations', $operations);
        }

        return $this->design->fetch('statistics/payments.tpl');
    }

    private function action_eventlogs()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);


            $query_manager_id = '';
            if ($filter_manager_id = $this->request->get('manager_id')) {
                if ($filter_manager_id != 'all')
                    $query_manager_id = $this->db->placehold("AND o.manager_id = ?", (int)$filter_manager_id);

                $this->design->assign('filter_manager_id', $filter_manager_id);
            }

            $query = $this->db->placehold("
                SELECT
                    o.id AS order_id,
                    o.date,
                    o.reason_id,
                    o.reject_reason,
                    o.user_id,
                    o.manager_id,
                    o.status,
                    u.lastname,
                    u.firstname,
                    u.patronymic
                FROM __orders AS o
                LEFT JOIN __users AS u
                ON u.id = o.user_id
                WHERE o.manager_id IS NOT NULL
                AND DATE(o.date) >= ?
                AND DATE(o.date) <= ?
                $query_manager_id
            ", $date_from, $date_to);
            $this->db->query($query);

            $orders = array();
            foreach ($this->db->results() as $o)
                $orders[$o->order_id] = $o;

            if (!empty($orders)) {
                foreach ($orders as $o) {
                    $o->eventlogs = $this->eventlogs->get_logs(array('order_id' => $o->order_id));
                }
            }

            $events = $this->eventlogs->get_events();
            $this->design->assign('events', $events);

            $reasons = $this->reasons->get_reasons();
            $this->design->assign('reasons', $reasons);


            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m)
                    $managers[$m->id] = $m;

                $order_statuses = $this->orders->get_statuses();

                $filename = 'files/reports/eventlogs.xls';
                require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Логи " . $from . "-" . $to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(6);
                $active_sheet->getColumnDimension('B')->setWidth(30);
                $active_sheet->getColumnDimension('C')->setWidth(10);
                $active_sheet->getColumnDimension('D')->setWidth(10);
                $active_sheet->getColumnDimension('E')->setWidth(30);
                $active_sheet->getColumnDimension('F')->setWidth(30);

                $active_sheet->setCellValue('A1', '#');
                $active_sheet->setCellValue('B1', 'Заявка');
                $active_sheet->mergeCells('C1:F1');
                $active_sheet->setCellValue('C1', 'События');

                $style_bold = array(
                    'font' => array(
                        'name' => 'Calibri',
                        'size' => 13,
                        'bold' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'wrap' => true,
                    )
                );
                $active_sheet->getStyle('A1:C1')->applyFromArray($style_bold);

                $i = 2;
                $rc = 1;
                foreach ($orders as $order) {
                    $start_i = $i;

                    $a_indexes = 'A' . $i . ':A' . ($i + count($order->eventlogs) - 1);
                    if (count($order->eventlogs) > 2)
                        $active_sheet->mergeCells($a_indexes);
                    $active_sheet->getStyle($a_indexes)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $active_sheet->getStyle($a_indexes)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    $active_sheet->setCellValue('A' . $i, $rc);


                    $active_sheet->setCellValue('B' . $i, $order->order_id);
                    $active_sheet->setCellValue('B' . ($i + 1), 'Статус: ' . $order_statuses[$order->status]);
                    $active_sheet->setCellValue('B' . ($i + 2), 'Менеджер: ' . $managers[$order->manager_id]->name);

                    foreach ($order->eventlogs as $ev) {
                        $active_sheet->setCellValue('C' . $i, date('d.m.Y', strtotime($ev->created)));
                        $active_sheet->setCellValue('D' . $i, date('H:i:s', strtotime($ev->created)));
                        $active_sheet->setCellValue('E' . $i, $events[$ev->event_id]);
                        $active_sheet->setCellValue('F' . $i, $managers[$ev->manager_id]->name);

                        $i++;
                    }

                    $rc++;

                    $active_sheet->getStyle('A' . $start_i . ':F' . ($i - 1))->applyFromArray(
                        array(
                            'borders' => array(
                                'allborders' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                                    'color' => array('rgb' => '666666')
                                )
                            )
                        )
                    );
                    $active_sheet->getStyle('A' . $start_i . ':F' . ($i - 1))->applyFromArray(
                        array(
                            'borders' => array(
                                'top' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                                    'color' => array('rgb' => '222222')
                                ),
                                'bottom' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                                    'color' => array('rgb' => '222222')
                                ),
                                'left' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                                    'color' => array('rgb' => '222222')
                                ),
                                'right' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                                    'color' => array('rgb' => '222222')
                                )
                            )
                        )
                    );
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir . $filename);

                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }


            $this->design->assign('orders', $orders);
        }

        return $this->design->fetch('statistics/eventlogs.tpl');
    }

    private function action_penalties()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);


            $filter = array();
            $filter['date_from'] = $date_from;
            $filter['date_to'] = $date_to;
            $filter['status'] = 4;

            if ($this->manager->role == 'user') {
                $filter['manager_id'] = $this->manager->id;
            } elseif ($filter_manager_id = $this->request->get('manager_id')) {
                if ($filter_manager_id != 'all')
                    $filter['manager_id'] = $filter_manager_id;

                $this->design->assign('filter_manager_id', $filter_manager_id);
            }

            $orders = array();
            if ($penalties = $this->penalties->get_penalties($filter)) {
                $order_ids = array();
                foreach ($penalties as $penalty)
                    $order_ids[] = $penalty->order_id;

                foreach ($this->orders->get_orders(array('id' => $order_ids)) as $order) {
                    $order->penalties = array();
                    $orders[$order->order_id] = $order;
                }

                foreach ($penalties as $penalty) {
                    if (isset($orders[$penalty->order_id]))
                        $orders[$penalty->order_id]->penalties[] = $penalty;
                }

                $total_summ = 0;
                $total_count = 0;
                foreach ($orders as $order) {
                    $total_count++;
                    $order->penalty_summ = 0;
                    foreach ($order->penalties as $p) {
                        if ($order->penalty_summ < $p->cost)
                            $order->penalty_summ = $p->cost;
                    }
                    $order->penalty_summ = min($order->penalty_summ, 500);
                    $total_summ += $order->penalty_summ;
                }

                $this->design->assign('total_summ', $total_summ);
                $this->design->assign('total_count', $total_count);
            }

            $this->design->assign('orders', $orders);

            $penalty_types = array();
            foreach ($this->penalties->get_types() as $t)
                $penalty_types[$t->id] = $t;
            $this->design->assign('penalty_types', $penalty_types);

            $penalty_statuses = $this->penalties->get_statuses();
            $this->design->assign('penalty_statuses', $penalty_statuses);

        }

        return $this->design->fetch('statistics/penalties.tpl');
    }

    private function action_dailyreports()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);


            $filter = array();
            $filter['date_from'] = $date_from;
            $filter['date_to'] = $date_to;

            if ($this->manager->role == 'user') {
                $filter['manager_id'] = $this->manager->id;
            } elseif ($filter_manager_id = $this->request->get('manager_id')) {
                if ($filter_manager_id != 'all')
                    $filter['manager_id'] = $filter_manager_id;

                $this->design->assign('filter_manager_id', $filter_manager_id);
            }

            $final_array = [];

            //Выдано новых + сумма, Выдано повторно + сумма

            $filter['inssuance_date_from'] = $date_from;
            $filter['inssuance_date_to'] = $date_to;

            $inssuance_contracts = $this->contracts->get_contracts_orders($filter);

            $new_rep_orders = [];

            foreach ($inssuance_contracts as $contract) {
                $date = date('Y-m-d', strtotime($contract->inssuance_date));

                if (array_key_exists($date, $new_rep_orders) == false) {
                    $new_rep_orders[$date] = [
                        'count_new_orders' => 0,
                        'sum_new_orders' => 0,
                        'count_repeat_orders' => 0,
                        'sum_repeat_orders' => 0
                    ];
                }
                $operations = $this->operations->get_operations(array('contract_id'=>$contract->contract_id, 'type'=>'P2P'));
                if ($contract->client_status == 'nk' || $contract->client_status == 'rep') {
                    $new_rep_orders[$date]['count_new_orders'] += 1;
                    // $new_rep_orders[$date]['sum_new_orders'] += $contract->amount;
                    $new_rep_orders[$date]['sum_new_orders'] += $operations[0]->amount;
                }
                if ($contract->client_status == 'pk' || $contract->client_status == 'crm') {
                    $new_rep_orders[$date]['count_repeat_orders'] += 1;
                    // $new_rep_orders[$date]['sum_repeat_orders'] += $contract->amount;
                    $new_rep_orders[$date]['sum_repeat_orders'] += $operations[0]->amount;
                }
            }

            foreach ($new_rep_orders as $date => $order) {
                $final_array[$date]['count_new_orders'] = $order['count_new_orders'];
                $final_array[$date]['sum_new_orders'] = $order['sum_new_orders'];
                $final_array[$date]['count_repeat_orders'] = $order['count_repeat_orders'];
                $final_array[$date]['sum_repeat_orders'] = $order['sum_repeat_orders'];
            }

            //Погашено
            $filter_closed_contracts['close_date_from'] = $date_from;
            $filter_closed_contracts['close_date_to'] = $date_to;
            $count_closed_contracts = [];

            $contracts = $this->contracts->get_contracts($filter_closed_contracts);

            foreach ($contracts as $contract) {
                $date = date('Y-m-d', strtotime($contract->close_date));

                if (array_key_exists($date, $count_closed_contracts) == false) {
                    $count_closed_contracts[$date] = ['count_closed_contracts' => 0];
                }
                $count_closed_contracts[$date]['count_closed_contracts'] += 1;
            }

            foreach ($count_closed_contracts as $date => $contract) {
                $final_array[$date]['count_closed_contracts'] = $contract['count_closed_contracts'];
            }

            $operations = $this->operations->get_operations_transactions($filter);
            $operations_by_date = [];

            foreach ($operations as $operation) {
                $date = date('Y-m-d', strtotime($operation->created));

                if (array_key_exists($date, $operations_by_date) == false) {
                    $operations_by_date[$date]['count_prolongations'] = 0;
                    $operations_by_date[$date]['loan_body_summ'] = 0;
                    $operations_by_date[$date]['loan_charges_summ'] = 0;
                    $operations_by_date[$date]['count_insurance'] = 0;
                    $operations_by_date[$date]['sum_insurance'] = 0;
                    $operations_by_date[$date]['count_insurance_prolongation'] = 0;
                    $operations_by_date[$date]['sum_insurance_prolongation'] = 0;
                    $operations_by_date[$date]['count_sms_services'] = 0;
                    $operations_by_date[$date]['sum_sms_services'] = 0;
                    $operations_by_date[$date]['count_reject_reason'] = 0;
                    $operations_by_date[$date]['sum_reject_reason'] = 0;
                    $operations_by_date[$date]['count_return'] = 0;
                    $operations_by_date[$date]['sum_return'] = 0;
                    $operations_by_date[$date]['sum_cor_percents'] = 0;
                    $operations_by_date[$date]['sum_cor_body'] = 0;
                    $operations_by_date[$date]['count_cor_prolongations'] = 0;
                    $operations_by_date[$date]['count_cor_closed'] = 0;
                    $operations_by_date[$date]['count_partial_release'] = 0;
                }

                if ($operation->prolongation == 1 && $operation->type == 'PAY') {
                    $operations_by_date[$date]['count_prolongations'] += 1;

                    if ($operation->type_payment == 1) {
                        $operations_by_date[$date]['count_cor_prolongations'] += 1;
                    }
                }
                if ($operation->contract_id && $operation->type == 'PAY') {
                    $operations_by_date[$date]['loan_body_summ'] += $operation->loan_body_summ;

                    $charges_sum = $operation->loan_percents_summ + $operation->loan_charge_summ + $operation->loan_peni_summ;
                    $operations_by_date[$date]['loan_charges_summ'] += $charges_sum;

                    if ($operation->type_payment == 1) {
                        $operations_by_date[$date]['sum_cor_percents'] += $operation->loan_percents_summ;
                        $operations_by_date[$date]['sum_cor_body'] += $operation->loan_body_summ;
                    }

                    if ($operation->op_loan_percents_summ == 0 && $operation->op_loan_body_summ == 0 && $operation->type_payment == 1) {
                        $operations_by_date[$date]['count_cor_closed'] += 1;
                    }

                    if ($operation->prolongation == 0 && $operation->contract_is_closed == 0) {
                        $operations_by_date[$date]['count_partial_release']++;
                    }
                }

                if ($operation->type == 'INSURANCE') {
                    $operations_by_date[$date]['count_insurance'] += 1;
                    $operations_by_date[$date]['sum_insurance'] += $operation->amount;

                }
                if ($operation->type == 'INSURANCE_BC') {
                    $operations_by_date[$date]['count_insurance_prolongation'] += 1;
                    $operations_by_date[$date]['sum_insurance_prolongation'] += $operation->amount;
                }

                if ($operation->type == 'BUD_V_KURSE') {
                    $operations_by_date[$date]['count_sms_services'] += 1;
                    $operations_by_date[$date]['sum_sms_services'] += $operation->amount;
                }
                if ($operation->type == 'REJECT_REASON') {
                    $operations_by_date[$date]['count_reject_reason'] += 1;
                    $operations_by_date[$date]['sum_reject_reason'] += $operation->amount;
                }
                if (strrpos($operation->type, 'RETURN') !== false) {
                    $operations_by_date[$date]['count_return'] += 1;
                    $operations_by_date[$date]['sum_return'] += $operation->amount;
                }
            }

            foreach ($operations_by_date as $date => $operation) {
                $final_array[$date]['count_prolongations'] = $operation['count_prolongations'];
                $final_array[$date]['loan_body_summ'] = $operation['loan_body_summ'];
                $final_array[$date]['loan_charges_summ'] = $operation['loan_charges_summ'];
                $final_array[$date]['count_insurance'] = $operation['count_insurance'];
                $final_array[$date]['sum_insurance'] = $operation['sum_insurance'];
                $final_array[$date]['count_insurance_prolongation'] = $operation['count_insurance_prolongation'];
                $final_array[$date]['sum_insurance_prolongation'] = $operation['sum_insurance_prolongation'];
                $final_array[$date]['count_sms_services'] = $operation['count_sms_services'];
                $final_array[$date]['sum_sms_services'] = $operation['sum_sms_services'];
                $final_array[$date]['count_reject_reason'] = $operation['count_reject_reason'];
                $final_array[$date]['sum_reject_reason'] = $operation['sum_reject_reason'];
                $final_array[$date]['count_return'] = $operation['count_return'];
                $final_array[$date]['sum_return'] = $operation['sum_return'];
                $final_array[$date]['sum_cor_percents'] = $operation['sum_cor_percents'];
                $final_array[$date]['sum_cor_body'] = $operation['sum_cor_body'];
                $final_array[$date]['count_cor_prolongations'] = $operation['count_cor_prolongations'];
                $final_array[$date]['count_cor_closed'] = $operation['count_cor_closed'];
                $final_array[$date]['count_partial_release'] = $operation['count_partial_release'];
            }

            $operations = $this->operations->get_operations_insurance($filter);
            $operations_insurance_inssuance = [];
            $operations_insurance_close = [];

            foreach ($operations as $operation) {
                $date = date('Y-m-d', strtotime($operation->created));

                if ($operation->close_date) {
                    $close_date = date('Y-m-d', strtotime($operation->close_date));

                    if ($date == $close_date && $operation->amount == 200 || $operation->amount == 400) {

                        if (array_key_exists($date, $operations_insurance_close) == false) {
                            $operations_insurance_close[$date] = [
                                'count_insurance_close' => 0,
                                'sum_insurance_close' => 0];
                        }

                        $operations_insurance_close[$date]['count_insurance_close'] += 1;
                        $operations_insurance_close[$date]['sum_insurance_close'] += $operation->amount;
                    }
                }
                if ($operation->inssuance_date) {

                    $inssuance_date = date('Y-m-d', strtotime($operation->inssuance_date));

                    if ($date == $inssuance_date && $operation->type == 'INSURANCE') {
                        if (array_key_exists($date, $operations_insurance_inssuance) == false) {
                            $operations_insurance_inssuance[$date] = [
                                'count_insurance_inssuance' => 0,
                                'sum_insurance_inssuance' => 0,
                            ];
                        }
                        $operations_insurance_inssuance[$date]['count_insurance_inssuance'] += 1;
                        $operations_insurance_inssuance[$date]['sum_insurance_inssuance'] += $operation->amount;
                    }
                }
            }

            foreach ($operations_insurance_close as $date => $operation) {
                $final_array[$date]['count_insurance_close'] = $operation['count_insurance_close'];
                $final_array[$date]['sum_insurance_close'] = $operation['sum_insurance_close'];
            }

            foreach ($operations_insurance_inssuance as $date => $operation) {
                $final_array[$date]['count_insurance_inssuance'] = $operation['count_insurance_inssuance'];
                $final_array[$date]['sum_insurance_inssuance'] = $operation['sum_insurance_inssuance'];
            }

            $transactions = $this->transactions->get_transactions_cards($filter);
            $card_binding = [];

            foreach ($transactions as $transaction) {
                $date = date('Y-m-d', strtotime($transaction->operation_date));

                if (array_key_exists($date, $card_binding) == false) {
                    $card_binding[$date] = ['count_card_binding' => 0, 'sum_card_binding' => 0];
                }

                $card_binding[$date]['count_card_binding'] += 1;
                $card_binding[$date]['sum_card_binding'] += ($transaction->amount / 100);
            }

            foreach ($card_binding as $date => $operation) {
                $final_array[$date]['count_card_binding'] = $operation['count_card_binding'];
                $final_array[$date]['sum_card_binding'] = $operation['sum_card_binding'];
            }

            foreach ($final_array as $array) {
                if (array_key_exists('Итого', $final_array) == false) {
                    $final_array['Итого']['count_new_orders'] = 0;
                    $final_array['Итого']['sum_new_orders'] = 0;
                    $final_array['Итого']['count_repeat_orders'] = 0;
                    $final_array['Итого']['sum_repeat_orders'] = 0;
                    $final_array['Итого']['count_closed_contracts'] = 0;
                    $final_array['Итого']['count_prolongations'] = 0;
                    $final_array['Итого']['loan_body_summ'] = 0;
                    $final_array['Итого']['loan_charges_summ'] = 0;
                    $final_array['Итого']['count_insurance'] = 0;
                    $final_array['Итого']['sum_insurance'] = 0;
                    $final_array['Итого']['count_insurance_prolongation'] = 0;
                    $final_array['Итого']['sum_insurance_prolongation'] = 0;
                    $final_array['Итого']['count_sms_services'] = 0;
                    $final_array['Итого']['sum_sms_services'] = 0;
                    $final_array['Итого']['count_reject_reason'] = 0;
                    $final_array['Итого']['sum_reject_reason'] = 0;
                    $final_array['Итого']['count_return'] = 0;
                    $final_array['Итого']['sum_return'] = 0;
                    $final_array['Итого']['count_insurance_close'] = 0;
                    $final_array['Итого']['sum_insurance_close'] = 0;
                    $final_array['Итого']['count_insurance_inssuance'] = 0;
                    $final_array['Итого']['sum_insurance_inssuance'] = 0;
                    $final_array['Итого']['count_card_binding'] = 0;
                    $final_array['Итого']['sum_card_binding'] = 0;
                    $final_array['Итого']['sum_cor_percents'] = 0;
                    $final_array['Итого']['sum_cor_body'] = 0;
                    $final_array['Итого']['count_cor_prolongations'] = 0;
                    $final_array['Итого']['count_cor_closed'] = 0;
                    $final_array['Итого']['count_partial_release'] = 0;
                }
                $final_array['Итого']['count_new_orders'] += ($array['count_new_orders']) ?: 0;
                $final_array['Итого']['sum_new_orders'] += ($array['sum_new_orders']) ?: 0;
                $final_array['Итого']['count_repeat_orders'] += ($array['count_repeat_orders']) ?: 0;
                $final_array['Итого']['sum_repeat_orders'] += ($array['sum_repeat_orders']) ?: 0;
                $final_array['Итого']['count_closed_contracts'] += ($array['count_closed_contracts']) ?: 0;
                $final_array['Итого']['count_prolongations'] += ($array['count_prolongations']) ?: 0;
                $final_array['Итого']['loan_body_summ'] += ($array['loan_body_summ']) ?: 0;
                $final_array['Итого']['loan_charges_summ'] += ($array['loan_charges_summ']) ?: 0;
                $final_array['Итого']['count_insurance'] += ($array['count_insurance']) ?: 0;
                $final_array['Итого']['sum_insurance'] += ($array['sum_insurance']) ?: 0;
                $final_array['Итого']['count_insurance_prolongation'] += ($array['count_insurance_prolongation']) ?: 0;
                $final_array['Итого']['sum_insurance_prolongation'] += ($array['sum_insurance_prolongation']) ?: 0;
                $final_array['Итого']['count_sms_services'] += ($array['count_sms_services']) ?: 0;
                $final_array['Итого']['sum_sms_services'] += ($array['sum_sms_services']) ?: 0;
                $final_array['Итого']['count_reject_reason'] += ($array['count_reject_reason']) ?: 0;
                $final_array['Итого']['sum_reject_reason'] += ($array['sum_reject_reason']) ?: 0;
                $final_array['Итого']['count_return'] += ($array['count_return']) ?: 0;
                $final_array['Итого']['sum_return'] += ($array['sum_return']) ?: 0;
                $final_array['Итого']['count_insurance_close'] += ($array['count_insurance_close']) ?: 0;
                $final_array['Итого']['sum_insurance_close'] += ($array['sum_insurance_close']) ?: 0;
                $final_array['Итого']['count_insurance_inssuance'] += ($array['count_insurance_inssuance']) ?: 0;
                $final_array['Итого']['sum_insurance_inssuance'] += ($array['sum_insurance_inssuance']) ?: 0;
                $final_array['Итого']['count_card_binding'] += ($array['count_card_binding']) ?: 0;
                $final_array['Итого']['sum_card_binding'] += ($array['sum_card_binding']) ?: 0;
                $final_array['Итого']['sum_cor_percents'] += ($array['sum_cor_percents']) ?: 0;
                $final_array['Итого']['sum_cor_body'] += ($array['sum_cor_body']) ?: 0;
                $final_array['Итого']['count_cor_prolongations'] += ($array['count_cor_prolongations']) ?: 0;
                $final_array['Итого']['count_cor_closed'] += ($array['count_cor_closed']) ?: 0;
                $final_array['Итого']['count_partial_release'] += ($array['count_partial_release']) ?: 0;
            }

            if ($this->request->get('download') == 'excel') {

                $filename = 'files/reports/days.xls';
                require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle($from . "-" . $to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(15);
                $active_sheet->getColumnDimension('D')->setWidth(15);
                $active_sheet->getColumnDimension('E')->setWidth(15);
                $active_sheet->getColumnDimension('F')->setWidth(15);
                $active_sheet->getColumnDimension('G')->setWidth(15);
                $active_sheet->getColumnDimension('H')->setWidth(15);
                $active_sheet->getColumnDimension('I')->setWidth(15);
                $active_sheet->getColumnDimension('J')->setWidth(15);
                $active_sheet->getColumnDimension('K')->setWidth(15);
                $active_sheet->getColumnDimension('L')->setWidth(15);
                $active_sheet->getColumnDimension('M')->setWidth(15);
                $active_sheet->getColumnDimension('N')->setWidth(15);
                $active_sheet->getColumnDimension('O')->setWidth(15);
                $active_sheet->getColumnDimension('P')->setWidth(15);
                $active_sheet->getColumnDimension('Q')->setWidth(15);
                $active_sheet->getColumnDimension('R')->setWidth(15);
                $active_sheet->getColumnDimension('S')->setWidth(15);
                $active_sheet->getColumnDimension('T')->setWidth(15);
                $active_sheet->getColumnDimension('U')->setWidth(15);

                $active_sheet->setCellValue('A1', 'Дата');
                $active_sheet->setCellValue('B1', 'Выдано новых/Сумма');
                $active_sheet->setCellValue('C1', 'Выдано повторных/Сумма');
                $active_sheet->setCellValue('D1', 'Погашено');
                $active_sheet->setCellValue('E1', 'Продлено');
                $active_sheet->setCellValue('F1', 'Получено ОД');
                $active_sheet->setCellValue('G1', 'Получено %%');
                $active_sheet->setCellValue('H1', 'Всего страховок/Сумма');
                $active_sheet->setCellValue('I1', 'Страховки при выдаче/Сумма');
                $active_sheet->setCellValue('J1', 'Страховки при продлении/Сумма');
                $active_sheet->setCellValue('K1', 'Страховки при закрытии/Сумма');
                $active_sheet->setCellValue('L1', '"Будь в курсе"/Сумма');
                $active_sheet->setCellValue('M1', '"Узнай причину отказа"/Сумма');
                $active_sheet->setCellValue('N1', '"Привязка карты"/Сумма');
                $active_sheet->setCellValue('O1', 'Итого доп продуктов/Сумма');
                $active_sheet->setCellValue('P1', 'Отменено доп продуктов/Сумма');
                $active_sheet->setCellValue('Q1', 'Оплачено на р/сч ОД');
                $active_sheet->setCellValue('R1', 'Оплачено на р/сч %%');
                $active_sheet->setCellValue('S1', 'Продления по р/сч');
                $active_sheet->setCellValue('T1', 'Погашения по р/сч');
                $active_sheet->setCellValue('U1', 'Частично погашено');

                $i = 2;
                foreach ($final_array as $date => $report) {
                    $count_add_services = $report['count_insurance'] + $report['count_sms_services'] + $report['count_reject_reason'] + $report['count_card_binding'];
                    $sum_add_services = $report['sum_insurance'] + $report['sum_sms_services'] + $report['sum_reject_reason'] + $report['sum_card_binding'];

                    $active_sheet->setCellValue('A' . $i, $date);
                    $active_sheet->setCellValue('B' . $i, $report['count_new_orders'] . 'шт /' . $report['sum_new_orders'] . 'руб');
                    $active_sheet->setCellValue('C' . $i, $report['count_repeat_orders'] . 'шт /' . $report['sum_repeat_orders'] . 'руб');
                    $active_sheet->setCellValue('D' . $i, $report['count_closed_contracts']);
                    $active_sheet->setCellValue('E' . $i, $report['count_prolongations']);
                    $active_sheet->setCellValue('F' . $i, $report['loan_body_summ']);
                    $active_sheet->setCellValue('G' . $i, $report['loan_charges_summ']);
                    $active_sheet->setCellValue('H' . $i, $report['count_insurance'] . 'шт /' . $report['sum_insurance'] . 'руб');
                    $active_sheet->setCellValue('I' . $i, $report['count_insurance_inssuance'] . 'шт /' . $report['sum_insurance_inssuance'] . 'руб');
                    $active_sheet->setCellValue('J' . $i, $report['count_insurance_prolongation'] . 'шт /' . $report['sum_insurance_prolongation'] . 'руб');
                    $active_sheet->setCellValue('K' . $i, $report['count_insurance_close'] . 'шт /' . $report['sum_insurance_close'] . 'руб');
                    $active_sheet->setCellValue('L' . $i, $report['count_sms_services'] . 'шт /' . $report['sum_sms_services'] . 'руб');
                    $active_sheet->setCellValue('M' . $i, $report['count_reject_reason'] . 'шт /' . $report['sum_reject_reason'] . 'руб');
                    $active_sheet->setCellValue('N' . $i, $report['count_card_binding'] . 'шт /' . $report['sum_card_binding'] . 'руб');
                    $active_sheet->setCellValue('O' . $i, $count_add_services . 'шт /' . $sum_add_services . 'руб');
                    $active_sheet->setCellValue('P' . $i, $report['count_return'] . 'шт /' . $report['sum_return'] . 'руб');
                    $active_sheet->setCellValue('Q' . $i, $report['sum_cor_body'] . ' руб');
                    $active_sheet->setCellValue('R' . $i, $report['sum_cor_percents'] . ' руб');
                    $active_sheet->setCellValue('S' . $i, $report['count_cor_prolongations'] . ' шт');
                    $active_sheet->setCellValue('T' . $i, $report['count_cor_closed'] . ' шт');
                    $active_sheet->setCellValue('U' . $i, $report['count_partial_release'] . ' шт');

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir . $filename);

                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }

            ksort($final_array);

            $this->design->assign('final_array', $final_array);
        }

        return $this->design->fetch('statistics/dailyreports.tpl');
    }

    private function action_adservices()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);


            $filter = array();
            $filter['date_from'] = $date_from;
            $filter['date_to'] = $date_to;

            $ad_services = $this->operations->operations_contracts_insurance($filter);

            foreach ($ad_services as $service) {
                $service->regAddr = AdressesORM::find($service->regaddress_id);
                $service->regAddr = $service->regAddr->adressfull;

                $contract = $this->contracts->get_contract($service->contract_id);
                $service->contract = $contract;

                // сумма выдачи
                $p2p_amount = 0;
                $contract_operations = $this->operations->get_operations(array('contract_id' => $service->contract->id, 'type' => 'P2P'));
                foreach ($contract_operations as $contract_operation) {
                    $p2p_amount += $contract_operation->amount;
                }
                $service->p2p_amount = $p2p_amount;
            }

            $op_type = ['INSURANCE' => 'Страхование от НС', 'BUD_V_KURSE' => 'Будь в курсе', 'REJECT_REASON' => 'Узнай причину отказа', 'INSURANCE_BC' => 'Страхование БК'];
            $gender = ['male' => 'Мужской', 'female' => 'Женский'];

            $this->design->assign('ad_services', $ad_services);
            $this->design->assign('op_type', $op_type);
            $this->design->assign('gender', $gender);

            $card_binding = $this->transactions->get_transactions_cards_users($filter);

            $this->design->assign('card_binding', $card_binding);

            if ($this->request->get('download') == 'excel') {

                $filename = 'files/reports/adservices.xls';
                require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle($from . "-" . $to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(15);
                $active_sheet->getColumnDimension('D')->setWidth(15);
                $active_sheet->getColumnDimension('E')->setWidth(15);
                $active_sheet->getColumnDimension('F')->setWidth(15);
                $active_sheet->getColumnDimension('G')->setWidth(15);
                $active_sheet->getColumnDimension('H')->setWidth(15);
                $active_sheet->getColumnDimension('I')->setWidth(15);
                $active_sheet->getColumnDimension('J')->setWidth(15);
                $active_sheet->getColumnDimension('K')->setWidth(15);
                $active_sheet->getColumnDimension('L')->setWidth(15);
                $active_sheet->getColumnDimension('M')->setWidth(15);
                $active_sheet->getColumnDimension('N')->setWidth(15);
                $active_sheet->getColumnDimension('O')->setWidth(15);
                $active_sheet->getColumnDimension('P')->setWidth(15);

                $active_sheet->setCellValue('A1', 'Дата продажи');
                $active_sheet->setCellValue('B1', 'Договор займа');
                $active_sheet->setCellValue('C1', 'Сумма займа');
                $active_sheet->setCellValue('D1', 'ID клиента');
                $active_sheet->setCellValue('E1', 'Номер полиса');
                $active_sheet->setCellValue('F1', 'Продукт');
                $active_sheet->setCellValue('G1', 'ID операции');
                $active_sheet->setCellValue('H1', 'УИД договора');
                $active_sheet->setCellValue('I1', 'ФИО, дата рождения');
                $active_sheet->setCellValue('J1', 'Номер телефона');
                $active_sheet->setCellValue('K1', 'Пол');
                $active_sheet->setCellValue('L1', 'Паспорт');
                $active_sheet->setCellValue('M1', 'Адрес');
                $active_sheet->setCellValue('N1', 'Дата начала / завершения ответственности');
                $active_sheet->setCellValue('O1', 'Страховая сумма');
                $active_sheet->setCellValue('P1', 'Сумма оплаты/Страховая премия');

                $i = 2;
                foreach ($ad_services as $ad_service) {

                    $fio_birth = "$ad_service->lastname $ad_service->firstname $ad_service->patronymic $ad_service->birth";


                    $active_sheet->setCellValue('A' . $i, $ad_service->created);
                    $active_sheet->setCellValue('B' . $i, $ad_service->contract_id);
                    $active_sheet->setCellValue('C' . $i, $ad_service->p2p_amount);
                    $active_sheet->setCellValue('D' . $i, $ad_service->user_id);
                    $active_sheet->setCellValue('E' . $i, $ad_service->number);

                    if ($ad_service->type == 'INSURANCE' && in_array($ad_service->amount_insurance, [200, 400]))
                        $active_sheet->setCellValue('F' . $i, 'Страхование БК');
                    else
                        $active_sheet->setCellValue('F' . $i, $op_type[$ad_service->type]);

                    $active_sheet->setCellValue('G' . $i, $ad_service->id);
                    $active_sheet->setCellValue('H' . $i, $ad_service->uid);
                    $active_sheet->setCellValue('I' . $i, $fio_birth);
                    $active_sheet->setCellValue('J' . $i, $ad_service->phone_mobile);
                    $active_sheet->setCellValue('K' . $i, $gender[$ad_service->gender]);
                    $active_sheet->setCellValue('L' . $i, $ad_service->passport_serial . ' выдан ' . $ad_service->passport_issued . ' ' . date('Y-m-d', strtotime($ad_service->passport_date)) . ' г ' . 'код подразделения ' . $ad_service->subdivision_code);
                    $active_sheet->setCellValue('M' . $i, $ad_service->regAddr);

                    if ($ad_service->start_date) {
                        $active_sheet->setCellValue('N' . $i, $ad_service->start_date . '/' . $ad_service->end_date);
                    } else {
                        $active_sheet->setCellValue('N' . $i, '-');
                    }
                    if ($ad_service->number) {
                        $active_sheet->setCellValue('O' . $i, ($ad_service->amount_contract * 3) . ' руб');
                    }
                    $active_sheet->setCellValueExplicit('P' . $i, $ad_service->amount_insurance, PHPExcel_Cell_DataType::TYPE_NUMERIC);

                    $i++;
                }

                foreach ($card_binding as $card) {

                    if ($ad_service->Regcity) {
                        $address = "$card->Regindex $card->Regcity $card->Regstreet_shorttype $card->Regstreet $card->Reghousing $card->Regroom";

                    } else {
                        $address = "$card->Regindex $card->Reglocality $card->Regstreet_shorttype $card->Regstreet $card->Reghousing $card->Regroom";
                    }

                    $fio_birth = "$card->lastname $card->firstname $card->patronymic $card->birth";


                    $active_sheet->setCellValue('A' . $i, $card->created);
                    $active_sheet->setCellValue('B' . $i, $card->contract_id);
                    $active_sheet->setCellValue('C' . $i, $card->user_id);
                    $active_sheet->setCellValue('D' . $i, $card->number);
                    $active_sheet->setCellValue('E' . $i, $card->description);
                    $active_sheet->setCellValue('F' . $i, $card->id);
                    $active_sheet->setCellValue('G' . $i, $card->uid);
                    $active_sheet->setCellValue('H' . $i, $fio_birth);
                    $active_sheet->setCellValue('I' . $i, $card->phone_mobile);
                    $active_sheet->setCellValue('J' . $i, $gender[$card->gender]);
                    $active_sheet->setCellValue('K' . $i, $card->passport_serial);
                    $active_sheet->setCellValue('L' . $i, $address);

                    if ($card->start_date) {
                        $active_sheet->setCellValue('M' . $i, $card->start_date . '/' . $card->end_date);
                    } else {
                        $active_sheet->setCellValue('M' . $i, '-');
                    }
                    if ($card->number) {
                        $active_sheet->setCellValue('N' . $i, ($card->amount_contract * 3) . ' руб');
                    }
                    $active_sheet->setCellValue('O' . $i, '1 руб');

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir . $filename);

                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }

        }


        return $this->design->fetch('statistics/adservices.tpl');
    }

    private function action_sources()
    {
        $integrations = $this->Integrations->get_integrations();
        $this->design->assign('integrations', $integrations);

        if ($action = $this->request->get('to-do', 'string')) {
            if ($action == 'report') {

                $daterange = $this->request->get('daterange');

                list($from, $to) = explode('-', $daterange);

                $date_from = date('Y-m-d', strtotime($from));
                $date_to = date('Y-m-d', strtotime($to));
                $this->design->assign('from', $from);
                $this->design->assign('to', $to);
                $this->design->assign('date_from', $date_from);
                $this->design->assign('date_to', $date_to);


                $filter = array();
                $filter['date_from'] = $date_from;
                $filter['date_to'] = $date_to;

                foreach ($integrations as $integration) {
                    $filter['integrations'][] = $integration->utm_source;
                }

                $utm_source_filter = $this->request->get('utm_source_filter');
                $utm_medium_filter = $this->request->get('utm_medium_filter');
                $utm_campaign_filter = $this->request->get('utm_campaign_filter');
                $utm_term_filter = $this->request->get('utm_term_filter');
                $utm_content_filter = $this->request->get('utm_content_filter');


                if ($this->request->get('utm_source'))
                    $filter['utm_source'][] = 'utm_source';

                if ($this->request->get('utm_medium'))
                    $filter['utm_source'][] = 'utm_medium';

                if ($this->request->get('utm_campaign'))
                    $filter['utm_source'][] = 'utm_campaign';

                if ($this->request->get('utm_term'))
                    $filter['utm_source'][] = 'utm_term';

                if ($this->request->get('utm_content'))
                    $filter['utm_source'][] = 'utm_content';


                $filtres = [];


                if ($utm_source_filter) {
                    $filter['utm_source_filter'] = $this->request->get('utm_source_filter_val');
                    $filtres['utm_source_filter'] = $filter['utm_source_filter'];
                }

                if ($utm_medium_filter) {
                    $filter['utm_medium_filter'] = $this->request->get('utm_medium_filter_val');
                    $filtres['utm_medium_filter'] = $filter['utm_medium_filter'];
                }


                if ($utm_campaign_filter) {
                    $filter['utm_campaign_filter'] = $this->request->get('utm_campaign_filter_val');
                    $filtres['utm_campaign_filter'] = $filter['utm_campaign_filter'];
                }


                if ($utm_term_filter) {
                    $filter['utm_term_filter'] = $this->request->get('utm_term_filter_val');
                    $filtres['utm_term_filter'] = $filter['utm_term_filter'];
                }


                if ($utm_content_filter) {
                    $filter['utm_content_filter'] = $this->request->get('utm_content_filter_val');
                    $filtres['utm_content_filter'] = $filter['utm_content_filter'];
                }

                $this->design->assign('filtres', $filtres);

                $group_by = $this->request->get('group_by');
                $filter['date_group_by'] = $this->request->get('date_group_by');
                $filter['group_by'] = $group_by;

                $this->design->assign('date_group_by', $filter['date_group_by']);

                $orders = $this->orders->get_orders_by_utm($filter);

                $visits = $this->Visits->search_visits($filter);

                $this->design->assign('group_by', $group_by);

                $months = [
                    '01' => 'Январь',
                    '02' => 'Февраль',
                    '03' => 'Март',
                    '04' => 'Апрель',
                    '05' => 'Май',
                    '06' => 'Июнь',
                    '07' => 'Июль',
                    '08' => 'Август',
                    '09' => 'Сентябрь',
                    '10' => 'Октябрь',
                    '11' => 'Ноябрь',
                    '12' => 'Декабрь',
                ];

                $this->design->assign('months', $months);

                $all_params =
                    [
                        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
                        'visits', 'all_orders', 'CR', 'orders_nk',
                        'orders_pk', 'orders_bk', 'accept_all',
                        'accept_nk', 'accept_pk', 'accept_bk',
                        'ar_all', 'ar_nk', 'ar_pk', 'ar_bk',
                        'reject_all', 'reject_all_prc',
                        'reject_nk', 'reject_nk_prc', 'reject_pk',
                        'reject_pk_prc', 'reject_bk', 'reject_bk_prc',
                        'check_all_summ', 'check_nk_summ', 'check_pk_summ',
                        'check_srch', 'check_srch_nk', 'check_srch_pk',
                        'orders_on_check'
                    ];

                foreach ($all_params as $k => $param) {
                    if ($this->request->get($param) == 1)
                        $all_get_params[$param] = $param;
                }

                $this->design->assign('all_get_params', $all_get_params);

                $months =
                    [
                        1 => 'Январь',
                        2 => 'Февраль',
                        3 => 'Март',
                        4 => 'Апрель',
                        5 => 'Май',
                        6 => 'Июнь',
                        7 => 'Июль',
                        8 => 'Август',
                        9 => 'Сентябрь',
                        10 => 'Октябрь',
                        11 => 'Ноябрь',
                        12 => 'Декабрь'
                    ];

                if ($filter['date_group_by'] == 'issuance') {

                    $contracts = $this->orders->get_orders_contracts_issuance($filter);

                    foreach ($orders as $key => $order) {
                        $orders[$key]->accept_all = 0;
                        $orders[$key]->accept_pk = 0;
                        $orders[$key]->accept_nk = 0;
                        $orders[$key]->accept_bk = 0;
                        foreach ($contracts as $k => $contract) {

                            if ($order->utm_source == $contract->utm_source) {
                                if ($contract->group_date == $order->group_date) {
                                    if ($this->request->get('accept_all') == 1)
                                        $orders[$key]->accept_all = ($contract->accept_all) ? $contract->accept_all : 0;

                                    if ($this->request->get('accept_pk') == 1)
                                        $orders[$key]->accept_pk = ($contract->accept_pk) ? $contract->accept_pk : 0;

                                    if ($this->request->get('accept_nk') == 1)
                                        $orders[$key]->accept_nk = ($contract->accept_nk) ? $contract->accept_nk : 0;

                                    if ($this->request->get('accept_bk') == 1)
                                        $orders[$key]->accept_bk = ($contract->accept_bk) ? $contract->accept_bk : 0;

                                }
                            }
                        }
                    }
                }

                if ($this->request->get('visits') == 1) {
                    foreach ($visits as $visit) {
                        foreach ($orders as $key => $order) {
                            if ($order->utm_source == $visit->utm_source) {
                                $orders[$key]->visits = $visit->count_visit;
                            }
                        }
                    }
                }

                foreach ($orders as $key => $order) {
                    if ($this->request->get('CR') == 1
                        && isset($order->all_orders)
                        && isset($order->visits)
                        && $order->all_orders != 0
                        && $order->visits != 0) {
                        $order->CR = (int)($order->all_orders / $order->visits * 100);
                    } else {
                        $order->CR = 0;
                    }

                    if ($this->request->get('ar_all') == 1
                        && isset($order->accept_all)
                        && isset($order->all_orders)
                        && $order->accept_all != 0
                        && $order->all_orders != 0) {
                        $order->ar_all = (int)($order->accept_all / $order->all_orders * 100);
                    } else {
                        $order->ar_all = 0;
                    }

                    if ($this->request->get('ar_nk') == 1
                        && isset($order->accept_nk)
                        && isset($order->orders_nk)
                        && $order->accept_nk != 0
                        && $order->orders_nk != 0) {
                        $order->ar_nk = (int)($order->accept_nk / $order->orders_nk * 100);
                    } else {
                        $order->ar_nk = 0;
                    }

                    if ($this->request->get('ar_pk') == 1
                        && isset($order->accept_pk)
                        && isset($order->orders_pk)
                        && $order->accept_pk != 0
                        && $order->orders_pk != 0) {
                        $order->ar_pk = (int)($order->accept_pk / $order->orders_pk * 100);
                    } else {
                        $order->ar_pk = 0;
                    }

                    if ($this->request->get('ar_bk') == 1
                        && isset($order->accept_bk)
                        && isset($order->orders_bk)
                        && $order->accept_bk != 0
                        && $order->orders_bk != 0) {
                        $order->ar_bk = (int)($order->accept_bk / $order->orders_bk * 100);
                    } else {
                        $order->ar_bk = 0;
                    }

                    if ($this->request->get('reject_all_prc') == 1
                        && isset($order->reject_all)
                        && isset($order->all_orders)
                        && $order->reject_all != 0
                        && $order->all_orders != 0) {
                        $order->reject_all_prc = (int)($order->reject_all / $order->all_orders * 100);
                    } else {
                        $order->reject_all_prc = 0;
                    }

                    if ($this->request->get('reject_nk_prc') == 1
                        && isset($order->reject_nk)
                        && isset($order->orders_nk)
                        && $order->reject_nk != 0
                        && $order->orders_nk != 0) {
                        $order->reject_nk_prc = (int)($order->reject_nk / $order->orders_nk * 100);
                    } else {
                        $order->reject_nk_prc = 0;
                    }

                    if ($this->request->get('reject_pk_prc') == 1
                        && isset($order->reject_pk)
                        && isset($order->orders_pk)
                        && $order->reject_pk != 0
                        && $order->orders_pk != 0) {
                        $order->reject_pk_prc = (int)($order->reject_pk / $order->orders_pk * 100);
                    } else {
                        $order->reject_pk_prc = 0;
                    }

                    if ($this->request->get('reject_bk_prc') == 1
                        && isset($order->reject_bk)
                        && isset($order->orders_bk)
                        && $order->reject_bk != 0
                        && $order->orders_bk != 0) {
                        $order->reject_bk_prc = (int)($order->reject_bk / $order->orders_bk * 100);
                    } else {
                        $order->reject_bk_prc = 0;
                    }
                }

                $i = 0;
                $results = array();

                foreach ($orders as $order) {
                    foreach ($all_get_params as $param) {
                        if (isset($order->{$param})) {

                            if ($group_by == 'week') {
                                $dto = new DateTime();
                                $dto->setISODate($order->year, $order->group_date);
                                $ret['week_start'] = $dto->format('d.m.Y');
                                $dto->modify('+6 days');
                                $ret['week_end'] = $dto->format('d.m.Y');

                                $key = $ret['week_start'] . ' - ' . $ret['week_end'];
                            } elseif ($group_by == 'month') {
                                $key = $months[$order->group_date];
                            } else {
                                $key = $order->group_date;
                            }

                            $results[$key][$i][$param] = $order->{$param};
                            $results[$key][$i]['visits'] = 0;
                        }
                    }
                    $i++;
                }

                $all_thead =
                    [
                        'utm_source' => 'Источник',
                        'utm_medium' => 'Канал',
                        'utm_campaign' => 'Кампания',
                        'utm_term' => 'Таргетинг',
                        'utm_content' => 'Контент',
                        'visits' => 'Визиты',
                        'all_orders' => 'Заявки',
                        'orders_nk' => 'Заявки НК',
                        'orders_pk' => 'Заявки ПК',
                        'orders_bk' => 'Заявки ПБ',
                        'CR' => 'CR %',
                        'accept_all' => 'Выдано',
                        'accept_nk' => 'Выдано НК',
                        'accept_pk' => 'Выдано ПК',
                        'accept_bk' => 'Выдано ПБ',
                        'ar_all' => 'AR %',
                        'ar_nk' => 'AR НК%',
                        'ar_pk' => 'AR ПК%',
                        'ar_bk' => 'AR ПБ%',
                        'reject_all' => 'Отказы',
                        'reject_all_prc' => 'Отказы %',
                        'reject_nk' => 'Отказы НК',
                        'reject_nk_prc' => 'Отказы НК%',
                        'reject_pk' => 'Отказы ПК',
                        'reject_pk_prc' => 'Отказы ПК%',
                        'reject_bk' => 'Отказы ПБ',
                        'reject_bk_prc' => 'Отказы ПБ%',
                        'check_all_summ' => 'Сумма',
                        'check_nk_summ' => 'Cумма НК',
                        'check_pk_summ' => 'Сумма ПК',
                        'check_srch' => 'СРЧ',
                        'check_srch_nk' => 'СРЧ НК',
                        'check_srch_pk' => 'СРЧ ПК',
                        'orders_on_check' => 'Проверка',
                    ];

                $group_results = array();
                $thead = array();

                foreach ($results as $key => $result) {
                    foreach ($result as $date => $value) {
                        foreach ($all_thead as $k => $head) {
                            if (array_key_exists($k, $value)) {
                                $group_results[$key][$date][$k] = $value[$k];
                                $thead[$k] = $head;
                            }
                        }
                    }
                }

                $this->design->assign('thead', $thead);
                $this->design->assign('results', $group_results);
            }
        }

        return $this->design->fetch('statistics/sources.tpl');
    }

    private function action_conversions()
    {
        if ($action = $this->request->get('to-do', 'string')) {
            if ($action == 'report') {

                $items_per_page = $this->request->get('page_count');

                if (empty($items_per_page))
                    $items_per_page = 25;

                $this->design->assign('page_count', $items_per_page);

                $daterange = $this->request->get('daterange');

                list($from, $to) = explode('-', $daterange);

                $date_from = date('Y-m-d', strtotime($from));
                $date_to = date('Y-m-d', strtotime($to));

                $this->design->assign('from', $from);
                $this->design->assign('to', $to);
                $this->design->assign('date_from', $date_from);
                $this->design->assign('date_to', $date_to);

                $filter = array();
                $filter['date_from'] = $date_from;
                $filter['date_to'] = $date_to;

                if ($this->request->get('utm_source_filter')) {
                    $filter['utm_source_filter'] = $this->request->get('utm_source_filter_val');
                    $filtres['utm_source_filter'] = $filter['utm_source_filter'];
                }

                if ($this->request->get('utm_medium_filter')) {
                    $filter['utm_medium_filter'] = $this->request->get('utm_medium_filter_val');
                    $filtres['utm_medium_filter'] = $filter['utm_medium_filter'];
                }


                if ($this->request->get('utm_campaign_filter')) {
                    $filter['utm_campaign_filter'] = $this->request->get('utm_campaign_filter_val');
                    $filtres['utm_campaign_filter'] = $filter['utm_campaign_filter'];
                }


                if ($this->request->get('utm_term_filter')) {
                    $filter['utm_term_filter'] = $this->request->get('utm_term_filter_val');
                    $filtres['utm_term_filter'] = $filter['utm_term_filter'];
                }


                if ($this->request->get('utm_content_filter')) {
                    $filter['utm_content_filter'] = $this->request->get('utm_content_filter_val');
                    $filtres['utm_content_filter'] = $filter['utm_content_filter'];
                }

                if (isset($filtres))
                    $this->design->assign('filtres', $filtres);


                if ($this->request->get('date_filter') == 1)
                    $filter['issuance'] = 1;

                $date_select = $this->request->get('date_filter');
                $this->design->assign('date_select', $date_select);

                $all_checkbox = [
                    'id' => 'Заявка',
                    'utm_source' => 'Источник',
                    'utm_medium' => 'Канал',
                    'utm_campaign' => 'Кампания',
                    'utm_term' => 'Таргетинг',
                    'click_hash' => 'Контент',
                    'client_status' => 'Статус клиента',
                    'status' => 'Статус заявки',
                    'leadcraft_postback_type' => 'Постбэк'
                ];

                $thead = array();

                $orders_statuses =
                    [
                        0 => 'Принята',
                        1 => 'На рассмотрении',
                        2 => 'Одобрена',
                        3 => 'Отказ',
                        4 => 'Готов к выдаче',
                        5 => 'Займ выдан',
                        6 => 'Не удалось выдать',
                        7 => 'Погашен',
                        8 => 'Отказ клиента',
                    ];

                $this->design->assign('orders_statuses', $orders_statuses);

                foreach ($all_checkbox as $key => $checkbox) {
                    if ($this->request->get($key) == 1) {
                        $filter['select'][] = $key;
                        $thead[$key] = $checkbox;
                    }
                }

                $current_page = $this->request->get('page', 'integer');
                $current_page = max(1, $current_page);
                $this->design->assign('current_page_num', $current_page);

                $orders = $this->orders->get_orders_for_conversions($filter);
                $orders_count = count($orders);

                $filter['page'] = $current_page;
                $filter['limit'] = $items_per_page;
                $orders = $this->orders->get_orders_for_conversions($filter);

                $pages_num = ceil($orders_count / $items_per_page);

                $this->design->assign('total_pages_num', $pages_num);
                $this->design->assign('total_orders_count', $orders_count);

                $this->design->assign('thead', $thead);
                $this->design->assign('orders', $orders);

                if ($this->request->get('download') == 'excel') {

                    unset($filter['page']);
                    unset($filter['limit']);

                    $orders = $this->orders->get_orders_for_conversions($filter);

                    $filename = 'files/reports/conversions.xls';
                    require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                    $excel = new PHPExcel();

                    $excel->setActiveSheetIndex(0);
                    $active_sheet = $excel->getActiveSheet();

                    $active_sheet->setTitle($from . "-" . $to);

                    $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                    $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                    $active_sheet->getColumnDimension('A')->setWidth(25);
                    $active_sheet->setCellValue('A1', 'Дата');

                    $characters = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
                    $checkboxes = array_values($thead);

                    for ($i = 0; $i <= count($thead); $i++) {
                        $active_sheet->getColumnDimension("$characters[$i]")->setWidth(30);
                        $active_sheet->setCellValue("$characters[$i]" . '1', $checkboxes[$i]);
                    }

                    $i = 2;
                    foreach ($orders as $key => $order) {

                        $active_sheet->setCellValue('A' . $i, $order->date);

                        $ch = 0;
                        foreach ($order as $k => $value) {
                            if ($k != 'date') {
                                if ($k == 'status') {
                                    foreach ($orders_statuses as $kii => $status) {
                                        if ($kii == $value) {
                                            $active_sheet->setCellValue("$characters[$ch]" . $i, $status);
                                        }
                                    }
                                } else {
                                    $active_sheet->setCellValue("$characters[$ch]" . $i, $value);
                                }
                                $ch++;
                            }
                        }

                        $i++;
                    }

                    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                    $objWriter->save($this->config->root_dir . $filename);

                    header('Location:' . $this->config->root_url . '/' . $filename);
                    exit;
                }
            }
        }

        return $this->design->fetch('statistics/conversions.tpl');
    }

    private function action_orders()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $filter = array();
            $filter['date_from'] = date('Y-m-d', strtotime($from));
            $filter['date_to'] = date('Y-m-d', strtotime($to));

            $orders = $this->orders->orders_for_risks($filter);
            $orders_statuses = $this->orders->get_statuses();

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(11);

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getDefaultRowDimension()->setRowHeight(55);
            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(18);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(35);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(20);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(15);
            $sheet->getColumnDimension('M')->setWidth(20);

            $styles_cells =
                [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ];

            $sheet->getStyle('A:AC')->applyFromArray($styles_cells);

            $sheet->setCellValue('A1', 'ID заявки');
            $sheet->setCellValue('B1', 'ID клиента');
            $sheet->setCellValue('C1', 'Признак new/old');
            $sheet->setCellValue('D1', 'Дата заявки');
            $sheet->setCellValue('E1', 'ФИО клиента');
            $sheet->setCellValue('F1', 'Дата рождения');
            $sheet->setCellValue('G1', 'Паспорт');
            $sheet->setCellValue('H1', 'Регион');
            $sheet->setCellValue('I1', 'Зона качества клиента');
            $sheet->setCellValue('J1', 'Решение');
            $sheet->setCellValue('K1', 'Причина отказа');
            $sheet->setCellValue('L1', 'ИНН клиента');
            $sheet->setCellValue('M1', 'Запрашиваемая сумма займа');
            $sheet->setCellValue('N1', 'Скоринговый бал');
            $sheet->setCellValue('O1', 'Балл Idx');
            // $sheet->setCellValue('O1', 'Одобренный лимит');
            $sheet->setCellValue('P1', 'Количество активных займов');


            // $sheet->setCellValue('O1', 'pdl_overdue_count');
            // $sheet->setCellValue('P1', 'pdl_npl_limit_share');
            // $sheet->setCellValue('Q1', 'pdl_npl_90_limit_share');
            // $sheet->setCellValue('R1', 'pdl_current_limit_max');
            // $sheet->setCellValue('S1', 'pdl_last_3m_limit');
            // $sheet->setCellValue('T1', 'pdl_last_good_max_limit');
            // $sheet->setCellValue('U1', 'pdl_good_limit');
            // $sheet->setCellValue('V1', 'pdl_prolong_3m_limit');
            // $sheet->setCellValue('W1', 'consum_current_limit_max');
            // $sheet->setCellValue('X1', 'consum_good_limit');

            // $sheet->setCellValue('Y1', 'days_from_last_closed');
            // $sheet->setCellValue('Z1', 'prev_3000_500_paid_count_wo_del');
            // $sheet->setCellValue('AA1', 'sumPayedPercents');
            // $sheet->setCellValue('AB1', 'prev_max_delay');
            // $sheet->setCellValue('AC1', 'last_credit_delay');
            // $sheet->setCellValue('AD1', 'current_overdue_sum');
            // $sheet->setCellValue('AE1', 'closed_to_total_credits_count_share');
            // $sheet->setCellValue('AF1', 'pdl_overdue_count');
            // $sheet->setCellValue('AG1', 'pdl_npl_90_limit_share');

            // $sheet->setCellValue('AH1', 'Общаяя сумма долгов ФССП');
            $sheet->setCellValue('Q1', 'Общаяя сумма долгов ФССП');
            $sheet->setCellValue('R1', 'Количество исполнительных листов');
            $sheet->setCellValue('S1', 'Скорбалл МФО2НБКИ');

            $sheet->setCellValue('T1', 'Всего займов');
            $sheet->setCellValue('U1', 'Всего активных займов');
            $sheet->setCellValue('V1', 'Количество закрытых займов');
            $sheet->setCellValue('W1', 'Количество просроченных займов');
            $sheet->setCellValue('X1', 'Отношение кол-ва займов в текущей просрочке к кол-ву всех актинвх займов');
            
            $sheet->setCellValue('Y1', 'Сумма обязательств по активным займам');
            $sheet->setCellValue('Z1', 'Сумма обязательств по закрытым займам');
            $sheet->setCellValue('AA1', 'Ежемесячный платеж по обязательствам всех активных займов');
            $sheet->setCellValue('AB1', 'Размер просроченной задолженности на дату запроса');
            $sheet->setCellValue('AC1', 'Максимальная просроченная задолженность за текущий год');
            $sheet->setCellValue('AD1', 'Количество микрозаймов за последние 90 дней');
            $sheet->setCellValue('AE1', 'Количество активных микрозаймов');

            $sheet->setCellValue('AF1', 'Количество активных PDL займов');
            $sheet->setCellValue('AG1', 'Количество активных PDL займов с пролонгацией');
            $sheet->setCellValue('AH1', 'Количество активных микрозаймов КЛ');
            $sheet->setCellValue('AI1', 'Количество активных микрозаймов с ошибочным сроком');

            $sheet->setCellValue('AJ1', 'Количество активных кредитных карт');
            $sheet->setCellValue('AK1', 'Количество других активных видов займов');
            
            $sheet->setCellValue('AL1', 'Количество ЗДЗ займов с текущей просрочкой');
            $sheet->setCellValue('AM1', 'Сумма лимитов ЗДЗ займов находящихся в просрочке');
            $sheet->setCellValue('AN1', 'Доля ЗДЗ займов с просрочкой 90+');
            $sheet->setCellValue('AO1', 'Максимальный лимит по ЗДЗ займам');
            $sheet->setCellValue('AP1', 'Сумма открытых за последние 3 месяца ЗДЗ займов');
            $sheet->setCellValue('AQ1', 'Максимальный лимит по последним погашенным без просрочки ЗДЗ займам');
            $sheet->setCellValue('AR1', 'Сумма лимитов по погашенным без просрочки ЗДЗ займам');
            $sheet->setCellValue('AS1', 'Сумма лимитов по продленным без просрочки ЗДЗ займам');
            $sheet->setCellValue('AT1', 'Максимальный лимит по активным потреб кредитам');
            $sheet->setCellValue('AU1', 'Сумма лимитов по погашенным без просрочки потреб кредитам');
            $sheet->setCellValue('AV1', 'Кол-во дней с даты погашения последнего займа во внутренней кредитной истории для данного клиента (т.е. дата подачи новой заявки – дата погашения предыдущего займа)');
            $sheet->setCellValue('AW1', 'Количество займов во внутренней кредитной истории для данного клиента, у которых сумма займа от 3000 руб, сумма погашенных процентов от 500 руб и нет просрочки по займу');
            $sheet->setCellValue('AX1', 'Сумма погашенных процентов по всем займам во внутренней кредитной истории для данного клиента');
            $sheet->setCellValue('AY1', 'Максимальный срок просрочки по всем займам во внутренней кредитной истории для данного клиента');
            $sheet->setCellValue('AZ1', 'Срок просрочки по предыдущему займу во внутренней кредитной истории для данного клиента');
            $sheet->setCellValue('BA1', 'Сумма по полю amtPastDue по всем кредитам из отчета НБКИ');
            $sheet->setCellValue('BB1', 'Сумма полю creditLimit по всем closed кредитам / сумма полю creditLimit по всем кредитам');


            $sheet->setCellValue('BC1', 'Кол-во дней с даты погашения последнего займа во внутренней кредитной истории для данного клиента');
            $sheet->setCellValue('BD1', 'Кол-во займов во внутренней кредитной истории для данного клиента, у которых сумма займа>=3000 руб И сумма погашенных процентов>=500 руб И срок просрочки по займу=0');
            $sheet->setCellValue('BE1', 'Сумма погашенных процентов по всем займам во внутренней кредитной истории для данного клиента');
            $sheet->setCellValue('BF1', 'Максимальный срок просрочки по всем займам во внутренней кредитной истории для данного клиента');
            $sheet->setCellValue('BG1', 'Срок просрочки по предыдущему займу во внутренней кредитной истории для данного клиента');
            

            $i = 2;
            foreach ($orders as $key => $order) {
                $order->scoreballs = $this->NbkiScoreballs->get($order->order_id);

                if (empty($order->scoreballs)) {
                    // continue;
                } else {
                    $order->scoreballs->variables = json_decode($order->scoreballs->variables, true);
                    $order->scoreballs->variables['ball'] = $order->scoreballs->ball;
                    $order->scoreballs = $order->scoreballs->variables;
                }

                $order->idx = $this->scorings->get_idx_scoring($order->order_id);

                if (empty($order->idx)) {
                    // continue;
                } else
                    $order->idx = $order->idx->body;

                $order->status = $orders_statuses[$order->status];

                // $nbki = $this->scorings->get_type_scoring($order->order_id, 'nbki');
                $nbki = $this->scorings->get_type_scoring_last($order->order_id, 'nbki');
                if (empty($nbki)) {
                    // continue;
                }
                $nbki = unserialize($nbki->body);
                if (empty($nbki)) {
                    // continue;
                }

                $user = $this->users->get_user($order->user_id);

                $sheet->setCellValue('A' . $i, $order->order_id);
                $sheet->setCellValue('B' . $i, $order->user_id);
                $sheet->setCellValue('C' . $i, $order->client_status);
                $sheet->setCellValue('D' . $i, $order->date);
                $sheet->setCellValue('E' . $i, $order->lastname . ' ' . $order->firstname . ' ' . $order->patronymic);
                $sheet->setCellValue('F' . $i, $order->birth);
                $sheet->setCellValue('G' . $i, $order->passport_serial);
                // $sheet->setCellValue('H' . $i, $order->region);

                // $result_scorings = $this->scorings->get_scorings(array('order_id' => $order->order_id, 'type' => 'location'));
                // $result_scorings_end = end($result_scorings);
                // $result_scorings_end_arr = explode(" ", $result_scorings_end->string_result);
                // $region_type_arr_sliced = array_slice($result_scorings_end_arr, -2);
                // $region_type = implode(" ", $region_type_arr_sliced);

                // // Добавление в таблицу адресов зон<
                // if(!$region_type){
                    $scoring_type = $this->scorings->get_type('location');

                    $order->region = trim($order->region);
                    $order_Regregion = $order->region;
                    if(mb_substr($order->region, -2) == " г" ||
                    mb_substr($order->region, 0, 2) == "г " ||
                    mb_substr($order->region, -4) == " обл" ||
                    mb_substr($order->region, -5) == " обл." ||
                    mb_substr($order->region, -8) == " область" ||
                    mb_substr($order->region, -8) == " ОБЛАСТЬ" ||
                    mb_substr($order->region, -5) == " край" ||
                    mb_substr($order->region, -5) == " Край" ||
                    mb_substr($order->region, -11) == " республика" ||
                    mb_substr($order->region, -11) == " Республика" ||
                    mb_substr($order->region, -5) == " Респ" ||
                    mb_substr($order->region, 0, 5) == "Респ " ||
                    mb_substr($order->region, 0, 11) == "Республика " ){
                        $order_Regregion = str_replace(["г ", " г", " область", " ОБЛАСТЬ", " обл.", " обл", " край", " Край", " республика", " Республика", " Респ", "Респ ", "Республика "], "", $order->region);
                    }
                    // $exception_regions = array_map('trim', explode(',', $scoring_type->params['regions']));
                    // if(isset(explode(' ', $order_Regregion)[1]) && mb_strtolower(explode(' ', $order_Regregion)[1]) == 'обл'){
                    //     $order_Regregion = explode(' ', $order_Regregion)[0];
                    // }

                //     $green = 0;
                //     $red = 0;
                //     $yellow = 0;
                //     $gray = 0;
                //     $stop = 0;

                //     $geen_regions = ['москва', 'удмуртская', 'мордовия', 'ярославская', 'архангельская', 'московская', 'башкортостан', 'новосибирская', 'орловская', 'хабаровский', 'костромская', 'новгородская', 'тверская', 'астраханская', 'рязанская', 'нижегородская', 'владимирская', 'томская', 'пензенская', 'камчатский', 'белгордская'];
                //     $green = in_array(mb_strtolower(trim($order_Regregion), 'utf8'), $geen_regions);

                //     $red_regions = array_map('trim', explode(',', $scoring_type->params['red-regions']));
                //     $red = in_array(mb_strtolower(trim($order_Regregion), 'utf8'), $red_regions);

                //     $yellow_regions = array_map('trim', explode(',', $scoring_type->params['yellow-regions']));
                //     $yellow = in_array(mb_strtolower(trim($order_Regregion), 'utf8'), $yellow_regions);
                    
                //     $gray_regions = array_map('trim', explode(',', $scoring_type->params['gray-regions']));
                //     $gray = in_array(mb_strtolower(trim($order_Regregion), 'utf8'), $gray_regions);

                //     $stop_regions = array_map('trim', explode(',', $scoring_type->params['regions']));
                //     $stop = in_array(mb_strtolower(trim($order_Regregion), 'utf8'), $stop_regions);

                //     if($yellow){
                //         $region_type = "ЖЕЛТАЯ ЗОНА";
                //     }
                //     elseif ($red) {
                //         $region_type = "КРАСНАЯ ЗОНА";
                //     }
                //     elseif ($gray) {
                //         $region_type = "СЕРАЯ ЗОНА";
                //     }
                //     elseif ($green) {
                //         $region_type = "ЗЕЛЕНАЯ ЗОНА";
                //     }
                //     elseif ($stop) {
                //         $region_type = "ОЧЕНЬ КРАСНАЯ ЗОНА";
                //     }
                //     else{
                //         $region_type = "---";
                //     }
                // }

                // $regaddress['zone'] = $region_type;
                // $this->Addresses->update_address($order->region_id, $regaddress);

                // // if ($region_type == "---") {
                // //     echo "<hr>";
                // //     echo "<hr>";
                // //     echo $order->date." - ".$order->user_id." - ".$order->region_id." - ".$order->region;
                // //     echo "<hr>";
                // // }
                // // Добавление в таблицу адресов зон>

                $sheet->setCellValue('H' . $i, $order_Regregion);
                $region_type = $order->zone;
                $sheet->setCellValue('I' . $i, $region_type);
                $sheet->setCellValue('J' . $i, $order->status);
                $sheet->setCellValue('K' . $i, $order->reject_reason);
                $sheet->setCellValue('L' . $i, ' '.(string)$user->inn);
                $sheet->setCellValue('M' . $i, $order->amount);
                $sheet->setCellValue('N' . $i, $order->scoreballs['ball']);
                $sheet->setCellValue('O' . $i, $order->idx);
                // $sheet->setCellValue('O' . $i, $order->scoreballs['limit']);
                $sheet->setCellValue('P' . $i, $nbki['number_of_active'][0]);

                // if ($order->client_status == 'new') {
                //     $sheet->setCellValue('O' . $i, $order->scoreballs['pdl_overdue_count']);
                //     $sheet->setCellValue('P' . $i, $order->scoreballs['pdl_npl_limit_share']);
                //     $sheet->setCellValue('Q' . $i, $order->scoreballs['pdl_npl_90_limit_share']);
                //     $sheet->setCellValue('R' . $i, $order->scoreballs['pdl_current_limit_max']);
                //     $sheet->setCellValue('S' . $i, $order->scoreballs['pdl_last_3m_limit']);
                //     $sheet->setCellValue('T' . $i, $order->scoreballs['pdl_last_good_max_limit']);
                //     $sheet->setCellValue('U' . $i, $order->scoreballs['pdl_good_limit']);
                //     $sheet->setCellValue('V' . $i, $order->scoreballs['pdl_prolong_3m_limit']);
                //     $sheet->setCellValue('W' . $i, $order->scoreballs['consum_current_limit_max']);
                //     $sheet->setCellValue('X' . $i, $order->scoreballs['consum_good_limit']);
                // } else {
                //     $sheet->setCellValue('Y' . $i, $order->scoreballs['days_from_last_closed']);
                //     $sheet->setCellValue('Z' . $i, $order->scoreballs['prev_3000_500_paid_count_wo_del']);
                //     $sheet->setCellValue('AA' . $i, $order->scoreballs['sumPayedPercents']);
                //     $sheet->setCellValue('AB' . $i, $order->scoreballs['prev_max_delay']);
                //     $sheet->setCellValue('AC' . $i, $order->scoreballs['last_credit_delay']);
                //     $sheet->setCellValue('AD' . $i, $order->scoreballs['current_overdue_sum']);
                //     $sheet->setCellValue('AE' . $i, $order->scoreballs['closed_to_total_credits_count_share']);
                //     $sheet->setCellValue('AF' . $i, $order->scoreballs['pdl_overdue_count']);
                //     $sheet->setCellValue('AG' . $i, $order->scoreballs['pdl_npl_90_limit_share']);
                // }

                // $fsspScor = ScoringsORM::query()->where('order_id', '=', $order->order_id)->where('type', '=', 'fssp')->first();
                $fsspScor = $this->scorings->get_type_scoring_last($order->order_id, 'fssp');
                if ($fsspScor) {
                    $fsspParams = unserialize($fsspScor->body);

                    if (isset($fsspParams['expSum'])) 
                        $sheet->setCellValue('Q' . $i, $fsspParams['expSum']);
                    if (isset($fsspParams['expCount'])) 
                        $sheet->setCellValue('R' . $i, $fsspParams['expCount']);
                }

                // $nbkiScor = ScoringsORM::query()->where('order_id', '=', $order->order_id)->where('type', '=', 'nbki')->first();
                $nbkiScor = $this->scorings->get_type_scoring_last($order->order_id, 'nbki');
                
                if ($nbkiScor) {
                    $nbkiParams = unserialize($nbkiScor->body);
                    
                    if (isset($nbkiParams['score'])) 
                        $sheet->setCellValue('S' . $i, $nbkiParams['score']);
                    if (isset($nbkiParams['number_of_active']) && isset($nbkiParams['count_of_closed'])) {
                        if (is_array($nbkiParams['number_of_active'])) 
                            $var_number_of_active = $nbkiParams['number_of_active'][0];
                        else
                            $var_number_of_active = $nbkiParams['number_of_active'];
                        
                        if (is_array($nbkiParams['count_of_closed'])) 
                            $var_count_of_closed = $nbkiParams['count_of_closed'][0];
                        else
                            $var_count_of_closed = $nbkiParams['count_of_closed'];
                        
                        $sheet->setCellValue('T' . $i, ($var_number_of_active + $var_count_of_closed));
                    }
                    if (isset($nbkiParams['number_of_active'])) {
                        if (is_array($nbkiParams['number_of_active'])) 
                            $sheet->setCellValue('U' . $i, $nbkiParams['number_of_active'][0]);
                        else
                            $sheet->setCellValue('U' . $i, $nbkiParams['number_of_active']);
                    }
                    if (isset($nbkiParams['count_of_closed'])) {
                        if (is_array($nbkiParams['count_of_closed'])) 
                            $sheet->setCellValue('V' . $i, $nbkiParams['count_of_closed'][0]);
                        else
                            $sheet->setCellValue('V' . $i, $nbkiParams['count_of_closed']);
                    }
                    if (isset($nbkiParams['count_of_overdue'])) {
                        if (is_array($nbkiParams['count_of_overdue'])) 
                            $sheet->setCellValue('W' . $i, $nbkiParams['count_of_overdue'][0]);
                        else
                            $sheet->setCellValue('W' . $i, $nbkiParams['count_of_overdue']);
                    }
                    if (isset($nbkiParams['share_of_overdue_by_active']) && !is_null($nbkiParams['share_of_overdue_by_active'])){
                        if (is_array($nbkiParams['share_of_overdue_by_active'])) 
                            $sheet->setCellValue('X' . $i, $nbkiParams['share_of_overdue_by_active'][0]);
                        else
                            $sheet->setCellValue('X' . $i, $nbkiParams['share_of_overdue_by_active']);
                    }
                    
                    if (isset($nbkiParams['extra_scoring']['active_loans_credit_limit_sum'])) 
                        $sheet->setCellValue('Y' . $i, $nbkiParams['extra_scoring']['active_loans_credit_limit_sum']);
                    if (isset($nbkiParams['extra_scoring']['closed_loans_credit_limit_sum'])) 
                        $sheet->setCellValue('Z' . $i, $nbkiParams['extra_scoring']['closed_loans_credit_limit_sum']);
                    if (isset($nbkiParams['extra_scoring']['monthly_active_loans_payment_sum'])) 
                        $sheet->setCellValue('AA' . $i, $nbkiParams['extra_scoring']['monthly_active_loans_payment_sum']);
                    if (isset($nbkiParams['extra_scoring']['overdue_amount_sum'])) 
                        $sheet->setCellValue('AB' . $i, $nbkiParams['extra_scoring']['overdue_amount_sum']);
                    if (isset($nbkiParams['extra_scoring']['current_year_max_overdue_amount'])) 
                        $sheet->setCellValue('AC' . $i, $nbkiParams['extra_scoring']['current_year_max_overdue_amount']);
                    if (isset($nbkiParams['extra_scoring']['microloans_over_last_90_days_count'])) 
                        $sheet->setCellValue('AD' . $i, $nbkiParams['extra_scoring']['microloans_over_last_90_days_count']);
                    if (isset($nbkiParams['extra_scoring']['active_microloan_count'])) 
                        $sheet->setCellValue('AE' . $i, $nbkiParams['extra_scoring']['active_microloan_count']);
                    
                    if (isset($nbkiParams['extra_scoring']['active_pay_day_loans_count'])) 
                        $sheet->setCellValue('AF' . $i, $nbkiParams['extra_scoring']['active_pay_day_loans_count']);
                    if (isset($nbkiParams['extra_scoring']['active_pay_day_loans_with_extension_count'])) 
                        $sheet->setCellValue('AG' . $i, $nbkiParams['extra_scoring']['active_pay_day_loans_with_extension_count']);
                    if (isset($nbkiParams['extra_scoring']['active_credit_lines_count'])) 
                        $sheet->setCellValue('AH' . $i, $nbkiParams['extra_scoring']['active_credit_lines_count']);
                    if (isset($nbkiParams['extra_scoring']['active_microloans_with_wrong_term_days_count'])) 
                        $sheet->setCellValue('AI' . $i, $nbkiParams['extra_scoring']['active_microloans_with_wrong_term_days_count']);

                    if (isset($nbkiParams['extra_scoring']['active_credit_cards_count'])) 
                        $sheet->setCellValue('AJ' . $i, $nbkiParams['extra_scoring']['active_credit_cards_count']);
                    if (isset($nbkiParams['extra_scoring']['active_other_loans_count'])) 
                        $sheet->setCellValue('AK' . $i, $nbkiParams['extra_scoring']['active_other_loans_count']);
                    
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_overdue_count'])) 
                        $sheet->setCellValue('AL' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_overdue_count']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_limit_share'])) 
                        $sheet->setCellValue('AM' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_limit_share']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_90_limit_share'])) 
                        $sheet->setCellValue('AN' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_npl_90_limit_share']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_current_limit_max'])) 
                        $sheet->setCellValue('AO' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_current_limit_max']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_3m_limit'])) 
                        $sheet->setCellValue('AP' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_3m_limit']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_good_max_limit'])) 
                        $sheet->setCellValue('AQ' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_last_good_max_limit']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_good_limit'])) 
                        $sheet->setCellValue('AR' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_good_limit']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['pdl_prolong_3m_limit'])) 
                        $sheet->setCellValue('AS' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['pdl_prolong_3m_limit']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['consum_current_limit_max'])) 
                        $sheet->setCellValue('AT' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['consum_current_limit_max']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['consum_good_limit'])) 
                        $sheet->setCellValue('AU' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['consum_good_limit']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['days_from_last_closed'])) 
                        $sheet->setCellValue('AV' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['days_from_last_closed']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_3000_500_paid_count_wo_del'])) 
                        $sheet->setCellValue('AW' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['prev_3000_500_paid_count_wo_del']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_paid_percent_sum'])) 
                        $sheet->setCellValue('AX' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['prev_paid_percent_sum']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['prev_max_delay'])) 
                        $sheet->setCellValue('AY' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['prev_max_delay']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['last_credit_delay'])) 
                        $sheet->setCellValue('AZ' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['last_credit_delay']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['current_overdue_sum'])) 
                        $sheet->setCellValue('BA' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['current_overdue_sum']);
                    if (isset($nbkiParams['barents_scoring']['client_scoring_data']['closed_to_total_credits_count_share'])) 
                        $sheet->setCellValue('BB' . $i, $nbkiParams['barents_scoring']['client_scoring_data']['closed_to_total_credits_count_share']);
                                        
                }

                $contracts = $this->contracts->get_contracts(array('user_id' => $order->user_id, 'status' => 3));
                    
                $contract_close_date = '';
                $count_contracts_3000_500_0 = 0;
                $all_percents_summ = 0;
                $all_peni_summ = 0;
                $period_peni_biggest = 0;
                $period_peni_last = 0;

                foreach ($contracts as $contract) {
                    if (date('Y-m-d', strtotime($contract->inssuance_date)) > date('Y-m-d', strtotime($from)))
                        continue;
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


                $sheet->setCellValue('BC' . $i, $delay_last_contract);
                $sheet->setCellValue('BD' . $i, $count_contracts_3000_500_0);
                $sheet->setCellValue('BE' . $i, $all_percents_summ);
                $sheet->setCellValue('BF' . $i, $period_peni_biggest);
                $sheet->setCellValue('BG' . $i, $period_peni_last);

                $i++;
            }

            $filename = 'Orders.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($this->config->root_dir . $filename);
            header('Location:' . $this->config->root_url . '/' . $filename);
            exit;
        }

        return $this->design->fetch('statistics/orders.tpl');
    }

    private function action_leadgens()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $items_per_page = $this->request->get('page_count');

            if (empty($items_per_page))
                $items_per_page = 25;

            $this->design->assign('page_count', $items_per_page);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            $filter = array();
            $filter['date_from'] = $date_from;
            $filter['date_to'] = $date_to;
            $filter['integration_filter'] = $this->request->get('integration_filter');

            $this->design->assign('integration_filter', $filter['integration_filter']);

            $current_page = $this->request->get('page', 'integer');
            $current_page = max(1, $current_page);
            $this->design->assign('current_page_num', $current_page);

            $count = $this->orders->count_leadgens($filter);

            $filter['page'] = $current_page;
            $filter['limit'] = $items_per_page;

            $orders = $this->orders->leadgens($filter);

            $orders_statuses = $this->orders->get_statuses();

            if (!empty($orders)) {
                foreach ($orders as $order){
                    $order->status = $orders_statuses[$order->status];
                    $user = $this->users->get_user($order->user_id);
                    $order->user = $user;

                    $faktaddress = $this->Addresses->get_address($user->faktaddress_id);
                    $order->region = $faktaddress->region;
                    $order->zone = $faktaddress->zone;
                    $order->type_pk = $this->contracts->type_pk_order($order);
                }

                $this->design->assign('orders', $orders);
            }

            $pages_num = ceil($count / $items_per_page);

            $this->design->assign('total_pages_num', $pages_num);
            $this->design->assign('total_orders_count', $count);

            if ($this->request->get('download') == 'excel') {

                unset($filter['page']);
                unset($filter['limit']);

                $orders = $this->orders->leadgens($filter);

                if (!empty($orders)) {
                    foreach ($orders as $order){
                        $order->status = $orders_statuses[$order->status];
                        $user = $this->users->get_user($order->user_id);
                        $order->user = $user;

                        $faktaddress = $this->Addresses->get_address($user->faktaddress_id);
                        $order->region = $faktaddress->region;
                        $order->zone = $faktaddress->zone;
                        $order->type_pk = $this->contracts->type_pk_order($order);
                    }
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultRowDimension()->setRowHeight(20);
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(35);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(20);
                $sheet->getColumnDimension('J')->setWidth(20);

                $sheet->setCellValue('A1', 'Номер заявки');
                $sheet->setCellValue('B1', 'Номер контракта');
                $sheet->setCellValue('C1', 'ФИО');
                $sheet->setCellValue('D1', 'Дата рождения');
                $sheet->setCellValue('E1', 'ИНН');
                $sheet->setCellValue('F1', 'Статус');
                $sheet->setCellValue('G1', 'Лидогенератор');
                $sheet->setCellValue('H1', 'ID клика');
                $sheet->setCellValue('I1', 'ID вебмастера');
                $sheet->setCellValue('J1', 'Регион выдачи');
                $sheet->setCellValue('K1', 'Зона качества');
                $sheet->setCellValue('L1', 'ПК/НК');
                $sheet->setCellValue('M1', 'Тип ПК');
                $sheet->setCellValue('N1', 'Дата создания');
                $sheet->setCellValue('O1', 'Сумма заявки');
                $sheet->setCellValue('P1', 'Сумма контракта');
                $sheet->setCellValue('Q1', 'Ставка');

                $i = 2;

                foreach ($orders as $order) {

                    $sheet->setCellValue('A' . $i, $order->id);
                    $sheet->setCellValue('B' . $i, $order->number);
                    $sheet->setCellValue('C' . $i, $order->user->lastname.' '.$order->user->firstname.' '.$order->user->patronymic);
                    $sheet->setCellValue('D' . $i, $order->user->birth);
                    $sheet->setCellValue('E' . $i, $order->user->inn);
                    $sheet->setCellValue('F' . $i, $order->status);
                    $sheet->setCellValue('G' . $i, $order->utm_source);
                    $sheet->setCellValue('H' . $i, $order->click_hash);
                    $sheet->setCellValue('I' . $i, $order->webmaster_id);
                    $sheet->setCellValue('J' . $i, $order->region);
                    $sheet->setCellValue('K' . $i, $order->zone);
                    if ($order->client_status == 'pk')
                        $client_status = 'ПК';
                    if ($order->client_status == 'nk')
                        $client_status = 'НК';
                    if ($order->client_status == 'crm')
                        $client_status = 'ПК CRM';
                    if ($order->client_status == 'rep')
                        $client_status = 'НК';
                    $sheet->setCellValue('L' . $i, $client_status);
                    $sheet->setCellValue('M' . $i, $order->type_pk);
                    $sheet->setCellValue('N' . $i, date('d.m.Y', strtotime($order->date)));
                    $sheet->setCellValue('O' . $i, $order->amount);
                    $sheet->setCellValue('P' . $i, $order->con_amount);
                    $sheet->setCellValue('Q' . $i, 0.00);

                    $i++;
                }

                $filename = 'Leadgens.xlsx';
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($this->config->root_dir . $filename);
                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }
        }

        $integrations = IntegrationsORM::get();
        $this->design->assign('integrations', $integrations);

        return $this->design->fetch('statistics/leadgens.tpl');
    }

    private function action_ip_rejects()
    {
        // $reasons = array();
        // foreach ($this->reasons->get_reasons() as $reason)
        //     $reasons[$reason->id] = $reason;
        // $this->design->assign('reasons', $reasons);


        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $date_from = date('Y-m-d', strtotime($from));
            $date_to = date('Y-m-d', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

                // $query_reason = '';
                // if ($filter_reason = $this->request->get('reason_id')) {
                //     if ($filter_reason != 'all') {
                //         $query_reason = $this->db->placehold("AND o.reason_id = ?", (int)$filter_reason);
                //     }

                //     $this->design->assign('filter_reason', $filter_reason);
                // }

            $query = $this->db->placehold("
                SELECT
                    o.id AS order_id,
                    o.ip,
                    u.lastname,
                    u.firstname,
                    u.patronymic,
                    u.id as user_id
                    
                FROM __orders AS o
                LEFT JOIN __users AS u ON u.id = o.user_id
                WHERE o.status IN (3, 8)
                AND DATE(o.date) >= ?
                AND DATE(o.date) <= ?
                GROUP BY order_id
            ", $date_from, $date_to);
            // echo $query;
            // die;
            $this->db->query($query);

            $orders = array();
            foreach ($this->db->results() as $o){
                $orders[$o->order_id] = $o;
                $promocode = $this->promocodes->get($o->promocode_id);
                $orders[$o->order_id]->promocode = $promocode->code;
            }

            if ($this->request->get('download') == 'excel') {
                $managers = array();
                foreach ($this->managers->get_managers() as $m)
                    $managers[$m->id] = $m;

                $filename = 'files/reports/ip_rejects.xls';
                require $this->config->root_dir . 'PHPExcel/Classes/PHPExcel.php';

                $excel = new PHPExcel();

                $excel->setActiveSheetIndex(0);
                $active_sheet = $excel->getActiveSheet();

                $active_sheet->setTitle("Выдачи " . $from . "-" . $to);

                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
                $excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(15);
                $active_sheet->getColumnDimension('C')->setWidth(45);
                $active_sheet->getColumnDimension('D')->setWidth(20);

                $active_sheet->setCellValue('A1', 'Дата');
                $active_sheet->setCellValue('B1', 'Заявка');
                $active_sheet->setCellValue('C1', 'ФИО');
                $active_sheet->setCellValue('D1', 'IP');

                $i = 2;
                foreach ($orders as $contract) {

                    $successTransaction = empty($contract->checked) ? ' (провал)' : ' успех';

                    $active_sheet->setCellValue('A' . $i, date('d.m.Y', strtotime($contract->date)));
                    $active_sheet->setCellValue('B' . $i, $contract->order_id);
                    $active_sheet->setCellValue('C' . $i, $contract->lastname . ' ' . $contract->firstname . ' ' . $contract->patronymic);
                    $active_sheet->setCellValue('D' . $i, $contract->ip);

                    $i++;
                }

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');

                $objWriter->save($this->config->root_dir . $filename);

                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }


            $this->design->assign('orders', $orders);
        }

        return $this->design->fetch('statistics/ip_rejects.tpl');
    }

    private function action_requests_contracts()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $items_per_page = $this->request->get('page_count');

            if (empty($items_per_page))
                $items_per_page = 25;

            $this->design->assign('page_count', $items_per_page);

            $date_from = date('Y-m-d 00:00:00', strtotime($from));
            $date_to = date('Y-m-d 23:59:59', strtotime($to));

            $this->design->assign('date_from', $date_from);
            $this->design->assign('date_to', $date_to);
            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            $filter = array();

            $current_page = $this->request->get('page', 'integer');
            $current_page = max(1, $current_page);
            $this->design->assign('current_page_num', $current_page);

            $query = OrdersORM::query();

            $query->whereBetween('reject_date', [$date_from, $date_to]);
            $cQuery = ContractsORM::query();
            $cQuery->orWhereBetween('accept_date', [$date_from, $date_to]);

            $orderIds = [];
            foreach ($cQuery->get() as $contract) {
                $orderIds[] = $contract->order_id;
            }
            foreach ($query->get() as $order) {
                $orderIds[] = $order->id;
            }
            $query = OrdersORM::query();
            $query->whereIn('id', $orderIds);

            $count = $query->count();

            $data = $query->limit($items_per_page)->offset(($current_page-1) * $items_per_page);
            $orders = $data->get();

            $orders_statuses = $this->orders->get_statuses();
            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $order->manager = ManagerORM::where('id', '=', $order->manager_id)->first();
                    $order->contract = ContractsORM::where('id', '=', $order->contract_id)->first();
                    $order->client = UsersORM::where('id', '=', $order->user_id)->first();
                    $order->status = $orders_statuses[$order->status];
                    $promocode = PromocodesORM::where('id', '=', $order->promocode_id)->first();
                    $insurance = InsurancesORM::where('order_id', '=', $order->order_id)->first();
                    $order->insurance_summ = $insurance ? $insurance->amount : 0;
                    $order->promocode = $promocode ? $promocode->code : '';
                    $order->payed_summ = round(OperationsORM::query()
                        ->where('order_id', '=', $order->order_id)
                        ->where('type', '=', 'PAY')
                        ->sum('amount'), 2);
                    $order->total_amt = $order->amount;
                    if ($insurance) {
                        $order->total_amt += $insurance->amount;
                    }
                    if ($order->contract && $order->contract->loan_body_summ) {
                        $order->total_amt = $order->contract->loan_body_summ;
                    }
                }
                $this->design->assign('orders', $orders);
            }

            $pages_num = ceil($count / $items_per_page);

            $this->design->assign('total_pages_num', $pages_num);
            $this->design->assign('total_orders_count', $count);

            if ($this->request->get('download') == 'excel') {

                unset($filter['page']);
                unset($filter['limit']);

                $query = OrdersORM::query();

                $query->whereBetween('reject_date', [$date_from, $date_to]);
                $cQuery = ContractsORM::query();
                $cQuery->orWhereBetween('accept_date', [$date_from, $date_to]);

                $orderIds = [];
                foreach ($cQuery->get() as $contract) {
                    $orderIds[] = $contract->order_id;
                }
                foreach ($query->get() as $order) {
                    $orderIds[] = $order->id;
                }
                $query = OrdersORM::query();
                $query->whereIn('id', $orderIds);
                $orders = $query->get();

                $orders_statuses = $this->orders->get_statuses();
                if (!empty($orders)) {
                    foreach ($orders as $order) {
                        $order->manager = ManagerORM::where('id', '=', $order->manager_id)->first();
                        $order->contract = ContractsORM::where('id', '=', $order->contract_id)->first();
                        $order->client = UsersORM::where('id', '=', $order->user_id)->first();
                        $order->status = $orders_statuses[$order->status];
                        $promocode = PromocodesORM::where('id', '=', $order->promocode_id)->first();
                        $insurance = InsurancesORM::where('order_id', '=', $order->order_id)->first();
                        $order->insurance_summ = $insurance ? $insurance->amount : 0;
                        $order->promocode = $promocode ? $promocode->code : '';
                        $order->payed_summ = round(OperationsORM::query()
                            ->where('order_id', '=', $order->order_id)
                            ->where('type', '=', 'PAY')
                            ->sum('amount'), 2);
                        $order->total_amt = $order->amount;
                        if ($insurance) {
                            $order->total_amt += $insurance->amount;
                        }
                        if ($order->contract && $order->contract->loan_body_summ) {
                            $order->total_amt = $order->contract->loan_body_summ;
                        }
                    }
                }


                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getDefaultRowDimension()->setRowHeight(20);
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(35);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(70);
                $sheet->getColumnDimension('K')->setWidth(15);
                $sheet->getColumnDimension('L')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(10);
                $sheet->getColumnDimension('N')->setWidth(15);
                $sheet->getColumnDimension('O')->setWidth(20);
                $sheet->getColumnDimension('P')->setWidth(20);
                $sheet->getColumnDimension('Q')->setWidth(25);
                $sheet->getColumnDimension('R')->setWidth(10);
                $sheet->getColumnDimension('S')->setWidth(10);

                $sheet->getColumnDimension('T')->setWidth(10);
                $sheet->getColumnDimension('U')->setWidth(10);
                $sheet->getColumnDimension('V')->setWidth(10);
                $sheet->getColumnDimension('W')->setWidth(10);
                $sheet->getColumnDimension('X')->setWidth(10);
                $sheet->getColumnDimension('Y')->setWidth(10);
                $sheet->getColumnDimension('Z')->setWidth(10);
                $sheet->getColumnDimension('AA')->setWidth(10);
                $sheet->getColumnDimension('AB')->setWidth(10);

                $sheet->setCellValue('A1', 'Дата');
                $sheet->setCellValue('B1', 'Договор');
                $sheet->setCellValue('C1', 'ФИО');
                $sheet->setCellValue('D1', 'Телефон');
                $sheet->setCellValue('E1', 'Почта');
                $sheet->setCellValue('F1', 'Сумма');
                $sheet->setCellValue('G1', 'ПК/НК');
                $sheet->setCellValue('H1', 'Менеджер');
                $sheet->setCellValue('I1', 'Статус');
                $sheet->setCellValue('J1', 'Причина отказа');
                $sheet->setCellValue('K1', 'Промокод');
                $sheet->setCellValue('L1', 'Дата возврата');
                $sheet->setCellValue('M1', 'ПДН');
                $sheet->setCellValue('N1', 'Дней займа');
                $sheet->setCellValue('O1', 'Дата факт возврата');
                $sheet->setCellValue('P1', 'Сумма выплачено');
                $sheet->setCellValue('Q1', 'Источник привлечения');
                $sheet->setCellValue('R1', 'ID заявки');
                $sheet->setCellValue('S1', 'ID клиента');

                $sheet->setCellValue('T1', 'Всего активных кредитов количество');
                $sheet->setCellValue('U1', 'Всего активных кредитов сумма');
                $sheet->setCellValue('V1', 'Всего погашено кредитов количество');
                $sheet->setCellValue('W1', 'Всего погашено кредитов сумма');
                $sheet->setCellValue('Z1', 'Ежемесячный платеж по кредитам');
                $sheet->setCellValue('Y1', 'Размер просроченной задолженности на сегодня');
                $sheet->setCellValue('Z1', 'Максимальная просрочка за последний год');
                $sheet->setCellValue('AA1', 'Количество микрозаймов за последние 3 месяца');
                $sheet->setCellValue('AB1', 'Количество активных микрозаймов');

                $i = 2;

                foreach ($orders as $order) {
                    try {
                        $pk = '';
                        if ($order->client_status) {
                            if ($order->client_status == 'pk') {
                                $pk = 'ПК';
                            } elseif($order->client_status == 'crm') {
                                $pk = 'ПК CRM';
                            } elseif($order->client_status == 'rep') {
                                $pk = 'Новая';
                            } elseif($order->client_status == 'nk') {
                                $pk = 'Новая';
                            }
                        }
                        $sheet->setCellValue('A' . $i, $order->date);
                        $sheet->setCellValue('B' . $i, $order->contract->number);
                        $sheet->setCellValue('C' . $i, "{$order->client->lastname} {$order->client->firstname} {$order->client->patronymic}");
                        $sheet->setCellValue('D' . $i, $order->client->phone_mobile);
                        $sheet->setCellValue('E' . $i, $order->client->email);
                        $sheet->setCellValue('F' . $i, $order->total_amt);
                        $sheet->setCellValue('G' . $i, $pk);
                        $sheet->setCellValue('H' . $i, $order->manager->name);
                        $sheet->setCellValue('I' . $i, $order->status);
                        $sheet->setCellValue('J' . $i, $order->reject_reason);
                        $sheet->setCellValue('K' . $i, $order->promocode);
                        $sheet->setCellValue('L' . $i, $order->contract->return_date);
                        $sheet->setCellValue('M' . $i, $order->client->pdn);
                        $sheet->setCellValue('N' . $i, $order->period);
                        $sheet->setCellValue('O' . $i, $order->contract->close_date);
                        $sheet->setCellValue('P' . $i, $order->payed_summ);
                        $sheet->setCellValue('Q' . $i, $order->utm_source ?? 'Не оп');
                        $sheet->setCellValue('R' . $i, $order->id);
                        $sheet->setCellValue('S' . $i, $order->user_id);

                        $nbkiScor = ScoringsORM::query()->where('order_id', '=', $order->id)->where('status', '=', 'completed')->where('type', '=', 'nbki')->first();
                        if ($nbkiScor) {
                            $nbkiParams = unserialize($nbkiScor->body);
                            if (isset($nbkiParams['report_url'])) {
                                $data = file_get_contents(str_replace('log_report','log_xml', $nbkiParams['report_url']));
                                $xml = simplexml_load_string($data);
                                $nbkiParams['json'] = json_decode(json_encode($xml), true)['preply']['report'];
                            }
                            if (!empty($nbkiParams)) {
                                $activeProduct = 0;
                                $doneProduct = 0;
                                $summ = 0;
                                $totalAmtOutstanding = 0;
                                $totalAmtOutstandingDone = 0;
                                $totalAverPaymtAmt = 0;
                                $dolg = 0;
                                $mkk = 0;
                                $mkkSumm = 0;
                                foreach ($nbkiParams['json']['AccountReplyRUTDF'] as $reply) {

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


                                    $curentDateDiff = isset($reply['reportingDt']) ? date_diff(new DateTime(), new DateTime($reply['reportingDt'])) : date_diff(new DateTime(), new DateTime());
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
                                                $openDt = self::date_format($reply['trade']['openedDt']);
                                            } else {
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

                                $sheet->setCellValue('T' . $i, $activeProduct);
                                $sheet->setCellValue('U' . $i, $totalAmtOutstanding);
                                $sheet->setCellValue('V' . $i, $doneProduct);
                                $sheet->setCellValue('W' . $i, $totalAmtOutstandingDone);
                                $sheet->setCellValue('X' . $i, $totalAverPaymtAmt);
                                $sheet->setCellValue('Y' . $i, $dolg);
                                $sheet->setCellValue('Z' . $i, $dolg);
                                $sheet->setCellValue('AA' . $i, $mkk);
                                $sheet->setCellValue('AB' . $i, $mkkSumm);
                            }
                        }

                        $i++;
                    } catch (Exception $exception) {
                        continue;
                    }
                }
                $filename = 'Report.xlsx';
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save($this->config->root_dir . $filename);
                header('Location:' . $this->config->root_url . '/' . $filename);
                exit;
            }
        }

        return $this->design->fetch('statistics/requests_contracts.tpl');
    }

    public function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

        return $out;
    }

}
