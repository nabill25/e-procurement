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

class sppjb_json extends CI_Controller {

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
		$this->load->model("Sppjb");
		$sppjb = new Sppjb();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");
		
		$aColumns 			= array('SPPJB_ID', 'NAMA', 'KODE', 'TANGGAL', 'NAMA_DIRUT', 'NAMA', 'ALAMAT_DIRUT', 'KOTA_DIRUT', 'PPN', 'PERSEN_JAMINAN', 'TMT_JAMINAN', 'JANGKA_WAKTU', 'PENANDA_TANGAN', 'PENANDA_TANGAN_JABATAN');
		$aColumnsAlias		= array('SPPJB_ID', 'NAMA', 'KODE', 'TANGGAL', 'NAMA_DIRUT', 'NAMA', 'ALAMAT_DIRUT', 'KOTA_DIRUT', 'PPN', 'PERSEN_JAMINAN', 'TMT_JAMINAN', 'JANGKA_WAKTU', 'PENANDA_TANGAN', 'PENANDA_TANGAN_JABATAN');
		
		/*
		 * Ordering
		 */
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = " ORDER BY ";
			 
			//Go over all sorting cols
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				//If need to sort by current col
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					//Add to the order by clause
					$sOrder .= $aColumnsAlias[ intval( $_GET['iSortCol_'.$i] ) ];
					 
