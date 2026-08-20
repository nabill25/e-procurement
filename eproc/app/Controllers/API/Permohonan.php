<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PermohonanModel;

class Permohonan extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        // Require login
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->failUnauthorized('Silakan login terlebih dahulu');
        }

        $model = new PermohonanModel();
        $rawData = $model->getPengajuanSummary();

        // Map data to UI format
        $mappedData = array_map(function($row) {
            // Determine status based on posting and approval
            // Legacy mapping rules: 
            // posting = 0 -> draft, posting = 1 -> diajukan
            // approval = 1 -> disetujui (Tender)
            $status = 'draft';
            if ($row['approval'] === '1') {
                $status = 'disetujui';
            } elseif ($row['posting'] === '1') {
                $status = 'diajukan';
            }

            return [
                'id'              => $row['id'],
                'request_number'  => $row['request_number'] ?: 'PR-'.str_pad($row['id'], 4, '0', STR_PAD_LEFT),
                'title'           => $row['title'],
                'fiscal_year'     => $row['fiscal_year'] ?: date('Y'),
                'estimated_value' => (float)$row['estimated_value'],
                'unit_kerja'      => $row['unit_kerja'] ?: 'Unit Kerja Tidak Diketahui',
                'status'          => $status,
                'created_at'      => $row['created_at']
            ];
        }, $rawData);

        return $this->respond([
            'success' => true,
            'data'    => $mappedData
        ]);
    }
}
