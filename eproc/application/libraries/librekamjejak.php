<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

class librekamjejak
{
    private $_CI;
    /*
      Posisi :
      '1' => 'Input Permohonan Paket',
      '2' => 'Teruskan Usulan Kebutuhan',
      '3' => 'Approve Usulan Kebutuhan',
      '4' => 'Kembalikan/Tolak Usulan Kebutuhan',
      '5' => 'Teruskan Permohonan Paket ke Kepala Pengadaan',
      '6' => 'Approve Permohonan Paket',
      '7' => 'Kembalikan/Tolak Permohonan Paket',
      '8' => 'Membuat Paket',
      '9' => 'Update Paket',
      '10' => 'Membuat jadwal',
      '11' => 'Reschedule Jadwal',
      '12' => 'Upload Dokumen Pengadaan',
      '121' => 'Upload Dokumen Kualifikasi',
      '13' => 'Input Syarat Dokumen Penawaran',
      '131' => 'Input Syarat Kualifikasi',
      '14' => 'Penambahan Tim Pengadaan',
      '15' => 'Publish Paket',
      '151' => 'Un-Publish Paket',
      '16' => 'Validasi Paket',
      '17' => 'Publish Pembukaan Penawaran',
      '181' => 'Evaluasi Dokumen Kualifikasi',
      '18' => 'Evaluasi Administrasi',
      '19' => 'Evaluasi Teknis',
      '20' => 'Evaluasi Harga & Koreksi Aritmatik',
      '21' => 'Publish Pembukaan Penawaran 2',
      '22' => 'Publish Negosiasi',
      '23' => 'Mulai eReverse Auction',
      '24' => 'Publish Penetapan Pemenang',
      '25' => 'Pengumuman Pemenang',
      '26' => 'Batalkan Paket',
      '27' => 'Upload Dokumen Laporan',
      '28' => 'Ulang Paket',
      '29' => 'Kirim Undangan Pengadaan',
      '30' => 'Penunjukan Pengelola Kontrak',
      '101' => 'Input Usulan Kebutuhan',
      '102' => 'Kembalikan/Tolak Usulan Kebutuhan',
      '103' => 'Verifikasi Usulan Kebutuhan', // Terverifikasi
      '104' => 'Validasi Perencana Usulan Kebutuhan', // Berhasil Validasi
      '105' => 'Validasi Keuangan Usulan Kebutuhan', // Berhasil Validasi
      '106' => 'Approval PKPA Usulan Kebutuhan', // Berhasil Approve
      '107' => 'Approval KPA Usulan Kebutuhan', // Berhasil Approve
      '108' => 'Update Rencana Pengadaan',
      '250' => 'Input SPPBJ',
      '251' => 'Kirim SPPBJ ke Penyedia',
    */

    function __construct()
    {
      $this->_CI =& get_instance();
      $this->_CI->load->library('kauth');
      $this->_CI->USER_LOGIN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
      $this->_CI->USER_LOGIN      =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN;
      $this->_CI->USER_NAMA       =  $this->_CI->kauth->getInstance()->getIdentity()->USER_NAMA;
      $this->_CI->USER_TYPE_ID    =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
      $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    }

    // Insert Rekam Jejak
    /*
    $this->load->library("librekamjejak");
    $this->librekamjejak->insertRJ('1-22','','null','null');
    // param 1: Posisi/'1-22', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null', param 5: flow, param 6: Contractingrekananid
    */
    // End Insert Rekam Jejak

