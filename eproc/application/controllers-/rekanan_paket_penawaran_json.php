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
include_once("lib/php-excel-reader-2.21/excel_reader2.php");

class rekanan_paket_penawaran_json extends CI_Controller {

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

	function add_lelang()
	{
		$this->load->model("RekananPaketPenawaran");
		$this->load->model("PaketNegoisasi");

		$reqId = httpFilterPost("reqId");
		$reqPaketRekananId = httpFilterPost("reqPaketRekananId");
		$reqPaketPenawaranId = $_POST["reqPaketPenawaranId"];
		$reqUnitPriceNegosiasi = $_POST["reqUnitPriceNegosiasi"];
		$reqJumlahNegosiasi = $_POST["reqJumlahNegosiasi"];
		$reqQuantity = $_POST["reqQuantity"];
		$reqUnitPricePenawaran = $_POST["reqUnitPricePenawaran"];
		$reqJumlahPenawaran = $_POST["reqJumlahPenawaran"];
		$reqTotalPenawaran = CommaToDot(dotToNo($_POST["reqTotalPenawaran"]));

		/* VALIDASI ENTRIAN PENAWARAN */
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
			echo "Rincian penawaran tidak sesuai, total rincian = ".numberToIna($hasilPenawaran).", sedangkan total penawaran = ".numberToIna($reqTotalPenawaran).".";
			return;
		}

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

	function dokumen_pengadaan_penawaran_rekanan()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketDokumen");
		$this->load->model("PaketRekanan");
		$this->load->model("RekananPaketPenawaran");
		$this->load->model("PaketTahap");
		// echo "<pre>"; print_r($this->input->post()); die();

		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqCetak= $this->input->post('reqCetak');
		$reqNamaDokumen= $this->input->post('reqNamaDokumen');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$reqKeterangan= $this->input->post('reqKeterangan');
		$reqBayar= $this->input->post('reqBayar');
		$reqDokumenId = $this->input->post('reqDokumenId');
		$submitSimpan= $this->input->post('submitSimpan');
		$submitBOQ = $this->input->post('submitBOQ');
		$reqTotal = $this->input->post('reqTotal');

		$paketInfo->getPaket($reqId);
		$reqNilai = $paketInfo->nilai; 

		$reqLinkFile= $_FILES['reqLinkFile'];
		$reqBoqKolom = $_POST["reqBoqKolom"];
		$reqLinkFileTemp = $_POST["reqLinkFileTemp"];
		$reqUnitPrice = $_POST["reqUnitPrice"];
		$reqDeliveryDate = $_POST["reqDeliveryDate"];
		$reqQuantity = $_POST["reqQuantity"];
		$reqJumlah = $_POST["reqJumlah"];
		$reqPaketPenawaranId = $_POST["reqPaketPenawaranId"];
		$reqBiayaKirim = $_POST["reqBiayaKirim"];

