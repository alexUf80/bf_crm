<?php

class ActiveSegment extends SegmentsAbastract
{

    public static function sendReminder($reminder)
    {
        $reminders = RemindersORM::where('segmentId', 3)->get();

        foreach ($reminders as $reminder) {

            switch ($reminder->id) {
                case 4:
                    self::toDayReminder($reminder);
                    break;

                case 6:
                    self::beforeDayReminder($reminder);
                    break;

            }
        }
    }

    private static function toDayReminder($reminder)
    {
        $nowHour = gmdate('G', strtotime('UTC+3'));

        if ($nowHour != $reminder->countTime)
            exit;

        $now = date('Y-m-d');

        $contracts = ContractsORM::where('return_date', $now)->get();

        foreach ($contracts as $contract) {

            $user = UsersORM::where('id', $contract->user_id)->fisrt();

            $isSent = RemindersCronORM::where('userId', $user->id)->where('reminderId', $reminder->id)->first();

            if (!empty($isSent))
                continue;

            if (empty($user->time_zone))
                continue;

            $clientTime = gmdate('Y-m-d H:i:s', strtotime($user->time_zone));

            $isHoliday = WeekendCalendarORM::where('date', date('Y-m-d'))->first();
            $settings = new Settings();
            $sent = 0;

            if (!empty($isHoliday) && date('G', strtotime($clientTime)) >= $settings->holiday_worktime['from'] && date('G', strtotime($clientTime)) < $settings->holiday_worktime['to'])
                $sent = 1;

            if (empty($isHoliday) && date('G', strtotime($clientTime)) >= $settings->workday_worktime['from'] && date('G', strtotime($clientTime)) < $settings->workday_worktime['to'])
                $sent = 1;

            $reminderLog =
                [
                    'reminderId' => $reminder->id,
                    'userId' => $user->id,
                    'message' => $reminder->msgSms,
                    'phone' => $user->phone_mobile
                ];

            RemindersCronORM::insert($reminderLog);

            if ($sent == 1) {
                $send =
                    [
                        'phone' => $user->phone_mobile,
                        'msg' => $reminder->msgSms
                    ];

                self::send($send);
            }
        }
    }

    private static function beforeDayReminder($reminder)
    {
        $returnDate = date('Y-m-d', strtotime('+'.$reminder->countTime.' days'));

        $contracts = ContractsORM::where('return_date', $returnDate)->get();

        foreach ($contracts as $contract) {
            $user = UsersORM::where('id', $contract->user_id)->fisrt();

            $isSent = RemindersCronORM::where('userId', $user->id)->where('reminderId', $reminder->id)->first();

            if (!empty($isSent))
                continue;

            if (empty($user->time_zone))
                continue;

            $clientTime = gmdate('Y-m-d H:i:s', strtotime($user->time_zone));

            $isHoliday = WeekendCalendarORM::where('date', date('Y-m-d'))->first();
            $settings = new Settings();
            $sent = 0;

            if (!empty($isHoliday) && date('G', strtotime($clientTime)) >= $settings->holiday_worktime['from'] && date('G', strtotime($clientTime)) < $settings->holiday_worktime['to'])
                $sent = 1;

            if (empty($isHoliday) && date('G', strtotime($clientTime)) >= $settings->workday_worktime['from'] && date('G', strtotime($clientTime)) < $settings->workday_worktime['to'])
                $sent = 1;

            if ($sent == 1) {

                $reminderLog =
                    [
                        'reminderId' => $reminder->id,
                        'userId' => $user->id,
                        'message' => $reminder->msgSms,
                        'phone' => $user->phone_mobile
                    ];

                RemindersCronORM::insert($reminderLog);

                $send =
                    [
                        'phone' => $user->phone_mobile,
                        'msg' => $reminder->msgSms
                    ];

                self::send($send);
            }
        }
    }
}