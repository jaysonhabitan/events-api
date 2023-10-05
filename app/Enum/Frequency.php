<?php

namespace App\Enum;

use Carbon\Carbon;

class Frequency
{
    const ONCE_OFF_ID = 1;
    const WEEKLY_ID = 2;
    const MONTHLY_ID = 3;

    const ONCE_OFF_NAME = 'Once-Off';
    const WEEKLY_NAME = 'Weekly';
    const MONTHLY_NAME = 'Monthly';

    const DEFINITION_LIST = [
        [
            'name' => self::ONCE_OFF_NAME,
            'description' => 'A one time event occurring from startDateTime up to the defined duration.'
        ],
        [
            'name' => self::WEEKLY_NAME,
            'description' => 'A recurring event happening within startDateTime up to endDateTime.'
        ],
        [
            'name' => self::MONTHLY_NAME,
            'description' => 'A recurring event happening within startDateTime up to endDateTime.'
        ],
    ];
}

