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
        Schema::create('places', function (Blueprint $table) {
            $table->id();

            //General info
            $table->string('name');
            $table->text('description')->nullable();

            //Specific info
            $table->string('schedule')->nullable();
            $table->integer('capacity')->nullable();
            $table->decimal('cost', 10, 2)->nullable();

            //Location coordinates
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->foreignId('category_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