    function insertRJ($posisi,$keterangan=null,$paketid=null,$permohonanid=null,$flow=null,$conRekId=null)
    {
      $this->_CI->load->model("Rekamjejak");
      $rekamjejak = new Rekamjejak();
      $rekamjejakCek = new Rekamjejak();

      if ($conRekId) {
        $conRekId = $conRekId;
      } else {
        $conRekId = 0;
      }

      $createdby = $this->_CI->USER_LOGIN_ID ?: 2; // pakai defaul Administrator untuk yang insert pakai mesin API
      $usertype = $this->_CI->USER_TYPE_ID ?: 1; // pakai Administrator untuk yang insert pakai mesin API

      // macam2 error halaman https://ruanglaptop.com/kode-error-internet/
      $rekamjejak->setField("POSISI", $this->__getPosisi($posisi));
      $rekamjejak->setField("KETERANGAN", str_replace("'","''",strip_tags($keterangan)));
      $rekamjejak->setField("CREATED_BY", $createdby);
      $rekamjejak->setField("CREATED_DATE", date('Y-m-d H:i:s'));
      $rekamjejak->setField("USER_TYPE", $usertype);
      $rekamjejak->setField("PAKET_ID", $paketid);
      $rekamjejak->setField("PERMOHONAN_PAKET_ID", $permohonanid);
      $rekamjejak->setField("BROWSER", $this->_CI->HTTP_USER_AGENT);
      $rekamjejak->setField("FLOW", $flow);
      $rekamjejak->setField("CONTRACTINGREKANANID", $conRekId);

      /*
      if ($paketid) { // cari berdasarkan paketid
        // echo "paketid kosong";
        $cekInputan = $rekamjejakCek->getCountByParams(array('A.POSISI' => $this->__getPosisi($posisi), 'A.CREATED_BY' => $this->_CI->USER_LOGIN_ID, 'A.PERMOHONAN_PAKET_ID' => $permohonanid));
      } else { // cari berdasarkan permohonanid/permohonan_paket_analisa_id
        // echo "paketid ada";
        $cekInputan = $rekamjejakCek->getCountByParams(array('A.POSISI' => $this->__getPosisi($posisi), 'A.CREATED_BY' => $this->_CI->USER_LOGIN_ID, 'A.PAKET_ID' => $paketid ));

      }
      // echo $cekInputan;
      $ins = false;
      if ($cekInputan < 1) {
        $ins = $rekamjejak->insert();
      }

      */
        $ins = $rekamjejak->insert();
        // echo $rekamjejak->query; die();
      if ($ins) {
        return true;
      } else {
        return false;
      }
    }

