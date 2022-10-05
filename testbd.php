<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set ( 'max_execution_time', 1200); 
require 'autoload.php';

$core = new Core();
exit;
$core->db->query("
    SELECT *
    FROM __documents
    WHERE type = 'DOP_SOGLASHENIE_PROLONGATSIYA'
");
//type = 'SOGLASIE_VZAIMODEYSTVIE'
//"2021-12-06 08:26:30"

$results = $core->db->results();
$results = array_map(function($var){
    $var->params = unserialize($var->params);
    return $var;
}, $results);
//$results = $results[0];
//todo: форыч
foreach ($results as $key => $result) {
	$order = $core->orders->get_order($result->order_id);
	$result->params['order_created'] = $order->date;
	$core->documents->update_document($result->id, array('params'=>$result->params));
}
//var_dump($results->params);
//$results->params = unserialize($results->params);
//$results->params['order_created'] = "2021-12-31 08:26:30";
//var_dump($results->params);

//$order = $core->orders->get_order($results->order_id);
//var_dump($order->date);
//$core->documents->update_document($results->id, array('params'=>$results->params));

echo __FILE__.' '.__LINE__.'<br /><pre>';var_dump($results);echo '</pre><hr />';
exit;