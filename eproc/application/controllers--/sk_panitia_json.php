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


class sk_panitia_json extends CI_Controller {

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
    	$this->USER_LOGIN_ID =  isset($this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID) ? $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID : '';
	}	
	
	function json_sk_panitia() 
	{
		$this->load->model("SKPanitia");
		$sk_panitia = new SKPanitia();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");
		
		$aColumns 			= array('UNIT_KERJA', 'SK_PANITIA_ID', 'NAMA_UNIT_KERJA', 'PEJABAT_PENETAP_NIP',  'TANGGAL', 'STATUS',  'SK_PANITIA_ID');
		$aColumnsAlias		= array('UNIT_KERJA', 'SK_PANITIA_ID', 'NAMA_UNIT_KERJA', 'PEJABAT_PENETAP_NIP',  'TANGGAL', 'STATUS',  'SK_PANITIA_ID');
		
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
					if (substr_compare(( $_GET['sSortDir_'.$i] ), "asc", 0) == 0)
					{
						$sOrder .=" asc, ";
					}else
					{
						$sOrder .=" desc, ";
					}
				}
			// echo $_GET['sSortDir_'.$i];
			}
			//Remove the last space / comma
			$sOrder = substr_replace( $sOrder, "", -2 );
			
			//Check if there is an order by clause
			if ( trim($sOrder) == "ORDER BY SK_PANITIA_ID desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY UNIT_KERJA ASC, TANGGAL DESC";
				 
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
				$getbS = isset($_GET['bSearchable_'.$i]) ? $_GET['bSearchable_'.$i] : '';
				//If current col has a search param
				if ( $getbS == "true" )
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
			$getbS = isset($_GET['bSearchable_'.$i]) ? $_GET['bSearchable_'.$i] : '';
			if ( $getbS == "true" && $_GET['sSearch_'.$i] != '' )
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
		
		$statement = "AND (UPPER(A.NO_SK) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(B.NAMA) LIKE '".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $sk_panitia->getCountByParams(array(), $statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $sk_panitia->getCountByParams(array(), $statement);

		$sk_panitia->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($sk_panitia->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				$sk_panitia_getdata = new SKPanitia();
				$sk_panitia_getdata->selectByParams(array('SK_PANITIA_ID' => $sk_panitia->getField('SK_PANITIA_ID')));
				$sk_panitia_getdata->firstRow();

				if ($sk_panitia_getdata->getField('FILE_SK')) {
					$fileSK = '<br><a target="_blank" href="uploads/lampiran/'.$sk_panitia_getdata->getField('PATH_FILE').'" class="badge badge-primary">Download Lampiran</a>';
				} else {
					$fileSK = '';
				}

				if($aColumns[$i]=='NO') {
					// $row[] = $number;
				}else if($aColumns[$i] == "PEJABAT_PENETAP_NIP")
				{ 
					$row[] = $sk_panitia_getdata->getField('PEJABAT_PENETAP').'<br> <small>NPP: <i><b>'.$sk_panitia->getField('PEJABAT_PENETAP_NIP').'</b></i></small>'.$fileSK;

				} elseif ($aColumns[$i]=='TANGGAL')
				{ 
					$row[] = 'SK: '.getFormattedDateJson($sk_panitia->getField(trim($aColumns[$i]))).'<br> Mulai: '.getFormattedDateJson($sk_panitia_getdata->getField('TANGGAL_MULAI')).'<br> Akhir: '.getFormattedDateJson($sk_panitia_getdata->getField('TANGGAL_AKHIR'));
				} elseif($aColumns[$i]=='STATUS') 
				{
					if( $sk_panitia->getField(trim($aColumns[$i])) == 1)	$st = '<span class="badge badge-info">Berlaku</span>';					
					else												$st = '<span class="badge badge-danger">Tidak Berlaku</span>';				
					$row[] = $st;
				} elseif($aColumns[$i]=='NAMA_UNIT_KERJA')	
				{
					$row[] = '<b>'.$sk_panitia->getField('UNIT_KERJA').'</b><br><small>No. : '.$sk_panitia_getdata->getField('NO_SK').'<br>'.$sk_panitia_getdata->getField('NAMA_UNIT_KERJA').'</small>';
				}elseif($aColumns[$i]=='UNIT_KERJA')	
				{
					$row[] = $sk_panitia->getField(trim($aColumns[$i]))."*".$sk_panitia->getField("SK_PANITIA_ID").'</small>';
				} else	
				{ 
					$row[] = $sk_panitia->getField(trim($aColumns[$i]));
				}
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function delete_sk_panitia()
	{
		$this->load->model("SKPanitia");
		$set= new SKPanitia();
		$arrayId=explode('*', $reqId);
		$set->setField('SK_PANITIA_ID', $arrayId[1]);
	
		if($set->delete())
			echo "Data berhasil dihapus";
		else
			echo "Data gagal dihapus";
		
		//echo "asd".$set->query;
	}
	
	function sk_panitia_add()
	{
		/* INCLUDE FILE */
		$this->load->model("SKPanitia");
		$this->load->model("UnitKerja");
		$this->load->model("Panitia");
		
		/* create objects */
		$sk_panitia = new SKPanitia();
		$unit_kerja = new UnitKerja();
		
		/* VARIABLE */
		$reqId				= $this->input->post("reqId");
		$reqUnit			= $this->input->post("reqUnit");
		$reqNomor			= $this->input->post("reqNomor");
		$reqTanggalSK		= $this->input->post("reqTanggalSK");
		$reqPejabat			= $this->input->post("reqPejabat");
		$reqNIPPejabat		= $this->input->post("reqNIPPejabat");
		$reqUnitKerja		= $this->input->post("reqUnitKerja");
		$reqStatus			= $this->input->post("reqStatus");
		$reqTanggalMulaiSK	= $this->input->post("reqTanggalMulaiSK");
		$reqTanggalSelesaiSK	= $this->input->post("reqTanggalSelesaiSK");
		$reqSubmit			= $this->input->post("reqSubmit");
		$reqMode			= $this->input->post('reqMode');
		$reqNip				= $this->input->post('reqNip');
		
		$reqNip			= $this->input->post("reqNip");
		$reqNama			= $this->input->post("reqNama");
		$reqNamaPanitia			= $this->input->post("reqNamaPanitia");
		$reqJabatanPanitia		= $this->input->post("reqJabatanPanitia");
		$reqStatusPanitia		= $this->input->post("reqStatusPanitia");
		$reqFungsiPanitia		= $this->input->post("reqFungsiPanitia");
		$reqStatusBerlaku		= $this->input->post("reqStatusBerlaku");
		
		$sk_panitia->setField("SK_PANITIA_ID", $reqId);
		$sk_panitia->setField("UNIT_KERJA",$reqUnit);	
		$sk_panitia->setField("UNIT_KERJA_ID",$reqUnitKerja);
		$sk_panitia->setField("TANGGAL",dateToDBCheck($reqTanggalSK));
		$sk_panitia->setField("PEJABAT_PENETAP",$reqPejabat);
		$sk_panitia->setField("PEJABAT_PENETAP_NIP",$reqNIPPejabat);
		$sk_panitia->setField("STATUS", $reqStatus);
		$sk_panitia->setField("TANGGAL_MULAI",dateToDBCheck($reqTanggalMulaiSK));
		$sk_panitia->setField("TANGGAL_AKHIR",dateToDBCheck($reqTanggalSelesaiSK));
		$sk_panitia->setField("NO_SK",$reqNomor);
		$sk_panitia->setField("AKTIF",$reqStatusBerlaku);
		
		/* ACTION BY REQMODE */
		if($reqMode == 'insert')
		{
			if($sk_panitia->insert())
			{
				$reqId = $sk_panitia->id;
				
				if ($reqNama) {
					for($i=0;$i<count($reqNama);$i++)
					{
						if($reqNama[$i]==''){}
						else{
							$panitia = new Panitia();
							$panitia->setField("SK_PANITIA_ID", $reqId);
							$panitia->setField("NIP", $reqNip[$i]);
							$panitia->setField("NAMA", $reqNamaPanitia[$i]);	
							$panitia->setField("JABATAN",  $reqJabatanPanitia[$i]);	
							$panitia->setField("STATUS", $reqStatusPanitia[$i]);
							$panitia->setField("KETUA", $reqFungsiPanitia[$i]);
							$panitia->insert();
							unset($panitia);
						}
					}
				}
			}
		}
		else
		{
			if($sk_panitia->update())
			{
				$panitia = new Panitia();
				$panitia->setField("SK_PANITIA_ID", $reqId);
				$panitia->deleteParent();
				for($i=0;$i<count($reqNama);$i++)
				{
					if($reqNama[$i]==''){}
					else{
						$panitia = new Panitia();
						$panitia->setField("SK_PANITIA_ID", $reqId);
						$panitia->setField("NIP", $reqNip[$i]);
						$panitia->setField("NAMA", $reqNamaPanitia[$i]);	
						$panitia->setField("JABATAN",  $reqJabatanPanitia[$i]);	
						$panitia->setField("STATUS", $reqStatusPanitia[$i]);
						$panitia->setField("KETUA", $reqFungsiPanitia[$i]);
						$panitia->insert();
						
						//echo $panitia->query;exit;
						unset($panitia);
					}
				}
			}
		}
		echo "Data berhasil disimpan.";
	}

	function uploadLampiran() 
	{

		$this->load->model("SKPanitia");
		$this->load->library("FileHandler");
		
		$SKPanitia = new SKPanitia();
		$file = new FileHandler();
		
		$reqId = $this->input->post("reqId");
		$reqLinkFile= $_FILES['reqLinkFile'];
		$FILE_DIR = "uploads/lampiran/";
		
		$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
		if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
		{
			$SKPanitia->setField("UPDATED_BY", $this->USER_LOGIN_ID);
			$SKPanitia->setField("SK_PANITIA_ID", $reqId);
			$SKPanitia->setField("FILE_SK", $reqLinkFile['name']);
			$SKPanitia->setField("PATH_FILE", $file->uploadedFileName);
			$SKPanitia->updateFile();
			// echo "Gambar/Foto berhasil diupload.";
		}
		else
			echo "Gambar/Foto gagal diupload.";
	}  

	function delete_file() 
	{

		$this->load->model("SKPanitia");
		
		$SKPanitia = new SKPanitia();
		
		$reqId = $this->input->get("reqId");

		$SKPanitia->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		$SKPanitia->setField("SK_PANITIA_ID", $reqId);
		$SKPanitia->setField("FILE_SK", '');
		$SKPanitia->setField("PATH_FILE", '');
		$SKPanitia->updateFile();
		
		echo "Lampiran berhasil di hapus.";
	}  
	
	function delete()
	{
		/* INCLUDE FILE */
		$this->load->model("SKPanitia");
		$this->load->model("Panitia");
		/* create objects */
		$sk_panitia = new SKPanitia();
		$panitia = new Panitia();
		
		/* VARIABLE */
		$reqId				= $this->input->get("reqId");
		
		$sk_panitia->setField("SK_PANITIA_ID", $reqId);
		
		if($sk_panitia->delete())
		{
			echo 'Data berhasil dihapus.';	
		} 
		else 
		{
			echo 'Data gagal dihapus.';	
		}	
		
	}
	
	
}
?>
