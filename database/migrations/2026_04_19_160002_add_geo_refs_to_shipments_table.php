<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('origin_department_id')->nullable()->after('destination_postal_code')->constrained('departments')->nullOnDelete();
            $table->foreignId('origin_city_id')->nullable()->after('origin_department_id')->constrained('cities')->nullOnDelete();
            $table->foreignId('destination_department_id')->nullable()->after('origin_city_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('destination_city_id')->nullable()->after('destination_department_id')->constrained('cities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['origin_department_id']);
            $table->dropForeign(['origin_city_id']);
            $table->dropForeign(['destination_department_id']);
            $table->dropForeign(['destination_city_id']);
        });
    }
};
