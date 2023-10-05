<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $invitees = $this->users()->pluck('user_id') ?? [];

        return [
            "eventName" => $this->event_name,
            "frequency" => $this->frequency->name,
            "startDateTime" => format_date($this->start_date_time),
            "endDateTime" => format_date($this->end_date_time),
            "duration" => $this->duration,
            "invitees" => $invitees
        ];
    }
}
