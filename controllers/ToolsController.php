<?php

ini_set('max_execution_time', 40);

class ToolsController extends Controller
{
    public function fetch()
    {
        if (in_array('analitics', $this->manager->permissions)) {

            if ($this->request->post('action') && $this->request->post('action') == 'generate_fedresurs') {
                return $this->action_generate_fedresurs();
            }

            switch ($this->request->get('action', 'string')):
                case 'integrations':
                    return $this->action_integrations();
                    break;

                case 'main':
                    return $this->action_main();
                    break;

                case 'short_link':
                    return $this->action_short_link();
                    break;

                case 'reminders':
                    return $this->action_reminders();
                    break;

                case 'onec_download':
                    return $this->action_onec_download();
                    break;

                case 'fedresurs':
                    return $this->action_fedresurs();
                    break;

            endswitch;
        }
    }

    private function action_main()
    {
        return $this->design->fetch('tools/main.tpl');
    }

    private function action_integrations()
    {
        if ($this->request->method('post')) {
            switch ($this->request->post('action', 'string')):

                case 'add_integration':
                    $this->action_add_integration();
                    break;

                case 'delete_integration':
                    $this->action_delete_integration();
                    break;

                case 'get_integration':
                    $this->action_get_integration();
                    break;

                case 'update_integration':
                    $this->action_update_integration();
                    break;

            endswitch;
        }

        $integrations = $this->Integrations->get_integrations();
        $this->design->assign('integrations', $integrations);


        return $this->design->fetch('tools/integrations.tpl');
    }

    private function action_add_integration()
    {
        $utm_source = $this->request->post('utm_source', 'string');
        $utm_medium = $this->request->post('utm_medium', 'string');
        $utm_campaign = $this->request->post('utm_campaign', 'string');
        $utm_term = $this->request->post('utm_term', 'string');
        $utm_content = $this->request->post('utm_content', 'string');
        $utm_source_name = $this->request->post('utm_source_name', 'string');
        $utm_medium_name = $this->request->post('utm_medium_name', 'string');
        $utm_campaign_name = $this->request->post('utm_campaign_name', 'string');
        $utm_term_name = $this->request->post('utm_term_name', 'string');
        $utm_content_name = $this->request->post('utm_content_name', 'string');

        $integration =
            [
                'name' => $utm_source,
                'utm_source' => $utm_source,
                'utm_source_name' => $utm_source_name,
                'utm_medium' => ($utm_medium) ? $utm_medium : ' ',
                'utm_campaign' => ($utm_campaign) ? $utm_campaign : ' ',
                'utm_term' => ($utm_term) ? $utm_term : ' ',
                'utm_content' => ($utm_content) ? $utm_content : ' ',
                'utm_medium_name' => ($utm_medium_name) ? $utm_medium_name : ' ',
                'utm_campaign_name' => ($utm_campaign_name) ? $utm_campaign_name : ' ',
                'utm_term_name' => ($utm_term_name) ? $utm_term_name : ' ',
                'utm_content_name' => ($utm_content_name) ? $utm_content_name : ' '
            ];

        $result = $this->Integrations->add_integration($integration);

        if ($result != 0) {
            echo json_encode(['resp' => 'success']);
        } else {
            echo json_encode(['resp' => 'error']);
        }

        exit;
    }

    private function action_update_integration()
    {
        $integration_id = $this->request->post('integration_id');

        $utm_source = $this->request->post('utm_source', 'string');
        $utm_medium = $this->request->post('utm_medium', 'string');
        $utm_campaign = $this->request->post('utm_campaign', 'string');
        $utm_term = $this->request->post('utm_term', 'string');
        $utm_content = $this->request->post('utm_content', 'string');
        $utm_source_name = $this->request->post('utm_source_name', 'string');
        $utm_medium_name = $this->request->post('utm_medium_name', 'string');
        $utm_campaign_name = $this->request->post('utm_campaign_name', 'string');
        $utm_term_name = $this->request->post('utm_term_name', 'string');
        $utm_content_name = $this->request->post('utm_content_name', 'string');

        $integration =
            [
                'name' => $utm_source,
                'utm_source' => $utm_source,
                'utm_source_name' => $utm_source_name,
                'utm_medium' => ($utm_medium) ? $utm_medium : ' ',
                'utm_campaign' => ($utm_campaign) ? $utm_campaign : ' ',
                'utm_term' => ($utm_term) ? $utm_term : ' ',
                'utm_content' => ($utm_content) ? $utm_content : ' ',
                'utm_medium_name' => ($utm_medium_name) ? $utm_medium_name : ' ',
                'utm_campaign_name' => ($utm_campaign_name) ? $utm_campaign_name : ' ',
                'utm_term_name' => ($utm_term_name) ? $utm_term_name : ' ',
                'utm_content_name' => ($utm_content_name) ? $utm_content_name : ' '
            ];

        $result = $this->Integrations->update_integration($integration_id, $integration);

        if ($result != 0) {
            echo json_encode(['resp' => 'success']);
        } else {
            echo json_encode(['resp' => 'error']);
        }

        exit;
    }


