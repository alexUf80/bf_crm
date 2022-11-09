<?php

interface ApiInterface
{
    public static function get_request($request);
    public static function curl($params);
    public static function response($response);
}