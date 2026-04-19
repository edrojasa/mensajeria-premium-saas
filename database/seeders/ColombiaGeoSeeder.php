<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Department;
use Illuminate\Database\Seeder;

class ColombiaGeoSeeder extends Seeder
{
    public function run(): void
    {
        $base = database_path('data/colombia');

        $departmentsPath = $base.'/departments.json';
        $citiesPath = $base.'/cities.json';

        if (! is_file($departmentsPath) || ! is_file($citiesPath)) {
            $this->command?->warn('Colombia JSON files missing in database/data/colombia — skipping geo seed.');

            return;
        }

        $departmentsJson = json_decode((string) file_get_contents($departmentsPath), true);
        foreach ($departmentsJson['data'] ?? [] as $row) {
            Department::updateOrCreate(
                [
                    'country_code' => 'CO',
                    'external_id' => $row['id'],
                ],
                [
                    'name' => $row['name'],
                ]
            );
        }

        $citiesJson = json_decode((string) file_get_contents($citiesPath), true);
        foreach ($citiesJson['data'] ?? [] as $row) {
            $department = Department::query()
                ->where('country_code', 'CO')
                ->where('external_id', $row['departmentId'])
                ->first();

            if ($department === null) {
                continue;
            }

            City::updateOrCreate(
                [
                    'department_id' => $department->id,
                    'external_id' => $row['id'],
                ],
                [
                    'name' => $row['name'],
                ]
            );
        }
    }
}
