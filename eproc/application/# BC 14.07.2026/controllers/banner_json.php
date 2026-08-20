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

class Banner_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}       
		
		/* GLOBAL VARIABLE */
		//$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID; 
		
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;  
		$this->USER_LOGIN_ID =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_LOGIN =  $this->kauth->getInstance()->getIdentity()->USER_LOGIN;
		$this->USER_NAMA =  $this->kauth->getInstance()->getIdentity()->USER_NAMA;
		$this->USER_TYPE_ID =  $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->REKANAN_ID =  $this->kauth->getInstance()->getIdentity()->REKANAN_ID;
		$this->UNIT_KERJA_ID =  $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID;
		$this->NIP =  $this->kauth->getInstance()->getIdentity()->NIP;
		$this->LOGIN_TIME = $this->kauth->getInstance()->getIdentity()->LOGIN_TIME;
		$this->LOGIN_DATE = $this->kauth->getInstance()->getIdentity()->LOGIN_DATE;
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->REKANAN;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->REKANAN_KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->REKANAN_PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->REKANAN_NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;  
	}	
	
	function banner()
	{

		$this->load->model('Banner');
		$this->load->library("Pagination");
		
		$reqPage = $this->input->post("page");
		$reqPencarian = $this->input->post("search");
		$reqShow = $this->input->post("show");
		$reqContent = $this->input->post("content");
		$reqArrStatement = unserialized($this->input->post("array_serialized"));
		
		$banner = new Banner();
		if(isset($reqPage)){
			
			$dsplyStart = !empty($reqPage)?$reqPage:0;
			$dsplyRange = $reqShow;
			
			//get rows
			$rowCount = $banner->getCountByParams($reqArrStatement);
			if($reqPencarian == "")
				$rowCount = $rowCount;
			else	
				$rowCount =  $banner->getCountByParams($reqArrStatement, $statement);
						
			$banner->selectByParams($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
						
			//initialize pagination class
			$pagConfig = array('baseURL'=>'banner_json/banner', 'showRecord' => $reqShow, 'totalRows'=>$rowCount, 'currentPage'=>$dsplyStart, 'perPage'=>$dsplyRange, 'contentDiv'=>$reqContent, 'searchText' => $reqPencarian, 'arrSerialized' => $this->input->post("array_serialized"));
			$pagination =  new Pagination($pagConfig);
			
			if($rowCount > 0)
			{
				while($banner->nextRow())
				{
					$bannerId = $banner->getField("BANNER_ID");
				?>  
					<!-- <div class="list">
						<div class="tanggal"><span><?php //getFormattedDate($banner->getField("TANGGAL"))?></span></div>
						<div class="judul"><a href="main/index/berita_detil/?reqId=<?=$bannerId?>"><?php //$banner->getField("NAMA")?></a></div>
						<div class="isi">
							<?php // truncate($banner->getField("KETERANGAN"), 40)?>...
						</div>
					</div> -->
					<blockquote>
			          <p><h4><a href="main/index/banner_detil/?reqId=<?=$bannerId?>"><?=$banner->getField("NAMA")?></a> <small><?=getFormattedDate($banner->getField("TANGGAL"))?></small>
			          </h4></p>
			            <?php 
			            // truncate($banner->getField("KETERANGAN"), 40);
			            echo substr($banner->getField("KETERANGAN"), 0, 250);
			            ?>...
			        </blockquote> 
				<?php
				}  
				echo "<div>".$pagination->createLinks()."</div>";
			}
		}	
	}
	
	function json() 
	{
		$this->load->model("Banner");
		$banner = new Banner();
		
		$reqKeterangan = $this->input->post("reqKeterangan");
		$reqId = $this->input->get("reqId");
		$reqSearch = $this->input->post("reqSearch");
		$reqAgamaId = $this->input->post("reqAgamaId");
		
		$aColumns 			= array('BANNER_ID', 'NAMA', 'GAMBAR');
		$aColumnsAlias		= array('BANNER_ID', 'NAMA', 'GAMBAR');
		
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
			if ( trim($sOrder) == "ORDER BY BANNER_ID desc" )
			{
				/*
				* If there is no order by clause - ORDER BY INDEX COLUMN!!! DON'T DELETE IT!
				* If there is no order by clause there might be bugs in table display.
				* No order by clause means that the db is not responsible for the data ordering,
				* which means that the same row can be displayed in two pages - while
				* another row will not be displayed at all.
				*/
				$sOrder = " ORDER BY TANGGAL DESC ";
				 
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
		
		$statement = "AND (UPPER(NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $banner->getCountByParams(array(), $statement, $sOrder);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter =  $banner->getCountByParams(array(), $statement, $sOrder);

		$banner->selectByParams(array(), $dsplyRange, $dsplyStart, $statement, $sOrder);
		
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($banner->nextRow())		
		{		
			$row = array();		
			for ( $i=0 ; $i<count($aColumns) ; $i++ )		
			{	
				if($aColumns[$i]=='NO')		$row[] = $number;
					elseif($aColumns[$i]=='NAMA')
						$row[] = $banner->getField(trim($aColumns[$i])).'<br><span class="badge badge-primary" style="font-size:10px; padding: 3px 10px"><i class="fa fa-clock-o"></i> '.getFormattedDateJson($banner->getField(trim('TANGGAL'))).'</span>'; 
					elseif($aColumns[$i]=='STATUS'){
						if( $banner->getField(trim($aColumns[$i])) == 1)	$st = 'Berlaku';					
						else												$st = 'Tidak Berlaku';				
						$row[] = $st;
					}
					elseif($aColumns[$i]=='GAMBAR')	$row[] = "<img style='width:200px' src='".base_urL()."uploads/banner/".$banner->getField(trim($aColumns[$i]))."' data-action=\"zoom\">" ;
					else	$row[] = $banner->getField(trim($aColumns[$i]));
			}
			$output['aaData'][] = $row;
		}
		echo json_encode( $output );	
	}
	
	function add() 
	{
		$this->load->model('Banner');
		$this->load->library("FileHandler"); 
		$banner	= new Banner();
		$file = new FileHandler();
		
		$reqId		= $this->input->post('reqId');
		$reqMode	= $this->input->post('reqMode');
		
		$reqNama			= $this->input->post('reqNama');
		$reqTanggal			= date('Y-m-d H:i:s');
		
		$reqLinkFile			= $_FILES['reqLinkFile'];
		$reqLinkFileTemp = $this->input->post("reqLinkFileTemp");
		
		$FILE_DIR = "uploads/banner/";
		
		if($reqMode == "insert")
		{
			$banner	= new Banner();
			$banner->setField("BANNER_ID", $reqId);
			$banner->setField("NAMA", $reqNama); 
			
			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFile =  $renameFile;
			}
			else
			{
				$insertLinkFile =  $reqLinkFileTemp;
			}
			$banner->setField("GAMBAR", $insertLinkFile);
			
			// $banner->setField("TANGGAL", dateToDBCheck($reqTanggal));
			$banner->setField("TANGGAL", "'".$reqTanggal."'");
			$banner->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			
			$banner->insert();
			
		}
		else
		{
			$banner	= new Banner();
			$banner->setField("BANNER_ID", $reqId);
			$banner->setField("NAMA", $reqNama);  
			
			$renameFile = md5(date("dmYHis").$reqLinkFile['name'].$this->ID).".".getExtension($reqLinkFile['name']);
			if($file->uploadToDir('reqLinkFile', $FILE_DIR, $renameFile))
			{
				$insertLinkFile =  $renameFile;
			}
			else
			{
				$insertLinkFile =  $reqLinkFileTemp;
			}
			$banner->setField("GAMBAR", $insertLinkFile);
			
			// $banner->setField("TANGGAL", dateToDBCheck($reqTanggal));
			$banner->setField("TANGGAL", "'".$reqTanggal."'");
			$banner->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			$banner->update();
		}
		
		echo "Data berhasil disimpan.";
	}
	
	function delete() 
	{
		$this->load->model('Banner');
		
		$banner	= new Banner();
		
		$reqId		= $this->input->get('reqId');
		
		$reqNama		= $this->input->post('reqNama');
		
		$banner	= new Banner();
		$banner->setField("BANNER_ID", $reqId);
		$banner->delete();
		
		echo "Data berhasil disimpan.";
	}
	
	function combo() 
	{
		$this->load->model('Banner');
		$banner = new Banner();
		
		$banner->selectByParams();
		
		$i = 0;
		while($banner->nextRow())
		{
			$arr_json[$i]['id']		= $banner->getField("BANNER_ID");
			$arr_json[$i]['text']	= $banner->getField("NAMA");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
	
}
?>
