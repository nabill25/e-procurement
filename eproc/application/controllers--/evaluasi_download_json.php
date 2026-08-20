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


class evaluasi_download_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity()) { }       
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function aanwijzing_publish_json() 
	{
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes
		ini_set('memory_limit','2048M');
		ob_start();
		$this->load->library("paketinfo"); 
		$this->load->library('zip');
		$paketInfo = new paketinfo();
		include_once("functions/string.func.php");
		include_once("functions/default.func.php");
		include_once("functions/date.func.php");
		$this->load->model(array("PaketEvaluasiAdminTawar","PaketEvaluasiTeknisTawar","PaketEvaluasiHargaTawar","PaketEvaluasiKualifikasi","PaketRekanan","PaketDokumen"));

		$pathDok = 'uploads/penawaran/';

		$paket_evaluasi_admin = new PaketEvaluasiAdminTawar();
		$paket_evaluasi_teknis = new PaketEvaluasiTeknisTawar();
		$paket_evaluasi_harga = new PaketEvaluasiHargaTawar();
		$paket_evaluasi_kualifikasi = new PaketEvaluasiKualifikasi();
		$paket_rekanan = new PaketRekanan();

		$reqId = $this->input->get("reqId");
		$reqRekanan = $this->input->get("rekanan");
		$reqFile = $this->input->get("file");
		$reqTahap = $this->input->get("tahap");

		$paketInfo->getPaket($reqId);
		$reqNamaPaket = $paketInfo->nama;
		$reqSistemSampul = $paketInfo->sistem_sampul;
		$reqMetodeEvaluasiId = $paketInfo->metode_lelang_id;

		$paket_evaluasi_admin->selectByParams(array("PAKET_ID" => $reqId));
		$paket_evaluasi_teknis->selectByParams(array("PAKET_ID" => $reqId));
		$paket_evaluasi_harga->selectByParams(array("PAKET_ID" => $reqId));
		$paket_evaluasi_kualifikasi->selectByParams(array("PAKET_ID" => $reqId));

		if ($reqTahap == 'kualifikasi') { 
		  // Lulus Pendaftaran Saat Daftar 0:gagal, 2:Proses, 1:lulus
		  // $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekanan), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 2 ");
		  $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekanan), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL ");
		  while($paket_rekanan->nextRow())
		  {
		    $arrRekanan[] = $paket_rekanan->getField("REKANAN");
		    $arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
		    $arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
		    $arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
		    $arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("NILAI_PENAWARAN_SEBELUMNYA");
		    $arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
		    $arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
		    $arrPasswordDokumen2[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");
		  }

		  if (is_array($arrRekanan)) {
		    $arrRekanan = $arrRekanan;
		    $arrRekananId = $arrRekananId;
		    $arrPaketRekananId = $arrPaketRekananId;
		    $arrPaketRekananNilai = $arrPaketRekananNilai;
		    $arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
		    $arrRekananHadirPembukaan = $arrRekananHadirPembukaan;
		    $arrPasswordDokumen = $arrPasswordDokumen;
		    $arrPasswordDokumen2 = $arrPasswordDokumen2;
		  } else {
		    $arrRekanan = array();
		    $arrRekananId = array();
		    $arrPaketRekananId = array();
		    $arrPaketRekananNilai = array();
		    $arrPaketRekananNilaiSebelumnya = array();
		    $arrRekananHadirPembukaan = array();
		    $arrPasswordDokumen = array(); 
		    $arrPasswordDokumen2 = array(); 
		  }

		} 
		else 
		{

		  $paket_rekanan->selectByParams(array("PAKET_ID" => $reqId, "A.REKANAN_ID" => $reqRekanan), -1, -1, " AND A.TANGGAL_DAFTAR IS NOT NULL AND A.LULUS_PENDAFTARAN = 1 AND A.LULUS_KUALIFIKASI = 1 AND A.KIRIM_PENAWARAN = 1 ");
		  while($paket_rekanan->nextRow())
		  {
		  	$arrRekanan[] = $paket_rekanan->getField("REKANAN");
		  	$arrRekananId[] = $paket_rekanan->getField("REKANAN_ID");
		  	$arrPaketRekananId[] = $paket_rekanan->getField("PAKET_REKANAN_ID");
		  	$arrPaketRekananNilai[] = $paket_rekanan->getField("NILAI_PENAWARAN");
		  	$arrPaketRekananNilaiSebelumnya[] = $paket_rekanan->getField("NILAI_PENAWARAN_SEBELUMNYA");
		  	$arrRekananHadirPembukaan[] = $paket_rekanan->getField("HADIR_PEMBUKAAN_PENAWARAN");
		    $arrPasswordDokumen[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD");
		  	$arrPasswordDokumen2[] = $paket_rekanan->getField("KIRIM_PENAWARAN_PASSWORD2");
		  }

		  if (is_array($arrRekanan)) {
		  	$arrRekanan = $arrRekanan;
		  	$arrRekananId = $arrRekananId;
		  	$arrPaketRekananId = $arrPaketRekananId;
		  	$arrPaketRekananNilai = $arrPaketRekananNilai;
		  	$arrPaketRekananNilaiSebelumnya = $arrPaketRekananNilaiSebelumnya;
		  	$arrRekananHadirPembukaan = $arrRekananHadirPembukaan;
		    $arrPasswordDokumen = $arrPasswordDokumen;
		  	$arrPasswordDokumen2 = $arrPasswordDokumen2;
		  } else {
		  	$arrRekanan = array();
		  	$arrRekananId = array();
		  	$arrPaketRekananId = array();
		  	$arrPaketRekananNilai = array();
		  	$arrPaketRekananNilaiSebelumnya = array();
		  	$arrRekananHadirPembukaan = array();
		    $arrPasswordDokumen = array(); 
		  	$arrPasswordDokumen2 = array(); 
		  }
		}

		if ($reqTahap == 'admin') {
			$zipName = $reqId."-Dokumen_Administrasi_".str_replace(" ","_",$arrRekanan[0])."_".date('YmdHis').".zip";
			while($paket_evaluasi_admin->nextRow())
          	{
          		for($j=0;$j<count($arrRekanan);$j++)
              	{
              		$paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_admin->getField("NAMA"))));
                    $paket_dokumen->firstRow();

                    if($paket_dokumen->getField("PATH_FILE") == "")
					{}
					else
					{
						if($info == "0") { }
						else
						{
	   						ob_end_clean();
							$this->zip->read_file($pathDok.$paket_dokumen->getField("PATH_FILE"));
						}
					}
					unset($paket_dokumen);
              	}
          	}
	   		$this->zip->download($zipName); 
		}


		// echo "<pre>"; print_r($dokAdministrasi);

		if ($reqTahap == 'teknis') {
			$zipName = $reqId."-Dokumen_Teknis_".str_replace(" ","_",$arrRekanan[0])."_".date('YmdHis').".zip";
			while($paket_evaluasi_teknis->nextRow())
          	{
          		for($j=0;$j<count($arrRekanan);$j++)
              	{
              		$paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_teknis->getField("NAMA"))));
                    $paket_dokumen->firstRow();
                    if($paket_dokumen->getField("PATH_FILE") == "")
					{}
					else
					{
						if($info == "0") { }
                        else
                        {
	   						ob_end_clean();
							$this->zip->read_file($pathDok.$paket_dokumen->getField("PATH_FILE"));
                        }
					}
					unset($paket_dokumen);
              	}
          	}
	   		$this->zip->download($zipName); 
		}

		if ($reqTahap == 'harga') {
			$zipName = $reqId."-Dokumen_Harga_".str_replace(" ","_",$arrRekanan[0])."_".date('YmdHis').".zip";
			while($paket_evaluasi_harga->nextRow())
            {
            	for($j=0;$j<count($arrRekanan);$j++)
              	{
              		$paket_dokumen = new PaketDokumen();
                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_harga->getField("NAMA"))));
                    $paket_dokumen->firstRow();
                    if($paket_dokumen->getField("PATH_FILE") == "")
                    {}
                    else
                    {
                    	if($info == "0") { }
						else
						{
	   						ob_end_clean();
							$this->zip->read_file($pathDok.$paket_dokumen->getField("PATH_FILE"));
						}
                    }
					unset($paket_dokumen);
              	}
            }
	   		$this->zip->download($zipName); 
		}

		if ($reqTahap == 'all') { // admin, teknis, harga  

			if ($reqFile == 'all') { // 1 File => all
				$zipName = $reqId."-Dokumen_Penawaran_".str_replace(" ","_",$arrRekanan[0])."_".date('YmdHis').".zip";
			} else if ($reqFile == '1') { // 2File => File 1
				$zipName = $reqId."-Dokumen_Penawaran_File1_".str_replace(" ","_",$arrRekanan[0])."_".date('YmdHis').".zip";
			} else if ($reqFile == '2') { // 2File => File 2
				$zipName = $reqId."-Dokumen_Penawaran_File2_".str_replace(" ","_",$arrRekanan[0])."_".date('YmdHis').".zip";
			}

			if ($reqFile == 'all' || $reqFile == '1') {
			
				// Administrasi
				while($paket_evaluasi_admin->nextRow())
	          	{
	          		for($j=0;$j<count($arrRekanan);$j++)
	              	{
	              		$paket_dokumen = new PaketDokumen();
	                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_admin->getField("NAMA"))));
	                    $paket_dokumen->firstRow();

	                    if($paket_dokumen->getField("PATH_FILE") == "")
						{}
						else
						{
							if($info == "0") { }
							else
							{
								$this->zip->read_file($pathDok.$paket_dokumen->getField("PATH_FILE"));
							}
						}
						unset($paket_dokumen);
	              	}
	          	}

				// Teknis
				while($paket_evaluasi_teknis->nextRow())
	          	{
	          		for($j=0;$j<count($arrRekanan);$j++)
	              	{
	              		$paket_dokumen = new PaketDokumen();
	                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_teknis->getField("NAMA"))));
	                    $paket_dokumen->firstRow();
	                    if($paket_dokumen->getField("PATH_FILE") == "")
						{}
						else
						{
							if($info == "0") { }
	                        else
	                        {
								$this->zip->read_file($pathDok.$paket_dokumen->getField("PATH_FILE"));
	                        }
						}
						unset($paket_dokumen);
	              	}
	          	}

	        }

	        if ($reqFile == 'all' || $reqFile == '2') {
				// Harga
				while($paket_evaluasi_harga->nextRow())
	            {
	            	for($j=0;$j<count($arrRekanan);$j++)
	              	{
	              		$paket_dokumen = new PaketDokumen();
	                    $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_harga->getField("NAMA"))));
	                    $paket_dokumen->firstRow();
	                    if($paket_dokumen->getField("PATH_FILE") == "")
	                    {}
	                    else
	                    {
	                    	if($info == "0") { }
							else
							{
	   							ob_end_clean();
								$this->zip->read_file($pathDok.$paket_dokumen->getField("PATH_FILE"));
							}
	                    }
						unset($paket_dokumen);
	              	}
	            }

	        }

	   		$this->zip->download($zipName); 
		}

		if ($reqTahap == 'kualifikasi') {  
			$zipName = $reqId."-Dokumen_Kualifikasi_".str_replace(" ","_",$arrRekanan[0])."_".date('YmdHis').".zip";
			while($paket_evaluasi_kualifikasi->nextRow())
            {
            	for($j=0;$j<count($arrRekanan);$j++)
	            { 
	                $paket_dokumen = new PaketDokumen();
	                $paket_dokumen->selectByParams(array("PAKET_ID" => $reqId, "REKANAN_USER_ID" => $arrRekananId[$j], "TRIM(NAMA)" => trim($paket_evaluasi_kualifikasi->getField("NAMA"))));
	                $paket_dokumen->firstRow();

	                if($paket_dokumen->getField("PATH_FILE") == "")
                    {}
                    else
                    {
                    	if($info == "0") { } 
                      	else
                      	{
	   						ob_end_clean();
							$this->zip->read_file($pathDok.$paket_dokumen->getField("PATH_FILE"));
                      	}
                    }
					unset($paket_dokumen);
	            }
            }
	   		$this->zip->download($zipName); 
		}

	}

}
?>
