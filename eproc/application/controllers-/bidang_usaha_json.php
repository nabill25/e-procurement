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

class bidang_usaha_json extends CI_Controller {

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

	function json()
	{
		$this->load->model("BidangUsaha");

		$reqSearch = $this->input->get("reqSearch");

		if ($reqSearch) { 

			$i = 0;
			$bidang_usaha = new BidangUsaha();
			// $bidang_usaha->selectByParamsAll(array(), -1, -1, " AND BIDANG_USAHA_PARENT_ID != '0' AND (UPPER(KODE) LIKE '%".strtoupper($reqSearch)."%') OR ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%'))");
			$bidang_usaha->selectByParamsAll(array(), -1, -1, " AND (UPPER(KODE) LIKE '%".strtoupper($reqSearch)."%') OR ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%'))");
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				unset($parent);
				unset($arr_parent);
				$i++;
			}

		} else 
		{
			$i = 0;
			$bidang_usaha = new BidangUsaha();
			$bidang_usaha->selectByParamsAll(array("BIDANG_USAHA_PARENT_ID" => "0"), -1, -1, "");
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				$parent = new BidangUsaha();
				$parent->selectByParamsAll(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")), -1, -1, "");

				$j=0;
				while($parent->nextRow())
				{
					$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
					$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");
					// $arr_parent[$j]['aktif'] = $parent->getField("STATUS_BIDANG_USAHA");
					if ($parent->getField("STATUS_BIDANG_USAHA") == '1') {
						$arr_parent[$j]['aktif'] = '<img src="images/centang-cetak.png">';
					} else {
						$arr_parent[$j]['aktif'] = '<img src="images/uncentang-cetak.png">';
					}
					$arr_parent[$j]['children'] = $arr_child;

					unset($child);
					unset($arr_child);
					$j++;
				}
				$arr_json[$i]['children'] = $arr_parent;

				unset($parent);
				unset($arr_parent);
				$i++;
			}
		}

