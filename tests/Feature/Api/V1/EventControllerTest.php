<?php

namespace Tests\Feature\Api\V1;

use App\Enum\Frequency as EnumFrequency;
use App\Models\Event;
use App\Models\Frequency;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * The user that will be
     * use for this testing.
     *
     * @var User
     */
    private $apiUser;

    /**
     * The api endpoint that will
     * be use for this testing.
     *
     * @var string
     */
    private $apiEndpoint;

    /**
     * Setup the variables that
     * is necessary for this testing.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->apiEndpoint = '/api/v1/events';
        $this->apiUser = User::factory()->create();

        foreach (EnumFrequency::DEFINITION_LIST as $definition) {
            Frequency::factory()
                ->withName($definition['name'])
                ->create();
        }
    }

    /**
     * Test if unauthenticated user cannot fetch a collection of event.
     *
     * @return void
     */
    public function test_unauthenticated_api_user_cannot_fetch_all_events()
    {
        $response = $this->getJson($this->apiEndpoint);
        $response->assertUnauthorized();
    }

    /**
     * Test if unauthenticated user cannot create an event.
     *
     * @return void
     */
    public function test_unauthenticated_api_user_cannot_create_an_event()
    {
        $payload = [
            'eventName' => $this->faker->name(),
            'frequency' => EnumFrequency::ONCE_OFF_NAME,
            'startDateTime' => $this->faker->dateTime()->format('Y-m-d H:i'),
            'endDateTime' => $this->faker->dateTime()->format('Y-m-d H:i'),
            'duration' => $this->faker->numberBetween(0, 60)
        ];

        $response = $this->postJson($this->apiEndpoint, $payload);
        $response->assertUnauthorized();
    }

    /**
     * Test if unauthenticated user cannot fetch an event.
     *
     * @return void
     */
    public function test_unauthenticated_api_user_cannot_fetch_an_event()
    {
        $event = Event::factory()->create();

        $response = $this->getJson($this->apiEndpoint. '/' .$event->id);
        $response->assertUnauthorized();
    }

    /**
     * Test if unauthenticated user cannot update an event.
     *
     * @return void
     */
    public function test_unauthenticated_api_user_cannot_update_an_event()
    {
        $event = Event::factory()->create();

        $payload = [
            'eventName' => $this->faker->name(),
            'frequency' => EnumFrequency::ONCE_OFF_NAME,
            'startDateTime' => $this->faker->dateTime()->format('Y-m-d H:i'),
            'endDateTime' => $this->faker->dateTime()->format('Y-m-d H:i'),
            'duration' => $this->faker->numberBetween(0, 60)
        ];

        $response = $this->putJson($this->apiEndpoint. '/' .$event->id, $payload);
        $response->assertUnauthorized();
    }

    /**
     * Test if unauthenticated user cannot patch an event.
     *
     * @return void
     */
    public function test_unauthenticated_api_user_cannot_patch_event()
    {
        $event = Event::factory()->create();

        $eventName = $this->faker->name();

        $payload = [
            'eventName' => $eventName,
        ];

        $response = $this->patchJson($this->apiEndpoint. '/' .$event->id, $payload);
        $response->assertUnauthorized();
    }

    /**
     * Test if unauthenticated user cannot delete an event.
     *
     * @return void
     */
    public function test_unauthenticated_api_user_cannot_delete_an_event()
    {
        $event = Event::factory()->create();

        $response = $this->deleteJson($this->apiEndpoint. '/' .$event->id);
        $response->assertUnauthorized();
    }

    /**
     * Test if authenticated user can fetch a collection of event.
     *
     * @return void
     */
    public function test_authenticated_api_user_can_fetch_can_fetch_all_events()
    {
        Sanctum::actingAs($this->apiUser);

        Event::factory(5)
            ->withFrequencyId(EnumFrequency::ONCE_OFF_ID)
            ->create();

        $response = $this->getJson($this->apiEndpoint);
        $response->assertOk();
    }

    /**
     * Test if authenticated user can create an event.
     *
     * @return void
     */
    public function test_authenticated_api_user_can_create_an_event()
    {
        Sanctum::actingAs($this->apiUser);
        $user = User::factory()->create();

        $eventName = $this->faker->name();
        $frequency = EnumFrequency::ONCE_OFF_NAME;
        $startDateTime = '2023-01-01 01:00';
        $endDateTime = null;
        $duration = $this->faker->numberBetween(0, 60);
        $invitees = [$user->id];

        $payload = [
            'eventName' => $eventName,
            'frequency' => $frequency,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'duration' => $duration,
            'invitees' => $invitees
        ];

        $response = $this->postJson($this->apiEndpoint, $payload);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'eventName',
                'frequency',
                'startDateTime',
                'endDateTime',
                'duration',
                'invitees'
            ],
            'message'
        ]);

        $this->assertDatabaseHas('events', [
            'event_name' => $eventName,
            'frequency_id' => EnumFrequency::ONCE_OFF_ID,
            'start_date_time' => $startDateTime,
            'end_date_time' => $endDateTime,
            'duration' => $duration
        ])->assertDatabaseHas('event_user', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Test if authenticated user cannot create an event
     * if new schedule is conflicting with other event(s) schedule.
     *
     * @return void
     */
    public function test_authenticated_api_user_cannot_create_an_even_if_its_conflicting()
    {
        Sanctum::actingAs($this->apiUser);

        $event = Event::factory()
            ->withStartDateTime('2023-09-23 10:00')
            ->withDuration(120)
            ->withFrequencyId(EnumFrequency::WEEKLY_ID)
            ->has(User::factory())
            ->create();

        $eventName = $this->faker->name();
        $frequency = EnumFrequency::ONCE_OFF_NAME;
        $startDateTime = '2023-09-30 11:00';
        $invitees = $event->users()->pluck('user_id')->all();

        $payload = [
            'eventName' => $eventName,
            'frequency' => $frequency,
            'startDateTime' => $startDateTime,
            'invitees' => $invitees
        ];

        $response = $this->postJson($this->apiEndpoint, $payload);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

     /**
     * Test if authenticated user can fetch an event.
     *
     * @return void
     */
    public function test_authenticated_api_user_can_fetch_an_event()
    {
        Sanctum::actingAs($this->apiUser);

        $event = Event::factory()->create();

        $response = $this->getJson($this->apiEndpoint. '/' .$event->id);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'eventName',
                'frequency',
                'startDateTime',
                'endDateTime',
                'duration',
                'invitees'
            ]
        ]);
    }

    /**
     * Test if authenticated user can update an event.
     *
     * @return void
     */
    public function test_authenticated_api_user_can_update_an_event()
    {
        Sanctum::actingAs($this->apiUser);

        $user = User::factory()->create();
        $event = Event::factory()->create();

        $eventName = $this->faker->name();
        $frequency = EnumFrequency::ONCE_OFF_NAME;
        $startDateTime = '2023-10-30 11:00';
        $endDateTime = null;
        $duration = $this->faker->numberBetween(0, 60);
        $invitees = [$user->id];

        $payload = [
            'eventName' => $eventName,
            'frequency' => $frequency,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'duration' => $duration,
            'invitees' => $invitees
        ];

        $response = $this->putJson($this->apiEndpoint. '/' .$event->id, $payload);
        $response->assertOk();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'event_name' => $eventName,
            'frequency_id' => EnumFrequency::ONCE_OFF_ID,
            'start_date_time' => $startDateTime,
            'end_date_time' => $endDateTime,
            'duration' => $duration
        ])->assertDatabaseHas('event_user', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Test if authenticated user can patch an event.
     *
     * @return void
     */
    public function test_authenticated_api_user_can_patch_event()
    {
        Sanctum::actingAs($this->apiUser);

        $event = Event::factory()->create();

        $eventName = $this->faker->name();

        $payload = [
            'eventName' => $eventName,
        ];

        $response = $this->patchJson($this->apiEndpoint. '/' .$event->id, $payload);
        $response->assertOk();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'event_name' => $eventName,
        ]);
    }

    /**
     * Test if authenticated user can delete an event.
     *
     * @return void
     */
    public function test_authenticated_api_user_can_delete_an_event()
    {
        Sanctum::actingAs($this->apiUser);
        $event = Event::factory()->create();


        $response = $this->deleteJson($this->apiEndpoint. '/' .$event->id);
        $response->assertOk();

        $this->assertDatabaseMissing('events', [
            'id' => $event->id
        ]);
    }
}
