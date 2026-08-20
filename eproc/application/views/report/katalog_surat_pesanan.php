<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("kauth");  $userLogin = new kauth();

include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model("Paket");
$this->load->model("Katalogrekanan");
$this->load->model("Kataloglogistik");

$paket = new Paket();
$katalogrekanan = new Katalogrekanan();
$kataloglogistik = new Kataloglogistik();
$katalogrekananRow = new Katalogrekanan();
$katalogrekananGroupPenyedia = new Katalogrekanan();

$reqId = httpFilterGet("reqId");

$totalPenyedia = $katalogrekananGroupPenyedia->selectByParamsGroupByPenyedia(array()," AND A.PAKET_ID = '".$reqId."'");
// echo $totalPenyedia; die();

$paket->selectByParamsMonitoring(array("A.PAKET_ID" => coalesce($reqId, 0)));
$paket->firstRow();

$kataloglogistik->selectByParams(array("A.PAKET_ID" => coalesce($reqId, 0)));
$kataloglogistik->firstRow();
$kataloglogistikOngkosKirim = $kataloglogistik->getField('ONGKOS_KIRIM');

  $reqMetodePengadaan = $paket->getField("PAKET_METODE_LELANG_ID");
  $reqUserLogin = $paket->getField("USER_LOGIN");
  $reqMetodeKualifikasi = $paket->getField("PAKET_METODE_KUALIFIKASI_ID");
  $reqMetodeEvaluasi = $paket->getField("PAKET_METODE_EVALUASI_ID");
  $reqJenisPekerjaan = $paket->getField("PAKET_JENIS_ID");
  $reqJenisPekerjaanStr = $paket->getField("PAKET_JENIS");
  $reqKualifikasiRekanan = $paket->getField("REKANAN_KUALIFIKASI_ID");
  $reqNamaPaket = $paket->getField("NAMA");
  $reqUraianKegiatan = $paket->getField("URAIAN");
  $reqLokasiPekerjaan = $paket->getField("LOKASI");
  $reqAlamatPanitia =  $paket->getField("ALAMAT");
  $arrTelp = explode(" ", trim($paket->getField("TELEPON")));
  $reqTelpPanitiaKode = $arrTelp[0];
  $reqTelpPanitia = $arrTelp[1];
  $reqEmailPanitia = $paket->getField("EMAIL");
  $reqNilaiPekerjaan = $paket->getField("NILAI");
  $reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID"); 
  $reqPermohonan = $paket->getField("PERMOHONAN");
  $reqPermohonanNotaDinas = $paket->getField("PERMOHONAN_NOTA_DINAS");
  $reqMetodePenyampulan = $paket->getField("SISTEM_SAMPUL");
  $reqBahasa = $paket->getField("BAHASA");
  $reqMataUang = $paket->getField("NILAI_MATA_UANG");
  $reqBidingMenit = $paket->getField("BIDDING_MENIT");
  $reqBidding = $paket->getField("BIDDING");
  $reqBobotTeknis = $paket->getField("BOBOT_TEKNIS");
  $reqBobotHarga = $paket->getField("BOBOT_HARGA");
  $reqPassingGrade = $paket->getField("PASSING_GRADE");

  if ($reqId == '' || $reqMetodePengadaan != '6')
    exit;

  $katalogrekanan->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->selectByParams(array('A.PAKET_ID' => $reqId));
  $katalogrekananRow->firstRow();
  if ($katalogrekananRow->getField('STATUS') == '')
    exit;
?>

<html moznomarginboxes mozdisallowselectionprint>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<base href="<?=base_url()?>" />
</head>

<body> 

