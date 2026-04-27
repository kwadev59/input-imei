<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');
        $validKey = env('API_KEY', 'default_secret_key_123');

        if (empty($apiKey) || $apiKey !== $validKey) {
            $response = Services::response();
            $response->setStatusCode(401);
            return $response->setJSON([
                'status' => 401,
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing API Key'
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
