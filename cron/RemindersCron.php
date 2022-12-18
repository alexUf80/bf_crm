<?php
error_reporting(-1);
ini_set('display_errors', 'On');
chdir(dirname(__FILE__) . '/../');

require 'autoload.php';

class RemindersCron extends Core
{
    public function __construct()
    {
        parent::__construct();
        $this->run();
    }

    private function run()
    {
        RegSegment::sendReminder(1);
        ActiveSegment::sendReminder(1);
        PendingSegment::sendReminder(1);
        RepSegment::sendReminder(1);
    }
}

new RemindersCron();