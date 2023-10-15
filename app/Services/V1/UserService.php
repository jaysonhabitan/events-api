<?php

namespace App\Services\V1;

use App\Enum\Frequency;
use App\Models\User;
use App\Tools\Event\MonthlyChecker;
use App\Tools\Event\OnceOffChecker;
use App\Tools\Event\WeeklyChecker;
/**
 * UserService
 *
 * Contains functions mainly for user resource.
 */
class UserService
{
    /**
     * Check a given user if it has a conflicting event issue.
     *
     * @param  Use $user
     * @param  int $duration
     * @param  int $frequencyId
     * @param  string $startDateTime
     * @param  string|null $endDateTime
     *
     * @return bool
     */
    public function checkEvents(
        User $user,
        int $duration,
        int $frequencyId,
        string $startDateTime,
        string $endDateTime = null
    ): bool {
        $newEventStart = strtotime($startDateTime);
        $newEventEnd = strtotime("+{$duration} minutes", strtotime($startDateTime));
        $newEventFinalDateTime = strtotime($endDateTime);

        $userEvents = $user->events()
            ->get([
                'frequency_id',
                'start_date_time',
                'end_date_time',
                'duration'
            ]);

        foreach ($userEvents as $userEvent) {
            $data = [
                $userEvent,
                $newEventStart,
                $newEventEnd,
                $frequencyId,
                $newEventFinalDateTime
            ];

            switch ($userEvent->frequency_id) {
                case Frequency::ONCE_OFF_ID:
                    $onceOff = new OnceOffChecker(...$data);
                    if ($onceOff->hasConflict()) return true;
                case Frequency::WEEKLY_ID:
                    $weekly = new WeeklyChecker(...$data);
                    if ($weekly->hasConflict()) return true;
                case Frequency::MONTHLY_ID:
                    $monthly = new MonthlyChecker(...$data);
                    if ($monthly->hasConflict()) return true;
                default:
                    // no break
                    break;
            }
        }

        return false;
    }
}
