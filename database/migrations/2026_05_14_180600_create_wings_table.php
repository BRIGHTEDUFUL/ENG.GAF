<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('code', 20)->unique();
            $table->string('base_location', 255);
            $table->unsignedBigInteger('commander_id')->nullable();
            $table->string('status', 20)->default('active'); // active|inactive
            $table->date('established_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('commander_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wings');
    }
};
