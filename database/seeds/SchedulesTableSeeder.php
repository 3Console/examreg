<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('schedules')->truncate();

        $faker = Faker::create();

        DB::table('schedules')->insert([
            [
                'id' => '1',
                'unit_class_id' => '1',
                'shift_id' => '1',
                'location_id' => '1',
                'date' => $faker->dateTime(),
            ],
            [
                'id' => '2',
                'unit_class_id' => '1',
                'shift_id' => '1',
                'location_id' => '2',
                'date' => $faker->dateTime(),
            ],
            [
                'id' => '3',
                'unit_class_id' => '2',
                'shift_id' => '2',
                'location_id' => '3',
                'date' => $faker->dateTime(),
            ],
        ]);
    }
}
