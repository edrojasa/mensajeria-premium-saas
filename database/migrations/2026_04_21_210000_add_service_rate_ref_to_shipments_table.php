<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('shipments', 'service_rate_id')) {
            Schema::table('shipments', function (Blueprint $table): void {
                $table->foreignId('service_rate_id')
                    ->nullable()
                    ->after('cost')
                    ->constrained('service_rates')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('shipments', 'service_rate_id')) {
            Schema::table('shipments', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('service_rate_id');
            });
        }
    }
};
