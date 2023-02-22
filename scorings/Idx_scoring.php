<?php

class Idx_scoring extends Core
{
    public function run_scoring($scoring_id)
    {
        $scoring = $this->scorings->get_scoring($scoring_id);
        $order = $this->orders->get_order((int)$scoring->order_id);

        $person =
            [
                'personLastName' => $order->lastname,
                'personFirstName' => $order->firstname,
                'phone' => preg_replace('/[^0-9]/', '', $order->phone_mobile),
                'personBirthDate' => date('d.m.Y', strtotime($order->birth))
            ];

        if (!empty($order->patronymic))
            $person['personMidName'] = $order->patronymic;

        $score = $this->IdxApi->search($person);

        if (empty($score)) {

            $update =
                [
                    'status' => 'error',
                    'body' => '',
                    'success' => 0,
                    'string_result' => 'Ошибка запроса'
                ];

            $this->scorings->update_scoring($scoring_id, $update);
            return $update;
        }

        if ($score['operationResult'] == 'fail') {
            $update =
                [
                    'status' => 'completed',
                    'body' => '',
                    'success' => 0,
                    'string_result' => 'Клиент не найден в списке'
                ];

            $this->scorings->update_scoring($scoring_id, $update);
            return $update;
        }

        $update =
            [
                'status' => 'completed',
                'body' => $score['validationScorePhone'],
                'success' => 1,
                'string_result' => 'Пользователь найден: ' . $this->IdxApi->result[$score['validationScorePhone']]
            ];

        $this->scorings->update_scoring($scoring_id, $update);
        $this->logging($person, $score);
        return $update;
    }

    private function logging($request, $response, $filename = 'idxLog.txt')
    {
        $log_filename = $this->log_dir . $filename;

        if (date('d', filemtime($log_filename)) != date('d')) {
            $archive_filename = $this->log_dir . 'archive/' . date('ymd', filemtime($log_filename)) . '.' . $filename;
            rename($log_filename, $archive_filename);
            file_put_contents($log_filename, "\xEF\xBB\xBF");
        }


        $str = PHP_EOL . '===================================================================' . PHP_EOL;
        $str .= date('d.m.Y H:i:s') . PHP_EOL;
        $str .= var_export($request, true) . PHP_EOL;
        $str .= var_export($response, true) . PHP_EOL;
        $str .= 'END' . PHP_EOL;

        file_put_contents($this->log_dir . $filename, $str, FILE_APPEND);

        return 1;
    }
}