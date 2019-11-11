<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Spatie\Permission\Models\Role::create(['name' => \App\Constants\Constant::ADMIN]);
        \Spatie\Permission\Models\Role::create(['name' => \App\Constants\Constant::CUSTOMER]);
        \Spatie\Permission\Models\Role::create(['name' => \App\Constants\Constant::SERVICE_PROVIDER]);
    }
}
