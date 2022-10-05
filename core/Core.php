<?php

class Core
{
    public $is_developer = 0;
    
	private $classes = array(
        'collector_tags' => 'CollectorTags',
	);
	private static $objects = array();
	
	public function __construct()
	{
        if (in_array($_SERVER['REMOTE_ADDR'], array('94.154.39.111', '94.154.39.244', '212.46.18.173')))
            $this->is_developer = 1;
	    if (isset($_COOKIE['developer']) && $_COOKIE['developer'] == '12091979')
            $this->is_developer = 1;
        if (isset($_GET['set_developer']))
        {
            setcookie('developer', $_GET['set_developer'], time()+86400, '/');
            header('Location: /');
            exit;
        }

        if ($this->is_developer)
        {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        }
        
	}

	public function __get($name)
	{
		if(isset(self::$objects[$name]))
			return(self::$objects[$name]);
		
		if (class_exists(ucfirst($name)))
            $class = ucfirst($name);
        elseif(array_key_exists($name, $this->classes))
    		$class = $this->classes[$name];
		else
            return null;

		self::$objects[$name] = new $class();
		
		return self::$objects[$name];
	}
}