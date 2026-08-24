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
 * Calculate AOW percentage based on SVB 2%-per-year rule
 * (50 years before AOW age). Optional voluntary years after emigration.
 */
if (!function_exists('calculate_aow_percentage')) {
    function calculate_aow_percentage($emigrationDate, $dateOfBirth, $retirementAge = 67, $voluntaryYears = 0)
    {
        $calculator = new \App\Libraries\FinanceCalculator();

        return $calculator->calculateAowPercentage(
            $emigrationDate ?: null,
            $dateOfBirth ?: null,
            (int) $retirementAge,
            (float) $voluntaryYears
        );
    }
}

// Backward compatibility alias
if (!function_exists('calculate_AOW_percentage')) {
    function calculate_AOW_percentage($emigrationDate, $dateOfBirth, $retirementAge = 67)
    {
        return calculate_aow_percentage($emigrationDate, $dateOfBirth, $retirementAge, 0);
    }
}
