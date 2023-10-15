<?php

namespace App\Tools\Event;

use App\Enum\Frequency;

class WeeklyChecker extends ScheduleConflictChecker
{
    /**
     * Checks if the new and existing event
     * has a conflicting schedule.
     *
     * @return bool
     */
    public function hasConflict(): bool
    {
        if ($this->currEventFrequencyId !== Frequency::WEEKLY_ID) return false;

        // Checks if the existing event occurs indefinitely.
        if (!$this->currEventFinalDateTime) {
            // Checks if the new event occurs indefinitely.
            if (!$this->newEventFinalDateTime) {
                switch ($this->newEventFrequencyId) {
                    case Frequency::WEEKLY_ID:
                        return $this->checkWeeklySchedule();
                    case Frequency::MONTHLY_ID:
                        return $this->isScheduleOverlapping();
                    default:
                        return $this->checkOnceOffSchedule();
                }
            }

            // Checks if the new event occurs between two dates.
            if (
                $this->newEventStartsAt
                && $this->newEventFinalDateTime
                && $this->isWithinDateRange()
            ) return $this->checkRangedSchedules();
        }

        // Checks if the existing event occurs between two dates.
        if ($this->currEventFinalDateTime && $this->isWithinDateRange()) {
            // Checks if the new event occurs indefinitely.
            if (!$this->newEventFinalDateTime) {
                switch ($this->newEventFrequencyId) {
                    case Frequency::WEEKLY_ID:
                        return $this->checkWeeklySchedule();
                    case Frequency::MONTHLY_ID:
                        return $this->checkMonthlySchedule();
                    default:
                        return $this->checkOnceOffSchedule();
                }
            }

            // Checks if the new event occurs between two dates.
            if (
                $this->newEventStartsAt
                && $this->newEventFinalDateTime
            ) return $this->checkRangedSchedules();
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
            $this->newEventDayOfWeek === $this->currEventDayOfWeek
            && ($this->newEventStartsAt >= $this->currEventStartsAt)
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
            && $this->isScheduleOverlapping()
        ) return true;

        return false;
    }

    /**
     * Checks if monthly schedules are conflicting.
     *
     * @return bool
     */
    protected function checkMonthlySchedule()
    {
        $originalDay = date('d', $this->newEventStartsAt);
        $currentDate = format_unix_date($this->newEventStartsAt);

        $to = $this->currEventFinalDateTime
            ? $this->currEventFinalDateTime
            : $this->newEventFinalDateTime;

        while ($currentDate <= $to) {
            if (
                format_unix_date($currentDate, 'l', true) === $this->currEventDayOfWeek
                && format_unix_date($currentDate) >= format_unix_date($this->currEventStartsAt)
                && $this->isScheduleOverlapping()
            ) return true;

            $currentDate = get_next_month_date($currentDate, $originalDay);
        }

        return false;
    }
}
