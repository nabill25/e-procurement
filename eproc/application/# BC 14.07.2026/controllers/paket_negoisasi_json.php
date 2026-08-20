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

class paket_negoisasi_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function add() 
	{
		$this->load->model("PaketNegoisasi");
		
		$reqId = httpFilterPost("reqId");
		$reqPaketPenawaranId = $_POST["reqPaketPenawaranId"];
		$reqUnitPriceNegosiasi = $_POST["reqUnitPriceNegosiasi"];
		$reqJumlahNegosiasi = $_POST["reqJumlahNegosiasi"];
		$reqQuantity = $_POST["reqQuantity"];
	
		$paket_negosiasi = new PaketNegoisasi();
		$paket_negosiasi->setField("PAKET_ID", $reqId);
		$paket_negosiasi->delete();
	
		for($i=0;$i<count($reqPaketPenawaranId);$i++)
		{
			$paket_negosiasi = new PaketNegoisasi();
			$paket_negosiasi->setField("PAKET_ID", $reqId);
			$paket_negosiasi->setField("JUMLAH", CommaToDot(dotToNo($reqJumlahNegosiasi[$i])));
			$paket_negosiasi->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
			$paket_negosiasi->setField("UNIT_PRICE", CommaToDot(dotToNo($reqUnitPriceNegosiasi[$i])));
			$paket_negosiasi->setField("QUANTITY", $reqQuantity[$i]);
			$paket_negosiasi->insert();
			unset($paket_negosiasi);
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function setujui() 
	{

		$this->load->model("PaketNegoisasi");
		
		$paket_negosiasi = new PaketNegoisasi();
		
		$reqId = $this->input->get("reqId");
	
		$paket_negosiasi->setField("PAKET_PENAWARAN_ID", $reqId);
		$paket_negosiasi->updateSetujui();
		
	}	
	
	function negosiasi() 
	{

		$this->load->model("PaketNegoisasi");
		
		$paket_negosiasi = new PaketNegoisasi();
		
		$reqId = $this->input->get("reqId");
		$reqNilai = $this->input->get("reqNilai");
	
		$paket_negosiasi->setField("UNIT_PRICE", CommaToNo($reqNilai));
		$paket_negosiasi->setField("PAKET_PENAWARAN_ID", $reqId);
		if($paket_negosiasi->updateUnitPrice())
			echo "Data berhasil disimpan.";
		else
			echo "Data gagal disimpan.";
		
	}	
	
	function ambil_negosiasi()
	{
		$this->load->model("PaketNegoisasi");
		
		$reqId = $this->input->get("reqId");

		$paket_negosiasi = new PaketNegoisasi();
		$paket_negosiasi->selectByParams(array("PAKET_PENAWARAN_ID" => $reqId));
		$paket_negosiasi->firstRow();
		
		$data["UNIT_PRICE"] = numberToIna($paket_negosiasi->getField("UNIT_PRICE"));
		$data["TOTAL"] = numberToIna($paket_negosiasi->getField("TOTAL"));
		$data["SETUJUI"] = $paket_negosiasi->getField("SETUJUI");
		
		echo json_encode($data);
			
	}	
	
	function negosiasi_lelang()
	{
		$this->load->model("RekananPaketPenawaran");
		$this->load->model("PaketNegoisasi");
		
		$reqId = $this->input->post("reqId");
		$reqPaketRekananId = $this->input->post("reqPaketRekananId");
		$reqPaketPenawaranId = $_POST["reqPaketPenawaranId"];
		$reqUnitPriceNegosiasi = $_POST["reqUnitPriceNegosiasi"];
		$reqJumlahNegosiasi = $_POST["reqJumlahNegosiasi"];
		$reqQuantity = $_POST["reqQuantity"];
		$reqUnitPricePenawaran = $_POST["reqUnitPricePenawaran"]; 
		$reqJumlahPenawaran = $_POST["reqJumlahPenawaran"]; 
		$reqTotalPenawaran = CommaToDot(dotToNo($_POST["reqTotalPenawaran"]));
		
		/* VALIDASI ENTRIAN PENAWARAN 
		$hasilPenawaran = 0;

		$rekanan_paket_penawaran = new RekananPaketPenawaran();
		$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
		$rekanan_paket_penawaran->delete();
				
		for($i=0;$i<count($reqPaketPenawaranId);$i++)
		{
			$rekanan_paket_penawaran = new RekananPaketPenawaran();
			$rekanan_paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
			$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$rekanan_paket_penawaran->setField("DELIVERY_DATE", "NULL");
			$rekanan_paket_penawaran->setField("QUANTITY", CommaToDot(dotToNo($reqQuantity[$i])));
			$rekanan_paket_penawaran->setField("UNIT_PRICE", CommaToDot(dotToNo($reqUnitPricePenawaran[$i])));
			$rekanan_paket_penawaran->setField("JUMLAH", CommaToDot(dotToNo($reqJumlahPenawaran[$i])));
			$rekanan_paket_penawaran->insert();
			unset($rekanan_paket_penawaran);
			
			$hasilPenawaran += CommaToDot(dotToNo($reqJumlahPenawaran[$i]));	
		}
		
		if($hasilPenawaran == $reqTotalPenawaran)
		{}
		else
		{
			echo "0-Rincian penawaran tidak sesuai, total rincian = ".numberToIna($hasilPenawaran).", sedangkan total penawaran = ".numberToIna($reqTotalPenawaran).".";	
			return;
		}
		*/
		$paket_negosiasi = new PaketNegoisasi();
		$paket_negosiasi->setField("PAKET_ID", $reqId);
		$paket_negosiasi->delete();
	
		for($i=0;$i<count($reqPaketPenawaranId);$i++)
		{
			$paket_negosiasi = new PaketNegoisasi();
			$paket_negosiasi->setField("PAKET_ID", $reqId);
			$paket_negosiasi->setField("JUMLAH", CommaToDot(dotToNo($reqJumlahNegosiasi[$i])));
			$paket_negosiasi->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
			$paket_negosiasi->setField("UNIT_PRICE", CommaToDot(dotToNo($reqUnitPriceNegosiasi[$i])));
			$paket_negosiasi->setField("QUANTITY", $reqQuantity[$i]);
			$paket_negosiasi->setField("BIAYA_KIRIM", "NULL");
			$paket_negosiasi->setField("BIAYA_KIRIM_AWAL", "NULL");
			$paket_negosiasi->insert();
			unset($paket_negosiasi);
		}
		
		echo "1-Data berhasil disimpan.";
			
	}	
	
	function undangan()
	{
		$this->load->library("KMail");	
		$this->load->model("PaketRekanan");
		$this->load->model("Metode");
		
		$paket_rekanan = new PaketRekanan();
		$metode = new Metode();
		
		$reqId = $this->input->post("reqId");
		$reqPaketRekananId = $this->input->post("reqPaketRekananId"); 
		$reqPaketTahapId = $this->input->post("reqPaketTahapId");
		$reqTanggal = $this->input->post("reqTanggalNegosiasi"); 
		$reqJamMulai = $this->input->post("reqJamMulai"); 
		$reqMenitMulai = $this->input->post("reqMenitMulai"); 
		$reqUrut = $this->input->post("reqUrut"); 

		// Insert to paket_negosiasi
		$this->load->model("RekananPaketPenawaran");
		$this->load->model("PaketNegoisasi");
		
		$reqPaketPenawaranId = $_POST["reqPaketPenawaranId"];
		$reqUnitPriceNegosiasi = $_POST["reqUnitPriceNegosiasi"];
		$reqJumlahNegosiasi = $_POST["reqJumlahNegosiasi"];
		$reqQuantity = $_POST["reqQuantity"];
		$reqUnitPricePenawaran = $_POST["reqUnitPricePenawaran"]; 
		$reqJumlahPenawaran = $_POST["reqJumlahPenawaran"]; 
		$reqTotalPenawaran = CommaToDot(dotToNo($_POST["reqTotalPenawaran"])); 

		$paket_negosiasi = new PaketNegoisasi();
		$paket_negosiasi->setField("PAKET_ID", $reqId);
		$paket_negosiasi->delete();
	
		for($i=0;$i<count($reqPaketPenawaranId);$i++)
		{
			$paket_negosiasi = new PaketNegoisasi();
			$paket_negosiasi->setField("PAKET_ID", $reqId);
			$paket_negosiasi->setField("JUMLAH", CommaToDot(dotToNo($reqJumlahNegosiasi[$i])));
			$paket_negosiasi->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
			$paket_negosiasi->setField("UNIT_PRICE", CommaToDot(dotToNo($reqUnitPriceNegosiasi[$i])));
			$paket_negosiasi->setField("QUANTITY", $reqQuantity[$i]);
			$paket_negosiasi->setField("BIAYA_KIRIM", "NULL");
			$paket_negosiasi->setField("BIAYA_KIRIM_AWAL", "NULL");
			$paket_negosiasi->insert();
			unset($paket_negosiasi);
		}
		// End Insert to paket_negosiasi

		if(trim($reqTanggal) == "")
		{
			echo "Tanggal harus diisi.";	
			return;
		}
		
		if(trim($reqJamMulai) == "")
		{
			echo "Jam harus diisi.";	
			return;
		}

		if(trim($reqMenitMulai) == "")
		{
			echo "Menit harus diisi.";	
			return;
		}
		
		if($reqTanggal == "")
			$tanggal_awal = "NULL";
		elseif($reqJamMulai[$i] == "")		
			$tanggal_awal = "TO_DATE('".$reqTanggal."', 'DD-MM-YYYY')";
		else
		{
			$tanggal_awal = "TO_TIMESTAMP('".$reqTanggal." ".$reqJamMulai.":".$reqMenitMulai."', 'DD-MM-YYYY HH24:MI')";
			$jam_awal = $reqJamMulai.":".$reqMenitMulai;
		}
		
		if($reqPaketTahapId == "")
		{
			$metode->setField("PAKET_ID", $reqId);
			$metode->setField("NAMA", "Negosiasi");
			$metode->setField("HADIR", "0");
			$metode->setField("TAMPILKAN", "1");
			$metode->setField("TANGGAL_AWAL", $tanggal_awal);
			$metode->setField("TANGGAL_AKHIR",  "NULL");
			$metode->setField("JAM_AWAL", $jam_awal);
			$metode->setField("JAM_AKHIR", "");
			$metode->setField("URUT", ValToNull($reqUrut));
			$metode->insert();	
		}
		else
		{
			$metode->setField("TANGGAL_AWAL", $tanggal_awal);
			$metode->setField("JAM_AWAL", $jam_awal);
			$metode->setField("PAKET_TAHAP_ID", $reqPaketTahapId);
			$metode->update();					
		}
	
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
      	$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;

		$paket_rekanan->selectByParamsEmail(array('PAKET_REKANAN_ID' => coalesce($reqPaketRekananId, 0)));
		$paket_rekanan->firstRow();


		$mail = new KMail();
		$mail->AddAddress($paket_rekanan->getField("EMAIL") , $paket_rekanan->getField("NAMA"));
		$mail->Subject  =  "Undangan Pembuktian dan Negosiasi - ".$reqNama;
		$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/negosiasi_paket/".$reqId."/".$reqPaketRekananId);
		$mail->MsgHTML($body);
		if($mail->Send())
		{
			$paket_rekanan->setField("FIELD", "DI_EMAIL_NEGOSIASI");
			$paket_rekanan->setField("FIELD_VALUE", "(COALESCE(DI_EMAIL_NEGOSIASI, 0) + 1)");
			$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$paket_rekanan->update();			
			echo "Kirim Undangan berhasil.";
		}
		else
			echo "Kirim Undangan gagal.";
	}	

	function undanganauction()
	{
		$this->load->library("KMail");	
		$this->load->model("PaketRekanan");
		$this->load->model("Metode");
		
		$paket_rekanan = new PaketRekanan();
		$metode = new Metode();
		
		$reqId = $this->input->post("reqId");
		$reqPaketRekananId = $this->input->post("reqPaketRekananId"); 
		$reqPaketTahapId = $this->input->post("reqPaketTahapId");
		$reqTanggal = $this->input->post("reqTanggalNegosiasi"); 
		$reqJamMulai = $this->input->post("reqJamMulai"); 
		$reqMenitMulai = $this->input->post("reqMenitMulai"); 
		$reqUrut = $this->input->post("reqUrut"); 

		// Insert to paket_negosiasi
		$this->load->model("RekananPaketPenawaran");
		$this->load->model("PaketNegoisasi");
		
		$reqPaketPenawaranId = $_POST["reqPaketPenawaranId"];
		$reqUnitPriceNegosiasi = $_POST["reqUnitPriceNegosiasi"];
		$reqJumlahNegosiasi = $_POST["reqJumlahNegosiasi"];
		$reqQuantity = $_POST["reqQuantity"];
		$reqUnitPricePenawaran = $_POST["reqUnitPricePenawaran"]; 
		$reqJumlahPenawaran = $_POST["reqJumlahPenawaran"]; 
		$reqTotalPenawaran = CommaToDot(dotToNo($_POST["reqTotalPenawaran"])); 

		$paket_negosiasi = new PaketNegoisasi();
		$paket_negosiasi->setField("PAKET_ID", $reqId);
		$paket_negosiasi->delete(); 

		if(trim($reqTanggal) == "")
		{
			echo "Tanggal harus diisi.";	
			return;
		}
		
		if(trim($reqJamMulai) == "")
		{
			echo "Jam harus diisi.";	
			return;
		}

		if(trim($reqMenitMulai) == "")
		{
			echo "Menit harus diisi.";	
			return;
		}
		
		if($reqTanggal == "")
			$tanggal_awal = "NULL";
		elseif($reqJamMulai[$i] == "")		
			$tanggal_awal = "TO_DATE('".$reqTanggal."', 'DD-MM-YYYY')";
		else
		{
			$tanggal_awal = "TO_TIMESTAMP('".$reqTanggal." ".$reqJamMulai.":".$reqMenitMulai."', 'DD-MM-YYYY HH24:MI')";
			$jam_awal = $reqJamMulai.":".$reqMenitMulai;
		} 

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
      	$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;
		$reqMultiPemenang = $paketInfo->multi_pemenang;
	
        $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
     	$urut=1;
     	$html = '';
        while($paket_rekanan->nextRow())
        { 
			$paket_rekanan_satuan = new PaketRekanan();
        	$paket_rekanan_satuan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => coalesce($paket_rekanan->getField("REKANAN_ID"), 0)), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
			$paket_rekanan_satuan->firstRow();

	        $paket_rekanan2 = new PaketRekanan();
			$paket_rekanan2->selectByParamsEmail(array('PAKET_REKANAN_ID' => coalesce($paket_rekanan_satuan->getField("PAKET_REKANAN_ID"), 0)));
			$paket_rekanan2->firstRow();
			// echo $paket_rekanan_satuan->getField("PAKET_REKANAN_ID").'<br>';
			// echo $paket_rekanan2->getField("EMAIL").'---'.$paket_rekanan2->getField("NAMA");

			$mail = new KMail();
			$mail->AddAddress($paket_rekanan2->getField("EMAIL") , $paket_rekanan2->getField("NAMA"));
			$mail->Subject  =  "Notifikasi Pembuktian - ".$reqNama;

			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/negosiasi_paket_multipemenang/".$reqId."/".$paket_rekanan_satuan->getField("PAKET_REKANAN_ID"));
			// if ($reqMultiPemenang == '1') { // Multi Pemenang pakai Template khusus
			// } else {
			// 	$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/negosiasi_paket_multipemenang/".$reqId."/".$paket_rekanan_satuan->getField("PAKET_REKANAN_ID"));
			// }

			$mail->MsgHTML($body);
			if($mail->Send())
			{
				$paket_rekanan2->setField("FIELD", "DI_EMAIL");
				$paket_rekanan2->setField("FIELD_VALUE", "(COALESCE(DI_EMAIL, 0) + 1)");
				$paket_rekanan2->setField("PAKET_REKANAN_ID", $paket_rekanan_satuan->getField("PAKET_REKANAN_ID"));
				$paket_rekanan2->update();			
				$html .= "Kirim Notifikasi berhasil ke ".$paket_rekanan2->getField("NAMA")."<br>";
			}
			else {
				$html .= "Kirim Notifikasi gagal ke ".$paket_rekanan2->getField("NAMA")."<br>";
			}
		}
		echo $html;
	}	

	function undanganNegosiasi()
	{
		$this->load->library("KMail");	
		$this->load->model("PaketRekanan");
		$this->load->model("Metode");
		
		$paket_rekanan = new PaketRekanan();
		$metode = new Metode();
		
		$reqId = $this->input->get("reqId");

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
      	$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;

		$paket_rekanan->selectByParamsEmail(array('PAKET_REKANAN_ID' => coalesce($reqPaketRekananId, 0)));
		$paket_rekanan->firstRow();


		$mail = new KMail();
		$mail->AddAddress($paket_rekanan->getField("EMAIL") , $paket_rekanan->getField("NAMA"));
		$mail->Subject  =  "Undangan Pembuktian dan Negosiasi - ".$reqNama;
		$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/negosiasi_paket/".$reqId."/".$reqPaketRekananId);
		$mail->MsgHTML($body);
		if($mail->Send())
		{
			$paket_rekanan->setField("FIELD", "DI_EMAIL_NEGOSIASI");
			$paket_rekanan->setField("FIELD_VALUE", "(COALESCE(DI_EMAIL_NEGOSIASI, 0) + 1)");
			$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$paket_rekanan->update();			
			echo "Kirim Undangan berhasil.";
		}
		else
			echo "Kirim Undangan gagal.";
	}	

	function undanganNegosiasiChat()
	{
		$this->load->library("KMail");	
		$this->load->model("PaketRekanan");
		$this->load->model("Metode");
		
		$paket_rekanan = new PaketRekanan();
		$metode = new Metode();
		
		$reqId = $this->input->get("reqId");
		$reqPaketRekananId = $this->input->get("reqPaketRekananId"); 
		$reqJenis = $this->input->get("reqJenis"); 
 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
      	$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;

		$paket_rekanan->selectByParamsEmail(array('PAKET_REKANAN_ID' => coalesce($reqPaketRekananId, 0)));
		$paket_rekanan->firstRow();


		$mail = new KMail();
		$mail->AddAddress($paket_rekanan->getField("EMAIL") , $paket_rekanan->getField("NAMA"));
		if($reqJenis == '1') { // Auction
			$mail->Subject  =  "Undangan e-Reverse Auction - ".$reqNama;
			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/undangan_negosiasi_reverse/".$reqId."/".$reqPaketRekananId);
			$jenis = "e-Reverse Auction";
		} else {
			$mail->Subject  =  "Undangan Negosiasi - ".$reqNama;
			$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/undangan_negosiasi_chat/".$reqId."/".$reqPaketRekananId);
			$jenis = "Negosiasi";
		}

		$mail->MsgHTML($body);
		if($mail->Send())
		{
			$paket_rekanan->setField("FIELD", "DI_EMAIL_NEGOSIASI_2");
			$paket_rekanan->setField("FIELD_VALUE", "(COALESCE(DI_EMAIL_NEGOSIASI_2, 0) + 1)");
			$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$paket_rekanan->update();			
			echo "Kirim Undangan ".$jenis." berhasil.";
		}
		else
			echo "Kirim Undangan ".$jenis." gagal.";
	}

	// function undanganNegosiasiAuction()
	// {
	// 	$this->load->library("KMail");	
	// 	$this->load->model("PaketRekanan");
	// 	$this->load->model("Metode");
		
	// 	$paket_rekanan = new PaketRekanan();
	// 	$metode = new Metode();
		
	// 	$reqId = $this->input->post("reqId"); 

	// 	$this->load->library("paketinfo"); $paketInfo = new paketinfo();
 //      	$paketInfo->getPaket($reqId);
	// 	$reqNama = $paketInfo->nama;
	// 	$reqMultiPemenang = $paketInfo->multi_pemenang;
	
 //        $paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId, "A.LULUS_PENDAFTARAN" => 1, "A.KIRIM_PENAWARAN" => 1, "A.LULUS_PENAWARAN" => 1), -1, -1, "", "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
 //     	$urut=1;
 //     	$html = '';
 //        while($paket_rekanan->nextRow())
 //        { 
	// 		$paket_rekanan_satuan = new PaketRekanan();
 //        	$paket_rekanan_satuan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => coalesce($paket_rekanan->getField("REKANAN_ID"), 0)), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 ");
	// 		$paket_rekanan_satuan->firstRow();

	//         $paket_rekanan2 = new PaketRekanan();
	// 		$paket_rekanan2->selectByParamsEmail(array('PAKET_REKANAN_ID' => coalesce($paket_rekanan_satuan->getField("PAKET_REKANAN_ID"), 0)));
	// 		$paket_rekanan2->firstRow(); 

	// 		$mail = new KMail();
	// 		$mail->AddAddress($paket_rekanan2->getField("EMAIL") , $paket_rekanan2->getField("NAMA"));
	// 		$mail->Subject  =  "Undangan Reverse Auction - ".$reqNama;

	// 		$body = file_get_contents(SYSTEM_URL_EMAIL."/main/loadUrl/email/undangan_negosiasi_reverse/".$reqId."/".$paket_rekanan_satuan->getField("PAKET_REKANAN_ID")); 

	// 		$mail->MsgHTML($body);
	// 		if($mail->Send())
	// 		{
	// 			$paket_rekanan2->setField("FIELD", "DI_EMAIL");
	// 			$paket_rekanan2->setField("FIELD_VALUE", "(COALESCE(DI_EMAIL, 0) + 1)");
	// 			$paket_rekanan2->setField("PAKET_REKANAN_ID", $paket_rekanan_satuan->getField("PAKET_REKANAN_ID"));
	// 			$paket_rekanan2->update();			
	// 			$html .= "Kirim Undangan Reverse Auction berhasil ke ".$paket_rekanan2->getField("NAMA")."<br>";
	// 		}
	// 		else {
	// 			$html .= "Kirim Undangan Reverse Auction gagal ke ".$paket_rekanan2->getField("NAMA")."<br>";
	// 		}
	// 	}
	// 	echo $html;
	// }	
	
	
}
?>
