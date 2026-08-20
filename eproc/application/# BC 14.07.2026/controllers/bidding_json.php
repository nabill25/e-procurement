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

class bidding_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			redirect('main');
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
		$this->USER_TYPE_ID = $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;   
	}	
	
	
	function ambil_jam() 
	{
		$this->load->model("Paket");
		$paket = new Paket();
		$reqId = $this->input->get("reqId");
		
		$paket->selectBidding($reqId);
		$paket->firstRow();
		echo $paket->getField("BIDDING_MULAI");
		
		//$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		//$paketInfo->getPaket($reqId);
		//echo $paketInfo->bidding_mulai;
		
	}

	function ambil_reset() 
	{
		$this->load->model("PaketRekanan");
		$paket_rekanan = new PaketRekanan();
		$reqId = $this->input->get("reqId");
		
		$paket_rekanan->selectByParamsSimple(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID));
		$paket_rekanan->firstRow();
		
		if($paket_rekanan->getField("BIDDING_RESET") == "1")
		{
			$paket_rekanan->setField("FIELD", "BIDDING_RESET");
			$paket_rekanan->setField("FIELD_VALUE", "0");
			$paket_rekanan->setField("REKANAN_ID", $this->ID);
			$paket_rekanan->setField("PAKET_ID", $reqId);
			$paket_rekanan->updateByRekananPaket();
			echo "1";	
		}
		else
			echo "0";
			
	}
	
	function mulai() 
	{
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$reqId = $this->input->get("reqId");
		$reqStatus = $this->input->get("reqStatus");
		
		$paketInfo->getPaket($reqId);
		
		if($paketInfo->bidding_menit == "")
		{
			echo "Waktu bidding belum ditentukan.";	
			return;
		}
		
		$this->load->model("Paket");
		$paket = new Paket();
		$paket->setField("FIELD", "BIDDING_MULAI");
		$paket->setField("FIELD_VALUE", "CURRENT_TIMESTAMP + INTERVAL '".$paketInfo->bidding_menit."' MINUTE ");
		$paket->setField("PAKET_ID", $reqId);
		if($paket->updateByField())
		{
			$this->load->model("PaketRekanan");
			$paket_rekanan = new PaketRekanan();
			$paket_rekanan->setField("FIELD", "BIDDING_RESET");
			$paket_rekanan->setField("FIELD_VALUE", "1");
			$paket_rekanan->setField("PAKET_ID", $reqId);
			$paket_rekanan->updateByPaket();
			if($reqStatus == "reset")
			{
				echo "Auction berhasil direset.";
			}
			else {
				// Insert Rekam Jejak
			    $this->load->library("librekamjejak"); 
			    $this->librekamjejak->insertRJ('23','',$reqId,'null','23'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
			    // End Insert Rekam Jejak

				echo "Auction dimulai.";
			}
		}
		else {
			echo "Auction gagal.";
		}
		
	}
	
	function kirim_penawaran()
	{
		$this->load->model("Paket");
		$this->load->model("PaketRekanan");
		
		$paket = new Paket();
		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->post("reqId");
		$reqPenawaran = $this->input->post("reqPenawaran");	
      	
      	$filepath = 'logs/Auction-log_' .$reqId. '.txt'; 
      	$handle = fopen($filepath, "a+");
      	$text   = "";

		if((int)dotToNo($reqPenawaran) == 0 || $reqPenawaran == '')
		{
      		// Insert to Logs
      		$text   .= "TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### USERID:". $this->ID." ### IP:". $this->getIP()." ### STATEMENT: Penawaran belum di isi
-----------------------------------------------------------------------------------------";
			$arr = array('<br>');
	        $logtext = str_replace($arr, "", $text);
	        fwrite($handle, $logtext . "\r\n"); 
      		// End Insert to Logs
			echo "0-Isi nilai penawaran anda.";
			return;
		}
		
		
		$paket_rekanan->selectByParamsSimple(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID));
		$paket_rekanan->firstRow();
		$reqPenawaranSebelumnya = (int)$paket_rekanan->getField("NILAI_PENAWARAN");

		if((int)dotToNo($reqPenawaran) > (int)$paket_rekanan->getField("NILAI_PENAWARAN"))
		{
			// Insert to Logs
      		$text   .= "TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### USERID:". $this->ID." ### IP:". $this->getIP()." ### STATEMENT: Penawaran submit (".(int)dotToNo($reqPenawaran).") lebih tinggi dari penawaran sebelum nya (".$reqPenawaranSebelumnya.")
-----------------------------------------------------------------------------------------";
			$arr = array('<br>');
	        $logtext = str_replace($arr, "", $text);
	        fwrite($handle, $logtext . "\r\n"); 
      		// End Insert to Logs

			echo "0-Isi penawaran yang lebih rendah.";
			return;	
		}
		
		$bolehEntri = $paket->getCountByParamsMonitoring(array("A.PAKET_ID" => $reqId), " AND BIDDING_MULAI >= CURRENT_TIMESTAMP ");
		if($bolehEntri == 0)
		{
			// Insert to Logs
      		$text   .= "TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### USERID:". $this->ID." ### IP:". $this->getIP()." ### STATEMENT: Waktu pemasukan penawaran telah selesai
-----------------------------------------------------------------------------------------";
			$arr = array('<br>');
	        $logtext = str_replace($arr, "", $text);
	        fwrite($handle, $logtext . "\r\n"); 
      		// End Insert to Logs

			echo "0-Waktu pemasukan penawaran telah selesai.";
			return;	
		}
		
		$paket_rekanan->setField("NILAI_PENAWARAN", dotToNo($reqPenawaran));
		$paket_rekanan->setField("REKANAN_ID", $this->ID);
		$paket_rekanan->setField("PAKET_ID", $reqId);
		
		if($paket_rekanan->updatePenawaran()) {
			// Insert to Logs
      		$text   .= "TIME:".date('h:i:s')." ### DATE:".date('Y-m-d')." ### USERID:". $this->ID." ### IP:". $this->getIP()." ### STATEMENT: Penawaran berhasil di update ke-".$reqPenawaran." dari (".$reqPenawaranSebelumnya.")
-----------------------------------------------------------------------------------------";
			$arr = array('<br>');
	        $logtext = str_replace($arr, "", $text);
	        fwrite($handle, $logtext . "\r\n"); 
      		// End Insert to Logs

			echo "1-Penawaran berhasil di update ke-".$reqPenawaran;
		}
		
	}

	function kirim_penawaran_rincian()
	{
		$this->load->model("Paket");
		$this->load->model("PaketRekanan");
		$this->load->model("RekananPaketPenawaran");
		
		$paket = new Paket();
		$paket_rekanan = new PaketRekanan();
		$rekanan_paket_penawaran = new RekananPaketPenawaran();
		
		$reqRekananPaketPenawaranId = $this->input->post("reqRekananPaketPenawaranId");
		$reqUnitPriceKoreksi = $this->input->post("reqUnitPriceKoreksi");
		$reqJumlah = $this->input->post("reqJumlah");	
		$reqId = $this->input->post("reqId");	
		
		$reqPenawaran = 0;
		for($i=0;$i<count($reqJumlah);$i++)
		{
			if((int)dotToNo($reqJumlah[$i]) == 0)
			{
				echo "0-Isi nilai penawaran anda.";
				return;
			}
			$reqPenawaran += dotToNo($reqJumlah[$i]);
		}
		
		$paket_rekanan->selectByParamsSimple(array("A.PAKET_ID" => $reqId, "A.REKANAN_ID" => $this->ID));
		$paket_rekanan->firstRow();
		
		$paketRekananId = $paket_rekanan->getField("PAKET_REKANAN_ID");
		
		if((int)dotToNo($reqPenawaran) > (int)$paket_rekanan->getField("NILAI_PENAWARAN"))
		{
			echo "0-Isi penawaran yang lebih rendah.";
			return;	
		}
		
		$bolehEntri = $paket->getCountByParamsMonitoring(array("A.PAKET_ID" => $reqId), " AND BIDDING_MULAI >= CURRENT_TIMESTAMP ");
		if($bolehEntri == 0)
		{
			echo "0-Waktu pemasukan penawaran telah selesai.";
			return;	
		}
		
		
		for($i=0;$i<count($reqUnitPriceKoreksi);$i++)
		{
			$rekanan_paket_penawaran = new RekananPaketPenawaran();
			$rekanan_paket_penawaran->setField("UNIT_PRICE_KOREKSI", dotToNo($reqUnitPriceKoreksi[$i]));
			$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $paketRekananId);
			$rekanan_paket_penawaran->setField("REKANAN_PAKET_PENAWARAN_ID", $reqRekananPaketPenawaranId[$i]);
			$rekanan_paket_penawaran->updateKoreksiBidding();
			unset($rekanan_paket_penawaran);
		}		
		
		$paket_rekanan->setField("NILAI_PENAWARAN", dotToNo($reqPenawaran));
		$paket_rekanan->setField("REKANAN_ID", $this->ID);
		$paket_rekanan->setField("PAKET_ID", $reqId);
		
		if($paket_rekanan->updatePenawaranRincian())
			echo "1-".numberToIna($reqPenawaran);
		
	}
		
	function ambil_nilai_terkecil()
	{
		$this->load->model("Paket");
		$this->load->model("PaketRekanan");
		$paket_rekanan = new PaketRekanan();
		$paket_rekanan_cek = new PaketRekanan();
		$paket = new Paket();
		$reqId = $this->input->get("reqId");
		
		$paket_rekanan->selectByParamsSimple(array("A.PAKET_ID" => $reqId, "NOT A.NILAI_PENAWARAN" => "0", "A.LULUS_PENAWARAN" => 1), -1, -1, "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
		$paket_rekanan->firstRow();
		
		$paket->selectByParams(array("A.PAKET_ID" => $reqId));
		$paket->firstRow();

		// cek penawaran yang sama, jika penawaran terendah nilainya sama dengan penawaran penyedia lain, maka icon harga terendah gak ada yang dapet
		$paket_rekanan_cek->selectByParamsSimple(array("A.PAKET_ID" => $reqId, "A.NILAI_PENAWARAN" => $paket_rekanan->getField("NILAI_PENAWARAN"), "NOT A.NILAI_PENAWARAN" => "0", "A.LULUS_PENAWARAN" => 1), -1, -1, "", " ORDER BY NILAI_PENAWARAN ASC, KIRIM_PENAWARAN_TANGGAL ASC ");
		$cekPenawaranNilainyaSama = $paket_rekanan_cek->countRow();
		
		$arrData["harga"] = numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"));
		$arrData["kode"]  = $paket_rekanan->getField("KODE_REKANAN");
		
		// Nilai Penawaran harus lebih kecil dari Nilai Penawaran Harga Maksimal
		if ($paket_rekanan->getField("NILAI_PENAWARAN") < $paket->getField("PENAWARAN_HARGA_MAKSIMAL") && $cekPenawaranNilainyaSama == 1) {
			$arrData["show"]  = '1';
		} else {
			$arrData["show"]  = '0';
		}

		echo json_encode($arrData);
			
	} 

	function ambil_penawaran_rekanan() 
	{
		
		$this->load->model("PaketRekanan");
		$paket_rekanan = new PaketRekanan();
		$reqId = $this->input->get("reqId");
		
		if($this->USER_TYPE_ID == "3")
		{}
		else
			return;

		$paket_rekanan->selectUrutPenawaran(array("A.PAKET_ID" => $reqId));
		$i = 0;
		while($paket_rekanan->nextRow())
		{ 
			$met[$i]['KODE_REKANAN'] = $paket_rekanan->getField("KODE_REKANAN");
			$met[$i]['NILAI_PENAWARAN'] = numberToIna($paket_rekanan->getField("NILAI_PENAWARAN"));
			$met[$i]['NILAI_URUT'] = coalesce($paket_rekanan->getField("NILAI_URUT"), -1);
			$i++;
		}
		echo json_encode($met);	
	}	

	function updateHargaMaksimal() 
	{
		$reqId = $this->input->post("reqId");
		$reqPenawaranHargaMaksimal = $this->input->post("reqPenawaranHargaMaksimal"); 
		
		$this->load->model("Paket");
		$paket = new Paket();
		$paket->setField("FIELD", "PENAWARAN_HARGA_MAKSIMAL");
		$paket->setField("FIELD_VALUE", CommaToDot(dotToNo($reqPenawaranHargaMaksimal)));
		$paket->setField("PAKET_ID", $reqId);
		if($paket->updateByField())
		{ 
			echo "Harga Penawaran Maksimal berhasil di simpan.";
		}
		
	}

	public function getIP()
    {
        // $ip = $_SERVER['REMOTE_ADDR']?:($_SERVER['HTTP_X_FORWARDED_FOR']?:$_SERVER['HTTP_CLIENT_IP']);
      $ipaddress = '';
      if (isset($_SERVER['HTTP_CLIENT_IP']))
          $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
      else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_X_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
      else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_FORWARDED'];
      else if(isset($_SERVER['REMOTE_ADDR']))
          $ipaddress = $_SERVER['REMOTE_ADDR'];
      else
          $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
		
}
?>
