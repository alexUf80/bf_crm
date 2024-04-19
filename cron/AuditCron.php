<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class AuditCron extends Core
{
    public function __construct()
    {
        parent::__construct();

        file_put_contents($this->config->root_dir . 'cron/log.txt', date('d-m-Y H:i:s') . ' AUDIT RUN' . PHP_EOL, FILE_APPEND);
    }


    public function run()
    {
        $datetime = date('Y-m-d H:i:s', time() - 300);

        $overtime_scorings = $this->scorings->get_overtime_scorings($datetime);
        if (!empty($overtime_scorings)) {
            foreach ($overtime_scorings as $overtime_scoring) {
                if (in_array($overtime_scoring->type, array('fms', 'fns', 'fssp')) && $overtime_scoring->repeat_count < 2) {
                    $this->scorings->update_scoring($overtime_scoring->id, array(
                        'status' => 'repeat',
                        'body' => 'Истекло время ожидания',
                        'string_result' => 'Повторный запрос',
                        'repeat_count' => $overtime_scoring->repeat_count + 1,
                    ));

                } else {
                    $this->scorings->update_scoring($overtime_scoring->id, array(
                        'status' => 'error',
                        'string_result' => 'Истекло время ожидания'
                    ));
                }
            }
        }
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($overtime_scorings);echo '</pre><hr />';

        $i = 30;
        while ($i > 0) {
            if ($scoring = $this->scorings->get_repeat_scoring()) {
                $this->scorings->update_scoring($scoring->id, array(
                    'status' => 'process',
                    'start_date' => date('Y-m-d H:i:s')
                ));

                $classname = $scoring->type . "_scoring";

                $scoring_result = $this->{$classname}->run_scoring($scoring->id);

                $this->handling_result($scoring, $scoring_result);
            }
            $i--;
        }

        $i = 30;
        while ($i > 0) {
            if ($scoring = $this->scorings->get_new_scoring()) {
                
                $scoring_repeat_count = $scoring->scoring_repeat_count;
                $scoring_repeat_count++;

                $orders = $this->orders->get_orders(array('user_id' => $scoring->user_id));
                
                //сколько отказных заявок 
                $reject_count = 0;
                foreach ($orders as $order) {
                    if ($order->order_id == $scoring->order_id) {
                        continue;
                    }
                    if ($order->status == 3) {
                        $reject_count++;
                        if ($reject_count == 1) {
                            $reject_order = $order;
                        }
                    }
                    else if($order->status != 3 && $order->status != 0){
                        break;
                    }
                }

                $user = UsersORM::query()->where('id', '=', $order->user_id)->first();
                if (($reject_count % 3) != 0 && isset($reject_order) && $reject_order->reason_id != 45 && $user->utm_source != 'kpk' && $user->utm_source != 'part1') {

                    $update = array(
                        'autoretry' => 0,
                        'autoretry_result' => 'Отказ повторной заявки '.($reject_count % 3),
                        'status' => 3,
                        'reason_id' => $reject_order->reason_id,
                        'reject_reason' => $reject_order->reject_reason,
                        'reject_date' => date('Y-m-d H:i:s'),
                        'manager_id' => 1, // System
                    );

                    // ставим отказ по заявке 
                    $this->orders->update_order($scoring->order_id, $update);

                    $old_scoring = $this->scorings->get_type_scoring($reject_order->order_id, $scoring->type);

                    $scoring_result = array(
                        'status' => $old_scoring->status,
                        'body' => $old_scoring->body,
                        'string_result' => '.'.$old_scoring->string_result,
                        'success' => $old_scoring->success,
                        'start_date' => date('Y-m-d H:i:s'),
                        'scoring_repeat_count' => $scoring_repeat_count
                    );
                    $this->scorings->update_scoring($scoring->id, $scoring_result);
                    $this->handling_result($scoring, $scoring_result);

                    $this->Gurulead->sendApiVitkol($order_id);
                }
                else{
                    $query = $this->db->placehold("
                        SELECT * 
                        FROM s_scorings 
                        WHERE 
                        order_id=? AND status='completed' AND success=0
                    ", $scoring->order_id);
                    $this->db->query($query);
                    $completed_not_success_scorings = $this->db->results();

                    $completed_not_success = false;
                    foreach ($completed_not_success_scorings as $completed_not_success_scoring) {
                        $scoring_type = $this->scorings->get_type($completed_not_success_scoring->type);
                        if ($scoring_type->negative_action == 'stop' || $scoring_type->negative_action == 'reject') {
                            $this->scorings->update_scoring($scoring->id, array('status' => 'stopped'));
                            $completed_not_success = true;
                            break;
                        }
                    }
                    if ($completed_not_success) {
                        continue;
                    }

                    $this->scorings->update_scoring($scoring->id, array(
                        'status' => 'process',
                        'start_date' => date('Y-m-d H:i:s'),
                        'scoring_repeat_count' => $scoring_repeat_count
                    ));
    
                    $classname = $scoring->type . "_scoring";
                    $scoring_result = $this->{$classname}->run_scoring($scoring->id);
    
                    $this->handling_result($scoring, $scoring_result);
                }
            }
            $i--;
        }

    }

    private function reject_amount($address_id)
    {

        $address = $this->Addresses->get_address($address_id);
        
        $scoring_type = $this->scorings->get_type('location');
        
        if (stripos($address->region, 'кути')) {
            $address->region = 'Саха/Якутия';
        }
        
        $reg='green-regions';
        $yellow_regions = array_map('trim', explode(',', mb_strtolower($scoring_type->params['yellow-regions'])));
        if(in_array(mb_strtolower(trim($address->region), 'utf8'), $yellow_regions)){
            $reg = 'yellow-regions';
        }
        $red_regions = array_map('trim', explode(',', mb_strtolower($scoring_type->params['red-regions'])));
        if(in_array(mb_strtolower(trim($address->region), 'utf8'), $red_regions)){
            $reg = 'red-regions';
        }
        $exception_regions = array_map('trim', explode(',', mb_strtolower($scoring_type->params['regions'])));
        if(in_array(mb_strtolower(trim($address->region), 'utf8'), $exception_regions)){
            $reg = 'regions';
        }

        $contract_operations = $this->ServicesCost->gets(array('region' => $reg));
        if (isset($contract_operations[0]->reject_reason_cost)) {
            return (float)$contract_operations[0]->reject_reason_cost;
        }
        else{
            return 19;
        }
    }

    private function handling_result($scoring, $result)
    {
        $scoring_type = $this->scorings->get_type($scoring->type);
        $user = UsersORM::query()->where('id', '=', $scoring->user_id)->first();
        if ($result['status'] == 'completed' && $result['success'] == 0 && $user->utm_source != 'kpk' && $user->utm_source != 'part1') {

            if ($scoring->type == 'nbki') {
                $order = $this->orders->get_order($scoring->order_id);
                if ($order->client_status == 'pk' || $order->client_status == 'crm') {
                    $scoring_type->negative_action = $scoring_type->params['negative_action_pk'];
                    $scoring_type->reason_id = $scoring_type->params['reason_id_pk'];
                }
            }


            if ($scoring_type->negative_action == 'stop' || $scoring_type->negative_action == 'reject') {
                // останавливаем незаконченные скоринги
                if ($order_scorings = $this->scorings->get_scorings(array('order_id' => $scoring->order_id))) {
                    foreach ($order_scorings as $os) {
                        if (in_array($os->status, ['new', 'process', 'repeat'])) {
                            $this->scorings->update_scoring($os->id, array('status' => 'stopped'));
                        }
                    }
                }
            }

            if ($scoring_type->negative_action == 'reject') {
                if (!empty($scoring_type->reason_id)) {
                    $order = $this->orders->get_order($scoring->order_id);
                    $reason = $this->reasons->get_reason($scoring_type->reason_id);

                    $update = array(
                        'autoretry' => 0,
                        'autoretry_result' => 'Отказ по скорингу ' . $scoring_type->title,
                        'status' => 3,
                        'reason_id' => $reason->id,
                        'reject_reason' => $reason->client_name,
                        'reject_date' => date('Y-m-d H:i:s'),
                        'manager_id' => 1, // System
                    );

                    // ставим отказ по заявке 
                    $this->orders->update_order($scoring->order_id, $update);

                    $this->changelogs->add_changelog(array(
                        'manager_id' => 1,
                        'created' => date('Y-m-d H:i:s'),
                        'type' => 'order_status',
                        'old_values' => serialize(array()),
                        'new_values' => serialize($update),
                        'order_id' => $order->order_id,
                        'user_id' => $order->user_id,
                    ));
                }

                //отказной трафик
                //LeadFinances::sendRequest($order->user_id);

                //if(!empty($order->utm_source) && $order->utm_source == 'leadstech')
                //PostbacksCronORM::insert(['order_id' => $order->order_id, 'status' => 2, 'goal_id' => 3]);

                $order = $this->orders->get_order($scoring->order_id);

                $user = UsersORM::query()->where('id', '=', $order->user_id)->first();

                $address = $this->Addresses->get_address($user->regaddress_id);
                $reject_cost = $this->reject_amount($address->id);
                // $reject_cost = 19;

                if ($user && $user->service_reason == 1) {
                    $defaultCard = CardsORM::where('user_id', $order->user_id)->where('base_card', 1)->first();
                    if(!$defaultCard){
                        $defaultCard = CardsORM::where('user_id', $order->user_id)->first();
                    }

                    $resp = $this->Best2pay->purchase_by_token($defaultCard->id, (int)$reject_cost * 100, 'Списание за услугу "Причина отказа"');
                    $status = (string)$resp->state;

                    if ($status == 'APPROVED') {
                        $this->operations->add_operation(array(
                            'contract_id' => 0,
                            'user_id' => $order->user_id,
                            'order_id' => $order->order_id,
                            'type' => 'REJECT_REASON',
                            'amount' => $reject_cost,
                            'created' => date('Y-m-d H:i:s'),
                            'transaction_id' => 0,
                        ));

                        //Отправляем чек по страховке
                        $resp = $this->Cloudkassir->send_reject_reason($order->order_id, $reject_cost);

                        if (!empty($resp)) {
                            $resp = json_decode($resp);

                            $this->receipts->add_receipt(array(
                                'user_id' => $order->user_id,
                                'Информирование о причине отказа',
                                'order_id' => $order->order_id,
                                'contract_id' => 0,
                                'insurance_id' => 0,
                                'receipt_url' => (string)$resp->Model->ReceiptLocalUrl,
                                'response' => serialize($resp),
                                'created' => date('Y-m-d H:i:s')
                            ));
                        }
                    }

                    $params = array(
                        'lastname' => $order->lastname,
                        'firstname' => $order->firstname,
                        'patronymic' => $order->patronymic,
                        'birth' => $order->birth,
                        'passport_issued' => $order->passport_issued,
                        'passport_series' => substr(str_replace(array(' ', '-'), '', $order->passport_serial), 0, 4),
                        'passport_number' => substr(str_replace(array(' ', '-'), '', $order->passport_serial), 4, 6),
                        'address' => $address->adressfull,
                        'date' => date('Y-m-d H:i:s'),
                    );

                    $this->documents->create_document(array(
                        'user_id' => $order->user_id,
                        'order_id' => $order->order_id,
                        'contract_id' => $order->contract_id,
                        'type' => 'DOGOVOR_REJECT_REASON',
                        'params' => json_encode($params),
                    ));

                    CardsORM::where('user_id', $order->user_id)->delete();
                    
                    $this->Gurulead->sendApiVitkol($order_id);
                }

                // !!!
                if (!empty($order->utm_source) && $order->utm_source == 'alians'){
                    $this->Leadgens->sendPendingPostbackToAlians($order->order_id, 3);
                }

                // $this->Leadgens->sendRejectToAlians($order->order_id);
                
                if (!empty($order->utm_source) && $order->utm_source == 'click2money' && !empty($order->lead_postback_type)) {
                    try {
                        $this->leadgens->send_cancelled_postback_click2money($order->order_id, $order);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }
        }
    }

}

$cron = new AuditCron();
$cron->run();
