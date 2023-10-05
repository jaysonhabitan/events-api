<?php

namespace App\Http\Controllers\Api\V1;

use App\Enum\ProcessResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateEventRequest;
use App\Http\Requests\V1\UpdateEventRequest;
use App\Http\Resources\V1\EventResource;
use App\Http\Resources\V1\EventResourceCollection;
use App\Models\Event;
use App\Services\V1\EventService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{

    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * Create a new controller instance.
     *
     * @param  EventService  $eventService
     *
     * @return void
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $result = $this->eventService->fetchEvents($request);

            if (!$result['success']) {
                return response()->json(
                    $result['data'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return response()->json(
                $result['data'],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
             // TODO: Add a proper logging channel
             Log::error(
                "{$e->getMessage()}:
                 {$e->getFile()}:
                 {$e->getLine()}"
            );

            return response()->json([
                'message' => ProcessResponse::SERVER_ERROR],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateEventRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateEventRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $result = $this->eventService->createEvent($request);

                if (!$result['success']) {
                    DB::rollBack();

                    return response()->json(
                        $result['data'],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                return response()->json(
                    $result['data'],
                    Response::HTTP_OK
                );
            });
        } catch (Exception $e) {
            // TODO: Add a proper logging channel
            Log::error(
                "{$e->getMessage()}:
                 {$e->getFile()}:
                 {$e->getLine()}"
            );

            return response()->json([
                'message' => ProcessResponse::SERVER_ERROR],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateEventRequest $request
     * @param  Event $event
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            return DB::transaction(function() use ($request, $event) {
                $result = $this->eventService->updateEvent($event, $request);

                if (!$result['success']) {
                    DB::rollBack();

                    return response()->json(
                        $result['data'],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                return response()->json(
                    $result['data'],
                    Response::HTTP_OK
                );
            });
        } catch (Exception $e) {
            // TODO: Add a proper logging channel
            Log::error(
                "{$e->getMessage()}:
                 {$e->getFile()}:
                 {$e->getLine()}"
            );

            return response()->json([
                'message' => ProcessResponse::SERVER_ERROR],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Event  $event
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Event $event)
    {
        try {
            return DB::transaction(function() use ($event) {
                $result = $this->eventService->deleteEvent($event);

                if (!$result['success']) {
                    DB::rollBack();

                    return response()->json(
                        [],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                return response()->json();
            });
        } catch (Exception $e) {
              // TODO: Add a proper logging channel
              Log::error(
                "{$e->getMessage()}:
                 {$e->getFile()}:
                 {$e->getLine()}"
            );

            return response()->json([
                'message' => ProcessResponse::SERVER_ERROR],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
