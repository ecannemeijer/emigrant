<?php

/**
 * Calculate age from birthdate
 */
if (!function_exists('calculate_age')) {
    function calculate_age($birthdate)
    {
        if (empty($birthdate)) {
            return null;
        }
        
        $birth = new DateTime($birthdate);
        $today = new DateTime();
        return $today->diff($birth)->y;
    }
}

/**
 * Get personalization data (partner name, ages, etc.)
 */
if (!function_exists('get_personalization')) {
    function get_personalization($userId)
    {
        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel->where('user_id', $userId)->first();
        
        $result = [
            'partner_name' => $profile['partner_name'] ?? 'partner',
            'user_age' => null,
            'partner_age' => null,
            'retirement_age' => $profile['retirement_age'] ?? 67,
            'partner_retirement_age' => $profile['partner_retirement_age'] ?? 67,
        ];
        
        if (!empty($profile['date_of_birth'])) {
            $result['user_age'] = calculate_age($profile['date_of_birth']);
        }
        
        if (!empty($profile['partner_date_of_birth'])) {
            $result['partner_age'] = calculate_age($profile['partner_date_of_birth']);
        }
        
        return $result;
    }
}

/**
 * Replace "vrouw" and similar words with personalized names in text
 */
if (!function_exists('personalize_text')) {
    function personalize_text($text, $partnerName = null)
    {
        if (empty($partnerName)) {
            return $text;
        }
        
        $replacements = [
            'vrouw' => $partnerName,
            'Vrouw' => ucfirst($partnerName),
            'partner' => $partnerName,
            'Partner' => ucfirst($partnerName),
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}

/**
 * Calculate WaO reduction percentage based on emigration date
 * Dutch pension rights (AOW/WaO) are built up from age 15 to retirement age
 * 2% per year, so full rights = 50 years * 2% = 100%
 * 
 * @param string $emigrationDate Date of emigration
 * @param string $dateOfBirth Date of birth
 * @param int $retirementAge Retirement age (default 67)
 * @return float Percentage of full WaO (0-100)
 */
if (!function_exists('calculate_wao_percentage')) {
    function calculate_wao_percentage($emigrationDate, $dateOfBirth, $retirementAge = 67)
    {
        if (empty($emigrationDate) || empty($dateOfBirth)) {
            return 100.0; // No emigration date = assume full rights
        }
        
        $birthDate = new DateTime($dateOfBirth);
        $emigDate = new DateTime($emigrationDate);
        
        // Calculate age at emigration
        $ageAtEmigration = $birthDate->diff($emigDate)->y;
        
        // If emigrated at or after retirement age, full rights
        if ($ageAtEmigration >= $retirementAge) {
            return 100.0;
        }
        
        // Rights start building from age 15
        $startAge = 15;
        
        // Cannot have built up rights if emigrated before age 15
        if ($ageAtEmigration < $startAge) {
            return 0.0;
        }
        
        // Calculate percentage: (years worked in NL) / (total years) * 100
        $yearsInNL = $ageAtEmigration - $startAge;
        $totalYears = $retirementAge - $startAge;
        
        return ($yearsInNL / $totalYears) * 100;
    }
}
