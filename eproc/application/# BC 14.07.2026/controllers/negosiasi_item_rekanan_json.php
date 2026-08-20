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

class negosiasi_item_rekanan_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");

		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{ }

		/* GLOBAL VARIABLE */
		$this->ID = isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';

		$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
		$this->USER_LOGIN =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN : '';
		$this->USER_NAMA =  isset($this->kauth->getInstance()->getIdentity()->USER_NAMA) ? $this->kauth->getInstance()->getIdentity()->USER_NAMA : '';
		$this->USER_TYPE_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_TYPE_ID) ? $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID : '';
		$this->REKANAN_ID =  isset($this->kauth->getInstance()->getIdentity()->REKANAN_ID) ? $this->kauth->getInstance()->getIdentity()->REKANAN_ID : '';
		$this->UNIT_KERJA_ID =  isset($this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID) ? $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID : '';
		$this->NIP =  isset($this->kauth->getInstance()->getIdentity()->NIP) ? $this->kauth->getInstance()->getIdentity()->NIP : '';
		$this->APPROVAL_UNIT =  isset($this->kauth->getInstance()->getIdentity()->APPROVAL_UNIT) ? $this->kauth->getInstance()->getIdentity()->APPROVAL_UNIT : '';
		$this->LOGIN_TIME = isset($this->kauth->getInstance()->getIdentity()->LOGIN_TIME) ? $this->kauth->getInstance()->getIdentity()->LOGIN_TIME : '';
		$this->LOGIN_DATE = isset($this->kauth->getInstance()->getIdentity()->LOGIN_DATE) ? $this->kauth->getInstance()->getIdentity()->LOGIN_DATE : '';
		$this->REKANAN = isset($this->kauth->getInstance()->getIdentity()->NAMA) ? $this->kauth->getInstance()->getIdentity()->NAMA : '';
		$this->REKANAN_KODE = isset($this->kauth->getInstance()->getIdentity()->KODE) ? $this->kauth->getInstance()->getIdentity()->KODE : '';
		$this->REKANAN_PKP = isset($this->kauth->getInstance()->getIdentity()->PKP) ? $this->kauth->getInstance()->getIdentity()->PKP : '';
		$this->REKANAN_NPWP = isset($this->kauth->getInstance()->getIdentity()->NPWP) ? $this->kauth->getInstance()->getIdentity()->NPWP : '';
		$this->REKANAN_STATUS_PERUSAHAAN = isset($this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN) ? $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN : '';
		$this->REKANAN_STATUS_VALIDASI = isset($this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI) ? $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI : '';
		$this->DEPARTMENT = isset($this->kauth->getInstance()->getIdentity()->DEPARTMENT) ? $this->kauth->getInstance()->getIdentity()->DEPARTMENT : '';
	} 

	function json() 
	{
		$this->load->model("Paketnegosiasiitem");
		$negosiasiitem = new Paketnegosiasiitem();
		
		$reqId = $this->input->get("reqId"); // PAKET ID
		$reqSearch = $this->input->post("reqSearch");
		
		$aColumns 			= array('PAKET_NEGOSIASI_ITEM_ID','PAKET_ID','URAIAN','VOLUME','SATUAN_VOLUME','DURASI','SATUAN_DURASI','HARGA_SATUAN','JUMLAH_HARGA','NILAI_PENAWARAN','JUMLAH_PENAWARAN','NILAI_NEGOSIASI','JUMLAH_NEGOSIASI','STATUS_NEGO','STATUS_NEGO_ID');
		$aColumnsAlias		= array('PAKET_NEGOSIASI_ITEM_ID','PAKET_ID','URAIAN','VOLUME','SATUAN_VOLUME','DURASI','SATUAN_DURASI','HARGA_SATUAN','JUMLAH_HARGA','NILAI_PENAWARAN','JUMLAH_PENAWARAN','NILAI_NEGOSIASI','JUMLAH_NEGOSIASI','STATUS_NEGO','STATUS_NEGO_ID');
		
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";

			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];

					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}
			$sOrder = substr_replace( $sOrder, "", -2 );
			
			if ( trim($sOrder) == "ORDER BY PAKET_NEGOSIASI_ITEM_ID desc" )
			{
				$sOrder = " ORDER BY PAKET_NEGOSIASI_ITEM_ID DESC ";
				 
			}
		}
		 
		$sWhere = "";
		$nWhereGenearalCount = 0;
		if (isset($_GET['sSearch']))
		{
			$sWhereGenearal = $_GET['sSearch'];
		}
		else
		{
			$sWhereGenearal = '';
		}
		
		if ( $_GET['sSearch'] != "" )
		{
			$sWhere = " AND (";
			for ( $i=0 ; $i<count($aColumnsAlias)+1 ; $i++ )
			{
				if ( $_GET['bSearchable_'.$i] == "true" )
				{
					$sWhere .= $aColumnsAlias[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
					$nWhereGenearalCount += 1;
				}
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}

		$sWhereSpecificArray = array();
		$sWhereSpecificArrayCount = 0;
		for ( $i=0 ; $i<count($aColumnsAlias) ; $i++ )
		{
			if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "AND ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				 
				$sWhere .= $aColumnsAlias[$i]." LIKE '%' || :whereSpecificParam".$sWhereSpecificArrayCount." || '%' ";
				$sWhereSpecificArrayCount++;
				$sWhereSpecificArray[] =  $_GET['sSearch_'.$i];
			}
		}
		 
		if ( $sWhere == "" )
		{
			$sWhere = " AND 1=1";
		}
		 
		if ( isset( $_GET['iDisplayStart'] ))
		{
			$dsplyStart = $_GET['iDisplayStart'];
		}
		else{
			$dsplyStart = 0;
		}
		if ( isset( $_GET['iDisplayLength'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$dsplyRange = $_GET['iDisplayLength'];
			if ($dsplyRange > (2147483645 - intval($dsplyStart)))
			{
				$dsplyRange = 2147483645;
			}
			else
			{
				$dsplyRange = intval($dsplyRange);
			}
		}
		else
		{
			$dsplyRange = 2147483645;
		}
		
		$statement = " AND STATUS_NEGO != '0' AND PAKET_ID = ".$reqId." AND (UPPER(URAIAN) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $negosiasiitem->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $negosiasiitem->getCountByParams(array(), $statement, $sOrder);

		$negosiasiitem->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($negosiasiitem->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO') {
					$row[] = $number;
				} elseif($aColumns[$i]=='STATUS_NEGO') { 
					if ($negosiasiitem->getField(trim($aColumns[$i])) == '1') {
						$row[] = '<span class="badge badge-success">Approved</span>';
					} elseif ($negosiasiitem->getField(trim($aColumns[$i])) == '2') {
						$row[] = '<span class="badge badge-warning">Menunggu cek penyedia</span>';
					} elseif ($negosiasiitem->getField(trim($aColumns[$i])) == '3') {
						$row[] = '<span class="badge badge-danger">Ditolak penyedia</span>';
					} else {
						$row[] = '-';
					} 
				} elseif($aColumns[$i]=='URAIAN') { 
					$row[] = $negosiasiitem->getField(trim($aColumns[$i]));
				} elseif($aColumns[$i]=='VOLUME') { 
					$row[] = $negosiasiitem->getField(trim($aColumns[$i])).' '.$negosiasiitem->getField('SATUAN_VOLUME');
				} elseif($aColumns[$i]=='DURASI') { 
					$row[] = $negosiasiitem->getField(trim($aColumns[$i])).' '.$negosiasiitem->getField('SATUAN_DURASI');
				} elseif($aColumns[$i]=='HARGA_SATUAN' || $aColumns[$i]=='JUMLAH_HARGA' || $aColumns[$i]=='NILAI_PENAWARAN' || $aColumns[$i]=='JUMLAH_PENAWARAN' || $aColumns[$i]=='NILAI_NEGOSIASI' || $aColumns[$i]=='JUMLAH_NEGOSIASI') { 
					$row[] = number_format($negosiasiitem->getField(trim($aColumns[$i])),0,',','.');
				} else {	
					$row[] = $negosiasiitem->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}

	function deleteFileAnalisa() 
	{
		$this->load->model('Paketnegosiasiitem');
		$reqId		= $this->input->get('reqId');
		$negosiasiitem	= new Paketnegosiasiitem();
		$negosiasiitem->setField("PAKET_NEGOSIASI_ITEM_ID", $reqId);
		$negosiasiitem->deleteByID();
		
		echo "Data berhasil disimpan.";
	}

	function randomKode($length = 4) {
	    $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ123456789';
	    $result = '';

	    for ($i = 0; $i < $length; $i++) {
	        $result .= $chars[random_int(0, strlen($chars) - 1)];
	    }

	    return $result;
	}

	function edit()
	{
		$this->load->model(array("Paketnegosiasiitem"));
		$negoitem = new Paketnegosiasiitem(); 

		$reqId = $this->input->post("reqId"); // Negosiasi Item ID
		$reqPaketId = $this->input->post("reqPaketId"); // Paket ID

		$reqUraian = $this->input->post("reqUraian");
		$reqNilaiNegosiasi = $this->input->post("reqNilaiNegosiasi");
		$reqJumlahNegosiasi = $this->input->post("reqJumlahNegosiasi");
		$reqStatusNego = $this->input->post("reqStatusNego");

		$negoitem->setField('PAKET_NEGOSIASI_ITEM_ID', $reqId);
		$negoitem->setField('NILAI_NEGOSIASI', dotToNo($reqNilaiNegosiasi));
		$negoitem->setField('JUMLAH_NEGOSIASI', dotToNo($reqJumlahNegosiasi));
		$negoitem->setField('STATUS_NEGO', $reqStatusNego);
		$negoitem->setField('UPDATED_BY', $this->USER_LOGIN_ID);
		$update = $negoitem->updateNegoRekanan();
 	
 		if ($update) {
 			if ($reqStatusNego == '3') { // Ditolak
 				// Insert Rekam Jejak
			    $this->load->library("librekamjejak");
			    $this->librekamjejak->insertRJ('3512','Penyedia menolak item '.$reqUraian.' menjadi '.dotToNo($reqNilaiNegosiasi),$reqPaketId,'null','3512'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
 			} elseif ($reqStatusNego == '1') { // Approved
	 			// Insert Rekam Jejak
			    $this->load->library("librekamjejak");
			    $this->librekamjejak->insertRJ('3511','Penyedia setuju item '.$reqUraian.' dengan nilai nego '.dotToNo($reqNilaiNegosiasi),$reqPaketId,'null','3511'); // param 1: Posisi/'null', param 2: Keterangan/'', param 3: Paket_id/'null', param 4: permohonan_id/'null'
 			}

			echo "Data berhasil diupdate";
 		} else {
			echo "Data gagal diupdate";
 		}
	}

}
?>