    function __getPosisi($posisi)
    {

      $posisiArray
      = array(
            // New Rekam Jejak
            '210' => 'Teruskan Usulan Paket',
            '1011' => 'RUP di teruskan ke Kasubdit Perencana',
            '1012' => 'Diteruskan ke Kasubdit Perencana',
            '1013' => 'Kembalikan/Tolak Paket ke Perencanaan',
            '511' => 'Approve PPK',
            '512' => 'Kaji Ulang Selesai',
            '611' => 'Teruskan ke PPK',
            '711' => 'Kembalikan/Tolak Usulan Paket', 
            '811' => 'Kirim Ulang Usulan Paket', 
            '90' => 'PPK meneruskan paket proses kontrak', 
            '901' => 'Penunjukan PIC Kontrak', 
            '902' => 'SPPBJ Proses Approval Kasubdit', 
            '903' => 'SPPBJ Proses Approval PPK', 
            '904' => 'PPK telah menyetujui SPPBJ', 
            '905' => 'Teruskan kontrak ke Kasubdit', 
            '906' => 'Kasubdit menolak', 
            '907' => 'Meneruskan paket ke PPK', 
            '908' => 'Kasubdit menolak SPPBJ', 
            '909' => 'PPK menolak SPPBJ', 
            '910' => 'Penunjukan PIC Pengendali Kontrak', 
            '3501' => 'Negosiasi Item dikirim ke Penyedia',
            '3502' => 'Negosiasi Item dikembalikan oleh Penyedia',
            '3503' => 'Negosiasi Item Diterima oleh penyedia',
            '3504' => 'Negosiasi Item Diterima oleh pjp',
            '3505' => 'Negosiasi Item Ditolak oleh penyedia',
            '3511' => 'Penyedia setuju',
            '3512' => 'Penyedia menolak',

            '1' => 'Input Permohonan Paket',
            '2' => 'Teruskan Usulan Kebutuhan',
            '3' => 'Approve Usulan Kebutuhan',
            '4' => 'Kembalikan/Tolak Usulan Kebutuhan',
            '5' => 'Teruskan Permohonan Paket ke Kepala Pengadaan',
            '6' => 'Penunjukan PIC',
            '7' => 'Kembalikan/Tolak Permohonan Paket',
            '8' => 'Membuat Paket',
            '91' => 'Update Sistem Negosiasi',
            '9' => 'Update Paket',
            '10' => 'Membuat jadwal',
            '11' => 'Reschedule Jadwal',
            '12' => 'Upload Dokumen Pengadaan',
            '121' => 'Upload Dokumen Kualifikasi',
            '13' => 'Input Syarat Dokumen Penawaran',
            '131' => 'Input Syarat Kualifikasi',
            '14' => 'Penambahan Tim Pengadaan',
            '15' => 'Publish Paket',
            '151' => 'Un-Publish Paket',
            '16' => 'Validasi Paket',
            '161' => 'Validasi Hasil Kualifikasi',
            '17' => 'Publish Pembukaan Penawaran',
            '181' => 'Evaluasi Dokumen Kualifikasi',
            '18' => 'Evaluasi Administrasi',
            '19' => 'Evaluasi Teknis',
            '20' => 'Evaluasi Harga & Koreksi Aritmatik',
            '21' => 'Publish Pembukaan Penawaran 2',
            '22' => 'Publish Negosiasi',
            '23' => 'Mulai eReverse Auction',
            '24' => 'Publish Penetapan Pemenang',
            '241' => 'Batal Publish Penetapan Pemenang',
            '25' => 'Pengumuman Pemenang',
            '26' => 'Batalkan Paket',
            '27' => 'Upload Dokumen Laporan',
            '28' => 'Ulang Paket',
            '29' => 'Kirim Undangan Pengadaan',
            '30' => 'Penunjukan Pengelola Kontrak',
            '101' => 'Input Usulan Kebutuhan',
            '102' => 'Kembalikan/Tolak Usulan Kebutuhan',
            '103' => 'Verifikasi Usulan Kebutuhan', // Terverifikasi
            '104' => 'Validasi Perencana Usulan Kebutuhan', // Berhasil Validasi
            '105' => 'Validasi Keuangan Usulan Kebutuhan', // Berhasil Validasi
            '106' => 'Approval PKPA Usulan Kebutuhan', // Berhasil Approve
            '107' => 'Approval KPA Usulan Kebutuhan', // Berhasil Approve
            '108' => 'Update Rencana Pengadaan',
            '250' => 'Input/Edit SPPBJ',
            '2501' => 'Input Non-SPPBJ',
            '251' => 'Kirim SPPBJ ke Penyedia',
            '252' => 'Konfirmasi SPPBJ oleh Penyedia',
            '253' => 'Input/Edit Kontrak',
            '254' => 'Input/Edit Deliverable Pekerjaan',
            '255' => 'Input/Edit Temin Pembayaran',
            '256' => 'Approve Kontrak',
            '257' => 'Kirim Kontrak ke Penyedia',
            '258' => 'Setujui Kontrak oleh Penyedia',
            '259' => 'Dikembalikan Kontrak oleh Penyedia',
            '260' => 'Persiapan menjadi Pengendali Kontrak',
            '261' => 'Input/Edit SPMK',
            '262' => 'Input/Edit Barang Jasa Kontrak',
            '263' => 'Update Realisasi Pekerjaan',
            '2631' => 'Update (BAPP) Realisasi Pekerjaan',
            '264' => 'Update Termin Pembayaran',
            '265' => 'Pelaksanaan Kontrak menjadi Penutupan Kontrak',
            '266' => 'Input/Edit Penilaian Kinerja',
            '267' => 'Penyelesaian menjadi Selesai Kontrak',
            '268' => 'Update data BAST Pekerjaan',
            '269' => 'Update data BAST Masa Pemeliharaan',
            '270' => 'Input/Edit Surat Pesanan',
            '271' => 'Update data Realisasi Pekerjaan',
            '272' => 'Perubahan Kontrak',
            '2721' => 'Penyedia Upload Dok. Addendum',
            '273' => 'Penyesuaian Harga',
            '274' => 'Keadaan Kahar',
            '275' => 'Berakhir Kontrak',
            '276' => 'Pemutusan Kontrak',
            '277' => 'Pemberian Kesempatan Kontrak',
            '278' => 'Denda dan Ganti Rugi',
            '279' => 'Input/Edit Catatan Kontrak',
            '350' => 'Negosiasi dengan Penyedia',
            '351' => 'Penyedia telah menyetujui Negosiasi',
            '352' => 'Upload Surat Pesanan',
            '353' => 'Pesanan Diproses',
            '354' => 'Pesanan Diterima',
            '355' => 'Menghapus Item di Keranjang',
            '356' => 'Mengulangi Proses Pemilihan Produk',
            '357' => 'Pesanan Dikirim',
            '358' => 'Pesanan Diterima',
            '359' => 'Upload Dokumen',
            '360' => 'Penunjukan Vendor',
            '361' => 'Input Jaminan Pemeliharaan',
            );

      foreach ($posisiArray as $key => $value) {
        if ($key == $posisi) {
          $convertPosisi = $value;
        }
      }
      return $convertPosisi;
    }

