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
            [
                'id' => '4',
                'room' => '14',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '5',
                'room' => '105',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '6',
                'room' => '106',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '7',
                'room' => '107',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '8',
                'room' => '108',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '9',
                'room' => '109',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '10',
                'room' => '110',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '11',
                'room' => '201',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '12',
                'room' => '202',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '13',
                'room' => '203',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '14',
                'room' => '204',
                'address' => 'G2',
                'slot' => '40',
            ],
            [
                'id' => '15',
                'room' => '205',
                'address' => 'G2',
                'slot' => '40',
            ],
        ]);
    }
}
