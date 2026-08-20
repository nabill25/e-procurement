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

class paket_penawaran_json extends CI_Controller {

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
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PaketPenawaran");
		$this->load->model("Paket");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$paket_penawaran = new PaketPenawaran();

		// $reqId = $this->input->post("reqId");
		$reqId = $this->input->post("reqPer");
		$reqPaketPenawaranId = $this->input->post("reqPaketPenawaranId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqTotal = $this->input->post("reqTotal");
		//echo $reqTotal;exit;

		$reqLot = isset($_POST["reqLot"]) ? $_POST["reqLot"] : '';
		$reqItem = isset($_POST["reqItem"]) ? $_POST["reqItem"] : '';
		$reqSatuan = isset($_POST["reqSatuan"]) ? $_POST["reqSatuan"] : '';
		$reqQuantity = isset($_POST["reqQuantity"]) ? $_POST["reqQuantity"] : '';
		$reqOE = isset($_POST["reqOE"]) ? $_POST["reqOE"] : '';
		$reqJumlah = isset($_POST["reqJumlah"]) ? $_POST["reqJumlah"] : '';
		$reqLokasi = isset($_POST["reqLokasi"]) ? $_POST["reqLokasi"] : array(0);
		$reqParent = isset($_POST["reqParent"]) ? $_POST["reqParent"] : '';

		$reqLotChild = isset($_POST["reqLotChild"]) ? $_POST["reqLotChild"] : '';
		$reqItemChild = isset($_POST["reqItemChild"]) ? $_POST["reqItemChild"] : '';
		$reqSatuanChild = isset($_POST["reqSatuanChild"]) ? $_POST["reqSatuanChild"] : '';
		$reqQuantityChild = isset($_POST["reqQuantityChild"]) ? $_POST["reqQuantityChild"] : '';
		$reqOEChild = isset($_POST["reqOEChild"]) ? $_POST["reqOEChild"] : '';
		$reqJumlahChild = isset($_POST["reqJumlahChild"]) ? $_POST["reqJumlahChild"] : '';
		$reqLokasChild = isset($_POST["reqLokasChild"]) ? $_POST["reqLokasChild"] : array(0);
		$reqChild 	= isset($_POST["reqChild"]) ? $_POST["reqChild"] : '';
		$reqBiayaPengiriman 	= isset($_POST["reqBiayaPengiriman"]) ? $_POST["reqBiayaPengiriman"] : array(0);
		$reqBiayaPengirimanChild 	= isset($_POST["reqBiayaPengirimanChild"]) ? $_POST["reqBiayaPengirimanChild"] : array(0);
		$reqPaketPenawaranIdChild 	= isset($_POST["reqPaketPenawaranIdChild"]) ? $_POST["reqPaketPenawaranIdChild"] : array('');
		$reqBOQKolom 	= isset($_POST["reqBOQKolom"]) ? $_POST["reqBOQKolom"] : '';

		$reqLinkFile 		= $_FILES["reqLinkFile"];
		$reqLinkFileTemp 	= isset($_POST["reqLinkFileTemp"]) ? $_POST["reqLinkFileTemp"] : '';

		$FILE_DIR = "uploads/boq/";

		if($submitSimpan=='Simpan')
		{
			/* HAPUS BAWAAN DARI IHARGA */
			$paket_penawaran->setField("PAKET_ID", $reqId);
			$paket_penawaran->deleteHarga();

			for($i=0; $i<count($reqItem);$i++)
			{
				if($reqPaketPenawaranId[$i] == "")
				{
					$paket_penawaran = new PaketPenawaran();
					$paket_penawaran->setField("PAKET_ID", $reqId);
					$paket_penawaran->setField("ITEM_NUMBER", $reqLot[$i]);
					$paket_penawaran->setField("ITEM", $reqItem[$i]);
					$paket_penawaran->setField("SATUAN", $reqSatuan[$i]);
					$paket_penawaran->setField("QUANTITY", ValToNull(dotToNo($reqQuantity[$i])));
					$paket_penawaran->setField("OE", ValToNull(dotToNo($reqOE[$i])));
					$paket_penawaran->setField("JUMLAH", ValToNull(dotToNo($reqJumlah[$i])));
					$paket_penawaran->setField("LOKASI", $reqLokasi[$i]);
					$paket_penawaran->setField("DELIVERY_DATE", "NULL");
					$paket_penawaran->setField("ITEM_PARENT", $reqParent[$i]);
					$paket_penawaran->setField("ITEM_CHILD", "0");
					$paket_penawaran->setField("BIAYA_KIRIM", ValToNull(dotToNo($reqBiayaPengiriman[$i])));
					$paket_penawaran->setField("BOQ_KOLOM", $reqBOQKolom[$i]);
					$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$this->ID).".".getExtension($reqLinkFile['name'][$i]);
					if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
					{
						$insertLinkFile =  $renameFile;
						$insertLinkFileNama = $reqLinkFile['name'][$i];
					}
					else
					{
						$insertLinkFile =  $reqLinkFileTemp[$i];
					}
					$paket_penawaran->setField("BOQ_FILE", $insertLinkFile);
					$paket_penawaran->insert();
					unset($paket_penawaran);

					$paket = new Paket();
					$paket->setField("PAKET_ID", $reqId);
					$paket->setField("FIELD", "NILAI");
					$paket->setField("FIELD_VALUE", $reqTotal);
					$paket->updateByField();
					unset($paket);
				}
				else
				{
					$paket_penawaran = new PaketPenawaran();
					$paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
					$paket_penawaran->setField("PAKET_ID", $reqId);
					$paket_penawaran->setField("ITEM_NUMBER", $reqLot[$i]);
					$paket_penawaran->setField("ITEM", $reqItem[$i]);
					$paket_penawaran->setField("SATUAN", $reqSatuan[$i]);
					$paket_penawaran->setField("QUANTITY", ValToNull(dotToNo($reqQuantity[$i])));
					$paket_penawaran->setField("OE", ValToNull(dotToNo($reqOE[$i])));
					$paket_penawaran->setField("JUMLAH", ValToNull(dotToNo($reqJumlah[$i])));
					$paket_penawaran->setField("LOKASI", $reqLokasi[$i]);
					$paket_penawaran->setField("DELIVERY_DATE", "NULL");
					$paket_penawaran->setField("BIAYA_KIRIM", ValToNull(dotToNo($reqBiayaPengiriman[$i])));
					$paket_penawaran->setField("BOQ_KOLOM", $reqBOQKolom[$i]);
					$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$this->ID).".".getExtension($reqLinkFile['name'][$i]);
					if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
					{
						$insertLinkFile =  $renameFile;
						$insertLinkFileNama = $reqLinkFile['name'][$i];
					}
					else
					{
						$insertLinkFile =  $reqLinkFileTemp[$i];
					}
					$paket_penawaran->setField("BOQ_FILE", $insertLinkFile);

					if ($reqId) {
						$paket_penawaran->updatePenawaran();
					} else {
						$paket_penawaran->updatePenawaranPermohonan();
					}
					unset($paket_penawaran);

					$paket = new Paket();
					$paket->setField("PAKET_ID", $reqId);
					$paket->setField("FIELD", "NILAI");
					$paket->setField("FIELD_VALUE", dotToNo($reqTotal));
					$paket->updateByField();
					unset($paket);
				}
			}

