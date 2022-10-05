<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set ( 'max_execution_time', 1200); 
require 'autoload.php';

$core = new Core();

$order_id = '249253';
$resp = $core->Onec->send_pdn($order_id);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';


exit;

?>
<body> 
<div id="content"> 
<div class="content-container"> 
  <h3>Ошибка HTTP 401.2 — Unauthorized</h3> 
  <h4>Вы не имеете права просматривать эту страницу из-за недопустимости заголовков проверки подлинности.</h4> 
</div> 
<div class="content-container"> 
 <fieldset><h4>Наиболее вероятные причины:</h4> 
  <ul> 	<li>В IIS не задан какой-либо протокол проверки подлинности (в том числе анонимный).</li> 	<li>Активированы только встроенные средства проверки подлинности, но при этом использовался клиентский веб-браузер, не поддерживающий проверку подлинности с помощью встроенных средств.</li> 	<li>Активированы встроенные средства проверки подлинности, при этом запрос был направлен через прокси, который изменил заголовки проверки подлинности еще до того, как они попали на веб-сервер.</li> 	<li>Настройки веб-сервера не допускают предоставления анонимного доступа, при этом необходимый заголовок авторизации не был получен.</li> 	<li>Возможно, что в разделе конфигурации "configuration/system.webServer/authorization" доступ для данного пользователя заблокирован явным образом.</li> </ul> 
 </fieldset> 
</div> 
<div class="content-container"> 
 <fieldset><h4>Возможные решения:</h4> 
  <ul> 	<li>Проверьте установку проверки подлинности для данного ресурса и затем попытайтесь обратиться к ресурсу с помощью метода проверки подлинности.</li> 	<li>Удостоверьтесь в том, что клиентский веб-браузер обеспечивает выполнение процедуры проверки подлинности с помощью встроенных средств.</li> 	<li>Удостоверьтесь в том, что при использовании встроенных средств проверки подлинности запрос не проходит через прокси-сервер.</li> 	<li>Убедитесь в том, что в разделе конфигурации "configuration/system.webServer/authorization" не содержится явный запрет на доступ данного пользователя.</li> 	<li>Создайте правило трассировки, чтобы отслеживать невыполненные запросы для этого кода состояния HTTP. Чтобы получить дополнительные сведения о создании правила трассировки для невыполненных запросов, щелкните <a href="http://go.microsoft.com/fwlink/?LinkID=66439">здесь</a>. </li> </ul> 
 </fieldset> 
</div> 
 
<div class="content-container"> 
 <fieldset><h4>Подробные сведения об ошибке:</h4> 
  <div id="details-left"> 
   <table border="0" cellpadding="0" cellspacing="0"> 
    <tr class="alt"><th>Модуль</th><td>nbsp;&nbsp;&nbsp;IIS Web Core</td></tr> 
    <tr><th>Уведомление</th><td>&nbsp;&nbsp;&nbsp;AuthenticateRequest</td></tr> 
    <tr class="alt"><th>Обработчик</th><td>&nbsp;&nbsp;&nbsp;1C Web-service Extension</td></tr> 
    <tr><th>Код ошибки</th><td>&nbsp;&nbsp;&nbsp;0x80070005</td></tr> 
     
   </table> 
  </div> 
  <div id="details-right"> 
   <table border="0" cellpadding="0" cellspacing="0"> 
    <tr class="alt"><th>Запрошенный URL-адрес</th><td>&nbsp;&nbsp;&nbsp;http://45.137.152.39:80/aspectsql/hs/Request</td></tr> 
    <tr><th>Физический путь</th><td>&nbsp;&nbsp;&nbsp;C:\\inetpub\\wwwroot\\aspectsql\\hs\\Request</td></tr> 
    <tr class="alt"><th>Метод входа</th><td>&nbsp;&nbsp;&nbsp;Пока не определено</td></tr> 
    <tr><th>Пользователь, выполнивший вход</th><td>&nbsp;&nbsp;&nbsp;Пока не определено</td></tr> 
     
   </table> 
   <div class="clear"></div> 
  </div> 
 </fieldset> 
</div> 
 
<div class="content-container"> 
 <fieldset><h4>Дополнительные сведения:</h4> 
  Эта ошибка отмечается в тех случаях, когда направляемый на веб-сервер заголовок WWW-Authenticate не поддерживается конфигурацией сервера. Выясните, какой метод проверки подлинности применяется к данному ресурсу и какой метод проверки подлинности использовал клиент. Эта ошибка отмечается в случаях, когда используются разные методы проверки подлинности. Чтобы определить, какой тип проверки подлинности использует клиент, уточните настройки проверки подлинности для клиента. 
  <p><a href="http://go.microsoft.com/fwlink/?LinkID=62293&amp;IIS70Error=401,2,0x80070005,14393">Просмотреть дополнительные сведения &raquo;</a></p> 
  <p>Статьи базы знаний Microsoft Knowledge Base:</p> 
 <ul><li>907273</li><li>253667</li></ul> 
 
 </fieldset> 
</div> 
</div> 
</body> 

<?php
exit;

$op = '409054251';
$resp = $core->best2pay->get_register_info(8081, $op);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
exit;

