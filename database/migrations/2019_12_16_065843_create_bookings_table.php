<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('service_provider_id')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->text('location');
            $table->dateTime('offer_on');
            $table->enum('status', [
                \App\Constants\Status::OPEN,
                \App\Constants\Status::ASSIGNED,
                \App\Constants\Status::EXECUTED,
                \App\Constants\Status::CLOSED
            ])->default(\App\Constants\Status::OPEN);
            $table->double('amount', 8, 2);
            $table->text('comment')->nullable();
            $table->timestamps();

            // foreign key
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table){
            $table->dropForeign(['customer_id', 'service_provider_id', 'service_id']);
        });
        Schema::dropIfExists('bookings');
    }
}
