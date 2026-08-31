<?php

namespace App\Libraries;

class ExpenseEstimator
{
    /**
     * Indicative monthly household costs for living in Italy.
     *
     * @return array<string, float>
     */
    public static function estimate(int $adults, int $children, int $cars): array
    {
        $adults = max(1, min(2, $adults));
        $children = max(0, min(6, $children));
        $cars = max(0, min(2, $cars));
        $extraAdults = $adults - 1;
        $otherPeople = $extraAdults + $children;

        return [
            'energy' => round(90 + 40 * $extraAdults + 20 * $children, 2),
            'water' => round(20 + 8 * $otherPeople, 2),
            'internet' => 30.0,
            'health_insurance' => round(150 * $adults + 50 * $children, 2),
            'car_insurance' => round(70 * $cars, 2),
            'car_fuel' => round(120 * $cars, 2),
            'car_maintenance' => round(40 * $cars, 2),
            'groceries' => round(250 + 180 * $extraAdults + 120 * $children, 2),
            'leisure' => round(80 + 40 * $extraAdults + 30 * $children, 2),
            'unforeseen' => round(50 + 20 * $otherPeople, 2),
            'other' => 0.0,
        ];
    }

    /**
     * @return list<string>
     */
    public static function fields(): array
    {
        return [
            'energy', 'water', 'internet', 'health_insurance',
            'car_insurance', 'car_fuel', 'car_maintenance',
            'groceries', 'leisure', 'unforeseen', 'other',
        ];
    }
}
