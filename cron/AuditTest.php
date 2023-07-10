<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class AuditTest extends Core
{
    public function __construct()
    {
        parent::__construct();

        file_put_contents($this->config->root_dir . 'cron/log.txt', date('d-m-Y H:i:s') . ' AUDIT RUN' . PHP_EOL, FILE_APPEND);
    }


    public function run()
    {
        $returnStartTime = date('Y-m-15 00:00:00', strtotime('+3 days'));
        $returnEndTime = date('Y-m-15 23:59:59', strtotime('+3 days'));
        $contracts = ContractsORM::whereBetween('return_date', [$returnStartTime, $returnEndTime])->where('status', 2)->get();
        foreach ($contracts as $contract) {
            print_r($contract->user_id.PHP_EOL);
        }

    }

    private function handling_result($scoring, $result)
    {
        $scoring_type = $this->scorings->get_type($scoring->type);
        if ($result['status'] == 'completed' && $result['success'] == 0) {
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

                $user = UsersORM::query()->where('id', '=', $order->user_id)->first();
                if ($user && $user->service_reason == 1) {
                    $defaultCard = CardsORM::where('user_id', $order->user_id)->where('base_card', 1)->first();

                    $resp = $this->Best2pay->purchase_by_token($defaultCard->id, 1900, 'Списание за услугу "Причина отказа"');
                    $status = (string)$resp->state;

                    if ($status == 'APPROVED') {
                        $this->operations->add_operation(array(
                            'contract_id' => 0,
                            'user_id' => $order->user_id,
                            'order_id' => $order->order_id,
                            'type' => 'REJECT_REASON',
                            'amount' => 19,
                            'created' => date('Y-m-d H:i:s'),
                            'transaction_id' => 0,
                        ));

                        //Отправляем чек по страховке
                        $resp = $this->Cloudkassir->send_reject_reason($order->order_id);

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

                    CardsORM::where('user_id', $order->user_id)->delete();
                }
            }
        }
    }

}

$cron = new AuditTest();
$cron->run();
