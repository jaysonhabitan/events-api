<?php

namespace App\Services\V1;

use App\Enum\ProcessResponse;
use App\Filters\V1\EventsFilter;
use App\Http\Resources\V1\EventResource;
use App\Http\Resources\V1\EventResourceCollection;
use App\Tools\EndProcess;
use App\Models\Event;
use App\Models\Frequency;
use Exception;
use Illuminate\Http\Request;

/**
 * EventService
 *
 * Contains functions mainly for event resource.
 */
class EventService
{
    /**
     * Create an event data.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return array
     */
    public function createEvent(Request $request): array
    {
        $frequency = Frequency::where('name', $request->frequency)->firstOrFail();
        $request->merge(['frequency_id' => $frequency->id]);

        $event = Event::create($request->except(['frequency', 'invitees']));

        if ($event) {
            $event->users()->sync($request->invitees ?? []);

            return EndProcess::success([
                'data' => new EventResource($event),
                'message' => ProcessResponse::EVENT_CREATE_SUCCESS
            ]);
        }

        return EndProcess::failed([
            'message' => ProcessResponse::EVENT_CREATE_FAILED
        ]);
    }

    /**
     * Update an event data.
     *
     * @param App\Models\Event $event
     * @param Illuminate\Http\Request $request
     *
     * @return array
     */
    public function updateEvent(Event $event, Request $request): array
    {
        if ($request->has('frequency')) {
            $frequency = Frequency::where('name', $request->frequency)->firstOrFail();
            $request->merge(['frequency_id' => $frequency->id]);
        }

        if ($event->update($request->except(['frequency', 'invitees']))) {
            if ($request->has('invitees')) {
               if (count($request->invitees) > 0 ){
                    $event->users()->sync($request->invitees);
               } else {
                    $event->users()->detach();
               }
            }

            return EndProcess::success([
                'message' => ProcessResponse::EVENT_UPDATE_SUCCESS
            ]);
        }

        return EndProcess::failed([
            'message' => ProcessResponse::EVENT_CREATE_FAILED
        ]);
    }

    /**
     * Fetch all the event instances.
     *
     * @param  Illuminate\Http\Request;
     *
     */
    public function fetchEvents(Request $request)
    {
        $filter = new EventsFilter();
        $queryItems = $filter->transform($request);

        $events = $filter->queryModel(new Event(), $queryItems);

        if (!$events) {
            return EndProcess::failed([
                'message' => ProcessResponse::EVENT_FETCH_FAILED
            ]);
        }

        return EndProcess::success([
            'items' => new EventResourceCollection($events),
        ]);
    }

    /**
     * Delete event data from storage.
     *
     * @param App\Models\Event
     *
     * @return array
     */
    public function deleteEvent(Event $event): array
    {
        if (!$event->delete())
            return EndProcess::failed();

        return EndProcess::success();
    }
}
