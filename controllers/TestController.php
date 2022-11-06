<?php
error_reporting(-1);
ini_set('display_errors', 'Off');

class TestController extends Controller
{
    public function fetch()
    {
        $user = TestModel::find(11965);

        echo '<pre>';
        var_dump($user->lastname);
        exit;
    }
}