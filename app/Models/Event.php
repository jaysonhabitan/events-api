<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The table that corresponds to the model
     */
    protected $table = 'events';

    /**
     * Will automatically set the value for created_at
     * and updated_at columns when set to true.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_name',
        'frequency_id',
        'start_date_time',
        'end_date_time',
        'duration'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date_time' => 'datetime:Y-m-d H:i',
        'end_date_time' => 'datetime:Y-m-d H:i',
    ];

    /**
     * The users that belong to the event.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the frequency that the event belongs to.
     */
    public function frequency(): BelongsTo
    {
        return $this->belongsTo(Frequency::class);
    }
}
