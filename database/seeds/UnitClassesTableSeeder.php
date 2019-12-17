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
                'class_code' => 'INT3301_1',
                'lecturer' => $faker->name,
            ],
            [
                'id' => '2',
                'subject' => 'Lập trình hướng đối tượng',
                'class_code' => 'INT3302_1',
                'lecturer' => $faker->name,
            ],
            [
                'id' => '3',
                'subject' => 'Cơ sở dữ liệu',
                'class_code' => 'INT3303_1',
                'lecturer' => $faker->name,
            ],
        ]);
    }
}