<div style="width: 100%; padding:20px 5px">
  <p style="text-align: center; font-size: 12px"><i>[kop surat]</i></p>
  <p style="text-align: center; font-size: 18px; font-weight: bold">SURAT PESANAN (SP)</p><br>
  <p>Yang bertanda tangan di bawah ini: <br>
    <b><?= $reqUserLogin ?></b> (<?= SYSTEM_NAME_PT ?>) <br>
    <?= $reqLokasiPekerjaan ?> <br> 
    selanjutnya disebut sebagai Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian; <br><br>

    <b><?= $katalogrekananRow->getField('USER_NAMA') ?></b> <br>
    (NPWP: <?= $katalogrekananRow->getField('NPWP') ?>) <br>
    <?= $katalogrekananRow->getField('ALAMAT').', '.$katalogrekananRow->getField('KOTA').' - '.$katalogrekananRow->getField('KODEPOS') ?> <br>
    selanjutnya disebut sebagai Penyedia; <br>

    untuk mengirimkan barang dengan memperhatikan ketentuan-ketentuan sebagai berikut:
    <table>
      <thead>
        <tr>
          <th style="border: 1px solid #b7b7b7; width: 5%;">#</th>
          <th style="border: 1px solid #b7b7b7; width: 45%;">Produk</th>
          <th style="border: 1px solid #b7b7b7; width: 15%;">Harga</th>
          <th style="border: 1px solid #b7b7b7; width: 5%;">Qty</th>
          <th style="border: 1px solid #b7b7b7; width: 15%;">Total</th> 
        </tr>
      </thead>
      <tbody>
        <?php 
        $no=1;
        $totalBayar=0;
        $totalBayarHargaAwal=0;
        while($katalogrekanan->nextRow())
        { 
          $totalBayar += $katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA_NEGO');
          $totalBayarHargaAwal += $katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA');
        ?>
        <tr>
          <td style="border: 1px solid #b7b7b7;"><?=$no?></td>
          <td style="border: 1px solid #b7b7b7;"> 
            <?= $katalogrekanan->getField('NAMAPRODUK') ?> 
          </td> 
          <td style="border: 1px solid #b7b7b7;">
            <?php echo number_format($katalogrekanan->getField('HARGA_NEGO'),2,',','.'); ?>
          </td>
          <td style="border: 1px solid #b7b7b7;">
            <?php  echo $katalogrekanan->getField('QTY'); ?>
          </td>
          <td style="border: 1px solid #b7b7b7;">
            <?= number_format(($katalogrekanan->getField('QTY') * $katalogrekanan->getField('HARGA_NEGO')),2,',','.') ?>
          </td> 
        </tr> 
        <?php 
        $no++;
        } ?>
      </tbody>
      <tfoot>
        <!-- <tr class="tr-bc"><td class="tr" colspan="6"></td></tr><tr class="tr-bc"><td class="tr" colspan="6"></td></tr> -->
        <tr>
          <th colspan="4"  style="border: 1px solid #b7b7b7;">Biaya Kirim</th> 
          <th colspan="2"  style="border: 1px solid #b7b7b7;"><b><?= number_format($kataloglogistikOngkosKirim,2,',','.') ?></b></th> 
        </tr>  
        <tr>
          <th colspan="4"  style="border: 1px solid #b7b7b7;">Total</th> 
          <th colspan="2"  style="border: 1px solid #b7b7b7;"><b><?= number_format(($totalBayar+$kataloglogistikOngkosKirim),2,',','.') ?></b></th> 
        </tr>  
        <tr class="tr-bc"><td class="tr" colspan="6"></td></tr><tr class="tr-bc"><td class="tr" colspan="6"></td></tr>
      </tfoot>
    </table>

    <h4>SYARAT DAN KETENTUAN:</h4>

    <ul style="list-style-type:decimal;">
      <li>Hak dan Kewajiban
        <ul style="list-style-type:lower-alpha;">
          <li>PENYEDIA
            <ul style="list-style-type:decimal;">
              <li>Penyedia memiliki hak menerima pembayaran atas pembelian barang sesuai dengan total harga dan waktu yang tercantum di dalam SP ini.</li>
              <li>Penyedia memiliki kewajiban
                <ul style="list-style-type:lower-alpha;">
                  <li> tidak membuat dan/atau menyampaikan dokumen dan/atau keterangan lain yang tidak benar untuk memenuhi persyaratan Katalog Elektronik;</li>
                  <li>tidak menjual barang melalui e-Purchasing lebih mahal dari harga barang yang dijual selain melalui e-Purchasing pada periode penjualan, jumlah, dan tempat serta spesifikasi teknis dan persyaratan yang sama;</li>
                  <li>mengirimkan barang sesuai spesifikasi dalam SP ini selambat-lambatnya pada (tanggal/bulan/tahun) sejak SP ini diterima oleh Penyedia;</li>
                  <li>bertanggung jawab atas keamanan,kualitas,dan kuantitas barang yang dipesan;</li>
                  <li>mengganti barang setelah Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian melalui Pejabat/Pelaksana Pengadaan Barang dan Jasa Penerima Hasil Pekerjaan (PPHP) melakukan pemeriksaan barang dan menemukan bahwa:
                    <ul style="list-style-type:decimal;">
                      <li>barang rusak akibat cacat produksi; </li>
                      <li>barang rusak pada saat pengiriman barang hingga barang diterima oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian; dan/atau
                      <li>barang yang diterima tidak sesuai dengan spesifikasi barang sebagaimana tercantum pada SP ini.</li>
                    </ul>
                  </li>
                  <li>memberikanlayanantambahanyangdiperjanjikansepertiinstalasi,testing,dan pelatihan (apabila ada);</li>
                  <li>memberikan layanan purnajual sesuai dengan ketentuan garansi masing- masing barang.</li>
                </ul>
              </li>
            </ul>
          </li>
          <li>PEJABAT PENANDATANGAN/PENGESAHAN TANDA BUKTI PERJANJIAN
            <ul style="list-style-type:decimal;">
              <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian memiliki hak:
                <ul style="list-style-type:lower-alpha;">
                  <li> menerima barang dari Penyedia sesuai dengan spesifikasi yang tercantum di dalam SP ini.</li>
                  <li>mendapatkan jaminan keamanan, kualitas, dan kuantitas barang yang dipesan;</li>
                  <li>mendapatkan penggantian barang, dalam hal:
                    <ul style="list-style-type:decimal;">
                      <li>barang rusak akibat cacat produksi;</li>
                      <li>barang rusak pada saat pengiriman barang hingga barang diterima oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian; dan/atau
                      <li>barang yang diterima tidak sesuai dengan spesifikasi barang sebagaimana tercantum pada SP ini.</li>
                    </ul>
                  </li>
                  <li>Mendapatkan layanan tambahan yang diperjanjikan seperti instalasi, testing, dan pelatihan (apabila ada);</li>
                  <li>Mendapatkan layanan purnajual sesuai dengan ketentuan garansi masing-masing barang.</li>
                </ul>
              </li>
              <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian memiliki kewajiban:
                <ul style="list-style-type:lower-alpha;">
                  <li> melakukan pembayaran sesuai dengan total harga yang tercantum di dalam SP ini; dan </li>
                  <li>memeriksa kualitas dan kuantitas barang;</li>
                  <li>memastikan layanan tambahan telah dilaksanakan oleh penyedia seperti instalasi, testing, dan pelatihan (apabila ada).</li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </li>

      <li>Waktu Pengiriman Barang <br>
        Penyedia mengirimkan barang dan melaksanakan laysesuai spesifikasi dalam SP ini selambat-lambatnya pada (tanggal/bulan/tahun)sejak SP ini diterima oleh Penyedia.
      </li>

      <li>Alamat Pengiriman Barang <br>
        Penyedia mengirimkan barang ke alamat sebagai berikut: <br>
        <b><i><?= SYSTEM_ALAMAT_PT ?></i></b> 
      </li>

      <li>Tanggal Barang Diterima <br>
        Barang diterima pada (tanggal/bulan/tahun) _______________________
      </li>

      <li>Penerimaan, Pemeriksaan, dan Retur Barang <br>
        <ul style="list-style-type:lower-alpha;">
          <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian melalui PPHP menerima barang dan melakukan pemeriksaan barang berdasarkan ketentuan di dalam SP ini.</li>
          <li>Dalam hal pada saat pemeriksaan barang, Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian menemukan bahwa:
            <ul style="list-style-type:decimal;">
              <li> barang rusak akibat cacat produksi;</li>
              <li>barang rusak pada saat pengiriman barang hingga barang diterima oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian; dan/atau</li>
              <li>barang yang diterima tidak sesuai dengan spesifikasi barang sebagaimana tercantum pada SP ini.</li>
            </ul>
            Maka Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dapat menolak penerimaan barang dan menyampaikan pemberitahuan tertulis kepada Penyedia atas cacat mutu atau kerusakan barang tersebut.
          </li>
          <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dapat meminta Tim Teknis untuk melakukan pemeriksaan atau uji mutu terhadap barang yang diterima.</li>
          <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dapat memerintahkan Penyedia untuk menemukan dan mengungkapkan cacat mutu serta melakukan pengujian terhadap barang yang dianggap Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian mengandung cacat mutu atau kerusakan.</li>
          <li>Penyedia bertanggungjawab atas cacat mutu atau kerusakan barang dengan memberikan penggantian barang selambat-lambatnya (___________) hari kerja.</li>
        </ul>
      </li>

      <li>Harga 
        <ul style="list-style-type:lower-alpha;">
          <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian membayar kepada Penyedia atas pelaksanaan pekerjaan sebesar harga yang tercantum pada SP ini.</li>
          <li>Harga SP telah memperhitungkan keuntungan, pajak, biaya overhead, biaya pengiriman, biaya asuransi, biaya layanan tambahan (apabila ada) dan biaya layanan purna jual.</li>
          <li>Rincian harga SP sesuai dengan rincian yang tercantum dalam daftar kuantitas dan harga.</li>
        </ul>
      </li>

      <li>Perpajakan <br>
        Penyedia berkewajiban untuk membayar semua pajak, bea, retribusi, dan pungutan lain yang sah yang dibebankan oleh hukum yang berlaku atas pelaksanaan SP. Semua pengeluaran perpajakan ini dianggap telah termasuk dalam harga SP.
      </li>

      <li>Pengalihan dan/atau subkontrak
        <ul style="list-style-type:lower-alpha;">
          <li> Pengalihan seluruh Kontrak hanya diperbolehkan dalam hal terdapat pergantian nama Penyedia, baik sebagai akibat peleburan (merger), konsolidasi, atau pemisahan.</li>
          <li>Pengalihan sebagian pelaksanaan Kontrak dilakukan dengan ketentuan sebagai berikut:
            <ul style="list-style-type:decimal;">
              <li> Pengalihan sebagian pelaksanaan Kontrak untuk barang/jasa yang bersifat standar dilakukan untuk pekerjaan seperti pengiriman barang (distribusi barang) dari Penyedia kepada Kementerian/Lembaga/Satuan Kerja Perangkat Daerah/Institusi; dan</li>
              <li>Pengalihan sebagian pelaksanaan Kontrak dapat dilakukan untuk barang/jasa yang bersifat tidak standar misalnya untuk pekerjaan konstruksi (minor), pengadaan ambulans, ready mix, hot mix dan lain sebagainya.</li>
            </ul>
          </li>
        </ul>
      </li>

      <li>Perubahan SP
        <ul style="list-style-type:lower-alpha;">
          <li>SP hanya dapat diubah melalui adendum SP.</li>
          <li>Perubahan SP dapat dilakukan apabila disetujui oleh para pihak dalam hal terjadi perubahan jadwal pengiriman barang atas permintaan Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian atau permohonan Penyedia yang disepakati oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian.</li>
        </ul>
      </li>

      <li>Peristiwa Kompensasi
        <ul style="list-style-type:lower-alpha;">
          <li>Peristiwa Kompensasi dapat diberikan kepada penyedia dalam hal Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian terlambat melakukan pembayaran prestasi pekerjaan kepada Penyedia.</li>
          <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dikenakan ganti rugi atas keterlambatan pembayaran sebesar <i>[sesuai kesepakatan para pihak]</i>.</li>
        </ul>
      </li>

      <li>Hak Atas Kekayaan Intelektual
        <ul style="list-style-type:lower-alpha;">
          <li>Penyedia berkewajiban untuk memastikan bahwa barang yang dikirimkan/dipasok tidak melanggar Hak Atas Kekayaan Intelektual (HAKI) pihak manapun dan dalam bentuk apapun.</li>
          <li>Penyedia berkewajiban untuk menanggung Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dari atau atas semua tuntutan, tanggung jawab, kewajiban, kehilangan, kerugian, denda, gugatan atau tuntutan hukum, proses pemeriksaan hukum, dan biaya yang dikenakan terhadap Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian sehubungan dengan klaim atas pelanggaran HAKI, termasuk pelanggaran hak cipta, merek dagang, hak paten, dan bentuk HAKI lainnya yang dilakukan atau diduga dilakukan oleh Penyedia.</li>
        </ul>
      </li>

      <li>Jaminan Bebas Cacat Mutu/Garansi
        <ul style="list-style-type:lower-alpha;">
          <li>Penyedia dengan jaminan pabrikan dari produsen pabrikan (jika ada) berkewajiban untuk menjamin bahwa selama penggunaan secara wajar oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian, Barang tidak mengandung cacat mutu yang disebabkan oleh tindakan atau kelalaian Penyedia, atau cacat mutu akibat desain, bahan, dan cara kerja.</li>
          <li>Jaminanbebascacatmutuiniberlakusampaidengan12(duabelas)bulansetelahserah terima Barang atau jangka waktu lain yang ditetapkan dalam SP ini.</li>
          <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian akan menyampaikan pemberitahuan cacat mutu kepada Penyedia segera setelah ditemukan cacat mutu tersebut selama Masa Layanan Purnajual.</li>
          <li>Terhadap pemberitahuan cacat mutu oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian, Penyedia berkewajiban untuk memperbaiki atau mengganti Barang dalam jangka waktu yang ditetapkan dalam pemberitahuan tersebut.</li>
          <li>Jika Penyedia tidak memperbaiki atau mengganti Barang akibat cacat mutu dalam jangka waktu yang ditentukan, maka Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian akan menghitung biaya perbaikan yang diperlukan dan Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian secara langsung atau melalui pihak ketiga yang ditunjuk oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian akan melakukan perbaikan tersebut. Penyedia berkewajiban untuk membayar biaya perbaikan atau penggantian tersebut sesuai dengan klaim yang diajukan secara tertulis oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian. Biaya tersebut dapat dipotong oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dari nilai tagihan Penyedia.</li> 
        </ul>
      </li>

      <li>Pembayaran
        <ul style="list-style-type:lower-alpha;">
          <li>pembayaran prestasi hasil pekerjaan yang disepakati dilakukan oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian, dengan ketentuan:
            <ul style="list-style-type:decimal;">
              <li>penyedia telah mengajukan tagihan;</li>
              <li>pembayaran dilakukan dengan [sistem bulanan/sistem termin/pembayaran secara sekaligus]; dan</li>
              <li>pembayaran harus dipotong denda (apabila ada) dan pajak.</li>
            </ul>
          </li>
          <li>pembayaran terakhir hanya dilakukan setelah pekerjaan selesai 100% (seratus perseratus) dan bukti penyerahan pekerjaan diterbitkan.</li>
          <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian melakukan proses pembayaran atas pembelian barang selambat-lambatnya (___________) hari kerja setelah PPK menilai bahwa dokumen pembayaran lengkap dan sah.</li>
        </ul>
      </li> 

      <li>Sanksi
        <ul style="list-style-type:lower-alpha;">
          <li>Penyedia dikenakan sanksi apabila:
            <ul style="list-style-type:decimal;">
              <li>Tidak menanggapi pesanan barang selambat-lambatnya (___________) hari kerja;</li>
              <li>Tidak dapat memenuhi pesanan sesuai dengan kesepakatan dalam transaksi melalui e-Purchasing dan SP ini tanpa disertai alasan yang dapat diterima; dan/atau</li>
              <li>menjual barang melalui proses e-Purchasing dengan harga yang lebih mahal dari harga Barang/Jasa yang dijual selain melalui e-Purchasing pada periode penjualan, jumlah, dan tempat serta spesifikasi teknis dan persyaratan yang sama.</li>
            </ul>
          </li>
          <li>Penyedia yang melakukan perbuatan sebagaimana dimaksud dalam huruf a dikenakan sanksi administratif berupa:
            <ul style="list-style-type:decimal;">
              <li>peringatan tertulis;</li>
              <li>denda; dan</li>
              <li>pelaporan kepada Perusahaan untuk dilakukan:
                <ul style="list-style-type:upper-alpha;">
                  <li>penghentian sementara dalam sistem transaksi e-Purchasing; atau</li>
                  <li>penurunan pencantuman dari Katalog Elektronik (e-Katalog).</li>
                </ul>
              </li>
            </ul>
          </li>
          <li>Tata Cara Pengenaan Sanksi <br>
          Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian mengenakan sanksi sebagaimana dimaksud dalam huruf a dan huruf b berdasarkan ketentuan mengenai sanksi sebagaimana diatur dalam Peraturan Kepala LKPP tentang e-Purchasing.</li>
        </ul>
      </li> 

      <li>Penghentian dan Pemutusan SP
        <ul style="list-style-type:lower-alpha;">
          <li>Penghentian SP dapat dilakukan karena pekerjaan sudah selesai atau terjadi Keadaan Kahar.</li>
          <li>Pemutusan SP oleh Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian
            <ul style="list-style-type:decimal;">
              <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dapat melakukan pemutusan SP apabila:
                 <ul style="list-style-type:upper-alpha;">
                  <li>kebutuhan barang/jasa tidak dapat ditunda melebihi batas berakhirnya SP;</li>
                  <li>berdasarkan penelitian Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian, Penyedia tidak akan mampu menyelesaikan keseluruhan pekerjaan walaupun diberikan kesempatan sampai dengan 50 (lima puluh) hari kalender sejak masa berakhirnya pelaksanaan pekerjaan untuk menyelesaikan pekerjaan;</li>
                  <li>setelah diberikan kesempatan menyelesaikan pekerjaan sampai dengan 50 (lima puluh) hari kalender sejak masa berakhirnya pelaksanaan pekerjaan, Penyedia Barang/Jasa tidak dapat menyelesaikan pekerjaan;</li>
                  <li>Penyedia lalai/cidera janji dalam melaksanakan kewajibannya dan tidak memperbaiki kelalaiannya dalam jangka waktu yang telah ditetapkan;</li>
                  <li>Penyedia terbukti melakukan KKN, kecurangan dan/atau pemalsuan dalam proses Pengadaan yang diputuskan oleh instansi yang berwenang; dan/atau</li>
                  <li>pengaduan tentang penyimpangan prosedur, dugaan KKN dan/atau pelanggaran persaingan sehat dalam pelaksanaan pengadaan dinyatakan benar oleh instansi yang berwenang.</li>
                 </ul>
              </li>
              <li>Pemutusan SP sebagaimana dimaksud pada angka 1 dilakukan selambat-lambatnya (___________) kerja setelah Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian menyampaikan pemberitahuan rencana pemutusan SP secara tertulis kepada Penyedia.</li>
            </ul>
          </li>
          <li>Pemutusan SP oleh Penyedia
            <ul style="list-style-type:decimal;">
              <li>Penyedia dapat melakukan pemutusan Kontrak jika terjadi hal-hal sebagai berikut:
                <ul style="list-style-type:upper-alpha;">
                  <li>akibat keadaan kahar sehingga Penyedia tidak dapat melaksanakan pekerjaan sesuai ketentuan SP atau adendum SP;</li>
                  <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian gagal mematuhi keputusan akhir penyelesaian perselisihan; atau</li>
                  <li>Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian tidak memenuhi kewajiban sebagaimana dimaksud dalam SP atau Adendum SP.</li>
                </ul>
              </li>
              <li>Pemutusan SP sebagaimana dimaksud pada angka 1 dilakukan selambat-lambatnya (___________) kerja setelah Penyedia menyampaikan pemberitahuan rencana pemutusan SP secara tertulis kepada Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian.</li>
            </ul>
          </li>
        </ul>
      </li> 

      <li>Denda Keterlambatan Pelaksanaan Pekerjaan <br>
        Penyedia yang terlambat menyelesaikan pekerjaan dalam jangka waktu sebagaimana ditetapkan dalam SP ini karena kesalahan Penyedia, dikenakan denda keterlambatan sebesar 1/1000 (satu perseribu) dari total harga atau dari sebagian total harga sebagaimana tercantum dalam SP ini untuk setiap hari keterlambatan.
      </li>

      <li>Keadaan Kahar
        <ul style="list-style-type:lower-alpha;"> 
          <li>Keadaan Kahar adalah suatu keadaan yang terjadi diluar kehendak para pihak dan tidak dapat diperkirakan sebelumnya, sehingga kewajiban yang ditentukan dalam SP menjadi tidak dapat dipenuhi.</li>
          <li>Dalam hal terjadi Keadaan Kahar, Penyedia memberitahukan tentang terjadinya Keadaan Kahar kepada Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian secara tertulis dalam waktu selambat-lambatnya 14 (empat belas) hari kalender sejak terjadinya Keadaan Kahar yang dikeluarkan oleh pihak/instansi yang berwenang sesuai ketentuan peraturan perundang-undangan.</li>
          <li>Tidak termasuk Keadaan Kahar adalah hal-hal merugikan yang disebabkan oleh perbuatan atau kelalaian para pihak.</li>
          <li>Keterlambatan pelaksanaan pekerjaan yang diakibatkan oleh terjadinya Keadaan Kahar tidak dikenakan sanksi.</li>
          <li>Setelah terjadinya Keadaan Kahar, para pihak dapat melakukan kesepakatan, yang dituangkan dalam perubahan SP.</li>
        </ul>
      </li> 

      <li>Penyelesaian Perselisihan <br>
        Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian dan penyedia berkewajiban untuk berupaya sungguh-sungguh menyelesaikan secara damai semua perselisihan yang timbul dari atau berhubungan dengan SP ini atau interpretasinya selama atau setelah pelaksanaan pekerjaan. Jika perselisihan tidak dapat diselesaikan secara musyawarah maka perselisihan akan diselesaikan melalui mediasi, konsiliasi, arbitrase atau pengadilan negeri dalam wilayah hukum Republik Indonesia.
      </li>

      <li>Larangan Pemberian Komisi <br>
        Penyedia menjamin bahwa tidak satu pun personil satuan kerja Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian telah atau akan menerima komisi dalam bentuk apapun (gratifikasi) atau keuntungan tidak sah lainnya baik langsung maupun tidak langsung dari SP ini. Penyedia menyetujui bahwa pelanggaran syarat ini merupakan pelanggaran yang mendasar terhadap SP ini.
      </li>

      <li> Masa Berlaku SP <br>
        SP ini berlaku sejak tanggal SP ini ditandatangani oleh para pihak sampai dengan selesainya pelaksanaan pekerjaan.
      </li>

    </ul>  

  </p>
  <p style="padding: 10px">
    Demikian SP ini dibuat dan ditandatangani dalam 2 (dua) rangkap bermaterai dan masing- masing memiliki kekuatan hukum yang sama.
  </p>

  <table>
    <tbody>
      <tr>
        <td style="border: 1px solid #b7b7b7; width: 50%; padding: 10px 5px; text-align: center">
          Untuk dan atas nama <b><?= $reqUserLogin ?></b> (<?= SYSTEM_NAME_PT ?>) <br> 
          Pejabat Penandatangan/Pengesahan Tanda Bukti Perjanjian
          <br><br><br><br><br><br> 
          <i>[nama lengkap]</i><br>
          <i>[jabatan]</i>
        </td>
        <td style="border: 1px solid #b7b7b7; width: 50%; padding: 10px 5px; text-align: center">
          Untuk dan atas nama Penyedia/kemitraan (KSO) <br> 
          <b><?= $katalogrekananRow->getField('USER_NAMA') ?> </b>
          <br><br><br><br><br><br><br> 
          <i>[nama lengkap]</i><br>
          <i>[jabatan]</i>
        </td>
    </tbody>
  </tr>


</div>

</body>
</html>