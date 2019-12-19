<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UnitClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('unit_classes')->truncate();

        $faker = Faker::create();

        DB::table('unit_classes')->insert([
            [
                'id' => '1',
                'subject' => 'Phát triển ứng dụng web',
            ],
            [
                'id' => '2',
                'subject' => 'Lập trình hướng đối tượng',
            ],
            [
                'id' => '3',
                'subject' => 'Cơ sở dữ liệu',
            ],
        ]);
    }
}
