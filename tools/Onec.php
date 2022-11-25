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
                'Контрагент' =>
                    [
                        'Наименование' => $order->user->lastname . '' . $order->user->firstname . '' . $order->user->patronymic,
                        'УИД' => $order->user->id,
                        'СерияПаспорта' => $passportSerial,
                        'НомерПаспорта' => $passportNumber,
                        'ДатаВыдачиПаспорта' => date('Y-m-d', strtotime($order->user->passport_date)),
                        'ДатаРождения' => date('Y-m-d', strtotime($order->user->birth)),
                        'КемВыданПаспорт' => $order->user->passport_issued,
                        'КодПодразделения' => $order->user->subdivision_code,
                        'МестоРождения' => $order->user->birth_place,
                        'Пол' => $order->user->gender,
                        'СотовыйТелефон' => $order->user->phone_mobile,
                        'Фамилия' => $order->user->lastname,
                        'Имя' => $order->user->firstname,
                        'Отчество' => $order->user->patronymic,
                        'СНИЛС' => $order->user->snils,
                        'ИНН' => $order->user->inn,
                        'ИндексПоРегистрации' => $order->user->regAddress->zip,
                        'ИндексФактическогоПроживания' => $order->user->factAddress->zip,
                        'РайонОбластьПоРегистрации' => $order->user->regAddress->region . ' ' . $order->user->regAddress->region_type,
                        'РайонОбластьФактическогоПроживания' => $order->user->factAddress->region . ' ' . $order->user->factAddress->region_type,
                        'РайонПоРегистрации' => $order->user->regAddress->district . ' ' . $order->user->regAddress->district_type,
                        'РайонФактическогоПроживания' => $order->user->factAddress->district . ' ' . $order->user->factAddress->district_type,
                        'ГородПоРегистрации' => $order->user->regAddress->city . ' ' . $order->user->regAddress->city_type,
                        'ГородФактическогоПроживания' => $order->user->factAddress->city . ' ' . $order->user->factAddress->city_type,
                        'НаселенныйПунктПоРегистрации' => $order->user->regAddress->locality . ' ' . $order->user->regAddress->locality_type,
                        'НаселенныйПунктФактическогоПроживания' => $order->user->factAddress->locality . ' ' . $order->user->factAddress->locality_type,
                        'УлицаПоРегистрации' => $order->user->regAddress->street . ' ' . $order->user->regAddress->street_type,
                        'УлицаФактическогоПроживания' => $order->user->factAddress->street . ' ' . $order->user->factAddress->street_type,
                        'ДомПоРегистрации' => $order->user->regAddress->house,
                        'ДомФактическогоПроживания' => $order->user->factAddress->house,
                        'КорпусПоРегистрации' => $order->user->regAddress->building,
                        'КорпусФактическогоПроживания' => $order->user->factAddress->building,
                        'КвартираПоРегистрации' => $order->user->regAddress->room,
                        'КвартираФактическогоПроживания' => $order->user->factAddress->room,
                        'ПредставлениеАдресаПоРегистрации' => $order->user->regAddress->adressfull,
                        'ПредставлениеАдресаФактическогоПроживания' => $order->user->factAddress->adressfull,
                        'МестоРаботы' => $order->user->workplace,
                        'РабочийТелефон' => $order->user->workphone,
                        'Email' => $order->user->email,
                        'СреднемесячныйДоход' => $order->user->income,
                        'ДатаСоздания' => date('Y-m-d', strtotime($order->user->created))
                    ],
                'Сделка' =>
                    [
                        'ДатаЗайма' => date('Y-m-d', strtotime($contract->inssuance_date)),
                        'НомерЗайма' => $contract->number,
                        'УИД' => $contract->id,
                        'ПСК' => round($contract->base_percent * 365, 3),
                        'Организация' => 1,
                        'Подразделение' => 1,
                        'СуммаЗайма' => round($contract->amount, 2),
                        'ДатаВозврата' => date('Y-m-d', strtotime($contract->return_date)),
                        'Заемщик' => $order->user->id,
                        'ДатаПолнойОплаты' => date('Y-m-d', strtotime($contract->return_date)),
                    ]
            ];

        return self::processing($xml);
    }

    public static function processing($xml)
    {
        $xmlSerializer = new XMLSerializer();
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