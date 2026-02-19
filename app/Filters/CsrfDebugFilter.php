<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CsrfDebugFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Only log for POST requests to help root-cause CSRF mismatches
        if (strtoupper($request->getMethod()) !== 'POST') {
            return;
        }

        $tokenName = config('Security')->tokenName ?? 'csrf_token_name';
        $posted = $request->getPost($tokenName);

        $session = service('session');
        $sessionToken = $session->get($tokenName);
        $sessionId = $session->getId();

        log_message('debug', "CSRF-DBG: route=" . (string) current_url(false) . " method=" . $request->getMethod());
        log_message('debug', "CSRF-DBG: posted_token={$posted}");
        log_message('debug', "CSRF-DBG: session_id={$sessionId} session_token={$sessionToken}");
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
