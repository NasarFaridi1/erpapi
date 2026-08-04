<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::updateOrCreate(['name' => 'Single Job'], [
            'price' => 10.00,
            'duration' => 'single',
            'job_credits' => 1,
        ]);

        Package::updateOrCreate(['name' => 'Monthly'], [
            'price' => 0, // set your price
            'duration' => 'monthly',
            'job_credits' => null, // unlimited
        ]);

        Package::updateOrCreate(['name' => 'Yearly'], [
            'price' => 0, // set your price
            'duration' => 'yearly',
            'job_credits' => null, // unlimited
        ]);
    }
}
