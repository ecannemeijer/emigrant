<?php

namespace App\Libraries;

/**
 * Central financial engine for the Italy emigration calculator.
 * Pure PHP so it can be unit-tested without the CodeIgniter bootstrap.
 */
class FinanceCalculator
{
    public const DAYS_PER_MONTH = 365.25 / 12;
    public const FORFETTARIO_LIMIT = 85000.0;
    public const BNB_COEFFICIENT = 67.0;

    /**
     * SVB rule: 2% per insured year in the 50 years before AOW age.
     */
    public function calculateAowPercentage(
        ?string $emigrationDate,
        ?string $dateOfBirth,
        int $retirementAge = 67,
        float $voluntaryYears = 0.0
    ): float {
        if (empty($emigrationDate) || empty($dateOfBirth)) {
            return 100.0;
        }

        $birth = new \DateTime($dateOfBirth);
        $emig  = new \DateTime($emigrationDate);
        $ageAtEmigration = (int) $birth->diff($emig)->y;

        if ($ageAtEmigration >= $retirementAge) {
            return 100.0;
        }

        $startAge = $retirementAge - 50;
        $insured  = $ageAtEmigration < $startAge ? 0.0 : (float) ($ageAtEmigration - $startAge);
        $percentage = ($insured + max(0.0, $voluntaryYears)) * 2.0;

        return max(0.0, min(100.0, $percentage));
    }

    public function calculateAge(?string $birthdate, ?string $onDate = null): ?int
    {
        if (empty($birthdate)) {
            return null;
        }

        $birth = new \DateTime($birthdate);
        $on    = $onDate ? new \DateTime($onDate) : new \DateTime();

        return (int) $birth->diff($on)->y;
    }

    public function calculateBnbYearlyNights(array $settings): float
    {
        if (empty($settings['enabled'])) {
            return 0.0;
        }

        $rooms = (float) ($settings['number_of_rooms'] ?? 0);
        $highPct = ((float) ($settings['high_season_percentage'] ?? 0)) / 100;
        $lowPct  = ((float) ($settings['low_season_percentage'] ?? 0)) / 100;
        $highMonths = (float) ($settings['high_season_months'] ?? 0);
        $lowMonths  = (float) ($settings['low_season_months'] ?? 0);

        return $highMonths * self::DAYS_PER_MONTH * $highPct * $rooms
            + $lowMonths * self::DAYS_PER_MONTH * $lowPct * $rooms;
    }

    public function calculateBnbMonthlyRevenue(array $settings): float
    {
        if (empty($settings['enabled'])) {
            return 0.0;
        }

        $nights = $this->calculateBnbYearlyNights($settings);
        $price  = (float) ($settings['price_per_room_per_night'] ?? 0);

        return ($nights * $price) / 12.0;
    }

    public function calculateBnbMonthlyExpenses(array $settings, array $expenses, float $monthlyRevenue): float
    {
        if (empty($settings['enabled'])) {
            return 0.0;
        }

        $fixed = (float) ($expenses['extra_energy_water'] ?? 0)
            + (float) ($expenses['insurance'] ?? 0)
            + (float) ($expenses['cleaning'] ?? 0)
            + (float) ($expenses['linen_laundry'] ?? 0)
            + (float) ($expenses['marketing'] ?? 0)
            + (float) ($expenses['maintenance'] ?? 0)
            + (float) ($expenses['administration'] ?? 0);

        $commission = $monthlyRevenue * (((float) ($expenses['platform_commission'] ?? 0)) / 100);
        $breakfast  = $this->calculateBnbYearlyNights($settings)
            * ((float) ($expenses['breakfast_per_guest'] ?? 0)) / 12.0;

        return $fixed + $commission + $breakfast;
    }

