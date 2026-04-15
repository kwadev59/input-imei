<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter untuk mengecek apakah user sudah login via session.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Jika sudah login, izinkan akses
        if (session()->get('logged_in')) {
            return $request;
        }

        // 2. Jika tidak login, redirect ke halaman login
        // Jika request via AJAX/API, kirim JSON 401
        if (str_contains($request->getHeaderLine('Accept'), 'application/json')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized. Please login.']);
        }

        return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
