<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Compatibilidad: ejecuta el mismo flujo que DatabaseSeeder (departamentos + ciudades).
 *
 * @deprecated Preferir php artisan db:seed o DepartmentSeeder + CitySeeder.
 */
class ColombiaGeoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            CitySeeder::class,
        ]);
    }
}