			// for($i=0; $i<count($reqItemChild);$i++)
			// {
			// 	if($reqPaketPenawaranIdChild[$i] == "")
			// 	{
			// 		$paket_penawaran_child = new PaketPenawaran();
			// 		$paket_penawaran_child->setField("PAKET_ID", $reqId);
			// 		$paket_penawaran_child->setField("ITEM_NUMBER", "NULL");
			// 		$paket_penawaran_child->setField("ITEM", $reqItemChild[$i]);
			// 		$paket_penawaran_child->setField("SATUAN", $reqSatuanChild[$i]);
			// 		$paket_penawaran_child->setField("QUANTITY", ValToNull(dotToNo($reqQuantityChild[$i])));
			// 		$paket_penawaran_child->setField("OE", ValToNull(dotToNo($reqOEChild[$i])));
			// 		$paket_penawaran_child->setField("JUMLAH", ValToNull(dotToNo($reqJumlahChild[$i])));
			// 		$paket_penawaran_child->setField("LOKASI", $reqLokasChild[$i]);
			// 		$paket_penawaran_child->setField("DELIVERY_DATE", "NULL");
			// 		$paket_penawaran_child->setField("ITEM_PARENT", "0");
			// 		$paket_penawaran_child->setField("ITEM_CHILD", $reqChild[$i]);
			// 		$paket_penawaran_child->setField("BIAYA_KIRIM", ValToNull(dotToNo($reqBiayaPengirimanChild[$i])));
			// 		$paket_penawaran_child->insert();
			// 		unset($paket_penawaran_child);

