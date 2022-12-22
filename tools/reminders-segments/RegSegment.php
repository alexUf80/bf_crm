<?php

class RegSegment extends SegmentsAbastract
{

    public static function sendReminder($request)
    {
        $reminders = RemindersORM::where('segmentId', 1)->get();

        foreach ($reminders as $reminder) {
            $startTime = date('Y-m-d H:i:s', strtotime('-' . $reminder->countTime . ' ' . $reminder->timeType));
            $users = UsersORM::where('lastUpdate', '<=', $startTime)->where('stage_card', 0)->get();

            if (!empty($users)) {
                foreach ($users as $user) {

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
        }
    }
}