					//Determine if it is sorted asc or desc
					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 1)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			}
			
			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );
			
			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY SPPJB_ID desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY NAMA ASC ";
				 
			}
		}
		 
		/*
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables.
		 */
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
			//Set a default where clause in order for the where clause not to fail
			//in cases where there are no searchable cols at all.
			$sWhere = " AND (";
			for ( $i=0 ; $i<count($aColumnsAlias)+1 ; $i++ )
			{
				//If current col has a search param
				if ( $_GET['bSearchable_'.$i] == "true" )
				{
					//Add the search to the where clause
					$sWhere .= $aColumnsAlias[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
					$nWhereGenearalCount += 1;
				}
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		 
		/* Individual aColumns filtering */
		$sWhereSpecificArray = array();
		$sWhereSpecificArrayCount = 0;
		for ( $i=0 ; $i<count($aColumnsAlias) ; $i++ )
		{
			if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
			{
				//If there was no where clause
				if ( $sWhere == "" )
				{
					$sWhere = "AND ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				 
				//Add the clause of the specific col to the where clause
				$sWhere .= $aColumnsAlias[$i]." LIKE '%' || :whereSpecificParam".$sWhereSpecificArrayCount." || '%' ";
				 
				//Inc sWhereSpecificArrayCount. It is needed for the bind var.
				//We could just do count($sWhereSpecificArray) - but that would be less efficient.
				$sWhereSpecificArrayCount++;
				 
				//Add current search param to the array for later use (binding).
				$sWhereSpecificArray[] =  $_GET['sSearch_'.$i];
				 
			}
		}
		 
		//If there is still no where clause - set a general - always true where clause
		if ( $sWhere == "" )
		{
			$sWhere = " AND 1=1";
		}
		 
		//Bind variables.
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
		
		//$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $sppjb->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $sppjb->getCountByParams(array(), $statement, $sOrder);

		$sppjb->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($sppjb->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO')		$row[] = $number;
					elseif($aColumns[$i]=='TANGGAL' || $aColumns[$i]=='TMT_JAMINAN' )	
							$row[] = getFormattedDateJson($sppjb->getField(trim($aColumns[$i])));
					elseif($aColumns[$i]=='STATUS'){
						if( $sppjb->getField(trim($aColumns[$i])) == 1)	$st = 'Berlaku';					
						else												$st = 'Tidak Berlaku';				
						$row[] = $st;
					}
					elseif($aColumns[$i]=='UNIT_KERJA')	$row[] = $sppjb->getField(trim($aColumns[$i]))."*".$sppjb->getField("SK_PANITIA_ID");
					else	$row[] = $sppjb->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function add() 
	{
		$this->load->model('Sppjb');
		
		$sppjb	= new Sppjb();
		
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		
		$reqKode					= $this->input->post('reqKode');
		$reqTanggal					= $this->input->post('reqTanggal');
		$reqNamaDirut				= $this->input->post('reqNamaDirut');
		$reqAlamatDirut				= $this->input->post('reqAlamatDirut');
		$reqKota					= $this->input->post('reqKota');
		$reqPPN						= $this->input->post('reqPPN');
		$reqPersenJaminan			= $this->input->post('reqPersenJaminan');
		$reqTMTJaminan				= $this->input->post('reqTMTJaminan');
		$reqJangkaWaktu				= $this->input->post('reqJangkaWaktu');
		$reqPenandaTangan			= $this->input->post('reqPenandaTangan');
		$reqJabatanPenandaTangan	= $this->input->post('reqJabatanPenandaTangan');
		$reqSppjbId					= $this->input->post('reqSppjbId');
		$reqPaketPemenangId			= $this->input->post('reqPaketPemenangId');
		$submitSimpan				= $this->input->post('submitSimpan');
		$reqJangkaWaktuJaminan		= $this->input->post('reqJangkaWaktuJaminan');
		
		if($submitSimpan == "Simpan")
		{
			
			if($reqSppjbId== '')
			{
				$sppjb	= new Sppjb();
				$sppjb->setField("PAKET_ID", $reqId);
				$sppjb->setField("KODE", $reqKode);
				$sppjb->setField("TANGGAL", dateToDBCheck($reqTanggal));
				$sppjb->setField("NAMA_DIRUT", $reqNamaDirut);
				$sppjb->setField("ALAMAT_DIRUT", $reqAlamatDirut);
				$sppjb->setField("KOTA_DIRUT", $reqKota);
				$sppjb->setField("PPN", $reqPPN);
				$sppjb->setField("PERSEN_JAMINAN", $reqPersenJaminan);
				$sppjb->setField("TMT_JAMINAN",dateToDBCheck($reqTMTJaminan));
				$sppjb->setField("JANGKA_WAKTU", $reqJangkaWaktu);
				$sppjb->setField("PENANDA_TANGAN", $reqPenandaTangan);
				$sppjb->setField("PENANDA_TANGAN_JABATAN", $reqJabatanPenandaTangan);
				$sppjb->setField("PAKET_PEMENANG_ID", $reqPaketPemenangId);
				$sppjb->setField("JANGKA_WAKTU_JAMINAN", $reqJangkaWaktuJaminan);
				$sppjb->insert();
			}
			else
			{
				$sppjb	= new Sppjb();
				$sppjb->setField("SPPBJ_ID", $reqSppjbId);
				$sppjb->setField("PAKET_ID", $reqId);
				$sppjb->setField("KODE", $reqKode);
				$sppjb->setField("TANGGAL", dateToDBCheck($reqTanggal));
				$sppjb->setField("NAMA_DIRUT", $reqNamaDirut);
				$sppjb->setField("ALAMAT_DIRUT", $reqAlamatDirut);
				$sppjb->setField("KOTA_DIRUT", $reqKota);
				$sppjb->setField("PPN", $reqPPN);
				$sppjb->setField("PERSEN_JAMINAN", $reqPersenJaminan);
				$sppjb->setField("TMT_JAMINAN",dateToDBCheck($reqTMTJaminan));
				$sppjb->setField("JANGKA_WAKTU", $reqJangkaWaktu);
				$sppjb->setField("PENANDA_TANGAN", $reqPenandaTangan);
				$sppjb->setField("PENANDA_TANGAN_JABATAN", $reqJabatanPenandaTangan);
				$sppjb->setField("PAKET_PEMENANG_ID", $reqPaketPemenangId);
				$sppjb->setField("JANGKA_WAKTU_JAMINAN", $reqJangkaWaktuJaminan);
				$sppjb->update();
			}
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('Sppjb');
		
		$sppjb	= new Sppjb();
		
		$reqId		= $this->input->get('reqId');
		
		$reqKode		= $this->input->post('reqKode');
		
		$sppjb	= new Sppjb();
		$sppjb->setField("PAKET_ID", $reqId);
		$sppjb->delete();
		
		echo "Data berhasil disimpan.";
	}
	
	function combo() 
	{
		$this->load->model('Sppjb');
		$sppjb = new Sppjb();
		
		$sppjb->selectByParams();
		
		$i = 0;
		while($sppjb->nextRow())
		{
			$arr_json[$i]['id']		= $sppjb->getField("SPPJB_ID");
			$arr_json[$i]['text']	= $sppjb->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
