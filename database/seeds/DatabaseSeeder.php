<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(OauthClientsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UnitClassesTableSeeder::class);
        $this->call(LocationsTableSeeder::class);
        $this->call(SchedulesTableSeeder::class);
        $this->call(SemestersTableSeeder::class);
        $this->call(ShiftsTableSeeder::class);
        $this->call(UserClassesTableSeeder::class);
        $this->call(SemesterClassesTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
    }
}
