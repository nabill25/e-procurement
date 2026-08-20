<?php

namespace App\Models;

use CodeIgniter\Model;

class PermohonanModel extends Model
{
    protected $table            = 'permohonan_paket';
    protected $primaryKey       = 'permohonan_paket_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    protected $allowedFields    = [
        'nama', 
        'nota_dinas', 
        'no_ppa', 
        'unit_kerja_id', 
        'nilai', 
        'tahun_anggaran', 
        'posting', 
        'approval',
        'created_date'
    ];

    /**
     * Get list of pengajuan with unit_kerja name
     */
    public function getPengajuanSummary()
    {
        $builder = $this->builder();
        $builder->select('
            permohonan_paket.permohonan_paket_id as id,
            permohonan_paket.no_ppa as request_number,
            permohonan_paket.nama as title,
            permohonan_paket.tahun_anggaran as fiscal_year,
            permohonan_paket.nilai as estimated_value,
            permohonan_paket.posting,
            permohonan_paket.approval,
            permohonan_paket.created_date as created_at,
            unit_kerja.nama as unit_kerja
        ');
        $builder->join('unit_kerja', 'unit_kerja.unit_kerja_id = permohonan_paket.unit_kerja_id', 'left');
        $builder->orderBy('permohonan_paket.created_date', 'DESC');
        $builder->limit(100);
        
        $query = $builder->get();
        return $query->getResultArray();
    }
}