		$rekanan_paket_penawaran = new RekananPaketPenawaran();
		$paket_dokumen = new PaketDokumen();
		$paket_dokumen_rekanan = new PaketDokumen();
		$paket_rekanan = new PaketRekanan();
		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		//$file = new FileHandler();

		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		$arrDokumenPenawaran            = DOKUMEN_PENAWARAN; // ikn
		$aktif_dok_penawaran1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrDokumenPenawaran[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1, "HADIR" => 0));

		if($aktif_dok_penawaran1 == 0) {
		   echo "3";
        } else 
        {
			$reqPaketRekananId = $paket_rekanan->getPaketRekananId($reqId, $this->ID);

			$FILE_DIR_ARITMATIKA = "uploads/aritmatika/";
			$FILE_DIR = "uploads/penawaran/";
			$reloadForm = 0;
			if($submitSimpan == "Simpan")
			{
				$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
				$rekanan_paket_penawaran->delete();

				for($i=0;$i<count($reqPaketPenawaranId);$i++)
				{
					if($reqUnitPrice[$i] == "" || $reqUnitPrice[$i] == "0")
					{
						if($reqQuantity[$i] > 0 && $reqLinkFile['name'][$i] == "")
						{
							echo "0";
							return;
						}
					}

					// Nilai penawaran tidak boleh diatas Harga Perkiraan ikn 20241105
					if (dotToNo($reqJumlah[$i]) > $reqNilai) {
						echo "100";
						return;
					}

					$rekanan_paket_penawaran = new RekananPaketPenawaran();
					$rekanan_paket_penawaran->setField("PAKET_PENAWARAN_ID", $reqPaketPenawaranId[$i]);
					$rekanan_paket_penawaran->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
					$rekanan_paket_penawaran->setField("DELIVERY_DATE", 'NULL');
					$rekanan_paket_penawaran->setField("QUANTITY", $reqQuantity[$i]);

					if($reqLinkFile['name'][$i] == ""){
						$insertLinkFile = $reqLinkFileTemp[$i];
					}
					else
					{
						$reloadForm = 1;
						$renameFile = $reqPaketRekananId.pathinfo(renameFile($reqLinkFile['name'][$i]), PATHINFO_FILENAME)."-".date("dmYHi").$i.".".getExtension($reqLinkFile['name'][$i]);
						if (move_uploaded_file($reqLinkFile['tmp_name'][$i], $FILE_DIR.$renameFile))
						{
							$insertLinkFile = $renameFile;
						}
						// $arrBoqKolom = explode("-", $reqBoqKolom[$i]);
						//
						// $data = new Spreadsheet_Excel_Reader($FILE_DIR.$renameFile);
						//
						// $kolom = getColumnExcel($arrBoqKolom[0]);
						// $row = $arrBoqKolom[1];
						// $reqUnitPrice[$i] = CommaToNo($data->sheets[0]['cells'][$row][$kolom]);// total ambil dari excel
						// $reqJumlah[$i] = CommaToNo($data->sheets[0]['cells'][$row][$kolom]);// total ambil dari excel
						// $reqUnitPrice[$i] 	= $reqUnitPrice[$i]; // total ambil dari inputan
						// $reqJumlah[$i] 		= $reqUnitPrice[$i]; // total ambil dari inputan

						unset($data);


					}
					$rekanan_paket_penawaran->setField("BOQ", $insertLinkFile);

					$setTotalNilaiPenawaran = $reqJumlah[$i];
					$rekanan_paket_penawaran->setField("UNIT_PRICE", dotToNo($reqUnitPrice[$i]));
					// $rekanan_paket_penawaran->setField("JUMLAH", dotToNo($reqJumlah[$i]));
					$rekanan_paket_penawaran->setField("JUMLAH", dotToNo($reqJumlah[$i]));
					$rekanan_paket_penawaran->setField("BIAYA_KIRIM", valToNull(dotToNo($reqBiayaKirim[$i])));
					$rekanan_paket_penawaran->setField('CREATED_BY', $this->USER_LOGIN_ID);
					$rekanan_paket_penawaran->insert();
					// echo $rekanan_paket_penawaran->query; die();

					unset($rekanan_paket_penawaran);
				}


				$paket_rekanan = new PaketRekanan();
				$paket_rekanan->setField("FIELD", "NILAI_PENAWARAN");
				$paket_rekanan->setField("FIELD_VALUE", dotToNo($setTotalNilaiPenawaran));
				$paket_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
				$paket_rekanan->updateIkn();
				unset($paket_rekanan);

				if($reloadForm == 1) // Berhasil
					echo "2";
				else
					echo "1";
					// $rekanan_paket_penawaran_cek = new RekananPaketPenawaran();
					// $cek_pe = $rekanan_paket_penawaran_cek->getCountByParams(array("PAKET_PENAWARAN_ID" => $reqPaketPenawaranId[0], "PAKET_REKANAN_ID" => $reqPaketRekananId));
					// if ($cek_pe == 0) { // belom upload penawaran
					// 	echo "1";
					// } else { // sudah upload penawaran
					// 	echo "2";
					// }
			}
		}

	}


}
?>
