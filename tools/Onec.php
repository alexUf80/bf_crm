<?php

error_reporting(-1);
ini_set('display_errors', 'Off');

class Onec implements ToolsInterface
{
    protected static $params;

    public static function action($params)
    {
        return self::{$params['method']}($params['items']);
    }

    private static function sendCancelledOrders($orders)
    {
        $i = 0;

        foreach ($orders as $order) {
            list($passportSerial, $passportNumber) = explode('-', $order->user->passport_serial);

            if (empty($order->user->regAddress)) {
                $order->user->regAddress->adressfull = '163000, Архангельск, Попова, д.15';
                $order->user->regAddress->zip = '163000';
                $order->user->regAddress->region = 'Архангельская';
                $order->user->regAddress->region_type = 'обл';
                $order->user->regAddress->city = 'Архангельск';
                $order->user->regAddress->city_type = 'г';
                $order->user->regAddress->district = '';
                $order->user->regAddress->district_type = '';
                $order->user->regAddress->locality = '';
                $order->user->regAddress->locality_type = '';
                $order->user->regAddress->street = 'Попова';
                $order->user->regAddress->street_type = 'ул';
                $order->user->regAddress->house = '15';
                $order->user->regAddress->room = '';
            }

            if (empty($order->user->factAddress)) {
                $order->user->factAddress->adressfull = '163000, Архангельск, Попова, д.15';
                $order->user->factAddress->zip = '163000';
                $order->user->factAddress->region = 'Архангельская';
                $order->user->factAddress->region_type = 'обл';
                $order->user->factAddress->city = 'Архангельск';
                $order->user->factAddress->city_type = 'г';
                $order->user->factAddress->district = '';
                $order->user->factAddress->district_type = '';
                $order->user->factAddress->locality = '';
                $order->user->factAddress->locality_type = '';
                $order->user->factAddress->street = 'Попова';
                $order->user->factAddress->street_type = 'ул';
                $order->user->factAddress->house = '15';
                $order->user->factAddress->room = '';
            }

            $xml['Справочники'][$i]['Контрагент'] =
                [
                    'Наименование' => trim($order->user->lastname . ' ' . $order->user->firstname . ' ' . $order->user->patronymic),
                    'УИД' => trim($order->user->id),
                    "ВидКонтрагента" => 'ФизЛицо',
                    'СерияПаспорта' => trim($passportSerial),
                    'НомерПаспорта' => trim($passportNumber),
                    'ДатаВыдачиПаспорта' => date('Y-m-d', strtotime($order->user->passport_date)),
                    'ДатаРождения' => date('Y-m-d', strtotime($order->user->birth)),
                    'КемВыданПаспорт' => trim($order->user->passport_issued),
                    'КодПодразделения' => trim($order->user->subdivision_code),
                    'МестоРождения' => trim($order->user->birth_place),
                    'Пол' => ($order->user->gender == 'female') ? 'Ж' : 'М',
                    'СотовыйТелефон' => trim($order->user->phone_mobile),
                    'Фамилия' => trim($order->user->lastname),
                    'Имя' => trim($order->user->firstname),
                    'Отчество' => trim($order->user->patronymic),
                    'ИндексПоРегистрации' => trim($order->user->regAddress->zip),
                    'ИндексФактическогоПроживания' => trim($order->user->factAddress->zip),
                    'РайонОбластьПоРегистрации' => trim($order->user->regAddress->region) . ' ' . trim($order->user->regAddress->region_type),
                    'РайонОбластьФактическогоПроживания' => trim($order->user->factAddress->region) . ' ' . trim($order->user->factAddress->region_type),
                    'РайонПоРегистрации' => trim($order->user->regAddress->district).' '.trim($order->user->regAddress->district_type),
                    'РайонФактическогоПроживания' => trim($order->user->factAddress->district).' '.trim($order->user->factAddress->district_type),
                    'ГородПоРегистрации' => (trim($contract->user->regAddress->city . ' ' . $contract->user->regAddress->city_type)),
                    'ГородФактическогоПроживания' => (trim($contract->user->factAddress->city . ' ' . $contract->user->factAddress->city_type)),
                    'НаселенныйПунктПоРегистрации' => (trim($contract->user->regAddress->locality . ' ' . $contract->user->regAddress->locality_type)),
                    'НаселенныйПунктФактическогоПроживания' => (trim($contract->user->factAddress->locality . ' ' . $contract->user->factAddress->locality_type)),
                    'УлицаПоРегистрации' => trim($order->user->regAddress->street . ' ' . $order->user->regAddress->street_type),
                    'УлицаФактическогоПроживания' => trim($order->user->factAddress->street . ' ' . $order->user->factAddress->street_type),
                    'ДомПоРегистрации' => trim($order->user->regAddress->house),
                    'ДомФактическогоПроживания' => trim($order->user->factAddress->house),
                    'КвартираПоРегистрации' => trim($order->user->regAddress->room),
                    'КвартираФактическогоПроживания' => trim($order->user->factAddress->room),
                    'ПредставлениеАдресаПоРегистрации' => trim($order->user->regAddress->adressfull),
                    'ПредставлениеАдресаФактическогоПроживания' => trim($order->user->factAddress->adressfull),
                    'МестоРаботы' => trim($order->user->workplace),
                    'РабочийТелефон' => trim($order->user->workphone),
                    'Email' => trim($order->user->email),
                    'ДатаСоздания' => date('Y-m-d', strtotime($order->user->created)),
                    'Решение' => $order->status == 3 ? 2 : 1,
                ];

            $i++;
        }

        $xml['Справочники']['Подразделение'] = ['Наименование' => 'АРХАНГЕЛЬСК 1', 'УИД' => 1];
        $xml['Справочники']['Организация'] = ['Наименование' => 'ООО МКК "БАРЕНЦ ФИНАНС"', 'УИД' => 1];

        $xml['Справочники'][$i]['СервисыОнлайнОплаты'] =
            [
                'Наименование' => 'Best2Pay',
                'УИД' => '1',
            ];
        $i++;

        $xml['Справочники'][$i]['КредитныеПродукты'] =
            [
                'Наименование' => 'Стандартный',
                'УИД' => 1,
                'Процент' => 0.8
            ];

        $i++;

        $promocodes = PromocodesORM::get();

        foreach ($promocodes as $promocode) {
            $percent = 1 - ($promocode->discount / 100);

            $xml['Справочники'][$i]['КредитныеПродукты'] =
                [
                    'Наименование' => 'Стандартный-' . $promocode->id,
                    'УИД' => $promocode->id,
                    'Процент' => $percent
                ];
            $i++;
        }

        return self::processing($xml);
    }