    private function action_delete_integration()
    {

        $integration_id = $this->request->post('integration_id');

        $this->Integrations->delete_integration($integration_id);

        echo json_encode(['resp' => 'success']);

        exit;
    }

    private function action_get_integration()
    {
        $integration_id = $this->request->post('integration_id');

        $integration = $this->Integrations->get_integration($integration_id);

        echo json_encode($integration);

        exit;
    }

    private function action_short_link()
    {

        if ($this->request->method('post')) {


            if ($this->request->post('action', 'string') == 'change_link') {
                $this->change_link();
            } elseif ($this->request->post('action', 'string') == 'del_link') {
                $this->del_link();
            } else {
                $page = new StdClass();

                $page->url = $this->request->post('url');
                $page->link = $this->request->post('link');

                $exist_page = $this->shortlink->get_link($page->url);

                if (!empty($exist_page)) {
                    $this->design->assign('message_error', 'Данное сокращение уже используется');
                } elseif (empty($page->url)) {
                    $this->design->assign('message_error', 'Укажите сокращение');
                } elseif (empty($page->link)) {
                    $this->design->assign('message_error', 'Укажите ссылку');
                } else {

                    $page->id = $this->shortlink->add_link($page);
                    $this->design->assign('message_success', 'Ссылка сохранена');

                }
            }


        } else {

        }

        $pages = $this->shortlink->get_links();
        $this->design->assign('pages', $pages);

        return $this->design->fetch('tools/short_link.tpl');
    }

    private function change_link()
    {
        $id = $this->request->post('idlink');
        $url = $this->request->post('url', 'string');
        $link = $this->request->post('link');


        $data =
            [
                'url' => $url,
                'link' => $link
            ];

        $result = $this->shortlink->update_link($id, $data);

        if ($result != 0) {
            echo json_encode(['resp' => 'success', 'test' => $data]);
        } else {
            echo json_encode(['resp' => 'error']);
        }

        exit;
    }

    private function del_link()
    {
        $id = $this->request->post('id_link');

        $this->shortlink->del_link($id);
    }

    private function action_reminders()
    {
        if ($this->request->method('post')) {
            switch ($this->request->post('action', 'string')):

                case 'addReminder':
                    $this->action_addReminder();
                    break;

                case 'deleteReminder':
                    $this->action_deleteReminder();
                    break;

                case 'getReminder':
                    $this->action_getReminder();
                    break;

                case 'updateReminder':
                    $this->action_updateReminder();
                    break;

                case 'switchReminder':
                    $this->action_switchReminder();
                    break;

            endswitch;
        }

        $reminders = RemindersORM::get();
        $this->design->assign('reminders', $reminders);

        $remindersEvents = RemindersEventsORM::get();
        $this->design->assign('remindersEvents', $remindersEvents);

        $remindersSegments = RemindersSegmentsORM::get();
        $this->design->assign('remindersSegments', $remindersSegments);

        return $this->design->fetch('tools/reminders.tpl');
    }

    private function action_addReminder()
    {
        $eventId = $this->request->post('event');
        $segmentId = $this->request->post('segment');
        $typeTime = $this->request->post('typeTime');
        $count = $this->request->post('count');
        $msgSms = $this->request->post('msgSms');
        $msgZvon = $this->request->post('msgZvon');
        $timeToSend = $this->request->post('timeToSend');

        $insert =
            [
                'eventId' => $eventId,
                'segmentId' => $segmentId,
                'timeType' => $typeTime,
                'countTime' => $count,
                'msgSms' => $msgSms,
                'msgZvon' => $msgZvon,
                'timeToSend' => $timeToSend
            ];

        RemindersORM::insert($insert);
        exit;
    }

