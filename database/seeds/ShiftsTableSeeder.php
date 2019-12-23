<?php

use Illuminate\Database\Seeder;

class ShiftsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('shifts')->truncate();
        DB::table('shifts')->insert([
            [
                'id' => '1',
                'start_time' => '8:00',
                'end_time' => '8:50',
            ],
            [
                'id' => '2',
                'start_time' => '9:00',
                'end_time' => '9:50',
            ],
            [
                'id' => '3',
                'start_time' => '10:00',
                'end_time' => '10:50',
            ],
            [
                'id' => '4',
                'start_time' => '11:00',
                'end_time' => '11:50',
            ],
            [
                'id' => '5',
                'start_time' => '14:00',
                'end_time' => '14:50',
            ],
            [
                'id' => '6',
                'start_time' => '15:00',
                'end_time' => '15:50',
            ],
            [
                'id' => '7',
                'start_time' => '16:00',
                'end_time' => '16:50',
            ],
        ]);
    }
}