    function buttonRJ($id=null)
    {
      /*
      Put on view
      $this->load->library("librekamjejak"); $librekamjejak = new librekamjejak();
      echo $librekamjejak->buttonRJ($usulanId);
      */

      return '<a onclick="openAddLg(\'main/loadUrl/main/rekam_jejak_view?id='.$id.'\')" class="btn round btn-min-width box-shadow-1 btn-danger mr-1 text-white" style="padding:.4rem 1rem"> <i class="fa fa-clock-o"></i> Rekam Jejak </a> ';
    }

    function buttonRJContract($id=null)
    {
      return '<a onclick="openAddLg(\'main/loadUrl/main/rekam_jejak_view?conRekId='.$id.'\')" class="btn round btn-min-width box-shadow-1 btn-dark mr-1 text-white"> <i class="fa fa-paw"></i> Rekam Jejak </a> ';
    }

    function viewRJ($id=null,$paketid=null) // id:PermohonanID, Paketid:paketid
    {
      include_once("functions/date.func.php");
      $this->_CI->load->model("Rekamjejak");
      $rekamjejak = new Rekamjejak();
      $rekamjejakCount = new Rekamjejak();
      if ($paketid) {
        // $rekamjejak->selectByParamsOR(array("A.PAKETID_PERMOHONANID" => "".$paketid.",".$id."" ));
        $rekamjejak->selectByParamsOR2($id,$paketid);
        // echo $rekamjejak->query; die;
      } else {
        $rekamjejak->selectByParamsOR2($id);
        // $rekamjejak->selectByParamsOR(array("A.PERMOHONAN_PAKET_ID|| IN " => "(".$id.")" ));
      }
      // echo $rekamjejak->countRow();
      // echo $rekamjejak->query; die();
      // $cekDataCount = $rekamjejakCount->getCountByParams(array('A.PAKET_ID' => $paketid, "A.PERMOHONAN_PAKET_ID|| IN " => "(".$id.")" ));
      // echo $rekamjejakCount->query; die();
      // $rekamjejak->firstRow();
      // $rekamid = $rekamjejak->getField("REKAM_ID");

      $html   = '';
      $html  .= '<h4 style="margin-top: 0%; text-align: center"><i><b>REKAM JEJAK</b></i></h4><br>
                <div  style="height: 500px; overflow: scroll; padding: 0 5px">
                  <table class="table table-bordered" width="100%">
                    <!-- <tr><th width="5%">No</th><th width="60%">Kegiatan</th><th width="20%">User</th><th width="12%">Tanggal</th></tr> --!>
                    <tr><th width="80%">Kegiatan</th></tr>';
      $no=1;
      if ($rekamjejak->countRow() > 0) {
        while($rekamjejak->nextRow())
        {
            $dateTime = explode(' ', dateTimeToPageCheck($rekamjejak->getField("CREATED_DATE")));
            $html  .=     '<tr>
                            <td>
                              '.$rekamjejak->getField("POSISI").'<br>';
            if ($rekamjejak->getField("POSISI") == 'Teruskan Permohonan Paket ke Kepala Pengadaan') {
            $html .=           '<span style="font-size:10px">Automatis Update PR Code';
            } else {
              // if ($this->_CI->USER_TYPE_ID == '1' || $this->_CI->USER_TYPE_ID == '10') { // 1:Super Admin, 10:Audit
                $html .=           '<span style="font-size:10px">'.$rekamjejak->getField("USER_NAMA").' '.$rekamjejak->getField("USER_TYPE_STR").'';
              // }
            }
                        if ($rekamjejak->getField("LEVEL")) {
                          $html .= '<i><b> ('.$rekamjejak->getField("LEVEL").')</b></i>';
                        }

            $html .=      '        </span>';
            $html  .=     '    <p class="pull-right" style="top:-20px; position:relative; font-size:11px; right: -20px; margin:0px !important">'.getFormattedDateShort2($dateTime[0]).' <span class="fa fa-clock-o"></span> '.$dateTime[1].'</p>';
                          if ($rekamjejak->getField("KETERANGAN")) {
            $html  .=     '     <p style="line-height:1em; margin:0px !important;">
                                  <span style="font-size:10px;"> <b>Catatan/Keterangan:</b><br> <i style="color:blue">'.$rekamjejak->getField("KETERANGAN").'</i></span>
                                </p>';
                          }
            $html  .=     '    </td>
                            <!-- <td></td> -->
                          </tr>';
            // $html  .=     '<tr>
            //                 <td>'.$no.'</td>
            //                 <td>'.$rekamjejak->getField("POSISI").'</td>
            //                 <td>'.$rekamjejak->getField("USER_NAMA").' <br> <small>'.$rekamjejak->getField("USER_TYPE_STR").'</small></td>
            //                 <td>'.getFormattedDateShort2($dateTime[0]).'<br> <small>'.$dateTime[1].'</small></td>
            //               </tr>';
          $no++;
        }
      } else {
        $html  .=     '<tr> <td colspan="4" style="text-align:center">Belum ada rekam jejak</td></tr>';
      }

      $html  .=   '</table>
                  </div>';
      return $html;
    }

