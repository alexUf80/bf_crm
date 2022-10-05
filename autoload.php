<?php

function core_autoload($classname)
{
    if (file_exists(dirname(__FILE__).'/core/'.$classname.'.php'))
        require dirname(__FILE__).'/core/'.$classname.'.php';
    if (file_exists(dirname(__FILE__).'/models/'.$classname.'.php'))
        require dirname(__FILE__).'/models/'.$classname.'.php';
    if (file_exists(dirname(__FILE__).'/controllers/'.$classname.'.php'))
        require dirname(__FILE__).'/controllers/'.$classname.'.php';
    if (file_exists(dirname(__FILE__).'/scorings/'.$classname.'.php'))
        require dirname(__FILE__).'/scorings/'.$classname.'.php';
}
spl_autoload_register('core_autoload');
