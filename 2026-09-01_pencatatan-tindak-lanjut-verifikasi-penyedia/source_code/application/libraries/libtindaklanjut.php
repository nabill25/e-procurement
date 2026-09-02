<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 * Library fitur baru: Pencatatan Tindak Lanjut Kelengkapan Dokumen Penyedia.
 *
 * Meniru pola librekamjejak.php yang sudah ada (insert kejadian + render
 * timeline HTML + tombol pemicu), supaya cara pakainya familiar buat
 * developer eproc. BEDA dengan Rekam Jejak: fitur ini punya tabel sendiri
 * (REKANAN_TINDAK_LANJUT) yang memang punya kolom REKANAN_ID, karena Rekam
 * Jejak (tabel REKAM_JEJAK) hanya mengenal PAKET_ID / PERMOHONAN_PAKET_ID,
 * tidak cocok untuk konteks registrasi penyedia.
 *
 * Alur singkat:
 *   verifikatorMintaLengkapi()   -> status PERLU_DILENGKAPI, email ke penyedia
 *   penyediaKonfirmasiLengkap()  -> status SUDAH_DILENGKAPI, email ke verifikator
 *   verifikatorTandaiSelesai()   -> status TERVERIFIKASI
 *   kirimReminderOtomatis()      -> dipanggil cron, email pengingat ke penyedia
 */

class libtindaklanjut
{
    private $_CI;
    private $userLoginId = null;
    private $userNama = null;

    // Jeda hari sebelum pengingat otomatis berikutnya dikirim, dan batas
    // maksimal jumlah pengingat otomatis per rekanan. Dibungkus define()
    // supaya gampang di-override lewat constants.php kalau perlu, tanpa
    // mengubah library ini.
    const HARI_JEDA_REMINDER = 7;
    const MAKS_REMINDER      = 3;

    function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->load->library("kauth");
        $this->_CI->load->model("Rekanantindaklanjut");
        $this->_CI->load->model("Rekanan");

