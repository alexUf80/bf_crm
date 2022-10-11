<?php

class NeworderController extends Controller
{
    public function fetch()
    {
        if ($this->request->method('post')) {
            if ($this->request->post('action', 'string')) {
                $methodName = 'action_' . $this->request->post('action', 'string');
                if (method_exists($this, $methodName)) {
                    $this->$methodName();
                }
            }
        } else {
            $this->design->assign('percent', 1);
            $this->design->assign('charge', 1);
            $this->design->assign('peni', 320);
        }

        $organizations = array();
        foreach ($this->offline->get_organizations() as $org)
            $organizations[$org->id] = $org;
        $this->design->assign('organizations', $organizations);

        $loantypes = $this->loantypes->get_loantypes();
        $this->design->assign('loantypes', $loantypes);

        return $this->design->fetch('neworder.tpl');
    }

    private function action_create_order()
    {
        $amount = intval($this->request->post('amount'));

        $user = array();

        $user_id = intval($this->request->post('user_id'));

        $user['firstname'] = trim($this->request->post('firstname'));
        $user['lastname'] = trim($this->request->post('lastname'));
        $user['patronymic'] = trim($this->request->post('patronymic'));

        $user['phone_mobile'] = trim((string)$this->request->post('phone'));
        $user['email'] = trim((string)$this->request->post('email'));
        $user['gender'] = trim((string)$this->request->post('gender'));
        $user['birth'] = trim((string)$this->request->post('birth'));
        $user['birth_place'] = trim((string)$this->request->post('birth_place'));

        $user['passport_serial'] = (string)$this->request->post('passport_serial');
        $user['passport_date'] = (string)$this->request->post('passport_date');
        $user['passport_issued'] = (string)$this->request->post('passport_issued');
        $user['subdivision_code'] = (string)$this->request->post('subdivision_code');

        $user['workplace'] = (string)$this->request->post('workplace');
        $user['workaddress'] = (string)$this->request->post('workaddress');
        $user['profession'] = (string)$this->request->post('profession');
        $user['workphone'] = (string)$this->request->post('workphone');
        $user['income'] = (string)$this->request->post('income');
        $user['expenses'] = (string)$this->request->post('expenses');
        $user['chief_name'] = (string)$this->request->post('chief_name');
        $user['chief_position'] = (string)$this->request->post('chief_position');
        $user['chief_phone'] = (string)$this->request->post('chief_phone');

        $Regadress = json_decode($this->request->post('regaddressfull'));

        $regaddress = [];
        $regaddress['adressfull'] = $this->request->post('regaddress');
        $regaddress['zip'] = $Regadress->data->postal_code ?? '';
        $regaddress['region'] = $Regadress->data->region ?? '';
        $regaddress['region_type'] = $Regadress->data->region_type ?? '';
        $regaddress['city'] = $Regadress->data->city ?? '';
        $regaddress['city_type'] = $Regadress->data->city_type ?? '';
        $regaddress['district'] = $Regadress->data->city_district ?? '';
        $regaddress['district_type'] = $Regadress->data->city_district_type ?? '';
        $regaddress['locality'] = $Regadress->data->settlement ?? '';
        $regaddress['locality_type'] = $Regadress->data->settlement_type ?? '';
        $regaddress['street'] = $Regadress->data->street ?? '';
        $regaddress['street_type'] = $Regadress->data->street_type ?? '';
        $regaddress['house'] = $Regadress->data->house ?? '';
        $regaddress['building'] = $Regadress->data->block ?? '';
        $regaddress['room'] = $Regadress->data->flat ?? '';
        $regaddress['okato'] = $Regadress->data->okato ?? '';
        $regaddress['oktmo'] = $Regadress->data->oktmo ?? '';

        $faktaddress['adressfull'] = $this->request->post('Faktadressfull');

        if (empty($faktaddress['adressfull'])) {
            $faktaddress = [];
            $faktaddress['adressfull'] = $regaddress['adressfull'];
            $faktaddress['zip'] = $regaddress['zip'];
            $faktaddress['region'] = $regaddress['region'];
            $faktaddress['region_type'] = $regaddress['region_type'];
            $faktaddress['city'] = $regaddress['city'];
            $faktaddress['city_type'] = $regaddress['city_type'];
            $faktaddress['district'] = $regaddress['district'];
            $faktaddress['district_type'] = $regaddress['district_type'];
            $faktaddress['locality'] = $regaddress['locality'];
            $faktaddress['locality_type'] = $regaddress['locality_type'];
            $faktaddress['street'] = $regaddress['street'];
            $faktaddress['street_type'] = $regaddress['street_type'];
            $faktaddress['house'] = $regaddress['house'];
            $faktaddress['building'] = $regaddress['building'];
            $faktaddress['room'] = $regaddress['room'];
            $faktaddress['okato'] = $regaddress['okato'];
            $faktaddress['oktmo'] = $regaddress['oktmo'];

        } else {
            $faktaddress = [];
            $faktaddress['adressfull'] = $this->request->post('Faktadressfull');
            $faktaddress['zip'] = $Fakt_adress->data->postal_code ?? '';
            $faktaddress['region'] = $Fakt_adress->data->region ?? '';
            $faktaddress['region_type'] = $Fakt_adress->data->region_type ?? '';
            $faktaddress['city'] = $Fakt_adress->data->city ?? '';
            $faktaddress['city_type'] = $Fakt_adress->data->city_type ?? '';
            $faktaddress['district'] = $Fakt_adress->data->city_district ?? '';
            $faktaddress['district_type'] = $Fakt_adress->data->city_district_type ?? '';
            $faktaddress['locality'] = $Fakt_adress->data->settlement ?? '';
            $faktaddress['locality_type'] = $Fakt_adress->data->settlement_type ?? '';
            $faktaddress['street'] = $Fakt_adress->data->street ?? '';
            $faktaddress['street_type'] = $Fakt_adress->data->street_type ?? '';
            $faktaddress['house'] = $Fakt_adress->data->house ?? '';
            $faktaddress['building'] = $Fakt_adress->data->block ?? '';
            $faktaddress['room'] = $Fakt_adress->data->flat ?? '';
            $faktaddress['okato'] = $Fakt_adress->data->okato ?? '';
            $faktaddress['oktmo'] = $Fakt_adress->data->oktmo ?? '';
        }

        if (empty($user_id)) {
            $user['stage_personal'] = 1;
            $user['stage_passport'] = 1;
            $user['stage_address'] = 1;
            $user['stage_work'] = 1;
            $user['stage_files'] = 1;
            $user['stage_card'] = 1;

            $user['regaddress_id'] = $this->Addresses->add_address($regaddress);
            $user['faktaddress_id'] = $this->Addresses->add_address($faktaddress);

            $user_id = $this->users->add_user($user);
        } else {

            $old_user = $this->users->get_user($user_id);
            $this->users->update_user($user_id, $user);

            $this->Addresses->update_address($old_user->regaddress_id, $regaddress);
            $this->Addresses->update_address($old_user->faktaddress_id, $faktaddress);
        }

        $order = array(
            'type' => 'offline',
            'user_id' => $user_id,
            'amount' => $amount,
            'percent' => 1,
            'period' => $this->request->post('period'),
            'date' => date('Y-m-d H:i:s'),
            'manager_id' => $this->manager->id,
            'status' => 1,
            'offline' => 1,

        );

        if ($order_id = $this->orders->add_order($order)) {
            echo json_encode(['success' => $order_id]);
            exit;
        } else {
            echo json_encode(['error' => 'Не удалось создать заявку']);
            exit;
        }
    }

    private function action_search_user()
    {
        $fio = $this->request->post('fio');

        $users = $this->users->search_user($fio);

        if (!empty($users)) {
            $items = [];

            foreach ($users as $user)
                $items[] = ['id' => $user->id, 'text' => "$user->lastname $user->firstname $user->patronymic"];

            echo json_encode(['items' => $items]);
        } else
            echo json_encode(['empty' => 1]);

        exit;
    }

    private function action_get_user()
    {
        $user_id = $this->request->post('user_id');

        $user = $this->users->get_user($user_id);

        echo json_encode($user);
        exit;
    }
}