			// 		$paket = new Paket();
			// 		$paket->setField("PAKET_ID", $reqId);
			// 		$paket->setField("FIELD", "NILAI");
			// 		$paket->setField("FIELD_VALUE", dotToNo($reqTotal));
			// 		$paket->updateByField();
			// 		unset($paket);
			// 	}
			// 	else
			// 	{
			// 		$paket_penawaran_child = new PaketPenawaran();
			// 		$paket_penawaran_child->setField("PAKET_ID", $reqId);
			// 		$paket_penawaran_child->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranIdChild[$i]);
			// 		$paket_penawaran_child->setField("ITEM_NUMBER", "NULL");
			// 		$paket_penawaran_child->setField("ITEM", $reqItemChild[$i]);
			// 		$paket_penawaran_child->setField("SATUAN", $reqSatuanChild[$i]);
			// 		$paket_penawaran_child->setField("QUANTITY", ValToNull(dotToNo($reqQuantityChild[$i])));
			// 		$paket_penawaran_child->setField("OE", ValToNull(dotToNo($reqOEChild[$i])));
			// 		$paket_penawaran_child->setField("JUMLAH", ValToNull(dotToNo($reqJumlahChild[$i])));
			// 		$paket_penawaran_child->setField("LOKASI", $reqLokasChild[$i]);
			// 		$paket_penawaran_child->setField("DELIVERY_DATE", "NULL");
			// 		$paket_penawaran_child->setField("BIAYA_KIRIM", ValToNull(dotToNo($reqBiayaPengirimanChild[$i])));
			// 		if ($reqId) {
			// 			$paket_penawaran_child->updatePenawaran();
			// 		} else {
			// 			$paket_penawaran_child->updatePenawaranPermohonan();
			// 		}
			// 		unset($paket_penawaran_child);

