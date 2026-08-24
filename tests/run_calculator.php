<?php
require __DIR__ . '/bootstrap.php';

use App\Libraries\FinanceCalculator;

$calc = new FinanceCalculator();
$fail = 0;

function check($ok, $msg) {
    global $fail;
    echo ($ok ? 'OK  ' : 'FAIL') . " $msg\n";
    if (!$ok) {
        $fail++;
    }
}

$pct = $calc->calculateAowPercentage('2010-01-01', '1970-01-01', 67);
check(abs($pct - 46) < 0.02, "AOW 46% got $pct");

$cap = $calc->calculateStartingCapital([
    'house_sale_price' => 400000,
    'mortgage_debt' => 100000,
    'savings' => 50000,
    'selling_costs_percent' => 2,
    'moving_costs' => 5000,
]);
check(abs($cap - 337000) < 0.01, "capital $cap");

$tax = $calc->calculateBnbTax(1000, 200, [
    'forfettario_enabled' => 1,
    'forfettario_percentage' => 15,
    'profitability_coefficient' => 67,
    'startup_rate_enabled' => 0,
], 10);
check(abs($tax - 100.5) < 0.05, "forfettario tax $tax");

$r0 = $calc->forfettarioRate(['forfettario_percentage' => 15, 'startup_rate_enabled' => 1], 0);
$r5 = $calc->forfettarioRate(['forfettario_percentage' => 15, 'startup_rate_enabled' => 1], 5);
check($r0 == 5.0 && $r5 == 15.0, "startup rate $r0 / $r5");

$result = $calc->analyze([
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
check(($result['yearlyProjections'][0]['year'] ?? 0) === 2026, 'projection starts 2026');

$wia = $calc->analyze([
    'profile' => [
        'date_of_birth' => '1970-01-01',
        'partner_date_of_birth' => '1972-01-01',
        'retirement_age' => 67,
        'partner_retirement_age' => 67,
    ],
    'start_position' => [
        'house_sale_price' => 0,
        'savings' => 0,
        'interest_rate' => 0,
        'inflation_rate' => 2,
    ],
    'income' => [
        'own_income' => 0,
        'wia_wife' => 1000,
        'partner_has_wia' => 1,
    ],
    'expenses' => [],
    'taxes' => [],
    'bnb_settings' => [],
    'bnb_expenses' => [],
], 2026);
$wia0 = $wia['yearlyProjections'][0]['wia_amount'];
$wia5 = $wia['yearlyProjections'][5]['wia_amount'];
$expected5 = 1000 * pow(1.02, 5);
check(abs($wia0 - 1000) < 0.02, "WIA year 0 = $wia0");
check(abs($wia5 - $expected5) < 0.05, "WIA year 5 indexed $wia5 vs $expected5");

$aowSplit = $calc->analyze([
    'profile' => [
        'date_of_birth' => '1961-01-01',
        'partner_date_of_birth' => '1964-01-01',
        'emigration_date' => '2010-01-01',
        'retirement_age' => 67,
        'partner_retirement_age' => 67,
    ],
    'start_position' => ['house_sale_price' => 0, 'savings' => 0, 'interest_rate' => 0, 'inflation_rate' => 0],
    'income' => [
        'has_partner' => 1,
        'own_benefit_type' => 'wia',
        'own_income' => 900,
        'own_aow' => 1200,
        'own_aow_start_age' => 65,
        'partner_benefit_type' => 'wia',
        'wia_wife' => 800,
        'partner_has_wia' => 1,
        'aow_future' => 1100,
        'partner_aow_start_age' => 67,
    ],
    'expenses' => [],
    'taxes' => [],
    'bnb_settings' => [],
    'bnb_expenses' => [],
], 2026);
check(($aowSplit['yearlyProjections'][0]['own_aow_amount'] ?? 0) > 0, 'own AOW starts at 65');
check(($aowSplit['yearlyProjections'][0]['wia_amount'] ?? 0) > 0, 'partner WIA still running at 62');
check(($aowSplit['yearlyProjections'][0]['partner_aow_amount'] ?? 0) == 0, 'partner AOW not yet');

echo $fail === 0 ? "\nAll good\n" : "\n$fail failed\n";
exit($fail === 0 ? 0 : 1);
