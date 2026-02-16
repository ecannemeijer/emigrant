<?php

namespace App\Models;

use CodeIgniter\Model;

class BnbSettingModel extends Model
{
    protected $table            = 'bnb_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'enabled',
        'number_of_rooms',
        'price_per_room_per_night',
        'occupancy_rate',
        'high_season_percentage',
        'low_season_percentage',
        'high_season_months',
        'low_season_months'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    public function calculateMonthlyRevenue($userId)
    {
        $settings = $this->getByUserId($userId);
        
        if (!$settings || !$settings['enabled']) {
            return 0;
        }

        $rooms = $settings['number_of_rooms'];
        $pricePerNight = $settings['price_per_room_per_night'];
        $highSeasonPercent = $settings['high_season_percentage'] / 100;
        $lowSeasonPercent = $settings['low_season_percentage'] / 100;
        $highSeasonMonths = $settings['high_season_months'];
        $lowSeasonMonths = $settings['low_season_months'];

        // Calculate days per season
        $daysHighSeason = $highSeasonMonths * 30;
        $daysLowSeason = $lowSeasonMonths * 30;

        // Calculate occupied nights
        $highSeasonNights = $daysHighSeason * $highSeasonPercent * $rooms;
        $lowSeasonNights = $daysLowSeason * $lowSeasonPercent * $rooms;

        // Calculate yearly revenue
        $yearlyRevenue = ($highSeasonNights + $lowSeasonNights) * $pricePerNight;

        // Return monthly average
        return $yearlyRevenue / 12;
    }

    public function calculateYearlyRevenue($userId)
    {
        return $this->calculateMonthlyRevenue($userId) * 12;
    }
}
