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
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->integer('user');
            $table->string('name');
            $table->datetime('dateTime');
            $table->date('dateFull');
            $table->string('day_index');
            $table->string('day');
            $table->string('month');
            $table->string('time');
            $table->integer('slot');
            $table->boolean('active')->default(1);
            $table->string('owner')->nullable();
            $table->string('owner_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
