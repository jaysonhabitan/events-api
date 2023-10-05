<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\BelongsToRelationship;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventsFilter extends ApiFilter
{
    /**
     * The expected query params
     * that corresponds to a operator(s).
     */
    protected $safeParams = [
        'from' => ['gte'],
        'to' => ['lte'],
        'duration' => ['eq'],
        'invitees' => ['eq']
    ];

    /**
     * The expected query params
     * that corresponds to a table column.
     */
    protected $columnMap = [
        'from' => 'start_date_time',
        'to' => 'end_date_time',
        'duration' => 'duration'
    ];

    // TODO: Add more operators if needed.
    /**
     * The expected query operator characters
     * that corresponds to an operator symbol.
     */
    protected $operatorMap = [
        'eq' => '=',
        'gte' => '>=',
        'lte' => '<='
    ];

    /**
     * The expected query parameter(s)
     * that corresponds to a model.
     */
    protected $relationships = [
        'users' => 'BelongToMany',
    ];
}