$core->db->query("
    SELECT * 
    FROM __orders
    WHERE sent_1c = 0
    AND status = 5
    ORDER BY id ASC
    LIMIT 10, 1
");
$order = $core->db->result();

$resp = $core->onec->send_order($order->id);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($order, $resp);echo '</pre><hr />';


exit;

exit;

$core->db->query("
SELECT * FROM `s_operations` 
WHERE type='BUD_V_KURSE' 
AND sent_receipt = 0 
LIMIT 40
");
$results = $core->db->results();

foreach ($results as $op)
{
    $resp = $core->ekam->send_bud_v_kurse($op->order_id);
    $core->operations->update_operation($op->id, array('sent_receipt'=>1));

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
    
}
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

exit;


$core->db->query("
    SELECT * FROM __operations
    WHERE type = 'REJECT_REASON'
    AND sent_receipt = 0
    ORDER BY id ASC
    LIMIT 30
");
$operations = $core->db->results();
foreach ($operations as $operation)
{
    $resp = $core->ekam->send_reject_reason($operation->order_id);
    $core->operations->update_operation($operation->id, array('sent_receipt'=>1));
    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operation, $resp);echo '</pre><hr />';
}


exit;


$core->db->query("
    DELETE FROM __operations
    WHERE DATE(created) = '2022-12-31'
    AND type = 'PERCENTS'
");




exit;
$core->db->query("
    SELECT *
    FROM __operations
    WHERE DATE(created) = '2022-01-15'
    AND type = 'PERCENTS'
");
$operations = $core->db->results();

foreach ($operations as $operation)
{
    $query = $core->db->placehold("
        UPDATE __contracts
        SET stop_profit = 0
        WHERE id = ?
    ", $operation->contract_id);
    $core->db->query($query);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query);echo '</pre><hr />';    
    
    if ($operation->contract_id != 102744 || $operation->contract_id != 102337)
    {
/*    
    $query = $core->db->placehold("
        UPDATE __contracts
        SET loan_percents_summ = loan_percents_summ - ?
        , checked = 1
        WHERE id = ?
    ", $operation->amount, $operation->contract_id);
    $core->db->query($query);
*/    
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query, $operation->order_id);echo '</pre><hr />';
    }
}

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operations);echo '</pre><hr />';

















exit;



$core->db->query("
    SELECT * 
    FROM __contracts
    WHERE (status = 4 OR status = 2)
    AND stop_profit = 0
");
$contracts = $core->db->results();

$need_contracts = array();
foreach ($contracts as $contract)
{
    $max_profit = $contract->amount * 2.5;
    $max_profit_with_paid = $max_profit - $contract->allready_paid;
    
    $total = $contract->loan_body_summ + $contract->loan_percents_summ;
    
    if ($total > $max_profit_with_paid)
    {
        $contract->max_profit = $max_profit;
        $contract->max_profit_with_paid = $max_profit_with_paid;
        $contract->new_percents_summ = $max_profit_with_paid - $contract->loan_body_summ;
        
        
        $need_contracts[] = $contract;
        
        
//        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract);echo '</pre><hr />';
    }
    
    foreach ($need_contracts as $contr)
    {
        $core->contracts->update_contract($contr->id, array(
            'loan_percents_summ' => $contr->new_percents_summ,
            'stop_profit' => 1
        ));
    }
    
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($need_contracts);echo '</pre><hr />';


exit;

$core->db->query("
    SELECT * FROM `s_operations` WHERE type='PAY' AND contract_id IS NOT NULL ORDER BY `id` DESC 
");
$pays = $core->db->results();

foreach ($pays as $pay)
{
    $query = $core->db->placehold("
        UPDATE __contracts
        SET allready_paid = allready_paid + ?
        WHERE id = ?
    ", $pay->amount, $pay->contract_id);
    $core->db->query($query);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query);echo '</pre><hr />';
}

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($pays);echo '</pre><hr />';

exit;


exit;


$data = '{ 
  "ID" : "KreditAPI", 
  "creditproduct": "ОНЛАЙН-ЗАЙМ ДО 30 ДНЕЙ", 
  "last_name" : "val(loan.user.last_name)", 
  "first_name" : "val(loan.user.first_name)", 
  "middle_name" : "val(loan.user.patronymic)", 
  "phone" : "val(loan.user.phone)",  
  "birthday" : "val(loan.user.birthdate|date:Y-m-d)", 
  "email" : "val(loan.user.email)",  
  "amount" : "val(loan.amount)", 
  "date" : "val(common.issued_at_or_created_at|date:Y-m-d)", 
  "data_request" : "val(loan.createdAt|date:Y-m-d)", 
  "uid": "val(loan.uid)", 
  "period" : "val(common.final_payment_date|date:Y-m-d)", 
  "id_sex" : "val(common.user_gender_rus)",  
  "passport_series" : "val(loan.user.passport.series)",  
  "passport_number" : "val(loan.user.passport.number)",  
  "passport_date_of_issue" : "val(loan.user.passport.issued_at|date:Y-m-d)",  
  "birthplace" : "val(loan.user.birth_place)",  
  "passport_org" : "val(loan.user.passport.issued_by)",  
  "passport_code" : "val(loan.user.passport.code)",  
  "incoming" : "val(loan.user.job.income)",  
  "work_name" : "val(loan.user.job.company|escapeQuotas)",  
  "work_phone" : "val(loan.user.job.mainphone)",  
  "residential_index": "val(loan.user.address_res.index)",  
  "residential_region" : "val(common.russian_residential_region_name)",  
  "residential_city" : "val(loan.user.address_res.city)",  
  "residential_street" : "val(loan.user.address_res.street)",  
  "residential_house" : "val(loan.user.address_res.house)",  
  "residential_apartment" : "val(loan.user.address_res.flat)",  
  "registration_index": "val(loan.user.address_reg.index)",  
  "registration_region" : "val(common.russian_registration_region_name)",  
  "registration_city" : "val(loan.user.address_reg.city)",  
  "registration_street" : "val(loan.user.address_reg.street)",  
  "registration_house" : "val(loan.user.address_reg.house)",  
  "registration_apartment" : "val(loan.user.address_reg.flat)", 
  "status":"val(common.mymfo_status)", 
  "decision": "val(common.mymfo_decision)", 
  "NewLoanFromLK":"val(common.mymfo_isnew_flag)", 
  "nomerrashodnika": "Дистанционно", 
  "service": "МАНДАРИН БАНКОВСКАЯ КАРТА"  
}';


$resp = $core->onec->test($data);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';

exit;

$core->db->query("
    SELECT *
    FROM __documents
    WHERE type = 'SOGLASIE_VZAIMODEYSTVIE'
");

$results = $core->db->results();
$results = array_map(function($var){
    $var->params = unserialize($var->params);
    return $var;
}, $results);


echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';
exit;

foreach ($results as $r)
{
    $r->params = unserialize($r->params);
    
    $core->db->query("
        SELECT * 
        FROM __documents
        WHERE type = 'DOP_USLUGI_VIDACHA'
        AND contract_id = ?
    ", $r->contract_id);
    $dop_doc = $core->db->result();
    $dop_doc->params = unserialize($dop_doc->params);

    $insurance = $core->insurances->get_insurance($dop_doc->params['insurance']->id);

    $dop_doc->params['insurance'] = $insurance;
    $dop_doc->params['insurance_summ'] = $core->insurances->get_insurance_cost($dop_doc->params['amount']);

    $core->documents->update_document($r->id, array('params'=>$dop_doc->params));
    $core->documents->update_document($dop_doc->id, array('params'=>$dop_doc->params));
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';



exit;









    function create_document($document_type, $contract, $core)
    {
        $ob_date = new DateTime();
        $ob_date->add(DateInterval::createFromDateString($contract->period.' days'));
        $return_date = $ob_date->format('Y-m-d H:i:s');

        $return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
        $return_amount_rouble = (int)$return_amount;
        $return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

        $contract_order = $core->orders->get_order((int)$contract->order_id);
        
        $params = array(
            'lastname' => $contract_order->lastname,
            'firstname' => $contract_order->firstname,
            'patronymic' => $contract_order->patronymic,
            'phone' => $contract_order->phone_mobile,
            'birth' => $contract_order->birth,
            'number' => $contract->number,
            'contract_date' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
            'return_date' => $return_date,
            'return_date_day' => date('d', strtotime($return_date)),
            'return_date_month' => date('m', strtotime($return_date)),
            'return_date_year' => date('Y', strtotime($return_date)),
            'return_amount' => $return_amount,
            'return_amount_rouble' => $return_amount_rouble,
            'return_amount_kop' => $return_amount_kop,
            'base_percent' => $contract->base_percent,
            'amount' => $contract->amount,
            'period' => $contract->period,
            'return_amount_percents' => round($contract->amount * $contract->base_percent * $contract->period / 100, 2),
            'passport_serial' => $contract_order->passport_serial,
            'passport_date' => $contract_order->passport_date,
            'subdivision_code' => $contract_order->subdivision_code,
            'passport_issued' => $contract_order->passport_issued,
            'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
            'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
            'asp' => $contract->accept_code,
            'insurance_summ' => $core->insurances->get_insurance_cost($contract->amount),
        );
        $regaddress_full = empty($contract_order->Regindex) ? '' : $contract_order->Regindex.', ';
        $regaddress_full .= trim($contract_order->Regregion.' '.$contract_order->Regregion_shorttype);
        $regaddress_full .= empty($contract_order->Regcity) ? '' : trim(', '.$contract_order->Regcity.' '.$contract_order->Regcity_shorttype);
        $regaddress_full .= empty($contract_order->Regdistrict) ? '' : trim(', '.$contract_order->Regdistrict.' '.$contract_order->Regdistrict_shorttype);
        $regaddress_full .= empty($contract_order->Reglocality) ? '' : trim(', '.$contract_order->Reglocality.' '.$contract_order->Reglocality_shorttype);
        $regaddress_full .= empty($contract_order->Reghousing) ? '' : ', д.'.$contract_order->Reghousing;
        $regaddress_full .= empty($contract_order->Regbuilding) ? '' : ', стр.'.$contract_order->Regbuilding;
        $regaddress_full .= empty($contract_order->Regroom) ? '' : ', к.'.$contract_order->Regroom;

        $params['regaddress_full'] = $regaddress_full;

        if (!empty($contract->insurance_id))
        {
            $params['insurance'] = $core->insurances->get_insurance($contract->insurance_id);
        }
        

        $core->documents->create_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => $document_type,
            'params' => $params,                
        ));

    }



exit;







$insurances = $core->insurances->get_insurances();
foreach ($insurances as $in)
{
    $new_number = $core->insurances->create_number($in->id);
echo $new_number.'<br />';

    $core->insurances->update_insurance($in->id, array('number'=>$new_number));
}
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($insurances);echo '</pre><hr />';

exit;


$core->db->query("
    SELECT * 
    FROM __contracts
    WHERE status = 4
    AND DATE(return_date) > '2022-01-05'
");
$results = $core->db->results();

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

exit;


echo file_get_contents('https://manager.nalichnoeplus.ru/generic');

exit;

$uid = exec($core->config->root_dir.'generic/uidgen');

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($uid);echo '</pre><hr />';

exit;



//echo __DIR__;

        $codes = array(
            1 => "адыгея",
            2 => "башкортостан",
            3 => "бурятия",
            4 => "алтай",
            5 => "дагестан",
            6 => "ингушетия",
            7 => "кабардино-балкарская",
            8 => "калмыкия",
            9 => "карачаево-черкесская",
            10 => "карелия",
            11 => "коми",
            12 => "марий эл",
            13 => "мордовия",
            14 => "саха /якутия/",
            15 => "северная осетия - алания",
            16 => "татарстан",
            17 => "тыва",
            18 => "удмуртская",
            19 => "хакасия",
            20 => "чеченская",
            21 => "чувашская",
            22 => "алтайский",
            23 => "краснодарский",
            24 => "красноярский",
            25 => "приморский",
            26 => "ставропольский",
            27 => "хабаровский", 
            28 => "амурская",
            29 => "архангельская",
            30 => "астраханская",
            31 => "белгородская",
            32 => "брянская",
            33 => "владимирская",
            34 => "волгоградская",
            35 => "вологодская",
            36 => "воронежская",
            37 => "ивановская",
            38 => "иркутская",
            39 => "калининградская",
            40 => "калужская",
            41 => "камчатский",
            42 => "кемеровская",
            43 => "кировская",
            44 => "костромская",
            45 => "курганская",
            46 => "курская",
            47 => "ленинградская",
            48 => "липецкая",
            49 => "магаданская",
            50 => "московская",
            51 => "мурманская",
            52 => "нижегородская",
            53 => "новгородская",
            54 => "новосибирская",
            55 => "омская",
            56 => "оренбургская",
            57 => "орловская",
            58 => "пензенская",
            59 => "пермский",
            60 => "псковская",
            61 => "ростовская",
            62 => "рязанская",
            63 => "самарская",
            64 => "саратовская",
            65 => "сахалинская",
            66 => "свердловская",
            67 => "смоленская",
            68 => "тамбовская",
            69 => "тверская",
            70 => "томская",
            71 => "тульская",
            72 => "тюменская",
            73 => "ульяновская",
            74 => "челябинская",
            75 => "забайкальский",
            76 => "ярославская",
            77 => "москва",
            78 => "санкт-петербург",
            82 => "крым",
            83 => "ненецкий автономный округ",
            86 => "ханты-мансийский автономный округ - югра",
            87 => "чукотский",
            89 => "ямало-ненецкий",
            92 => "севастополь",
            91 => "республика крым",

        );



$core->db->query("SELECT id, Regregion, Faktregion FROM __users");
$users = $core->db->results();
foreach ($users as $user)
{
    $update = array();
    
    if (isset($codes[$user->Regregion]))
        $update['Regregion'] = mb_strtoupper($codes[$user->Regregion], 'utf-8');
    if (isset($codes[$user->Faktregion]))
        $update['Faktregion'] = mb_strtoupper($codes[$user->Faktregion], 'utf-8');

    if (!empty($update))
        $core->users->update_user($user->id, $update);
}


exit;



if ($page = $core->request->get('page', 'integer'))
{
    $limit = 20;
    
    $query = $core->db->placehold("
        SELECT * FROM __contracts 
        WHERE status IN (2,3,4)
        AND type = 'base'
        AND DATE(inssuance_date) < '2021-07-01'
        ORDER BY id ASC
        LIMIT ?, ?
    ", ($page-1)*$limit, $limit);
    $core->db->query($query);

    $contracts = $core->db->results();
    
    foreach ($contracts as $contract)
    {
        $core->db->query("
            SELECT * FROM __documents
            WHERE user_id = ? 
            AND type = 'ANKETA_PEP'
        ", $contract->user_id);
        $result = $core->db->result();
        $result->params = unserialize($result->params);
        
        $result->params['asp'] = $contract->accept_code;
        $result->params['contract_date'] = $contract->inssuance_date;
        $result->params['created'] = $contract->inssuance_date;

        
        
        $core->documents->add_document(array(
            'user_id' => $contract->user_id,
            'order_id' => $contract->order_id,
            'contract_id' => $contract->id,
            'type' => 'ANKETA_PEP',
            'name' => 'Анкета - заявление ПЭП',
            'template' => 'anketa-zayavlenie-pep.tpl',
            'client_visible' => 0,
            'params' => $result->params,                
            'created' => $contract->inssuance_date
        ));

    }
    
    if (!empty($contracts))
    {
        $new_page = $page + 1;
        echo '<meta http-equiv="refresh" content="1; url='.$core->config->root_url.'/test.php?page='.$new_page.'">';
    }
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contracts);echo '</pre><hr />';        
    
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contracts);echo '</pre><hr />';
}



















exit;
$core->contracts->check_collection_contracts();




exit;

$core->db->query("SELECT * FROM __collector_movings WHERE id > 3075");
$results = $core->db->results();

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

foreach ($results as $r)
{
    $contract = $core->contracts->get_contract($r->contract_id);
    $core->users->update_user($contract->user_id, array('contact_status' => 0));
    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract);echo '</pre><hr />';
}

exit;



$core->db->query("SELECT * FROM __comments WHERE manager_id = 88");
$results = $core->db->results();

foreach ($results as $r)
{
    $core->db->query("
        SELECT * FROM __contracts WHERE order_id = ?
    ", $r->order_id);
    $contract = $core->db->result();
    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract);echo '</pre><hr />';

    $core->contracts->update_contract($contract->id, array(
        'collection_manager_id' => 88,
        'collection_status' => 10
    ));
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';


exit;


$url = 'https://nalichnoeplus.ru/test.php';

$ch = curl_init($url);

$resp = curl_exec($ch);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
exit;

$z = '[{"\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f":"NalPlus","\\u041d\\u043e\\u043c\\u0435\\u0440":"0928-116244","id_\\u0437\\u0430\\u044f\\u0432\\u043a\\u0430":"145086","\\u0421\\u0443\\u043c\\u043c\\u0430":"8000.00","\\u0421\\u0440\\u043e\\u043a":14,"\\u0414\\u0430\\u0442\\u0430":"20210928124856","\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442\\u0421\\u0442\\u0430\\u0432\\u043a\\u0430":"1.00","\\u041c\\u0435\\u0440\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"1.00","\\u0423\\u0418\\u0414_\\u0417\\u0430\\u0439\\u043c":"8f7bbe10-2040-11ec-876b-f56347d4d60d-3","\\u0423\\u0418\\u0414_\\u0417\\u0430\\u044f\\u0432\\u043a\\u0430":"1e0e2f30-203e-11ec-8167-13e8e28fa010-e","\\u041a\\u043e\\u0434\\u0421\\u041c\\u0421":"1421","\\u041c\\u0435\\u043d\\u0435\\u0434\\u0436\\u0435\\u0440":"\\u041b\\u043e\\u0433\\u0438\\u043d\\u043e\\u0432\\u0430 \\u0413\\u0430\\u043b\\u0438\\u043d\\u0430 \\u0412\\u0438\\u043a\\u0442\\u043e\\u0440\\u043e\\u0432\\u043d\\u0430","Payment":{"CardId":"27786","\\u0414\\u0430\\u0442\\u0430":"20210928125001","PaymentId":"452152442","OrderId":"327140625"},"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430":0,"\\u041d\\u043e\\u043c\\u0435\\u0440":0,"OrderID":0,"OperationID":0,"\\u041a\\u0440\\u0435\\u0434\\u0438\\u0442\\u043d\\u0430\\u044f\\u0417\\u0430\\u0449\\u0438\\u0442\\u0430":0},"\\u041a\\u043b\\u0438\\u0435\\u043d\\u0442":{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"\\u0410\\u0432\\u0434\\u0435\\u0435\\u043d\\u043a\\u043e\\u0432\\u0430","\\u0418\\u043c\\u044f":"\\u0410\\u043b\\u043b\\u0430","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"\\u041c\\u0438\\u0445\\u0430\\u0439\\u043b\\u043e\\u0432\\u043d\\u0430","\\u0414\\u0430\\u0442\\u0430\\u0420\\u043e\\u0436\\u0434\\u0435\\u043d\\u0438\\u044f\\u041f\\u043e\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0443":"19881209000000","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0418\\u043d\\u0434\\u0435\\u043a\\u0441":"344068","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0420\\u0435\\u0433\\u0438\\u043e\\u043d":"\\u0420\\u043e\\u0441\\u0442\\u043e\\u0432\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0420\\u0430\\u0439\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0413\\u043e\\u0440\\u043e\\u0434":"\\u0420\\u043e\\u0441\\u0442\\u043e\\u0432-\\u043d\\u0430-\\u0414\\u043e\\u043d\\u0443 \\u0433","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u041d\\u0430\\u0441\\u041f\\u0443\\u043d\\u043a\\u0442":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0423\\u043b\\u0438\\u0446\\u0430":"\\u041a\\u0430\\u043d\\u043e\\u043d\\u0435\\u0440\\u0441\\u043a\\u0438\\u0439 \\u043f\\u0435\\u0440","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0414\\u043e\\u043c":"62","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u041a\\u0432\\u0430\\u0440\\u0442\\u0438\\u0440\\u0430":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0418\\u043d\\u0434\\u0435\\u043a\\u0441":"344068","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0420\\u0435\\u0433\\u0438\\u043e\\u043d":"\\u0420\\u043e\\u0441\\u0442\\u043e\\u0432\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0420\\u0430\\u0439\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0413\\u043e\\u0440\\u043e\\u0434":"\\u0420\\u043e\\u0441\\u0442\\u043e\\u0432-\\u043d\\u0430-\\u0414\\u043e\\u043d\\u0443 \\u0433","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041d\\u0430\\u0441\\u041f\\u0443\\u043d\\u043a\\u0442":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0423\\u043b\\u0438\\u0446\\u0430":"\\u041a\\u0430\\u043d\\u043e\\u043d\\u0435\\u0440\\u0441\\u043a\\u0438\\u0439 \\u043f\\u0435\\u0440","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0414\\u043e\\u043c":"62","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041a\\u0432\\u0430\\u0440\\u0442\\u0438\\u0440\\u0430":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"8(988)537-09-23","\\u0418\\u041d\\u041d":"616131887855","\\u041a\\u043e\\u043b\\u0438\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e\\u0418\\u0436\\u0434\\u0435\\u0432\\u0435\\u043d\\u0446\\u0435\\u0432":"","\\u041c\\u0435\\u0441\\u0442\\u043e\\u0420\\u043e\\u0436\\u0434\\u0435\\u043d\\u0438\\u044f\\u041f\\u043e\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0443":"\\u0413\\u041e\\u0420. \\u0420\\u041e\\u0421\\u0422\\u041e\\u0412-\\u041d\\u0410-\\u0414\\u041e\\u041d\\u0423","\\u041e\\u0431\\u0440\\u0430\\u0437\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0410\\u0434\\u0440\\u0435\\u0441":"344029, \\u0420\\u043e\\u0441\\u0442\\u043e\\u0432\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b, \\u0420\\u043e\\u0441\\u0442\\u043e\\u0432-\\u043d\\u0430-\\u0414\\u043e\\u043d\\u0443 \\u0433, \\u041c\\u0435\\u043d\\u0436\\u0438\\u043d\\u0441\\u043a\\u043e\\u0433\\u043e \\u0443\\u043b, \\u0434\\u043e\\u043c 2 \\u0441","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0413\\u0440\\u0430\\u0444\\u0438\\u043a\\u0417\\u0430\\u043d\\u044f\\u0442\\u043e\\u0441\\u0442\\u0438":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0414\\u043e\\u043b\\u0436\\u043d\\u043e\\u0441\\u0442\\u044c":"\\u041e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u043e\\u043d\\u0438\\u0441\\u0442","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0415\\u0436\\u0435\\u043c\\u0435\\u0441\\u044f\\u0447\\u043d\\u044b\\u0439\\u0414\\u043e\\u0445\\u043e\\u0434":"60000","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u041d\\u0430\\u0437\\u0432\\u0430\\u043d\\u0438\\u0435":"\\u041e\\u041e\\u041e \\"\\u0420\\u0441\\u043c\\u044d\\"; \\u041e\\u041e\\u041e \\"\\u0420\\u043e\\u0441\\u0442\\u0441\\u0435\\u043b\\u044c\\u043c\\u0430\\u0448\\u044d\\u043d\\u0435\\u0440\\u0433\\u043e\\"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0421\\u0442\\u0430\\u0436\\u0420\\u0430\\u0431\\u043e\\u0442\\u044b\\u041b\\u0435\\u0442":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0421\\u0444\\u0435\\u0440\\u0430\\u0414\\u0435\\u044f\\u0442\\u0435\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0438":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"8(863)250-60-34","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0424\\u0418\\u041e\\u0420\\u0443\\u043a\\u043e\\u0432\\u043e\\u0434\\u0438\\u0442\\u0435\\u043b\\u044f":"\\u0413\\u0440\\u0438\\u0433\\u043e\\u0440\\u044c\\u0435\\u0432 \\u0412\\u0430\\u0441\\u0438\\u043b\\u0438\\u0439 \\u041d\\u0438\\u043a\\u043e\\u043b\\u0430\\u0435\\u0432\\u0438\\u0447","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u0420\\u0443\\u043a\\u043e\\u0432\\u043e\\u0434\\u0438\\u0442\\u0435\\u043b\\u044f":"8(909)431-37-36","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0414\\u0430\\u0442\\u0430\\u0412\\u044b\\u0434\\u0430\\u0447\\u0438":"20191121000000","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041a\\u0435\\u043c\\u0412\\u044b\\u0434\\u0430\\u043d":"\\u0413\\u0423 \\u041c\\u0412\\u0414 \\u0420\\u041e\\u0421\\u0421\\u0418\\u0418 \\u041f\\u041e \\u0420\\u041e\\u0421\\u0422\\u041e\\u0412\\u0421\\u041a\\u041e\\u0419 \\u041e\\u0411\\u041b\\u0410\\u0421\\u0422\\u0418","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041a\\u043e\\u0434\\u041f\\u043e\\u0434\\u0440\\u0430\\u0437\\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u044f":"610-010","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041d\\u043e\\u043c\\u0435\\u0440":"711977","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0421\\u0435\\u0440\\u0438\\u044f":"6019","\\u041f\\u043e\\u043b":"\\u0416\\u0435\\u043d\\u0441\\u043a\\u0438\\u0439","\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u043d\\u044b\\u0435\\u041b\\u0438\\u0446\\u0430":[{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"\\u0410\\u0432\\u0438\\u043b\\u043e\\u0432\\u0430","\\u0418\\u043c\\u044f":"\\u0421\\u0432\\u0435\\u0442\\u043b\\u0430\\u043d\\u0430","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"\\u0433\\u0440\\u0438\\u0433\\u043e\\u0440\\u044c\\u0435\\u0432\\u043d\\u0430","\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439":"8(918)507-13-20","\\u0421\\u0442\\u0435\\u043f\\u0435\\u043d\\u044c\\u0420\\u043e\\u0434\\u0441\\u0442\\u0432\\u0430":"\\u043c\\u0430\\u0442\\u044c\\/\\u043e\\u0442\\u0435\\u0446"},{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"\\u0410\\u0432\\u0434\\u0435\\u0435\\u043d\\u043a\\u043e\\u0432","\\u0418\\u043c\\u044f":"\\u041d\\u0438\\u043a\\u0438\\u0442\\u0430","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"\\u0430\\u043d\\u0430\\u0442\\u043e\\u043b\\u044c\\u0435\\u0432\\u0438\\u0447","\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439":"8(952)418-85-08","\\u0421\\u0442\\u0435\\u043f\\u0435\\u043d\\u044c\\u0420\\u043e\\u0434\\u0441\\u0442\\u0432\\u0430":"\\u043c\\u0443\\u0436\\/\\u0436\\u0435\\u043d\\u0430"}]}}]';
$r = json_decode($z);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($r);echo '</pre><hr />';
exit;

$core->contracts->check_collection_contracts_new();
//$core->contracts->check_collection_contracts();


exit;



$start = time();

$number = '0718-106552';
$resp = $core->soap1c->get_cession_info($number);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';

$end = time();

echo 'loaded: '.($end - $start).' сек';

exit;

$query = $core->db->placehold("
    SELECT * FROM __contracts
    WHERE DATE(sold_date) = '2021-09-06'
");
$core->db->query($query);

$results = $core->db->results();

$numbers = array();
foreach ($results as $r)
    $numbers[] = $r->number;

$res = $core->soap1c->send_cessions($numbers);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($res, $numbers);echo '</pre><hr />';



exit;


$core->contracts->distribute_contracts();

exit;



$register_id = '270713653';
$operation_id= '415324949';
$info = $core->best2pay->get_operation_info(7184, $register_id, $operation_id);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($info);echo '</pre><hr />';

exit;



$doc = $core->documents->get_document(461);
$doc->params['insurance'] = $core->insurances->get_insurance(100008);
$core->documents->update_document($doc->id, array('params' => $doc->params));

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($doc->params);echo '</pre><hr />';


exit;

if ($page = $core->request->get('page', 'integer'))
{
    
    $params = array(
        'type' => 'POLIS_STRAHOVANIYA',
        'page' => $page,
        'limit' => 10,
    );
    if ($docs = $core->documents->get_documents($params))
    {
        foreach ($docs as $doc)
        {
            $doc->params = (array)$doc->params;
            $doc->params['insurance'] = $core->insurances->get_insurance($doc->params['insurance']->id);
            
            $core->documents->update_document($doc->id, array('params' => $doc->params));
        }
        
        $new_page = $page +1;
        echo '<meta http-equiv="refresh" content="1; url='.$core->request->url(array('page'=>$new_page)).'">';

        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($docs);echo '</pre><hr />';
    }

}

exit;


if ($page = $core->request->get('page', 'integer'))
{
    $limit = 100;
    if ($results = $core->insurances->get_insurances(array('page' => $page, 'limit' => $limit)))
    {
        foreach ($results as $r)
        {
            $start_date = date('Y-m-d 00:00:00', strtotime($r->create_date) + (1 * 86400));
            $end_date = date('Y-m-d 23:59:59', strtotime($r->create_date) + (31 * 86400));

            $core->insurances->update_insurance($r->id, array(
                'start_date' => $start_date,
                'end_date' => $end_date,
            ));
            
            $new_page = $page +1;
            echo '<meta http-equiv="refresh" content="2; url='.$core->request->url(array('page'=>$new_page)).'">';
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($r, $start_date, $end_date);echo '</pre><hr />';
        }

        
    }
}












exit;



$date_minus34 = date('Y-m-d', time() - 34 * 86400);

$query = $core->db->placehold("
    SELECT * FROM __contracts
    WHERE DATE(return_date) = ?
    AND collection_handchange = 0
", $date_minus34);

$r = $core->db->results();

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($query, $r);echo '</pre><hr />';










exit;


$managers = $core->managers->get_managers();
foreach ($managers as $m)
{
    $resp = $core->soap1c->check_manager_name($m->name_1c);
    if (empty($resp))
    {
    echo ($m->name_1c).'<br>';
    }
}

exit;


$q = '[{"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0417\\u0430\\u0439\\u043c\\u0430":"0804-108805","\\u0414\\u0430\\u0442\\u0430":"20210819101305","\\u041a\\u043e\\u043c\\u043c\\u0435\\u043d\\u0442\\u0430\\u0440\\u0438\\u0439":"\\u0432\\u0445 \\u0437\\u0432 , \\u043d\\u0435 \\u043c\\u043e\\u0436\\u0435\\u0442 \\u043d\\u0430\\u0439\\u0442\\u0438 \\u0441\\u0430\\u0439\\u0442 \\u043a\\u043e\\u043c\\u043f\\u0430\\u043d\\u0438\\u0438, \\u043d\\u0430\\u043f\\u0440\\u0430\\u0432\\u043b\\u0435\\u043d\\u043e \\u0441\\u043c\\u0441 ","\\u0421\\u043e\\u0442\\u0440\\u0443\\u0434\\u043d\\u0438\\u043a":"\\u0413\\u0430\\u0440\\u0438\\u0444\\u0443\\u043b\\u043b\\u0438\\u043d\\u0430 \\u0421\\u044e\\u0437\\u0430\\u043d\\u043d\\u0430 \\u041c\\u0435\\u0440\\u0443\\u0436\\u0430\\u043d\\u043e\\u0432\\u043d\\u0430"}]';
$g = json_decode($q);     
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($g);echo '</pre><hr />';	

exit;



if ($page = $core->request->get('page', 'integer'))
{
    $limit = 10;
    
    $core->db->query("
        SELECT * FROM __contracts
        WHERE collection_manager_id != 0
        AND collection_manager_id IS NOT NULL
        AND status != 3
        LIMIT ?, ?
    ", $page * $limit, $limit);
    if ($results = $core->db->results())
    {
        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';
        
        foreach ($results as $contract)
        {
            $res = $core->soap1c->send_collector($contract->number, $contract->collection_manager_id);
            echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($res);echo '</pre><hr />';
        }
        
        $new_page = $page + 1;
        
        echo '<meta http-equiv="refresh" content="2; url='.$core->request->url(array('page' => $new_page)).'">';
    }
}






exit;


$cs = $core->contactpersons->get_contactpersons(array('user_id' => 106676));

$filtered = array();
foreach ($cs as $c)
{
    
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($cs);echo '</pre><hr />';

exit;

$order_1c_id = '126354';
$scoring = $core->scorings->get_scoring(210697);
$body = unserialize($scoring->body);
$resp = $core->soap1c->send_fssp($order_1c_id, $body);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';

exit;


$r = 'O:8:"stdClass":4:{s:6:"status";i:0;s:10:"task_start";s:19:"2021-07-29 16:04:22";s:8:"task_end";s:19:"2021-07-29 16:04:23";s:6:"result";a:1:{i:0;O:8:"stdClass":3:{s:6:"status";i:0;s:5:"query";O:8:"stdClass":2:{s:4:"type";i:1;s:6:"params";O:8:"stdClass":5:{s:6:"region";s:2:"72";s:9:"firstname";s:16:"Светлана";s:10:"secondname";s:14:"Львовна";s:8:"lastname";s:16:"Мальцева";s:9:"birthdate";s:10:"15.03.1989";}}s:6:"result";a:5:{i:0;O:8:"stdClass":7:{s:4:"name";s:160:"МАЛЬЦЕВА СВЕТЛАНА ЛЬВОВНА 15.03.1989 627720, РОССИЯ, ТЮМЕНСКАЯ ОБЛ., ИШИМСКИЙ Р-Н, П. ОКТЯБРЬСКИЙ";s:14:"exe_production";s:35:"20726/19/72009-ИП от 26.03.2019";s:7:"details";s:263:"Судебный приказ от 18.05.2010 № ВС№010197347 Постановление о взыскании исполнительского сбора СУДЕБНЫЙ УЧАСТОК № 2 ИШИМСКОГО СУДЕБНОГО РАЙОНА Г. ИШИМА";s:7:"subject";s:94:"Задолженность: 9085 руб. Исполнительский сбор: 1000 руб.";s:10:"department";s:148:"Ишимское МОСП 627759, Тюменская область, Ишимский район, г. Ишим, ул. Проезд Майский, 2";s:7:"bailiff";s:36:"МЁДОВА К. В.<br>+73452495331";s:6:"ip_end";s:0:"";}i:1;O:8:"stdClass":7:{s:4:"name";s:138:"МАЛЬЦЕВА СВЕТЛАНА ЛЬВОВНА 15.03.1989 ТЮМЕНСКАЯ ОБЛ., ИШИМСКИЙ Р-Н, П. ОКТЯБРЬСКИЙ";s:14:"exe_production";s:35:"26376/19/72009-ИП от 11.04.2019";s:7:"details";s:162:"Судебный приказ от 01.03.2019 № 2-303/19 СУДЕБНЫЙ УЧАСТОК № 2 ИШИМСКОГО СУДЕБНОГО РАЙОНА Г. ИШИМА";s:7:"subject";s:95:"Задолженность по кредитным платежам (кроме ипотеки)";s:10:"department";s:148:"Ишимское МОСП 627759, Тюменская область, Ишимский район, г. Ишим, ул. Проезд Майский, 2";s:7:"bailiff";s:42:"КУЗНЕЦОВА А. В.<br>+73452495331";s:6:"ip_end";s:20:"2019-05-07, 46, 1, 3";}i:2;O:8:"stdClass":7:{s:4:"name";s:138:"МАЛЬЦЕВА СВЕТЛАНА ЛЬВОВНА 15.03.1989 ТЮМЕНСКАЯ ОБЛ., ИШИМСКИЙ Р-Н, П. ОКТЯБРЬСКИЙ";s:14:"exe_production";s:35:"93190/19/72009-ИП от 25.09.2019";s:7:"details";s:212:"Исполнительный лист от 21.08.2019 № ФС № 033932781 Постановление о взыскании исполнительского сбора ИШИМСКИЙ ГОРОДСКОЙ СУД";s:7:"subject";s:170:"Задолженность по кредитным платежам (кроме ипотеки): 38870.86 руб. Исполнительский сбор: 2992.16 руб.";s:10:"department";s:148:"Ишимское МОСП 627759, Тюменская область, Ишимский район, г. Ишим, ул. Проезд Майский, 2";s:7:"bailiff";s:36:"МЁДОВА К. В.<br>+73452495331";s:6:"ip_end";s:0:"";}i:3;O:8:"stdClass":7:{s:4:"name";s:138:"МАЛЬЦЕВА СВЕТЛАНА ЛЬВОВНА 15.03.1989 ТЮМЕНСКАЯ ОБЛ., ИШИМСКИЙ Р-Н, П. ОКТЯБРЬСКИЙ";s:14:"exe_production";s:36:"203047/20/72009-ИП от 07.08.2020";s:7:"details";s:257:"Акт по делу об административном правонарушении от 01.04.2020 № 72К00047502 Постановление о взыскании исполнительского сбора МО МВД РОССИИ "ИШИМСКИЙ"";s:7:"subject";s:96:"Иной штраф ОВД: 471.78 руб. Исполнительский сбор: 1000 руб.";s:10:"department";s:148:"Ишимское МОСП 627759, Тюменская область, Ишимский район, г. Ишим, ул. Проезд Майский, 2";s:7:"bailiff";s:36:"КАТАЕВ К. И.<br>+73452495331";s:6:"ip_end";s:0:"";}i:4;O:8:"stdClass":7:{s:4:"name";s:150:"МАЛЬЦЕВА СВЕТЛАНА ЛЬВОВНА 15.03.1989 П. ОКТЯБРЬСКИЙ, ИШИМСКИЙ РАЙОН, ТЮМЕНСКАЯ ОБЛАСТЬ";s:14:"exe_production";s:36:"107313/21/72009-ИП от 05.07.2021";s:7:"details";s:169:"Судебный приказ от 13.11.2020 № 2-7070/2020/2М СУДЕБНЫЙ УЧАСТОК № 2 ИШИМСКОГО СУДЕБНОГО РАЙОНА Г. ИШИМА";s:7:"subject";s:120:"Задолженность по платежам за газ, тепло и электроэнергию: 169.81 руб.";s:10:"department";s:148:"Ишимское МОСП 627759, Тюменская область, Ишимский район, г. Ишим, ул. Проезд Майский, 2";s:7:"bailiff";s:42:"НЕСТЕРОВА Н. А.<br>+73452495331";s:6:"ip_end";s:0:"";}}}}}';
$u = unserialize($r);
$s = json_encode($u);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($u);echo '</pre><hr />';
exit;


$order_1c_id = '112535';
$passport = '3211-080766';
$resp = $core->soap1c->send_fms($order_1c_id, $passport, 1);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';

exit;

$s = 'O:8:"stdClass":4:{s:6:"status";i:0;s:10:"task_start";s:19:"2021-07-27 09:05:13";s:8:"task_end";s:19:"2021-07-27 09:05:17";s:6:"result";a:1:{i:0;O:8:"stdClass":3:{s:6:"status";i:0;s:5:"query";O:8:"stdClass":2:{s:4:"type";i:1;s:6:"params";O:8:"stdClass":5:{s:6:"region";s:2:"61";s:9:"firstname";s:14:"Евгений";s:10:"secondname";s:20:"Витальевич";s:8:"lastname";s:18:"Бурцайлов";s:9:"birthdate";s:10:"21.08.1994";}}s:6:"result";a:1:{i:0;O:8:"stdClass":7:{s:4:"name";s:103:"БУРЦАЙЛОВ ЕВГЕНИЙ ВИТАЛЬЕВИЧ 21.08.1994 СТАВРОПОЛЬСКИЙ КРАЙ";s:14:"exe_production";s:35:"67987/21/61032-ИП от 30.04.2021";s:7:"details";s:279:"Судебный приказ от 08.04.2021 № 3-2-122/2021 Постановление о взыскании исполнительского сбора СУДЕБНЫЙ УЧАСТОК № 3 СОВЕТСКОГО СУДЕБНОГО РАЙОНА Г. РОСТОВА-НА-ДОНУ";s:7:"subject";s:167:"Задолженность по кредитным платежам (кроме ипотеки): 25475 руб. Исполнительский сбор: 1783.25 руб.";s:10:"department";s:219:"Советское РОСП г. Ростова-на-Дону УФССП России по Ростовской области 344091, Россия, , , г. Ростов-на-Дону, , ул. Каширская, 8/3, , ";s:7:"bailiff";s:42:"ПОДРЕЗОВА Е. В.<br>+78632100237";s:6:"ip_end";s:0:"";}}}}}';
$u = unserialize($s);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($u);echo '</pre><hr />';




exit;
$operation_id = '181882';
$res = $core->cloudkassir->send_insurance($operation_id);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($res);echo '</pre><hr />';

exit;


$order_id = '123873';
$res = $core->cloudkassir->send_reject_reason($order_id);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($res);echo '</pre><hr />';

exit;


$resp = $core->communications->check_user(101609);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';


exit;
$core->contracts->distribute_contracts();


exit;
// все одобренные но не полученные заявки в отказ клиента
$query = $core->db->placehold("
    SELECT * 
    FROM __orders
    WHERE date < '2021-07-12 23:59:59'
    AND (status = 2)
    ORDER BY date DESC
");
$core->db->query($query);
$results = $core->db->results();
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

foreach ($results as $order)
{
    $core->orders->update_order($order->id, array(
        'status' => 8,
        'reason_id' => 2,
        'reject_reason' => 'Вы отказались от займа',
        'reject_date' => date('Y-m-d'),
    ));
    if (!empty($order->contract_id))
    {
        $core->contracts->update_contract($order->contract_id, array('status' => 8));
    }
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

exit;




$resp = $core->zvonobot->create_record('Первый день просрочки', 'Здравствуйте. Юридическая компания номер один. Уведомляем о наличии просроченной задолженности по вашему договору займа. Не нарушайте принятые вами условия договора. По всем имеющимся вопросам звоните восемь восемьсот двести двадцать два шестьдесят девяносто один', 1);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';

exit;



$from = '2021-07-08';
$to = '2021-07-08';
$resp = $core->soap1c->get_payments1c($from, $to);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';




// убрал антиразгон с заявок клиентов из списка
exit;
$filename = $core->config->root_dir.'contracts.csv';
$list = file($filename);

$items = array();
foreach ($list as $row)
{
    $row = trim($row);
    $row = array_map('trim', explode("\t", $row));
    $data = array_map('trim', explode(" ", $row[0]));

    $item['phone'] = $row[1];
    $item['lastname'] = $data[0];
    $item['firstname'] = $data[1];
    $item['patronymic'] = $data[2];
    $item['birth'] = $data[3];
    
    $items[] = $item;
}


foreach ($items as $client)
{
    $core->db->query("
        SELECT id FROM __users
        WHERE lastname = ?
        AND firstname = ?
        AND patronymic = ?
        AND phone_mobile = ?
    ", $client['lastname'], $client['firstname'], $client['patronymic'], $client['phone']);

    $user_id = $core->db->result('id');
    if (!empty($user_id))
    {
        if ($orders = $core->orders->get_orders(array('user_id' => $user_id)))
        {
            foreach ($orders as $order)
            {
                echo $order->order_id.'<br />';
                
                $core->orders->update_order($order->order_id, array(
                    'antirazgon' => 0,
                    'antirazgon_date' => NULL,
                    'antirazgon_amount' => NULL,
                ));
                
                if (in_array($order->reason_id, array(1, 4, 5, 6, 7, 8, 10, 11, 13, 14)))
                    $core->orders->update_order($order->order_id, array(
                        'reason_id' => 9,
                    ));
                
                    
            }
        }

//        echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($client);echo '</pre><hr />';
    
    }
}

//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($items);echo '</pre><hr />';


exit;





$contracts = array_map('trim', file($core->config->root_dir.'contracts.csv'));

$current = $core->request->get('page', 'integer');


if ($current < count($contracts))
{
    $number = $contracts[$current];
    $contract = $core->contracts->get_number_contract($number);

    send_operations($contract, $core);
//    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract);echo '</pre><hr />';
    
    $current++;
    
    echo '<meta http-equiv="refresh" content="3;URL=/test.php?page='.$current.'">';
}
else
{
    echo '<h1>DONE</h1>';
}


    function send_operations($contract, $core)
    {
//        $date = '2021-05-06';
        if ($operations = $core->operations->get_operations(array('contract_id' => $contract->id, 'type'=>array('PERCENTS', 'CHARGE', 'PENI'))))
        {            
            foreach ($operations as $o)
            {
                $o->contract = $contract;
            }
            $result = $core->soap1c->send_operations($operations);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($result, $operations);echo '</pre><hr />';
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operations);echo '</pre><hr />';            
        }
        
    }
    
















exit;


$r = '[{"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0417\\u0430\\u0439\\u043c\\u0430":"\\u041d\\u041f21-240100","\\u0414\\u0430\\u0442\\u0430\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"20210604220549","\\u041d\\u043e\\u043c\\u0435\\u043e\\u0440\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"0604-104105","ID_\\u0417\\u0430\\u043a\\u0430\\u0437":"271017928","ID_\\u0423\\u0441\\u043f\\u0435\\u0448\\u043d\\u0430\\u044f\\u041e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f":"354425334","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0414":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442":"280.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0435\\u043d\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041a\\u043e\\u043c\\u0438\\u0441\\u0441\\u0438\\u0438":"30.00","\\u041f\\u0440\\u043e\\u043b\\u043e\\u043d\\u0433\\u0430\\u0446\\u0438\\u044f":1,"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":0,"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":""}},{"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0417\\u0430\\u0439\\u043c\\u0430":"\\u041d\\u041f21-190695","\\u0414\\u0430\\u0442\\u0430\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"20210604223338","\\u041d\\u043e\\u043c\\u0435\\u043e\\u0440\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"0604-104110","ID_\\u0417\\u0430\\u043a\\u0430\\u0437":"271027165","ID_\\u0423\\u0441\\u043f\\u0435\\u0448\\u043d\\u0430\\u044f\\u041e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f":"354441800","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0414":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442":"1570.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0435\\u043d\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041a\\u043e\\u043c\\u0438\\u0441\\u0441\\u0438\\u0438":"30.00","\\u041f\\u0440\\u043e\\u043b\\u043e\\u043d\\u0433\\u0430\\u0446\\u0438\\u044f":1,"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":0,"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":""}},{"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0417\\u0430\\u0439\\u043c\\u0430":"\\u041d\\u041f21-220054","\\u0414\\u0430\\u0442\\u0430\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"20210604223658","\\u041d\\u043e\\u043c\\u0435\\u043e\\u0440\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"0604-104112","ID_\\u0417\\u0430\\u043a\\u0430\\u0437":"271028238","ID_\\u0423\\u0441\\u043f\\u0435\\u0448\\u043d\\u0430\\u044f\\u041e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f":"354443562","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0414":"3000.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442":"547.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0435\\u043d\\u0438":"6.56","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041a\\u043e\\u043c\\u0438\\u0441\\u0441\\u0438\\u0438":"63.97","\\u041f\\u0440\\u043e\\u043b\\u043e\\u043d\\u0433\\u0430\\u0446\\u0438\\u044f":0,"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":0,"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":""}},{"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0417\\u0430\\u0439\\u043c\\u0430":"\\u041d\\u041f21-200817","\\u0414\\u0430\\u0442\\u0430\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"20210604224154","\\u041d\\u043e\\u043c\\u0435\\u043e\\u0440\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"0604-104113","ID_\\u0417\\u0430\\u043a\\u0430\\u0437":"271029817","ID_\\u0423\\u0441\\u043f\\u0435\\u0448\\u043d\\u0430\\u044f\\u041e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f":"354446268","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0414":"8000.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442":"1711.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0435\\u043d\\u0438":"30.66","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041a\\u043e\\u043c\\u0438\\u0441\\u0441\\u0438\\u0438":"175.35","\\u041f\\u0440\\u043e\\u043b\\u043e\\u043d\\u0433\\u0430\\u0446\\u0438\\u044f":0,"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":0,"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":""}},{"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0417\\u0430\\u0439\\u043c\\u0430":"0602-100795","\\u0414\\u0430\\u0442\\u0430\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"20210605013429","\\u041d\\u043e\\u043c\\u0435\\u043e\\u0440\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"0605-104129","ID_\\u0417\\u0430\\u043a\\u0430\\u0437":"271066934","ID_\\u0423\\u0441\\u043f\\u0435\\u0448\\u043d\\u0430\\u044f\\u041e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f":"354515060","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0414":"3000.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442":"90.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0435\\u043d\\u0438":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041a\\u043e\\u043c\\u0438\\u0441\\u0441\\u0438\\u0438":"55.62","\\u041f\\u0440\\u043e\\u043b\\u043e\\u043d\\u0433\\u0430\\u0446\\u0438\\u044f":0,"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":0,"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":""}}]'
;
$v = json_decode($r);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($v);echo '</pre><hr />';

exit;


$host = '46.29.78.222';
$port = '3036';
$user = 'master';
$pswd = '123123qQ';
$name = 'vse4etkoy2_nalic';
$r = mysqli_connect($host, $user, $pswd, $name, $port);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($r);echo '</pre><hr />';

exit;


$core->contracts->distribute_contracts();


exit;

$scoring_id = 103370;
$core->antirazgon_scoring->run_scoring($scoring_id);

exit;

$query = $core->db->placehold("
    SELECT * 
    FROM __orders
    WHERE date < '2021-06-24 23:59:59'
    AND (status = 1 OR status = 0)
    ORDER BY date DESC
");
$core->db->query($query);
$results = $core->db->results();
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

foreach ($results as $order)
{
    $core->orders->update_order($order->id, array(
        'status' => 8,
        'reason_id' => 2,
        'reject_reason' => 'Вы отказались от займа',
        'reject_date' => date('Y-m-d'),
    ));
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

exit;



$operation = $core->operations->get_operation(11852);
$operation->transaction = $core->transactions->get_transaction($operation->transaction_id);
                
$resp = $core->soap1c->send_reject_reason($operation);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
exit;

$core->contracts->distribute_contracts();


exit;



$uid = 'abca97c2-9b70-11eb-818a-ac1f6b7d528f';
$resp = $core->soap1c->get_client_credits($uid);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
exit;

$core->contracts->check_collection_contracts();
$core->contracts->check_sold_contracts();
$core->contracts->distribute_contracts();
exit;


exit;


// отправка неотправленных фоток в 1с
$files = $core->users->get_files(array('user_id' => 100382));
$user_files = array();
foreach ($files as $file)
{
    if (!isset($user_files[$file->user_id]))
    {
        $user_files[$file->user_id] = new StdClass();
        $user_files[$file->user_id]->files = array();
    }
    
    $user_files[$file->user_id]->files[] = $file;
}
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($files);echo '</pre><hr />';
if (!empty($user_files))
{
    $orders = $core->orders->get_orders(array('user_id' => array_keys($user_files)));
    foreach ($orders as $order)
        $user_files[$order->user_id]->order = $order;


    foreach ($user_files as $item)
    {
        $need_send = array();
        $files_dir = str_replace('https://', 'http://', $core->config->front_url.'/files/users/');
        foreach ($item->files as $f)
        {
            if (1 || ($f->sent_1c == 0 && $f->status == 2))
            {
                $need_send_item = new StdClass();
                $need_send_item->id = $f->id;
                $need_send_item->user_id = $f->user_id;
                $need_send_item->type = $f->type;
                $need_send_item->url = $files_dir.$f->name;
                
                $need_send[] = $need_send_item;
            }
        }
    
        $send_resp = $core->soap1c->send_order_images($order->order_id, $need_send);
        if ($send_resp == 'OK')
            foreach ($need_send as $need_send_file)
                $core->users->update_file($need_send_file->id, array('sent_1c' => 1, 'sent_date' => date('Y-m-d H:i:s')));
    
    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($item, $send_resp, $need_send);echo '</pre><hr />';
    //exit;
    }
}




exit;

$str = '{"code":1,"inn":"711871723612","captchaRequired":false}';
$arr = json_decode($str);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($arr);echo '</pre><hr />';

exit;



$contracts = $core->contracts->get_contracts(array('type'=>'base'));
foreach ($contracts as $contract)
{
    $contract->user = $core->users->get_user((int)$contract->user_id);
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contracts);echo '</pre><hr />';

exit;

exit;


// нулевой день просрочки 812862
// 1 и 2 день 812870

$core->contracts->check_collection_contracts();

exit;


$resp = $core->zvonobot->create_record('Первый день просрочки', 'Здравствуйте. Микрокредитная Компания - Наличное Плюс. Уведомляем о наличии просроченной задолженности по вашему договору займа. Не нарушайте принятые вами условия договора. По всем имеющимся вопросам звоните восемьсот триста тридцать три двадцать четыре восемьдесят четыре');

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';

exit;
/*
$order_id = '100189';
$user_id = '100262';
$documents = $core->users->get_files(array('user_id'=>$user_id));


$send_documents = array();
foreach ($documents as $document)
{
    $item = new StdClass();
    
    $item->id = $document->id;
    $item->user_id = $document->user_id;
    $item->type = $document->type;
    $item->url = str_replace('https://', 'http://', $core->config->front_url.'/files/users/'.$document->name);
    
    $send_documents[] = $item;
}
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($send_documents);echo '</pre><hr />';
$resp = $core->soap1c->send_order_images($order_id, $send_documents);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';


exit;
*/
$order_id = '100465';
$documents = $core->documents->get_documents(array('order_id'=>$order_id));

$send_documents = array();
foreach ($documents as $document)
{
    $item = new StdClass();
    
    $item->id = $document->id;
    $item->name = $document->name;
    $item->order_id = $document->order_id;
    $item->url = str_replace('https://', 'http://', $core->config->front_url.'/document/'.$document->user_id.'/'.$document->id.'.pdf');
    $item->created = date('YmdHis', strtotime($document->created));
    
    $send_documents[] = $item;
}
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($send_documents);echo '</pre><hr />';
$resp = $core->soap1c->send_order_files($order_id, $send_documents);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';









exit;

$core->db->query("
    SELECT *
    FROM __transactions
    WHERE description = ?
    AND operation IS NULL
", 'Страховой полис');
$results  = $core->db->results();

foreach ($results as $result)
{
    $xml = simplexml_load_string($result->callback_response);
    
    $state = empty($xml->state) ? '' : (string)$xml->state;
    
        echo 'APPROVED<br />';
        $core->transactions->update_transaction($result->id, array(
            'operation' => (string)$xml->id,
            'reason_code' => (string)$xml->reason_code,
        ));

        if ($state == 'APPROVED')
        {
            $core->db->query("
                SELECT id FROM __operations 
                WHERE order_id = ?
            ");
        }

    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($result);echo '</pre><hr />';
}



exit;



$core->db->query("
    SELECT * FROM __operations
    WHERE type = 'INSURANCE'
    AND transaction_id IS NULL
");
$operations = $core->db->results();


foreach ($operations as $operation)
{
    $core->db->query("
        SELECT * FROM __transactions 
        WHERE user_id = ?
        AND description = 'Страховой полис'
        AND DATE(created) = ?
    ", $operation->user_id, date('Y-m-d', strtotime($operation->created)));
    $operation->transaction = $core->db->result();
    $core->operations->update_operation($operation->id, array('transaction_id'=>$operation->transaction->id));
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($operations);echo '</pre><hr />';





exit;

$core->db->query("
    SELECT *
    FROM __operations
    WHERE type = 'REJECT_REASON'
");
$results = $core->db->results();

foreach ($results as $operation)
{
    $operation->transaction = $core->transactions->get_transaction($operation->transaction_id);
    
    $resp = $core->soap1c->send_reject_reason($operation);
    echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
    $core->operations->update_operation($operation->id, array(
        'sent_status' => 2,
        'sent_date' => date('Y-m-d H:i:s')
    ));
}

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';




exit;
foreach ($results as $result)
{
    $core->db->query("
        SELECT *
        FROM __transactions
        WHERE user_id = ?
        AND description = ?
    ", $result->user_id, 'Услуга "Узнай причину отказа"');
    $transactions = $core->db->results();
    if (count($transactions) == 1)
    {
        $core->operations->update_operation($result->id, array('transaction_id' => $transactions[0]->id));
        
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($transactions);
var_dump($result);echo '</pre><hr />';        
    }
}


//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';

exit;



//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';








exit;
// отправляем причины отказа старые
$operation = $core->operations->get_operation(202);
$operation->transaction = $core->transactions->get_transaction($operation->transaction_id);

$resp = $core->soap1c->send_reject_reason($operation);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
$core->operations->update_operation($operation->id, array(
    'sent_status' => 2,
    'sent_date' => date('Y-m-d H:i:s')
));

exit;


// order 100335
// contract 100124
$contracts = array();
$contract = $core->contracts->get_contract(100124);

    $contract->user = $core->users->get_user((int)$contract->user_id);
    $contract->order = $core->orders->get_order((int)$contract->order_id);
    $contract->p2pcredit = $core->best2pay->get_contract_p2pcredit($contract->id);
    $contract->insurance = $core->insurances->get_insurance($contract->insurance_id);
$contracts[] = $contract;

$result = $core->soap1c->send_contracts($contracts);

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($result);echo '</pre><hr />';
if (isset($result->return) && $result->return == 'OK')
{
        $core->contracts->update_contract($contract->id, array(
            'sent_date' => date('Y-m-d H:i:s'),
            'sent_status' => 2
        ));
}




//$resp = $core->best2pay->get_operation_info(7180, '246472682', 313344231);



//37b8651e-1f9a-4abf-b7fe-5c165261fc3f
//$resp = $core->sms->send('+79276928586', 'Тестируем нал плюс');


/* Списание страховки*/
//$register_id = '238536559';
//$info = $core->best2pay->get_register_info(7184, $register_id);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($info);echo '</pre><hr />';
//exit;

//$resp = $core->best2pay->recurrent(121, 3900, 'Допуслуга Узнай причину отказа');
//$resp = $core->best2pay->recurrent(119, 15000, 'Страховой полис');

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';



exit;

$contract = $core->contracts->get_contract(100050);

$ob_date = new DateTime();
$ob_date->add(DateInterval::createFromDateString($contract->period.' days'));
$return_date = $ob_date->format('Y-m-d H:i:s');

$return_amount = round($contract->amount + $contract->amount * $contract->base_percent * $contract->period / 100, 2);
$return_amount_rouble = (int)$return_amount;
$return_amount_kop = ($return_amount - $return_amount_rouble) * 100;

$contract_order = $core->orders->get_order((int)$contract->order_id);
$params = array(
    'lastname' => $contract_order->lastname,
    'firstname' => $contract_order->firstname,
    'patronymic' => $contract_order->patronymic,
    'phone' => $contract_order->phone_mobile,
    'birth' => $contract_order->birth,
    'number' => $contract->number,
    'contract_date' => date('Y-m-d H:i:s'),
    'return_date' => $return_date,
    'return_date_day' => date('d', strtotime($return_date)),
    'return_date_month' => date('m', strtotime($return_date)),
    'return_date_year' => date('Y', strtotime($return_date)),
    'return_amount' => $return_amount,
    'return_amount_rouble' => $return_amount_rouble,
    'return_amount_kop' => $return_amount_kop,
    'base_percent' => $contract->base_percent,
    'amount' => $contract->amount,
    'period' => $contract->period,
    'passport_serial' => $contract_order->passport_serial,
    'passport_date' => $contract_order->passport_date,
    'subdivision_code' => $contract_order->subdivision_code,
    'passport_issued' => $contract_order->passport_issued,
    'passport_series' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 0, 4),
    'passport_number' => substr(str_replace(array(' ', '-'), '', $contract_order->passport_serial), 4, 6),
    'asp' => $contract->accept_code,
);
$regaddress_full = empty($contract_order->Regindex) ? '' : $contract_order->Regindex.', ';
$regaddress_full .= trim($contract_order->Regregion.' '.$contract_order->Regregion_shorttype);
$regaddress_full .= empty($contract_order->Regcity) ? '' : trim(', '.$contract_order->Regcity.' '.$contract_order->Regcity_shorttype);
$regaddress_full .= empty($contract_order->Regdistrict) ? '' : trim(', '.$contract_order->Regdistrict.' '.$contract_order->Regdistrict_shorttype);
$regaddress_full .= empty($contract_order->Reglocality) ? '' : trim(', '.$contract_order->Reglocality.' '.$contract_order->Reglocality_shorttype);
$regaddress_full .= empty($contract_order->Reghousing) ? '' : ', д.'.$contract_order->Reghousing;
$regaddress_full .= empty($contract_order->Regbuilding) ? '' : ', стр.'.$contract_order->Regbuilding;
$regaddress_full .= empty($contract_order->Regroom) ? '' : ', к.'.$contract_order->Regroom;

$params['regaddress_full'] = $regaddress_full;
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($params);echo '</pre><hr />';

$core->documents->update_document(205, array(
    'params' => $params,                
));

exit;

$js = '[{"\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f":"NalPlus","\\u041d\\u043e\\u043c\\u0435\\u0440":"0322-100004","\\u0421\\u0443\\u043c\\u043c\\u0430":"7000.00","\\u0421\\u0440\\u043e\\u043a":"14","\\u0414\\u0430\\u0442\\u0430":"20210322160246","\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442\\u0421\\u0442\\u0430\\u0432\\u043a\\u0430":"1.00","\\u041c\\u0435\\u0440\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"1.50","\\u0423\\u0418\\u0414_\\u0417\\u0430\\u0439\\u043c":"c0100004-2021-0322-1602-c041777ac177","\\u0423\\u0418\\u0414_\\u0417\\u0430\\u044f\\u0432\\u043a\\u0430":"a0100006-2021-0322-1600-01771ca07de7","\\u041a\\u043e\\u0434\\u0421\\u041c\\u0421":"6498","Payment":{"CardId":"8","\\u0414\\u0430\\u0442\\u0430":"20210322160301","PaymentId":"683692","OrderId":"998168"},"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430":"1050.00","\\u041d\\u043e\\u043c\\u0435\\u0440":"210H3NZI1638000001"},"\\u041a\\u043b\\u0438\\u0435\\u043d\\u0442":{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"\\u0422\\u0435\\u0441\\u0442","\\u0418\\u043c\\u044f":"\\u0421\\u0435\\u0440\\u0433\\u0435\\u0439","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"\\u0412\\u043b\\u0430\\u0434\\u0438\\u043c\\u0438\\u0440\\u043e\\u0432\\u0438\\u0447 ","\\u0414\\u0430\\u0442\\u0430\\u0420\\u043e\\u0436\\u0434\\u0435\\u043d\\u0438\\u044f\\u041f\\u043e\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0443":"19901111000000","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0418\\u043d\\u0434\\u0435\\u043a\\u0441":"443110","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0420\\u0435\\u0433\\u0438\\u043e\\u043d":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0420\\u0430\\u0439\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0413\\u043e\\u0440\\u043e\\u0434":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0430 \\u0433","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u041d\\u0430\\u0441\\u041f\\u0443\\u043d\\u043a\\u0442":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0423\\u043b\\u0438\\u0446\\u0430":"\\u041b\\u0435\\u043d\\u0438\\u043d\\u0430 \\u043f\\u0440-\\u043a\\u0442","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0414\\u043e\\u043c":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u041a\\u0432\\u0430\\u0440\\u0442\\u0438\\u0440\\u0430":"1","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0418\\u043d\\u0434\\u0435\\u043a\\u0441":"443110","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0420\\u0435\\u0433\\u0438\\u043e\\u043d":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0420\\u0430\\u0439\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0413\\u043e\\u0440\\u043e\\u0434":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0430 \\u0433","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041d\\u0430\\u0441\\u041f\\u0443\\u043d\\u043a\\u0442":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0423\\u043b\\u0438\\u0446\\u0430":"\\u041b\\u0435\\u043d\\u0438\\u043d\\u0430 \\u043f\\u0440-\\u043a\\u0442","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0414\\u043e\\u043c":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041a\\u0432\\u0430\\u0440\\u0442\\u0438\\u0440\\u0430":"1","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"8(345)435-34-54","\\u0418\\u041d\\u041d":"","\\u041a\\u043e\\u043b\\u0438\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e\\u0418\\u0436\\u0434\\u0435\\u0432\\u0435\\u043d\\u0446\\u0435\\u0432":"","\\u041c\\u0435\\u0441\\u0442\\u043e\\u0420\\u043e\\u0436\\u0434\\u0435\\u043d\\u0438\\u044f\\u041f\\u043e\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0443":"11","\\u041e\\u0431\\u0440\\u0430\\u0437\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0410\\u0434\\u0440\\u0435\\u0441":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0413\\u0440\\u0430\\u0444\\u0438\\u043a\\u0417\\u0430\\u043d\\u044f\\u0442\\u043e\\u0441\\u0442\\u0438":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0414\\u043e\\u043b\\u0436\\u043d\\u043e\\u0441\\u0442\\u044c":"\\u0441\\u043b\\u0435\\u0441\\u0430\\u0440\\u044c","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0415\\u0436\\u0435\\u043c\\u0435\\u0441\\u044f\\u0447\\u043d\\u044b\\u0439\\u0414\\u043e\\u0445\\u043e\\u0434":"11","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u041d\\u0430\\u0437\\u0432\\u0430\\u043d\\u0438\\u0435":"J&J \\u0410\\u041e\\u0417\\u0422 (\\u0433 \\u0421\\u0430\\u043d\\u043a\\u0442-\\u041f\\u0435\\u0442\\u0435\\u0440\\u0431\\u0443\\u0440\\u0433, \\u0443\\u043b \\u041d\\u0430\\u043b\\u0438\\u0447\\u043d\\u0430\\u044f, \\u0434 40 \\u043a 7, \\u043e\\u0444 166)","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0421\\u0442\\u0430\\u0436\\u0420\\u0430\\u0431\\u043e\\u0442\\u044b\\u041b\\u0435\\u0442":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0421\\u0444\\u0435\\u0440\\u0430\\u0414\\u0435\\u044f\\u0442\\u0435\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0438":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"8(911)111-11-11","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0414\\u0430\\u0442\\u0430\\u0412\\u044b\\u0434\\u0430\\u0447\\u0438":"11111111000000","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041a\\u0435\\u043c\\u0412\\u044b\\u0434\\u0430\\u043d":"\\u0435\\u043a\\u0435\\u043a","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041a\\u043e\\u0434\\u041f\\u043e\\u0434\\u0440\\u0430\\u0437\\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u044f":"111-111","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041d\\u043e\\u043c\\u0435\\u0440":"-11111","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0421\\u0435\\u0440\\u0438\\u044f":"1111","\\u041f\\u043e\\u043b":"\\u041c\\u0443\\u0436\\u0441\\u043a\\u043e\\u0439","\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u043d\\u044b\\u0435\\u041b\\u0438\\u0446\\u0430":[{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"\\u044b\\u0432\\u0430","\\u0418\\u043c\\u044f":"","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"","\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439":"8(911)111-11-11","\\u0421\\u0442\\u0435\\u043f\\u0435\\u043d\\u044c\\u0420\\u043e\\u0434\\u0441\\u0442\\u0432\\u0430":""},{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"\\u0446\\u0443\\u043a\\u0443\\u0446\\u043a","\\u0418\\u043c\\u044f":"","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"","\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439":"8(911)111-11-11","\\u0421\\u0442\\u0435\\u043f\\u0435\\u043d\\u044c\\u0420\\u043e\\u0434\\u0441\\u0442\\u0432\\u0430":""}]}},{"\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f":"NalPlus","\\u041d\\u043e\\u043c\\u0435\\u0440":"0321-100003","\\u0421\\u0443\\u043c\\u043c\\u0430":"14000.00","\\u0421\\u0440\\u043e\\u043a":"14","\\u0414\\u0430\\u0442\\u0430":"20210321001119","\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442\\u0421\\u0442\\u0430\\u0432\\u043a\\u0430":"1.00","\\u041c\\u0435\\u0440\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":"1.50","\\u0423\\u0418\\u0414_\\u0417\\u0430\\u0439\\u043c":"c0100003-2021-0321-0008-c041777ac177","\\u0423\\u0418\\u0414_\\u0417\\u0430\\u044f\\u0432\\u043a\\u0430":"a0100005-2021-0317-1519-01771ca07de7","\\u041a\\u043e\\u0434\\u0421\\u041c\\u0421":"9505","Payment":{"CardId":"5","\\u0414\\u0430\\u0442\\u0430":"20210321001201","PaymentId":"683115","OrderId":"997362"},"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430":0,"\\u041d\\u043e\\u043c\\u0435\\u0440":0},"\\u041a\\u043b\\u0438\\u0435\\u043d\\u0442":{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"\\u0418\\u0432\\u0430\\u043d\\u043e\\u0432","\\u0418\\u043c\\u044f":"\\u0410\\u0440\\u0441\\u0435\\u043d","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"\\u041f\\u0435\\u0442\\u0440\\u043e\\u0432\\u0447\\u0438","\\u0414\\u0430\\u0442\\u0430\\u0420\\u043e\\u0436\\u0434\\u0435\\u043d\\u0438\\u044f\\u041f\\u043e\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0443":"19900101000000","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0418\\u043d\\u0434\\u0435\\u043a\\u0441":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0420\\u0435\\u0433\\u0438\\u043e\\u043d":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0420\\u0430\\u0439\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0413\\u043e\\u0440\\u043e\\u0434":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0430 \\u0433","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u041d\\u0430\\u0441\\u041f\\u0443\\u043d\\u043a\\u0442":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0423\\u043b\\u0438\\u0446\\u0430":"\\u041b\\u0435\\u043d\\u0438\\u043d\\u0430 \\u043f\\u0440-\\u043a\\u0442","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0414\\u043e\\u043c":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u041a\\u0432\\u0430\\u0440\\u0442\\u0438\\u0440\\u0430":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0420\\u0435\\u0433\\u0438\\u0441\\u0442\\u0440\\u0430\\u0446\\u0438\\u0438\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0418\\u043d\\u0434\\u0435\\u043a\\u0441":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0420\\u0435\\u0433\\u0438\\u043e\\u043d":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0420\\u0430\\u0439\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0413\\u043e\\u0440\\u043e\\u0434":"\\u0421\\u0430\\u043c\\u0430\\u0440\\u0430 \\u0433","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041d\\u0430\\u0441\\u041f\\u0443\\u043d\\u043a\\u0442":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0423\\u043b\\u0438\\u0446\\u0430":"\\u041b\\u0435\\u043d\\u0438\\u043d\\u0430 \\u043f\\u0440-\\u043a\\u0442","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0414\\u043e\\u043c":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041a\\u0432\\u0430\\u0440\\u0442\\u0438\\u0440\\u0430":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"","\\u0410\\u0434\\u0440\\u0435\\u0441\\u0424\\u0430\\u043a\\u0442\\u0438\\u0447\\u0435\\u0441\\u043a\\u043e\\u0433\\u043e\\u041f\\u0440\\u043e\\u0436\\u0438\\u0432\\u0430\\u043d\\u0438\\u044f\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"8(321)312-31-23","\\u0418\\u041d\\u041d":"","\\u041a\\u043e\\u043b\\u0438\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e\\u0418\\u0436\\u0434\\u0435\\u0432\\u0435\\u043d\\u0446\\u0435\\u0432":"","\\u041c\\u0435\\u0441\\u0442\\u043e\\u0420\\u043e\\u0436\\u0434\\u0435\\u043d\\u0438\\u044f\\u041f\\u043e\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0443":"fgh","\\u041e\\u0431\\u0440\\u0430\\u0437\\u043e\\u0432\\u0430\\u043d\\u0438\\u0435":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0410\\u0434\\u0440\\u0435\\u0441":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0413\\u0440\\u0430\\u0444\\u0438\\u043a\\u0417\\u0430\\u043d\\u044f\\u0442\\u043e\\u0441\\u0442\\u0438":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0414\\u043e\\u043b\\u0436\\u043d\\u043e\\u0441\\u0442\\u044c":"\\u0441\\u043b\\u0435\\u0441\\u0430\\u0440\\u044c","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0415\\u0436\\u0435\\u043c\\u0435\\u0441\\u044f\\u0447\\u043d\\u044b\\u0439\\u0414\\u043e\\u0445\\u043e\\u0434":"111111111111111","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u041d\\u0430\\u0437\\u0432\\u0430\\u043d\\u0438\\u0435":"\\u041f\\u0410\\u041e \\"\\u0421\\u0415\\u0412\\u0415\\u0420\\u0421\\u0422\\u0410\\u041b\\u042c\\" (\\u0412\\u043e\\u043b\\u043e\\u0433\\u043e\\u0434\\u0441\\u043a\\u0430\\u044f \\u043e\\u0431\\u043b, \\u0433 \\u0427\\u0435\\u0440\\u0435\\u043f\\u043e\\u0432\\u0435\\u0446, \\u0443\\u043b \\u041c\\u0438\\u0440\\u0430, \\u0434 30)","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0421\\u0442\\u0430\\u0436\\u0420\\u0430\\u0431\\u043e\\u0442\\u044b\\u041b\\u0435\\u0442":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0421\\u0444\\u0435\\u0440\\u0430\\u0414\\u0435\\u044f\\u0442\\u0435\\u043b\\u044c\\u043d\\u043e\\u0441\\u0442\\u0438":"","\\u041e\\u0440\\u0433\\u0430\\u043d\\u0438\\u0437\\u0430\\u0446\\u0438\\u044f\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d":"8(111)111-11-11","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0414\\u0430\\u0442\\u0430\\u0412\\u044b\\u0434\\u0430\\u0447\\u0438":"20111111000000","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041a\\u0435\\u043c\\u0412\\u044b\\u0434\\u0430\\u043d":"11111111111111111","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041a\\u043e\\u0434\\u041f\\u043e\\u0434\\u0440\\u0430\\u0437\\u0434\\u0435\\u043b\\u0435\\u043d\\u0438\\u044f":"111-111","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u041d\\u043e\\u043c\\u0435\\u0440":"-11111","\\u041f\\u0430\\u0441\\u043f\\u043e\\u0440\\u0442\\u0421\\u0435\\u0440\\u0438\\u044f":"1111","\\u041f\\u043e\\u043b":"\\u041c\\u0443\\u0436\\u0441\\u043a\\u043e\\u0439","\\u041a\\u043e\\u043d\\u0442\\u0430\\u043a\\u0442\\u043d\\u044b\\u0435\\u041b\\u0438\\u0446\\u0430":[{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"1111111111111111","\\u0418\\u043c\\u044f":"","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"","\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439":"8(111)111-11-11","\\u0421\\u0442\\u0435\\u043f\\u0435\\u043d\\u044c\\u0420\\u043e\\u0434\\u0441\\u0442\\u0432\\u0430":""},{"\\u0424\\u0430\\u043c\\u0438\\u043b\\u0438\\u044f":"11111111","\\u0418\\u043c\\u044f":"","\\u041e\\u0442\\u0447\\u0435\\u0441\\u0442\\u0432\\u043e":"","\\u0422\\u0435\\u043b\\u0435\\u0444\\u043e\\u043d\\u041c\\u043e\\u0431\\u0438\\u043b\\u044c\\u043d\\u044b\\u0439":"8(111)111-11-11","\\u0421\\u0442\\u0435\\u043f\\u0435\\u043d\\u044c\\u0420\\u043e\\u0434\\u0441\\u0442\\u0432\\u0430":""}]}}]'
;
$js = '[{"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0417\\u0430\\u0439\\u043c\\u0430":"0322-100004","\\u0414\\u0430\\u0442\\u0430\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"20210322161113","\\u041d\\u043e\\u043c\\u0435\\u043e\\u0440\\u041e\\u043f\\u043b\\u0430\\u0442\\u044b":"0322-15","ID_\\u0417\\u0430\\u043a\\u0430\\u0437":"998176","ID_\\u0423\\u0441\\u043f\\u0435\\u0448\\u043d\\u0430\\u044f\\u041e\\u043f\\u0435\\u0440\\u0430\\u0446\\u0438\\u044f":"683701","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0414":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0440\\u043e\\u0446\\u0435\\u043d\\u0442":"0.00","\\u0421\\u0443\\u043c\\u043c\\u0430\\u041e\\u0442\\u0432\\u0435\\u0442\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0441\\u0442\\u0438":null,"\\u0421\\u0443\\u043c\\u043c\\u0430\\u041f\\u0435\\u043d\\u0438":null,"\\u0421\\u0443\\u043c\\u043c\\u0430\\u041a\\u043e\\u043c\\u0438\\u0441\\u0441\\u0438\\u0438":"140.00","\\u041f\\u0440\\u043e\\u043b\\u043e\\u043d\\u0433\\u0430\\u0446\\u0438\\u044f":0,"\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0430":{"\\u0421\\u0443\\u043c\\u043c\\u0430\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":0,"\\u041d\\u043e\\u043c\\u0435\\u0440\\u0421\\u0442\\u0440\\u0430\\u0445\\u043e\\u0432\\u043a\\u0438":""}}]';
$ar = json_decode($js);
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($ar);echo '</pre><hr />';
exit;

$resp = $core->best2pay->get_register_info(2242, '978674');
echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($resp);echo '</pre><hr />';
exit;

//70093bcc-3a3f-11eb-9983-00155d2d0507
/* создаем уиды
$orders = $core->orders->get_orders();
foreach ($orders as $order)
{
    $order_date = strtotime($order->date);
    $uid = 'a0'.$order->order_id.'-'.date('Y', $order_date).'-'.date('md', $order_date).'-'.date('Hi', $order_date).'-01771ca07de7';
    $core->orders->update_order($order->order_id, array('uid' => $uid));
echo $uid.'<br />';
}

$contracts = $core->contracts->get_contracts();
foreach ($contracts as $contract)
{
    $contract_date = strtotime($contract->create_date);
    $uid = 'c0'.$contract->id.'-'.date('Y', $contract_date).'-'.date('md', $contract_date).'-'.date('Hi', $contract_date).'-c041777ac177';
    $core->contracts->update_contract($contract->id, array('uid' => $uid));
echo $uid.'<br />';
    
}

exit;
*/

//
$contracts = $core->contracts->get_contracts(array('id' => 100011));
foreach ($contracts as $contract)
{
    $contract->user = $core->users->get_user((int)$contract->user_id);
    $contract->order = $core->orders->get_order((int)$contract->order_id);
    $contract->p2pcredit = $core->best2pay->get_contract_p2pcredit($contract->id);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($contract->p2pcredit);echo '</pre><hr />';
}
$core->soap1c->send_contracts($contracts);

/*
$core->notify->send_reject_reason(102433);

exit;
$core->sms->send('79050247065', 'Тестовое сообщение');

// регистрация карты
//$link = $core->best2pay->add_card('ADD-CARD-002');

//echo '<a target="_blank" href="'.$link.'">'.$link.'</a>';


/*
// Оплата картой
$link = $core->best2pay->get_payment_link(100, 'TEST-PAYMENT-001');

echo '<a href="'.$link.'">'.$link.'</a>';
*/


// инфо по заказу
//$info = $core->best2pay->get_operation_info(2243, 815107, 579290);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($info);echo '</pre><hr />';


// рекурентный платеж
// 8c23314b-3300-424f-b132-53b89b013ef1
//$recurrent = $core->best2pay->recurrent(100);
//echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($recurrent);echo '</pre><hr />';

//$core->unicom_scoring->test();

