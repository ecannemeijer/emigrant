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

$aowInf = $calc->analyze([
    'profile' => [
        'date_of_birth' => '1958-01-01',
        'emigration_date' => '2040-01-01',
        'retirement_age' => 67,
    ],
    'start_position' => [
        'house_sale_price' => 0,
        'savings' => 0,
        'interest_rate' => 0,
        'inflation_rate' => 2,
    ],
    'income' => [
        'has_partner' => 0,
        'own_aow' => 1000,
        'own_aow_start_age' => 67,
        'own_benefit_type' => 'none',
    ],
    'expenses' => [],
    'taxes' => [],
    'bnb_settings' => [],
    'bnb_expenses' => [],
], 2026);
$aow0 = $aowInf['yearlyProjections'][0]['own_aow_amount'];
$aow5 = $aowInf['yearlyProjections'][5]['own_aow_amount'];
$aowExp5 = 1000 * pow(1.02, 5);
check(abs($aow0 - 1000) < 0.02, "AOW year 0 = $aow0");
check(abs($aow5 - $aowExp5) < 0.05, "AOW year 5 indexed $aow5 vs $aowExp5");

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

$reno = $calc->analyze([
    'profile' => [
        'date_of_birth' => '1970-01-01',
        'retirement_age' => 67,
    ],
    'start_position' => [
        'house_sale_price' => 0,
        'savings' => 100000,
        'interest_rate' => 0,
        'inflation_rate' => 0,
        'renovation_by_year' => [2027 => 20000],
        'renovation_outlay' => 20000,
    ],
    'income' => ['own_income' => 0],
    'expenses' => [],
    'taxes' => [],
    'bnb_settings' => [],
    'bnb_expenses' => [],
], 2026);
$reno0 = $reno['yearlyProjections'][0];
$reno1 = $reno['yearlyProjections'][1];
check(abs(($reno0['renovation_outlay'] ?? 0) - 0) < 0.01, 'no renovation in 2026');
check(abs(($reno1['renovation_outlay'] ?? 0) - 20000) < 0.01, 'renovation 20000 in 2027');
check(abs(($reno['calculations']['remaining_capital'] ?? 0) - 100000) < 0.01, 'remaining still 100000 before 2027 reno');
check(abs(($reno1['capital'] ?? 0) - 80000) < 0.01, 'capital after 2027 reno '.$reno1['capital']);

$extraBase = [
    'profile' => ['date_of_birth' => '1980-01-01', 'retirement_age' => 67],
    'start_position' => ['house_sale_price' => 0, 'savings' => 0, 'interest_rate' => 0, 'inflation_rate' => 2],
    'income' => ['own_income' => 0, 'own_benefit_type' => 'none'],
    'taxes' => [],
    'bnb_settings' => [],
    'bnb_expenses' => [],
];
$withoutExtra = $calc->analyze($extraBase + ['expenses' => ['energy' => 100]], 2026);
$withExtra = $calc->analyze($extraBase + ['expenses' => [
    'energy' => 100,
    'items' => [
        ['name' => 'Netflix', 'category' => 'subscription', 'amount' => 30],
        ['name' => 'Inboedel', 'category' => 'insurance', 'amount' => 20],
    ],
]], 2026);
check(abs(($withoutExtra['calculations']['monthly_expenses'] ?? 0) - 100) < 0.02, 'expenses without extra items 100');
check(abs(($withExtra['calculations']['monthly_expenses'] ?? 0) - 150) < 0.02, 'expenses with extra items 150');
$year5exp = ($withExtra['yearlyProjections'][5]['yearly_expenses'] ?? 0) / 12;
check(abs($year5exp - 150 * pow(1.02, 5)) < 0.1, 'extra items inflate year 5 '.$year5exp);

$help86 = $calc->calculateAowPercentage('2020-01-01', '1960-01-01', 67);
$help90 = $calc->calculateAowPercentage('2022-01-01', '1960-01-01', 67);
check(abs($help86 - 86) < 0.02, "AOW help 86% got $help86");
check(abs($help90 - 90) < 0.02, "AOW help 90% got $help90");

$salary = $calc->analyze([
    'profile' => [
        'date_of_birth' => '1970-01-01',
        'emigration_date' => '2015-01-01',
        'retirement_age' => 67,
    ],
    'start_position' => ['house_sale_price' => 0, 'savings' => 0, 'interest_rate' => 0, 'inflation_rate' => 0],
    'income' => [
        'has_partner' => 0,
        'own_benefit_type' => 'none',
        'own_other_income' => 2000,
        'income_stops_at_retirement' => 1,
        'pension' => 400,
        'pension_start_age' => 67,
    ],
    'expenses' => [],
    'taxes' => [],
    'bnb_settings' => [],
    'bnb_expenses' => [],
], 2026);
check(abs(($salary['calculations']['own_other_income'] ?? -1) - 2000) < 0.01, 'salary before AOW');
$retiredSalary = null;
foreach ($salary['yearlyProjections'] as $row) {
    if ((int) ($row['user_age'] ?? 0) >= 67) {
        $retiredSalary = $row;
        break;
    }
}
check($retiredSalary && abs($retiredSalary['own_other_income'] - 0) < 0.01, 'salary stops at AOW');
check($retiredSalary && abs($retiredSalary['pension_amount'] - 400) < 0.01, 'pension starts at AOW');

$partnerPen = $calc->analyze([
    'profile' => [
        'date_of_birth' => '1970-01-01',
        'partner_date_of_birth' => '1958-01-01',
        'emigration_date' => '2040-01-01',
        'retirement_age' => 67,
        'partner_retirement_age' => 67,
        'has_partner' => 1,
    ],
    'start_position' => ['house_sale_price' => 0, 'savings' => 0, 'interest_rate' => 0, 'inflation_rate' => 0],
    'income' => [
        'has_partner' => 1,
        'own_benefit_type' => 'none',
        'partner_benefit_type' => 'none',
        'pension' => 300,
        'partner_pension' => 450,
    ],
    'expenses' => [],
    'taxes' => [],
    'bnb_settings' => [],
    'bnb_expenses' => [],
], 2026);
check(abs(($partnerPen['calculations']['pension_amount'] ?? -1) - 0) < 0.01, 'own pension not yet');
check(abs(($partnerPen['calculations']['partner_pension_amount'] ?? -1) - 450) < 0.01, 'partner pension at 67');

$irpef = $calc->analyze([
    'profile' => ['date_of_birth' => '1980-01-01', 'retirement_age' => 67],
    'start_position' => ['house_sale_price' => 0, 'savings' => 0, 'interest_rate' => 0],
    'income' => [
        'has_partner' => 0,
        'own_benefit_type' => 'none',
        'own_other_income' => 2000,
        'income_stops_at_retirement' => 1,
    ],
    'expenses' => [],
    'taxes' => ['irpef_salary_percent' => 15],
    'bnb_settings' => [],
    'bnb_expenses' => [],
], 2026);
check(abs(($irpef['calculations']['irpef_nl_amount'] ?? -1) - 300) < 0.05, 'IRPEF 15% on salary');
check(abs(($irpef['calculations']['monthly_taxes'] ?? -1) - 300) < 0.05, 'IRPEF in monthly taxes');

echo $fail === 0 ? "\nAll good\n" : "\n$fail failed\n";
exit($fail === 0 ? 0 : 1);
