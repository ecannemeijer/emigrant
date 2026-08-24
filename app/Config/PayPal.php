<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PayPal extends BaseConfig
{
    public string $mode = 'sandbox';
    public string $clientId = '';
    public string $clientSecret = '';
    public string $webhookId = '';
}
