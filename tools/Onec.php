<?php

class Onec implements ToolsInterface
{
    protected static $params;

    public static function request($orderId)
    {
        $order = OrdersORM::with('user')->find($orderId);
        $contract = ContractsORM::where('order_id', $orderId)->first();

        list($passportSerial, $passportNumber) = explode('-', $order->user->passport_serial);

        $xml =
            [
                'Справочники' =>
                    [
                        'Контрагент' =>
                            [
                                'Наименование' => trim($order->user->lastname . ' ' . $order->user->firstname . ' ' . $order->user->patronymic),
                                'УИД' => trim($order->user->id),
                                "ВидКонтрагента" => 'ФизЛицо',
                                'СерияПаспорта' => trim($passportSerial),
                                'НомерПаспорта' => trim($passportNumber),
                                'ДатаРождения' => date('Y-m-d', strtotime($order->user->birth)),
                                'КемВыданПаспорт' => trim($order->user->passport_issued . ' ' . date('Y-m-d', strtotime($order->user->passport_date))),
                                'КодПодразделения' => trim($order->user->subdivision_code),
                                'МестоРождения' => trim($order->user->birth_place),
                                'Пол' => ($order->user->gender == 'female') ? 'Ж' : 'М',
                                'СотовыйТелефон' => trim($order->user->phone_mobile),
                                'Фамилия' => trim($order->user->lastname),
                                'Имя' => trim($order->user->firstname),
                                'Отчество' => trim($order->user->patronymic),
                                'ИндексПоРегистрации' => trim($order->user->regAddress->zip),
                                'ИндексФактическогоПроживания' => trim($order->user->factAddress->zip),
                                'РайонОбластьПоРегистрации' => trim($order->user->regAddress->region),
                                'РайонОбластьФактическогоПроживания' => trim($order->user->factAddress->region),
                                'ГородПоРегистрации' => trim($order->user->regAddress->city . ' ' . $order->user->regAddress->city_type),
                                'ГородФактическогоПроживания' => trim($order->user->factAddress->city . ' ' . $order->user->factAddress->city_type),
                                'УлицаПоРегистрации' => trim($order->user->regAddress->street . ' ' . $order->user->regAddress->street_type),
                                'УлицаФактическогоПроживания' => trim($order->user->factAddress->street . ' ' . $order->user->factAddress->street_type),
                                'ДомПоРегистрации' => trim($order->user->regAddress->building),
                                'ДомФактическогоПроживания' => trim($order->user->factAddress->building),
                                'КвартираПоРегистрации' => trim($order->user->regAddress->room),
                                'КвартираФактическогоПроживания' => trim($order->user->factAddress->room),
                                'ПредставлениеАдресаПоРегистрации' => trim($order->user->regAddress->adressfull),
                                'ПредставлениеАдресаФактическогоПроживания' => trim($order->user->factAddress->adressfull),
                                'МестоРаботы' => trim($order->user->workplace),
                                'РабочийТелефон' => trim($order->user->workphone),
                                'Email' => trim($order->user->email),
                                'ДатаСоздания' => date('Y-m-d', strtotime($order->user->created))
                            ],
                        'Подразделение' =>
                            [
                                'Наименование' => 'АРХАНГЕЛЬСК 1',
                                'УИД' => 1
                            ],
                        'Организация' =>
                            [
                                'Наименование' => 'ООО МКК "БАРЕНЦ ФИНАНС"',
                                'УИД' => 1
                            ]
                    ],
                'Документы' => [
                    'Сделка' =>
                        [
                            'ДатаЗайма' => date('Y-m-d', strtotime($contract->inssuance_date)),
                            'НомерЗайма' => $contract->number,
                            'УИД' => $contract->id,
                            'ПСК' => number_format(round($contract->base_percent * 365, 3), 3, '.', ''),
                        ]
                ]
            ];

        $xml['Справочники'][0]['КредитныеПродукты'] =
            [
                'Наименование' => 'Стандартный',
                'УИД' => 1,
                'Процент' => 1
            ];

        $promocodes = PromocodesORM::get();

        $i = 1;
        $xml['Документы']['Сделка']['КредитныйПродукт'] = 1;

        foreach ($promocodes as $promocode) {

            $percent = 1 - ($promocode->discount / 100);

            $xml['Справочники'][$i]['КредитныеПродукты'] =
                [
                    'Наименование' => 'Стандартный-' . $promocode->id,
                    'УИД' => $promocode->id,
                    'Процент' => $percent
                ];

            if ($percent == $contract->base_percent)
                $xml['Документы']['Сделка']['КредитныйПродукт'] = $promocode->id;

            $i++;
        }


        $xml['Документы']['Сделка']['Организация'] = 1;
        $xml['Документы']['Сделка']['Подразделение'] = 1;
        $xml['Документы']['Сделка']['СуммаЗайма'] = number_format(round($contract->amount, 2), 2, '.', '');
        $xml['Документы']['Сделка']['ДатаВозврата'] = date('Y-m-d', strtotime($contract->return_date));
        $xml['Документы']['Сделка']['Заемщик'] = $order->user->id;
        $xml['Документы']['Сделка']['ДатаПолнойОплаты'] = date('Y-m-d', strtotime($contract->return_date));
        $xml['Документы']['Сделка']['ТипДокументаРасхода'] = 0;

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