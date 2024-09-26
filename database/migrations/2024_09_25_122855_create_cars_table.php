<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('cars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('brand_id')->constrained()->onDelete('restrict');
        $table->foreignId('model_id')->constrained('car_models')->onDelete('restrict');
        $table->foreignId('user_id')->constrained()->onDelete('restrict');
        $table->integer('year')->nullable();
        $table->integer('mileage')->nullable();
        $table->string('color')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
