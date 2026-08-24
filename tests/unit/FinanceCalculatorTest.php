<?php

use App\Libraries\FinanceCalculator;
use PHPUnit\Framework\TestCase;

class FinanceCalculatorTest extends TestCase
{
    private FinanceCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new FinanceCalculator();
    }

    public function testAowTwoPercentRule(): void
    {
        // Born 1970-01-01, emigrate 2010-01-01 → age 40, AOW 67 → start 17 → 23 years × 2% = 46%
        $pct = $this->calc->calculateAowPercentage('2010-01-01', '1970-01-01', 67);
        $this->assertEqualsWithDelta(46.0, $pct, 0.01);
    }

    public function testAowFullAfterRetirementAge(): void
    {
        $pct = $this->calc->calculateAowPercentage('2040-01-01', '1970-01-01', 67);
        $this->assertEquals(100.0, $pct);
    }

    public function testAowVoluntaryYears(): void
    {
        $base = $this->calc->calculateAowPercentage('2010-01-01', '1970-01-01', 67, 0);
        $extra = $this->calc->calculateAowPercentage('2010-01-01', '1970-01-01', 67, 5);
        $this->assertEqualsWithDelta($base + 10.0, $extra, 0.01);
    }

    public function testAowCappedAt100(): void
    {
        $pct = $this->calc->calculateAowPercentage('2010-01-01', '1970-01-01', 67, 40);
        $this->assertEquals(100.0, $pct);
    }

    public function testBnbRevenueUses365Days(): void
    {
        $settings = [
            'enabled' => 1,
            'number_of_rooms' => 1,
            'price_per_room_per_night' => 100,
            'high_season_percentage' => 100,
            'low_season_percentage' => 100,
            'high_season_months' => 6,
            'low_season_months' => 6,
        ];
        $monthly = $this->calc->calculateBnbMonthlyRevenue($settings);
        $this->assertEqualsWithDelta(365.25 * 100 / 12, $monthly, 0.1);
    }

    public function testBnbBreakfastAndCommission(): void
    {
        $settings = [
            'enabled' => 1,
            'number_of_rooms' => 1,
            'price_per_room_per_night' => 100,
            'high_season_percentage' => 100,
            'low_season_percentage' => 0,
            'high_season_months' => 12,
            'low_season_months' => 0,
        ];
        $revenue = $this->calc->calculateBnbMonthlyRevenue($settings);
        $expenses = $this->calc->calculateBnbMonthlyExpenses($settings, [
            'platform_commission' => 10,
            'breakfast_per_guest' => 12,
            'extra_energy_water' => 50,
        ], $revenue);

        $nights = 12 * FinanceCalculator::DAYS_PER_MONTH;
        $expectedBreakfast = $nights * 12 / 12;
        $expected = 50 + $revenue * 0.10 + $expectedBreakfast;
        $this->assertEqualsWithDelta($expected, $expenses, 0.15);
    }

    public function testForfettarioUsesCoefficientNotGross(): void
    {
        $tax = $this->calc->calculateBnbTax(1000, 200, [
            'forfettario_enabled' => 1,
            'forfettario_percentage' => 15,
            'profitability_coefficient' => 67,
            'startup_rate_enabled' => 0,
        ], 10);

        $this->assertEqualsWithDelta(1000 * 0.67 * 0.15, $tax, 0.01);
        $this->assertNotEqualsWithDelta(1000 * 0.15, $tax, 0.01);
    }

    public function testStartupRateFirstFiveYears(): void
    {
        $year0 = $this->calc->forfettarioRate([
            'forfettario_percentage' => 15,
            'startup_rate_enabled' => 1,
        ], 0);
        $year5 = $this->calc->forfettarioRate([
            'forfettario_percentage' => 15,
            'startup_rate_enabled' => 1,
        ], 5);

        $this->assertEquals(5.0, $year0);
        $this->assertEquals(15.0, $year5);
    }

    public function testStartingCapitalSubtractsSellingCosts(): void
    {
        $capital = $this->calc->calculateStartingCapital([
            'house_sale_price' => 400000,
            'mortgage_debt' => 100000,
            'savings' => 50000,
            'selling_costs_percent' => 2,
            'moving_costs' => 5000,
        ]);
        // equity 300000 - 8000 selling + 50000 - 5000 moving
        $this->assertEquals(337000.0, $capital);
    }

    public function testPropertyOutlayIncludesPercentageCosts(): void
    {
        $outlay = $this->calc->calculatePropertyOutlay([
            'purchase_price' => 200000,
            'purchase_costs' => 0,
            'purchase_costs_percentage' => 10,
        ]);
        $this->assertEquals(220000.0, $outlay);
    }

    public function testCashflowAgeGatesAowAndStopsSalary(): void
    {
        $result = $this->calc->analyze([
            'profile' => [
                'date_of_birth' => '1970-01-01',
                'partner_date_of_birth' => '1972-01-01',
                'emigration_date' => '2015-01-01',
                'retirement_age' => 67,
                'partner_retirement_age' => 67,
            ],
            'start_position' => [
                'house_sale_price' => 300000,
                'mortgage_debt' => 0,
                'savings' => 0,
                'interest_rate' => 0,
                'inflation_rate' => 0,
            ],
            'income' => [
                'own_income' => 2000,
                'income_stops_at_retirement' => 1,
                'wia_wife' => 1000,
                'partner_has_wia' => 1,
                'aow_future' => 800,
                'own_aow' => 900,
                'pension' => 500,
                'other_income' => 100,
                'pension_start_age' => 67,
            ],
            'expenses' => ['energy' => 200],
            'taxes' => ['tari_yearly' => 0, 'forfettario_enabled' => 0],
            'main_property' => ['purchase_price' => 0, 'purchase_costs' => 0, 'annual_costs' => 0, 'maintenance_yearly' => 1200],
            'second_property' => ['rental_income' => 400, 'purchase_price' => 0],
            'bnb_settings' => ['enabled' => 0],
            'bnb_expenses' => [],
        ], 2026);

        $c = $result['calculations'];
        // User is 56 in 2026, partner 54 — no AOW/pension yet, salary still counted
        $this->assertEqualsWithDelta(2000 + 1000 + 100 + 400, $c['monthly_income'], 0.5);
        $this->assertGreaterThan(0, $c['wia_amount']);
        $this->assertEquals(0.0, $c['own_aow_amount']);
        $this->assertEquals(0.0, $c['pension_amount']);
        $this->assertEqualsWithDelta(100.0, $c['other_income'], 0.01);
        $this->assertEqualsWithDelta(100.0, $c['monthly_expenses'], 0.01); // 1200/12 maintenance

        $retired = null;
        foreach ($result['yearlyProjections'] as $row) {
            if ((int) $row['user_age'] >= 67) {
                $retired = $row;
                break;
            }
        }
        $this->assertNotNull($retired);
        $this->assertGreaterThan(0, $retired['pension_amount']);
        $this->assertGreaterThan(0, $retired['own_aow_amount']);
        // Salary of 2000 must have stopped; WIA/other/rental/pension/AOW remain.
        $this->assertLessThan(2000 + 1000 + 100 + 400 + 500, $retired['monthly_income']);
        $this->assertGreaterThan(1500, $retired['monthly_income']);
    }

    public function testProjectionStartsAtCurrentYear(): void
    {
        $result = $this->calc->analyze([
            'profile' => [
                'date_of_birth' => '1965-06-01',
                'emigration_date' => '2020-01-01',
                'retirement_age' => 67,
            ],
            'start_position' => [
                'house_sale_price' => 100000,
                'mortgage_debt' => 0,
                'savings' => 0,
                'interest_rate' => 0,
            ],
            'income' => ['own_income' => 1000],
            'expenses' => [],
            'taxes' => [],
            'main_property' => ['purchase_price' => 0],
            'bnb_settings' => [],
            'bnb_expenses' => [],
        ], 2026);

        $this->assertEquals(2026, $result['yearlyProjections'][0]['year']);
        $this->assertGreaterThan(2026, $result['yearlyProjections'][1]['year']);
    }

    public function testNoInterestOnNegativeCapital(): void
    {
        $result = $this->calc->analyze([
            'profile' => ['date_of_birth' => '1980-01-01', 'retirement_age' => 67],
            'start_position' => [
                'house_sale_price' => 10000,
                'mortgage_debt' => 0,
                'savings' => 0,
                'interest_rate' => 10,
            ],
            'income' => ['own_income' => 0],
            'expenses' => ['energy' => 500],
            'taxes' => [],
            'main_property' => ['purchase_price' => 50000, 'purchase_costs' => 0],
            'bnb_settings' => [],
            'bnb_expenses' => [],
        ], 2026);

        $this->assertLessThan(0, $result['calculations']['remaining_capital']);
        $this->assertEquals(0.0, $result['yearlyProjections'][0]['monthly_interest']);
    }

    public function testWiaAndAowFollowInflation(): void
    {
        $input = [
            'profile' => [
                'date_of_birth' => '1970-01-01',
                'partner_date_of_birth' => '1972-01-01',
                'emigration_date' => '2015-01-01',
                'retirement_age' => 67,
                'partner_retirement_age' => 67,
            ],
            'start_position' => [
                'house_sale_price' => 0,
                'mortgage_debt' => 0,
                'savings' => 0,
                'interest_rate' => 0,
                'inflation_rate' => 2,
            ],
            'income' => [
                'own_income' => 0,
                'income_stops_at_retirement' => 1,
                'wia_wife' => 1000,
                'partner_has_wia' => 1,
                'aow_future' => 800,
                'own_aow' => 0,
                'pension' => 0,
                'other_income' => 0,
            ],
            'expenses' => [],
            'taxes' => [],
            'bnb_settings' => [],
            'bnb_expenses' => [],
        ];

        $result = $this->calc->analyze($input, 2026);
        $this->assertEqualsWithDelta(1000.0, $result['calculations']['wia_amount'], 0.01);

        $year5 = $result['yearlyProjections'][5];
        $this->assertEqualsWithDelta(1000 * pow(1.02, 5), $year5['wia_amount'], 0.05);
    }

    public function testRentalIncomeIsTaxed(): void
    {
        $result = $this->calc->analyze([
            'profile' => ['date_of_birth' => '1980-01-01', 'retirement_age' => 67],
            'start_position' => ['house_sale_price' => 0, 'savings' => 0],
            'income' => ['own_income' => 0],
            'expenses' => [],
            'taxes' => ['rental_tax_rate' => 21],
            'second_property' => ['rental_income' => 1000, 'purchase_price' => 0],
            'bnb_settings' => [],
            'bnb_expenses' => [],
        ], 2026);

        $this->assertEqualsWithDelta(210.0, $result['calculations']['monthly_taxes'], 0.05);
        $this->assertEqualsWithDelta(1000.0, $result['calculations']['rental_income'], 0.01);
    }
}
