<?php

namespace App\Enum;

class ProcessResponse
{
    const SERVER_ERROR = 'An internal server error has occurred.';
    const AUTH_FAILED = 'The provided credentials do not match our records.';
    const TOKEN_INVALID = 'Invalid token.';
    const TOKEN_EXPIRED = 'Token expired.';
    const TOKEN_GENERATED = 'Public token successfully generated.';

    CONST EVENT_CREATE_SUCCESS = 'Event created successfully!';
    CONST EVENT_CREATE_FAILED = 'Failed to create an event.';
    CONST EVENT_UPDATE_SUCCESS = 'Event updated successfully.';
    CONST EVENT_UPDATE_FAILED = 'Failed to update event.';
    CONST EVENT_FETCH_FAILED = 'Failed to fetch events.';
    CONST EVENT_NOT_FOUND = 'Event does not exists.';
}
