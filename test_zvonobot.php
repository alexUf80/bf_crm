<?php
error_reporting(-1);
ini_set('display_errors', 'On');
exit;
require_once('autoload.php');

$core = new Core();

echo '<pre>';
$resp = $core->ekam->send_receipt(//210159
    [
        'amount' => 100,
        'title' => 'Тайтл',
        'operation_id' => 'eybr328',
        'email' => 'geg@gdg.ru'
    ]
);
var_dump(json_decode($resp));
echo '<pre>';