		echo json_encode($arr_json);
	}

	function jsonaktifall()
	{
		$this->load->model("BidangUsaha");

		$reqSearch = $this->input->get("reqSearch");
		// echo $reqSearch;exit;

		if ($reqSearch) { 
			$i = 0;
			$bidang_usaha = new BidangUsaha();
			$bidang_usaha->selectByParams(array(), -1, -1, " AND BIDANG_USAHA_PARENT_ID != '0' AND (UPPER(KODE) LIKE '%".strtoupper($reqSearch)."%') OR ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%'))");
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				unset($parent);
				unset($arr_parent);
				$i++;
			}
		} else {
			$i = 0;
			$bidang_usaha = new BidangUsaha();
			$bidang_usaha->selectByParams(array("BIDANG_USAHA_PARENT_ID" => "0"), -1, -1, "");
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				$parent = new BidangUsaha();
				$parent->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")), -1, -1, "");

				$j=0;
				while($parent->nextRow())
				{
					$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
					$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");
					// $arr_parent[$j]['aktif'] = $parent->getField("STATUS_BIDANG_USAHA");
					if ($parent->getField("STATUS_BIDANG_USAHA") == '1') {
						$arr_parent[$j]['aktif'] = '<img src="images/centang-cetak.png">';
					} else {
						$arr_parent[$j]['aktif'] = '<img src="images/uncentang-cetak.png">';
					}
	 
					$arr_parent[$j]['children'] = $arr_child;

					unset($child);
					unset($arr_child);
					$j++;
				}
				$arr_json[$i]['children'] = $arr_parent;

				unset($parent);
				unset($arr_parent);
				$i++;
			}
		}
		echo json_encode($arr_json);
	}

	function jsonaktif()
	{
		$this->load->model("BidangUsaha");

		$reqSearch = $this->input->get("reqSearch");

		if ($reqSearch) { 

			$i = 0;
			$bidang_usaha = new BidangUsaha();
			$bidang_usaha->selectByParams(array(), -1, -1, " AND ( UPPER ( KODE ) LIKE '%".strtoupper($reqSearch)."%' AND BIDANG_USAHA_JENIS = '1' AND BIDANG_USAHA_PARENT_ID != '0' )
				OR ( UPPER ( NAMA ) LIKE '%".strtoupper($reqSearch)."%' AND BIDANG_USAHA_JENIS = '1' AND BIDANG_USAHA_PARENT_ID != '0')");
			// echo $bidang_usaha->query; die;
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				unset($parent);
				unset($arr_parent);
				$i++;
			}

		} else 
		{
			$i = 0;
			$bidang_usaha = new BidangUsaha();
			$bidang_usaha->selectByParams(array("BIDANG_USAHA_PARENT_ID" => "0", "BIDANG_USAHA_JENIS" => "1"), -1, -1, "");
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				$parent = new BidangUsaha();
				$parent->selectByParamsAll(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")), -1, -1, "");

				$j=0;
				while($parent->nextRow())
				{
					$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
					$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");
					// $arr_parent[$j]['aktif'] = $parent->getField("STATUS_BIDANG_USAHA");
					if ($parent->getField("STATUS_BIDANG_USAHA") == '1') {
						$arr_parent[$j]['aktif'] = '<img src="images/centang-cetak.png">';
					} else {
						$arr_parent[$j]['aktif'] = '<img src="images/uncentang-cetak.png">';
					}
					$arr_parent[$j]['children'] = $arr_child;

					unset($child);
					unset($arr_child);
					$j++;
				}
				$arr_json[$i]['children'] = $arr_parent;

				unset($parent);
				unset($arr_parent);
				$i++;
			}
		}

		echo json_encode($arr_json);
	}
 
	function jsonaktifsbu()
	{
		$this->load->model("BidangUsaha");

		$reqSearch = $this->input->get("reqSearch");

		if ($reqSearch) { 

			$i = 0;
			$bidang_usaha = new BidangUsaha();
			$bidang_usaha->selectByParams(array(), -1, -1, " AND ( UPPER ( KODE ) LIKE '%".strtoupper($reqSearch)."%' AND BIDANG_USAHA_JENIS = '99' AND BIDANG_USAHA_PARENT_ID != '0' )
				OR ( UPPER ( NAMA ) LIKE '%".strtoupper($reqSearch)."%' AND BIDANG_USAHA_JENIS = '99' AND BIDANG_USAHA_PARENT_ID != '0')");
			// echo $bidang_usaha->query; die;
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				unset($parent);
				unset($arr_parent);
				$i++;
			}

		} else 
		{
			$i = 0;
			$bidang_usaha = new BidangUsaha();
			$bidang_usaha->selectByParams(array("BIDANG_USAHA_PARENT_ID" => "0", "BIDANG_USAHA_JENIS" => "99"), -1, -1, "");
			while($bidang_usaha->nextRow())
			{
				$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
				$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
				if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
					$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
				} else {
					$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
				}

				$parent = new BidangUsaha();
				$parent->selectByParamsAll(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")), -1, -1, "");

				$j=0;
				while($parent->nextRow())
				{
					$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
					$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");
					// $arr_parent[$j]['aktif'] = $parent->getField("STATUS_BIDANG_USAHA");
					if ($parent->getField("STATUS_BIDANG_USAHA") == '1') {
						$arr_parent[$j]['aktif'] = '<img src="images/centang-cetak.png">';
					} else {
						$arr_parent[$j]['aktif'] = '<img src="images/uncentang-cetak.png">';
					}
					$arr_parent[$j]['children'] = $arr_child;

					unset($child);
					unset($arr_child);
					$j++;
				}
				$arr_json[$i]['children'] = $arr_parent;

				unset($parent);
				unset($arr_parent);
				$i++;
			}
		}

		echo json_encode($arr_json);
	}

	function jsonaktifown()
	{
		$this->load->model("BidangUsaha");

		$reqSearch = $this->input->get("reqSearch");
		// echo $reqSearch;exit;
		$i = 0;

		$bidang_usaha = new BidangUsaha();

		// $bidang_usaha->selectByParamsOwn(array("B.REKANAN_ID" => $this->ID), -1, -1, ""); 
		$bidang_usaha->selectByParamsOwn(array("B.REKANAN_ID" => $this->ID), -1, -1, " AND ( UPPER ( KODE ) LIKE '%".strtoupper($reqSearch)."%'  OR  UPPER ( NAMA ) LIKE '%".strtoupper($reqSearch)."%')");
		// echo $bidang_usaha->query; die;
		while($bidang_usaha->nextRow())
		{
			$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
			$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");
			if ($bidang_usaha->getField("STATUS_BIDANG_USAHA") == '1') {
				$arr_json[$i]['aktif'] = '<img src="images/centang-cetak.png">';
			} else {
				$arr_json[$i]['aktif'] = '<img src="images/uncentang-cetak.png">';
			}

			// $parent = new BidangUsaha();
			// $parent->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")), -1, -1, " AND ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%') OR (UPPER(KODE) LIKE '%".strtoupper($reqSearch)."%') OR EXISTS(SELECT 1 FROM BIDANG_USAHA X WHERE UPPER(X.NAMA) LIKE '%".strtoupper($reqSearch)."%' AND X.BIDANG_USAHA_ID LIKE A.BIDANG_USAHA_ID || '%')) ");

			// $j=0;
			// while($parent->nextRow())
			// {
			// 	$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
			// 	$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");
			// 	// $arr_parent[$j]['aktif'] = $parent->getField("STATUS_BIDANG_USAHA");
			// 	if ($parent->getField("STATUS_BIDANG_USAHA") == '1') {
			// 		$arr_parent[$j]['aktif'] = '<img src="images/centang-cetak.png">';
			// 	} else {
			// 		$arr_parent[$j]['aktif'] = '<img src="images/uncentang-cetak.png">';
			// 	}
 
			// 	$arr_parent[$j]['children'] = $arr_child;

			// 	unset($child);
			// 	unset($arr_child);
			// 	$j++;
			// }
			$arr_json[$i]['children'] = $arr_parent;

			unset($parent);
			unset($arr_parent);
			$i++;
		}
		echo json_encode($arr_json);
	}

	function bidang_usaha_combo_json()
	{
		$this->load->model("BidangUsaha");

		/* create objects */
		$bidang_usaha = new BidangUsaha();

		$reqMode= httpFilterGet("reqMode");
		$reqSearch = $this->input->get("reqSearch");

		$i = 0;

		$bidang_usaha->selectByParams(array("BIDANG_USAHA_PARENT_ID" => 0), -1,-1);
		//echo $bidang_usaha->query;exit;

		while($bidang_usaha->nextRow())
		{
			$arr_json[$i]['id'] = $bidang_usaha->getField("BIDANG_USAHA_ID");
			$arr_json[$i]['text'] = $bidang_usaha->getField("NAMA_VIEW");

			$parent = new BidangUsaha();
			$parent->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $bidang_usaha->getField("BIDANG_USAHA_ID")));
			//echo $parent->query;exit;
			$j=0;
			while($parent->nextRow())
			{
				$arr_parent[$j]['id'] = $parent->getField("BIDANG_USAHA_ID");
				$arr_parent[$j]['text'] = $parent->getField("NAMA_VIEW");

				$child = new BidangUsaha();
				$child->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $parent->getField("BIDANG_USAHA_ID")));
				$k=0;
				while($child->nextRow())
				{
					$arr_child[$k]['id'] = $child->getField("BIDANG_USAHA_ID");
					$arr_child[$k]['text'] = $child->getField("NAMA_VIEW");


					$sub = new BidangUsaha();
					$sub->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $child->getField("BIDANG_USAHA_ID")));
					$l=0;
					while($sub->nextRow())
					{
						$arr_sub[$l]['id'] = $sub->getField("BIDANG_USAHA_ID");
						$arr_sub[$l]['text'] = $sub->getField("NAMA_VIEW");

						$detil = new BidangUsaha();
						$detil->selectByParams(array("BIDANG_USAHA_PARENT_ID" => $sub->getField("BIDANG_USAHA_ID")));
						$m=0;
						while($detil->nextRow())
						{
							$arr_detil[$m]['id'] = $detil->getField("BIDANG_USAHA_ID");
							$arr_detil[$m]['text'] = $detil->getField("NAMA_VIEW");

							$m++;
						}

						$arr_sub[$l]['children'] = $arr_detil;

						unset($detil);
						unset($arr_detil);

						$l++;
					}
					$arr_child[$k]['children'] = $arr_sub;

					unset($sub);
					unset($arr_sub);
					$k++;
				}
				$arr_parent[$j]['children'] = $arr_child;

				unset($child);
				unset($arr_child);
				$j++;
			}
			$arr_json[$i]['children'] = $arr_parent;

			unset($parent);
			unset($arr_parent);
			$i++;
		}
		echo json_encode($arr_json);
	}

	function add()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("BidangUsaha");

		/* create objects */
		$bidang_usaha		= new BidangUsaha();


		$reqSubmit = $this->input->post("reqSubmit");

		$reqParentId	= $this->input->post("reqParentId");
		$reqBidangId	= $this->input->post("reqBidangId");
		$reqId			= $this->input->post("reqId");

		$bidang_usaha->selectByParams(array("BIDANG_USAHA_ID" => $reqParentId));
		$bidang_usaha->firstRow();
		$parentId = $bidang_usaha->getField('BIDANG_USAHA_PARENT_ID');

		$reqBidangTahun 		= $this->input->post("reqBidangTahun"); // Kode
		$reqBidangUsaha 		= $this->input->post("reqBidangUsaha"); // Nama Bidang Usaha
		$reqStatusBidangUsaha 	= $this->input->post("reqStatusBidangUsaha"); // Status
		$reqBidangId 			= $this->input->post("reqBidangId");

		$tempBidangTahun	 = $reqBidangTahun ;
		$tempBidangUsaha	 = $reqBidangUsaha ;

		/* ACTION BY REQMODE */
		if($reqId == ''){
			//echo  $bidang_usaha->getMaxIdTree($reqParamKey);
			//$bidang_usaha->setField("SATKER_ID", $bidang_usaha->getMaxIdTree($reqParamKey)+1);
			// $bidang_usaha->setField("BIDANG_USAHA_ID", $reqBidangId);
			// $bidang_usaha->setField("BIDANG_USAHA_PARENT_ID", $reqParentId);
			$bidang_usaha->setField("BIDANG_USAHA_ID", $reqBidangTahun);
			$bidang_usaha->setField("BIDANG_USAHA_PARENT_ID", $parentId);
			$bidang_usaha->setField("NAMA", $reqBidangUsaha);
			$bidang_usaha->setField("KODE", $reqBidangTahun);
			$bidang_usaha->setField("STATUS_BIDANG_USAHA", $reqStatusBidangUsaha);
			$bidang_usaha->setField("BIDANG_USAHA_JENIS", "1");
			//$isAvalaible = $bidang_usahaCheck->checkSatkerAvalaible();
			if ($bidang_usaha->insert2()) {
				echo "Data berhasil disimpan";
			} else {
				echo "Data gagal disimpan, silahkan cek kembali Kode yang digunakan, pastikan belum di input dalam sistem";
			}
			//echo $bidang_usaha->query;
		}
		else
		{
			$bidang_usaha->setField("BIDANG_USAHA_ID_UPDATE", $reqBidangTahun);
			$bidang_usaha->setField("BIDANG_USAHA_ID", $reqId);
			$bidang_usaha->setField("NAMA", $reqBidangUsaha);
			$bidang_usaha->setField("KODE", $reqBidangTahun);
			$bidang_usaha->setField("STATUS_BIDANG_USAHA", $reqStatusBidangUsaha);
			//$isAvalaible = $bidang_usahaCheck->checkSatkerAvalaible();
			if ($bidang_usaha->update2()) {
				echo "Data berhasil diupdate";
			} else {
				echo "Data gagal diupdate, silahkan cek kembali Kode yang digunakan, pastikan belum di input dalam sistem";
			}

		}
		//echo $record;
	}

	function addCustom()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("BidangUsaha");

		$bidang_usaha		= new BidangUsaha();

		$reqKode = $this->input->post("reqKode");
		$reqBidangUsahaParentId = $this->input->post("reqBidangUsahaParentId");
		$reqNama = $this->input->post("reqNama");
		$reqStatusBidangUsaha = $this->input->post("reqStatusBidangUsaha"); 
		$reqBidangUsahaJenis = $this->input->post("reqBidangUsahaJenis");

		$bidang_usaha->setField("BIDANG_USAHA_ID", $reqKode);
		$bidang_usaha->setField("BIDANG_USAHA_PARENT_ID", $reqBidangUsahaParentId);
		$bidang_usaha->setField("NAMA", $reqNama);
		$bidang_usaha->setField("KODE", $reqKode);
		$bidang_usaha->setField("STATUS_BIDANG_USAHA", $reqStatusBidangUsaha);
		$bidang_usaha->setField("BIDANG_USAHA_JENIS", $reqBidangUsahaJenis);
  		$bidang_usaha->setField('CREATED_BY', $this->USER_LOGIN_ID);


		if ($bidang_usaha->insert3()) {
			echo "Data berhasil disimpan";
		} else {
			echo "Data gagal disimpan, silahkan cek kembali Kode yang digunakan, pastikan belum di input dalam sistem";
		}
		 
	}

	function master_bidang_usaha_edit()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("BidangUsaha");

		/* create objects */
		$bidang_usaha		= new BidangUsaha();

		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "main/index";';
				echo '</script>';
				exit;
			}
		}

		$submitSimpan = $this->input->post("submitSimpan");


		$reqParamKey	= $this->input->post("reqParamKey");
		$reqBidangTahun 	= $this->input->post("reqBidangTahun");
		$reqBidangUsaha 		= $this->input->post("reqBidangUsaha");

		$tempBidangTahun	 = $reqBidangTahun ;
		$tempBidangUsaha	 	 = $reqBidangUsaha ;

		/* VALIDATION */
		$validate->setValidate(array(
								$reqBidangTahun,
								$reqBidangUsaha
								));
		$validate->setMessage(array(
								'Anda belum mengisi Nama Satker',
								'Anda belum mengisi Kode Satker'
								));
		// trigger the validation
		if($submitSimpan == 'Simpan')
		{
			$validate->notEmpty();
			$alertMsg .= $validate->getMessage();
		}

		/* ACTION BY REQMODE */
		if($submitSimpan == 'Simpan' && $validate->statNotEmpty){
			//echo  $bidang_usaha->getMaxIdTree($reqParamKey);
			//$bidang_usaha->setField("SATKER_ID", $bidang_usaha->getMaxIdTree($reqParamKey)+1);
			$bidang_usaha->setField("BIDANG_USAHA_ID", $reqParamKey);
			$bidang_usaha->setField("NAMA", $reqBidangUsaha);
			$bidang_usaha->setField("KODE", $reqBidangTahun);

			//$isAvalaible = $bidang_usahaCheck->checkSatkerAvalaible();
			$bidang_usaha->update();
			//echo $bidang_usaha->query;
			echo '<script language="javascript">';
			echo "window.opener.location.href = '".base_url()."main/loadUrl/main/master_bidang_usaha/?reqStatus=simpan';";
			echo "window.close();";
			echo '</script>';

		}
	}

	function delete()
	{
		$this->load->model("BidangUsaha");
		$bidang_usaha = new BidangUsaha();
		/* PARAMETERS */
		$reqId			= $this->input->get('reqId');

		$bidang_usaha->selectByParams(array("BIDANG_USAHA_ID" => $reqId));
		$bidang_usaha->firstRow();
		//echo $bidang_usaha->query;exit;

		$bidang_usaha->setField("BIDANG_USAHA_ID", $reqId);

		if($bidang_usaha->delete())
		{
			echo "Data berhasil dihapus";
		}
		else
		{
			echo "Data gagal dihapus";
		}
	}

}
?>
