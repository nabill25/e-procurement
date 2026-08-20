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

class fungsi_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID)?$this->kauth->getInstance()->getIdentity()->REKANAN_ID:'';
	}

	function get_bulan_rekening_koran()
	{
		$this->load->model("Metode");
		$metode = new Metode();

		$reqId = $this->input->get("reqId");

		$arrBulanTahun = explode("-", $reqId);
		$reqBulan = $arrBulanTahun[0];
		$year = $year1 = $year2 = $arrBulanTahun[1];

		$month = $reqBulan;
		if($month <= 0)
		{
			$year = date("Y") - 1;
			$month = 12 + $month;
			$monthname = getNameMonth($month);
		}
		else
			$monthname = getNameMonth($month);
		$month1 = $reqBulan - 1;
		if($month1 <= 0)
		{
			$year1 = date("Y") - 1;
			$month1 = 12 + $month1;
			$monthname1 = getNameMonth($month1);
		}
		else
			$monthname1 = getNameMonth($month1);
		$month2 = $reqBulan - 2;
		if($month2 <= 0)
		{
			$year2 = date("Y") - 1;
			$month2 = 12 + $month2;
			$monthname2 = getNameMonth($month2);
		}
		else
			$monthname2 = getNameMonth($month2);

		$met = array("REKENING_KORAN" => $monthname2." ".$year2.", ".$monthname1." ".$year1.", ".$monthname." ".$year,"REKENING_KORAN_SET_VALID" => $month2.$year2.", ".$month1.$year1.", ".$month.$year);

		echo json_encode($met);

	}

	function captcha_validation()
	{
		$reqParam1 = $this->input->post("reqParam1");
		if(!empty($_SESSION['security_code']) && $_SESSION['security_code'] == $reqParam1 )
			echo "true";
		else
			echo "false";
	}

	function check_npwp()
	{
		$reqParam1 = $this->input->post("reqParam1");
		$reqParam2 = $this->input->post("reqParam2");

		if($reqParam1 == "")
			exit;

		if($reqParam2 == "0")
		{
			$this->load->model("Rekanan");
			$rekanan = new Rekanan();

			$rekanan->checkNpwp($reqParam1);
			$rekanan->firstRow();
			if($rekanan->getField("NPWP") == "")
				echo "true";
			else
				echo "false";
			unset($rekanan);
		}
		else
			echo "true";

	}

	function check_npwp_double()
	{
		$reqParam1 = $this->input->post("reqParam1");
		$reqParam2 = $this->input->post("reqParam2");

		if($reqParam1 == "")
			exit;

		if($reqParam2 == "0")
		{
			$this->load->model(array("Rekanan","Vendorretail"));
			$rekanan = new Rekanan();

			$rekanan->checkNpwp($reqParam1);
			$rekanan->firstRow();
			if($rekanan->getField("NPWP") == "") {
				$reqParam1 = str_replace(" ","",$reqParam1);
				$vendor_retail = new Vendorretail();
				$vendor_retail->checkNpwp($reqParam1);
				$vendor_retail->firstRow();
				if($vendor_retail->getField("NPWP") == "") {
					echo "true";
				} else {
					echo "false";
				}
			} else {
				echo "false";
			}
			unset($rekanan);
		}
		else {
			echo "true";
		}
	}

	function check_email()
	{
		$reqParam1 = $this->input->post("reqParam1");

		if($reqParam1 == "")
			exit;

		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		$adaData = $rekanan->getCountByParams(array("A.EMAIL" => $reqParam1));
		if($adaData == 0)
			echo "true";
		else
			echo "false";

	}

	function check_email_ubah()
	{
		$reqParam1 = $this->input->post("reqParam1");
		$reqParam2 = $this->input->post("reqParam2");

		if($reqParam1 == "")
			exit;

		$this->load->model("Rekanan");
		$rekanan = new Rekanan();

		// $adaData = $rekanan->getCountByParams(array("A.EMAIL" => $reqParam1), " AND A.EMAIL != '".$reqParam2."'");
		$adaData = $rekanan->getCountByParams(array("A.EMAIL" => $reqParam1));
		if($adaData == 0)
			echo "true";
		else
			echo "false";

	}

	function check_username()
	{
		$reqParam1 = $this->input->post("reqParam1");

		if($reqParam1 == "")
			exit;

		$this->load->model("UsersBase");
		$users_base = new UsersBase();

		$adaData = $users_base->getCountByParams(array("A.USER_LOGIN" => $reqParam1));
		if($adaData == 0)
			echo "true";
		else
			echo "false";

	}

}
?>
