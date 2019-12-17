<?php

use Illuminate\Database\Seeder;

class UserClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('user_classes')->truncate();
        DB::table('user_classes')->insert([
            [
                'id' => '1',
                'user_id' => '1',
                'unit_class_id' => '1',
            ],
            [
                'id' => '2',
                'user_id' => '2',
                'unit_class_id' => '1',
            ],
            [
                'id' => '3',
                'user_id' => '3',
                'unit_class_id' => '1',
            ],
            [
                'id' => '4',
                'user_id' => '4',
                'unit_class_id' => '1',
            ],
            [
                'id' => '5',
                'user_id' => '5',
                'unit_class_id' => '2',
            ],
        ]);
    }
}
