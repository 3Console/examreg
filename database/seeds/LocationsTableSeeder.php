<?php

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('locations')->truncate();
        DB::table('locations')->insert([
            [
                'id' => '1',
                'room' => '101',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '2',
                'room' => '102',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '3',
                'room' => '103',
                'address' => 'G2',
                'slot' => '40',
            ],
        ]);
    }
}
