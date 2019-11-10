<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'phone_no' => '09097694139',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@expansetech.com',
            'password' => bcrypt('p@ssword'),
        ]);
    }
}
