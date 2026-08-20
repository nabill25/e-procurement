<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->model("Rekanan");

$this->load->model("UserLogin");
$user_login = new UserLogin();
$adaKelengkapanData = $user_login->getCountByParams(array("USER_LOGIN_ID"=> $this->USER_LOGIN_ID, "USER_STATUS" => 0));

if($adaKelengkapanData == 0)
    redirect(base_url('main'));

$reqId = $this->input->get("reqId");

$rekanan = new Rekanan();
$rekanan->selectByParams(array("REKANAN_ID" => $this->REKANAN_ID));
$rekanan->firstRow();
?>

<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/vendors/css/ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url() ?>assets/new/css/plugins/ui/jqueryui.css">
<!-- <script src="<?=base_url()?>assets/new/js/core/libraries/jquery_ui/jquery-ui.min.js"></script> -->
<!-- <script src="<?=base_url()?>assets/new/js/scripts/ui/jquery-ui/navigations.js"></script> -->
<style type="text/css">
  small { font-weight: bold; font-size: 11.5px }
</style>

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
          <div class="alert bg-primary alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="fa fa-th"></i></span>
            <?php 
            if ($this->REKANAN_TIPE_ID == '7') { // Perorangan ?>
            <strong>Informasi Pendaftaran Perorangan</strong>
            <?php 
            } else { ?>
            <strong>Informasi Pendaftaran Daftar Rekanan Perusahaan</strong>
            <?php 
            } ?>
          </div>

             <div class="alert alert-primary">
                Email telah dikirim, jika dalam 24 jam email belum diterima, hubungi administrator. Silahkan simpan informasi di bawah ini untuk kepentingan validasi.
            </div>
            <div class="alert alert-primary">
                Anda telah berhasil melakukan registrasi pada sistem <strong><?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?></strong>
            </div>

            <table class="table table-bordered table-hover">
                <tr>
                    <td width="20%">
                        <?php 
                        if ($this->REKANAN_TIPE_ID == '7') { // Perorangan ?>
                        Nama Perorangan:
                        <?php 
                        } else { ?>
                        Nama Perusahaan:
                        <?php 
                        } ?>
                    </td>
                    <td><?=$rekanan->getField("NAMA")?></td>
                </tr>
                <tr>
                    <td>Nomor registrasi Anda:</td>
                    <td><?=$rekanan->getField("KODE")?></td>
                </tr>
            </table>

            <div>
                Apabila sampai dengan 30 (tiga puluh) hari perusahaan anda tidak melakukan validasi maka data registrasi anda akan terhapus.    <br>
                Untuk melakukan validasi, Pimpinan/ Direksi atau yang mewakili dengan membawa surat kuasa harus membawa asli dan fotocopy dokumen administrasi perusahaan ke:
            </div>
            <br>

            <strong>
              <!-- Divisi Pengadaan Barang dan Jasa<br> -->
            <?= SYSTEM_NAME_PT ?><br>
            <?= SYSTEM_ALAMAT_PT ?><br>
            <?= SYSTEM_TLP ?><br><br>
            </strong>

            <div class="judul-grup">Jenis Dokumen yang harus dibawa:</div>

            <div class="keterangan-list">
            <ul>
                <?php 
                if ($rekanan->getField("REKANAN_TIPE_ID") == '7') { ?>
                    <li>Asli Kartu Tanda Penduduk (KTP)</li>
                    <li>NPWP dan</li> 
                    <li>Dokumen Asli Lainnya yang sudah diinput ke dalam sistem <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?></li>
                </ul>
                Setelah data dinyatakan valid, anda dapat melengkapi data-data selanjutnya dan mengikuti proses pengadaan secara elektronik pada situs e-Procurement kami.
                <?php 
                } else { ?>
                    <li>Surat ijin usaha yang berlaku (SIUP / SIUJK / SIUI atau lainnya)</li>
                    <li>Akte Pendirian Perusahaan dan Akte Perubahan Terakhir</li>
                    <li>Untuk PT, surat ketetapan dari Departemen Hukum dan HAM Republik Indonesia </li>
                    <li>Surat penetapan PKP dan NPWP</li>
                    <li>Sertifikasi Badan Usaha (SBU) usaha jasa konstruksi.</li>
                    <li>Surat Kuasa Khusus (apabila yang mendaftar bukan pusat)</li>
                    <li>Surat pernyataan mengenai penggunaan sistem e-procurement.</li>
                    <li>Rekening Koran</li>
                    <li>Asli/Fotocopy Kartu Tanda Penduduk (KTP) Pimpinan Perusahaan dan </li>
                    <li>Dokumen Asli Lainnya yang sudah diinput ke dalam sistem <?= SYSTEM_NAME.' '.SYSTEM_NAME_PT ?></li>
                </ul>
                Setelah data perusahaan dinyatakan valid, anda dapat melengkapi data-data perusahaan dan mengikuti proses pengadaan secara elektronik pada situs e-Procurement kami.                
                <?php 
                } ?>
            </div>
           <br><br>

            Terima kasih telah mendaftar. <br><br><br>

            <strong>Administrator <?= SYSTEM_NAME ?><br>
            <?= SYSTEM_NAME_PT ?></strong>
            <br><br>

            <!-- <div class="form-actions">
                <a href="app" class="btn btn-danger mr-1 text-white"> <i class="fa fa-arrow-left"></i> Kembali </a>
            </div> -->

        </div>
      </div>
    </div>
  </div>
</div>
