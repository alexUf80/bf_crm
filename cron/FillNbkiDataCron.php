<?php
error_reporting(-1);
ini_set('display_errors', 'On');


chdir(dirname(__FILE__) . '/../');

require 'autoload.php';
class FillNbkiDataCron extends Core {

    public function __construct()
    {
        parent::__construct();

        $scorings = ScoringsORM::query()->where('type', '=', 'nbki')->where('status', '=', 'completed')->get();
        foreach ($scorings as $scoring) {

        }

    }

}

?>