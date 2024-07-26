<?php
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('max_execution_time', '600');

require '../autoload.php';

//exit;

//phpinfo();

$core = new Core();




//$_GET['password'];
if ($_GET['password'] == 'Hjkdf8d') {
    $scoring = $core->scorings->get_scoring($_GET['id']);
    
    $body = unserialize($scoring->body);
    echo '<pre>'. $body['response'].'</pre>';
    echo '<hr>';
    echo '<pre>'. $body['result'].'</pre>';

    
}

exit;