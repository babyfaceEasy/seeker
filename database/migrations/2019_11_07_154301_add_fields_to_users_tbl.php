<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', [\App\Constants\Status::ENABLED, \App\Constants\Status::DISABLED])
                ->after('password')
                ->default(\App\Constants\Status::DISABLED);
            $table->boolean('email_confirmed')->after('status')->default(false);
            $table->boolean('phone_no_confirmed')->after('email_confirmed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'email_confirmed', 'phone_no_confirmed']);
        });
    }
}
