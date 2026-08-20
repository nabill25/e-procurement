<?php

namespace App\Controllers;

use App\Models\ValidasiRekananModel;

class PenyediaController extends BaseController
{
    public function index()
    {
        // Cek apakah user sudah login (isLoggedIn = true)
        if (!session()->get('isLoggedIn')) {
            die('session hilang1');
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new ValidasiRekananModel();
        
        // Mengambil data user dari session CAS (Tahap 1)
        $data['user_logged'] = session()->get('username');
        
        // Ambil semua data penyedia
        $data['penyedia'] = $model->orderBy('nama', 'ASC')->findAll();
        $data['title'] = "Daftar Penyedia Barang/Jasa";

        return view('penyedia/index', $data);
    }
    public function proses_penyedia($id)
    {
        // Cek apakah user sudah login
        if (!session()->get('isLoggedIn')) {
            //die('session hilan2');
            die('session hilang2');
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil data penyedia berdasarkan ID
        $model = new ValidasiRekananModel();
        $data['penyedia'] = $model->find($id);
        $data['pdfNIB'] = $model->pdfNIB($id);
        if($data['penyedia']['validate_by'] == null){
            $datanya = array(
                'validate_by' => session()->get('username'),
                'update_at' => date('Y-m-d H:i:s')
            );
            //die(var_dump($datanya));
            $model->update($id,$datanya);
        }
        else if($data['penyedia']['validate_by'] != session()->get('username')){
            return redirect()->to('/penyedia')->with('error', 'Penyedia sudah diproses oleh user lain.');
        }

        //die(var_dump($data['penyedia'])); // Debug: Tampilkan data penyedia yang diambil
        if (!$data['penyedia']) {
            return redirect()->to('/penyedia')->with('error', 'Penyedia tidak ditemukan.');
        }

        $data['title'] = "Proses Penyedia: " . $data['penyedia']['nama'];
        return view('penyedia/gabungan', $data);
    }
    public function proses_kbli($id)
    {
        // Cek apakah user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        if ($this->request->getMethod() === 'POST') {
            $model = new ValidasiRekananModel();
            $status = $model->saveKBLIPenyedia($id, $this->request->getPost('selected') ?? []);
            // Lakukan proses penyimpanan atau update berdasarkan $selectedKBLI
            // Contoh: Simpan ke database atau lakukan logika bisnis lainnya
            // var_dump($selectedKBLI);exit; // Debug: Tampilkan KBLI yang dipilih
            return redirect()->to('/proses-kbli/'.$id)->with('success', 'KBLI berhasil diproses.');
        }
        // Ambil data penyedia berdasarkan ID
        $model = new ValidasiRekananModel();
        $data['penyedia'] = $model->find($id);
        $data['kbli'] = $model->getKBLI($id);
//var_dump($data['KBLI']);exit;
        //die(var_dump($data['penyedia'])); // Debug: Tampilkan data penyedia yang diambil
        if (!$data['penyedia']) {
            return redirect()->to('/penyedia')->with('error', 'Penyedia tidak ditemukan.');
        }

        $data['title'] = "Proses Penyedia: " . $data['penyedia']['nama'];
        return view('penyedia/proses', $data);
    }

}