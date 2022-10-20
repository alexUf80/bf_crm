<?php

error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('memory_limit', '1024M');

chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class test extends Core
{

    public function __construct()
    {
        parent::__construct();
        $this->import_orders();
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

            $created = $active_sheet->getCell('AN' . $row)->getFormattedValue();
            $birth = $active_sheet->getCell('B' . $row)->getFormattedValue();
            $passport_date = $active_sheet->getCell('AH' . $row)->getFormattedValue();

            $outer_id = $active_sheet->getCell('M' . $row)->getValue();

            if (empty($outer_id))
                continue;

            $in_blacklist = ($active_sheet->getCell('N' . $row)->getValue() == 'Нет') ? 0 : 1;

            $Regindex = $active_sheet->getCell('F' . $row)->getValue();
            $Regregion = $active_sheet->getCell('O' . $row)->getValue();
            $Regcity = $active_sheet->getCell('P' . $row)->getValue();
            $Regstreet = $active_sheet->getCell('Q' . $row)->getValue();
            $Regbuilding = $active_sheet->getCell('R' . $row)->getValue();
            $Regroom = $active_sheet->getCell('U' . $row)->getValue();

            $Faktindex = $active_sheet->getCell('G' . $row)->getValue();
            $Faktregion = $active_sheet->getCell('W' . $row)->getValue();
            $Faktcity = $active_sheet->getCell('X' . $row)->getValue();
            $Faktstreet = $active_sheet->getCell('Y' . $row)->getValue();
            $Faktbuilding = $active_sheet->getCell('Z' . $row)->getValue();
            $Faktroom = $active_sheet->getCell('AC' . $row)->getValue();

            $regaddress = "$Regindex $Regregion $Regcity $Regstreet $Regbuilding $Regroom";
            $faktaddress = "$Faktindex $Faktregion $Faktcity $Faktstreet $Faktbuilding $Faktroom";

            $reg_id = $this->Addresses->add_address(['adressfull' => $regaddress]);
            $fakt_id = $this->Addresses->add_address(['adressfull' => $faktaddress]);

            $fio = explode(' ', $active_sheet->getCell('A' . $row)->getValue());

            $phone = preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('H' . $row)->getValue());
            $phone = str_replace('8', '7', $phone);

            $user = [
                'firstname' => ucfirst($fio[1]),
                'lastname' => ucfirst($fio[0]),
                'patronymic' => ucfirst($fio[2]),
                'outer_id' => $outer_id,
                'phone_mobile' => $phone,
                'email' => $active_sheet->getCell('AD' . $row)->getValue(),
                'gender' => $active_sheet->getCell('AK' . $row)->getValue() == 'Мужчина' ? 'male' : 'female',
                'birth' => date('d.m.Y', strtotime($birth)),
                'birth_place' => $active_sheet->getCell('D' . $row)->getValue(),
                'passport_serial' => $active_sheet->getCell('AE' . $row)->getValue() . '-' . $active_sheet->getCell('AF' . $row)->getValue(),
                'passport_date' => date('d.m.Y', strtotime($passport_date)),
                'passport_issued' => $active_sheet->getCell('AG' . $row)->getValue(),
                'subdivision_code' => $active_sheet->getCell('E' . $row)->getValue(),
                'snils' => $active_sheet->getCell('AJ' . $row)->getValue(),
                'inn' => $active_sheet->getCell('AI' . $row)->getValue(),
                'workplace' => $active_sheet->getCell('I' . $row)->getValue(),
                'workaddress' => $active_sheet->getCell('J' . $row)->getValue(),
                'profession' => $active_sheet->getCell('K' . $row)->getValue(),
                'workphone' => $active_sheet->getCell('L' . $row)->getValue(),
                'income' => $active_sheet->getCell('AL' . $row)->getValue(),
                'expenses' => $active_sheet->getCell('AM' . $row)->getValue(),
                'chief_name' => '',
                'chief_phone' => '',
                'regaddress_id' => $reg_id,
                'faktaddress_id' => $fakt_id,
                'created' => date('Y-m-d H:i:s', strtotime($created))
            ];

            $this->users->add_user($user);

            if ($in_blacklist == 1) {
                $this->blacklist->add_person([
                    'fio' => ucfirst($active_sheet->getCell('A' . $row)->getValue()),
                    'phone' => preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('L' . $row)->getValue())]);
            }
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

            if(empty($id))
                continue;

            $created = $active_sheet->getCell('A' . $row)->getFormattedValue();
            $created = date('Y-m-d H:i:s', strtotime($created));

            $reject_reason = $active_sheet->getCell('N' . $row)->getValue();

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Отказ')
                $status = 3;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Выдан')
                $status = 5;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'На рассмотрении')
                $status = 1;

            if ($active_sheet->getCell('I' . $row)->getValue() === 'Оплачен')
                $status = 7;



            if ($active_sheet->getCell('Q' . $row)->getValue() === 'ONLINE')
                $loantype_id = 1;
            else
                $loantype_id = 2;

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

            $created = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('B' . $row)->getValue());
            $issuance_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('C' . $row)->getValue());
            $return_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('E' . $row)->getValue());

            if ($active_sheet->getCell('O' . $row)->getValue() === 'Отказ')
                $status = 3;

            if ($active_sheet->getCell('O' . $row)->getValue() === 'Выдан')
                $status = 5;

            if ($active_sheet->getCell('O' . $row)->getValue() === 'На рассмотрении')
                $status = 1;

            if ($active_sheet->getCell('O' . $row)->getValue() === 'Оплачен')
                $status = 7;

            $issuance_date = new DateTime(date('Y-m-d', $issuance_date));
            $return_date = new DateTime(date('Y-m-d', $return_date));

            $new_contract =
                [
                    'number' => $active_sheet->getCell('A' . $row)->getValue(),
                    'type' => 'base',
                    'period' => 30,
                    'uid' => $active_sheet->getCell('K' . $row)->getValue(),
                    'amount' => $active_sheet->getCell('F' . $row)->getValue(),
                    'status' => $status,
                    'create_date' => date('Y-m-d H:i:s', $created),
                    'inssuance_date' => $issuance_date->format('Y-m-d'),
                    'return_date' => $return_date->format('Y-m-d')
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

            $new_contract =
                [
                    'order_id' => $order->id,
                    'base_percent' => $percent,
                ];

            $this->contracts->update_contract($contract_id, $new_contract);
            $this->orders->update_order($order->id, ['contract_id' => $contract_id]);
        }
    }

    private function import_operations()
    {
        $tmp_name = $this->config->root_dir . '/files/import.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $number = $active_sheet->getCell('E' . $row)->getValue();

            $this->db->query("
            SELECT *
            FROM s_operations
            WHERE `number` = ?
            ", $number);

            $opertion = $this->db->result();

            if (!empty($opertion))
                continue;


            $id = $active_sheet->getCell('B' . $row)->getValue();
            $created = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('F' . $row)->getValue());
            $type = 'P2P';
            $amount = $active_sheet->getCell('I' . $row)->getValue();

            if ($active_sheet->getCell('H' . $row)->getValue() === 'Погашение') {
                $type = 'PAY';
                $amount = $active_sheet->getCell('J' . $row)->getValue();
            }

            $this->db->query("
            SELECT *
            FROM s_contracts
            where outer_id = ?
            ", $active_sheet->getCell('K' . $row)->getValue());

            $contract = $this->db->result();

            $this->operations->add_operation([
                'contract_id' => $contract->id,
                'user_id' => $contract->user_id,
                'order_id' => $contract->order_id,
                'type' => $type,
                'amount' => $amount,
                'created' => date('Y-m-d', $created),
                'number' => $number,
                'outer_id' => $id
            ]);
        }
    }

    private function import_balance()
    {
        $tmp_name = $this->config->root_dir . '/files/import.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {
            $id = $active_sheet->getCell('O' . $row)->getValue();
            $od = $active_sheet->getCell('H' . $row)->getValue();
            $prc = $active_sheet->getCell('K' . $row)->getValue();
            $peni = $active_sheet->getCell('L' . $row)->getValue();

            $contract =
                [
                    'loan_body_summ' => (float)$od,
                    'loan_percents_summ' => (float)$prc,
                    'loan_peni_summ' => (float)$peni
                ];

            $this->db->query("
            UPDATE s_contracts 
            SET ?% 
            WHERE outer_id = ?
            ", $contract, $id);
        }
    }


}

new test();