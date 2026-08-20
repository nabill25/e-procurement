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

class integrasi_aim_json extends CI_Controller {

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
	
	function project_reporting_add_data() 
	{
		$this->load->model("IntegrasiAIM");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$integrasi_aim = new IntegrasiAIM();
		
		$reqId = httpFilterPost("reqId");
		$reqRealisasiId = httpFilterPost("reqRealisasiId");
		$reqMode = httpFilterPost("reqMode");
		$reqTanggal = httpFilterPost("reqTanggal");
		$reqProgres = httpFilterPost("reqProgres");
		$reqKeterangan = httpFilterPost("reqKeterangan");
		$reqKendala = httpFilterPost("reqKendala");
		$reqLampiranLaporan = $_FILES["reqLampiranLaporan"];
		$reqLampiran = $_FILES["reqLampiran"];
		$reqLampiranTemp = $_POST["reqLampiranTemp"];
		$reqLampiranLaporanTemp = httpFilterPost("reqLampiranLaporanTemp");
		
							
		$integrasi_aim->setField('SUB_PROGRAM_SP2_REALISASI_ID', $reqRealisasiId);
		$integrasi_aim->setField('SUB_PROGRAM_SP2_ID', $reqId);
		$integrasi_aim->setField('PROGRES', $reqProgres);
		$integrasi_aim->setField('TANGGAL', dateToDBCheck($reqTanggal));
		$integrasi_aim->setField('NAMA', $reqNama);
		$integrasi_aim->setField('KETERANGAN', $reqKeterangan);
		$integrasi_aim->setField('KENDALA', $reqKendala);
		$integrasi_aim->setField('CREATED_BY', $userLogin->UID);
		$integrasi_aim->setField("CREATED_DATE", "SYSDATE");
		$integrasi_aim->setField('UPDATED_BY', $userLogin->UID);
		$integrasi_aim->setField("UPDATED_DATE", "SYSDATE");

		/* START UPLOAD FILE */
		$FILE_DIR = "uploads/pelaporan/";
		for($i=0;$i<count($reqLampiranLaporan);$i++)
		{	
			if($reqLampiranLaporan['name'][$i] == "")
			{}
			else			
			{
				$renameFile = md5(date("dmYHis").$reqLampiranLaporan['name'][$i]).".".getExtension($reqLampiranLaporan['name'][$i]);
				if (move_uploaded_file($reqLampiranLaporan['tmp_name'][$i], $FILE_DIR.$renameFile))
				{
					if($i == 0)	
						$insertLinkFile = $renameFile;
					else
						$insertLinkFile .= ",".$renameFile;
					
				}			
			}	
		}
		
		if($insertLinkFile == "")
			$insertLinkFile = $reqLampiranLaporanTemp;
			
		$integrasi_aim->setField("LINK_FILE", $insertLinkFile);


		$insertLinkFile = "";
		for($i=0;$i<count($reqLampiran);$i++)
		{	
			if($reqLampiran['name'][$i] == "")
			{}
			else			
			{
				$renameFile = md5(date("dmYHis").$reqLampiran['name'][$i]).".".getExtension($reqLampiran['name'][$i]);
				if (move_uploaded_file($reqLampiran['tmp_name'][$i], $FILE_DIR.$renameFile))
				{
					if($i == 0)	
						$insertLinkFile = $renameFile;
					else
						$insertLinkFile .= ",".$renameFile;
					
				}			
			}	
		}
		
		for($i=0;$i<count($reqLampiranTemp);$i++)
		{
			if($reqLampiranTemp[$i] == "")
			{}
			else
			{
				if($insertLinkFile == "")	
					$insertLinkFile = $reqLampiranTemp[$i];
				else
					$insertLinkFile .= ",".$reqLampiranTemp[$i];
			}
		}
			
		$integrasi_aim->setField("LINK_FOTO", $insertLinkFile);					
			
		if($reqMode == "insert")
		{	
			if($integrasi_aim->insertRealisasiKontrak())
			{
				$reqRealisasiId = $integrasi_aim->id;
				echo "Data berhasil disimpan.-".$reqId."-".$reqRealisasiId;
			}
		}
		else
		{
			if($integrasi_aim->updateRealisasiKontrak())
				echo "Data berhasil disimpan.-".$reqId."-".$reqRealisasiId;
		}
	}
	
