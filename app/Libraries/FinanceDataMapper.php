<?php

namespace App\Libraries;

class FinanceDataMapper
{
    public static function pack(
        ?array $profile,
        ?array $startPosition,
        ?array $income,
        ?array $expenses,
        ?array $taxes,
        ?array $mainProperty,
        ?array $secondProperty,
        ?array $bnbSettings,
        ?array $bnbExpenses
    ): array {
        return [
            'profile' => self::profile($profile),
            'start_position' => self::start($startPosition),
            'income' => self::income($income),
            'expenses' => self::expenses($expenses),
            'taxes' => self::taxes($taxes),
            'main_property' => $mainProperty ? self::property($mainProperty) : null,
            'second_property' => $secondProperty ? self::property($secondProperty) : null,
            'bnb_settings' => self::bnbSettings($bnbSettings),
            'bnb_expenses' => self::bnbExpenses($bnbExpenses),
        ];
    }

    public static function profile(?array $row): array
    {
        $row = $row ?? [];

        return [
            'date_of_birth' => $row['date_of_birth'] ?? null,
            'partner_date_of_birth' => $row['partner_date_of_birth'] ?? null,
            'emigration_date' => $row['emigration_date'] ?? null,
            'retirement_age' => $row['retirement_age'] ?? 67,
            'partner_retirement_age' => $row['partner_retirement_age'] ?? 67,
            'voluntary_aow_years' => $row['voluntary_aow_years'] ?? 0,
            'partner_name' => $row['partner_name'] ?? 'partner',
        ];
    }

    public static function start(?array $row): array
    {
        $row = $row ?? [];

        return [
            'house_sale_price' => $row['house_sale_price'] ?? $row['house_sale_price'] ?? 0,
            'mortgage_debt' => $row['mortgage_debt'] ?? $row['mortgage_debt'] ?? 0,
            'savings' => $row['savings'] ?? 0,
            'selling_costs_percent' => $row['selling_costs_percent'] ?? $row['selling_costs_percent'] ?? 0,
            'moving_costs' => $row['moving_costs'] ?? $row['moving_costs'] ?? 0,
            'interest_rate' => $row['interest_rate'] ?? $row['interest_rate'] ?? 2,
            'inflation_rate' => $row['inflation_rate'] ?? $row['inflation_rate'] ?? 0,
        ];
    }

    public static function income(?array $row): array
    {
        $row = $row ?? [];

        return [
            'own_income' => $row['own_income'] ?? 0,
            'wia_wife' => $row['wia_wife'] ?? 0,
            'partner_has_wia' => $row['partner_has_wia'] ?? 1,
            'aow_future' => $row['aow_future'] ?? 0,
            'own_aow' => $row['own_aow'] ?? 0,
            'pension' => $row['pension'] ?? 0,
            'other_income' => $row['other_income'] ?? 0,
            'aow_start_age' => $row['aow_start_age'] ?? null,
            'pension_start_age' => $row['pension_start_age'] ?? 67,
            'income_stops_at_retirement' => $row['income_stops_at_retirement'] ?? 1,
            'minimum_monthly_income' => $row['minimum_monthly_income'] ?? 0,
        ];
    }

    public static function expenses(?array $row): array
    {
        $row = $row ?? [];

        return [
            'energy' => $row['energy'] ?? 0,
            'water' => $row['water'] ?? 0,
            'internet' => $row['internet'] ?? 0,
            'health_insurance' => $row['health_insurance'] ?? 0,
            'car_insurance' => $row['car_insurance'] ?? 0,
            'car_fuel' => $row['car_fuel'] ?? 0,
            'car_maintenance' => $row['car_maintenance'] ?? 0,
            'groceries' => $row['groceries'] ?? 0,
            'leisure' => $row['leisure'] ?? 0,
            'unforeseen' => $row['unforeseen'] ?? 0,
            'other' => $row['other'] ?? 0,
        ];
    }

    public static function taxes(?array $row): array
    {
        $row = $row ?? [];

        return [
            'forfettario_enabled' => $row['forfettario_enabled'] ?? 0,
            'forfettario_percentage' => $row['forfettario_percentage'] ?? 15,
            'normal_tax_percentage' => $row['normal_tax_percentage'] ?? 23,
            'tari_yearly' => $row['tari_yearly'] ?? 0,
            'social_contributions' => $row['social_contributions'] ?? 0,
            'road_tax_yearly' => $row['road_tax_yearly'] ?? 0,
            'profitability_coefficient' => $row['profitability_coefficient'] ?? 67,
            'startup_rate_enabled' => $row['startup_rate_enabled'] ?? 1,
            'rental_tax_rate' => $row['rental_tax_rate'] ?? 21,
            'forfettario_limit' => $row['forfettario_limit'] ?? 85000,
        ];
    }

    public static function property(array $row): array
    {
        return [
            'purchase_price' => $row['purchase_price'] ?? 0,
            'purchase_costs' => $row['purchase_costs'] ?? 0,
            'purchase_costs_percentage' => $row['purchase_costs_percentage'] ?? 0,
            'annual_costs' => $row['annual_costs'] ?? 0,
            'maintenance_yearly' => $row['maintenance_yearly'] ?? 0,
            'energy_monthly' => $row['energy_monthly'] ?? 0,
            'other_monthly_costs' => $row['other_monthly_costs'] ?? 0,
            'imu_tax' => $row['imu_tax'] ?? 0,
            'tari_yearly' => $row['tari_yearly'] ?? 0,
            'rental_income' => $row['rental_income'] ?? 0,
        ];
    }

    public static function bnbSettings(?array $row): array
    {
        $row = $row ?? [];

        return [
            'enabled' => $row['enabled'] ?? 0,
            'number_of_rooms' => $row['number_of_rooms'] ?? 0,
            'price_per_room_per_night' => $row['price_per_room_per_night'] ?? 0,
            'high_season_percentage' => $row['high_season_percentage'] ?? 0,
            'low_season_percentage' => $row['low_season_percentage'] ?? 0,
            'high_season_months' => $row['high_season_months'] ?? 0,
            'low_season_months' => $row['low_season_months'] ?? 0,
        ];
    }

    public static function bnbExpenses(?array $row): array
    {
        $row = $row ?? [];

        return [
            'extra_energy_water' => $row['extra_energy_water'] ?? 0,
            'insurance' => $row['insurance'] ?? 0,
            'cleaning' => $row['cleaning'] ?? 0,
            'linen_laundry' => $row['linen_laundry'] ?? 0,
            'breakfast_per_guest' => $row['breakfast_per_guest'] ?? 0,
            'platform_commission' => $row['platform_commission'] ?? 0,
            'marketing' => $row['marketing'] ?? 0,
            'maintenance' => $row['maintenance'] ?? 0,
            'administration' => $row['administration'] ?? 0,
        ];
    }
}
