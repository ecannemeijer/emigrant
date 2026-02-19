<?php

namespace App\Filters;

use App\Models\AuditLogModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuditFilter implements FilterInterface
{
    /**
     * Map URI patterns to human-readable action descriptions.
     */
    private array $actionMap = [
        // Auth
        'POST:login'                => 'Ingelogd',
        'GET:logout'                => 'Uitgelogd',
        'POST:register'             => 'Geregistreerd',
        'POST:password-reset'       => 'Wachtwoord reset aangevraagd',
        'POST:password-reset/reset' => 'Wachtwoord gewijzigd',

        // Dashboard
        'GET:dashboard'             => 'Dashboard bekeken',

        // Start position
        'GET:start-position'        => 'Startpositie bekeken',
        'POST:start-position/store' => 'Startpositie opgeslagen',

        // Income
        'GET:income'                => 'Inkomsten bekeken',
        'POST:income/store'         => 'Inkomsten opgeslagen',

        // Property
        'GET:property'              => 'Vastgoed bekeken',
        'POST:property/store'       => 'Vastgoed opgeslagen',

        // Expenses
        'GET:expenses'              => 'Maandlasten bekeken',
        'POST:expenses/store'       => 'Maandlasten opgeslagen',

        // Taxes
        'GET:taxes'                 => 'Belastingen bekeken',
        'POST:taxes/store'          => 'Belastingen opgeslagen',

        // B&B
        'GET:bnb'                   => 'B&B module bekeken',
        'POST:bnb/store'            => 'B&B instellingen opgeslagen',
        'POST:bnb/store-expenses'   => 'B&B kosten opgeslagen',

        // Scenarios
        'GET:scenarios'             => 'Scenario overzicht bekeken',
        'POST:scenarios/store'      => 'Scenario aangemaakt',
        'POST:scenarios/delete'     => 'Scenario verwijderd',

        // Profile
        'GET:profile'               => 'Profiel bekeken',
        'POST:profile/update'       => 'Profiel bijgewerkt',

        // Export
        'GET:export/csv'            => 'CSV export gedownload',
        'GET:export/pdf'            => 'PDF export gedownload',

        // Admin
        'GET:admin/users'           => 'Admin: gebruikerslijst bekeken',
        'POST:admin/users/store'    => 'Admin: nieuwe gebruiker aangemaakt',
        'POST:admin/users/update'   => 'Admin: gebruiker bijgewerkt',
        'POST:admin/users/delete'   => 'Admin: gebruiker verwijderd',
        'GET:admin/audit-logs'      => 'Admin: audit log bekeken',
        'POST:admin/audit-logs/clear' => 'Admin: audit log gewist',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // Do nothing before — logging happens after the response
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Only log for authenticated users
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return;
        }

        $method = strtoupper($request->getMethod());
        $uri    = trim($request->getUri()->getPath(), '/');

        // Skip internal/asset/AJAX-only paths
        $skipPrefixes = ['assets/', 'debugbar/', 'favicon'];
        foreach ($skipPrefixes as $prefix) {
            if (strpos($uri, $prefix) === 0) {
                return;
            }
        }

        // Only log GET page loads and all mutating requests
        $isGet = $method === 'GET';

        // For GET requests, only log meaningful pages (not every AJAX/fetch)
        if ($isGet && $response->getStatusCode() >= 300) {
            return; // skip redirects
        }

        $action = $this->resolveAction($method, $uri);

        // Skip unrecognized GET requests to reduce noise
        if ($isGet && $action === null) {
            return;
        }

        if ($action === null) {
            $action = "{$method}: /{$uri}";
        }

        $model = new AuditLogModel();
        $model->log(
            $action,
            $method,
            '/' . $uri,
            $session->get('userId'),
            $session->get('username') ?? $session->get('email') ?? 'onbekend',
        );
    }

    private function resolveAction(string $method, string $uri): ?string
    {
        // Exact match first
        $key = "{$method}:{$uri}";
        if (isset($this->actionMap[$key])) {
            return $this->actionMap[$key];
        }

        // Pattern match for dynamic segments (e.g. scenarios/view/3)
        $patterns = [
            '#^GET:scenarios/view/\d+$#'          => 'Scenario bekeken',
            '#^POST:scenarios/delete/\d+$#'        => 'Scenario verwijderd',
            '#^GET:admin/users/edit/\d+$#'         => 'Admin: gebruiker bewerken bekeken',
            '#^POST:admin/users/update/\d+$#'      => 'Admin: gebruiker bijgewerkt',
            '#^POST:admin/users/delete/\d+$#'      => 'Admin: gebruiker verwijderd',
            '#^GET:password-reset/reset/[a-z0-9]+$#i' => 'Wachtwoord reset pagina bezocht',
        ];

        foreach ($patterns as $pattern => $label) {
            if (preg_match($pattern, $key)) {
                return $label;
            }
        }

        return null;
    }
}
