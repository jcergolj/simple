<?php

namespace App\Services;

use App\ValueObjects\Money;
use Illuminate\Support\Collection;

class WeeklyEarningsCalculator
{
    public static function calculate(Collection $weeklyEntries): array
    {
        // Calculate earnings grouped by currency
        $earningsByCurrency = [];
        foreach ($weeklyEntries as $entry) {
            $earnings = $entry->calculateEarnings();
            if ($earnings) {
                $currencyCode = $earnings->currency->value;
                if (! isset($earningsByCurrency[$currencyCode])) {
                    $earningsByCurrency[$currencyCode] = [
                        'amount' => 0,
                        'currency' => $earnings->currency,
                    ];
                }
                $earningsByCurrency[$currencyCode]['amount'] += $earnings->amount;
            }
        }

        // Sort by amount and get top 5
        uasort($earningsByCurrency, fn ($a, $b) => $b['amount'] <=> $a['amount']);
        $topEarnings = array_slice($earningsByCurrency, 0, 5);

        // Convert to Money objects
        $weeklyEarnings = array_map(
            fn ($earning) => new Money($earning['amount'], $earning['currency']),
            $topEarnings
        );

        // Calculate total amount (in cents)
        $totalAmount = array_sum(array_column($earningsByCurrency, 'amount'));

        return [
            'weeklyEarnings' => $weeklyEarnings,
            'totalAmount' => $totalAmount,
        ];
    }
}
