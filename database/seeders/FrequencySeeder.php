<?php

namespace Database\Seeders;

use App\Enum\Frequency as FrequencyEnum;
use App\Models\Frequency as Frequency;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $frequency = new Frequency();

        DB::transaction(function () use ($frequency) {
            try {
                foreach (FrequencyEnum::DEFINITION_LIST as $definition) {
                    $frequency->create($definition);
                }
            } catch (Exception $e) {
                DB::rollBack();

                Log::error(
                    "{$e->getMessage()}:
                    {$e->getFile()}:
                    {$e->getLine()}"
                );
            }
        });
    }
}
