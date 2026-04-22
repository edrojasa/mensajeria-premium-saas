<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estado global de la cuenta (sesión / login). Distinto del pivot organization_user.is_active.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Valores válidos: activo | suspendido (MySQL ENUM equivalente vía Enum PHP)
            $table->string('status', 32)->default('activo')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
