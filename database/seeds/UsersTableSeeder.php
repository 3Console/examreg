<?php

use Illuminate\Database\Seeder;
use App\Consts;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        $this->createBots();
    }

    private function createBots() { 
        $password = bcrypt('123123');

        $botCount = 10;
        for ($i = 1; $i < $botCount + 1; $i++) {
            $faker = Faker::create();
            $email = "bot$i@gmail.com";
            DB::table('users')->insert([
                'id'                => $i,
                'email'             => $email,
                'password'          => $password,
                'msv'               => "1602000{$i}",
                'username'          => "bot{$i}",
                'full_name'         => $faker->name,
                'email_verified'    => Consts::TRUE,
                'sex'               => rand(0, 1),
                'dob'               => $faker->dateTime(),
                'course'            => 'QH2016-I',
                'unit'              => 'UET',
                'remember_token'    => str_random(10),
            ]);
        }
    }
}
