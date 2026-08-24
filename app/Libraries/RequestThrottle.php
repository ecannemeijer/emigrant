<?php

namespace App\Libraries;

class RequestThrottle
{
    /**
     * @return bool False when the limit is exceeded
     */
    public static function allow(string $action, int $maxAttempts, int $periodSeconds, ?string $identity = null): bool
    {
        $throttler = service('throttler');
        $ip = service('request')->getIPAddress() ?: 'unknown';

        $ipKey = 't-' . $action . '-ip-' . md5($ip);
        if ($throttler->check($ipKey, $maxAttempts, $periodSeconds) === false) {
            return false;
        }

        if ($identity !== null && $identity !== '') {
            $idKey = 't-' . $action . '-id-' . md5(strtolower($identity));
            if ($throttler->check($idKey, $maxAttempts, $periodSeconds) === false) {
                return false;
            }
        }

        return true;
    }
}