	function project_reporting_monitoring_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("IntegrasiAIM");
		
		$integrasi_aim = new IntegrasiAIM();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		
		$reqStatus= httpFilterGet("reqStatus");
		
		$aColumns = array("SUB_PROGRAM_SP2_ID", "NAMA", "NOMOR", "NAMA_KONTRAK", "TANGGAL", "JANGKA_WAKTU", "NILAI_KONTRAK", "PROGRES_REKANAN", "PROGRES_VALIDASI");
		$aColumnsAlias = array("SUB_PROGRAM_SP2_ID", "NAMA", "NOMOR", "C.NAMA", "TANGGAL", "BERLAKU_HARI", "TOTAL", "D.PROGRES", "E.PROGRES");
		
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
					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 0)
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
			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";
				 
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
		 
		/* Individual column filtering */
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
		
		$statement  .= " AND A.REKANAN_ID_PEMENANG = '".$userLogin->userRekanan."' "; 
		$searchJson= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(C.NOMOR) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		
		$allRecord = $integrasi_aim->getCountByParamsRealisasiSP2(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $integrasi_aim->getCountByParamsRealisasiSP2(array(), $statement.$searchJson);
		
		$integrasi_aim->selectByParamsRealisasiSP2(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo $integrasi_aim->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($integrasi_aim->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($integrasi_aim->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "NILAI_KONTRAK")
					$row[] = "<div align='right'>".numberToIna($integrasi_aim->getField($aColumns[$i]))."</div>";					
				else
					$row[] = $integrasi_aim->getField($aColumns[$i]);
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
	function vendor_payment_add() 
	{

		$this->load->model("IntegrasiAIM");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$integrasi_aim = new IntegrasiAIM();

			
		$reqId = httpFilterPost("reqId");
		$reqTermin = httpFilterPost("reqTermin");
		$reqJumlahDokumen = httpFilterPost("reqJumlahDokumen");
		$reqMode = httpFilterPost("reqMode");

		/* CHECK APAKAH PERMOHONAN BAYAR SUDAH DI CREATE */
		$integrasi_aim->selectByParamsSubProgramBayar(array("A.SUB_PROGRAM_SP2_ID" => $reqId, "A.TERMIN" => $reqTermin));
		$integrasi_aim->firstRow();
		$reqSubProgramBayarId = $integrasi_aim->getField("SUB_PROGRAM_BAYAR_ID");
		
		/* JIKA TIDAK ADA DI INSERT */
		if($reqSubProgramBayarId == "")
		{
			$integrasi_aim->setField("SUB_PROGRAM_SP2_ID", $reqId);
			$integrasi_aim->setField("TERMIN", $reqTermin);
			$integrasi_aim->setField("CREATED_DATE", "SYSDATE");
			$integrasi_aim->setField("CREATED_BY", $userLogin->UID);
			$integrasi_aim->insertSubProgramBayar();
			$reqSubProgramBayarId = $integrasi_aim->id;
		}

		/* START UPLOAD FILE */
		$FILE_DIR = "uploads/penagihan/";
		for($i=1;$i<=$reqJumlahDokumen;$i++)
		{
			$integrasi_aim_upload = new IntegrasiAIM();
			
			$reqLampiran = $_FILES["reqLampiran".$i];
			$reqTerminSyaratId = $_POST["reqTerminSyaratId".$i];
			$reqNamaDokumen = $_POST["reqNamaDokumen".$i];
			if($reqLampiran['name'] == "")
			{}
			else			
			{
				$renameFile = md5(date("dmYHis").$reqLampiran['name']).".".getExtension($reqLampiran['name']);
				if (move_uploaded_file($reqLampiran['tmp_name'], $FILE_DIR.$renameFile))
				{
					$insertLinkFile = $renameFile;
				}	
			
				$integrasi_aim_upload->setField("SUB_PROGRAM_BAYAR_ID", $reqSubProgramBayarId);
				$integrasi_aim_upload->setField("SUB_PROGRAM_SP2_TERMIN_SYARAT", $reqTerminSyaratId);
				$integrasi_aim_upload->setField("NAMA", $reqNamaDokumen);
				$integrasi_aim_upload->setField("TANGGAL", "SYSDATE");
				$integrasi_aim_upload->setField("UPLOAD", $insertLinkFile);
				$integrasi_aim_upload->setField("CREATED_DATE", "SYSDATE");
				$integrasi_aim_upload->setField("CREATED_BY", $userLogin->UID);
				$integrasi_aim_upload->insertSubProgramBayarDokumen();
								
						
			}	

			unset($integrasi_aim_upload);
			unset($reqLampiran);
			unset($reqTerminSyaratId);
			unset($reqNamaDokumen);
			
		}
	}
	
	function delete()
	{
		$reqId = $this->input->get("reqId");	
		$this->load->model("IntegrasiAIM");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$integrasi_aim = new IntegrasiAIM();
		
		$integrasi_aim->setField("SUB_PROGRAM_BAYAR_DOKUMEN_ID", $reqId);
		$integrasi_aim->setField("CREATED_BY", $userLogin->UID);
		$integrasi_aim->deleteSubProgramBayarDokumen();
		echo "Data berhasil dihapus.";
	}
	
	function posting()
	{
		$reqId = httpFilterPost("reqId");
		$reqTermin = httpFilterPost("reqTermin");
		$reqRevisi = httpFilterPost("reqRevisi");
		
		$this->load->model("IntegrasiAIM");
		$this->load->library("kauth");  $userLogin = new kauth(); 
		
		$integrasi_aim = new IntegrasiAIM();

		/* CHECK APAKAH PERMOHONAN BAYAR SUDAH DI CREATE */
		$integrasi_aim->selectByParamsSubProgramBayar(array("A.SUB_PROGRAM_SP2_ID" => $reqId, "A.TERMIN" => $reqTermin));
		$integrasi_aim->firstRow();
		$reqSubProgramBayarId = $integrasi_aim->getField("SUB_PROGRAM_BAYAR_ID");
		if($reqSubProgramBayarId == "")
			echo "Dokumen belum diupload.";
		else
		{
			/* APABILA REVISI PERPAJAKAN (RP) MAKA LANGSUNG DIKIRIM KE PAJAK */
			if($reqRevisi == md5('RP'))
				$reqApproval = "PP";
			elseif($reqRevisi == md5('RK'))
				$reqApproval = "PK";				
			else
				$reqApproval = "PR";
						
			$integrasi_aim->setField("APPROVAL", $reqApproval);
			$integrasi_aim->setField("SUB_PROGRAM_BAYAR_ID", $reqSubProgramBayarId);
			$integrasi_aim->setField("CREATED_BY", $userLogin->UID);
			$integrasi_aim->postingSubProgramBayar();
			echo "Dokumen berhasil diposting.";
		}
	}
	
	function vendor_payment_monitoring_json()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("IntegrasiAIM");
		
		$integrasi_aim = new IntegrasiAIM();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		
		$reqStatus= httpFilterGet("reqStatus");
		
		$aColumns = array("SUB_PROGRAM_SP2_ID", "NAMA_KONTRAK", "KE", "NILAI_TAGIH", "KERJA_SYARAT", "PROGRES_VALIDASI", "STATUS");
		$aColumnsAlias = array("SUB_PROGRAM_SP2_ID", "NAMA_KONTRAK", "KE", "NILAI_TAGIH", "KERJA_SYARAT", "PROGRES_VALIDASI", "STATUS");
		
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
					if (strcasecmp(( $_GET['sSortDir_'.$i] ), "asc") == 0)
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
			if ( trim($sOrder) == "ORDER BY RECRUITMENT asc, RECRUITMENT asc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY A.RECRUITMENT_ID ASC, A.TANGGAL_AWAL_REN ASC";
				 
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
		 
		/* Individual column filtering */
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
		
		$statement  .= " AND A.REKANAN_ID_PEMENANG = '".$userLogin->userRekanan."' "; 
		$searchJson= " AND (UPPER(C.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(C.NOMOR) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		
		$allRecord = $integrasi_aim->getCountByParamsRealisasiPembayaran(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $integrasi_aim->getCountByParamsRealisasiPembayaran(array(), $statement.$searchJson);
		
		$integrasi_aim->selectByParamsRealisasiPembayaran(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo $integrasi_aim->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($integrasi_aim->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($integrasi_aim->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "NILAI_TAGIH")
					$row[] = "<div align='right'>".numberToIna($integrasi_aim->getField($aColumns[$i]))."</div>";					
				else
					$row[] = $integrasi_aim->getField($aColumns[$i]);
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}
	
}
?>
