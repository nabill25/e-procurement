<?php

namespace App\Models;

use CodeIgniter\Model;

class RekananModel extends Model
{
    protected $table            = 'rekanan';
    protected $primaryKey       = 'rekanan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    // Columns that can be fetched safely
    protected $allowedFields    = [
        'nama', 
        'npwp', 
        'kota', 
        'status_validasi', 
        'tanggal_daftar'
    ];

    /**
     * Get summary of vendors for the UI
     */
    public function getVendorSummary()
    {
        // For phase 5, we keep it simple. We can expand to JOIN rekanan_bidang_usaha later
        $builder = $this->builder();
        $builder->select('rekanan_id as id, nama as company_name, npwp, kota as city, status_validasi');
        $builder->orderBy('tanggal_daftar', 'DESC');
        $builder->limit(100); // Limit to 100 for performance during dev
        
        $query = $builder->get();
        return $query->getResultArray();
    }
}
