<?php

class PendingSegment extends SegmentsAbastract
{

    public static function sendReminder($reminder)
    {
        $reminders = RemindersORM::where('segmentId', 3)->get();

        foreach ($reminders as $reminder) {

            switch ($reminder->timeType) {
                case 'days':
                    self::daysWithoutSignReminder($reminder);
                    break;
            }
        }
    }

    private static function daysWithoutSignReminder($reminder)
    {
        $acceptDateFrom = date('Y-m-d 00:00:00', strtotime('-' . $reminder->countTime . ' days'));
        $acceptDateTo = date('Y-m-d 23:59:59', strtotime('-' . $reminder->countTime . ' days'));

        $contracts = ContractsORM::where('status', 0)->whereBetween('create_date', [$acceptDateFrom, $acceptDateTo])->get();

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