<?php

error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('memory_limit', '1024M');

chdir(dirname(__FILE__) . '/../');

require 'autoload.php';



class test extends Core
{

    public function __construct()
    {
        parent::__construct();
        //$this->request_contracts();
        die();
        $classname = "Nbki_scoring";
        $scoring_result = $this->{$classname}->run_scoring(249773);die();
        /*$classname = "Fns_scoring";
        $scoring_result = $this->{$classname}->run_scoring(111708);*/
        //42223,42635,42222,42231,41978,41991
        /*$content = file_get_contents('http://185.182.111.110:9009/log_report/9e14eb43c1e3b25ce3997d5cf9a42f41/');
        print_r($content);*/
        $orders = OrdersORM::query()
            ->where('status', '=', 2)
            ->where('date', '>=', '2023-07-01 00:00:01')
            ->where('date', '<=', '2023-07-31 23:59:59')
            ->get();
        foreach ($orders as $order) {
            $nbki = ScoringsORM::query()->where('type', '=', 'nbki')->where('order_id', '=', $order->id)->first();
            if ($nbki) {
                $classname = "Nbki_scoring";
                $scoring_result = $this->{$classname}->run_scoring($nbki->id);
            }
            $nbkis = ScoringsORM::query()->where('type', '=', 'nbkiscore')->where('order_id', '=', $order->id)->first();
            if ($nbkis) {
                $classname = "Nbkiscore_scoring";
                $scoring_result = $this->{$classname}->run_scoring($nbkis->id);
            }
        }
    }

    public static function date_format($date_string, $format = 'd.m.Y') {
        try {
            return date($format, strtotime($date_string));
        } catch (Exception $exception) {
            return $date_string;
        }
    }

