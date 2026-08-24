<?php

namespace App\Controllers;

use App\Models\StartPositionModel;
use App\Models\IncomeModel;
use App\Models\PropertyModel;
use App\Models\ExpenseModel;
use App\Models\TaxModel;
use App\Models\BnbSettingModel;
use App\Models\BnbExpenseModel;

class Export extends BaseController
{
    public function csv()
    {
        $userId = session()->get('userId');

        // Load all data
        $startPositionModel = new StartPositionModel();
        $incomeModel = new IncomeModel();
        $propertyModel = new PropertyModel();
        $expenseModel = new ExpenseModel();
        $taxModel = new TaxModel();
        $bnbSettingModel = new BnbSettingModel();
        $bnbExpenseModel = new BnbExpenseModel();

        $startPosition = $startPositionModel->getByUserId($userId);
        $income = $incomeModel->getByUserId($userId);
        $mainProperty = $propertyModel->getMainProperty($userId);
        $secondProperty = $propertyModel->getSecondProperty($userId);
        $expenses = $expenseModel->getByUserId($userId);
        $taxes = $taxModel->getByUserId($userId);
        $bnbSettings = $bnbSettingModel->getByUserId($userId);
        $bnbExpenses = $bnbExpenseModel->getByUserId($userId);

        // Prepare CSV data
        $csvData = [];
        
        // Header
        $csvData[] = ['Emigratie Italië Calculator - Export', date('Y-m-d H:i:s')];
        $csvData[] = [''];

        // Start Position
        $csvData[] = ['STARTPOSITIE NEDERLAND'];
        $csvData[] = ['Verkoopprijs woning', number_format($startPosition['house_sale_price'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Hypotheekrestschuld', number_format($startPosition['mortgage_debt'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Netto overwaarde', number_format($startPosition['net_equity'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Spaargeld', number_format($startPosition['savings'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Totaal startvermogen', number_format($startPosition['total_starting_capital'] ?? 0, 2, ',', '.')];
        $csvData[] = [''];

        // Income
        $csvData[] = ['INKOMSTEN (netto per maand)'];
        $csvData[] = ['Uitkering persoon 1', number_format($income['own_income'] ?? 0, 2, ',', '.')];
        $csvData[] = ['AOW persoon 1', number_format($income['own_aow'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Loon persoon 1', number_format($income['own_other_income'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Uitkering partner', number_format($income['wia_wife'] ?? 0, 2, ',', '.')];
        $csvData[] = ['AOW partner', number_format($income['aow_future'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Loon partner', number_format($income['partner_other_income'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Aanvullend pensioen', number_format($income['pension'] ?? 0, 2, ',', '.')];
        $csvData[] = [''];

        // Properties
        $csvData[] = ['HOOFDWONING ITALIË'];
        $csvData[] = ['Aankoopprijs', number_format($mainProperty['purchase_price'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Aankoopkosten %', number_format($mainProperty['purchase_costs_percentage'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Aankoopkosten €', number_format($mainProperty['purchase_costs'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Jaarlijkse kosten', number_format($mainProperty['annual_costs'] ?? 0, 2, ',', '.')];
        $csvData[] = [''];

        // Expenses
        $csvData[] = ['MAANDELIJKSE LASTEN'];
        $csvData[] = ['Energie', number_format($expenses['energy'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Water', number_format($expenses['water'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Internet', number_format($expenses['internet'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Zorgverzekering', number_format($expenses['health_insurance'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Auto verzekering', number_format($expenses['car_insurance'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Brandstof', number_format($expenses['car_fuel'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Auto onderhoud', number_format($expenses['car_maintenance'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Boodschappen', number_format($expenses['groceries'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Vrije tijd', number_format($expenses['leisure'] ?? 0, 2, ',', '.')];
        $csvData[] = ['Onvoorzien', number_format($expenses['unforeseen'] ?? 0, 2, ',', '.')];
        $csvData[] = [''];

        $renoModel = new \App\Models\RenovationItemModel();
        $renoItems = $renoModel->forUser($userId);
        if ($renoItems) {
            $csvData[] = ['VERBOUWING'];
            foreach ($renoItems as $item) {
                $csvData[] = [
                    $item['room'] . ' — ' . $item['title'],
                    number_format($renoModel->lineCost($item), 2, ',', '.'),
                    $item['status'],
                ];
            }
            $csvData[] = [''];
        }

        // B&B
        if ($bnbSettings && $bnbSettings['enabled']) {
            $csvData[] = ['B&B MODULE'];
            $csvData[] = ['Aantal kamers', $bnbSettings['number_of_rooms']];
            $csvData[] = ['Prijs per kamer per nacht', number_format($bnbSettings['price_per_room_per_night'] ?? 0, 2, ',', '.')];
            $csvData[] = ['Hoogseizoen bezetting %', number_format($bnbSettings['high_season_percentage'] ?? 0, 2, ',', '.')];
            $csvData[] = ['Laagseizoen bezetting %', number_format($bnbSettings['low_season_percentage'] ?? 0, 2, ',', '.')];
            $csvData[] = [''];
        }

        // Generate CSV
        $filename = 'emigratie_calculator_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';');
        }
        
        fclose($output);
        exit;
    }

    public function pdf()
    {
        $userId = (int) session()->get('userId');
        $result = Dashboard::analyzeForUser($userId);
        $profile = (new \App\Models\UserProfileModel())->where('user_id', $userId)->first();

        return view('export/pdf', [
            'title' => 'Financieel overzicht',
            'profile' => $profile,
            'calculations' => $result['calculations'],
            'yearlyProjections' => array_slice($result['yearlyProjections'], 0, 16),
            'warnings' => $result['warnings'],
        ]);
    }
}
