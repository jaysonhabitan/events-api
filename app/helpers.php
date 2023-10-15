<?php

if (! function_exists('format_date')) {
    /**
     * Format the given date.
     * Returns the formatted date if the date is valid.
     * Returns null if the given date is invalid.
     *
     * @param  string|null $date
     * @param  string $format
     *
     * @return string|null
     */
    function format_date($date = '', string $format = "Y-m-d H:i")
    {
        if ($strtotime = strtotime($date)) {
            return date($format, $strtotime);
        }

        return null;
    }
}

if (! function_exists('format_unix_date')) {
    /**
     * Format the given unix date.
     *
     * @param  int $date
     * @param  string $format
     *
     * @return int
     */
    function format_unix_date(int $date, string $format = "Y-m-d", $toString = false)
    {
        $newDate = date($format, $date);

        return $toString ? $newDate : strtotime($newDate);
    }
}


if (! function_exists('is_date_valid')) {
    /**
     * Validate if the given date string is valid.
     *
     * @param  string $dateInput
     *
     * @return bool
     */
    function is_date_valid(string $dateInput)
    {
        $format = "Y-m-d";
        $date = DateTime::createFromFormat($format, $dateInput);

        return $date && $date->format($format) === $dateInput;
    }
}

if (! function_exists('get_next_month_date')) {
    /**
     * Modify the given unix time.
     *
     * @param  int $unixTime
     *
     * @return int
     */
    function get_next_month_date(int $unixTime, $originalDay)
    {
        $year = date('Y', $unixTime);
        $month = date('m', $unixTime);

        $newMonth = sprintf('%02d', $month + 1);;

        if ($newMonth > 12) {
            $year = $year + 1;
            $newMonth = "01";
        }

        $newDate = "{$year}-{$newMonth}";

        return checkdate($newMonth, $originalDay, $year)
            ? strtotime($newDate."-{$originalDay}")
            : strtotime(date('Y-m-t', strtotime($newDate)));

    }
}
