<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\RekananModel;

class Rekanan extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        // Require login for API access
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->failUnauthorized('Silakan login terlebih dahulu');
        }

        $rekananModel = new RekananModel();
        $rawVendors = $rekananModel->getVendorSummary();

        // Map data to match React UI expectations
        $mappedVendors = array_map(function($v) {
            // Mapping status_validasi ke string status UI
            // Di sistem CI3 lama: 2 biasanya berarti valid/terverifikasi
            $status = 'pending';
            if ($v['status_validasi'] == 2) {
                $status = 'terverifikasi';
            } elseif ($v['status_validasi'] == 3) {
                $status = 'ditangguhkan';
            } elseif ($v['status_validasi'] == 4) {
                $status = 'diblokir';
            }

            return [
                'id'           => $v['id'],
                'company_name' => $v['company_name'],
                'npwp'         => $v['npwp'] ?: '-',
                'city'         => $v['city'] ?: 'Belum diisi',
                'category'     => 'Umum', // Placeholder until joined with bidang_usaha
                'score'        => rand(30, 50) / 10, // Placeholder mock score 3.0-5.0
                'status'       => $status
            ];
        }, $rawVendors);

        return $this->respond([
            'success' => true,
            'data'    => $mappedVendors
        ]);
    }
}
