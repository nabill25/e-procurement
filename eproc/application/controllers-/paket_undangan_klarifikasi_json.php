<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");

class paket_undangan_klarifikasi_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		if (!$this->kauth->getInstance()->hasIdentity())
		{
		}

		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
	} 

	function add()
	{
		$this->load->model('Paketundanganklarifikasi');
		$paketundangan	= new Paketundanganklarifikasi();

		$reqId		= $this->input->post('reqId');
		$reqRekId	= $this->input->post('reqRekId');
		$reqMode	= $this->input->post('reqMode');

		$reqTanggalUndangan	= $this->input->post('reqTanggalUndangan');
		$reqJamUndangan	= $this->input->post('reqJamUndangan');
		$reqPeserta			= $this->input->post('reqPeserta');
		$reqPelaksanaan		= $this->input->post('reqPelaksanaan');
		$reqTempat			= $this->input->post('reqTempat');
		$reqEmail			= $this->input->post('reqEmail');
		$reqKeterangan		= str_replace("'","''",$_POST["reqKeterangan"]);

		$paketundangan->setField("PAKET_ID", $reqId);
		$paketundangan->setField("REKANAN_ID", $reqRekId);
		$paketundangan->setField("TANGGAL_UNDANGAN", dateToDBCheck($reqTanggalUndangan));
		$paketundangan->setField("JAM", $reqJamUndangan);
		$paketundangan->setField("PESERTA", $reqPeserta);
		$paketundangan->setField("PELAKSANAAN", $reqPelaksanaan);
		$paketundangan->setField("TEMPAT", $reqTempat);
		$paketundangan->setField("KETERANGAN", $reqKeterangan);
		$paketundangan->setField("CREATED_BY", $this->USER_LOGIN_ID);

		if($reqMode == "insert")
		{
			if($paketundangan->insert()) {
				$this->load->library("KMail");
				$mail = new KMail();
				$mail->AddAddress($reqEmail,'Undangan Klarifikasi');
				$mail->Subject  =  "Undangan Klarifikasi";
				$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/undangan_klarifikasi/".$reqId."/".$reqRekId."");
				// echo $body; die;
				$mail->MsgHTML($body);
				if($mail->Send())
				{
					echo "Data berhasil disimpan dan dikirim via email.";
				}
				else {
					echo "Data gagal disimpan dan dikirim via email.";
				}
			} else {
				echo "Data gagal disimpan dan dikirim via email.";
			}
		}
		else
		{
			if($paketundangan->update()) {
				$this->load->library("KMail");
				$mail = new KMail();
				$mail->AddAddress($reqEmail,'Undangan Klarifikasi');
				$mail->Subject  =  "Undangan Klarifikasi";
				$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/undangan_klarifikasi/".$reqId."/".$reqRekId."");
				// echo $body; die;
				$mail->MsgHTML($body);
				if($mail->Send())
				{
					echo "Data berhasil disimpan dan dikirim via email.";
				}
				else {
					echo "Data gagal disimpan dan dikirim via email.";
				}
			} else {
				echo "Data gagal disimpan dan dikirim via email.";
			}
		}

	}

	function delete()
	{
		$this->load->model('Berita');

		$berita	= new Berita();

		$reqId		= $this->input->get('reqId');

		$reqNama		= $this->input->post('reqNama');

		$berita	= new Berita();
		$berita->setField("BERITA_ID", $reqId);
		$berita->delete();

		echo "Data berhasil disimpan.";
	} 

}
?>
