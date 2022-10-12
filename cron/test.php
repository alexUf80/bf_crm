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
        $this->import_clients();
    }

    private function import_clients()
    {
        $tmp_name = $this->config->root_dir . '/files/import.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $created = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('AN' . $row)->getValue());
            $birth = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('B' . $row)->getValue());
            $passport_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('AH' . $row)->getValue());

            $outer_id = $active_sheet->getCell('M' . $row)->getValue();
            $in_blacklist = ($active_sheet->getCell('N' . $row)->getValue() == 'Нет') ? 0 : 1;

            $Regindex = $active_sheet->getCell('F' . $row)->getValue();
            $Regregion = $active_sheet->getCell('O' . $row)->getValue();
            $Regcity = $active_sheet->getCell('P' . $row)->getValue();
            $Regstreet = $active_sheet->getCell('Q' . $row)->getValue();
            $Regbuilding = $active_sheet->getCell('R' . $row)->getValue();

            if (!empty($active_sheet->getCell('S' . $row)->getValue()))
                $Regbuilding .= '/' . $active_sheet->getCell('S' . $row)->getValue();

            $Reghousing = $active_sheet->getCell('T' . $row)->getValue();
            $Regroom = $active_sheet->getCell('U' . $row)->getValue();

            $Faktindex = $active_sheet->getCell('G' . $row)->getValue();
            $Faktregion = $active_sheet->getCell('W' . $row)->getValue();
            $Faktcity = $active_sheet->getCell('X' . $row)->getValue();
            $Faktstreet = $active_sheet->getCell('Y' . $row)->getValue();
            $Faktbuilding = $active_sheet->getCell('Z' . $row)->getValue();

            if (!empty($active_sheet->getCell('AA' . $row)->getValue()))
                $Faktbuilding .= '/' . $active_sheet->getCell('AA' . $row)->getValue();


            $Fakthousing = $active_sheet->getCell('AB' . $row)->getValue();
            $Faktroom = $active_sheet->getCell('AC' . $row)->getValue();

            $fio = explode(' ', $active_sheet->getCell('A' . $row)->getValue());

            $user = [
                'firstname' => ucfirst($fio[1]),
                'lastname' => ucfirst($fio[0]),
                'patronymic' => ucfirst($fio[2]),
                'outer_id' => $outer_id,
                'phone_mobile' => preg_replace("/[^,.0-9]/", '', $active_sheet->getCell('H' . $row)->getValue()),
                'email' => $active_sheet->getCell('AD' . $row)->getValue(),
                'gender' => $active_sheet->getCell('AK' . $row)->getValue() == 'Мужчина' ? 'male' : 'female',
                'birth' => date('Y-m-d', $birth),
                'birth_place' => $active_sheet->getCell('D' . $row)->getValue(),
                'passport_serial' => $active_sheet->getCell('AE' . $row)->getValue() . '-' . $active_sheet->getCell('AF' . $row)->getValue(),
                'passport_date' => date('Y-m-d', $passport_date),
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
                'employer' => '',
                'chief_name' => '',
                'chief_phone' => '',
                'Regindex' => $Regindex,
                'Regregion' => $Regregion,
                'Regdistrict' => '',
                'Regcity' => $Regcity,
                'Reglocality' => '',
                'Regstreet' => $Regstreet,
                'Regbuilding' => $Reghousing,
                'Reghousing' => $Regbuilding,
                'Regroom' => $Regroom,
                'Faktindex' => $Faktindex,
                'Faktregion' => $Faktregion,
                'Faktdistrict' => '',
                'Faktcity' => $Faktcity,
                'Faktlocality' => '',
                'Faktstreet' => $Faktstreet,
                'Faktbuilding' => $Fakthousing,
                'Fakthousing' => $Faktbuilding,
                'Faktroom' => $Faktroom,
                'created' => date('Y-m-d', $created)
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
        $tmp_name = $this->config->root_dir . '/files/import.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $id = $active_sheet->getCell('M' . $row)->getValue();
            $created = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('A' . $row)->getValue());
            $confirm_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('A' . $row)->getValue());

            $this->db->query("
            SELECT *
            FROM s_orders
            where outer_id = ?
            ", $id);

            $order = $this->db->result();

            $reject_reason = '';
            $loantype_id = 10;

            switch ($active_sheet->getCell('P' . $row)->getValue()):
                case 101542:
                    $status = 3;
                    $reject_reason = $active_sheet->getCell('J' . $row)->getValue();
                    break;

                case 101543 :
                    $status = 5;
                    break;

                case 101544:
                    $status = 4;
                    break;

                case 101546:
                    $status = 1;
                    break;

                case 101548:
                    $status = 8;
                    break;

            endswitch;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101343, 101350, 101351]))
                $loantype_id = 1;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101357, 101367, 101385]))
                $loantype_id = 2;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101398]))
                $loantype_id = 3;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101363, 101387, 101387]))
                $loantype_id = 4;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101354, 101352, 101353, 101365, 101389]))
                $loantype_id = 5;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101364, 101388]))
                $loantype_id = 6;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101360, 101345, 101366, 101390]))
                $loantype_id = 7;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(), [101401]))
                $loantype_id = 8;

            if (in_array($active_sheet->getCell('N' . $row)->getValue(),
                [
                    101403, 101404, 101369, 101370, 101371, 101372, 101373, 101374,
                    101375, 101376, 101377, 101378, 101379, 101380, 101381, 101382, 101383
                ])) {
                $loantype_id = 9;
            }


            if ($loantype_id != 10) {
                $loantype = $this->Loantypes->get_loantype($loantype_id);
                $period = $loantype->payment_count;
                $percent = $loantype->percent;
                $charge = $loantype->charge;
            } else {
                $period = 0;
                $percent = 0;
                $charge = 0;
                $peni = 0;
            }

            if ($active_sheet->getCell('Q' . $row)->getValue() == 1)
                $status = 7;

            $new_order = [
                'outer_id' => $id,
                'date' => date('Y-m-d H:i:s', $created),
                'loantype_id' => $loantype_id,
                'period' => $period,
                'amount' => $active_sheet->getCell('D' . $row)->getValue(),
                'accept_date' => date('Y-m-d H:i:s', $confirm_date),
                'confirm_date' => date('Y-m-d H:i:s', $confirm_date),
                'status' => $status,
                'offline_point_id' => 0,
                'percent' => $percent,
                'charge' => $charge,
                'reject_reason' => $reject_reason
            ];

            if (!empty($order)) {
                $this->orders->update_order($order->id, $new_order);
            } else {
                $order_id = $this->orders->add_order($new_order);

                $this->db->query("
                SELECT *
                FROM s_users
                where outer_id = ?
                ", $active_sheet->getCell('L' . $row)->getValue());

                $user = $this->db->result();

                if (!empty($user))
                    $this->orders->update_order($order_id, ['user_id' => $user->id]);
            }

        }
    }

    private function import_contracts()
    {
        $tmp_name = $this->config->root_dir . '/files/import.xlsx';
        $format = IOFactory::identify($tmp_name);
        $reader = IOFactory::createReader($format);
        $spreadsheet = $reader->load($tmp_name);

        $active_sheet = $spreadsheet->getActiveSheet();

        $first_row = 2;
        $last_row = $active_sheet->getHighestRow();

        for ($row = $first_row; $row <= $last_row; $row++) {

            $id = $active_sheet->getCell('L' . $row)->getValue();
            $created = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('B' . $row)->getValue());
            $issuance_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('C' . $row)->getValue());
            $return_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($active_sheet->getCell('E' . $row)->getValue());

            $status = $active_sheet->getCell('P' . $row)->getValue();

            switch ($status):
                case 101281:
                    $status = 2;
                    break;

                case 101481:
                    $status = 3;
                    break;

                case 101485:
                    $status = 13;
                    break;

                case 101486:
                    $status = 8;
                    break;

                case 101483:
                    $status = 12;
                    break;

                default:
                    if (!empty($active_sheet->getCell('R' . $row)->getValue()))
                        $status = 4;
                    else
                        $status = 2;
                    break;

            endswitch;

            $issuance_date = new DateTime(date('Y-m-d', $issuance_date));
            $return_date = new DateTime(date('Y-m-d', $return_date));
            $period = date_diff($issuance_date, $return_date)->days;

            $new_contract =
                [
                    'outer_id' => $id,
                    'number' => $active_sheet->getCell('A' . $row)->getValue(),
                    'type' => 'base',
                    'period' => $period,
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
            $charge = $loantype->charge;

            $new_contract =
                [
                    'order_id' => $order->id,
                    'base_percent' => $percent,
                    'charge_percent' => $charge,
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