        // Saat dipanggil dari cron tidak ada user login, jadi jangan asumsikan
        // identity selalu ada. Pakai properti library sendiri, jangan sentuh
        // properti controller.
        if ($this->_CI->kauth->getInstance()->hasIdentity()) {
            $ident = $this->_CI->kauth->getInstance()->getIdentity();
            $this->userLoginId = $ident->USER_LOGIN_ID;
            $this->userNama    = $ident->USER_NAMA;
        }
    }

    /* ============================================================= */
    /* AKSI                                                           */
    /* ============================================================= */

    /**
     * Verifikator minta penyedia melengkapi berkas.
     * Dipanggil dari halaman Validasi Rekanan (tombol "Tindak Lanjut Kelengkapan").
     * Return array('ok' => bool simpan berhasil, 'email' => bool email terkirim).
     */
    function verifikatorMintaLengkapi($rekananId, $catatan)
    {
        $rekanan = new Rekanan();
        $rekanan->selectByParamsSimple(array("A.REKANAN_ID" => $rekananId));
        $rekanan->firstRow();

        $emailPenyedia = $rekanan->getField("EMAIL");
        $emailTerkirim = $this->__kirimEmailKePenyedia($rekananId, $rekanan, 'rekanan_perlu_lengkapi');

        $ok = $this->__catat(array(
            'REKANAN_ID'     => $rekananId,
            'STATUS'         => 'PERLU_DILENGKAPI',
            'JENIS'          => 'PERMINTAAN',
            'CATATAN'        => $catatan,
            'PIHAK'          => 'VERIFIKATOR',
            'CREATED_BY'     => $this->userLoginId,
            'EMAIL_TUJUAN'   => $emailPenyedia,
            'EMAIL_TERKIRIM' => $emailTerkirim,
        ));

        return array('ok' => $ok, 'email' => $emailTerkirim);
    }

    /**
     * Penyedia konfirmasi sudah melengkapi berkas sesuai catatan verifikator.
     * Return array('ok' => bool, 'email' => bool).
     */
    function penyediaKonfirmasiLengkap($rekananId, $catatan = '')
    {
        list($emailVerifikator, $namaVerifikator) = $this->__emailVerifikatorTujuan($rekananId);
        $emailTerkirim = $this->__kirimEmailKeVerifikator($rekananId, $emailVerifikator, $namaVerifikator);

        $ok = $this->__catat(array(
            'REKANAN_ID'     => $rekananId,
            'STATUS'         => 'SUDAH_DILENGKAPI',
            'JENIS'          => 'KONFIRMASI',
            'CATATAN'        => $catatan,
            'PIHAK'          => 'PENYEDIA',
            'CREATED_BY'     => $this->userLoginId,
            'EMAIL_TUJUAN'   => $emailVerifikator,
            'EMAIL_TERKIRIM' => $emailTerkirim,
        ));

        return array('ok' => $ok, 'email' => $emailTerkirim);
    }

    /**
     * Verifikator menyatakan dokumen penyedia sudah lengkap/oke.
     * Ini menutup satu siklus tindak lanjut (tidak menyentuh STATUS_VALIDASI
     * di tabel REKANAN, itu tetap lewat tombol "VALIDASI & MINTA REKOMENDASI"
     * yang sudah ada). Return array('ok' => bool, 'email' => null).
     */
    function verifikatorTandaiSelesai($rekananId, $catatan = '')
    {
        $ok = $this->__catat(array(
            'REKANAN_ID'     => $rekananId,
            'STATUS'         => 'TERVERIFIKASI',
            'JENIS'          => 'SELESAI',
            'CATATAN'        => $catatan,
            'PIHAK'          => 'VERIFIKATOR',
            'CREATED_BY'     => $this->userLoginId,
            'EMAIL_TUJUAN'   => null,
            'EMAIL_TERKIRIM' => false,
        ));

        return array('ok' => $ok, 'email' => null);
    }

    /**
     * Dipanggil oleh cronjobs_reminder_kelengkapan untuk 1 rekanan.
     * Kirim email pengingat ke penyedia, catat sebagai kejadian REMINDER
     * (status tetap PERLU_DILENGKAPI). Return array('ok' => bool, 'email' => bool).
     */
    function kirimReminderOtomatis($rekananId)
    {
        $rekanan = new Rekanan();
        $rekanan->selectByParamsSimple(array("A.REKANAN_ID" => $rekananId));
        $rekanan->firstRow();

        $emailPenyedia = $rekanan->getField("EMAIL");
        $emailTerkirim = $this->__kirimEmailKePenyedia($rekananId, $rekanan, 'rekanan_reminder_lengkapi');

        $ok = $this->__catat(array(
            'REKANAN_ID'     => $rekananId,
            'STATUS'         => 'PERLU_DILENGKAPI',
            'JENIS'          => 'REMINDER',
            'CATATAN'        => 'Pengingat otomatis dari sistem.',
            'PIHAK'          => 'SISTEM',
            'CREATED_BY'     => null,
            'EMAIL_TUJUAN'   => $emailPenyedia,
            'EMAIL_TERKIRIM' => $emailTerkirim,
        ));

        return array('ok' => $ok, 'email' => $emailTerkirim);
    }

    /* ============================================================= */
    /* BACA / TAMPILAN                                                */
    /* ============================================================= */

    /**
     * Ringkasan status "sekarang" untuk 1 rekanan. Return array:
     *   ada        bool  (sudah pernah ada tindak lanjut atau belum)
     *   status     string kode status
     *   label      string label enak dibaca
     *   kelas      string kelas badge bootstrap
     *   umur       string "3 hari lalu"
     *   follow_up  int    berapa kali penyedia dikirimi permintaan/pengingat
     */
    function statusRingkas($rekananId)
    {
        include_once("functions/date.func.php");

        $tl = new Rekanantindaklanjut();
        $tl->selectTerakhirByRekananId($rekananId);

        if ($tl->countRow() == 0) {
            return array(
                'ada'       => false,
                'status'    => null,
                'label'     => 'Belum ada tindak lanjut',
                'kelas'     => 'badge-secondary',
                'umur'      => '',
                'follow_up' => 0,
            );
        }

        $tl->firstRow();
        $status = $tl->getField("STATUS");
        $meta   = $this->__labelStatus($status);

        return array(
            'ada'       => true,
            'status'    => $status,
            'label'     => $meta[0],
            'kelas'     => $meta[1],
            'umur'      => $this->__umur($tl->getField("CREATED_DATE")),
            'follow_up' => $tl->hitungFollowUp($rekananId),
        );
    }

    /**
     * True kalau saat ini bola ada di penyedia (status terakhir
     * PERLU_DILENGKAPI). Dipakai untuk memunculkan banner di halaman penyedia.
     */
    function adaPermintaanTerbuka($rekananId)
    {
        $tl = new Rekanantindaklanjut();
        $tl->selectTerakhirByRekananId($rekananId);
        if ($tl->countRow() == 0) return false;
        $tl->firstRow();
        return $tl->getField("STATUS") == 'PERLU_DILENGKAPI';
    }

    /**
     * Catatan terakhir dari verifikator (dipakai di banner penyedia).
     */
    function catatanTerakhir($rekananId)
    {
        $tl = new Rekanantindaklanjut();
        $tl->selectTerakhirByRekananId($rekananId);
        if ($tl->countRow() == 0) return '';
        $tl->firstRow();
        return $tl->getField("CATATAN");
    }

    /**
     * Render riwayat tektok sebagai timeline HTML, gaya mirip
     * librekamjejak->viewRJ(). Dipakai di widget verifikator maupun penyedia.
     */
    function viewTimeline($rekananId)
    {
        include_once("functions/date.func.php");

        $tl = new Rekanantindaklanjut();
        $tl->selectByRekananId($rekananId);

        $html  = '<h4 style="margin-top:0; text-align:center"><i><b>RIWAYAT TINDAK LANJUT KELENGKAPAN DOKUMEN</b></i></h4>';
        $html .= '<div style="max-height:360px; overflow:auto; padding:0 5px; border-top:1px solid #eee; margin-top:8px">';

        if ($tl->countRow() > 0) {
            while ($tl->nextRow()) {
                $meta  = $this->__labelStatus($tl->getField("STATUS"));
                $pihak = $this->__labelPihak($tl->getField("PIHAK"));
                $tgl   = dateTimeToPageCheck($tl->getField("CREATED_DATE"));
                $jenis = $tl->getField("JENIS");

                $html .= '<div style="border-bottom:1px solid #eee; padding:10px 0">';
                $html .= '  <span class="badge ' . $meta[1] . '" style="font-size:10px">' . $meta[0] . '</span> ';
                $html .= '  <span style="font-size:11px; color:#888">' . $this->__labelJenis($jenis) . ' &middot; oleh ' . $pihak . ' &middot; ' . $tgl . '</span>';
                if ($tl->getField("CATATAN")) {
                    $html .= '  <p style="margin:5px 0 0 0; font-size:12.5px">&ldquo;' . htmlspecialchars($tl->getField("CATATAN")) . '&rdquo;</p>';
                }
                if ($tl->getField("EMAIL_TUJUAN")) {
                    if ($tl->getField("EMAIL_TERKIRIM")) {
                        $html .= '  <p style="margin:2px 0 0 0; font-size:10px; color:#3a913a">Email terkirim ke ' . htmlspecialchars($tl->getField("EMAIL_TUJUAN")) . '</p>';
                    } else {
                        $html .= '  <p style="margin:2px 0 0 0; font-size:10px; color:#c00">Email ke ' . htmlspecialchars($tl->getField("EMAIL_TUJUAN")) . ' GAGAL terkirim (catatan tetap tersimpan).</p>';
                    }
                }
                $html .= '</div>';
            }
        } else {
            $html .= '<p style="text-align:center; color:#888; padding:20px 0">Belum ada riwayat tindak lanjut untuk penyedia ini.</p>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Panel tindak lanjut untuk SISI VERIFIKATOR, dirender langsung di halaman
     * (bukan popup) supaya aman dipakai di halaman biasa maupun di dalam iframe
     * (halaman Validasi Rekanan lewat modal Daftar Penyedia berupa iframe
     * bertingkat, jadi openAddLg tidak bisa dipanggil dari situ).
     *
     * Semua id diberi awalan tlv_ supaya tidak bentrok dengan elemen halaman.
     * Setelah aksi berhasil, halaman (atau iframe) di-reload.
     */
    function panelVerifikator($rekananId)
    {
        $rid      = (int) $rekananId;
        $ringkas  = $this->statusRingkas($rekananId);
        $badge    = '<span class="badge ' . $ringkas['kelas'] . '">' . $ringkas['label'] . '</span>';
        $infoUmur = ($ringkas['ada'] && $ringkas['umur'])
            ? ' <small style="color:#888">sejak ' . $ringkas['umur'] . ', sudah ' . $ringkas['follow_up'] . 'x diingatkan</small>'
            : '';
        $timeline = $this->viewTimeline($rekananId);

        return <<<HTML
<div class="card" style="border:1px solid #e4e7eb; margin-top:12px">
  <div class="card-header" style="padding:10px 14px; background:#f8f9fa; cursor:pointer" onclick="var b=document.getElementById('tlv_body_{$rid}'); b.style.display = (b.style.display==='none')?'block':'none';">
    <b><i class="fa fa-envelope-o"></i> Tindak Lanjut Kelengkapan Dokumen</b> &nbsp; {$badge}{$infoUmur}
    <span style="float:right; color:#888">klik untuk buka/tutup</span>
  </div>
  <div id="tlv_body_{$rid}" class="card-body" style="display:none; padding:14px">
    <div id="tlv_timeline_{$rid}">{$timeline}</div>
    <hr>
    <label style="font-size:12px; font-weight:600">Catatan untuk penyedia (jelaskan dokumen apa yang kurang)</label>
    <textarea id="tlv_catatan_{$rid}" class="form-control" rows="3" placeholder="Contoh: NPWP yang diupload sudah kedaluwarsa, mohon upload NPWP terbaru. Akta perubahan terakhir juga belum ada."></textarea>
    <div style="margin-top:10px">
      <button type="button" id="tlv_kirim_{$rid}" class="btn btn-primary text-white"><i class="fa fa-paper-plane"></i> Kirim Catatan &amp; Email ke Penyedia</button>
      <button type="button" id="tlv_selesai_{$rid}" class="btn btn-success text-white"><i class="fa fa-check"></i> Tandai Dokumen Sudah Lengkap</button>
    </div>
    <p style="font-size:11px; color:#888; margin-top:8px">
      "Kirim Catatan" mengubah status jadi <b>Perlu Dilengkapi</b> dan mengirim email otomatis ke penyedia.
      "Tandai Dokumen Sudah Lengkap" hanya menutup catatan tindak lanjut, tidak menggantikan tombol
      "KIRIM KE APPROVAL VMS" / "VALIDASI &amp; MINTA REKOMENDASI".
    </p>
  </div>
</div>
<script type="text/javascript">
(function(){
  var \$ = window.jQuery || window.\$; if (!\$) { return; }
  var rid = {$rid};
  var btns = '#tlv_kirim_'+rid+', #tlv_selesai_'+rid;
  function kirim(url, butuhCatatan){
    var catatan = \$.trim(\$('#tlv_catatan_'+rid).val());
    if (butuhCatatan && catatan === '') { alert('Catatan wajib diisi.'); return; }
    \$(btns).attr('disabled','disabled');
    \$.ajax({ type:'POST', url:url, data:{ reqRekananId: rid, reqCatatan: catatan },
      success: function(resp){
        var d = (typeof resp === 'string') ? JSON.parse(resp) : resp;
        if (window.\$ && \$.messager) { \$.messager.alert('Info', d.pesan, d.status==='sukses'?'info':'error'); } else { alert(d.pesan); }
        if (d.status === 'sukses') { setTimeout(function(){ location.reload(); }, 600); }
        else { \$(btns).removeAttr('disabled'); }
      },
      error: function(){ alert('Gagal menghubungi server.'); \$(btns).removeAttr('disabled'); }
    });
  }
  \$('#tlv_kirim_'+rid).click(function(){ kirim('rekanan_tindak_lanjut_json/kirimCatatan', true); });
  \$('#tlv_selesai_'+rid).click(function(){ if (confirm('Yakin dokumen penyedia ini sudah lengkap?')) kirim('rekanan_tindak_lanjut_json/tandaiSelesai', false); });
})();
</script>
HTML;
    }

    /**
     * Panel tindak lanjut untuk SISI PENYEDIA, dirender langsung di halaman
     * Konfirmasi Pendaftaran. Caller yang memutuskan kapan ditampilkan
     * (biasanya hanya kalau adaPermintaanTerbuka() true).
     */
    function panelPenyedia($rekananId)
    {
        $rid      = (int) $rekananId;
        $catatan  = htmlspecialchars($this->catatanTerakhir($rekananId));
        $timeline = $this->viewTimeline($rekananId);

        return <<<HTML
<div class="card" style="border:1px solid #e4e7eb; margin:12px 0">
  <div class="card-header" style="padding:10px 14px; background:#fbeceb">
    <b><i class="fa fa-exclamation-circle"></i> Verifikator meminta Anda melengkapi dokumen</b>
  </div>
  <div class="card-body" style="padding:14px">
    <div class="alert alert-warning" style="font-size:12.5px">Catatan verifikator: <i>&ldquo;{$catatan}&rdquo;</i></div>
    <div id="tlp_timeline_{$rid}">{$timeline}</div>
    <hr>
    <label style="font-size:12px; font-weight:600">Keterangan singkat (opsional)</label>
    <textarea id="tlp_catatan_{$rid}" class="form-control" rows="2" placeholder="Contoh: NPWP terbaru dan akta perubahan sudah saya upload ulang."></textarea>
    <div style="margin-top:10px">
      <button type="button" id="tlp_kirim_{$rid}" class="btn btn-primary text-white"><i class="fa fa-check"></i> Sudah Saya Lengkapi</button>
    </div>
    <p style="font-size:11px; color:#888; margin-top:8px">Setelah diklik, verifikator akan menerima pemberitahuan untuk memeriksa ulang dokumen Anda.</p>
  </div>
</div>
<script type="text/javascript">
(function(){
  var \$ = window.jQuery || window.\$; if (!\$) { return; }
  var rid = {$rid};
  \$('#tlp_kirim_'+rid).click(function(){
    if (!confirm('Kirim konfirmasi bahwa dokumen sudah Anda lengkapi?')) return;
    \$('#tlp_kirim_'+rid).attr('disabled','disabled');
    \$.ajax({ type:'POST', url:'rekanan_tindak_lanjut_json/konfirmasiLengkap',
      data:{ reqRekananId: rid, reqCatatan: \$.trim(\$('#tlp_catatan_'+rid).val()) },
      success: function(resp){
        var d = (typeof resp === 'string') ? JSON.parse(resp) : resp;
        if (window.\$ && \$.messager) { \$.messager.alert('Info', d.pesan, d.status==='sukses'?'info':'error'); } else { alert(d.pesan); }
        if (d.status === 'sukses') { setTimeout(function(){ location.reload(); }, 800); }
        else { \$('#tlp_kirim_'+rid).removeAttr('disabled'); }
      },
      error: function(){ alert('Gagal menghubungi server.'); \$('#tlp_kirim_'+rid).removeAttr('disabled'); }
    });
  });
})();
</script>
HTML;
    }

    /* ============================================================= */
    /* PRIVATE                                                        */
    /* ============================================================= */

    private function __catat($data)
    {
        $tl = new Rekanantindaklanjut();
        foreach ($data as $k => $v) {
            $tl->setField($k, $v);
        }
        return $tl->insert() ? true : false;
    }

    /**
     * Kirim email ke penyedia. $template = nama view di application/views/email/.
     * Return true kalau terkirim, false kalau gagal/tidak ada email.
     */
    private function __kirimEmailKePenyedia($rekananId, $rekanan, $template)
    {
        $email = $rekanan->getField("EMAIL");
        $nama  = $rekanan->getField("NAMA");
        if (!$email) {
            log_message('error', 'libtindaklanjut: rekanan ' . $rekananId . ' tidak punya EMAIL, notifikasi tidak dikirim.');
            return false;
        }
        return $this->__send($email, $nama,
            'Permintaan Kelengkapan Dokumen dari ' . SYSTEM_NAME . ' ' . SYSTEM_NAME_PT,
            $template, $rekananId);
    }

    private function __kirimEmailKeVerifikator($rekananId, $email, $nama)
    {
        if (!$email) {
            log_message('error', 'libtindaklanjut: email verifikator penangani rekanan ' . $rekananId . ' tidak ketemu, notifikasi tidak dikirim.');
            return false;
        }
        return $this->__send($email, $nama,
            'Penyedia Sudah Melengkapi Dokumen - ' . SYSTEM_NAME . ' ' . SYSTEM_NAME_PT,
            'rekanan_sudah_lengkap', $rekananId);
    }

    private function __send($email, $nama, $subject, $template, $rekananId)
    {
        try {
            $this->_CI->load->library("KMail");
            $mail = new KMail();
            $mail->Subject = $subject;
            $mail->AddAddress($email, $nama ? $nama : $email);
            $body = @file_get_contents(SYSTEM_URL_EMAIL . "/main/loadUrl/email/" . $template . "/" . $rekananId);
            if ($body === false || $body === '') {
                log_message('error', 'libtindaklanjut: gagal render email "' . $template . '" untuk rekanan ' . $rekananId);
                return false;
            }
            $mail->MsgHTML($body);
            if (!$mail->Send()) {
                log_message('error', 'libtindaklanjut: KMail gagal kirim "' . $template . '" ke ' . $email . ': ' . $mail->ErrorInfo);
                return false;
            }
            return true;
        } catch (Exception $e) {
            log_message('error', 'libtindaklanjut: exception saat kirim "' . $template . '" ke ' . $email . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tentukan email tujuan notifikasi "penyedia sudah melengkapi".
     * Prioritas: USER_LOGIN verifikator yang terakhir menangani (kalau
     * berupa alamat email), lalu konstanta EMAIL_TINDAK_LANJUT_FALLBACK
     * kalau di-define di constants.php, lalu SYSTEM_EMAIL_VMS.
     * Return array(email, nama).
     */
    private function __emailVerifikatorTujuan($rekananId)
    {
        $tl = new Rekanantindaklanjut();
        $tl->selectVerifikatorPenangani($rekananId);
        if ($tl->countRow() > 0) {
            $tl->firstRow();
            $login = trim($tl->getField("USER_LOGIN"));
            if ($login && strpos($login, '@') !== false) {
                return array($login, $tl->getField("USER_NAMA"));
            }
        }
        if (defined('EMAIL_TINDAK_LANJUT_FALLBACK') && EMAIL_TINDAK_LANJUT_FALLBACK) {
            return array(EMAIL_TINDAK_LANJUT_FALLBACK, 'Tim Verifikasi Penyedia');
        }
        if (defined('SYSTEM_EMAIL_VMS') && SYSTEM_EMAIL_VMS && strpos(SYSTEM_EMAIL_VMS, '@') !== false) {
            return array(SYSTEM_EMAIL_VMS, 'Tim Verifikasi Penyedia');
        }
        return array(null, null);
    }

    private function __labelStatus($status)
    {
        $map = array(
            'PERLU_DILENGKAPI' => array('Perlu Dilengkapi', 'badge-danger'),
            'SUDAH_DILENGKAPI' => array('Sudah Dilengkapi', 'badge-info'),
            'TERVERIFIKASI'    => array('Terverifikasi', 'badge-success'),
        );
        return isset($map[$status]) ? $map[$status] : array($status, 'badge-secondary');
    }

    private function __labelJenis($jenis)
    {
        $map = array(
            'PERMINTAAN' => 'Permintaan kelengkapan',
            'KONFIRMASI' => 'Konfirmasi penyedia',
            'REMINDER'   => 'Pengingat otomatis',
            'SELESAI'    => 'Dinyatakan selesai',
        );
        return isset($map[$jenis]) ? $map[$jenis] : $jenis;
    }

    private function __labelPihak($pihak)
    {
        $map = array('VERIFIKATOR' => 'Verifikator', 'PENYEDIA' => 'Penyedia', 'SISTEM' => 'Sistem');
        return isset($map[$pihak]) ? $map[$pihak] : $pihak;
    }

    private function __umur($tanggal)
    {
        $ts = strtotime($tanggal);
        if (!$ts) return '';
        $selisih = time() - $ts;
        if ($selisih < 3600)   return floor($selisih / 60) . ' menit lalu';
        if ($selisih < 86400)  return floor($selisih / 3600) . ' jam lalu';
        return floor($selisih / 86400) . ' hari lalu';
    }
}