			// 		$paket = new Paket();
			// 		$paket->setField("PAKET_ID", $reqId);
			// 		$paket->setField("FIELD", "NILAI");
			// 		$paket->setField("FIELD_VALUE", dotToNo($reqTotal));
			// 		$paket->updateByField();
			// 		unset($paket);
			// 	}
		 //  	}
		}
		echo "Data berhasil disimpan";
	}

	function updateBoqFile()
	{
		/* INCLUDE FILE */
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("PaketPenawaran");
		$this->load->model("Paket");
		$this->load->library("FileHandler");
		$file = new FileHandler();
		$paket_penawaran = new PaketPenawaran();

		$reqId = $this->input->post("reqId");
		$reqPer = $this->input->post("reqPer");
		$reqPaketPenawaranId = $this->input->post("reqPaketPenawaranId");
		$submitSimpan = $this->input->post("submitSimpan");
		$reqTotal = $this->input->post("reqTotal");
		//echo $reqTotal;exit;

		$reqLot = isset($_POST["reqLot"]) ? $_POST["reqLot"] : '';
		$reqItem = isset($_POST["reqItem"]) ? $_POST["reqItem"] : '';
		$reqSatuan = isset($_POST["reqSatuan"]) ? $_POST["reqSatuan"] : '';
		$reqQuantity = isset($_POST["reqQuantity"]) ? $_POST["reqQuantity"] : '';
		$reqOE = isset($_POST["reqOE"]) ? $_POST["reqOE"] : '';
		$reqJumlah = isset($_POST["reqJumlah"]) ? $_POST["reqJumlah"] : '';
		$reqLokasi = isset($_POST["reqLokasi"]) ? $_POST["reqLokasi"] : array(0);
		$reqParent = isset($_POST["reqParent"]) ? $_POST["reqParent"] : '';

		$reqLotChild = isset($_POST["reqLotChild"]) ? $_POST["reqLotChild"] : '';
		$reqItemChild = isset($_POST["reqItemChild"]) ? $_POST["reqItemChild"] : '';
		$reqSatuanChild = isset($_POST["reqSatuanChild"]) ? $_POST["reqSatuanChild"] : '';
		$reqQuantityChild = isset($_POST["reqQuantityChild"]) ? $_POST["reqQuantityChild"] : '';
		$reqOEChild = isset($_POST["reqOEChild"]) ? $_POST["reqOEChild"] : '';
		$reqJumlahChild = isset($_POST["reqJumlahChild"]) ? $_POST["reqJumlahChild"] : '';
		$reqLokasChild = isset($_POST["reqLokasChild"]) ? $_POST["reqLokasChild"] : array(0);
		$reqChild 	= isset($_POST["reqChild"]) ? $_POST["reqChild"] : '';
		$reqBiayaPengiriman 	= isset($_POST["reqBiayaPengiriman"]) ? $_POST["reqBiayaPengiriman"] : array(0);
		$reqBiayaPengirimanChild 	= isset($_POST["reqBiayaPengirimanChild"]) ? $_POST["reqBiayaPengirimanChild"] : array(0);
		$reqPaketPenawaranIdChild 	= isset($_POST["reqPaketPenawaranIdChild"]) ? $_POST["reqPaketPenawaranIdChild"] : array('');
		$reqBOQKolom 	= isset($_POST["reqBOQKolom"]) ? $_POST["reqBOQKolom"] : '';

		$reqLinkFile 		= $_FILES["reqLinkFile"];
		$reqLinkFileTemp 	= isset($_POST["reqLinkFileTemp"]) ? $_POST["reqLinkFileTemp"] : '';

		$FILE_DIR = "uploads/boq/";

		if($submitSimpan=='Simpan')
		{

			for($i=0; $i<count($reqItem);$i++)
			{
				$paket_penawaran = new PaketPenawaran();
				$paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
				$paket_penawaran->setField("PAKET_ID", $reqId);
				$renameFile = md5(date("dmYHis").$reqLinkFile['name'][$i].$this->ID).".".getExtension($reqLinkFile['name'][$i]);
				if($file->uploadToDirArray('reqLinkFile', $FILE_DIR, $renameFile, $i))
				{
					$insertLinkFile =  $renameFile;
					$insertLinkFileNama = $reqLinkFile['name'][$i];
				}
				else
				{
					$insertLinkFile =  $reqLinkFileTemp[$i];
				}

				$paket_penawaran->setField("BOQ_FILE", $insertLinkFile);
				$paket_penawaran->updatePenawaranBoq();

				unset($paket_penawaran);
			}

		}
		echo "Data berhasil disimpan";
	}

	function delete()
	{
		$this->load->model("PaketPenawaran");

		$paket_penawaran = new PaketPenawaran();
		$paket_penawaran_item_parent = new PaketPenawaran();

		$reqId = $this->input->get("reqId");
		$reqPaketPenawaranId = $this->input->get("reqPaketPenawaranId");

		/* DAPATKAN ITEM_PARENT */
		$paket_penawaran_item_parent->selectByParams(array("PAKET_PENAWARAN_ID" => $reqId));
		$paket_penawaran_item_parent->firstRow();
		$item_parent = $paket_penawaran_item_parent->getField("ITEM_PARENT");
		$paket_id = $paket_penawaran_item_parent->getField("PAKET_ID");
		//echo $paket_penawaran->query;exit;
		//fIRSTROW

		//HAPUS CHILD DENGAN ITEM PARENT YANG SUDAH DIDPATKAN

		$paket_penawaran->setField("PAKET_ID", $paket_id);
		$paket_penawaran->setField("ITEM_CHILD", $item_parent);
		if($paket_penawaran->deletePenawaran())
		{
			$paket_penawaran_parent = new PaketPenawaran();
			$paket_penawaran_parent->setField("PAKET_PENAWARAN_ID", $reqId);
			$paket_penawaran_parent->deletePenawaranParent();
		}

		echo "Data berhasil di hapus";


	}

	function delete_child()
	{
		$this->load->model("PaketPenawaran");

		$paket_penawaran = new PaketPenawaran();
		$paket_penawaran_item_parent = new PaketPenawaran();

		$reqId = $this->input->get("reqId");
		$reqPaketPenawaranId = $this->input->get("reqPaketPenawaranId");

		/* DAPATKAN ITEM_PARENT */
		$paket_penawaran_item_parent->selectByParams(array("PAKET_PENAWARAN_ID" => $reqId));
		$paket_penawaran_item_parent->firstRow();
		$item_child = $paket_penawaran_item_parent->getField("ITEM_CHILD");
		//echo $paket_penawaran->query;exit;
		//fIRSTROW

		//HAPUS CHILD DENGAN ITEM PARENT YANG SUDAH DIDPATKAN

		$paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqId);
		$paket_penawaran->deleteChild();

		echo "Data berhasil di hapus";


	}

}
?>
