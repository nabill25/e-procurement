<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model(array("Contracting","Queryfree","PaketNegoisasi","PaketRekanan")); 

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_kontrak.xls");

$contracting = new Contracting();

$reqTahun = $this->input->get("reqTahun");

$dataExport = new Queryfree();
$dataExport->selectByParams("SELECT A.REKANAN_ID, A.REKANAN_ID_STR, A.PAKET_ID,B.NAMA, B.CONTRACTINGPROSESID, B.CONTRACTINGPROSESID_STR, B.CONTRACTINGSTATUSKONTRAKID, B.CONTRACTING_STATUS_KONTRAK,
B.NILAI, B.CR_NILAI_KONTRAK, B.CR_SPPBJ_NILAI, B.NILAI_HPS_PR, B.NILAI_RAB_PR, B.JNS_KONTRAK_STR, A.CR_METODE_PEMBAYARAN_STR, B.TAHUN, B.TAHUN_SPPBJ, B.PAKET_METODE_LELANG,
B.SA, B.DPSJ, B.LIST_KEGIATAN, B.KODE_RUP, B.KODE_PR, B.CR_PO, B.SIRUP_ID, B.UNIT_KERJA_ID, B.TOTAL_SKOR, B.GRADE, B.SUMBER_DANA_KETERANGAN, B.NILAI_MATA_UANG,
B.PANITIA, B.PANITIA_STR, B.PENGGUNA, B.PENGGUNA_STR, B.PIC_KONTRAK, B.PIC_KONTRAK_STR, B.PIC_PENGENDALI, B.PIC_PENYELESAIAN,
A.CR_SPPBJ_CODE, A.CR_SPPBJ_TANGGAL
FROM VIEW_CONTRACTING_REKANAN A 
JOIN VIEW_CONTRACTING_PAKET B ON A.PAKET_ID=B.PAKET_ID");

$backBlue = 'style="background-color: #0e3a80; color: #fff"';
$backGreen = 'style="background-color: #08820e; color: #fff"';
$backOrange = 'style="background-color: #c77a06; color: #fff"';

?>
<!doctype html>
<html>
<style type="text/css">
  tr td { border: 1px solid #000; }
</style>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
	</head>
	<body>
     <table class="table table-bordered table-hover">
      <tr>
        <td>...</td>
      </tr>
      <tr>
        <td <?= $backBlue ?>>Nomor</td>  
        <td <?= $backBlue ?>>2.1 NO FOLDER</td>
        <td <?= $backBlue ?>>2.1 TGL LAP PEMILIHAN"</td>
        <td <?= $backBlue ?>>2.1 TANGGAL CREATE PO</td>
        <td <?= $backBlue ?>>2 PR</td>
        <td <?= $backBlue ?>>PO</td>
        <td <?= $backBlue ?>>DPSJ</td>
        <td <?= $backBlue ?>>Nama Paket Pengadaan</td>
        <td <?= $backBlue ?>>PENGADAAN UMUM/PENGADAAN KHUSUS/NILAI DIBAWAH 25 JUTA (ASSET/INV)</td>
        <td <?= $backBlue ?>>PIC PEMILIHAN2/PIC PERENCANA</td>
        <td <?= $backBlue ?>>NAMA KEGIATAN</td>
        <td <?= $backBlue ?>>METODE PEMILIHAN</td>
        <td <?= $backBlue ?>>JENIS PENGADAAN </td>
        <td <?= $backBlue ?>>JENIS KONTRAK</td>
        <td <?= $backBlue ?>>KONTRAK</td>
        <td <?= $backBlue ?>>NAMA PENYEDIA</td>
        <td <?= $backBlue ?>>CARA PEMBAYARAN</td>
        <td <?= $backBlue ?>>SUMBER DANA</td>
        <td <?= $backBlue ?>>MATA UANG</td>
        <td <?= $backBlue ?>>2/2.1 NILAI HPS PLUS PPN (NILAI PR)</td>
        <td <?= $backBlue ?>>2.1 NILAI HASIL PEMILIHAN</td>
        <td <?= $backGreen ?>>2.1 EFISIENSI HPS VS PEMILIHAN</td>
        <td <?= $backBlue ?>>NILAI KONTRAK AWAL (+PPN)</td>
        <td <?= $backBlue ?>>PIC 2.1</td>
        <td <?= $backBlue ?>> NILAI ADENDUM 1 </td>
        <td <?= $backBlue ?>> NILAI ADENDUM 2 </td>
        <td <?= $backBlue ?>> NILAI ADENDUM 3 </td>
        <td <?= $backBlue ?>> NILAI ADENDUM 4 </td>
        <td <?= $backGreen ?>>NILAI KONTRAK AKHIR SETELAH ADENDUM PLUS PPN (NILAI PO)</td>
        <td <?= $backGreen ?>>EFISIENSI PEMILIHAN VS KONTRAK (ADENDUM)</td>
        <td <?= $backGreen ?>>EFISIENSI HPS S.D KONTRAK (ADENDUM)</td>
        <td <?= $backBlue ?>>KETERANGAN (Sebutkan Ket Adendum)</td>
        <td <?= $backBlue ?>>NO. KONTRAK / ADDENDUM</td>
        <td <?= $backBlue ?>>TGL RAPAT e-Purchasing/TGL KAJUL PAKET</td>
        <td <?= $backBlue ?>>TANGGAL RANCANGAN KONTRAK</td>
        <td <?= $backBlue ?>>TGL KONTRAK/ADDENDUM</td>
        <td <?= $backBlue ?>>2.1 TGL MULAI KONTRAK</td>
        <td <?= $backBlue ?>>REMINDER JADWAL KIRIM/PENYELESAIAN PEKERJAAN</td>
        <td <?= $backBlue ?>>TGL PENGIRIMAN BARANG/JASA</td>
        <td <?= $backBlue ?>>TANGGAL  BATAS PENYELESAIAN ADMINISTRASI</td>
        <td <?= $backBlue ?>>2.1 TGL AKHIR KONTRAK</td>
        <td <?= $backGreen ?>>2.1 WAPEL</td>
        <td <?= $backBlue ?>>TGL EMAIL SETUP/UPDATE SUPPLIER</td>
        <td <?= $backBlue ?>>SPPBJ</td>
        <td <?= $backBlue ?>>BG</td>
        <td <?= $backBlue ?>>NO BG</td>
        <td <?= $backBlue ?>>NILAI BG</td>
        <td <?= $backBlue ?>>MASA BERLAKU (MULAI)</td>
        <td <?= $backBlue ?>>MASA BERLAKU (AKHIR)</td>
        <td <?= $backBlue ?>>SELISIH BERLAKU BG</td>
        <td <?= $backBlue ?>>TGL KONFIRMASI BG KE BANK</td>
        <td <?= $backBlue ?>>KONFIRMASI BG OLEH BANK</td>
        <td <?= $backBlue ?>>BG CONFIRMED?</td>
        <td <?= $backBlue ?>>TGL KIRIM EMAIL KE PENYEDIA</td>
        <td <?= $backBlue ?>>TGL TERIMA KONTRAK/SPK DARI PENYEDIA</td>
        <td <?= $backGreen ?>>SELISIH TTD KONTRAK</td>
        <td <?= $backBlue ?>>TGL PARAF KASUBDIT</td>
        <td <?= $backBlue ?>>TGL PPK TTD</td>
        <td <?= $backGreen ?>>LAMA PROSES</td>
        <td <?= $backBlue ?>>UPDATE LPSE</td>
        <td <?= $backGreen ?>>2.1 STATUS PAKET ADMIN KONTRAK</td>
        <td <?= $backOrange ?>>STATUS ADENDUM</td>
        <td <?= $backBlue ?>>KOMEN KASUB/KASI</td>
        <td <?= $backBlue ?>>2.1 KETERANGAN TAMBAHAN SIE 2.1</td>
        <td <?= $backBlue ?>>PIC 2.2</td>
        <td <?= $backBlue ?>>2.2 NAMA PENGAWAS UKER 1</td>
        <td <?= $backBlue ?>>2.2 PENGAWAS UKER 2</td>
        <td <?= $backBlue ?>>2.2 TGL KICK OFF MEETING</td>
        <td <?= $backOrange ?>>2.2 BA KICK OFF MEETING</td>
        <td <?= $backOrange ?>>2.2 MASA PEMELIHARAAN</td>
        <td <?= $backOrange ?>>NO BG JAMINAN PEMELIHARAAN</td>
        <td <?= $backOrange ?>>NILAI BG JAMINAN PEMELIHARAAN </td>
        <td <?= $backOrange ?>>TGL AWAL BG JAMINAN PEMELIHARAAN</td>
        <td <?= $backOrange ?>>TGL AKHIR BG JAMINAN PEMELIHARAAN</td>
        <td <?= $backGreen ?>>2.2 LANJUT KE SHEET BULANAN/TRIWULAN/QUARTAL/SEMESTER (NO URUT SHEET)</td>
        <td <?= $backBlue ?>>NO URUT SHEET SELANJUTNYA</td>
        <td <?= $backOrange ?>>2.2 TGL SELESAI  SESUAI DI LAPANGAN</td>
        <td <?= $backGreen ?>>2.2 SELISIH SELESAI PEKERJAAN VS TGL KONTRAK</td>
        <td <?= $backOrange ?>>2.2 TGL LAPORAN DITERIMA DARI PENYEDIA</td>
        <td <?= $backOrange ?>>2.2 TGL LAPORAN/SURAT JALAN DIKIRIM KE SEKSI 2.3</td>
        <td <?= $backOrange ?>>NILAI DENDA KETERLAMBATAN (JIKA ADA)</td>
        <td <?= $backGreen ?>>2.2 STATUS  PENGENDALIAN KONTRAK</td>
        <td <?= $backOrange ?>>2.2 KOMEN KASUB PROSES PENGENDALIAN</td>
        <td <?= $backOrange ?>>2.2 KETERANGAN TAMBAHAN</td>
        <td <?= $backOrange ?>>2.3 TGL PERMINTAAN RECEIPT DARI  2.2</td>
        <td <?= $backOrange ?>>2.3 TGL DITERIMA LAPORAN DARI SEKSI 2.22</td>
        <td <?= $backOrange ?>>2.3 PIC Admin Penagihan</td>
        <td <?= $backOrange ?>>2.3 TGL CREATE RECEIPT IPROC</td>
        <td <?= $backOrange ?>>2.3 TGL CETAK LPHP, BAST& BAP</td>
        <td <?= $backOrange ?>>2.3 NO RECEIPT</td>
        <td <?= $backOrange ?>>CETAK BAST (Ya/Tidak)</td>
        <td <?= $backOrange ?>>2.3 Nilai BAP </td>
        <td <?= $backGreen ?>> SELISIH NILAI KONTRAK VS BAP </td>
        <td <?= $backGreen ?>> SELISIH NILAI HPS VS BAP </td>
        <td <?= $backOrange ?>>2.3 TGL DIKIRIM KE PENYEDIA</td>
        <td <?= $backOrange ?>>2.3 TGL DITERIMA DARI PENYEDIA (LPHP, BAST &  BAP)</td>
        <td <?= $backOrange ?>>2.3 TANGGAL DITERIMA SETELAH REVISI DOKUMEN</td>
        <td <?= $backGreen ?>>SELISIH HARI (DIKIRIM VS DITERIMA DARI PENYEDIA)</td>
        <td <?= $backOrange ?>>2.3 TGL DIKIRIM KE UNIT KERJA</td>
        <td <?= $backOrange ?>>2.3 TGL TTD DARI UNIT KERJA (LPHP)</td>
        <td <?= $backGreen ?>> SELISIH HARI TTD UNIT KERJA </td>
        <td <?= $backOrange ?>>2.3 KETERANGAN REVISI</td>
        <td <?= $backOrange ?>>2.3 TANGGAL SUBMIT PARAF KASUB</td>
        <td <?= $backOrange ?>>2.3 TANGGAL DOKUMEN (BAST & BAP) TTD PPK</td>
        <td <?= $backGreen ?>>SELISIH (TTD INTERNAL)</td>
        <td <?= $backOrange ?>>2.3 TANGGAL DOKUMEN TAGIHAN DISERAHKAN KE DKA</td>
        <td <?= $backOrange ?>>2.3 TANGGAL DI RETUR OLEH DKA</td>
        <td <?= $backOrange ?>>2.3 KETERANGAN REVISI BERKAS </td>
        <td <?= $backOrange ?>>2.3 TANGGAL DOKUMEN  REVISI DISERAHKAN KEMBALI KE DKA</td>
        <td <?= $backOrange ?>>2.3 TANGGAL PAYMENT (BNI DIRECT)</td>
        <td <?= $backOrange ?>>NILAI DIBAYAR DKA</td>
        <td <?= $backGreen ?>>SELISIH NILAI BAP VS PAYMENT DKA</td>
        <td <?= $backBlue ?>>SELISIH SUBMIT DOKUMEN VS DIBAYAR OLEH DKA</td>
        <td <?= $backGreen ?>>2.3 STATUS PAKET ADM PENAGIHAN</td>
        <td <?= $backOrange ?>>2.3 KOMEN KASUB PROSES PENAGIHAN</td>
        <td <?= $backOrange ?>>2.3 KETERANGAN TAMBAHAN SIE 2.3</td>
        <td <?= $backGreen ?>>2.3 STATUS (OPEN/CLOSED)</td>
      </tr>
      <?php 
        $no = 1;
        $noUrut = 1;
        while ($dataExport->nextRow()) {
          $backTR = '';
          if ($no % 2 == 0) {
            $backTR = 'style="background-color: #b9eafa;"';
          }

          $paketInfo->getPaket($dataExport->getField("PAKET_ID"));
          $bidding = $paketInfo->bidding;
          $reqMultiPemenang = $paketInfo->multi_pemenang;
          
          $paket_rekanan = new PaketRekanan();
          $paket_rekanan->selectByParams3(array("A.PAKET_ID" => $dataExport->getField("PAKET_ID"), "A.REKANAN_ID" => $dataExport->getField("REKANAN_ID")), -1, -1);
          $paket_rekanan->firstRow();

          $paket_negosiasi = new PaketNegoisasi();
          if ($bidding == '0' || $bidding == '') { // jika Sistem Negosiasi
            $paket_negosiasi->selectByParams(array("A.PAKET_PENAWARAN_ID" => $paket_rekanan->getField("PAKET_PENAWARAN_ID")));
            $paket_negosiasi->firstRow();
            $totalAkhirNego =  $paket_negosiasi->getField("TOTAL");
          } else { // jika Sistem Negosiasi nya Bidding
            $totalAkhirNego = $paket_rekanan->getField("NILAI_PENAWARAN");
          }
      ?>
        <tr <?= $backTR ?>>
          <td><?= $noUrut ?></td>  
          <td>-</td> <!-- 2.1 NO FOLDER -->
          <td>-</td> <!-- 2.1 TGL LAP PEMILIHAN" -->
          <td>-</td> <!-- 2.1 TANGGAL CREATE PO -->
          <td><?= $dataExport->getField("KODE_PR") ?></td> <!-- 2 PR -->
          <td><?= $dataExport->getField("CR_PO") ?></td> <!-- PO -->
          <td><?= $dataExport->getField("DPSJ") ?></td> <!-- DPSJ -->
          <td><?= $dataExport->getField("NAMA") ?></td> <!-- Nama Paket Pengadaan -->
          <td>-</td> <!-- PENGADAAN UMUM/PENGADAAN KHUSUS/NILAI DIBAWAH 25 JUTA (ASSET/INV) -->
          <td><?= $dataExport->getField("PENGGUNA_STR") ?></td> <!-- PIC PEMILIHAN2/PIC PERENCANA -->
          <td><?= $dataExport->getField("LIST_KEGIATAN") ?></td> <!-- NAMA KEGIATAN -->
          <td><?= $dataExport->getField("PAKET_METODE_LELANG") ?></td> <!-- METODE PEMILIHAN -->
          <td><?= $dataExport->getField("PAKET_JENIS_STR") ?></td> <!-- JENIS PENGADAAN -->
          <td><?= $dataExport->getField("JNS_KONTRAK_STR") ?></td> <!-- JENIS KONTRAK -->
          <td><?= $dataExport->getField("JK_NAME") ?></td> <!-- KONTRAK -->
          <td><?= $dataExport->getField("REKANAN_ID_STR") ?></td> <!-- NAMA PENYEDIA -->
          <td><?= $dataExport->getField("CR_METODE_PEMBAYARAN_STR") ?></td> <!-- CARA PEMBAYARAN -->
          <td><?= $dataExport->getField("SUMBER_DANA_KETERANGAN") ?></td> <!-- SUMBER DANA -->
          <td><?= $dataExport->getField("NILAI_MATA_UANG") ?></td> <!-- MATA UANG -->
          <td><?= $dataExport->getField("NILAI_HPS_PR") ?></td> <!-- 2/2.1 NILAI HPS PLUS PPN (NILAI PR) -->
          <td><?= numberToIna($totalAkhirNego) ?></td> <!-- 2.1 NILAI HASIL PEMILIHAN -->
          <td><?= $dataExport->getField("NILAI_HPS_PR") - $totalAkhirNego ?></td> <!-- 2.1 EFISIENSI HPS VS PEMILIHAN -->
          <td><?= $dataExport->getField("CR_NILAI_KONTRAK") ?></td> <!-- NILAI KONTRAK AWAL (+PPN) -->
          <td><?= $dataExport->getField("PIC_KONTRAK_STR") ?></td> <!-- PIC 2.1 -->
          <td> </td> <!-- NILAI ADENDUM 1  -->
          <td> </td> <!-- NILAI ADENDUM 2 -->
          <td> </td> <!-- NILAI ADENDUM 3 -->
          <td> </td> <!-- NILAI ADENDUM 4 -->
          <td> </td> <!-- NILAI KONTRAK AKHIR SETELAH ADENDUM PLUS PPN (NILAI PO)  -->
          <td><?= $totalAkhirNego - $dataExport->getField("CR_NILAI_KONTRAK") ?></td> <!-- EFISIENSI PEMILIHAN VS KONTRAK (ADENDUM) -->
          <td> </td> <!-- EFISIENSI HPS S.D KONTRAK (ADENDUM) -->
          <td> </td> <!-- KETERANGAN (Sebutkan Ket Adendum) -->
          <td><?= $dataExport->getField("CR_SPPBJ_CODE") ?></td> <!-- NO. KONTRAK / ADDENDUM -->
          <td> </td> <!-- TGL RAPAT e-Purchasing/TGL KAJUL PAKET -->
          <td> </td> <!-- TANGGAL RANCANGAN KONTRAK -->
          <td> </td> <!-- TGL KONTRAK/ADDENDUM -->
          <td> </td> <!-- 2.1 TGL MULAI KONTRAK -->
          <td> </td> <!-- REMINDER JADWAL KIRIM/PENYELESAIAN PEKERJAAN -->
          <td> </td> <!-- TGL PENGIRIMAN BARANG/JASA -->
          <td> </td> <!-- TANGGAL  BATAS PENYELESAIAN ADMINISTRASI -->
          <td> </td> <!-- 2.1 TGL AKHIR KONTRAK -->
          <td> </td> <!-- 2.1 WAPEL -->
          <td> </td> <!-- TGL EMAIL SETUP/UPDATE SUPPLIER -->
          <td> </td> <!-- SPPBJ -->
          <td> </td> <!-- BG -->
          <td> </td> <!-- NO BG -->
          <td> </td> <!-- NILAI BG -->
          <td> </td> <!-- MASA BERLAKU (MULAI) -->
          <td> </td> <!-- MASA BERLAKU (AKHIR) -->
          <td> </td> <!-- SELISIH BERLAKU BG -->
          <td> </td> <!-- TGL KONFIRMASI BG KE BANK -->
          <td> </td> <!-- KONFIRMASI BG OLEH BANK -->
          <td> </td> <!-- BG CONFIRMED? -->
          <td> </td> <!-- TGL KIRIM EMAIL KE PENYEDIA -->
          <td> </td> <!-- TGL TERIMA KONTRAK/SPK DARI PENYEDIA -->
          <td> </td> <!-- SELISIH TTD KONTRAK -->
          <td> </td> <!-- TGL PARAF KASUBDIT -->
          <td> </td> <!-- TGL PPK TTD -->
          <td> </td> <!-- LAMA PROSES -->
          <td> </td> <!-- UPDATE LPSE -->
          <td> </td> <!-- 2.1 STATUS PAKET ADMIN KONTRAK  -->
          <td> </td> <!-- STATUS ADENDUM -->
          <td> </td> <!-- KOMEN KASUB/KASI -->
          <td> </td> <!-- 2.1 KETERANGAN TAMBAHAN SIE 2.1 -->
          <td> </td> <!-- PIC 2.2 -->
          <td> </td> <!-- 2.2 NAMA PENGAWAS UKER 1 -->
          <td> </td> <!-- 2.2 PENGAWAS UKER 2 -->
          <td> </td> <!-- 2.2 TGL KICK OFF MEETING -->
          <td> </td> <!-- 2.2 BA KICK OFF MEETING -->
          <td> </td> <!-- 2.2 MASA PEMELIHARAAN -->
          <td> </td> <!-- NO BG JAMINAN PEMELIHARAAN -->
          <td> </td> <!-- NILAI BG JAMINAN PEMELIHARAAN  -->
          <td> </td> <!-- TGL AWAL BG JAMINAN PEMELIHARAAN< -->
          <td> </td> <!-- TGL AKHIR BG JAMINAN PEMELIHARAAN -->
          <td> </td> <!-- 2.2 LANJUT KE SHEET BULANAN/TRIWULAN/QUARTAL/SEMESTER (NO URUT SHEET) -->
          <td> </td> <!-- NO URUT SHEET SELANJUTNYA -->
          <td> </td> <!-- 2.2 TGL SELESAI  SESUAI DI LAPANGAN -->
          <td> </td> <!-- 2.2 SELISIH SELESAI PEKERJAAN VS TGL KONTRAK -->
          <td> </td> <!-- 2.2 TGL LAPORAN DITERIMA DARI PENYEDIA -->
          <td> </td> <!-- 2.2 TGL LAPORAN/SURAT JALAN DIKIRIM KE SEKSI 2.3 -->
          <td> </td> <!-- NILAI DENDA KETERLAMBATAN (JIKA ADA) -->
          <td> </td> <!-- 2.2 STATUS  PENGENDALIAN KONTRAK -->
          <td> </td> <!-- 2.2 KOMEN KASUB PROSES PENGENDALIAN -->
          <td> </td> <!-- 2.2 KETERANGAN TAMBAHAN -->
          <td> </td> <!-- 2.3 TGL PERMINTAAN RECEIPT DARI  2.2 -->
          <td> </td> <!-- 2.3 TGL DITERIMA LAPORAN DARI SEKSI 2.22 -->
          <td> </td> <!-- 2.3 PIC Admin Penagihan -->
          <td> </td> <!-- 2.3 TGL CREATE RECEIPT IPROC -->
          <td> </td> <!-- 2.3 TGL CETAK LPHP, BAST& BAP  -->
          <td> </td> <!--  2.3 NO RECEIPT-->
          <td> </td> <!-- CETAK BAST (Ya/Tidak) -->
          <td> </td> <!-- 2.3 Nilai BAP -->
          <td> </td> <!-- SELISIH NILAI KONTRAK VS BAP -->
          <td> </td> <!-- SELISIH NILAI HPS VS BAP -->
          <td> </td> <!-- 2.3 TGL DIKIRIM KE PENYEDIA -->
          <td> </td> <!-- 2.3 TGL DITERIMA DARI PENYEDIA (LPHP, BAST &  BAP) -->
          <td> </td> <!-- 2.3 TANGGAL DITERIMA SETELAH REVISI DOKUMEN -->
          <td> </td> <!-- SELISIH HARI (DIKIRIM VS DITERIMA DARI PENYEDIA) -->
          <td> </td> <!-- 2.3 TGL DIKIRIM KE UNIT KERJA -->
          <td> </td> <!-- 2.3 TGL TTD DARI UNIT KERJA (LPHP) -->
          <td> </td> <!-- SELISIH HARI TTD UNIT KERJA -->
          <td> </td> <!-- 2.3 KETERANGAN REVISI -->
          <td> </td> <!-- 2.3 TANGGAL SUBMIT PARAF KASUB -->
          <td> </td> <!-- 2.3 TANGGAL DOKUMEN (BAST & BAP) TTD PPK -->
          <td> </td> <!-- SELISIH (TTD INTERNAL) -->
          <td> </td> <!-- 2.3 TANGGAL DOKUMEN TAGIHAN DISERAHKAN KE DKA -->
          <td> </td> <!-- 2.3 TANGGAL DI RETUR OLEH DKA -->
          <td> </td> <!-- 2.3 KETERANGAN REVISI BERKAS  -->
          <td> </td> <!-- 2.3 TANGGAL DOKUMEN  REVISI DISERAHKAN KEMBALI KE DKA -->
          <td> </td> <!-- 2.3 TANGGAL PAYMENT (BNI DIRECT)  -->
          <td> </td> <!-- NILAI DIBAYAR DKA -->
          <td> </td> <!-- SELISIH NILAI BAP VS PAYMENT DKA -->
          <td> </td> <!-- SELISIH SUBMIT DOKUMEN VS DIBAYAR OLEH DKA -->
          <td> </td> <!-- 2.3 STATUS PAKET ADM PENAGIHAN -->
          <td> </td> <!-- 2.3 KOMEN KASUB PROSES PENAGIHAN -->
          <td> </td> <!-- 2.3 KETERANGAN TAMBAHAN SIE 2.3 -->
          <td> </td> <!-- 2.3 STATUS (OPEN/CLOSED)  -->
        </tr>
      <?php 
          $no++;
          $noUrut++;
        } ?>
      <tr>
      </tr>
    </table>
 
  </table>
	</body>
</html>
