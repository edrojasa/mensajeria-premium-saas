<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->after('email');
        });

        Schema::table('organization_user', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
        });

        DB::table('organization_user')->where('role', 'owner')->update(['role' => 'admin']);
        DB::table('organization_user')->where('role', 'member')->update(['role' => 'operador']);
    }

    public function down(): void
    {
        DB::table('organization_user')->where('role', 'admin')->update(['role' => 'owner']);
        DB::table('organization_user')->where('role', 'operador')->update(['role' => 'member']);

        Schema::table('organization_user', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
