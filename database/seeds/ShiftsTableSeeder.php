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
                'end_time' => '11:30',
            ],
            [
                'id' => '2',
                'start_time' => '14:00',
                'end_time' => '17:30',
            ],
        ]);
    }
}