    private function action_switchReminder()
    {
        $id = $this->request->post('id');
        $value = $this->request->post('value');

        RemindersORM::where('id', $id)->update(['is_on' => $value]);
        exit;
    }

    private function action_getReminder()
    {
        $id = $this->request->post('id');
        $reminder = RemindersORM::find($id);

        echo json_encode($reminder);
        exit;
    }

    private function action_updateReminder()
    {
        $id = $this->request->post('id');
        $eventId = $this->request->post('event');
        $segmentId = $this->request->post('segment');
        $typeTime = $this->request->post('typeTime');
        $count = $this->request->post('count');
        $msgSms = $this->request->post('msgSms');
        $msgZvon = $this->request->post('msgZvon');
        $timeToSend = $this->request->post('timeToSend');

        $update =
            [
                'eventId' => $eventId,
                'segmentId' => $segmentId,
                'timeType' => $typeTime,
                'countTime' => $count,
                'msgSms' => $msgSms,
                'msgZvon' => $msgZvon,
                'timeToSend' => $timeToSend
            ];

        RemindersORM::where('id', $id)->update($update);
        exit;
    }

    private function action_deleteReminder()
    {
        $id = $this->request->post('id');
        RemindersORM::destroy($id);
        exit;
    }

    private function action_onec_download()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $from = date('Y-m-d 00:00:00', strtotime($from));
            $to   = date('Y-m-d 23:59:59', strtotime($to));

            $contracts = ContractsORM::with('user.regAddress', 'user.factAddress', 'order')
                ->whereIn('status', [2,3,4,11])
                ->whereBetween('inssuance_date', [$from, $to])
                ->get();