    private static function sendIssuedContracts($contracts)
    {
        $i = 0;

        $exceptions_array = [90236, 49346];

        foreach ($contracts as $contract) {
            if (in_array($contract->user_id, $exceptions_array)) {
                continue;
            }
            list($passportSerial, $passportNumber) = explode('-', $contract->user->passport_serial);

            if (empty($contract->user->regAddress)) {
                $contract->user->regAddress->adressfull = '163000, Архангельск, Попова, д.15';
                $contract->user->regAddress->zip = '163000';
                $contract->user->regAddress->region = 'Архангельская';
                $contract->user->regAddress->region_type = 'обл';
                $contract->user->regAddress->city = 'Архангельск';
                $contract->user->regAddress->city_type = 'г';
                $contract->user->regAddress->district = '';
                $contract->user->regAddress->district_type = '';
                $contract->user->regAddress->locality = '';
                $contract->user->regAddress->locality_type = '';
                $contract->user->regAddress->street = 'Попова';
                $contract->user->regAddress->street_type = 'ул';
                $contract->user->regAddress->house = '15';
                $contract->user->regAddress->room = '';
            }

            if (empty($contract->user->factAddress)) {
                $contract->user->factAddress->adressfull = '163000, Архангельск, Попова, д.15';
                $contract->user->factAddress->zip = '163000';
                $contract->user->factAddress->region = 'Архангельская';
                $contract->user->factAddress->region_type = 'обл';
                $contract->user->factAddress->city = 'Архангельск';
                $contract->user->factAddress->city_type = 'г';
                $contract->user->factAddress->district = '';
                $contract->user->factAddress->district_type = '';
                $contract->user->factAddress->locality = '';
                $contract->user->factAddress->locality_type = '';
                $contract->user->factAddress->street = 'Попова';
                $contract->user->factAddress->street_type = 'ул';
                $contract->user->factAddress->house = '15';
                $contract->user->factAddress->room = '';
            }

            $xml['Справочники'][$i]['Контрагент'] =
                [
                    'Наименование' => trim($contract->user->lastname . ' ' . $contract->user->firstname . ' ' . $contract->user->patronymic),
                    'УИД' => trim($contract->user->id),
                    "ВидКонтрагента" => 'ФизЛицо',
                    'СерияПаспорта' => trim($passportSerial),
                    'НомерПаспорта' => trim($passportNumber),
                    'ДатаВыдачиПаспорта' => date('Y-m-d', strtotime($contract->user->passport_date)),
                    'ДатаРождения' => date('Y-m-d', strtotime($contract->user->birth)),
                    'КемВыданПаспорт' => trim($contract->user->passport_issued),
                    'КодПодразделения' => trim($contract->user->subdivision_code),
                    'МестоРождения' => trim($contract->user->birth_place),
                    'Пол' => ($contract->user->gender == 'female') ? 'Ж' : 'М',
                    'СотовыйТелефон' => trim($contract->user->phone_mobile),
                    'Фамилия' => trim($contract->user->lastname),
                    'Имя' => trim($contract->user->firstname),
                    'Отчество' => trim($contract->user->patronymic),
                    'ИндексПоРегистрации' => trim($contract->user->regAddress->zip),
                    'ИндексФактическогоПроживания' => trim($contract->user->factAddress->zip),
                    'РайонОбластьПоРегистрации' => trim($contract->user->regAddress->region) . ' ' . trim($contract->user->regAddress->region_type),
                    'РайонОбластьФактическогоПроживания' => trim($contract->user->factAddress->region) . ' ' . trim($contract->user->factAddress->region_type),
                    'РайонПоРегистрации' => trim($contract->user->regAddress->district).' '.trim($contract->user->regAddress->district_type),
                    'РайонФактическогоПроживания' => trim($contract->user->factAddress->district).' '.trim($contract->user->factAddress->district_type),
                    'ГородПоРегистрации' => (trim($contract->user->regAddress->city . ' ' . $contract->user->regAddress->city_type)),
                    'ГородФактическогоПроживания' => (trim($contract->user->factAddress->city . ' ' . $contract->user->factAddress->city_type)),
                    'НаселенныйПунктПоРегистрации' => (trim($contract->user->regAddress->locality . ' ' . $contract->user->regAddress->locality_type)),
                    'НаселенныйПунктФактическогоПроживания' => (trim($contract->user->factAddress->locality . ' ' . $contract->user->factAddress->locality_type)),
                    'УлицаПоРегистрации' => trim($contract->user->regAddress->street . ' ' . $contract->user->regAddress->street_type),
                    'УлицаФактическогоПроживания' => trim($contract->user->factAddress->street . ' ' . $contract->user->factAddress->street_type),
                    'ДомПоРегистрации' => trim($contract->user->regAddress->house),
                    'ДомФактическогоПроживания' => trim($contract->user->factAddress->house),
                    'КвартираПоРегистрации' => trim($contract->user->regAddress->room),
                    'КвартираФактическогоПроживания' => trim($contract->user->factAddress->room),
                    'ПредставлениеАдресаПоРегистрации' => trim($contract->user->regAddress->adressfull),
                    'ПредставлениеАдресаФактическогоПроживания' => trim($contract->user->factAddress->adressfull),
                    'МестоРаботы' => trim($contract->user->workplace),
                    'РабочийТелефон' => trim($contract->user->workphone),
                    'Email' => trim($contract->user->email),
                    'ДатаСоздания' => date('Y-m-d', strtotime($contract->user->created)),
                ];

            $i++;
        }

        $xml['Справочники']['Подразделение'] = ['Наименование' => 'АРХАНГЕЛЬСК 1', 'УИД' => 1];
        $xml['Справочники']['Организация'] = ['Наименование' => 'ООО МКК "БАРЕНЦ ФИНАНС"', 'УИД' => 1];

        $xml['Справочники'][$i]['СервисыОнлайнОплаты'] =
            [
                'Наименование' => 'Best2Pay',
                'УИД' => '1',
            ];
        $i++;

        $xml['Справочники'][$i]['КредитныеПродукты'] =
            [
                'Наименование' => 'Стандартный-1',
                'УИД' => 1,
                'Процент' => 1
            ];

        $i++;

        $promocodes = PromocodesORM::get();

        foreach ($promocodes as $promocode) {
            if ($promocode->id >= 41) {
                $percent = 0.8 - ($promocode->discount / 100);
            } else {
                $percent = 1 - ($promocode->discount / 100);
            }

            $xml['Справочники'][$i]['КредитныеПродукты'] =
                [
                    'Наименование' => 'Стандартный-' . $promocode->id,
                    'УИД' => $promocode->id,
                    'Процент' => $percent
                ];
            $i++;
        }

        foreach ($contracts as $contract) {
            // в двух местах
            if (in_array($contract->user_id, $exceptions_array)) {
                continue;
            }
            $issuanceOperation = OperationsORM::where('contract_id', $contract->id)->where('type', 'P2P')->first();

            $xml['Документы'][$i]['Сделка'] =
                [
                    'ДатаЗайма' => date('Y-m-d', strtotime($contract->inssuance_date)),
                    'НомерЗайма' => $contract->number,
                    'УИД' => $contract->id,
                    'ПСК' => number_format(round($contract->base_percent * 365, 3), 3, '.', ''),
                    'Организация' => 1,
                    'Подразделение' => 1,
                    'СервисДистанционнойВыдачи' => 1,
                    'СуммаЗайма' => number_format(round($issuanceOperation->amount, 2), 2, '.', ''),
                    'ДатаВозврата' => date("Y-m-d", strtotime("+" . $contract->period . " days", strtotime($contract->inssuance_date))),
                    'Заемщик' => $contract->user->id,
                    'Процент' => $contract->base_percent,
                    'ПроцентПовышенный' => $contract->base_percent,
                    'ПроцентПриПросрочке' => $contract->base_percent + 0.05,
                    'ДатаПолнойОплаты' => $contract->close_date ? date('Y-m-d', strtotime($contract->close_date)) : '',
                    'ТипДокументаРасхода' => 2,
                    'ДатаРасхода' => date('Y-m-d', strtotime($contract->inssuance_date)),
                ];

            if (isset($xml['Документы'][$i]['Сделка']['ДатаПолнойОплаты']) && empty($xml['Документы'][$i]['Сделка']['ДатаПолнойОплаты'])) {
                unset($xml['Документы'][$i]['Сделка']['ДатаПолнойОплаты']);
            }


            $xml['Документы'][$i]['Сделка']['НомерДокументаРасхода'] = $issuanceOperation->id;
            $xml['Документы'][$i]['Сделка']['РасчетВоВнешнейСистеме'] = 'false';
            // $xml['Документы'][$i]['Сделка']['ЦельЗаймаДляНБКИ'] = 99; 
            // $xml['Документы'][$i]['Сделка']['СпособОформленияЗаявки'] = 8;

            $promocodes = PromocodesORM::get();

            $product = ['КредитныйПродукт' => 1];

            if (strtotime($contract->inssuance_date) >= strtotime('29.06.2023 15:20:00')) {
                $product = ['КредитныйПродукт' => 42];
            }

            if (in_array($contract->number, ['0628-5113', '0628-5094', '0629-5157', '0629-5150', '0628-5121', '0628-5138'])) {
                $product = ['КредитныйПродукт' => 1];
            }

            $order = OrdersORM::where('contract_id', $contract->id)->first();

            foreach ($promocodes as $promocode) {
                if ($order->promocode_id == $promocode->id)
                    $product = ['КредитныйПродукт' => $promocode->id];
            }

            $xml['Документы'][$i]['Сделка'] = array_slice($xml['Документы'][$i]['Сделка'], 0, 4, true) +
                $product +
                array_slice($xml['Документы'][$i]['Сделка'], 4, count($xml['Документы'][$i]['Сделка']) - 4, true);

            $operations = OperationsORM::where('contract_id', $contract->id)->where('type', 'PAY')->get();

            $k = 0;

            foreach ($operations as $operation) {
                $transaction = TransactionsORM::find($operation->transaction_id);

                if (in_array($transaction->id, [211976, 206587, 219692, 219685, 225954, 230708, 242605, 244169, 247731, 245536, 250876, 257126, 266059, 266998, 270638, 272493, 287999, 288317, 291524, 296531, 300208, 301560, 307185, 307836, 309244, 316454]))
                    continue;

                if (!empty($transaction)) {
                    $xml['Документы'][$i]['Сделка'][$k]['Оплаты'] =
                        [
                            'НомерПриходника' => $operation->id,
                            'ДатаОплаты' => date('Y-m-d', strtotime($operation->created)),
                            'СуммаОплаты' => number_format(round($operation->amount, 2), 2, '.', ''),
                            'ТипДокумента' => 2,
                            'Подразделение' => 1,
                            'СервисОнлайнОплаты' => 1,
                            'СуммаПроцентовОплаченных' => ($transaction->loan_percents_summ != null) ? $transaction->loan_percents_summ : 0,
                            'СуммаШтрафовОплаченных' => ($transaction->loan_peni_summ != null) ? $transaction->loan_peni_summ : 0,
                            'СуммаОсновногоДолга' => ($transaction->loan_body_summ != null) ? $transaction->loan_body_summ : 0
                        ];
                    $k++;
                }
            }
            $k = 0;
            foreach ($operations as $operation) {
                $transaction = TransactionsORM::find($operation->transaction_id);

                if (!empty($transaction)) {
                    if ($transaction->prolongation == 1) {
                        $xml['Документы'][$i]['Сделка'][]['ДатыПролонгации'] = [
                            'ДатаПролонгации' => date('Y-m-d', strtotime($operation->created)),
                            'ДатаВозврата' => date('Y-m-d', strtotime($operation->created) + (86400 * 30)),
                        ];
                    }
                    $k++;
                }
            }
            $i++;
        }

        return self::processing($xml);
    }

    private static function processing($xml)
    {
        $xmlSerializer = new XMLSerializer("Выгрузка xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns='http://localhost/mfo'", 'Выгрузка');
        $xml = $xmlSerializer->serialize($xml);
        self::$params = $xml;

        return self::response($xml);
    }

    private static function response($resp)
    {
        self::toLogs($resp);

        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="client.xml"');

        echo $resp;
    }

    private static function toLogs($log)
    {
        $insert =
            [
                'className' => self::class,
                'log' => $log
            ];

        LogsORM::insert($insert);
    }
}