    function viewRJContract($conRekId) // id:PermohonanID, Paketid:paketid
    {
      include_once("functions/date.func.php");
      $this->_CI->load->model("Rekamjejak");
      $rekamjejak = new Rekamjejak();
      $rekamjejakCount = new Rekamjejak();
      $rekamjejak->selectByParamsOR(array("CONTRACTINGREKANANID" => $conRekId));

      $html   = '';
      $html  .= '<h4 style="margin-top: 0%; text-align: center"><i><b>REKAM JEJAK</b></i></h4><br>
                <div  style="height: 500px; overflow: scroll; padding: 0 5px">
                  <table class="table table-bordered" width="100%">
                    <!-- <tr><th width="5%">No</th><th width="60%">Kegiatan</th><th width="20%">User</th><th width="12%">Tanggal</th></tr> --!>
                    <tr><th width="80%">Kegiatan</th></tr>';
      $no=1;
      if ($rekamjejak->countRow() > 0) {
        while($rekamjejak->nextRow())
        {
            $dateTime = explode(' ', dateTimeToPageCheck($rekamjejak->getField("CREATED_DATE")));
            $html  .=     '<tr>
                            <td>
                              '.$rekamjejak->getField("POSISI").'<br>
                                <span style="font-size:10px">'.$rekamjejak->getField("USER_NAMA").' '.$rekamjejak->getField("USER_TYPE_STR").'';
                        if ($rekamjejak->getField("LEVEL")) {
                          $html .= '<i><b> ('.$rekamjejak->getField("LEVEL").')</b></i>';
                        }

            $html .=      '        </span>';
            $html  .=     '    <p class="pull-right" style="top:-20px; position:relative; font-size:11px; right: -20px; margin:0px !important">'.getFormattedDateShort2($dateTime[0]).' <span class="fa fa-clock-o"></span> '.$dateTime[1].'</p>';
                          if ($rekamjejak->getField("KETERANGAN")) {
            $html  .=     '     <p style="line-height:1em; margin:0px !important;">
                                  <span style="font-size:10px;"> <b>Catatan/Keterangan:</b><br> <i style="color:blue">'.$rekamjejak->getField("KETERANGAN").'</i></span>
                                </p>';
                          }
            $html  .=     '    </td>
                            <!-- <td></td> -->
                          </tr>';
            // $html  .=     '<tr>
            //                 <td>'.$no.'</td>
            //                 <td>'.$rekamjejak->getField("POSISI").'</td>
            //                 <td>'.$rekamjejak->getField("USER_NAMA").' <br> <small>'.$rekamjejak->getField("USER_TYPE_STR").'</small></td>
            //                 <td>'.getFormattedDateShort2($dateTime[0]).'<br> <small>'.$dateTime[1].'</small></td>
            //               </tr>';
          $no++;
        }
      } else {
        $html  .=     '<tr> <td colspan="4" style="text-align:center">Belum ada rekam jejak</td></tr>';
      }

      $html  .=   '</table>
                  </div>';
      return $html;
    }

