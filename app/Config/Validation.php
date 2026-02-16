<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Custom Rules
    // --------------------------------------------------------------------

    /**
     * Registration validation rules
     */
    public array $registration = [
        'username' => [
            'label'  => 'Username',
            'rules'  => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'errors' => [
                'required'   => '{field} is verplicht.',
                'is_unique'  => 'Deze {field} bestaat al.',
                'min_length' => '{field} moet minimaal {param} karakters zijn.',
            ],
        ],
        'email' => [
            'label'  => 'Email',
            'rules'  => 'required|valid_email|is_unique[users.email]',
            'errors' => [
                'required'    => '{field} is verplicht.',
                'valid_email' => 'Voer een geldig {field} adres in.',
                'is_unique'   => 'Dit {field} adres bestaat al.',
            ],
        ],
        'password' => [
            'label'  => 'Wachtwoord',
            'rules'  => 'required|min_length[8]',
            'errors' => [
                'required'   => '{field} is verplicht.',
                'min_length' => '{field} moet minimaal {param} karakters zijn.',
            ],
        ],
        'password_confirm' => [
            'label'  => 'Wachtwoord bevestiging',
            'rules'  => 'required|matches[password]',
            'errors' => [
                'required' => '{field} is verplicht.',
                'matches'  => '{field} komt niet overeen.',
            ],
        ],
    ];

    /**
     * Login validation rules
     */
    public array $login = [
        'email' => [
            'label'  => 'Email',
            'rules'  => 'required|valid_email',
            'errors' => [
                'required'    => '{field} is verplicht.',
                'valid_email' => 'Voer een geldig {field} adres in.',
            ],
        ],
        'password' => [
            'label'  => 'Wachtwoord',
            'rules'  => 'required',
            'errors' => [
                'required' => '{field} is verplicht.',
            ],
        ],
    ];
}
