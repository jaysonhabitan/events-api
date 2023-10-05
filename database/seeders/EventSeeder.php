<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::transaction(function() {
                $userIds = User::all()->pluck('id');
                for ($i=0; $i <= 10 ; $i++) {
                    $event = Event::factory()->create();
                    $event->users()->sync($userIds->random(3));
                }
            });
        } catch (Exception $e) {
            Log::error(
                "{$e->getMessage()}:
                {$e->getFile()}:
                {$e->getLine()}"
            );

            DB::rollBack();
        }
    }
}
