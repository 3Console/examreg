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
            [
                'id' => '4',
                'subject' => 'Giải tích 1',
            ],
            [
                'id' => '5',
                'subject' => 'Trí tuệ nhân tạo',
            ],
            [
                'id' => '6',
                'subject' => 'Cấu trúc dữ liệu giải thuật',
            ],
            [
                'id' => '7',
                'subject' => 'Mạng máy tính',
            ],
            [
                'id' => '8',
                'subject' => 'Lập trình nâng cao',
            ],
            [
                'id' => '9',
                'subject' => 'Quản lý dự án phần mềm',
            ],
            [
                'id' => '10',
                'subject' => 'Kho dữ liệu',
            ],
        ]);
    }
}
