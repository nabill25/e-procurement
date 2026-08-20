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

class panitia_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}

		/* GLOBAL VARIABLE */
		// $this->db->query("alter session set nls_date_format='YYYY-MM-DD'");
		// $this->db->query("alter session set nls_numeric_characters='.,'");

		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';

		$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID)?$this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID:'';
		$this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN)?$this->kauth->getInstance()->getIdentity()->USER_LOGIN:'';
		$this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA)?$this->kauth->getInstance()->getIdentity()->USER_NAMA:'';
		$this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID)?$this->kauth->getInstance()->getIdentity()->USER_TYPE_ID:'';
		$this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';
		$this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID)?$this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID:'';
		$this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP)?$this->kauth->getInstance()->getIdentity()->NIP:'';
		$this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME)?$this->kauth->getInstance()->getIdentity()->LOGIN_TIME:'';
		$this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE)?$this->kauth->getInstance()->getIdentity()->LOGIN_DATE:'';
		$this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA)?$this->kauth->getInstance()->getIdentity()->NAMA:'';
		$this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE)?$this->kauth->getInstance()->getIdentity()->KODE:'';
		$this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP)?$this->kauth->getInstance()->getIdentity()->PKP:'';
		$this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP)?$this->kauth->getInstance()->getIdentity()->NPWP:'';
		$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN)?$this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN:'';
		$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI)?$this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI:'';
	}

	function get_data_daftar_panitia()
	{
		$this->load->model("Panitia");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$panitia = new Panitia();
		$reqSearch = strtoupper($this->input->get("reqSearch"));
		$reqId = $this->input->get("reqId");

		$paketInfo->getPaket($reqId);

		if($this->UNIT_KERJA_ID == "1")
			$arrStatement = array();
		else
			$arrStatement = array("C.UNIT_KERJA_ID" => $this->UNIT_KERJA_ID);

		if($reqId == '')
		{}
		else
			$statement =" AND NOT EXISTS (SELECT 1 FROM PAKET_PANITIA X WHERE X.NIP = A.NIP AND X.PAKET_ID = '".$reqId."')";

		$statement .= " AND NOT A.NIP = '".$paketInfo->nip."' AND C.USER_TYPE_ID='3' AND ((UPPER(NAMA) LIKE '%".strtoupper($reqSearch)."%')) ";

		$panitia->selectByParamsCari2($arrStatement, -1, -1, 0, $statement);
		// echo $panitia->query;exit;
		$met = array();
		$i=0;

		while($panitia->nextRow()){
			$met[$i]['id'] = $panitia->getField('NIP');
			$met[$i]['text'] = $panitia->getField('NAMA');
			$met[$i]['NAMA'] = $panitia->getField('NAMA');
			$met[$i]['NIP'] = $panitia->getField('NIP');
			$met[$i]['JABATAN'] = $panitia->getField('JABATAN');
			$met[$i]['FUNGSI'] = $panitia->getField('FUNGSI');
			$met[$i]['KETUA'] = $panitia->getField('KETUA');
			// $met[$i]['JABATAN_STR'] = $panitia->getField('JABATAN_STR').
			$met[$i]['JABATAN_STR'] = '<span>Beban Paket: '.$panitia->getField('BEBAN_PAKET_PANITIA').'</span>
									    <br><span>Beban Paket Proses: '.$panitia->getField('BEBAN_PAKET_PANITIA_PROSES').'</span>';
			$met[$i]['BEBAN_PAKET_PANITIA'] = $panitia->getField('BEBAN_PAKET_PANITIA');
			$met[$i]['BEBAN_PAKET_PANITIA_PROSES'] = $panitia->getField('BEBAN_PAKET_PANITIA_PROSES');
			$met[$i]['PAKTA'] = '-';
			$i++;
		}
		echo json_encode($met);
	}

	function sk_daftar_panitia_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth();
		$this->load->model("Panitia");
		$panitia = new Panitia();

		/* LOGIN CHECK
		if ($userLogin->checkUserLogin())
		{
			$userLogin->retrieveUserInfo();
		}*/

		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 520);

		$reqKeterangan = httpFilterRequest("reqKeterangan");
		$reqId = httpFilterRequest("reqId");
		$reqSearch = $this->input->get("reqSearch");

		/*
		 * Paging
		 */
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
		}
		else{
			$_GET['iDisplayStart'] = $_GET['iDisplayLength'] = '-1';
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
			if ( isset( $_GET['iSortCol_0'] ) )
			{
				$sOrder = "ORDER BY  ";
				for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
				{
					if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
					{
						$sOrder .= 'upper('.$aColumns[ intval( $_GET['iSortCol_'.$i] ) ].")
							".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
					}
				}

				$sOrder = substr_replace( $sOrder, "", -2 );
				if ( $sOrder == "ORDER BY" )
				{
					$sOrder = "";
				}
			}
		//$reqId = 2;

		if($reqSearch == "")
		{
			$allRecord = $panitia->getCountByParams(array("SK_PANITIA_ID"=>$reqId));
			$panitia->selectByParams(array("SK_PANITIA_ID"=>$reqId), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement);
		}
		else
		{
			$reqSearch = str_replace('\\', '', $reqSearch);
			$allRecord = 1;
			//$panitia->selectByParams(array(), $_GET['iDisplayLength'], $_GET['iDisplayStart'], $statement.$reqSearch." ");

		}

		$column = array('PANITIA_ID', 'NO', 'NIP','NAMA','JABATAN','FUNGSI','STATUS');
			/*
			 * Output
			 */
			$output = array(
				"sEcho" => intval($_GET['sEcho']),
				"iTotalRecords" => $allRecord,
				"iTotalDisplayRecords" => $allRecord,
				"aaData" => array()
			);
			$number = 1;
			while($panitia->nextRow())
			{
				$row = array();
				for ( $i=0 ; $i<count($column) ; $i++ )
				{
					if($column[$i]=='NO')		$row[] = $number;
					elseif($column[$i]=='STATUS'){
						if( $panitia->getField(trim($column[$i])) == 1)	$st = 'Berlaku';
						else												$st = 'Tidak Berlaku';
						$row[] = $st;
					}
					else						$row[] = $panitia->getField(trim($column[$i]));
				}
				$number++;
				$output['aaData'][] = $row;
			}

			echo json_encode( $output );
	}

	function delete_sk_daftar_panitia()
	{
		$this->load->model("Panitia");
		$set= new Panitia();
		$set->setField('PANITIA_ID', $reqId);

		if($set->delete())
			$alertMsg .= "Data berhasil dihapus";
		else
			$alertMsg .= "Error ".$set->getErrorMsg();
		//echo $set->query;
		//echo "asd";
	}

	function panitia_combo_json_bak()
	{
		$this->load->model("Panitia");
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$panitia = new Panitia();

		if($this->UNIT_KERJA_ID == "1")
			$arrStatement = array();
		else
			$arrStatement = array("UNIT_KERJA_ID" => $this->UNIT_KERJA_ID);

		$panitia->selectByParamsCari($arrStatement, -1, -1, 0, $statement);
		//echo $panitia->query;exit;
		$met = array();
		$i=0;

		while($panitia->nextRow())
		{
			$arr_json[$i]['id'] = $panitia->getField("NIP");
			$arr_json[$i]['text'] = trim($panitia->getField("NAMA"));
			$arr_json[$i]['jabatan'] = $panitia->getField("JABATAN");
			$arr_json[$i]['cabang'] = $panitia->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json()
	{
		$this->load->model("AuthUser");
		$auth_user = new AuthUser();

		$reqUnitKerja = $this->input->get("reqUnitKerja");

		$auth_user->selectByParams(array());
		//echo $auth_user->query;exit;

		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("NIPP");
			$arr_json[$i]['text'] = trim($auth_user->getField("NAMA"));
			$arr_json[$i]['jabatan'] = $auth_user->getField("NAJAB");
			$arr_json[$i]['cabang'] = $auth_user->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json_panitia()
	{
		$this->load->model("AuthUser");
		$auth_user = new AuthUser();

		$reqUnitKerja = $this->input->get("reqUnitKerja");

		$auth_user->selectByParams(array("USER_TYPE_ID" => 3));
		//echo $auth_user->query;exit;

		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("NIPP");
			$arr_json[$i]['text'] = trim($auth_user->getField("NAMA"));
			$arr_json[$i]['jabatan'] = $auth_user->getField("NAJAB");
			$arr_json[$i]['cabang'] = $auth_user->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json_pembeli()
	{
		$this->load->model("AuthUser");
		$auth_user = new AuthUser();

		$reqUnitKerja = $this->input->get("reqUnitKerja");

		$auth_user->selectByParams(array("USER_TYPE_ID" => 11));
		//echo $auth_user->query;exit;

		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("NIPP");
			$arr_json[$i]['text'] = trim($auth_user->getField("NAMA"));
			$arr_json[$i]['jabatan'] = $auth_user->getField("NAJAB");
			$arr_json[$i]['cabang'] = $auth_user->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json_nopl()
	{
		$this->load->model("AuthUser");
		$this->load->model("UserLogin");
		$auth_user = new AuthUser();
		$user_login = new UserLogin();

		$userloginid = $this->input->get("userloginid");
		$unitkerja = $this->input->get("unitkerja");

		$auth_user->selectByParams(array(),-1,-1," AND UNIT_KERJA_ID = '".$unitkerja."' AND USER_AKTIF = '1' AND USER_TYPE_ID = '3'");
		// echo $auth_user->query;exit;

		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("NIPP");
			$arr_json[$i]['text'] = trim($auth_user->getField("NAMA"));
			$arr_json[$i]['jabatan'] = $auth_user->getField("NAJAB");
			$arr_json[$i]['cabang'] = $auth_user->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json_pl()
	{
		$this->load->model("AuthUser");
		$this->load->model("UserLogin");
		$auth_user = new AuthUser();
		$user_login = new UserLogin();

		$userloginid = $this->input->get("userloginid");
		$unitkerja = $this->input->get("unitkerja");

		$user_login->selectByParams(array('USER_LOGIN_ID' => $userloginid));
		$user_login->firstRow();
		//echo $auth_user->query;exit;
		// $auth_user->selectByParams(array('USER_AKTIF' => '1', 'USER_LOGIN_ID' => $user_login->getField("CHILD_PL")));
		$auth_user->selectByParams(array(),-1,-1," AND UNIT_KERJA_ID = '".$unitkerja."' AND USER_AKTIF = '1' AND USER_TYPE_ID = '3' AND TENDER = 0");


		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("NIPP");
			$arr_json[$i]['text'] = trim($auth_user->getField("NAMA"));
			$arr_json[$i]['jabatan'] = $auth_user->getField("NAJAB");
			$arr_json[$i]['cabang'] = $auth_user->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json_purchase()
	{
		$this->load->model("AuthUser");
		$this->load->model("UserLogin");
		$auth_user = new AuthUser();
		$user_login = new UserLogin();

		$userloginid = $this->input->get("userloginid");
		$unitkerja = $this->input->get("unitkerja");

		// $user_login->selectByParams(array('USER_AKTIF' => '1', 'USER_TYPE_ID' => '11'),-1,-1," AND UNIT_KERJA_ID = '".$unitkerja."'" );
		$user_login->selectByParams(array('USER_AKTIF' => '1', 'USER_TYPE_ID' => '11'),-1,-1,"" );
		// echo $user_login->query;exit;

		$arr_json = array();
		$i = 0;
		while($user_login->nextRow())
		{
			$arr_json[$i]['id'] = $user_login->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($user_login->getField("USER_NAMA"));
			$arr_json[$i]['jabatan'] = $user_login->getField("USER_JABATAN");
			$arr_json[$i]['cabang'] = $user_login->getField("USER_NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json_penyelia()
	{
		$this->load->model("AuthUser");
		$this->load->model("UserLogin");
		$auth_user = new AuthUser();
		$user_login = new UserLogin();

		$userloginid = $this->input->get("userloginid");
		$reqUnitKerja = $this->input->get("reqUnitKerja");

		// $user_login->selectByParams(array('USER_AKTIF' => '1', 'USER_TYPE_ID' => '3', 'USER_JABATAN_PANITIA' => '2'));
		$user_login->selectByParams(array('USER_AKTIF' => '1', 'USER_TYPE_ID' => '3', 'USER_JABATAN_PANITIA' => '1'));
		// echo $user_login->query;exit;

		$arr_json = array();
		$i = 0;
		while($user_login->nextRow())
		{
			$arr_json[$i]['id'] = $user_login->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($user_login->getField("USER_NAMA"));
			$arr_json[$i]['jabatan'] = $user_login->getField("USER_JABATAN");
			$arr_json[$i]['cabang'] = $user_login->getField("USER_NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function panitia_combo_json_ppkom()
	{
		$this->load->model("AuthUser");
		$this->load->model("UserLogin");
		$auth_user = new AuthUser();
		$user_login = new UserLogin();

		$userloginid = $this->input->get("userloginid");
		$reqUnitKerja = $this->input->get("reqUnitKerja");

		$user_login->selectByParams(array('USER_AKTIF' => '1', 'USER_TYPE_ID' => '9', 'UNIT_KERJA_ID' => $reqUnitKerja));
		// echo $user_login->query;exit;

		$arr_json = array();
		$i = 0;
		while($user_login->nextRow())
		{
			$arr_json[$i]['id'] = $user_login->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($user_login->getField("USER_NAMA")).' : '.$user_login->getField("USER_JABATAN");
			$arr_json[$i]['jabatan'] = $user_login->getField("USER_JABATAN");
			$arr_json[$i]['cabang'] = $user_login->getField("USER_NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function ppk_combo_json()
	{
		$this->load->model("AuthUser");
		$this->load->model("UserLogin");
		$auth_user = new AuthUser();
		$user_login = new UserLogin();

		$userloginid = $this->input->get("userloginid");
		$reqUnitKerja = $this->input->get("reqUnitKerja");

		$user_login->selectByParams(array('USER_AKTIF' => '1', 'USER_TYPE_ID' => '12', 'LEGAL' => '0'));
		// echo $user_login->query;exit;

		$arr_json = array();
		$i = 0;
		while($user_login->nextRow())
		{
			$arr_json[$i]['id'] = $user_login->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($user_login->getField("USER_NAMA"));
			$arr_json[$i]['jabatan'] = $user_login->getField("USER_JABATAN");
			$arr_json[$i]['cabang'] = $user_login->getField("USER_NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function vendor_combo_json()
	{
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		$rekanan->selectByParams(array("STATUS_VALIDASI" => "1"), -1, -1, "");

		$arr_json = array();
		$i = 0;
		while($rekanan->nextRow())
		{
			$arr_json[$i]['id'] = $rekanan->getField("REKANAN_ID");
			$arr_json[$i]['text'] = trim($rekanan->getField("NAMA"));
			$arr_json[$i]['telepon'] = $rekanan->getField("TELEPON");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function vendor_retail_combo_json()
	{
		$this->load->model("Vendorretail");
		$vendor_retail = new Vendorretail();

		$vendor_retail->selectByParams(array(), -1, -1, "");

		$arr_json = array();
		$i = 0;
		while($vendor_retail->nextRow())
		{
			$arr_json[$i]['id'] = $vendor_retail->getField("REKANAN_RETAIL_ID");
			$arr_json[$i]['text'] = trim($vendor_retail->getField("NAMA"));
			$arr_json[$i]['telepon'] = $vendor_retail->getField("TELEPON");
			$i++;
		}
		echo json_encode($arr_json);
	}

	function vendor_combo_detail_json()
	{
		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		$reqRekananId = $this->input->get("reqRekananId");

		$rekanan->selectByParamsCari(array("A.REKANAN_ID" => $reqRekananId), -1, -1, "");
		$rekanan->firstRow();

		$arrJson["nama"] = $rekanan->getField('NAMA');
		$arrJson["alamat"] = $rekanan->getField('ALAMAT');
		$arrJson["npwp"] = $rekanan->getField('NPWP');
		$arrJson["telepon"] = $rekanan->getField('TELEPON');
		$arrJson["email"] = $rekanan->getField('EMAIL');

		echo json_encode($arrJson);
	}

	function vendor_retail_combo_detail_json()
	{
		$this->load->model("Vendorretail");
		$vendor_retail = new Vendorretail();

		$reqRekananId2 = $this->input->get("reqRekananId2");

		$vendor_retail->selectByParams(array("REKANAN_RETAIL_ID" => $reqRekananId2), -1, -1, "");
		$vendor_retail->firstRow();

		$arrJson["nama"] = $vendor_retail->getField('NAMA');
		$arrJson["alamat"] = $vendor_retail->getField('ALAMAT');
		$arrJson["npwp"] = $vendor_retail->getField('NPWP');
		$arrJson["telepon"] = $vendor_retail->getField('TELEPON');
		$arrJson["email"] = '';

		echo json_encode($arrJson);
	}

	function panitia_combo_json_tender()
	{
		$this->load->model("AuthUser");
		$this->load->model("UserLogin");
		$auth_user = new AuthUser();
		$user_login = new UserLogin();

		$userloginid = $this->input->get("userloginid");
		$unitkerja = $this->input->get("unitkerja");

		$auth_user->selectByParamsPanitiaTender(array(),-1,-1," AND A.UNIT_KERJA_ID = '".$unitkerja."'");
		// echo $auth_user->query;exit;

		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("USER_LOGIN_ID");
			$arr_json[$i]['text'] = trim($auth_user->getField("NAMA"));
			// $arr_json[$i]['jabatan'] = $auth_user->getField("NAJAB");
			// $arr_json[$i]['cabang'] = $auth_user->getField("NAMA");
			$i++;
		}
		echo json_encode($arr_json);
	}



	/*function panitia_combo_json()
	{
		$this->load->model("UsersBase");
		$auth_user = new UsersBase();

		$reqUnitKerja = $this->input->get("reqUnitKerja");

		$auth_user->selectByParams(array(), -1, -1, $stat." AND A.USER_TYPE_ID = 3 ");
		//echo $auth_user->query;exit;

		$arr_json = array();
		$i = 0;
		while($auth_user->nextRow())
		{
			$arr_json[$i]['id'] = $auth_user->getField("NIP");
			$arr_json[$i]['text'] = trim($auth_user->getField("USER_NAMA"));
			$arr_json[$i]['jabatan'] = $auth_user->getField("USER_JABATAN");
			$arr_json[$i]['cabang'] = "Kantor Pusat";
			$i++;
		}
		echo json_encode($arr_json);
	}*/

}
?>