    private function pick(array $row, array $keys, $default = 0)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return $default;
    }

    public function forfettarioRate(array $taxes, int $yearsSinceEmigration): float
    {
        $base    = (float) $this->pick($taxes, ['forfettario_percentage', 'forfettario_percentage'], 15);
        $startup = (int) $this->pick($taxes, ['startup_rate_enabled', 'startup_rate_enabled'], 1);
        if ($startup && $yearsSinceEmigration < 5) {
            return min($base, 5.0);
        }

        return $base;
    }

    public function calculateBnbTax(
        float $monthlyRevenue,
        float $monthlyExpenses,
        array $taxes,
        int $yearsSinceEmigration = 0
    ): float {
        if ($monthlyRevenue <= 0) {
            return 0.0;
        }

        if (!empty($this->pick($taxes, ['forfettario_enabled', 'forfettario_enabled'], 0))) {
            $rate  = $this->forfettarioRate($taxes, $yearsSinceEmigration);
            $coeff = ((float) $this->pick($taxes, ['profitability_coefficient', 'profitability_coefficient'], self::BNB_COEFFICIENT)) / 100;

            return $monthlyRevenue * $coeff * ($rate / 100);
        }

        $net  = max(0.0, $monthlyRevenue - $monthlyExpenses);
        $rate = (float) $this->pick($taxes, ['normal_tax_percentage', 'normal_tax_percentage'], 23);

        return $net * ($rate / 100);
    }

    public function calculateBreakevenPercentage(
        array $settings,
        array $expenses,
        array $taxes = [],
        int $yearsSinceEmigration = 0
    ): float {
        $rooms = (float) ($settings['number_of_rooms'] ?? 0);
        $price = (float) ($settings['price_per_room_per_night'] ?? 0);
        if ($rooms <= 0 || $price <= 0) {
            return 0.0;
        }

        $low  = 0.0;
        $high = 100.0;
        $best = 100.0;

        for ($i = 0; $i < 40; $i++) {
            $mid   = ($low + $high) / 2;
            $probe = $settings;
            $probe['enabled'] = 1;
            $probe['high_season_percentage'] = $mid;
            $probe['low_season_percentage']  = $mid;
            $probe['high_season_months'] = 6;
            $probe['low_season_months']  = 6;

            $rev = $this->calculateBnbMonthlyRevenue($probe);
            $exp = $this->calculateBnbMonthlyExpenses($probe, $expenses, $rev);
            $tax = $this->calculateBnbTax($rev, $exp, $taxes, $yearsSinceEmigration);
            $net = $rev - $exp - $tax;

            if ($net >= 0) {
                $best = $mid;
                $high = $mid;
            } else {
                $low = $mid;
            }
        }

        return $best;
    }

    public function calculateStartingCapital(array $startPosition): float
    {
        $sale     = (float) $this->pick($startPosition, ['house_sale_price', 'house_sale_price'], 0);
        $mortgage = (float) $this->pick($startPosition, ['mortgage_debt', 'mortgage_debt'], 0);
        $savings  = (float) $this->pick($startPosition, ['savings', 'savings'], 0);
        $sellPct  = (float) $this->pick($startPosition, ['selling_costs_percent', 'selling_costs_percent'], 0);
        $moving   = (float) $this->pick($startPosition, ['moving_costs', 'moving_costs'], 0);
        $equity   = $sale - $mortgage - ($sale * $sellPct / 100);

        return $equity + $savings - $moving;
    }

    public function calculatePropertyOutlay(?array $property): float
    {
        if (!$property) {
            return 0.0;
        }

        $price = (float) ($property['purchase_price'] ?? 0);
        $costs = (float) ($property['purchase_costs'] ?? 0);
        if ($costs <= 0 && isset($property['purchase_costs_percentage'])) {
            $costs = $price * ((float) $property['purchase_costs_percentage']) / 100;
        }

        return $price + $costs;
    }

    /**
     * @return array{calculations: array, yearlyProjections: array, warnings: array}
     */
    public function analyze(array $input, ?int $referenceYear = null): array
    {
        $referenceYear = $referenceYear ?? (int) date('Y');
        $asOf          = sprintf('%04d-01-01', $referenceYear);

        $profile        = $input['profile'] ?? [];
        $startPosition  = $input['start_position'] ?? [];
        $income         = $input['income'] ?? [];
        $expenses       = $input['expenses'] ?? [];
        $taxes          = $input['taxes'] ?? [];
        $mainProperty   = $input['main_property'] ?? null;
        $secondProperty = $input['second_property'] ?? null;
        $bnbSettings    = $input['bnb_settings'] ?? [];
        $bnbExpenses    = $input['bnb_expenses'] ?? [];

        $voluntaryYears = (float) ($profile['voluntary_aow_years'] ?? 0);
        $userRetAge     = (int) ($income['pension_start_age'] ?? $profile['retirement_age'] ?? 67);
        $partnerRetAge  = (int) ($profile['partner_retirement_age'] ?? 67);
        $ownAowAge      = (int) ($income['aow_start_age'] ?? $userRetAge);
        $stopOwnIncome  = ((int) ($income['income_stops_at_retirement'] ?? 1)) === 1;

        $partnerAowPct = $this->calculateAowPercentage(
            $profile['emigration_date'] ?? null,
            $profile['partner_date_of_birth'] ?? null,
            $partnerRetAge,
            $voluntaryYears
        );
        $ownAowPct = $this->calculateAowPercentage(
            $profile['emigration_date'] ?? null,
            $profile['date_of_birth'] ?? null,
            $userRetAge,
            $voluntaryYears
        );

        $emigrationYear = $referenceYear;
        if (!empty($profile['emigration_date'])) {
            $emigrationYear = (int) substr($profile['emigration_date'], 0, 4);
        }
        $yearsSinceEmigration = max(0, $referenceYear - $emigrationYear);

        $bnbRevenue = $this->calculateBnbMonthlyRevenue($bnbSettings);
        $bnbCosts   = $this->calculateBnbMonthlyExpenses($bnbSettings, $bnbExpenses, $bnbRevenue);
        $bnbTax     = $this->calculateBnbTax($bnbRevenue, $bnbCosts, $taxes, $yearsSinceEmigration);
        $bnbNet     = $bnbRevenue - $bnbCosts;

        $currentUserAge    = $this->calculateAge($profile['date_of_birth'] ?? null, $asOf);
        $currentPartnerAge = $this->calculateAge($profile['partner_date_of_birth'] ?? null, $asOf);

        $commissionPct = ((float) ($bnbExpenses['platform_commission'] ?? 0)) / 100;

        $startingCapital  = $this->calculateStartingCapital($startPosition);
        $remainingCapital = $startingCapital
            - $this->calculatePropertyOutlay($mainProperty)
            - $this->calculatePropertyOutlay($secondProperty);

        $interestRate  = (float) ($startPosition['interest_rate'] ?? 2);
        $inflationRate = (float) ($startPosition['inflation_rate'] ?? 0);

        $yearsToProject = 15;
        if ($currentPartnerAge && $currentPartnerAge < 68) {
            $yearsToProject = max(15, 68 - $currentPartnerAge);
        }
        if ($currentUserAge && $currentUserAge < 68) {
            $yearsToProject = max($yearsToProject, 68 - $currentUserAge);
        }

        $projections = [];
        $capital     = $remainingCapital;
        $year0       = null;

        for ($offset = 0; $offset <= $yearsToProject; $offset++) {
            $year       = $referenceYear + $offset;
            $userAge    = $currentUserAge !== null ? $currentUserAge + $offset : null;
            $partnerAge = $currentPartnerAge !== null ? $currentPartnerAge + $offset : null;
            $inflator   = pow(1 + $inflationRate / 100, $offset);
            $yrsEmig    = max(0, $year - $emigrationYear);

            $bnbRevY = $bnbRevenue * $inflator;
            $bnbExpY = 0.0;
            if (!empty($bnbSettings['enabled'])) {
                $baseExp    = $this->calculateBnbMonthlyExpenses($bnbSettings, $bnbExpenses, $bnbRevenue);
                $fixedShare = $baseExp - ($bnbRevenue * $commissionPct);
                $bnbExpY    = $fixedShare * $inflator + $bnbRevY * $commissionPct;
            }
            $bnbTaxY = $this->calculateBnbTax($bnbRevY, $bnbExpY, $taxes, $yrsEmig);
            $interestMonthly = $capital > 0 ? ($capital * ($interestRate / 100)) / 12 : 0.0;

            $cf = $this->cashflowForAges(
                $userAge,
                $partnerAge,
                $income,
                $expenses,
                $taxes,
                $mainProperty,
                $secondProperty,
                $bnbRevY,
                $bnbExpY,
                $bnbTaxY,
                $partnerAowPct,
                $ownAowPct,
                $userRetAge,
                $partnerRetAge,
                $ownAowAge,
                $partnerRetAge,
                $stopOwnIncome,
                $interestMonthly,
                $inflator
            );

            $cfWithoutBnb = $this->cashflowForAges(
                $userAge,
                $partnerAge,
                $income,
                $expenses,
                $taxes,
                $mainProperty,
                $secondProperty,
                0,
                0,
                0,
                $partnerAowPct,
                $ownAowPct,
                $userRetAge,
                $partnerRetAge,
                $ownAowAge,
                $partnerRetAge,
                $stopOwnIncome,
                $interestMonthly,
                $inflator
            );

            if ($offset === 0) {
                $year0 = $cf;
            }

            $yearlyNet = $cf['monthly_net'] * 12;
            $capital  += $yearlyNet;

            $projections[] = [
                'year' => $year,
                'age_offset' => $offset,
                'user_age' => $userAge,
                'partner_age' => $partnerAge,
                'monthly_income' => $cf['total_monthly_income'],
                'monthly_income_without_bnb' => $cfWithoutBnb['total_monthly_income'],
                'monthly_net' => $cf['monthly_net'],
                'monthly_net_without_bnb' => $cfWithoutBnb['monthly_net'],
                'bnb_monthly' => $bnbRevY - $bnbExpY,
                'monthly_interest' => $interestMonthly,
                'yearly_income' => $cf['total_monthly_income'] * 12,
                'yearly_income_without_bnb' => $cfWithoutBnb['total_monthly_income'] * 12,
                'yearly_expenses' => $cf['monthly_expenses'] * 12,
                'yearly_taxes' => $cf['monthly_taxes'] * 12,
                'yearly_net' => $yearlyNet,
                'yearly_net_without_bnb' => $cfWithoutBnb['monthly_net'] * 12,
                'capital' => $capital,
                'has_partner_aow' => $cf['has_partner_aow'],
                'has_partner_income' => $cf['has_partner_income'],
                'has_own_pension' => $cf['has_own_pension'],
                'has_own_aow' => $cf['has_own_aow'],
                'has_wia' => $cf['has_wia'],
                'partner_aow_amount' => $cf['partner_aow_amount'],
                'partner_income_amount' => $cf['partner_income_amount'],
                'own_aow_amount' => $cf['own_aow_amount'],
                'pension_amount' => $cf['pension_amount'],
                'wia_amount' => $cf['wia_amount'],
                'rental_income' => $cf['rental_income'],
                'other_income' => $cf['other_income'],
                'has_partner_retired' => ($partnerAge && $partnerAge >= $partnerRetAge),
                'has_user_retired' => ($userAge && $userAge >= $userRetAge),
            ];
        }

        $year0 = $year0 ?? $this->cashflowForAges(
            $currentUserAge,
            $currentPartnerAge,
            $income,
            $expenses,
            $taxes,
            $mainProperty,
            $secondProperty,
            $bnbRevenue,
            $bnbCosts,
            $bnbTax,
            $partnerAowPct,
            $ownAowPct,
            $userRetAge,
            $partnerRetAge,
            $ownAowAge,
            $partnerRetAge,
            $stopOwnIncome,
            0.0,
            1.0
        );

        $capital12 = $projections[1]['capital'] ?? ($remainingCapital + $year0['monthly_net'] * 12);
        $capital24 = $projections[2]['capital'] ?? ($remainingCapital + $year0['monthly_net'] * 24);
        $capital36 = $projections[3]['capital'] ?? ($remainingCapital + $year0['monthly_net'] * 36);

        $runway = null;
        if ($year0['monthly_net'] < 0 && $remainingCapital > 0) {
            $runway = (int) floor($remainingCapital / abs($year0['monthly_net']));
        }

        $warnings = [];
        $yearlyBnb = $bnbRevenue * 12;
        $limit = (float) ($taxes['forfettario_limit'] ?? self::FORFETTARIO_LIMIT);
        if (!empty($bnbSettings['enabled']) && !empty($taxes['forfettario_enabled']) && $yearlyBnb > $limit) {
            $warnings[] = 'B&B-omzet overschrijdt het forfettario-plafond van € ' . number_format($limit, 0, ',', '.') . '.';
        }
        $minimum = (float) ($income['minimum_monthly_income'] ?? 0);
        if ($minimum > 0 && $year0['monthly_net'] < $minimum) {
            $warnings[] = 'Netto per maand ligt onder je ingestelde minimuminkomen.';
        }
        if ($year0['monthly_net'] < 0) {
            $warnings[] = 'Negatieve cashflow: vermogen loopt terug.';
        }

        $breakeven = 0.0;
        if (!empty($bnbSettings['enabled'])) {
            $breakeven = $this->calculateBreakevenPercentage($bnbSettings, $bnbExpenses, $taxes, $yearsSinceEmigration);
        }

        $calculations = array_merge($year0, [
            'monthly_income' => $year0['base_monthly_income'],
            'bnb_revenue' => $bnbRevenue,
            'bnb_expenses' => $bnbCosts,
            'bnb_net_income' => $bnbNet,
            'bnb_tax' => $bnbTax,
            'bnb_breakeven_percentage' => $breakeven,
            'remaining_capital' => $remainingCapital,
            'starting_capital' => $startingCapital,
            'capital_12_months' => $capital12,
            'capital_24_months' => $capital24,
            'capital_36_months' => $capital36,
            'runway_months' => $runway,
            'partner_aow_percentage' => $partnerAowPct,
            'own_aow_percentage' => $ownAowPct,
            'forfettario_over_limit' => $yearlyBnb > $limit,
            'below_minimum' => $minimum > 0 && $year0['monthly_net'] < $minimum,
            'minimum_monthly_income' => $minimum,
        ]);

        return [
            'calculations' => $calculations,
            'yearlyProjections' => $projections,
            'warnings' => $warnings,
        ];
    }

    private function cashflowForAges(
        ?int $userAge,
        ?int $partnerAge,
        array $income,
        array $expenses,
        array $taxes,
        ?array $mainProperty,
        ?array $secondProperty,
        float $bnbRevenue,
        float $bnbCosts,
        float $bnbTax,
        float $partnerAowPct,
        float $ownAowPct,
        int $userRetAge,
        int $partnerRetAge,
        int $ownAowAge,
        int $partnerAowAge,
        bool $stopOwnIncome,
        float $monthlyInterest,
        float $inflator
    ): array {
        $ownIncome   = (float) ($income['own_income'] ?? 0);
        $userRetired = $userAge !== null && $userAge >= $userRetAge;
        if ($stopOwnIncome && $userRetired) {
            $ownIncome = 0.0;
        }

        $partnerHasWia         = ((int) ($income['partner_has_wia'] ?? 1)) === 1;
        $partnerIncomeField    = (float) ($income['wia_wife'] ?? 0);
        $wiaAmount             = 0.0;
        $partnerIncomeAmount   = 0.0;
        $partnerAowAmount      = 0.0;
        $hasWia                = false;
        $hasPartnerIncome      = false;
        $hasPartnerAow         = false;
        $partnerAowDue         = $partnerAge !== null && $partnerAge >= $partnerAowAge;

        if ($partnerHasWia) {
            if ($partnerAowDue) {
                $partnerAowAmount = (float) ($income['aow_future'] ?? 0) * ($partnerAowPct / 100) * $inflator;
                $hasPartnerAow    = $partnerAowAmount > 0;
            } else {
                // WIA wordt wettelijk geïndexeerd (doorgaans 2x per jaar, gekoppeld aan het minimumloon).
                $wiaAmount = $partnerIncomeField * $inflator;
                $hasWia    = $wiaAmount > 0;
            }
        } else {
            $partnerIncomeAmount = $partnerIncomeField;
            $hasPartnerIncome    = $partnerIncomeAmount > 0;
            if ($partnerAowDue) {
                $partnerAowAmount = (float) ($income['aow_future'] ?? 0) * ($partnerAowPct / 100) * $inflator;
                $hasPartnerAow    = $partnerAowAmount > 0;
            }
        }

        $pensionAmount = 0.0;
        $ownAowAmount  = 0.0;
        $hasOwnPension = false;
        $hasOwnAow     = false;
        if ($userAge !== null && $userAge >= $userRetAge) {
            $pensionAmount = (float) ($income['pension'] ?? 0);
            $hasOwnPension = $pensionAmount > 0;
        }
        if ($userAge !== null && $userAge >= $ownAowAge) {
            $ownAowAmount = (float) ($income['own_aow'] ?? 0) * ($ownAowPct / 100) * $inflator;
            $hasOwnAow    = $ownAowAmount > 0;
        }

        $otherIncome  = (float) ($income['other_income'] ?? 0);
        $rentalIncome = (float) ($secondProperty['rental_income'] ?? 0);

        $baseMonthly = $ownIncome + $wiaAmount + $partnerIncomeAmount + $partnerAowAmount
            + $ownAowAmount + $pensionAmount + $otherIncome + $rentalIncome;

        $bnbNet             = $bnbRevenue - $bnbCosts;
        $totalMonthlyIncome = $baseMonthly + $bnbNet + $monthlyInterest;

        $household = ((float) ($expenses['energy'] ?? 0)
            + (float) ($expenses['water'] ?? 0)
            + (float) ($expenses['internet'] ?? 0)
            + (float) ($expenses['health_insurance'] ?? 0)
            + (float) ($expenses['car_insurance'] ?? 0)
            + (float) ($expenses['car_fuel'] ?? 0)
            + (float) ($expenses['car_maintenance'] ?? 0)
            + (float) ($expenses['groceries'] ?? 0)
            + (float) ($expenses['leisure'] ?? 0)
            + (float) ($expenses['unforeseen'] ?? 0)
            + (float) ($expenses['other'] ?? 0)) * $inflator;

        $mainMonthly = 0.0;
        if ($mainProperty) {
            $mainMonthly = (((float) ($mainProperty['annual_costs'] ?? 0))
                + ((float) ($mainProperty['maintenance_yearly'] ?? 0))) / 12 * $inflator;
        }

        $secondMonthly = 0.0;
        $secondTari    = 0.0;
        $secondImu     = 0.0;
        if ($secondProperty) {
            $secondMonthly = ((((float) ($secondProperty['annual_costs'] ?? 0))
                + ((float) ($secondProperty['maintenance_yearly'] ?? 0))) / 12
                + (float) ($secondProperty['energy_monthly'] ?? 0)
                + (float) ($secondProperty['other_monthly_costs'] ?? 0)) * $inflator;
            $secondTari = ((float) ($secondProperty['tari_yearly'] ?? 0)) / 12 * $inflator;
            $secondImu  = ((float) ($secondProperty['imu_tax'] ?? 0)) / 12 * $inflator;
        }

        $monthlyExpenses = $household + $mainMonthly + $secondMonthly;
        $tariMain        = ((float) ($taxes['tari_yearly'] ?? 0)) / 12 * $inflator;
        $social          = (float) ($taxes['social_contributions'] ?? 0) * $inflator;
        $road            = ((float) ($taxes['road_tax_yearly'] ?? 0)) / 12 * $inflator;
        $rentalTax       = $rentalIncome * (((float) ($taxes['rental_tax_rate'] ?? 21)) / 100);
        $monthlyTaxes    = $tariMain + $secondTari + $social + $road + $secondImu + $bnbTax + $rentalTax;
        $monthlyNet      = $totalMonthlyIncome - $monthlyExpenses - $monthlyTaxes;

        return [
            'base_monthly_income' => $baseMonthly,
            'total_monthly_income' => $totalMonthlyIncome,
            'monthly_expenses' => $monthlyExpenses,
            'monthly_taxes' => $monthlyTaxes,
            'monthly_net' => $monthlyNet,
            'net_disposable' => $monthlyNet,
            'has_partner_aow' => $hasPartnerAow,
            'has_partner_income' => $hasPartnerIncome,
            'has_own_pension' => $hasOwnPension,
            'has_own_aow' => $hasOwnAow,
            'has_wia' => $hasWia,
            'partner_aow_amount' => $partnerAowAmount,
            'partner_income_amount' => $partnerIncomeAmount,
            'own_aow_amount' => $ownAowAmount,
            'pension_amount' => $pensionAmount,
            'wia_amount' => $wiaAmount,
            'rental_income' => $rentalIncome,
            'other_income' => $otherIncome,
        ];
    }
}
