<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('customers', 'customer_code')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('customer_code', 32)->nullable()->after('organization_id');
            });
        }

        if (! Schema::hasTable('service_rates')) {
            Schema::create('service_rates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->string('service_type', 32);
                $table->decimal('base_price', 14, 2)->default(0);
                $table->decimal('price_per_kg', 14, 4)->nullable();
                $table->decimal('price_per_km', 14, 4)->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();

                $table->unique(['organization_id', 'service_type']);
                $table->index(['organization_id', 'active']);
            });
        }

        if (! Schema::hasColumn('shipments', 'cost')) {
            Schema::table('shipments', function (Blueprint $table) {
                $table->string('service_type', 32)->nullable()->after('declared_value');
                $table->decimal('distance_km', 12, 3)->nullable()->after('service_type');
                $table->decimal('cost', 14, 2)->nullable()->after('distance_km');
                $table->string('payment_type', 24)->nullable()->after('cost');
                $table->string('payment_status', 24)->nullable()->after('payment_type');
                $table->decimal('paid_amount', 14, 2)->nullable()->after('payment_status');
                $table->date('payment_date')->nullable()->after('paid_amount');
            });
        }

        DB::table('shipments')->whereNull('service_type')->update([
            'service_type' => 'standard',
            'payment_type' => 'credit',
            'payment_status' => 'pending',
        ]);

        DB::table('shipments')->whereNull('payment_type')->update(['payment_type' => 'credit']);
        DB::table('shipments')->whereNull('payment_status')->update(['payment_status' => 'pending']);

        // Códigos de cliente secuenciales por organización (solo filas sin código)
        $organizationIds = DB::table('customers')->distinct()->pluck('organization_id');
        foreach ($organizationIds as $orgId) {
            $rows = DB::table('customers')
                ->where('organization_id', $orgId)
                ->whereNull('customer_code')
                ->orderBy('id')
                ->get(['id']);

            $existingMax = DB::table('customers')
                ->where('organization_id', $orgId)
                ->whereNotNull('customer_code')
                ->where('customer_code', 'like', 'CL-%')
                ->orderByDesc('customer_code')
                ->value('customer_code');

            $n = 1;
            if ($existingMax !== null && preg_match('/CL-(\d+)/', (string) $existingMax, $m)) {
                $n = (int) $m[1] + 1;
            }

            foreach ($rows as $row) {
                DB::table('customers')->where('id', $row->id)->update([
                    'customer_code' => 'CL-'.str_pad((string) $n++, 6, '0', STR_PAD_LEFT),
                ]);
            }
        }

        try {
            Schema::table('customers', function (Blueprint $table) {
                $table->unique(['organization_id', 'customer_code']);
            });
        } catch (\Throwable) {
            // Índice ya presente (ej. migración interrumpida antes).
        }

        foreach (['Rojas Tech', 'RojasTech', 'ROJAS TECH'] as $legacyName) {
            DB::table('organizations')->where('name', $legacyName)->update([
                'name' => 'Mensajería Premium',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'customer_code']);
            $table->dropColumn('customer_code');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn([
                'service_type',
                'distance_km',
                'cost',
                'payment_type',
                'payment_status',
                'paid_amount',
                'payment_date',
            ]);
        });

        Schema::dropIfExists('service_rates');
    }
};
