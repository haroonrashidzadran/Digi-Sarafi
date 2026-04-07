<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('city', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('trust_level', ['low', 'medium', 'high', 'trusted'])->default('medium');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('trust_level');
            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};