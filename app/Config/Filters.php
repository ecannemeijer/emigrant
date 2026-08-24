<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf_debug'    => \App\Filters\CsrfDebugFilter::class,
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'admin'         => \App\Filters\AdminFilter::class,
        'subscription'  => \App\Filters\SubscriptionFilter::class,
        'audit'         => \App\Filters\AuditFilter::class,
    ];

    /**
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps',
        ],
        'after' => [
            'performance',
        ],
    ];

    /**
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            'csrf' => ['except' => ['webhooks/paypal']],
            'invalidchars',
        ],
        'after' => [
            'secureheaders',
            'audit',
        ],
    ];

    public array $methods = [];

    public array $filters = [];

    public function __construct()
    {
        parent::__construct();

        if (ENVIRONMENT !== 'production') {
            $this->required['after'][] = 'toolbar';
            $this->globals['before'] = [
                'csrf_debug' => ['except' => ['webhooks/paypal']],
            ] + $this->globals['before'];
        }
    }
}
