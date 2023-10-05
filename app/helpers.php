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
