<?php

namespace App\Tools\Event;

use App\Enum\Frequency;
use App\Models\Event;

abstract class ScheduleConflictChecker
{
    protected int $currEventStartsAt;
    protected int $currEventEndsAt;
    protected int $currEventFrequencyId;
    protected string $currEventDayOfWeek;
    protected $currEventFinalDateTime;

    protected int $newEventStartsAt;
    protected int $newEventEndsAt;
    protected int $newEventFrequencyId;
    protected string $newEventDayOfWeek;
    protected $newEventFinalDateTime;

    protected string $currEventFrom;
    protected string $currEventTo;
    protected string $newEventFrom;
    protected string $newEventTo;

    /**
     * Initialize and prepare the necessary data.
     *
     * @param Event $event
     * @param int $newEventStartsAt
     * @param int $newEventEndsAt
     * @param int $newEventFrequencyId
     * @param int|null $newEventFinalDateTime
     *
     * @return void
     */
    public function __construct(
        Event $event,
        int $newEventStartsAt,
        int $newEventEndsAt,
        int $newEventFrequencyId,
        int $newEventFinalDateTime = null
    ) {
        $currEventStartAtUnix = strtotime($event->start_date_time);
        $currEventEndsAtUnix = strtotime("+{$event->duration} minutes", $currEventStartAtUnix);

        $this->currEventStartsAt = $currEventStartAtUnix;
        $this->currEventEndsAt = $currEventEndsAtUnix;
        $this->currEventFinalDateTime = strtotime($event->end_date_time) ?? null;
        $this->currEventFrequencyId = $event->frequency_id;
        $this->currEventDayOfWeek = date('l', $currEventStartAtUnix);


        $this->newEventStartsAt = $newEventStartsAt;
        $this->newEventEndsAt = $newEventEndsAt;
        $this->newEventFinalDateTime = $newEventFinalDateTime;
        $this->newEventFrequencyId = $newEventFrequencyId;
        $this->newEventDayOfWeek = date('l', $newEventStartsAt);

        $this->newEventFrom = date('H:i', $newEventStartsAt);
        $this->newEventTo = date('H:i', $newEventEndsAt);
        $this->currEventFrom = date('H:i', $currEventStartAtUnix);
        $this->currEventTo = date('H:i', $currEventEndsAtUnix);
    }

    /**
     * Abstract function for checking event conflicts.
     */
    abstract protected function hasConflict();

    /**
     * Checks if weekly schedules are conflicting.
     *
     * @return bool
     */
    abstract protected function checkWeeklySchedule();

    /**
     * Checks if monthly schedules are conflicting.
     *
     * @return bool
     */
    abstract protected function checkMonthlySchedule();

    /**
     * Checks if there is a conflicting schedule
     * between two events that occurs between
     * two dates.
     *
     * @param bool
     */
    protected function checkRangedSchedules(): bool
    {
        switch ($this->newEventFrequencyId) {
            case Frequency::WEEKLY_ID:
                return $this->checkWeeklySchedule();
            case Frequency::MONTHLY_ID:
                return $this->checkMonthlySchedule();
            default:
                // no break
                break;
        }

        return false;
    }

    /**
     * Checks if the given schedules are
     * overlapping each other.
     *
     * @param bool
     */
    protected function isScheduleOverlapping(): bool
    {
        if (
            ($this->newEventFrom >= $this->currEventFrom && $this->newEventFrom < $this->currEventTo)
            || ($this->newEventTo > $this->currEventFrom && $this->newEventTo <=  $this->currEventTo)
            || ($this->newEventFrom <= $this->currEventFrom && $this->newEventTo >=  $this->currEventTo)
        ) return true;

        return false;
    }

    /**
     * Checks if the new event is within
     * the date range of the existing event.
     *
     * @return bool
     */
    protected function isWithinDateRange()
    {

        if (!$this->currEventFinalDateTime && !$this->newEventFinalDateTime) {
            return (
                $this->currEventStartsAt >= $this->newEventStartsAt
                && $this->currEventStartsAt <= $this->newEventFinalDateTime
            );
        }

        if (!$this->currEventFinalDateTime
            && $this->newEventStartsAt
            && $this->newEventFinalDateTime
        ) return $this->newEventFinalDateTime >= $this->currEventStartsAt;

        if (
            !$this->newEventFinalDateTime
            && $this->currEventStartsAt
            && $this->currEventFinalDateTime
        ) {
            return (
                $this->newEventStartsAt <= $this->currEventStartsAt
                || (
                    $this->newEventStartsAt >= $this->currEventStartsAt
                    && $this->newEventStartsAt <= $this->currEventFinalDateTime
                )
            );
        }

        if (
            $this->newEventStartsAt
            && $this->newEventFinalDateTime
            && $this->currEventStartsAt
            && $this->currEventFinalDateTime
        ) {
            return (
                $this->currEventStartsAt <= $this->newEventFinalDateTime
                && $this->currEventFinalDateTime >= $this->newEventStartsAt
            );
        }

        return false;
    }

    /**
     * Returns a list of possible conflicting
     * dates from a given period of time.
     *
     * @param string $startDate
     * @param string $endDate
     */
    protected function conflictingDates($startDate, $endDate = null)
    {
        $startDate = format_unix_date($startDate);
        $endDate = format_unix_date($endDate);

        $currentDate = $startDate;
        $dates = [];

        while($currentDate <= $endDate) {
            $dates[] = $currentDate;
            $currentDate = get_next_month_date($currentDate, date('d', $startDate));
        }

        return $dates;
    }

}