    public function request_contracts() {
        $date_from = '2023-01-01';
        $date_to = '2023-07-31';

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
        echo "COunt orders = ".count($orders).PHP_EOL; sleep(3);
        foreach ($orders as $order) {
            echo $i.PHP_EOL;
            try {
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
                $sheet->setCellValue('B' . $i, isset($order->contract) ? $order->contract->number : "");
                $sheet->setCellValue('C' . $i, "{$order->client->lastname} {$order->client->firstname} {$order->client->patronymic}");
                $sheet->setCellValue('D' . $i, $order->client->phone_mobile);
                $sheet->setCellValue('E' . $i, $order->client->email);
                $sheet->setCellValue('F' . $i, $order->total_amt);
                $sheet->setCellValue('G' . $i, $pk);
                $sheet->setCellValue('H' . $i, $order->manager->name);
                $sheet->setCellValue('I' . $i, $order->status);
                $sheet->setCellValue('J' . $i, $order->reject_reason);
                $sheet->setCellValue('K' . $i, $order->promocode);
                $sheet->setCellValue('L' . $i, $order->contract ? $order->contract->return_date : "");
                $sheet->setCellValue('M' . $i, $order->client->pdn);
                $sheet->setCellValue('N' . $i, $order->period);
                $sheet->setCellValue('O' . $i, $order->contract ? $order->contract->close_date : "");
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
        echo "Done\e\n"; sleep(5);
        $filename = 'Report.xlsx';
        echo "{$this->config->root_dir}$filename";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($this->config->root_dir . $filename);
    }

    public function contracts() {
        $date_from = '2023-01-01';
        $date_to = '2023-07-31';

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
        $db_result = $this->db->results();
        echo "Count contracts = ".count($db_result).PHP_EOL;
        sleep(2);
        $test = 0;
        foreach ($db_result as $c){
            $test++;
            echo $test.PHP_EOL;
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
        }

        $test = 0;

        $managers = array();
        foreach ($this->managers->get_managers() as $m)
            $managers[$m->id] = $m;

        $filename = 'files/reports/contracts.xls';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(11);


        $active_sheet = $spreadsheet->getActiveSheet();
        $active_sheet->setTitle("Выдачи " . $date_from . "-" . $date_to);

        $active_sheet->getColumnDimension('A')->setWidth(15);
        $active_sheet->getColumnDimension('B')->setWidth(15);
        $active_sheet->getColumnDimension('C')->setWidth(45);
        $active_sheet->getColumnDimension('D')->setWidth(20);
        $active_sheet->getColumnDimension('E')->setWidth(20);
        $active_sheet->getColumnDimension('F')->setWidth(45);
        $active_sheet->getColumnDimension('G')->setWidth(20);
        $active_sheet->getColumnDimension('H')->setWidth(10);
        $active_sheet->getColumnDimension('I')->setWidth(10);
        $active_sheet->getColumnDimension('J')->setWidth(30);
        $active_sheet->getColumnDimension('K')->setWidth(10);
        $active_sheet->getColumnDimension('L')->setWidth(10);
        $active_sheet->getColumnDimension('M')->setWidth(10);
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
        $active_sheet->getColumnDimension('AE')->setWidth(10);
        $active_sheet->getColumnDimension('AF')->setWidth(10);

        $active_sheet->setCellValue('A1', 'Дата');
        $active_sheet->setCellValue('B1', 'Договор');
        $active_sheet->setCellValue('C1', 'ФИО');
        $active_sheet->setCellValue('D1', 'Дата рождения');
        $active_sheet->setCellValue('E1', 'Телефон');
        $active_sheet->setCellValue('F1', 'Адрес регистрации');
        $active_sheet->setCellValue('G1', 'Почта');
        $active_sheet->setCellValue('H1', 'Сумма');
        $active_sheet->setCellValue('I1', 'ПК/НК');
        $active_sheet->setCellValue('J1', 'Менеджер');
        $active_sheet->setCellValue('K1', 'Статус');
        $active_sheet->setCellValue('L1', 'Дата возврата');
        $active_sheet->setCellValue('M1', 'ПДН');
        $active_sheet->setCellValue('N1', 'Дней займа');
        $active_sheet->setCellValue('O1', 'Дата факт возврата');
        $active_sheet->setCellValue('P1', 'Сумма выплачено');
        $active_sheet->setCellValue('Q1', 'Источник');
        $active_sheet->setCellValue('R1', 'ID заявки');
        $active_sheet->setCellValue('S1', 'ID клиента');
        $active_sheet->setCellValue('T1', 'Промокод');
        $active_sheet->setCellValue('U1', 'Общая сумма активных долгов');
        $active_sheet->setCellValue('V1', 'Количество активных долгов');
        $active_sheet->setCellValue('W1', 'Наличие 46ой статьи');
        $active_sheet->setCellValue('X1', 'Всего активных кредитов количество');
        $active_sheet->setCellValue('Y1', 'Всего активных кредитов сумма');
        $active_sheet->setCellValue('Z1', 'Всего погашено кредитов количество');
        $active_sheet->setCellValue('AA1', 'Всего погашено кредитов сумма');
        $active_sheet->setCellValue('AB1', 'Ежемесячный платеж по кредитам');
        $active_sheet->setCellValue('AC1', 'Размер просроченной задолженности на сегодня');
        $active_sheet->setCellValue('AD1', 'Максимальная просрочка за последний год');
        $active_sheet->setCellValue('AE1', 'Количество микрозаймов за последние 3 месяца');
        $active_sheet->setCellValue('AF1', 'Количество активных микрозаймов');

        $i = 2;
        foreach ($contracts as $contract) {
            $test++;
            echo 'test.-'.$test.PHP_EOL;

            $sumPayed = OperationsORM::where('order_id', $contract->order_id)->where('type', 'PAY')->sum('amount');

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

            $active_sheet->setCellValue('A' . $i, date('d.m.Y', strtotime($contract->date)));
            $active_sheet->setCellValue('B' . $i, $contract->number);
            $active_sheet->setCellValue('C' . $i, $contract->lastname . ' ' . $contract->firstname . ' ' . $contract->patronymic . ' ' . $contract->birth);
            $active_sheet->setCellValue('D' . $i, $contract->birth);
            $active_sheet->setCellValue('E' . $i, $contract->phone_mobile);
            $active_sheet->setCellValue('F' . $i, $contract->RegAddr);
            $active_sheet->setCellValue('G' . $i, $contract->email);
            $active_sheet->setCellValue('H' . $i, $contract->amount * 1);
            $active_sheet->setCellValue('I' . $i, $client_status);
            $active_sheet->setCellValue('J' . $i, $managers[$contract->manager_id]->name);
            $active_sheet->setCellValue('K' . $i, $status);
            $active_sheet->setCellValue('L' . $i, date('d.m.Y', strtotime($contract->return_date)));
            $active_sheet->setCellValue('M' . $i, $contract->pdn);
            $active_sheet->setCellValue('N' . $i, $contract->period);
            $active_sheet->setCellValue('O' . $i, date('d.m.Y', strtotime($contract->close_date)));
            $active_sheet->setCellValue('P' . $i, $sumPayed);
            $active_sheet->setCellValue('Q' . $i, $contract->utm_source);
            $active_sheet->setCellValue('R' . $i, $contract->order_id);
            $active_sheet->setCellValue('S' . $i, $contract->user_id);
            $active_sheet->setCellValue('T' . $i, $contract->promocode);

            $fsspScor = ScoringsORM::query()->where('order_id', '=', $contract->order_id)->where('type', '=', 'fssp')->first();
            $nbkiScor = ScoringsORM::query()->where('order_id', '=', $contract->order_id)->where('type', '=', 'nbki')->first();

            if ($fsspScor) {
                $body = unserialize($fsspScor->body);
                if (isset($body['expSum'])) {
                    $active_sheet->setCellValue('U' . $i, $body['expSum']);
                    $active_sheet->setCellValue('V' . $i, $body['expCount']);
                    $active_sheet->setCellValue('W' . $i, $body['article'] ? 'Да' : 'Нет');
                } else {
                    $active_sheet->setCellValue('U' . $i, "0");
                    $active_sheet->setCellValue('V' . $i, "0");
                    $active_sheet->setCellValue('W' . $i, "Нет");
                }
            }

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


                        $curentDateDiff = isset($reply['reportingDt']) ? date_diff(new DateTime(), new DateTime($reply['reportingDt'])) : 0;
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

                    $active_sheet->setCellValue('X' . $i, $activeProduct ?? 0);
                    $active_sheet->setCellValue('Y' . $i, $totalAmtOutstanding ?? 0);
                    $active_sheet->setCellValue('Z' . $i, $doneProduct ?? 0);
                    $active_sheet->setCellValue('AA' . $i, $totalAmtOutstandingDone ?? 0);
                    $active_sheet->setCellValue('AB' . $i, $totalAverPaymtAmt ?? 0);
                    $active_sheet->setCellValue('AC' . $i, $dolg ?? 0);
                    $active_sheet->setCellValue('AD' . $i, $dolg ?? 0);
                    $active_sheet->setCellValue('AE' . $i, $mkk ?? 0);
                    $active_sheet->setCellValue('AF' . $i, $mkkSumm ?? 0);
                }
            }

            $i++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        echo "{$this->config->root_dir} $filename";
        $writer->save($this->config->root_dir . $filename);
    }
    public function orders() {
        $filter = array();
        $from = '2023-01-01';
        $to = '2023-07-31';
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
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(20);

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
        $sheet->setCellValue('E1', 'Решение');
        $sheet->setCellValue('F1', 'Причина отказа');
        $sheet->setCellValue('G1', 'Скоринговый бал');
        $sheet->setCellValue('H1', 'Балл Idx');
        $sheet->setCellValue('I1', 'Одобренный лимит');
        $sheet->setCellValue('J1', 'Количество активных займов');


        $sheet->setCellValue('K1', 'pdl_overdue_count');
        $sheet->setCellValue('L1', 'pdl_npl_limit_share');
        $sheet->setCellValue('M1', 'pdl_npl_90_limit_share');
        $sheet->setCellValue('N1', 'pdl_current_limit_max');
        $sheet->setCellValue('O1', 'pdl_last_3m_limit');
        $sheet->setCellValue('P1', 'pdl_last_good_max_limit');
        $sheet->setCellValue('Q1', 'pdl_good_limit');
        $sheet->setCellValue('R1', 'pdl_prolong_3m_limit');
        $sheet->setCellValue('S1', 'consum_current_limit_max');
        $sheet->setCellValue('T1', 'consum_good_limit');

        $sheet->setCellValue('U1', 'days_from_last_closed');
        $sheet->setCellValue('V1', 'prev_3000_500_paid_count_wo_del');
        $sheet->setCellValue('W1', 'sumPayedPercents');
        $sheet->setCellValue('X1', 'prev_max_delay');
        $sheet->setCellValue('Y1', 'last_credit_delay');
        $sheet->setCellValue('Z1', 'current_overdue_sum');
        $sheet->setCellValue('AA1', 'closed_to_total_credits_count_share');
        $sheet->setCellValue('AB1', 'pdl_overdue_count');
        $sheet->setCellValue('AC1', 'pdl_npl_90_limit_share');

        $i = 2;
        echo "count order = ".count($orders).PHP_EOL;
        sleep(2);
        foreach ($orders as $key => $order) {
            echo $i.PHP_EOL;
            $order->scoreballs = $this->NbkiScoreballs->get($order->order_id);

            if (empty($order->scoreballs)) {
                continue;
            } else {
                $order->scoreballs->variables = json_decode($order->scoreballs->variables, true);
                $order->scoreballs->variables['ball'] = $order->scoreballs->ball;
                $order->scoreballs = $order->scoreballs->variables;
            }

            $order->idx = $this->scorings->get_idx_scoring($order->order_id);

            if (empty($order->idx)) {
                continue;
            } else
                $order->idx = $order->idx->body;

            $order->status = $orders_statuses[$order->status];

            $nbki = $this->scorings->get_type_scoring($order->order_id, 'nbki');
            if (empty($nbki)) {
                continue;
            }
            $nbki = unserialize($nbki->body);
            if (empty($nbki)) {
                continue;
            }

            $sheet->setCellValue('A' . $i, $order->order_id);
            $sheet->setCellValue('B' . $i, $order->user_id);
            $sheet->setCellValue('C' . $i, $order->client_status);
            $sheet->setCellValue('D' . $i, $order->date);
            $sheet->setCellValue('E' . $i, $order->status);
            $sheet->setCellValue('F' . $i, $order->reject_reason);
            $sheet->setCellValue('G' . $i, $order->scoreballs['ball']);
            $sheet->setCellValue('H' . $i, $order->idx);
            $sheet->setCellValue('I' . $i, $order->scoreballs['limit']);
            $sheet->setCellValue('J' . $i, $nbki['number_of_active'][0]);

            if ($order->client_status == 'new') {
                $sheet->setCellValue('K' . $i, $order->scoreballs['pdl_overdue_count']);
                $sheet->setCellValue('L' . $i, $order->scoreballs['pdl_npl_limit_share']);
                $sheet->setCellValue('M' . $i, $order->scoreballs['pdl_npl_90_limit_share']);
                $sheet->setCellValue('N' . $i, $order->scoreballs['pdl_current_limit_max']);
                $sheet->setCellValue('O' . $i, $order->scoreballs['pdl_last_3m_limit']);
                $sheet->setCellValue('P' . $i, $order->scoreballs['pdl_last_good_max_limit']);
                $sheet->setCellValue('Q' . $i, $order->scoreballs['pdl_good_limit']);
                $sheet->setCellValue('R' . $i, $order->scoreballs['pdl_prolong_3m_limit']);
                $sheet->setCellValue('S' . $i, $order->scoreballs['consum_current_limit_max']);
                $sheet->setCellValue('T' . $i, $order->scoreballs['consum_good_limit']);
            } else {
                $sheet->setCellValue('U' . $i, $order->scoreballs['days_from_last_closed']);
                $sheet->setCellValue('V' . $i, $order->scoreballs['prev_3000_500_paid_count_wo_del']);
                $sheet->setCellValue('W' . $i, $order->scoreballs['sumPayedPercents']);
                $sheet->setCellValue('X' . $i, $order->scoreballs['prev_max_delay']);
                $sheet->setCellValue('Y' . $i, $order->scoreballs['last_credit_delay']);
                $sheet->setCellValue('Z' . $i, $order->scoreballs['current_overdue_sum']);
                $sheet->setCellValue('AA' . $i, $order->scoreballs['closed_to_total_credits_count_share']);
                $sheet->setCellValue('AB' . $i, $order->scoreballs['pdl_overdue_count']);
                $sheet->setCellValue('AC' . $i, $order->scoreballs['pdl_npl_90_limit_share']);
            }

            $i++;
        }

        $filename = 'Orders.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = $this->config->root_dir . $filename;
        echo $filename.PHP_EOL;
        $writer->save($filename);
    }

    public function create_document($document_type, $contract)
    {
        $ob_date = new DateTime();
        $ob_date->add(DateInterval::createFromDateString($contract->period . ' days'));
        $return_date = $ob_date->format('Y-m-d H:i:s');

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $this->orders->get_order((int)$contract->order_id);

        $insurance_cost = $this->insurances->get_insurance_cost($contract_order);

        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'number' => $contract->number,
            'contract_date' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
            'return_date' => $return_date,
            'return_date_day' => date('d', strtotime($return_date)),
            'return_date_month' => date('m', strtotime($return_date)),
            'return_date_year' => date('Y', strtotime($return_date)),
            'return_amount' => $return_amount,
            'return_amount_rouble' => $return_amount_rouble,
            'return_amount_kop' => $return_amount_kop,
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'period' => $contract->period,
            'return_amount_percents' => round($contract->amount * $contract->base_percent * $contract->period / 100, 2),
            'passport_serial' => $contract_order->passport_serial,
            'passport_date' => $contract_order->passport_date,
            'subdivision_code' => $contract_order->subdivision_code,
            'passport_issued' => $contract_order->passport_issued,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $contract->accept_code,
            'insurance_summ' => $insurance_cost,
        );

        $params['user'] = $this->users->get_user($contract->user_id);
        $params['order'] = $this->orders->get_order($contract->order_id);
        $params['contract'] = $contract;


        $this->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => json_encode($params),
        ));

    }

    private function import_addresses()
    {
        $tmp_name = $this->config->root_dir . '/files/clients.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $outer_id = $active_sheet->getCell('P' . $row)->getValue();

            if (empty($outer_id))
                continue;

            $Regindex = $active_sheet->getCell('I' . $row)->getValue();
            $Regregion = $active_sheet->getCell('R' . $row)->getValue();
            $Regcity = $active_sheet->getCell('S' . $row)->getValue();
            $Regstreet = $active_sheet->getCell('T' . $row)->getValue();
            $Regbuilding = $active_sheet->getCell('U' . $row)->getValue();
            $Regroom = $active_sheet->getCell('X' . $row)->getValue();

            $Faktindex = $active_sheet->getCell('J' . $row)->getValue();
            $Faktregion = $active_sheet->getCell('Z' . $row)->getValue();
            $Faktcity = $active_sheet->getCell('AA' . $row)->getValue();
            $Faktstreet = $active_sheet->getCell('AB' . $row)->getValue();
            $Faktbuilding = $active_sheet->getCell('AC' . $row)->getValue();
            $Faktroom = $active_sheet->getCell('AF' . $row)->getValue();

            $regaddress = "$Regindex $Regregion $Regcity $Regstreet $Regbuilding $Regroom";
            $faktaddress = "$Faktindex $Faktregion $Faktcity $Faktstreet $Faktbuilding $Faktroom";

            $faktaddres = [];
            $faktaddres['adressfull'] = $faktaddress;
            $faktaddres['zip'] = $Faktindex;
            $faktaddres['region'] = $Faktregion;
            $faktaddres['city'] = $Faktcity;
            $faktaddres['street'] = $Faktstreet;
            $faktaddres['building'] = $Faktbuilding;
            $faktaddres['room'] = $Faktroom;

            $regaddres = [];
            $regaddres['adressfull'] = $regaddress;
            $regaddres['zip'] = $Regindex;
            $regaddres['region'] = $Regregion;
            $regaddres['city'] = $Regcity;
            $regaddres['street'] = $Regstreet;
            $regaddres['building'] = $Regbuilding;
            $regaddres['room'] = $Regroom;

            foreach ($regaddres as $key => $address) {
                if ($address == '#NULL!')
                    unset($regaddres[$key]);
            }

            foreach ($faktaddres as $key => $address) {
                if ($address == '#NULL!')
                    unset($faktaddres[$key]);
            }

            $this->db->query("
            SELECT *
            from s_users
            where outer_id = ?
            ", $outer_id);

            $user = $this->db->result();


            $this->Addresses->update_address($user->regaddress_id, $regaddres);
            $this->Addresses->update_address($user->faktaddress_id, $faktaddres);

        }
    }

    private function import_clients()
    {
        $tmp_name = $this->config->root_dir . '/files/clients.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $created = $active_sheet->getCell('AQ' . $row)->getFormattedValue();
            $birth = $active_sheet->getCell('D' . $row)->getFormattedValue();
            $passport_date = $active_sheet->getCell('AK' . $row)->getFormattedValue();

            $outer_id = $active_sheet->getCell('P' . $row)->getValue();

            if (empty($outer_id))
                continue;

            $Regindex = $active_sheet->getCell('I' . $row)->getValue();
            $Regregion = $active_sheet->getCell('R' . $row)->getValue();
            $Regcity = $active_sheet->getCell('S' . $row)->getValue();
            $Regstreet = $active_sheet->getCell('T' . $row)->getValue();
            $Regbuilding = $active_sheet->getCell('U' . $row)->getValue();
            $Regroom = $active_sheet->getCell('X' . $row)->getValue();

            $Faktindex = $active_sheet->getCell('J' . $row)->getValue();
            $Faktregion = $active_sheet->getCell('Z' . $row)->getValue();
            $Faktcity = $active_sheet->getCell('AA' . $row)->getValue();
            $Faktstreet = $active_sheet->getCell('AB' . $row)->getValue();
            $Faktbuilding = $active_sheet->getCell('AC' . $row)->getValue();
            $Faktroom = $active_sheet->getCell('AF' . $row)->getValue();

            $regaddress = "$Regindex $Regregion $Regcity $Regstreet $Regbuilding $Regroom";
            $faktaddress = "$Faktindex $Faktregion $Faktcity $Faktstreet $Faktbuilding $Faktroom";

            $reg_id = $this->Addresses->add_address(['adressfull' => $regaddress]);
            $fakt_id = $this->Addresses->add_address(['adressfull' => $faktaddress]);

            $fio = explode(' ', $active_sheet->getCell('A' . $row)->getValue());

            $phone = preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('K' . $row)->getValue());
            $phone = str_split($phone);
            $phone[0] = '7';
            $phone = implode('', $phone);

            $user = [
                'firstname' => ucfirst($fio[1]),
                'lastname' => ucfirst($fio[0]),
                'patronymic' => ucfirst($fio[2]),
                'outer_id' => $outer_id,
                'phone_mobile' => $phone,
                'email' => $active_sheet->getCell('AG' . $row)->getValue(),
                'gender' => $active_sheet->getCell('AN' . $row)->getValue() == 'Мужской' ? 'male' : 'female',
                'birth' => date('d.m.Y', strtotime($birth)),
                'birth_place' => $active_sheet->getCell('G' . $row)->getValue(),
                'passport_serial' => $active_sheet->getCell('AH' . $row)->getValue() . '-' . $active_sheet->getCell('AI' . $row)->getValue(),
                'passport_date' => date('d.m.Y', strtotime($passport_date)),
                'passport_issued' => $active_sheet->getCell('AJ' . $row)->getValue(),
                'subdivision_code' => $active_sheet->getCell('H' . $row)->getValue(),
                'snils' => $active_sheet->getCell('AM' . $row)->getValue(),
                'inn' => $active_sheet->getCell('AL' . $row)->getValue(),
                'workplace' => $active_sheet->getCell('L' . $row)->getValue(),
                'workaddress' => $active_sheet->getCell('M' . $row)->getValue(),
                'profession' => $active_sheet->getCell('N' . $row)->getValue(),
                'workphone' => $active_sheet->getCell('O' . $row)->getValue(),
                'income' => $active_sheet->getCell('AO' . $row)->getValue(),
                'expenses' => $active_sheet->getCell('AP' . $row)->getValue(),
                'regaddress_id' => $reg_id,
                'faktaddress_id' => $fakt_id,
                'created' => date('Y-m-d H:i:s', strtotime($created))
            ];

            $this->users->add_user($user);
        }
    }

    private function import_orders()
    {
        $tmp_name = $this->config->root_dir . '/files/orders.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $id = $active_sheet->getCell('D' . $row)->getValue();

            if (empty($id))
                continue;

            $created = $active_sheet->getCell('A' . $row)->getFormattedValue();
            $created = date('Y-m-d H:i:s', strtotime($created));

            $reject_reason = '';

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Отказ') {
                $reject_reason = $active_sheet->getCell('N' . $row)->getValue();
                $status = 3;
            }

            if (in_array($active_sheet->getCell('I' . $row)->getFormattedValue(), ['Выдан', 'В суде', 'Отправлена претензия', 'Передан на судебную стадию', "Подписан (дистанционно)", "Получен исполнительный лист", "У коллектора"]))
                $status = 5;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'На рассмотрении')
                $status = 1;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Оплачен' || $active_sheet->getCell('I' . $row)->getValue() === 'Списан')
                $status = 7;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Отменен')
                $status = 8;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Одобрен' || $active_sheet->getCell('I' . $row)->getValue() === 'Одобрен предварительно')
                $status = 2;


            if ($active_sheet->getCell('Q' . $row)->getValue() === 'ONLINE-0,5!')
                $loantype_id = 2;
            elseif ($active_sheet->getCell('Q' . $row)->getValue() === 'ВСЕМ-0,9!')
                $loantype_id = 3;
            else
                $loantype_id = 1;

            $loantype = $this->Loantypes->get_loantype($loantype_id);


            $new_order = [
                'outer_id' => $id,
                'date' => $created,
                'loantype_id' => $loantype_id,
                'period' => 30,
                'amount' => $active_sheet->getCell('G' . $row)->getValue(),
                'accept_date' => $created,
                'confirm_date' => $created,
                'status' => $status,
                'percent' => $loantype->percent,
                'reject_reason' => $reject_reason
            ];

            $order_id = $this->orders->add_order($new_order);

            $this->db->query("
                SELECT *
                FROM s_users
                where outer_id = ?
                ", $active_sheet->getCell('O' . $row)->getValue());

            $user = $this->db->result();

            if (!empty($user))
                $this->orders->update_order($order_id, ['user_id' => $user->id]);

        }
        exit;
    }

    private function import_contracts()
    {
        $tmp_name = $this->config->root_dir . '/files/contracts.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $created = $active_sheet->getCell('B' . $row)->getFormattedValue();
            $created = date('Y-m-d H:i:s', strtotime($created));

            $issuance_date = $active_sheet->getCell('C' . $row)->getFormattedValue();
            $issuance_date = date('Y-m-d H:i:s', strtotime($issuance_date));

            $return_date = $active_sheet->getCell('E' . $row)->getFormattedValue();
            $return_date = date('Y-m-d', strtotime($return_date));

            $new_contract =
                [
                    'number' => $active_sheet->getCell('A' . $row)->getValue(),
                    'type' => 'base',
                    'period' => 30,
                    'uid' => $active_sheet->getCell('K' . $row)->getValue(),
                    'amount' => $active_sheet->getCell('F' . $row)->getValue(),
                    'status' => 0,
                    'create_date' => $created,
                    'inssuance_date' => $issuance_date,
                    'return_date' => $return_date
                ];

            $contract_id = $this->contracts->add_contract($new_contract);

            $this->db->query("
                SELECT *
                FROM s_users
                where outer_id = ?
                ", $active_sheet->getCell('N' . $row)->getValue());

            $user = $this->db->result();

            if (!empty($user))
                $this->contracts->update_contract($contract_id, ['user_id' => $user->id]);

            $this->db->query("
                SELECT *
                FROM s_orders
                where outer_id = ?
                ", $active_sheet->getCell('M' . $row)->getValue());

            $order = $this->db->result();

            $loantype = $this->Loantypes->get_loantype($order->loantype_id);
            $percent = $loantype->percent;

            $statuses = array(
                1 => 0,
                3 => 8,
                5 => 2,
                7 => 3,
                8 => 8
            );

            $new_contract =
                [
                    'order_id' => $order->id,
                    'base_percent' => $percent,
                    'status' => $statuses[$order->status]
                ];

            $this->contracts->update_contract($contract_id, $new_contract);
            $this->orders->update_order($order->id, ['contract_id' => $contract_id]);
        }
    }

    private function import_operations()
    {
        $tmp_name = $this->config->root_dir . '/files/operations.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $number = $active_sheet->getCell('B' . $row)->getValue();

            $this->db->query("
            SELECT *
            FROM s_operations
            WHERE `number` = ?
            ", $number);

            $opertion = $this->db->result();

            if (!empty($opertion))
                continue;


            $id = $active_sheet->getCell('B' . $row)->getValue();
            $created = $active_sheet->getCell('H' . $row)->getFormattedValue();
            $created = date('Y-m-d H:i:s', strtotime($created));
            $type = 'P2P';
            $amount = $active_sheet->getCell('K' . $row)->getValue();

            if ($active_sheet->getCell('J' . $row)->getValue() === 'Погашение') {
                $type = 'PAY';
                $amount = $active_sheet->getCell('L' . $row)->getValue();
            }

            $this->db->query("
            SELECT *
            FROM s_contracts
            where `number` = ?
            ", $id);

            $contract = $this->db->result();

            $this->operations->add_operation([
                'contract_id' => $contract->id,
                'user_id' => $contract->user_id,
                'order_id' => $contract->order_id,
                'type' => $type,
                'amount' => $amount,
                'created' => $created
            ]);
        }
    }

    private function import_balance()
    {
        $tmp_name = $this->config->root_dir . '/files/balances.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {
            $id = $active_sheet->getCell('B' . $row)->getValue();
            $od = $active_sheet->getCell('G' . $row)->getValue();
            $prc = $active_sheet->getCell('I' . $row)->getValue() + $active_sheet->getCell('H' . $row)->getValue();
            $peni = $active_sheet->getCell('K' . $row)->getFormattedValue();

            if ($peni == "#NULL!") {
                $peni = 0;
            }

            $contract =
                [
                    'loan_peni_summ' => (float)$peni
                ];

            $this->db->query("
            UPDATE s_contracts 
            SET ?% 
            WHERE `number` = ?
            ", $contract, $id);
        }
    }

    private function statuses()
    {
        $this->db->query("
        SELECT *
        from s_contracts
        where `status` = 3
        ");

        $contracts = $this->db->results();

        foreach ($contracts as $contract) {
            $this->db->query("
            UPDATE s_orders
            set `status` = 5
            where contract_id = ?
            ", $contract->id);
        }
    }

    private function edit_orders_amount()
    {
        $tmp_name = $this->config->root_dir . '/files/contracts.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {
            $this->db->query("
                UPDATE s_orders
                SET `amount` = ?
                where outer_id = ?
                ", $active_sheet->getCell('F' . $row)->getValue(), $active_sheet->getCell('M' . $row)->getValue());
        }
    }

    private function import_phones()
    {
        $tmp_name = $this->config->root_dir . '/files/clients.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 5;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $outer_id = $active_sheet->getCell('P' . $row)->getValue();

            if (empty($outer_id))
                continue;

            $phone = preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('K' . $row)->getValue());
            $phone = str_split($phone);
            $phone[0] = '7';
            $phone = implode('', $phone);

            $this->db->query("
            UPDATE s_users
            SET phone_mobile = ?
            where outer_id = ?
            ", $phone, $outer_id);
        }
    }

    private function import_prolongations()
    {
        $tmp_name = $this->config->root_dir . '/files/orders.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {
            $fio = $active_sheet->getCell('B' . $row)->getValue();
        }
    }

    private function competeCardEnroll()
    {
        $this->db->query("
        SELECT
        ts.id,
        ts.user_id,
        ts.amount,
        ts.register_id
        FROM s_orders os
        JOIN s_transactions ts ON os.user_id = ts.user_id
        WHERE ts.`description` = 'Привязка карты'
        AND reason_code = 1
        AND os.`status` = 3
        and checked = 0
        and created > '2022-11-25 00:00:00'
        order by id desc
        ");

        $transactions = $this->db->results();

        foreach ($transactions as $transaction)
            $this->Best2pay->completeCardEnroll($transaction);
    }


}

new test();