<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Node\Stmt\Continue_;
use PHPUnit\Event\Test\ConsideredRisky;

class ValidasiRekananModel extends Model
{
    protected $table            = 'validasi_rekanan';
    protected $primaryKey       = 'rekanan_id';

    protected $allowedFields    = ['validate_by', 'validate_date'];
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    function getKBLI($rekanan_id) {
        $sql = "SELECT rbu.bidang_usaha_id, bu.nama as nama_kbli,rbu.validasi, rbu.validasi_date
                FROM rekanan_bidang_usaha rbu
                left join bidang_usaha bu on rbu.bidang_usaha_id = bu.bidang_usaha_id
                WHERE rbu.rekanan_id = ?
                and rbu.ijin_usaha_id=2 ";
        $builder = $this->db->query($sql, [$rekanan_id]);  
        return $builder->getResultArray();
    }
    function saveKBLIPenyedia($rekanan_id, $selectedKBLI) {
        // Hapus data KBLI yang sudah ada untuk rekanan ini
        //$this->db->table('rekanan_bidang_usaha')->where('rekanan_id', $rekanan_id)->delete();
        //print_r($selectedKBLI);exit;
        // Simpan KBLI yang dipilih
        foreach ($selectedKBLI as $kbli_id) {
            //print $kbli_id;continue;
            $data = [
                'validasi_date'=> date('Y-m-d H:i:s'),
                'validasi' => 1, // Asumsikan validasi berhasil, bisa disesuaikan dengan logika bisnis
                'validasi_by' => session()->get('user_id'), // Asumsikan ijin_usaha_id tetap 2 untuk semua KBLI
            ];
            //var_dump($data);continue;
            $this->db->table('rekanan_bidang_usaha')->where(['rekanan_id' => $rekanan_id, 'bidang_usaha_id' => $kbli_id])->update($data);
        }
        //exit;
        return true;
    }
    function pdfNIB($rekanan_id) {
        $sql = "SELECT
                 riu.path_file
                 from rekanan_ijin_usaha riu
                 WHERE riu.rekanan_id = ?
                 and riu.ijin_usaha_id=2 ";
        $builder = $this->db->query($sql, [$rekanan_id]);  
        $result =  $builder->getResultArray();
        return $result ? $result[0]['path_file'] : null;
    }
}

