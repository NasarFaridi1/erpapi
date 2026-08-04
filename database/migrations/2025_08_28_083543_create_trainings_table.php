<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('training_name');
            $table->text('details')->nullable();
            $table->string('profile')->nullable(); // category/type
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('max_students')->default(0);
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->dateTime('expiry_datetime');
            $table->string('location');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
