<?php

use Illuminate\Database\Seeder;

class SemesterClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('semester_classes')->truncate();
        DB::table('semester_classes')->insert([
            [
                'id' => '1',
                'semester_id' => '1',
                'unit_class_id' => '1',
            ],
            [
                'id' => '2',
                'semester_id' => '1',
                'unit_class_id' => '2',
            ],
            [
                'id' => '3',
                'semester_id' => '1',
                'unit_class_id' => '3',
            ],
        ]);
    }
}
