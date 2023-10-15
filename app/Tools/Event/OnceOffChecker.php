<?php

namespace App\Tools\Event;

use App\Enum\Frequency;

class OnceOffChecker extends ScheduleConflictChecker
{
    /**
     * Checks if the new and existing event
     * has a conflicting schedule.
     *
     * @return bool
     */
    public function hasConflict(): bool
    {
        if ($this->currEventFrequencyId !== Frequency::ONCE_OFF_ID) return false;

        $meetsCondition = !$this->newEventFinalDateTime
            ? $this->newEventStartsAt <= $this->currEventStartsAt
            : $this->isWithinDateRange();

        if ($meetsCondition) {
            switch ($this->newEventFrequencyId) {
                case Frequency::WEEKLY_ID:
                    return $this->checkWeeklySchedule();
                case Frequency::MONTHLY_ID:
                    return $this->checkMonthlySchedule();
                default:
                    return $this->checkOnceOffSchedule();
            }
        }

        return false;
    }

     /**
     * Checks if once-off schedules are conflicting.
     *
     * @return bool
     */
    protected function checkOnceOffSchedule(): bool
    {
        if (
            format_unix_date($this->newEventStartsAt) === format_unix_date($this->currEventStartsAt)
            && $this->isScheduleOverlapping()
        ) return true;

        return false;
    }

    /**
     * Checks if weekly schedules are conflicting.
     *
     * @return bool
     */
    protected function checkWeeklySchedule(): bool
    {
        if (
            $this->newEventDayOfWeek === $this->currEventDayOfWeek
            && $this->newEventStartsAt <= $this->currEventStartsAt
            && $this->isScheduleOverlapping()
        ) return true;

        return false;
    }

    /**
     * Check if monthly schedules are conflicting.
     *
     * @return bool
     */
    protected function checkMonthlySchedule(): bool
    {
        $originalDay = date('d', $this->newEventStartsAt);
        $currentDate = format_unix_date($this->newEventStartsAt);

        $to = $this->newEventFinalDateTime
            ? $this->newEventFinalDateTime
            : $this->currEventEndsAt;

        while ($currentDate <= $to) {
            if (
                $currentDate === format_unix_date($this->currEventStartsAt)
                && $this->isScheduleOverlapping()
            ) return true;

            $currentDate = get_next_month_date($currentDate, $originalDay);
        }

        return false;
    }
}
