<?php

class Onec implements ToolsInterface
{
    protected static $params;

    public static function request($contracts)
    {
        $i = 0;

        foreach ($contracts as $contract) {
            list($passportSerial, $passportNumber) = explode('-', $contract->user->passport_serial);

            $xml['Справочники'][$i]['Контрагент'] =
                [
                    'Наименование' => trim($contract->user->lastname . ' ' . $contract->user->firstname . ' ' . $contract->user->patronymic),
                    'УИД' => trim($contract->user->id),
                    "ВидКонтрагента" => 'ФизЛицо',
                    'СерияПаспорта' => trim($passportSerial),
                    'НомерПаспорта' => trim($passportNumber),
                    'ДатаРождения' => date('Y-m-d', strtotime($contract->user->birth)),
                    'КемВыданПаспорт' => trim($contract->user->passport_issued . ' ' . date('Y-m-d', strtotime($contract->user->passport_date))),
                    'КодПодразделения' => trim($contract->user->subdivision_code),
                    'МестоРождения' => trim($contract->user->birth_place),
                    'Пол' => ($contract->user->gender == 'female') ? 'Ж' : 'М',
                    'СотовыйТелефон' => trim($contract->user->phone_mobile),
                    'Фамилия' => trim($contract->user->lastname),
                    'Имя' => trim($contract->user->firstname),
                    'Отчество' => trim($contract->user->patronymic),
                    'ИндексПоРегистрации' => trim($contract->user->regAddress->zip),
                    'ИндексФактическогоПроживания' => trim($contract->user->factAddress->zip),
                    'РайонОбластьПоРегистрации' => trim($contract->user->regAddress->region),
                    'РайонОбластьФактическогоПроживания' => trim($contract->user->factAddress->region),
                    'ГородПоРегистрации' => trim($contract->user->regAddress->city . ' ' . $contract->user->regAddress->city_type),
                    'ГородФактическогоПроживания' => trim($contract->user->factAddress->city . ' ' . $contract->user->factAddress->city_type),
                    'УлицаПоРегистрации' => trim($contract->user->regAddress->street . ' ' . $contract->user->regAddress->street_type),
                    'УлицаФактическогоПроживания' => trim($contract->user->factAddress->street . ' ' . $contract->user->factAddress->street_type),
                    'ДомПоРегистрации' => trim($contract->user->regAddress->building),
                    'ДомФактическогоПроживания' => trim($contract->user->factAddress->building),
                    'КвартираПоРегистрации' => trim($contract->user->regAddress->room),
                    'КвартираФактическогоПроживания' => trim($contract->user->factAddress->room),
                    'ПредставлениеАдресаПоРегистрации' => trim($contract->user->regAddress->adressfull),
                    'ПредставлениеАдресаФактическогоПроживания' => trim($contract->user->factAddress->adressfull),
                    'МестоРаботы' => trim($contract->user->workplace),
                    'РабочийТелефон' => trim($contract->user->workphone),
                    'Email' => trim($contract->user->email),
                    'ДатаСоздания' => date('Y-m-d', strtotime($contract->user->created))
                ];

            $i++;
        }

        $xml['Справочники']['Подразделение'] = ['Наименование' => 'АРХАНГЕЛЬСК 1', 'УИД' => 1];
        $xml['Справочники']['Организация'] = ['Наименование' => 'ООО МКК "БАРЕНЦ ФИНАНС"', 'УИД' => 1];

        $xml['Справочники'][$i]['КредитныеПродукты'] =
            [
                'Наименование' => 'Стандартный',
                'УИД' => 1,
                'Процент' => 1
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

        foreach ($contracts as $contract) {

            $xml['Документы'][$i]['Сделка'] =
                [
                    'ДатаЗайма' => date('Y-m-d', strtotime($contract->create_date)),
                    'НомерЗайма' => $contract->number,
                    'УИД' => $contract->id,
                    'ПСК' => number_format(round($contract->base_percent * 365, 3), 3, '.', ''),
                    'Организация' => 1,
                    'Подразделение' => 1,
                    'СуммаЗайма' => number_format(round($contract->amount, 2), 2, '.', ''),
                    'ДатаВозврата' => date('Y-m-d', strtotime($contract->return_date)),
                    'Заемщик' => $contract->user->id,
                    'Процент' => $contract->base_percent,
                    'ПроцентПовышенный' => $contract->base_percent,
                    'ПроцентПриПросрочке' => $contract->base_percent + 0.005,
                    'ТипДокументаРасхода' => 0,
                    'ДатаРасхода' => date('Y-m-d', strtotime($contract->inssuance_date))
                ];

            $issuanceOperation = OperationsORM::where('contract_id', $contract->id)->where('type', 'P2P')->first();

            $xml['Документы'][$i]['Сделка']['НомерДокументаРасхода'] = $issuanceOperation->id;
            $xml['Документы'][$i]['Сделка']['РасчетВоВнешнейСистеме'] = 'false';

            $promocodes = PromocodesORM::get();

            $product = ['КредитныйПродукт' => 1];

            $order = OrdersORM::where('contract_id', $contract->id)->first();

            foreach ($promocodes as $promocode) {
                if ($order->promocode_id == $promocode->id)
                    $product = ['КредитныйПродукт' => $promocode->id];
            }

            $xml['Документы'][$i]['Сделка'] = array_slice($xml['Документы'][$i]['Сделка'], 0, 4, true) +
                $product +
                array_slice($xml['Документы'][$i]['Сделка'], 4, count($xml['Документы'][$i]['Сделка'])-4, true);

            $operations = OperationsORM::where('contract_id', $contract->id)->where('type', 'PAY')->get();

            $k = 0;

            foreach ($operations as $operation) {
                $transaction = TransactionsORM::find($operation->transaction_id);

                $xml['Документы'][$i]['Сделка'][$k]['Оплаты'] =
                    [
                        'НомерПриходника' => $operation->id,
                        'ДатаОплаты' => date('Y-m-d', strtotime($operation->created)),
                        'СуммаОплаты' => number_format(round($operation->amount, 2), 2, '.', ''),
                        'ТипДокумента' => 0,
                        'Подразделение' => 1,
                        'СуммаПроцентовОплаченных' => ($transaction->loan_percents_summ != null) ? $transaction->loan_percents_summ : 0,
                        'СуммаШтрафовОплаченных' => ($transaction->loan_peni_summ != null) ? $transaction->loan_peni_summ : 0,
                        'СуммаОсновногоДолга' => ($transaction->loan_body_summ != null) ? $transaction->loan_body_summ : 0
                    ];

                $k++;
            }

            $i++;
        }

        return self::processing($xml);
    }

    public static function processing($xml)
    {
        $xmlSerializer = new XMLSerializer("Выгрузка xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns='http://localhost/mfo'", 'Выгрузка');
        $xml = $xmlSerializer->serialize($xml);
        self::$params = $xml;

        return self::response($xml);
    }

    public static function response($resp)
    {
        self::toLogs($resp);

        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="client.xml"');

        echo $resp;
    }

    public static function toLogs($log)
    {
        $insert =
            [
                'className' => self::class,
                'log' => $log
            ];

        LogsORM::insert($insert);
    }
}