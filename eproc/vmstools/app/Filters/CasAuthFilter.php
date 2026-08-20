<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Cas;

class CasAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pastikan session CI4 sudah aktif
        $session = session();
        $cas = new Cas();
        $cas->forceAuth();

        // Simpan username ke session CI4 hanya jika belum ada
        if (!$session->has('username')) {
            $session->set('username', $cas->getUser());
            $session->set('cas_attributes', $cas->getAttributes());
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak diperlukan
    }
}