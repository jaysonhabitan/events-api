<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
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
                DB::table('users')->insert([
                    [
                        'name' => 'John Doe',
                        'email' => 'api_user1@test.com',
                        'password' => Hash::make('pass1234'),
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'name' => 'John Doe',
                        'email' => 'api_user2@test.com',
                        'password' => Hash::make('pass1234'),
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'name' => 'John Doe',
                        'email' => 'api_user3@test.com',
                        'password' => Hash::make('pass1234'),
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'name' => 'John Doe',
                        'email' => 'api_user4@test.com',
                        'password' => Hash::make('pass1234'),
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'name' => 'John Doe',
                        'email' => 'api_user5@test.com',
                        'password' => Hash::make('pass1234'),
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ],
                ]);
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
