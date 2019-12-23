<?php

use Illuminate\Database\Seeder;

class SemestersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('semesters')->truncate();
        DB::table('semesters')->insert([
            [
                'id' => '1',
                'name' => 'Học kỳ 1 2019-2020',
                'start_time' => '2019-12-12',
                'end_time' => '2019-12-24',
                'is_register' => 1,
            ],
            [
                'id' => '2',
                'name' => 'Học kỳ 2 2019-2020',
                'start_time' => '2020-5-12',
                'end_time' => '2020-5-24',
                'is_register' => 0,
            ],
        ]);
    }
}
