<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TokenAuth implements FilterInterface
{
    /**
     * Validasi Bearer Token dari header Authorization.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Ambil header Authorization
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            return $this->failUnauthorized('Authorization header missing');
        }

        // 2. Ekstrak Bearer token
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->failUnauthorized('Invalid Authorization format. Use: Bearer YOUR_TOKEN');
        }

        $token = $matches[1];
        $expected = env('PRINT_AGENT_KEY', ''); // Gunakan key yang ada di .env

        // 3. Validasi token
        if (empty($expected) || $token !== $expected) {
            return $this->failUnauthorized('Invalid Bearer Token');
        }

        // Token valid, lanjutkan request
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }

    private function failUnauthorized(string $message)
    {
        return service('response')
            ->setStatusCode(401)
            ->setJSON([
                'status'  => 'error',
                'message' => $message
            ]);
    }
}