            Onec::action(['method' => 'sendIssuedContracts', 'items' => $contracts]);
            exit;
        }


        return $this->design->fetch('tools/onec_downloads.tpl');
    }

    private function action_fedresurs()
    {
        if ($daterange = $this->request->get('daterange')) {
            list($from, $to) = explode('-', $daterange);

            $this->design->assign('from', $from);
            $this->design->assign('to', $to);

            $from = date('Y-m-d 00:00:00', strtotime($from));
            $to   = date('Y-m-d 23:59:59', strtotime($to));
            $operations = OperationsORM::query()
                ->whereBetween('created', [$from, $to])
                ->where('type', '=', 'PENI')
                ->groupBy('contract_id')
                ->get();
            $contracts = [];
            foreach ($operations as $operation) {
                $contract = $operation->contract;

                $date_from = new DateTime();
                $date_from->modify('-5 days');
                $date_to = new DateTime();
                $date_to->modify('-70 days');

                if ($contract && $contract->return_date < $date_from->format('Y-m-d') && $contract->return_date > $date_to->format('Y-m-d')) {
                    $operations = OperationsORM::query()
                        ->where('contract_id', '=', $contract->id)
                        ->where('type', '=', 'PENI')
                        ->get();
                    
                    // $contract->expired_days = count($operations);
                    $date1 = new DateTime(date('Y-m-d', strtotime($contract->return_date)));
                    $date2 = new DateTime(date('Y-m-d'));
                    $contract->expired_days =  $date2->diff($date1)->days;

                    $contracts[] = $contract;
                }

            }
            $this->design->assign('contracts', $contracts);
        }


        return $this->design->fetch('tools/fedresurs.tpl');
    }

    public function action_generate_fedresurs() {

        $ids = $this->request->post('contracts');
        $type = $this->request->post('type');

        // $contracts = ContractsORM::whereIn('id', $ids)->get();

        $filter = [];
        $filter['id'] = $ids;
        $filter['sort'] = 'phone_asc';
        $contracts = $this->contracts->get_contracts($filter);

        $debtor = [];
        $prew_debtor = 0;
        $ctrs = [];
        foreach ($contracts as $contract) {
            $user = $this->users->get_user($contract->user_id);

            $user_lastname = $user->lastname;

            if ($prew_debtor == $user->inn) {
                $ctrs[] = [
                    // 'Uic' => $contract->id,
                    'Number' => $contract->number,
                    'Date' => date('Y-m-d', strtotime($contract->create_date)),
                    'InvolvementInfo' => [
                        'DateBegin' => date('Y-m-d', strtotime($contract->return_date. ' +5 days')),
                        'DateEnd' => date('Y-m-d', strtotime($contract->return_date. ' +45 days')),
                    ]
                ];

                $debtor[count($debtor)-1]['Contracts']['Contract'] = $ctrs;
            }
            else{

                $ctrs = [];
                $ctrs[] = [
                    // 'Uic' => $contract->id,
                    'Number' => $contract->number,
                    'Date' => date('Y-m-d', strtotime($contract->create_date)),
                    'InvolvementInfo' => [
                        'DateBegin' => date('Y-m-d', strtotime($contract->return_date. ' +5 days')),
                        'DateEnd' => date('Y-m-d', strtotime($contract->return_date. ' +45 days')),
                    ]
                ];

                $debtor[] = [
                    'IsRfCitizen' => 'true',
                    'LastName' => $user_lastname,
                    'FirstName' => $user->firstname,
                    'MiddleName' => $user->patronymic,
                    'Inn' => !empty($user->inn) ? $user->inn : $user->passport_serial,
                    'Document' => [
                        'Type' => [
                            'Code' => 'PassportRf',
                            'Description' => 'Российский паспорт',
                        ],
                        'Series' => explode('-', $user->passport_serial)[0],
                        'Number' => explode('-', $user->passport_serial)[1],
                    ],
                    'Contracts' => [
                        'Contract' => $ctrs,
                    ]
                ];
            }

            $prew_debtor = $user->inn;
            
        }

        // $debtor[] = [
        //     'IsRfCitizen' => 'true',
        //     'LastName' => $user_lastname,
        //     'FirstName' => $user->firstname,
        //     'MiddleName' => $user->patronymic,
        //     'Inn' => !empty($user->inn) ? $user->inn : $user->passport_serial,
        //     'Document' => [
        //         'Type' => [
        //             'Code' => 'PassportRf',
        //             'Description' => 'Российский паспорт',
        //         ],
        //         'Series' => explode('-', $user->passport_serial)[0],
        //         'Number' => explode('-', $user->passport_serial)[1],
        //     ],
        //     'Contracts' => [
        //         'Contract' => $ctrs,
        //     ]
        // ];   

        $data = [
            'Message' => [
                '@attributes' => [
                    'Number' => '01',
                    'Type' => $type,
                    'Ver' => '1.0'
                ],
                'MessageContentBase' => [
                    '@attributes' => [
                        'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                        'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                        'xsi:type' => $type
                    ],
                    'PublisherInfo' => [
                        '@attributes' => [
                            'xsi:type' => 'PublisherInfoCompany'
                        ],
                        'FullName' => 'ООО МКК "Баренц Финанс"',
                        'INN' => '9723120835',
                        'Ogrn' => '1217700350812'
                    ],
                    'Creditor' => [
                        '@attributes' => [
                            'xsi:type' => 'DebtCollectors.Participants.Company'
                        ],
                        'FullName' => 'ООО МКК "Баренц Финанс"',
                        'Inn' => '9723120835',
                        'Ogrn' => '1217700350812',
                        'LocationAddress' => '163045, Архангельск г., пр-д. К.С. Бадигина д.19, оф. 107',
                        'PostAddress' => '163045, Архангельск г., пр-д. К.С. Бадигина д.19, оф. 107',
                        'Email' => 'info@mkkbf.ru',
                        'Phone' => '88001018283'
                    ],
                    'DebtCollector' => [
                        '@attributes' => [
                            'xsi:type' => 'DebtCollectors.Participants.Company'
                        ],
                        'FullName' => 'ООО «КОЛЛЕКТОРСКОЕ АГЕНТСТВО «ШАМИЛЬ И ПАРТНЕРЫ»',
                        'Inn' => '6908019416',
                        'Ogrn' => '1216900005805',
                        'LocationAddress' => '171080, Тверская область, г. Бологое, ул. Кооперативная, д.4, кв. 38.',
                        'PostAddress' => '171080, Тверская область, г. Бологое, ул. Кооперативная, д.4, кв. 38.',
                        'Email' => 'shamil.collector@gmail.com',
                        'Phone' => ' 88007005346'
                    ],
                    'Debtors' => [
                        'Debtor' => $debtor,
                    ],
                ]
            ],
        ];

        $xml = \LaLit\Array2XML::createXML('Messages', $data);

        $path = $this->config->root_dir.'files/fedresurs.xml';
        file_put_contents($path, $xml->saveXML());
        echo json_encode(['status' => 'ok']);
        exit;
    }

}