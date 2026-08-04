<?php

namespace App\Services;

use App\Models\CompanyPackage;
use App\Models\Package;
use Carbon\Carbon;

class PackageService
{
    public function getPeriodAndCredits(string $duration): array
    {
        $start = Carbon::today();

        switch ($duration) {
            case 'single':
                return [
                    'start_date' => $start,
                    'end_date' => $start, // same day window
                    'remaining_credits' => 1, // one job post
                ];
            case 'monthly':
                return [
                    'start_date' => $start,
                    'end_date' => $start->copy()->addMonth()->subDay(),
                    'remaining_credits' => 20, // unlimited
                ];
            case 'yearly':
                return [
                    'start_date' => $start,
                    'end_date' => $start->copy()->addYear()->subDay(),
                    'remaining_credits' => 30, // unlimited
                ];
            default:
                throw new \InvalidArgumentException('Invalid package duration');
        }
    }

    

    public function latestActiveForCompany(int $companyId): ?CompanyPackage
    {
        return CompanyPackage::with('package')
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->latest('id')
                ->first();
    }
}