    function viewRJCetak($id=null,$paketid=null)
    {
      include_once("functions/date.func.php");
      $this->_CI->load->model("Rekamjejak");
      $rekamjejak = new Rekamjejak();
      $rekamjejakCount = new Rekamjejak();
      if ($paketid) {
        $rekamjejak->selectByParamsOR(array("A.PAKETID_PERMOHONANID" => "".$paketid.",".$id."" ));
      } else {
        $rekamjejak->selectByParamsOR(array("A.PERMOHONAN_PAKET_ID|| IN " => "(".$id.")" ));
      }

      $html   = '';
      $html  .= '<div  style="height: 500px; overflow: scroll; padding: 0 5px">
                  <table class="table table-bordered">
                    <tr class="tr"><th class="tdno" width="100%">Kegiatan</th></tr>';
      $no=1;
      if ($rekamjejak->countRow() > 0) {
        while($rekamjejak->nextRow())
        {
            $dateTime = explode(' ', dateTimeToPageCheck($rekamjejak->getField("CREATED_DATE")));
            $html  .=     '<tr>
                            <td class="td">
                              '.$rekamjejak->getField("POSISI").' <i style="font-size:8px">'.getFormattedDateShort2($dateTime[0]).' <span class="fa fa-clock-o"></span> '.$dateTime[1].'</i><br>
                                <span style="font-size:10px">'.$rekamjejak->getField("USER_NAMA").' '.$rekamjejak->getField("USER_TYPE_STR").'</span>';
                          if ($rekamjejak->getField("KETERANGAN")) {
            $html  .=     '     <p style="line-height:1em; margin:0px !important">
                                  <span class="badge badge-danger" style="font-size:9px"> Keterangan: <i>'.$rekamjejak->getField("KETERANGAN").'</i></span>
                                </p>';
                          }
            $html  .=     '    </td>
                            <!-- <td></td> -->
                          </tr>';
          $no++;
        }
      } else {
        $html  .=     '<tr> <td colspan="4" style="text-align:center">Belum ada rekam jejak</td></tr>';
      }

      $html  .=   '</table>
                  </div>';
      return $html;
    }

    function statusPerencanaan($status,$statusid)
    {
      $html = '';
      switch ($statusid) {
        case '0':
          $html  .= '<span class="badge badge-success" style="font-size:10px">'.$status.'</span>';
          break;

        case '1':
          $html  .= '<span class="badge badge-primary" style="font-size:10px">'.$status.'</span>';
          break;

        case '2':
        case '3241':
        case '3251':
        case '41242':
        case '42251':
        case '51252':
        case '':
          $html  .= '<span class="badge badge-danger" style="font-size:10px">'.$status.'</span>';
          break;

        case '3':
        case '41':
        case '42':
        case '51':
        case '52':
          $html  .= '<span class="badge badge-info" style="font-size:10px">'.$status.'</span>';
          break;

        default:
          // code...
          break;
      }

      return $html;
    }
}
