<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->index();
            $table->unsignedInteger('external_id')->nullable()->comment('ID del dataset fuente (ej. DIVIPOLA)');
            $table->string('name');
            $table->timestamps();

            $table->unique(['country_code', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
