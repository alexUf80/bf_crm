<?php

chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class TestCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {

        $this->db->query("
            SELECT count(*) as `count`
            from s_orders
            ");
        $count_orders = $this->db->result('count');

        $limit = 300;

        for ($limit_from = 0; $limit_from <= $count_orders; $limit_from += 300) {

            $this->db->query("
            SELECT id, user_id, `date`
            FROM s_orders
            LIMIT $limit_from, $limit
            ");

            $orders = $this->db->results();

            foreach ($orders as $order) {

                $document_exist = 0;

                $documents = $this->documents->get_documents(array('order_id' => $order->id));

                foreach ($documents as $document) {
                    if ($document->type == 'SOGLASIE_OPD')
                        $document_exist = 1;
                }

                if ($document_exist == 0) {

                    $user = $this->users->get_user($order->user_id);
                    
                    list($passport_serial, $passport_number) = explode('-', $user->passport_serial);

                    $params = array(
                        'lastname' => $user->lastname,
                        'firstname' => $user->firstname,
                        'patronymic' => $user->patronymic,
                        'gender' => $user->gender,
                        'phone' => $user->phone_mobile,
                        'birth' => $user->birth,
                        'birth_place' => $user->birth_place,
                        'inn' => $user->inn,
                        'snils' => $user->snils,
                        'email' => $user->email,
                        'created' => $user->created,

                        'passport_serial' => $passport_serial,
                        'passport_number' => $passport_number,
                        'passport_date' => $user->passport_date,
                        'passport_code' => $user->subdivision_code,
                        'passport_issued' => $user->passport_issued,

                        'regindex' => $user->Regindex,
                        'regregion' => $user->Regregion,
                        'regcity' => $user->Regcity,
                        'regstreet' => $user->Regstreet,
                        'reghousing' => $user->Reghousing,
                        'regbuilding' => $user->Regbuilding,
                        'regroom' => $user->Regroom,
                        'faktindex' => $user->Faktindex,
                        'faktregion' => $user->Faktregion,
                        'faktcity' => $user->Faktcity,
                        'faktstreet' => $user->Faktstreet,
                        'fakthousing' => $user->Fakthousing,
                        'faktbuilding' => $user->Faktbuilding,
                        'faktroom' => $user->Faktroom,
                        'profession' => $user->profession,
                        'workplace' => $user->workplace,
                        'workphone' => $user->workphone,
                        'chief_name' => $user->chief_name,
                        'chief_position' => $user->chief_position,
                        'chief_phone' => $user->chief_phone,
                        'income' => $user->income,
                        'expenses' => $user->expenses,
                        'first_loan_amount' => $user->first_loan_amount,
                        'first_loan_period' => $user->first_loan_period,
                        'number' => $order->id,
                        'create_date' => $order->date,
                        'asp' => $user->sms,
                    );

                    $this->documents->create_document(array(
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'type' => 'SOGLASIE_OPD',
                        'created' => date('Y-m-d H:i:s', strtotime($order->date)),
                        'params' => $params
                    ));
                }
            }
            usleep(300000);
        }


        $this->sms->send(79966208002, "Крон отработал");
    }
}

new TestCron();