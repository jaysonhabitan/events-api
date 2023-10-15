<?php

namespace App\Tools\Event;

use App\Enum\Frequency;

class MonthlyChecker extends ScheduleConflictChecker
{
    /**
     * Checks if the new and existing event
     * has a conflicting schedule.
     *
     * @return bool
     */
    public function hasConflict(): bool
    {
        if ($this->currEventFrequencyId !== Frequency::MONTHLY_ID) return false;

        // Checks if the existing event occurs indefinitely.
        if (!$this->currEventFinalDateTime) {

            // Checks if the new event occurs indefinitely.
            if (!$this->newEventFinalDateTime) {
                switch ($this->newEventFrequencyId) {
                    case Frequency::ONCE_OFF_ID:
                        return $this->checkOnceOffSchedule();
                    case Frequency::WEEKLY_ID:
                        return $this->isScheduleOverlapping();
                    case Frequency::MONTHLY_ID:
                        return $this->indefiniteMonthlyChecker();
                    default:
                        return false;
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
            if (!$this->newEventFinalDateTime) {
                // Checks if the new event occurs indefinitely.
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
     * Checks if weekly schedules are conflicting.
     *
     * @return bool
     */
    protected function checkOnceOffSchedule(): bool
    {
        $originalDay = date('d', $this->currEventStartsAt);
        $currentDate = format_unix_date($this->currEventStartsAt);
        $to = $this->currEventFinalDateTime
            ? $this->currEventFinalDateTime
            : $this->newEventStartsAt;

        while ($currentDate <= $to) {
            if (
                $currentDate === format_unix_date($this->newEventStartsAt)
                && $this->newEventStartsAt >= $this->currEventStartsAt
                && $this->isScheduleOverlapping()
            ) return true;

            $currentDate = get_next_month_date($currentDate, $originalDay);
        }

        return false;
    }

    /**
     * Checks if weekly schedules are conflicting.
     *
     * @return bool
     */
    protected function checkWeeklySchedule(): bool
    {
        $currentDate = format_unix_date($this->newEventStartsAt);

        $to = $this->newEventFinalDateTime
            ? $this->newEventFinalDateTime
            : $this->currEventFinalDateTime;

        $currEventTo = $this->currEventFinalDateTime
            ? $this->currEventFinalDateTime
            : $this->newEventFinalDateTime;

        // Existing event dates.
        $conflictingDates = $this->conflictingDates(
            $this->currEventStartsAt,
            $currEventTo
        );

        while ($currentDate <= $to) {
            if (in_array($currentDate, $conflictingDates)
                && $this->isScheduleOverlapping()
            ) return true;

            $currentDate = strtotime("+1 week", $currentDate);
        }

        return false;
    }

    /**
     * Checks if monthly schedules are conflicting.
     *
     * @return bool
     */
    protected function checkMonthlySchedule(): bool
    {
        $originalDay = date('d', $this->newEventStartsAt);
        $currentDate = format_unix_date($this->newEventStartsAt);

        $to = $this->newEventFinalDateTime
            ? $this->newEventFinalDateTime
            : $this->currEventFinalDateTime;

        $currEventTo = $this->currEventFinalDateTime
            ? $this->currEventFinalDateTime
            : $this->newEventFinalDateTime;

        $conflictingDates = $this->conflictingDates(
            $this->currEventStartsAt,
            $currEventTo
        );

        while ($currentDate <= $to) {
            if (in_array($currentDate, $conflictingDates)
                && $this->isScheduleOverlapping()
            ) return true;

            $currentDate = get_next_month_date($currentDate, $originalDay);
        }

        return false;
    }

    /**
     * Checks the conflicting schedule between
     * existing indefinite monthly event and
     * new indefinite monthly event.
     *
     * @return bool
     */
    protected function indefiniteMonthlyChecker(): bool
    {
        $possibleMonthEnds = [28, 29, 30,31];
        $newEventDay = date('d', $this->newEventStartsAt);
        $currEventDay = date('d', $this->currEventStartsAt);

        $occursEveryMonthEnds = in_array($newEventDay, $possibleMonthEnds)
            && in_array($currEventDay, $possibleMonthEnds);

        if ((
                $occursEveryMonthEnds
                || $newEventDay === $currEventDay
            )
            && $this->isScheduleOverlapping()
        ) return true;


        return false;
    }
}
