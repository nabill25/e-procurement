<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/default.func.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class paket_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}       
		
		/* GLOBAL VARIABLE */
		$this->db->query("alter session set nls_date_format='YYYY-MM-DD'"); 	
		$this->db->query("alter session set nls_numeric_characters='.,'");   
		
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
		$this->REKANAN = $this->kauth->getInstance()->getIdentity()->NAMA;
		$this->REKANAN_KODE = $this->kauth->getInstance()->getIdentity()->KODE;
		$this->REKANAN_PKP = $this->kauth->getInstance()->getIdentity()->PKP;
		$this->REKANAN_NPWP = $this->kauth->getInstance()->getIdentity()->NPWP;
		$this->REKANAN_STATUS_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->STATUS_PERUSAHAAN;
		$this->REKANAN_STATUS_VALIDASI = $this->kauth->getInstance()->getIdentity()->STATUS_VALIDASI;
		
	}	
	
	function home()
	{

		$this->load->model('Paket');
		$this->load->library("Pagination");
		
		$reqPage = $this->input->post("page");
		$reqPencarian = $this->input->post("search");
		$reqShow = $this->input->post("show");
		$reqContent = $this->input->post("content");
		$reqArrStatement = unserialized($this->input->post("array_serialized"));
		
		$paket = new Paket();
		if(isset($reqPage)){
			
			$dsplyStart = !empty($reqPage)?$reqPage:0;
			$dsplyRange = $reqShow;
			
			//get rows
			$statement= " AND (UPPER(NAMA) LIKE '%".strtoupper($reqPencarian)."%') ";
			$rowCount = $paket->getCountByParams($reqArrStatement);
			if($reqPencarian == "")
				$rowCount = $rowCount;
			else	
				$rowCount =  $paket->getCountByParams($reqArrStatement, $statement);
						
			$paket->selectByParamsMonitoring($reqArrStatement, $dsplyRange, $dsplyStart, $statement);
						
			//initialize pagination class
			$pagConfig = array('baseURL'=>'paket_json/home', 'showRecord' => $reqShow, 'totalRows'=>$rowCount, 'currentPage'=>$dsplyStart, 'perPage'=>$dsplyRange, 'contentDiv'=>$reqContent, 'searchText' => $reqPencarian, 'arrSerialized' => $this->input->post("array_serialized"));
			$pagination =  new Pagination($pagConfig);
			
			if($rowCount > 0)
			{
				while($paket->nextRow())
				{  
					$status_keterangan = str_replace(" ", "-", $paket->getField("STATUS_KETERANGAN"));
					$status = $paket->getField("STATUS");				
				?>   
                    <div class="list">
                        <div class="waktu">
                            <div class="tanggal"><?=getDay($paket->getField("TANGGAL_TAHAP"))?></div>
                            <div class="bulan-tahun"><?=strtoupper(getExtMonth((int)getMonth($paket->getField("TANGGAL_TAHAP"))))?>.<?=getYear($paket->getField("TANGGAL_TAHAP"))?></div>
                        </div>
                        <div class="keterangan">
                            <div class="judul"><a href="main/index/paket_lelang_detil/?reqId=<?=$paket->getField("PAKET_ID")?>"><?=strtoupper($paket->getField("NAMA"))?></a></div>
                            <div class="lokasi">Lokasi : <?=$paket->getField("LOKASI")?></div>
                            <div class="batas-bawah"></div>
                        </div>
                    </div>    
				<? 
				}
				
				echo "<div>".$pagination->createLinks()."</div>";
			}
		}	
	}
	
	function paket_lelang()
	{

		$this->load->model('Paket');
		$this->load->library("Pagination");
		$this->load->model("PaketRekanan");
		
		$reqPage = $this->input->post("page");
		$reqPencarian = $this->input->post("search");
		$reqShow = $this->input->post("show");
		$reqContent = $this->input->post("content");
		$reqArrStatement = unserialized($this->input->post("array_serialized"));
		
		$paket = new Paket();
		if(isset($reqPage)){
			
			$dsplyStart = !empty($reqPage)?$reqPage:0;
			$dsplyRange = $reqShow;
			
			//get rows
			$statement= " AND (UPPER(NAMA) LIKE '%".strtoupper($reqPencarian)."%') ";
			if($this->USER_TYPE_ID == 6)
			{
				$rowCount = $paket->getCountByParamsPaketRekanan($reqArrStatement, $this->REKANAN_ID, $statement);
				$paket->selectByParamsPaketRekanan($reqArrStatement, $dsplyRange, $dsplyStart, $this->REKANAN_ID, $statement);  
			}
			elseif($this->USER_TYPE_ID == 8 || $this->USER_TYPE_ID == 9 || $this->USER_TYPE_ID == 10)
			{
				$rowCount = $paket->getCountByParamsPaketFungsional($reqArrStatement, $this->USER_LOGIN_ID, $statement);
				$paket->selectByParamsPaketFungsional($reqArrStatement, $dsplyRange, $dsplyStart, $this->USER_LOGIN_ID, $statement);  
			}
			else
			{
				$rowCount = $paket->getCountByParams($reqArrStatement, $this->REKANAN_ID, $statement);
				$paket->selectByParamsMonitoring($reqArrStatement, $dsplyRange, $dsplyStart, $statement);  
			}			
						
			//initialize pagination class
			$pagConfig = array('baseURL'=>'paket_json/paket_lelang', 'showRecord' => $reqShow, 'totalRows'=>$rowCount, 'currentPage'=>$dsplyStart, 'perPage'=>$dsplyRange, 'contentDiv'=>$reqContent, 'searchText' => $reqPencarian, 'arrSerialized' => $this->input->post("array_serialized"));
			$pagination =  new Pagination($pagConfig);
			
			if($rowCount > 0)
			{
				while($paket->nextRow())
				{  
					if(trim($paket->getField("ALASAN")) == "")
					  $batal = 0;
					else
					  $batal = 1;													
				?>          
					<tr>
						<td class="tgl">
							<div class="tgl"><?=getDay($paket->getField("TANGGAL_TAHAP"))?></div>
							<div class="bln-thn"><?=strtoupper(getExtMonth((int)getMonth($paket->getField("TANGGAL_TAHAP"))))?>.<?=getYear($paket->getField("TANGGAL_TAHAP"))?></div>
						</td>
						<td><?=$paket->getField("LOKASI")?></td>
						<td class="nama">
							<div class="nama-paket">
							<a href="main/index/paket_lelang_detil/?reqId=<?=$paket->getField("PAKET_ID")?>"><?=strtoupper($paket->getField("NAMA"))?></a>
							</div>
							<div id="ket-daftar">
								<? if($batal == 1) { ?>
								<div class="dibatalkan-diulang">(PAKET DIBATALKAN / DIULANG)</div>
								<? } ?>
							<?
							/* STATUS PENDAFTARAN REKANAN */
							if($this->USER_TYPE_ID == "6")
							{
								$paket_mengikuti1 = new Paket();
								$mengikuti = $paket_mengikuti1->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
								$pendaftaran = 0;
								if($mengikuti == 0)
								{
										$paket_pendaftaran1 = new Paket();
										$pendaftaran = $paket_pendaftaran1->getPaketPendaftaran($paket->getField("PAKET_ID"));
								}
								$validasi = 0;
								if($mengikuti == 1)
								{
									echo "<div class=\"dapat\">Anda telah mendaftar paket ini</div>";
									$validasi = 1;
								}
								elseif($pendaftaran == 0)
									echo "<div class=\"tdk-dapat\">Anda tidak dapat mendaftar paket ini. Waktu pendaftaran belum dimulai atau sudah berakhir</div>";
							}
							?>
							</div>
							<?
							/* STATUS PEMBUAT PAKET PANITIA */
							if($this->USER_TYPE_ID == 3)
							{
							?>
							<div id="pembuat-paket">Pembuat Paket : <strong><?=$paket->getField("USER_LOGIN")?></strong></div>
							<?
							}
							else
							{
								$pendaftaran = 0;
								$paket_mengikuti = new Paket();
								$mengikuti = $paket_mengikuti->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
								if($mengikuti == 0)
								{
									$paket_pendaftaran = new Paket();
									$pendaftaran = $paket_pendaftaran->getPaketPendaftaran($paket->getField("PAKET_ID"));
									if($pendaftaran == 1 && ($paket->getField("PAKET_METODE_LELANG_ID") == 1 || $paket->getField("PAKET_METODE_LELANG_ID") == 3 || $paket->getField("PAKET_METODE_LELANG_ID") == 4)) 
									{
										if($this->USER_LOGIN_ID == "")
										{
										?>
										<?
										}
									}
								}
								else
								{
									/* jika sudah mengikuti cek apakah gagal */
									$paket_rekanan_lulus = new PaketRekanan();
									$lulus_pendaftaran = $paket_rekanan_lulus->getLulusPendaftaran($this->REKANAN_ID, $paket->getField("PAKET_ID"));	
									if($lulus_pendaftaran == "0")
									{
										$paket_pendaftaran = new Paket();
										$pendaftaran = $paket_pendaftaran->getPaketPendaftaran($paket->getField("PAKET_ID"));	
									}
								}
							}
							?>                                                        
						</td>
						<td><? if(trim($paket->getField("BIDANG_USAHA")) == "()") echo "-"; else echo str_replace(", (",", <br/>(", $paket->getField("BIDANG_USAHA"));?></td>
						<?
						/* CENTANG PUBLISH PAKET */
						if((int)$this->USER_TYPE_ID == 3)
						{
						?>
						<td align="center">
							<?
							if((int)$this->USER_TYPE_ID == 3 && $paket->getField("USER_LOGIN_ID") == $this->USER_LOGIN_ID && $paket->getField("PAKET_METODE_LELANG_ID") == "1")
							{
							?>
							<input type="checkbox" name="reqPublish" id="reqPublish<?=$paket->getField("PAKET_ID")?>" onclick="updatePublishPaket('<?=$paket->getField("PAKET_ID")?>')" <? if($paket->getField("PUBLISH_PAKET") == 1) { ?>  checked="checked" <? } ?> />
							<?
							}
							?>
						</td>
						<?
						}
						?>
						<?
						/* TOMBOL PENDAFTARAN PAKET OLEH REKANAN */
						if($this->USER_TYPE_ID == 6){
						?>
						<td style="display:table-cell; vertical-align:middle;">
							<div class="area-aksi-paket-lelang">
							
							<?
							if($this->USER_TYPE_ID == 6 && $pendaftaran == 1)
							{
							  $mengikuti = $paket_mengikuti->getPaketMengikuti($this->REKANAN_ID, $paket->getField("PAKET_ID"));
							  if($mengikuti == 1)
							  {
									if($lulus_pendaftaran == "0")
									{
									?>
									  <!--<div style="margin-bottom:10px; text-align:center">-->
									  <div class="tidak-memenuhi-syarat"><?=translate("Anda tidak memenuhi syarat pendaftaran, untuk melengkapi klik tombol daftar ulang.", "You are not eligible for registration, please click the button below to re-registration")?></div>
									  <div align="center">   
									  <a href="main/index/registrasi_paket/?reqPaketId=<?=md5($this->REKANAN_ID.$paket->getField("PAKET_ID"))?>" class="btn-daftar-ulang"><?=translate("Daftar Ulang", "Re-registration")?></a>
									  </div>
									<?	
									}
							  }
							  else{
								  ?>
								  <div align="center">   
								  <a href="main/index/registrasi_paket/?reqPaketId=<?=md5($this->REKANAN_ID.$paket->getField("PAKET_ID"))?>" class="btn-daftar"><?=translate("Daftar", "Register")?></a>
								  </div>
								  <?
							  }
							}
							?>
							</div>
						</td>
						<?
						}
						?>
						
					</tr>
				<? 
				}
				
				?>
                <tr>
                <td colspan="6">
                    <?=$pagination->createLinks()?> 
                </td>
                </tr>                
                <?
			}
		}	
		
	}

	function rekapitulasi_pekerjaan_monitoring_json() 
	{
		$this->load->model("Paket");
		
		$paket = new Paket();
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
		}
		
		ini_set("memory_limit","500M");
		ini_set('max_execution_time', 5200);
		
		$reqTahun= $this->input->get("reqTahun");
		
		if($reqTahun == "")
			$reqTahun = date("Y");
		
		$aColumns = array("NAMA_PEKERJAAN", "BULAN", "PR", "PO", "PIC", "METODE_KUALIFIKASI", "JENIS_PEKERJAAN", "METODE_PEKERJAAN", "KETERANGAN", "DIREKTORAT", "SUBDIT", "NAMA_PEJABAT", "NILAI_OE", "NILAI_PENAWARAN", "NILAI_NEGOSIASI", "EFISIENSI", "PERSEN_OE", "PELAKSANA", "TANGGAL_NID", "HUKUM", "KETERANGAN2");
		$aColumnsAlias = array("NAMA_PEKERJAAN", "BULAN", "PR", "PO", "PIC", "METODE_KUALIFIKASI", "JENIS_PEKERJAAN", "METODE_PEKERJAAN", "KETERANGAN", "DIREKTORAT", "SUBDIT", "NAMA_PEJABAT", "NILAI_OE", "NILAI_PENAWARAN", "NILAI_NEGOSIASI", "EFISIENSI", "PERSEN_OE", "PELAKSANA", "TANGGAL_NID", "HUKUM", "KETERANGAN2");
		
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
		
		
		$statement = " AND TO_CHAR(A.TANGGAL, 'YYYY') = '".$reqTahun."' ";
				
		
		$searchJson= " AND (UPPER(A.NAMA) LIKE '%".strtoupper($_GET['sSearch'])."%' OR UPPER(PR_GROUP_NUMBER) LIKE '%".strtoupper($_GET['sSearch'])."%')";
		$allRecord = $paket->getCountByParams(array(),$statement);
		if($_GET['sSearch'] == "")
			$allRecordFilter = $allRecord;
		else	
			$allRecordFilter = $paket->getCountByParams(array(), $statement.$searchJson);
		
		$paket->selectByParamsPaketPekerjaanLaporan(array(), $dsplyRange, $dsplyStart, $statement.$searchJson, $sOrder);
		//echo $paket->query;
		/* Output */
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $allRecord,
			"iTotalDisplayRecords" => $allRecordFilter,
			"aaData" => array()
		);
		
		while($paket->nextRow())
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if($aColumns[$i] == "BULAN")
					$row[] = getNameMonth((int)$paket->getField($aColumns[$i]));				
				else if(substr($aColumns[$i], 0,5) == "NILAI")
					$row[] = numberToIna($paket->getField($aColumns[$i]));
				else if($aColumns[$i] == "KETERANGAN")
					$row[] = truncate($paket->getField($aColumns[$i]), 5)."...";
				else if($aColumns[$i] == "STATUS_KETERANGAN")
					$row[] = "<div align='center'>".$paket->getField($aColumns[$i])."</div>";					
				else
					$row[] = $paket->getField($aColumns[$i]);
			}
			
			$output['aaData'][] = $row;
		}
		
		echo json_encode( $output );
	}

	function daftar()
	{
		
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Paket");
		$this->load->model("RekananTenagaAhli");
		$this->load->model("RekananPajak");
		$this->load->model("RekananPeralatan");
		$this->load->model("RekananSertifikat");
		$this->load->model("BidangUsaha");
		$this->load->model("RekananBidangUsaha");
		$this->load->model("Rekanan");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananNeraca");
		$this->load->model("Users");
		$this->load->model("IjinUsaha");
		$this->load->model("RekananAkta");
		$this->load->model("RekananPengurus");
		$this->load->model("RekananDaftarPengalaman");
		$this->load->model("RekananDaftarTenagaAhli");
		$this->load->model("RekananDaftarPeralatan");
		$this->load->model("RekananDaftarSertifikat");
		$this->load->model("RekananRekeningKoran");
		$this->load->model("PaketEvaluasiSyaratDaftar");
		$this->load->model("PaketRekananDaftar");
		$this->load->model("PaketRekanan");
		
		/* create objects */
		$rekanan = new Rekanan();
		$rekanan_tenaga_ahli = new RekananTenagaAhli();
		$rekanan_sertifikat = new RekananSertifikat();
		$rekanan_peralatan = new RekananPeralatan();
		$rekanan_ijin = new RekananIjinUsaha();
		$rekanan_pengurus = new RekananPengurus();
		$ijin_usaha = new IjinUsaha();
		$rekanan_akta = new RekananAkta();
		$user_login = new Users();
		$paket = new Paket();
		$paket_getid = new Paket();
		$paket_pengalaman = new Paket();
		$paket_tampil = new Paket();
		$rekanan_pkp 	= new Rekanan(); // tipe ?
		$rekanan_daftar_pengalaman = new RekananDaftarPengalaman();
		$rekanan_daftar_tenaga_ahli = new RekananDaftarTenagaAhli();
		$rekanan_daftar_peralatan = new RekananDaftarPeralatan();
		$rekanan_daftar_sertifikat = new RekananDaftarSertifikat();
		$paket_rekanan_daftar = new PaketRekananDaftar();
		
		
		$reqPaketId = $this->input->post("reqPaketId");
		
		$reqPaketId = $paket_getid->getPaketId(array("MD5('".$this->ID."' || A.PAKET_ID)" => $reqPaketId));

		$reqJenisPerusahaan= $this->input->post("reqJenisPerusahaan");
		$reqNamaPerusahaan= $this->input->post("reqNamaPerusahaan");
		$reqAlamat= $this->input->post("reqAlamat");
		$reqKota= $this->input->post("reqKota");
		$reqStatus= $this->input->post("reqStatus");
		$reqNPWP= $this->input->post("reqNPWP");
		$reqKodeTelepon= $this->input->post("reqKodeTelepon");
		$reqNomorTelepon= $this->input->post("reqNomorTelepon");
		$reqKodeFax= $this->input->post("reqKodeFax");
		$reqNomorFax= $this->input->post("reqNomorFax");
		$reqEmail= $this->input->post("reqEmail");
		$reqNomorIjinUsaha= $this->input->post("reqNomorIjinUsaha");
		$reqMasaBerlaku= $this->input->post("reqMasaBerlaku");
		$reqKualifikasi= $this->input->post("reqKualifikasi");
		$reqKualifikasi= $this->input->post("reqKualifikasi");
		$reqPimpinan= $this->input->post("reqPimpinan");
		$reqJabatan= $this->input->post("reqJabatan");
		$reqAkte= $this->input->post("reqAkte");
		$reqAkteTanggal= $this->input->post("reqAkteTanggal");
		$reqSuratKuasaNomor= $this->input->post("reqSuratKuasaNomor");
		$reqSuratKuasaTanggal= $this->input->post("reqSuratKuasaTanggal");
		$reqSetuju= $this->input->post("reqSetuju");
		$reqKirim= $this->input->post("reqKirim");
		$reqSubmit= $this->input->post("reqSubmit");
		$reqCaptcha= $this->input->post("reqCaptcha");
		$reqCaptchatemp= $this->input->post("reqCaptchatemp");
		$reqIjinUsaha= $this->input->post("reqIjinUsaha");
		$reqNotaris= $this->input->post("reqNotaris");
		$reqAkte2= $this->input->post("reqAkte2");
		$reqAkteTanggal2= $this->input->post("reqAkteTanggal2");
		$reqNotaris2= $this->input->post("reqNotaris2");
		$reqNotaris3= $this->input->post("reqNotaris3");
		$reqRekananId = $_POST["reqRekananId"];
		$reqPengalamanSyarat = $_POST["reqPengalamanSyarat"];
		$reqTenagaAhliSyarat = $_POST["reqTenagaAhliSyarat"];
		$reqPeralatanSyarat = $_POST["reqPeralatanSyarat"];
		$reqSertifikatSyarat = $_POST["reqSertifikatSyarat"];
		
		//rekening koran
		$paketInfo->getPaket($reqPaketId);
		
		if($reqKirim == 'Simpan') 
		{	
		
			for($i=0;$i<count($reqPengalamanSyarat);$i++)
			{
				if($reqPengalamanSyarat[$i] == "")
				{}
				else
				{
					$rekanan_daftar_pengalaman = new RekananDaftarPengalaman();
					$ada = $rekanan_daftar_pengalaman->getCountByParams(array("PAKET_ID" => $reqPaketId, 
																	   "REKANAN_ID" => $this->ID,
																	   "REKANAN_PENGALAMAN_ID" => $reqPengalamanSyarat[$i]));
					if($ada == 0)
					{
						$rekanan_daftar_pengalaman->setField("PAKET_ID", $reqPaketId);
						$rekanan_daftar_pengalaman->setField("REKANAN_ID", $this->ID);
						$rekanan_daftar_pengalaman->setField("REKANAN_PENGALAMAN_ID", $reqPengalamanSyarat[$i]);
						$rekanan_daftar_pengalaman->insert();
					}
					unset($rekanan_daftar_pengalaman);
				}
			}
		
			for($i=0;$i<count($reqTenagaAhliSyarat);$i++)
			{
				if($reqTenagaAhliSyarat[$i] == "")
				{}
				else
				{
					$rekanan_daftar_tenaga_ahli = new RekananDaftarTenagaAhli();
					$ada = $rekanan_daftar_tenaga_ahli->getCountByParams(array("PAKET_ID" => $reqPaketId, 
																	   "REKANAN_ID" => $this->ID,
																	   "REKANAN_TENAGA_AHLI_ID" => $reqTenagaAhliSyarat[$i]));
					if($ada == 0)
					{
						$rekanan_daftar_tenaga_ahli->setField("PAKET_ID", $reqPaketId);
						$rekanan_daftar_tenaga_ahli->setField("REKANAN_ID", $this->ID);
						$rekanan_daftar_tenaga_ahli->setField("REKANAN_TENAGA_AHLI_ID", $reqTenagaAhliSyarat[$i]);
						$rekanan_daftar_tenaga_ahli->insert();
					}
					unset($rekanan_daftar_tenaga_ahli);
				}
			}
		
			for($i=0;$i<count($reqPeralatanSyarat);$i++)
			{
				if($reqPeralatanSyarat[$i] == "")
				{}
				else
				{
					$rekanan_daftar_peralatan = new RekananDaftarPeralatan();
					$ada = $rekanan_daftar_peralatan->getCountByParams(array("PAKET_ID" => $reqPaketId, 
																	   "REKANAN_ID" => $this->ID,
																	   "REKANAN_PERALATAN_ID" => $reqPeralatanSyarat[$i]));
					if($ada == 0)
					{
						$rekanan_daftar_peralatan->setField("PAKET_ID", $reqPaketId);
						$rekanan_daftar_peralatan->setField("REKANAN_ID", $this->ID);
						$rekanan_daftar_peralatan->setField("REKANAN_PERALATAN_ID", $reqPeralatanSyarat[$i]);
						$rekanan_daftar_peralatan->insert();
					}
					unset($rekanan_daftar_peralatan);
				}
			}
		
			for($i=0;$i<count($reqSertifikatSyarat);$i++)
			{
				if($reqSertifikatSyarat[$i] == "")
				{}
				else
				{
					$rekanan_daftar_sertifikat = new RekananDaftarSertifikat();
					$ada = $rekanan_daftar_sertifikat->getCountByParams(array("PAKET_ID" => $reqPaketId, 
																	   "REKANAN_ID" => $this->ID,
																	   "REKANAN_SERTIFIKAT_ID" => $reqSertifikatSyarat[$i]));
					if($ada == 0)
					{
						$rekanan_daftar_sertifikat->setField("PAKET_ID", $reqPaketId);
						$rekanan_daftar_sertifikat->setField("REKANAN_ID", $this->ID);
						$rekanan_daftar_sertifikat->setField("REKANAN_SERTIFIKAT_ID", $reqSertifikatSyarat[$i]);
						$rekanan_daftar_sertifikat->insert();
					}
					unset($rekanan_daftar_sertifikat);
				}
			}
						
			$rekanan_daftar_pengalaman = new RekananDaftarPengalaman();
			$rekanan_daftar_tenaga_ahli = new RekananDaftarTenagaAhli();
			$rekanan_daftar_peralatan = new RekananDaftarPeralatan();
			$rekanan_daftar_sertifikat = new RekananDaftarSertifikat();
			
			$paket_rekanan = new PaketRekanan();
			$check = $paket_rekanan->getCountByParams(array("REKANAN_ID" => $this->ID, "PAKET_ID" => $reqPaketId));
			
			
			if($check == 1)
			{
				$paket_rekanan->setField("REKANAN_ID", $this->ID);
				$paket_rekanan->setField("PAKET_ID", $reqPaketId);
				$paket_rekanan->setField("KODE_REKANAN", date("ymd"));	
				$paket_rekanan->updateDaftar();
			}
			else
			{
				$paket_rekanan->setField("REKANAN_ID", $this->ID);
				$paket_rekanan->setField("PAKET_ID", $reqPaketId);
				$paket_rekanan->setField("KODE_REKANAN",date("ymd"));	
				$paket_rekanan->insertDaftar();		
			}	
			
			$status = "2";
			
			echo "Registrasi paket berhasil.";
		}

	}
		
	function set_publish_evaluasi()
	{
		$this->load->model("Paket");		
		$this->load->model("PaketEvaluasiValidasi");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
		
		/* VALIDASI PEMBUKAAN PENAWARAN */
		$paket_evaluasi_validasi = new PaketEvaluasiValidasi();
		$jumlahValidasi = $paket_evaluasi_validasi->getCountByParams(array("A.PAKET_ID" => $reqId));

		if($jumlahValidasi == 0)
		{
			echo "Minimal terdapat 1(satu) validasi dari panitia.";			
			return;
		}
		
		$paket->setField("PAKET_ID", $reqId);
		$paket->publishBAEvaluasi();
		
		echo "1";
	}
	
	function set_publish_kualifikasi()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Paket");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
		
		$paket->setField("PAKET_ID", $reqId);
		$paket->publishBAKualifikasi();
		$met[0]['STATUS'] = 1;
		echo json_encode($met);
	}
	
	function set_publish_negosiasi()
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Paket");
		$this->load->model("PaketNegosiasiValidasi");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");

		$paketInfo->getPaket($reqId);
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "index.php";';
				echo '</script>';
				exit;		
			}
		}

		
		if($paketInfo->jenis_pengadaan == "LELANG")
		{
			/* VALIDASI PEMBUKAAN PENAWARAN */
			$paket_negosiasi_validasi = new PaketNegosiasiValidasi();
			$jumlahValidasi = $paket_negosiasi_validasi->getCountByParams(array("A.PAKET_ID" => $reqId));
			
			if($jumlahValidasi == 0)
			{
				$arrFinal = array("STATUS" => "Minimal terdapat 1(satu) validasi dari panitia.");			
				echo json_encode($arrFinal);		
				return;
			}
		}
				
		$paket->setField("PAKET_ID", $reqId);
		$paket->publishBANegosiasi();
		$arrFinal = array("STATUS" => "1");			
		echo json_encode($arrFinal);	
	}
	
	function set_publish_paket()
	{
		$this->load->model("Paket");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
	
		$paket->setField("FIELD", "PUBLISH_PAKET");
		$paket->setField("FIELD_VALUE", "(SELECT DECODE(COALESCE(PUBLISH_PAKET, 0), 0, 1, 0) FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID)");
		$paket->setField("PAKET_ID", $reqId);
		if($paket->updateByField())
			echo "Paket berhasil di-publish.";
		else
			echo "Paket gagal di-publish.";
		
	}
	
	function set_publish_pembukaan()
	{
		$this->load->model("Paket");		
		$this->load->model("PaketPembukaanValidasi");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
		
		/* VALIDASI PEMBUKAAN PENAWARAN */
		$paket_pembukaan_validasi = new PaketPembukaanValidasi();
		$jumlahValidasi = $paket_pembukaan_validasi->getCountByParams(array("A.PAKET_ID" => $reqId));

		if($jumlahValidasi == 0)
		{			
			echo "Minimal terdapat 1(satu) validasi dari panitia.";		
			return;
		}
		
		$paket->setField("PAKET_ID", $reqId);
		$paket->publishBAPenawaran();
		echo "1";
	}
	
	function ulang() 
	{
		error_reporting(0);
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Paket");		
		$this->load->model("PaketPembukaanValidasi");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "index.php";';
				echo '</script>';
				exit;		
			}
		}
		
		/* VALIDASI PEMBUKAAN PENAWARAN */
		$paket_pembukaan_validasi = new PaketPembukaanValidasi();
		$jumlahValidasi = $paket_pembukaan_validasi->getCountByParams(array("A.PAKET_ID" => $reqId));

		if($jumlahValidasi == 0)
		{
			$arrFinal = array("STATUS" => "Minimal terdapat 1(satu) validasi dari panitia.");			
			echo json_encode($arrFinal);		
			return;
		}
		
		$paket->setField("PAKET_ID", $reqId);
		$paket->publishBALelangUlang();
		$arrFinal = array("STATUS" => "1");			
		echo json_encode($arrFinal);		
	}
	
	function validasi() 
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Paket");		
		$this->load->model("PaketPembukaanValidasi");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
		
		/* LOGIN CHECK */
		if ($userLogin->checkUserLogin()) 
		{ 
			$userLogin->retrieveUserInfo();
			if($userLogin->userLevel == 6)
			{
				echo '<script language="javascript">';
				echo 'alert("Anda tidak berhak mengakses halaman ini. IP address anda telah kami catat sebagai rekanan yang mencoba membuka halaman administrator.");';
				echo 'top.location.href = "index.php";';
				echo '</script>';
				exit;		
			}
		}
		
		/* VALIDASI PEMBUKAAN PENAWARAN */
		$paket_pembukaan_validasi = new PaketPembukaanValidasi();
		$jumlahValidasi = $paket_pembukaan_validasi->getCountByParams(array("A.PAKET_ID" => $reqId));

		if($jumlahValidasi == 0)
		{
			$arrFinal = array("STATUS" => "Minimal terdapat 1(satu) validasi dari panitia.");			
			echo json_encode($arrFinal);		
			return;
		}
		
		$arrFinal = array("STATUS" => "1");			
		echo json_encode($arrFinal);		
	}
	
	function set_publish_pembukaan_sampul2() 
	{
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Paket");		
		$this->load->model("PaketPembukaanKeduaValidasi");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
		
		/* VALIDASI PEMBUKAAN PENAWARAN */
		$paket_pembukaan_validasi = new PaketPembukaanKeduaValidasi();
		$jumlahValidasi = $paket_pembukaan_validasi->getCountByParams(array("A.PAKET_ID" => $reqId));

		if($jumlahValidasi == 0)
		{
			echo "Minimal terdapat 1(satu) validasi dari panitia.";			
			echo json_encode($arrFinal);		
			return;
		}
		
		$paket->setField("PAKET_ID", $reqId);
		$paket->publishBAPenawaran2();
		echo "1";
			
	}
	
	function add()
	{
		$this->load->model("Paket");
		$this->load->model("PaketBidangUsaha");
		$this->load->model("PermohonanPaket");
		$this->load->model("PaketKriteria");
		$this->load->model("PaketPihakLain");
		$this->load->model("PaketJenis");
		$this->load->model("PaketPenawaran");
		$this->load->model("Metode");
		$this->load->model("UnitKerja");
		$this->load->model("RekananKualifikasi");
		$this->load->model("BidangUsaha");
		
		$paket = new Paket();
		$paket_kriteria = new PaketKriteria();
		$paket_pihak_lain = new PaketPihakLain();
		$paket_penawaran = new PaketPenawaran();
		
		$reqId = $this->input->post("reqId");
		$reqNamaPaket = $this->input->post("reqNamaPaket");
		$reqBidangUsaha = $this->input->post("reqBidangUsaha");
		$reqUraianKegiatan = $_POST["reqUraianKegiatan"];
		$reqNilaiPekerjaan = $this->input->post("reqNilaiPekerjaan");
		$reqLokasiPekerjaan = $this->input->post("reqLokasiPekerjaan");
		$reqJenisPekerjaan = $this->input->post("reqJenisPekerjaan");
		$reqMetodePengadaan = $this->input->post("reqMetodePengadaan");
		$reqMetodeKualifikasi = $this->input->post("reqMetodeKualifikasi");
		$reqMetodeEvaluasi = $this->input->post("reqMetodeEvaluasi");
		$reqMetodePenyampulan = $this->input->post("reqMetodePenyampulan");
		$reqBahasa = $this->input->post("reqBahasa");
		$reqKualifikasiRekanan = $this->input->post("reqKualifikasiRekanan");
		$reqAlamatPanitia = $this->input->post("reqAlamatPanitia");
		$reqTelpPanitiaKode = $this->input->post("reqTelpPanitiaKode");
		$reqTelpPanitia = $this->input->post("reqTelpPanitia");
		$reqEmailPanitia = $this->input->post("reqEmailPanitia");
		$reqSimpan = $this->input->post("reqSimpan");
		$reqMetodePengadaanRekomendasiId = $this->input->post("reqMetodePengadaanRekomendasiId");
		$reqPrNumber = $this->input->post("reqPrNumber");
		$reqPermohonanUserLogin = $this->input->post("reqPermohonanUserLogin");
		$reqBidangUsahaId = $_POST["reqBidangUsahaId"];
		$reqMataUang = $this->input->post("reqMataUang");
		
		if($reqId == "")	
		{
			
			/* METODE PENYAMPULAN HANYA BERLAKU UNTUK LELANG TERBUKA DAN PEMILIHAN TERBATAS */
			if($reqMetodePengadaan == "1" || $reqMetodePengadaan == "2")
			{}
			else
				$reqMetodePenyampulan = "1";
			
			$paket->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
			$paket->setField("PAKET_METODE_LELANG_ID", $reqMetodePengadaan);
			$paket->setField("PAKET_METODE_KUALIFIKASI_ID", $reqMetodeKualifikasi);
			$paket->setField("PAKET_METODE_EVALUASI_ID", $reqMetodeEvaluasi);
			$paket->setField("PAKET_JENIS_ID", $reqJenisPekerjaan);
			$paket->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			$paket->setField("REKANAN_KUALIFIKASI_ID", $reqKualifikasiRekanan);
			$paket->setField("NAMA", $reqNamaPaket);
			$paket->setField("URAIAN", $reqUraianKegiatan);
			$paket->setField("LOKASI", $reqLokasiPekerjaan);
			$paket->setField("ALAMAT", $reqAlamatPanitia);
			$paket->setField("TELEPON", $reqTelpPanitiaKode." ".$reqTelpPanitia);
			$paket->setField("EMAIL", $reqEmailPanitia);
			$paket->setField("PUBLISH_PAKET", 0);
			$paket->setField("PUBLISH_PEMENANG", 0);
			$paket->setField("NILAI", CommaToDot(dotToNo($reqNilaiPekerjaan)));
			$paket->setField("NILAI_OWNER_ESTIMATE", CommaToDot(dotToNo($reqNilaiPekerjaan)));
			$paket->setField('UNIT_KERJA_ID', $this->UNIT_KERJA_ID);
			$paket->setField("PR_GROUP_NUMBER", $reqPrNumber);
			$paket->setField("SISTEM_SAMPUL", $reqMetodePenyampulan);
			$paket->setField("BAHASA", $reqBahasa);
			$paket->setField("NILAI_MATA_UANG", $reqMataUang);
			
			if($paket->insert())
			{
				$idPaket = $paket->id;
				
				$paket_penawaran = new PaketPenawaran();
				$paket_penawaran->setField("PAKET_ID", $idPaket);
				$paket_penawaran->setField("NAMA", $reqNamaPaket);
				$paket_penawaran->setField("LAST_CREATE_USER", $this->USER_LOGIN_ID);
				$paket_penawaran->setField("JUMLAH", CommaToDot(dotToNo($reqNilaiPekerjaan)));
				$paket_penawaran->insertPaket();
				
				/* APABILA PERMOHONAN OTOMASIS ISIKAN KE PAKET PIHAK LAIN */
				if($reqPermohonanId == "") {}
				else
				{
					$paket_pihak_lain->setField("USER_LOGIN_ID", $reqPermohonanUserLogin);
					$paket_pihak_lain->setField("STATUS", 1);
					$paket_pihak_lain->setField("PAKET_ID", $idPaket);
					$paket_pihak_lain->insert();
				}
				
				for($i=0; $i<count($reqBidangUsahaId);$i++)
				{
					$paketBidangUsaha = new PaketBidangUsaha();			
					$paketBidangUsaha->setField('PAKET_ID', $idPaket);
					$paketBidangUsaha->setField('BIDANG_USAHA_ID', $reqBidangUsahaId[$i]);		
					$paketBidangUsaha->insert();
					unset($paketBidangUsaha);
				}
				
				$paket_kriteria->setField('PAKET_ID', $idPaket);
				$paket_kriteria->insertPaketEvaluasiKD();
				$paket_kriteria->insertPaketEvaluasiKeuangan();
				$paket_kriteria->insertPaketEvaluasiPengalaman();
				$paket_kriteria->insertPaketEvaluasiPeralatan();
				$paket_kriteria->insertPaketKriteriaEvaluasi();
				
				echo $idPaket;
			}
			else
				$alertMsg .= "Data Gagal Tersimpan";	
		}
		else
		{
		
			/* METODE PENYAMPULAN HANYA BERLAKU UNTUK LELANG TERBUKA DAN PEMILIHAN TERBATAS */
			if($reqMetodePengadaan == "1" || $reqMetodePengadaan == "2")
			{}
			else
				$reqMetodePenyampulan = "1";
					
			$paket->setField("PERMOHONAN_PAKET_ID", $reqPermohonanId);
			$paket->setField("PAKET_ID", $reqId);
			$paket->setField("PAKET_METODE_LELANG_ID", $reqMetodePengadaan);
			$paket->setField("PAKET_METODE_KUALIFIKASI_ID", $reqMetodeKualifikasi);
			$paket->setField("PAKET_METODE_EVALUASI_ID", $reqMetodeEvaluasi);
			$paket->setField("PAKET_JENIS_ID", $reqJenisPekerjaan);
			$paket->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
			$paket->setField("REKANAN_KUALIFIKASI_ID", $reqKualifikasiRekanan);
			$paket->setField("NAMA", $reqNamaPaket);
			$paket->setField("URAIAN", $reqUraianKegiatan);
			$paket->setField("LOKASI", $reqLokasiPekerjaan);
			$paket->setField("ALAMAT", $reqAlamatPanitia);
			$paket->setField("TELEPON", $reqTelpPanitiaKode." ".$reqTelpPanitia);
			$paket->setField("EMAIL", $reqEmailPanitia);
			$paket->setField("NILAI", CommaToDot(dotToNo($reqNilaiPekerjaan)));
			$paket->setField("NILAI_OWNER_ESTIMATE", CommaToDot(dotToNo($reqNilaiPekerjaan)));
			$paket->setField('UNIT_KERJA_ID', $userLogin->unitKerjaId);
			$paket->setField("PR_GROUP_NUMBER", $reqPrNumber);
			$paket->setField("SISTEM_SAMPUL", $reqMetodePenyampulan);
			$paket->setField("BAHASA", $reqBahasa);
			$paket->setField("NILAI_MATA_UANG", $reqMataUang);
				
			if($paket->update())
			{
				$idPaket = $reqId;
					
				$paketBidangUsaha = new PaketBidangUsaha();					
				$paketBidangUsaha->setField('PAKET_ID', $idPaket);
				$paketBidangUsaha->delete();
				unset($paketBidangUsaha);
		
				for($i=0; $i<count($reqBidangUsahaId);$i++)
				{
					$paketBidangUsaha = new PaketBidangUsaha();			
					$paketBidangUsaha->setField('PAKET_ID', $idPaket);
					$paketBidangUsaha->setField('BIDANG_USAHA_ID', $reqBidangUsahaId[$i]);		
					$paketBidangUsaha->insert();
					unset($paketBidangUsaha);
				}
				
				echo $idPaket;

			}
			else
				$alertMsg .= "Data Gagal Tersimpan";
		}
		
	}
	
	function penentuan_pemenang()
	{
		
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("PaketRekanan");
		$this->load->model("Paket");
		
		$paket_rekanan = new PaketRekanan();
		$reqMode = $this->input->post("reqMode");
		$reqId = $this->input->post("reqId");
		$reqPemenang= $this->input->post('reqPemenang');
		$reqNegosiasi= $this->input->post('reqNegosiasi');
		$reqTanggal= $this->input->post('reqTanggal');
		$submitSimpan= $this->input->post('submitSimpan');
		
		if($submitSimpan == "Simpan")
		{
			$paket = new Paket();
			$paket->setField("NILAI_NEGOSIASI", dotToNo($reqNegosiasi));
			$paket->setField("TANGGAL_PENGUMUMAN_PEMENANG", dateToDBCheck($reqTanggal));
			$paket->setField("REKANAN_ID_PEMENANG", $reqPemenang);
			$paket->setField("PAKET_ID", $reqId);
			if($paket->updatePemenang())
			{
				echo "Data berhasil di Update";	
			}
		}
		
	}

	function kriteria_kualifikasi()
	{
		
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Paket");
		$this->load->model("PaketEvaluasiAdmin");
		$this->load->model("PaketEvaluasiKeuangan");
		$this->load->model("PaketKriteriaEvaluasi");
		$this->load->model("PaketEvaluasiKemampuanDasar");
		$this->load->model("PaketEvaluasiPengalaman");
		$this->load->model("PaketEvaluasiPersonil");
		$this->load->model("PaketEvaluasiSertifikatLain");
		$this->load->model("PaketEvaluasiPeralatan");
		$this->load->model("PaketEvaluasiPeralatanDetil");
		
		$paket = new Paket();
		$paket_evaluasi_admin = new PaketEvaluasiAdmin();
		$paket_evaluasi_keuangan = new PaketEvaluasiKeuangan();
		$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();
		$paket_evaluasi_kemampuan_dasar = new PaketEvaluasiKemampuanDasar();
		$paket_evaluasi_pengalaman = new PaketEvaluasiPengalaman();
		$paket_evaluasi_personil = new PaketEvaluasiPersonil();
		$paket_evaluasi_peralatan = new PaketEvaluasiPeralatan();
		$paket_evaluasi_peralatan_detil = new PaketEvaluasiPeralatanDetil();
		$paket_evaluasi_sertifikat_lain = new PaketEvaluasiSertifikatLain();
		
		$reqCheck = $_POST["reqCheck"];
		$reqEvaluasiAdministrasi = $_POST["reqEvaluasiAdministrasi"];
		
		/* PAKET_KRITERIA_EVAL */
		$reqSKKPilih = $this->input->post("reqSKKPilih");
		$reqSaldoPilih = $this->input->post("reqSaldoPilih");
		$reqKDPilih = $this->input->post("reqKDPilih");
		$reqBPPilih = $this->input->post("reqBPPilih");
		$reqNKPilih = $this->input->post("reqNKPilih");
		$reqSTPilih = $this->input->post("reqSTPilih");
		$reqPersonilPilih = $this->input->post("reqPersonilPilih");
		$reqPeralatanPilih = $this->input->post("reqPeralatanPilih");
		$reqSertifikatPilih = $this->input->post("reqSertifikatPilih");
		
		$reqKDPekerjaan = $this->input->post("reqKDPekerjaan");
		$reqKDNilaiMinimum = $this->input->post("reqKDNilaiMinimum");
		$reqKDPengalaman = $this->input->post("reqKDPengalaman");
		$reqJumlahPengalamanA = $this->input->post("reqJumlahPengalamanA"); 
		$reqProsentasePengalamanA = $this->input->post("reqProsentasePengalamanA");
		$reqJumlahPengalamanB = $this->input->post("reqJumlahPengalamanB"); 
		$reqProsentasePengalamanB = $this->input->post("reqProsentasePengalamanB");
		$reqJumlahPengalamanC = $this->input->post("reqJumlahPengalamanC"); 
		$reqProsentasePengalamanC = $this->input->post("reqProsentasePengalamanC");
		$reqJumlahPengalamanD = $this->input->post("reqJumlahPengalamanD"); 
		$reqProsentasePengalamanD = $this->input->post("reqProsentasePengalamanD");
		
		$reqBPNilai = $this->input->post("reqBPNilai");
		$reqBPSama = $this->input->post("reqBPSama");
		$reqBPBeda = $this->input->post("reqBPBeda");
		
		$reqNKNilai = $this->input->post("reqNKNilai");
		$reqNKBesarPersen = $this->input->post("reqNKBesarPersen");
		$reqNKSedangPersen = $this->input->post("reqNKSedangPersen");
		$reqNKKecilPersen = $this->input->post("reqNKKecilPersen");
		
		$reqSTJasaNilai = $this->input->post("reqSTJasaNilai");
		$reqSTKontraktorNilai = $this->input->post("reqSTKontraktorNilai");
		$reqSTSubKontraktorNilai = $this->input->post("reqSTSubKontraktorNilai");
		$reqSTNilaiMinimum = $this->input->post("reqSTNilaiMinimum");
		$reqSTNilaiMaksimum = $this->input->post("reqSTNilaiMaksimum");
		
		$reqPersonilNilaiMinimum = $this->input->post("reqPersonilNilaiMinimum");
		
		$reqPeralatanMSB = $this->input->post("reqPeralatanMSB");
		$reqPeralatanSPJB = $this->input->post("reqPeralatanSPJB");
		$reqPeralatanSPDB = $this->input->post("reqPeralatanSPDB");
		$reqPeralatanNilai = $this->input->post("reqPeralatanNilai");
		
		$reqSertifikatNilai = $this->input->post("reqSertifikatNilai");
		
		$reqSKKNilaiMaksimum = $this->input->post("reqSKKNilaiMaksimum");
		$reqSKKTinggi = $this->input->post("reqSKKTinggi");
		$reqSKKSedang = $this->input->post("reqSKKSedang");
		$reqSKKRendah = $this->input->post("reqSKKRendah");
		$reqSKKNilaiMinimum = $this->input->post("reqSKKNilaiMinimum");
		$reqSaldoBulan = $this->input->post("reqSaldoBulan"); 
		$reqSaldoMinimal = $this->input->post("reqSaldoMinimal"); 
		$reqBPProsentase = $this->input->post("reqBPProsentase"); 
		$reqNKProsentase = $this->input->post("reqNKProsentase"); 
		$reqSTJasaProsentase = $this->input->post("reqSTJasaProsentase"); 
		
		$submitSimpan = $this->input->post("submitSimpan");
		$reqId = $this->input->post("reqId");
		
		$reqPersonilKualifikasi = $_POST["reqPersonilKualifikasi"];
		$reqPendidikan = $_POST["reqPendidikan"];
		$reqPersonilPengalaman = $_POST["reqPersonilPengalaman"];
		$reqPersonilJumlah = $_POST["reqPersonilJumlah"];
		$reqPersonilSKA = $_POST["reqPersonilSKA"];
		$reqPersonilCV = $_POST["reqPersonilCV"];
		$reqPersonilNilai = $_POST["reqPersonilNilai"];
		$reqPersonilId = $_POST["reqPersonilId"];
		
		$reqPeralatanId = $_POST["reqPeralatanId"];
		$reqPeralatanNama = $_POST["reqPeralatanNama"];
		$reqPeralatanKeterangan = $_POST["reqPeralatanKeterangan"];
		$reqPeralatanDetilNilai = $_POST["reqPeralatanDetilNilai"];
		
		$reqSertifikatId = $_POST["reqSertifikatId"];
		$reqSertifikatNama = $_POST["reqSertifikatNama"];
		$reqSertifikatKeterangan = $_POST["reqSertifikatKeterangan"];
		$reqSertifikatDetilNilai = $_POST["reqSertifikatDetilNilai"];
		
		$reqPassingGrade = $this->input->post("reqPassingGrade");
		
		/*update tanggal 12-10-2012*/
		$reqPersonilMinimum= $this->input->post("reqPersonilMinimum");
		$reqPeralatanNilaiMinimum= $this->input->post("reqPeralatanNilaiMinimum");
		$reqSertifikatlainNilaiMinimum= $this->input->post("reqSertifikatlainNilaiMinimum");
		
		$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;
		$reqKualifikasi = $paketInfo->kualifikasi;
		$reqKualifikasiId = $paketInfo->kualifikasi_id;
		$reqNilai = $paketInfo->nilai;
		$reqPaketJenis = $paketInfo->jenis_id;
		$reqTahun = getYear($paketInfo->tanggal_pemasukan);
		$reqBulan = (int)getMonth($paketInfo->tanggal_pemasukan);
		
		$paket_evaluasi_keuangan->selectByParams(array("PAKET_ID" => $reqId));
		$paket_evaluasi_keuangan->firstRow();
		$paket_evaluasi_pengalaman->selectByParams(array("PAKET_ID" => $reqId));
		$paket_evaluasi_pengalaman->firstRow();
		
		$simpan = true;
		/* VALIDASI NILAI */
		//NILAI KUALIFIKASI
		if($reqSKKPilih == 1)
			$arrNilai[] = $reqSKKNilaiMaksimum;
		if($reqPersonilPilih == 1)
		{
			$arrNilai[] = $reqPersonilNilaiMinimum;
			$nilai_personil = "ABC-".array_sum($reqPersonilNilai); 
			if($nilai_personil == "ABC-100")
			{}
			else
			{
				echo "Prosentase penilaian personil harus 100%, saat ini ".array_sum($reqPersonilNilai)."%. Silahkan cek kembali.";		
				return;
			}
		}
		if($reqPeralatanPilih == 1)
		{
			$arrNilai[] = $reqPeralatanNilai;
			$nilai_alat = "ABC-".array_sum($reqPeralatanDetilNilai); 
			if($nilai_alat == "ABC-100")
			{}
			else
			{
				echo "Prosentase peralatan harus 100%, saat ini ".array_sum($reqPeralatanDetilNilai)."%. Silahkan cek kembali.'";		
				return;
			}	
		}
		if($reqSertifikatPilih == 1)
		{
			$arrNilai[] = $reqSertifikatNilai;
			$nilai_sertifikat = "ABC-".array_sum($reqSertifikatDetilNilai); 
			if($nilai_sertifikat == "ABC-100")
			{}
			else
			{
				echo "Prosentase sertifikat harus 100%, saat ini ".array_sum($reqSertifikatDetilNilai)."%. Silahkan cek kembali.";		
				return;
			}	
		}
		//NILAI PENGALAMAN
		/**/
		if($reqBPPilih == 1)
			$arrPengalamanNilai[] = $reqBPNilai;
		if($reqNKPilih == 1)
			$arrPengalamanNilai[] = $reqNKNilai;	
		if($reqSTPilih == 1)
			$arrPengalamanNilai[] = $reqSTJasaNilai;
		
		if($reqSKKPilih == 1 || $reqBPPilih == 1 || $reqNKPilih == 1 || $reqSTPilih == 1 || $reqPersonilPilih == 1 || $reqPeralatanPilih == 1 || $reqSertifikatPilih == 1)
		{
			$nilai = array_sum($arrNilai) + $reqSTNilaiMaksimum;
			if($nilai == 100)
			{}
			else
			{
				echo "Total Nilai Evaluasi = ".$nilai.", <br> kurang/melebihi 100. Silahkan cek kembali.";		
				return;
			}
		}
		
		if($reqBPPilih == 1 || $reqNKPilih == 1 || $reqSTPilih == 1)
		{
			//$nilai_pengalaman = array_sum($arrPengalamanNilai);
			//if($nilai_pengalaman == $reqSTNilaiMaksimum)
			$validasiPengalamanProsentase = 0;
			if($reqBPPilih == 1)
				$validasiPengalamanProsentase += $reqBPProsentase;
			if($reqNKPilih == 1)
				$validasiPengalamanProsentase += $reqNKProsentase;	
			if($reqSTPilih == 1)
				$validasiPengalamanProsentase += $reqSTJasaProsentase;
			
			$nilai_pengalaman = $validasiPengalamanProsentase;//$reqBPProsentase + $reqNKProsentase + $reqSTJasaProsentase;
			if("ABC-".$nilai_pengalaman == "ABC-100")
			{}
			else
			{
				echo "Prosentase nilai pengalaman harus 100%, saat ini ".$nilai_pengalaman."%. Silahkan cek kembali.";
				return;
			}		
		}
		
		//VARIABLE
			$dimPengaliSKK = 0.2;
			$dimPengaliPengalaman = 0.5;
			$dimFP = 6;
			$dimFPB = 8;
			$dimFL =  0.3;
			$dimFLB = 0.8;
			
		//PERHITUNGAN SEGMENTASI SKK
		if($paket_evaluasi_keuangan->getField("SKK2RPMIN") != '' 
			and $paket_evaluasi_keuangan->getField("SKK2RPMIN")==$paket_evaluasi_keuangan->getField("SKK3RP")
			and $paket_evaluasi_keuangan->getField("SKK1RP")== $reqNilai)
			$nilaiSkkHitung =$paket_evaluasi_keuangan->getField("SKK2RPMIN");
		else
			$nilaiSkkHitung = $reqNilai * $dimPengaliSKK;
		
		//PERHITUNGAN SEGMENTASI PENGALAMAN
		if($paket_evaluasi_pengalaman->getField("NK2_RPMIN") != '' 
		   and $paket_evaluasi_pengalaman->getField("NK2_RPMIN") == $paket_evaluasi_pengalaman->getField("NK3_RP")
		   and $paket_evaluasi_pengalaman->getField("NK1_RP")==$reqNilai)
			$nilaiNKHitung =$paket_evaluasi_pengalaman->getField("NK2_RPMIN");
		else
			$nilaiNKHitung = $reqNilai * $dimPengaliPengalaman;
		
		if($submitSimpan == "Simpan")
		{
				/* PAKET EVALUASI ADMIN */
			$paket_evaluasi_admin->setField("PAKET_ID", $reqId);
			$paket_evaluasi_admin->delete();
			for($i=1;$i<=count($reqEvaluasiAdministrasi);$i++)
			{
				if($reqCheck[$i] == 1)
				{
					
					$paket_evaluasi_admin_insert = new PaketEvaluasiAdmin();	
					if($reqEvaluasiAdministrasi[$i] == "")
					{}
					else
					{
						$paket_evaluasi_admin_insert->setField("PAKET_ID", $reqId);
						$paket_evaluasi_admin_insert->setField("NAMA", $reqEvaluasiAdministrasi[$i]);
						$paket_evaluasi_admin_insert->setField("EVALUASI_NUMBER", $i);	
						$paket_evaluasi_admin_insert->insert();
					}
					unset($paket_evaluasi_admin_insert);
				}
			}	
		
			/* PAKET EVALUASI KEUANGAN */
			$paket_evaluasi_keuangan_u = new PaketEvaluasiKeuangan();
			$paket_evaluasi_keuangan_u->setField("PAKET_ID", $reqId);
			$paket_evaluasi_keuangan_u->setField("FP", $dimFP);
			$paket_evaluasi_keuangan_u->setField("FL", $dimFL);
			$paket_evaluasi_keuangan_u->setField("FPB", $dimFPB);
			$paket_evaluasi_keuangan_u->setField("FLB", $dimFLB);
			$paket_evaluasi_keuangan_u->setField("SKK1RP", floatval($reqNilai));
			$paket_evaluasi_keuangan_u->setField("SKK1PERSEN", floatval($reqSKKTinggi)); //aim: RITM0000254
			$paket_evaluasi_keuangan_u->setField("SKK1NILAI", floatval($reqSKKNilaiMaksimum)); 
			$paket_evaluasi_keuangan_u->setField("SKK2RPMIN", floatval($nilaiSkkHitung));
			$paket_evaluasi_keuangan_u->setField("SKK2RPMAX", floatval($reqNilai));
			$paket_evaluasi_keuangan_u->setField("SKK2PERSEN", floatval($reqSKKSedang)); //aim: RITM0000254
			$paket_evaluasi_keuangan_u->setField("SKK3RP", floatval($nilaiSkkHitung));
			$paket_evaluasi_keuangan_u->setField("SKK3PERSEN", floatval($reqSKKRendah)); //aim: RITM0000254
			$paket_evaluasi_keuangan_u->setField("NILAI_LULUS", floatval($reqSKKNilaiMinimum));
			$paket_evaluasi_keuangan_u->setField("REKENING_BULAN", $reqSaldoBulan);
			$paket_evaluasi_keuangan_u->setField("SALDO_REK_MIN", dotToNo($reqSaldoMinimal));
			$paket_evaluasi_keuangan_u->update();
		    $paket_evaluasi_keuangan->selectByParams(array("PAKET_ID" => $reqId));
			$paket_evaluasi_keuangan->firstRow();
		
			
			/* PAKET_KRITERIA_EVAL */
			$paket_kriteria_evaluasi->setField("PAKET_ID", $reqId);
			$paket_kriteria_evaluasi->setField("SKK", (int)$reqSKKPilih);
			$paket_kriteria_evaluasi->setField("SALDO", (int)$reqSaldoPilih);
			$paket_kriteria_evaluasi->setField("KEMAMPUAN_DASAR", (int)$reqKDPilih);
			$paket_kriteria_evaluasi->setField("BIDANG_KERJA", (int)$reqBPPilih);
			$paket_kriteria_evaluasi->setField("NILAI_KONTRAK", (int)$reqNKPilih);
			$paket_kriteria_evaluasi->setField("STATUS_PENYEDIA", (int)$reqSTPilih);
			$paket_kriteria_evaluasi->setField("PERSONIL", (int)$reqPersonilPilih);
			$paket_kriteria_evaluasi->setField("PERALATAN", (int)$reqPeralatanPilih);
			$paket_kriteria_evaluasi->setField("SERTIFIKAT_LAIN", (int)$reqSertifikatPilih);
			$paket_kriteria_evaluasi->update();
		
			/* PAKET_EVAL_KD  */
			$paket_evaluasi_kemampuan_dasar->setField("PAKET_ID", $reqId);
			$paket_evaluasi_kemampuan_dasar->setField("NILAI_KONTRAK_MIN", dotToNo($reqKDNilaiMinimum));
			$paket_evaluasi_kemampuan_dasar->setField("PEKERJAAN", $reqKDPekerjaan);
			$paket_evaluasi_kemampuan_dasar->setField("PENGALAMAN_TAHUN", $reqKDPengalaman);
			$paket_evaluasi_kemampuan_dasar->update();
			
			/* PAKET_EVAL_PENGALAMAN */
			$paket_evaluasi_pengalaman_u = new PaketEvaluasiPengalaman();
			$paket_evaluasi_pengalaman_u->setField("PAKET_ID", $reqId);
			
			$paket_evaluasi_pengalaman_u->setField("NK_NILAI_PROSENTASE", $reqNKProsentase);
			$paket_evaluasi_pengalaman_u->setField("BP_NILAI_PROSENTASE", $reqBPProsentase);
			$paket_evaluasi_pengalaman_u->setField("STBU_NILAI_PROSENTASE", $reqSTJasaProsentase);
			
			$reqBPNilai	 	= round(($reqSTNilaiMaksimum * $reqBPProsentase) / 100, 2);
			$reqNKNilai 	= round(($reqSTNilaiMaksimum * $reqNKProsentase) / 100, 2);
			$reqSTJasaNilai = round(($reqSTNilaiMaksimum * $reqSTJasaProsentase) / 100, 2);
			
			$paket_evaluasi_pengalaman_u->setField("BP_NILAI", $reqBPNilai);
			$paket_evaluasi_pengalaman_u->setField("NK_NILAI", $reqNKNilai);
			$paket_evaluasi_pengalaman_u->setField("STBU_NILAI", $reqSTJasaNilai);
			
			$paket_evaluasi_pengalaman_u->setField("BP_SUB_SAMA_PERSEN", floatval($reqBPSama)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("BP_SUB_BEDA_PERSEN", floatval($reqBPBeda)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("NK1_RP", $reqNilai);
			$paket_evaluasi_pengalaman_u->setField("NK1_PERSEN", floatval($reqNKBesarPersen)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("NK2_RPMIN", $nilaiNKHitung);
			$paket_evaluasi_pengalaman_u->setField("NK2_RPMAX", $reqNilai);
			$paket_evaluasi_pengalaman_u->setField("NK2_PERSEN", floatval($reqNKSedangPersen)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("NK3_RP", $nilaiNKHitung);
			$paket_evaluasi_pengalaman_u->setField("NK3_PERSEN", floatval($reqNKKecilPersen)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("STBU_UTAMA_PERSEN", floatval($reqSTKontraktorNilai)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("STBU_SUB_PERSEN", floatval($reqSTSubKontraktorNilai)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("NILAI_MINIMAL", floatval($reqSTNilaiMinimum)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("NILAI_MAKSIMUM", floatval($reqSTNilaiMaksimum)); //aim: RITM0000254
			
			$paket_evaluasi_pengalaman_u->setField("JUMLAH_PENGALAMAN_A", (int)$reqJumlahPengalamanA);
			$paket_evaluasi_pengalaman_u->setField("PROSENTASE_PENGALAMAN_A", floatval($reqProsentasePengalamanA)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("JUMLAH_PENGALAMAN_B", (int)$reqJumlahPengalamanB);
			$paket_evaluasi_pengalaman_u->setField("PROSENTASE_PENGALAMAN_B", floatval($reqProsentasePengalamanB)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("JUMLAH_PENGALAMAN_C", (int)$reqJumlahPengalamanC);
			$paket_evaluasi_pengalaman_u->setField("PROSENTASE_PENGALAMAN_C", floatval($reqProsentasePengalamanC)); //aim: RITM0000254
			$paket_evaluasi_pengalaman_u->setField("JUMLAH_PENGALAMAN_D", (int)$reqJumlahPengalamanD);
			$paket_evaluasi_pengalaman_u->setField("PROSENTASE_PENGALAMAN_D", floatval($reqProsentasePengalamanD)); //aim: RITM0000254
			
			
			$paket_evaluasi_pengalaman_u->update();
			//refresh data
			$paket_evaluasi_pengalaman->selectByParams(array("PAKET_ID" => $reqId));
			$paket_evaluasi_pengalaman->firstRow();
		
			/* PAKET_EVAL_PERALATAN */ 
			$paket_evaluasi_peralatan->setField("PAKET_ID", $reqId);
			$paket_evaluasi_peralatan->setField("MSB", floatval($reqPeralatanMSB)); //aim: RITM0000254
			$paket_evaluasi_peralatan->setField("SPJB", floatval($reqPeralatanSPJB)); //aim: RITM0000254
			$paket_evaluasi_peralatan->setField("SPDB", floatval($reqPeralatanSPDB)); //aim: RITM0000254
			$paket_evaluasi_peralatan->setField("NILAI_MINIMUM", floatval($reqPeralatanNilai)); //aim: RITM0000254
			
			/*update tanggal 12-10-2012*/
			$paket_evaluasi_peralatan->setField("NILAI_MINIMAL", floatval($reqPeralatanNilaiMinimum)); //aim: RITM0000254
			$paket_evaluasi_peralatan->update();
		
			
			/* PAKET_EVAL_PERSONIL */
			$notDeleteString = '';
			//$ada_evaluasi_personil = false;
			for($i=0;$i<count($reqPersonilKualifikasi);$i++)
			{
				if($reqPersonilId[$i] == "")
				{
					if($reqPersonilNilaiMinimum and !empty($reqPersonilJumlah[$i]) and !empty($reqPersonilNilai[$i]))
					{
							$paket_evaluasi_personil_insert = new PaketEvaluasiPersonil();	
							$paket_evaluasi_personil_insert->setField("PAKET_ID", $reqId);
							$paket_evaluasi_personil_insert->setField("JABATAN", $reqPersonilKualifikasi[$i]);
							$paket_evaluasi_personil_insert->setField("PENDIDIKAN", $reqPendidikan[$i]);
							$paket_evaluasi_personil_insert->setField("PENGALAMAN", $reqPersonilPengalaman[$i]);
							$paket_evaluasi_personil_insert->setField("JUMLAH", $reqPersonilJumlah[$i]);
							$paket_evaluasi_personil_insert->setField("NILAI", $reqPersonilNilai[$i]);
							$paket_evaluasi_personil_insert->setField("NILAI_MINIMUM", $reqPersonilNilaiMinimum);
							
							/*update tanggal 12-10-2012*/
							$paket_evaluasi_personil_insert->setField("NILAI_MINIMAL", $reqPersonilMinimum);
							
							$paket_evaluasi_personil_insert->setField("SKA", (int)$reqPersonilSKA[$i]);
							$paket_evaluasi_personil_insert->setField("CV", (int)$reqPersonilCV[$i]);
							$paket_evaluasi_personil_insert->insert();
							//echo $paket_evaluasi_personil_insert->query;
							$notDeleteString .= "'".$paket_evaluasi_personil_insert->getField('PAKET_EVAL_PERSONIL_ID')."',";
							unset($paket_evaluasi_personil_insert);
						
					}
				}
				else
				{
					$paket_evaluasi_personil_insert = new PaketEvaluasiPersonil();	
					$paket_evaluasi_personil_insert->setField("PAKET_EVAL_PERSONIL_ID", $reqPersonilId[$i]);
					$paket_evaluasi_personil_insert->setField("JABATAN", $reqPersonilKualifikasi[$i]);
					$paket_evaluasi_personil_insert->setField("PENDIDIKAN", $reqPendidikan[$i]);
					$paket_evaluasi_personil_insert->setField("PENGALAMAN", $reqPersonilPengalaman[$i]);
					$paket_evaluasi_personil_insert->setField("JUMLAH", $reqPersonilJumlah[$i]);
					$paket_evaluasi_personil_insert->setField("NILAI", $reqPersonilNilai[$i]);
					$paket_evaluasi_personil_insert->setField("NILAI_MINIMUM", $reqPersonilNilaiMinimum);
					
					/*update tanggal 12-10-2012*/
					$paket_evaluasi_personil_insert->setField("NILAI_MINIMAL", $reqPersonilMinimum);
					
					$paket_evaluasi_personil_insert->setField("SKA", (int)$reqPersonilSKA[$i]);
					$paket_evaluasi_personil_insert->setField("CV", (int)$reqPersonilCV[$i]);
					$paket_evaluasi_personil_insert->updateData();
					$notDeleteString .= "'".$reqPersonilId[$i]."',";
					unset($paket_evaluasi_personil_insert);
				}
			}
				
				//DELETE EXCEPT INSERTED AND UPDATED
				$pepd = new PaketEvaluasiPersonil();
				$pepd->setField("PAKET_EVAL_PERSONIL_ID", rtrim($notDeleteString, ','));
				$pepd->setField("PAKET_ID", $reqId);
				$pepd->deleteIn();
				
			$not_peralatan = "0";
			for($i=0;$i<count($reqPeralatanNama);$i++)
			{
				if($reqPeralatanNama[$i] == "")
				{}
				else
				{
					if($reqPeralatanId[$i] == "")
					{
						$paket_evaluasi_peralatan_detil_insert = new PaketEvaluasiPeralatanDetil();	
						$paket_evaluasi_peralatan_detil_insert->setField("PAKET_ID", $reqId);
						$paket_evaluasi_peralatan_detil_insert->setField("NAMA", $reqPeralatanNama[$i]);
						$paket_evaluasi_peralatan_detil_insert->setField("KETERANGAN", $reqPeralatanKeterangan[$i]);
						$paket_evaluasi_peralatan_detil_insert->setField("NILAI", $reqPeralatanDetilNilai[$i]);
						$paket_evaluasi_peralatan_detil_insert->insert();	
			
						$not_peralatan .= ",".$paket_evaluasi_peralatan_detil_insert->id;
						
						unset($paket_evaluasi_peralatan_detil_insert);		
					}
					else
					{
						$paket_evaluasi_peralatan_detil_insert = new PaketEvaluasiPeralatanDetil();	
						$paket_evaluasi_peralatan_detil_insert->setField("PAKET_EVAL_PERALATAN_DETIL_ID", $reqPeralatanId[$i]);
						$paket_evaluasi_peralatan_detil_insert->setField("NAMA", $reqPeralatanNama[$i]);
						$paket_evaluasi_peralatan_detil_insert->setField("KETERANGAN", $reqPeralatanKeterangan[$i]);
						$paket_evaluasi_peralatan_detil_insert->setField("NILAI", $reqPeralatanDetilNilai[$i]);
						$paket_evaluasi_peralatan_detil_insert->updatePeralatan();
						
						$not_peralatan .= ",".$reqPeralatanId[$i];
			
						unset($paket_evaluasi_peralatan_detil_insert);	
					}
				}
			}	
			
			$paket_evaluasi_peralatan_detil_delete = new PaketEvaluasiPeralatanDetil();	
			$paket_evaluasi_peralatan_detil_delete->setField("PAKET_ID", $reqId);
			$paket_evaluasi_peralatan_detil_delete->setField("PAKET_EVAL_PERALATAN_DETIL_ID", $not_peralatan);
			$paket_evaluasi_peralatan_detil_delete->deleteNotIn();
			
			/* PAKET_EVAL_SERTIFIKAT_LAIN */ 
		
			$not_sertifikat = "0";
			for($i=0;$i<count($reqSertifikatNama);$i++)
			{
				if($reqSertifikatNama[$i] == "")
				{}
				else
				{
					if($reqSertifikatId[$i] == "")
					{
						$paket_evaluasi_sertifikat_lain_insert = new PaketEvaluasiSertifikatLain();	
						$paket_evaluasi_sertifikat_lain_insert->setField("PAKET_ID", $reqId);
						$paket_evaluasi_sertifikat_lain_insert->setField("NAMA", $reqSertifikatNama[$i]);
						$paket_evaluasi_sertifikat_lain_insert->setField("KETERANGAN", $reqSertifikatKeterangan[$i]);
						$paket_evaluasi_sertifikat_lain_insert->setField("NILAI", $reqSertifikatDetilNilai[$i]);			
						$paket_evaluasi_sertifikat_lain_insert->setField("NILAI_MINIMUM", $reqSertifikatNilai);
						
						/*update tanggal 12-10-2012*/
						$paket_evaluasi_sertifikat_lain_insert->setField("NILAI_MINIMAL", $reqSertifikatlainNilaiMinimum);
								
						$paket_evaluasi_sertifikat_lain_insert->insert();
						
						$not_sertifikat .= ",".$paket_evaluasi_sertifikat_lain_insert->id;
						
						unset($paket_evaluasi_sertifikat_lain_insert);			
					}
					else
					{
						$paket_evaluasi_sertifikat_lain_insert = new PaketEvaluasiSertifikatLain();	
						$paket_evaluasi_sertifikat_lain_insert->setField("PAKET_EVAL_SERTIFIKAT_LAIN_ID", $reqSertifikatId[$i]);
						$paket_evaluasi_sertifikat_lain_insert->setField("NAMA", $reqSertifikatNama[$i]);
						$paket_evaluasi_sertifikat_lain_insert->setField("KETERANGAN", $reqSertifikatKeterangan[$i]);
						$paket_evaluasi_sertifikat_lain_insert->setField("NILAI", $reqSertifikatDetilNilai[$i]);			
						$paket_evaluasi_sertifikat_lain_insert->setField("NILAI_MINIMUM", $reqSertifikatNilai);
						
						/*update tanggal 12-10-2012*/
						$paket_evaluasi_sertifikat_lain_insert->setField("NILAI_MINIMAL", $reqSertifikatlainNilaiMinimum);
								
						$paket_evaluasi_sertifikat_lain_insert->updateSertifikat();
						
						$not_sertifikat .= ",".$reqSertifikatId[$i];
						
						unset($paket_evaluasi_sertifikat_lain_insert);
					}
				}
			}
			
			$paket_evaluasi_sertifikat_lain_delete = new PaketEvaluasiSertifikatLain();	
			$paket_evaluasi_sertifikat_lain_delete->setField("PAKET_ID", $reqId);
			$paket_evaluasi_sertifikat_lain_delete->setField("PAKET_EVAL_SERTIFIKAT_LAIN_ID", $not_sertifikat);
			$paket_evaluasi_sertifikat_lain_delete->deleteNotIn();
			
			
			$paket->setField("FIELD", "PASS_GRADE");
			$paket->setField("FIELD_VALUE", floatval($reqPassingGrade));	//aim: RITM0000254
			$paket->setField("PAKET_ID", $reqId);		
			$paket->updateByField();
		
			echo "Data berhasil di simpan.";
		}
		
	}
	
	function data_kualifikasi()
	{

		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("RekananEvaluasiPersonil");
		$this->load->model("RekananEvaluasiPeralatan");
		$this->load->model("RekananEvaluasiSertifikatLain");
		$this->load->model("PaketEvaluasiSertifikatLain");
		$this->load->model("PaketEvaluasiPeralatan");
		$this->load->model("PaketEvaluasiPeralatanDetil");
		$this->load->model("PaketEvaluasiPersonil");
		$this->load->model("PaketRekanan");
		$this->load->model("RekananEvaluasiKeuangan");
		$this->load->model("RekananEvaluasiPengalaman");
		$this->load->model("RekananEvaluasiAdmin");
		$this->load->model("RekananPengalaman");
		$this->load->model("RekananIjinUsaha");
		$this->load->model("RekananNeraca");
		$this->load->model("RekananRekeningKoran");
		$this->load->model("RekananBidangUsaha");
		$this->load->model("RekananAkta");
		$this->load->model("RekananSaham");
		$this->load->model("RekananPajak");
		$this->load->model("RekananPengurus");
		$this->load->model("PaketEvaluasiAdmin");
		$this->load->model("PaketEvaluasiKeuangan");
		$this->load->model("PaketKriteriaEvaluasi");
		$this->load->model("PaketEvaluasiKemampuanDasar");
		$this->load->model("PaketBidangUsaha");
		$this->load->model("PaketEvaluasiPengalaman");
		$this->load->model("PaketTahap");
		$this->load->model("PaketRekananKualifikasi");
		$this->load->library("FileHandler");
		
		
		$paket_evaluasi_admin = new PaketEvaluasiAdmin();
		$paket_kriteria_evaluasi = new PaketKriteriaEvaluasi();
		$paket_rekanan = new PaketRekanan();
		$paket_tahap = new PaketTahap();
		$paket_tahap_metode = new PaketTahap();
		$file = new FileHandler();
		
		$reqId = $this->input->post("reqId");
		$reqEvaluasiNumber = $_POST["reqEvaluasiNumber"];
		$reqEvaluasiAdmin = $_POST["reqEvaluasiAdmin"];
		
		
		$reqPengalamanId = $_POST["reqPengalamanId"];
		$submitSimpan = $this->input->post("submitSimpan");
		
		$reqPaketEvalKeuanganId = $this->input->post("reqPaketEvalKeuanganId");
		$reqKB = $this->input->post("reqKB");
		$reqFL = $this->input->post("reqFL");
		$reqMK = $this->input->post("reqMK");
		$reqFP = $this->input->post("reqFP");
		$reqKK = $this->input->post("reqKK");
		$reqNK = $this->input->post("reqNK");
		$reqProgress = $this->input->post("reqProgress");
		$reqPrestasi = $this->input->post("reqPrestasi");
		$reqSKK = $this->input->post("reqSKK");
		$reqRekeningKoran = $this->input->post("reqRekeningKoran");
		
		$reqPaketEvalPersonilId = $_POST["reqPaketEvalPersonilId"];
		$reqPersonilId = $_POST["reqPersonilId"];
		$reqPaketEvalPeralatanDetilId = $_POST["reqPaketEvalPeralatanDetilId"];
		$reqPeralatanId = $_POST["reqPeralatanId"];
		$reqPaketEvalSertifikatLainId = $_POST["reqPaketEvalSertifikatLainId"];
		$reqSertifikatLainId = $_POST["reqSertifikatLainId"];
		$reqLinkFileDataAdministrasi = $_FILES["reqLinkFileDataAdministrasi"];
		$reqLinkFileDataAdministrasiTemp = $_POST["reqLinkFileDataAdministrasiTemp"];


		$paket_rekanan_check = new PaketRekanan();
		$reqCheckPaketRekananId = $paket_rekanan_check->getPaketRekananId($reqId, $this->REKANAN_ID);
		if($reqCheckPaketRekananId == "")
			exit;
		
				
		$paketInfo->getPaket($reqId);
		$reqNama = $paketInfo->nama;
		$reqKualifikasi = $paketInfo->kualifikasi;
		$reqKualifikasiId = $paketInfo->kualifikasi_id;
		$reqNilai = $paketInfo->nilai;
		$reqTahun = getYear($paketInfo->tanggal_tahap);
		$reqBulan = (int)getMonth($paketInfo->tanggal_tahap);
		
		$FILE_DIR_KUALIFIKASI = "uploads/kualifikasi/";
		
		$jenis_tahap = $paket_tahap_metode->getJenisTahapById($reqId);
		
		$arrEvaluasiKualifikasi  = array(0, 5,  6,  5,  6,  5,  6,  5,  5,  0, 0, 5,  0,  5,  0);
		$arrEvaluasiKualifikasi1 = array(0, 6,  8,  6,  8,  6,  8,  6,  6,  0, 0, 6,  0,  6,  0);
		
		$aktif_dok_kualifikasi1 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
		$aktif_dok_kualifikasi2 = $paket_tahap->getCountByParamsAktif(array("URUT" => $arrEvaluasiKualifikasi1[$jenis_tahap], "PAKET_ID" => $reqId, "TAMPILKAN" => 1));
		
		if($aktif_dok_kualifikasi1 > 0  || $aktif_dok_kualifikasi2 > 0)
			$aktif_entri = 1;
		else
			$aktif_entri = 0;
		
		$reqPaketRekananId = $paket_rekanan->getPaketRekananId($reqId, $this->REKANAN_ID);
		
		if($submitSimpan == "Simpan")
		{
			for($i=0;$i<count($reqEvaluasiNumber);$i++)
			{
				$evaluasi_admin = new RekananEvaluasiAdmin();
				$id = $evaluasi_admin->getIdEvaluasiAdmin(array("PAKET_REKANAN_ID" => $reqPaketRekananId, "EVALUASI_NUMBER" => $reqEvaluasiNumber[$i]));
				if((int)$id == 0)
				{
					$evaluasi_admin->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
					$evaluasi_admin->setField("URAIAN", str_replace("'", "''", $reqEvaluasiAdmin[$i]));
					$evaluasi_admin->setField("EVALUASI_NUMBER", $reqEvaluasiNumber[$i]);	
					
					/* UPLOAD FILE */
					$insertLinkFile = "";
					$insertLinkFilesSize = "NULL";
					$insertLinkFilesExe = "";
					$cek = formatTextToDb($file->getFileNameArray('reqLinkFileDataAdministrasi', $i));
					if($cek != "")
					{
						$renameFile = $this->REKANAN_ID.'~'.formatTextToDb($file->getFileNameArray('reqLinkFileDataAdministrasi', $i));
						$varSource=$FILE_DIR_KUALIFIKASI.$reqLinkFileDataAdministrasiTemp;
						
						if($file->uploadToDirArray('reqLinkFileDataAdministrasi', $FILE_DIR_KUALIFIKASI, $renameFile, $i))
						{
							if($reqLinkFileDataAdministrasiTemp != ''){
								if($file->delete($varSource)){}
							}
							$insertLinkFile = $file->uploadedFileName;
							$insertLinkFilesSize = $file->uploadedSize;
							$insertLinkFilesExe = $file->uploadedExtension;
						}		
					}
					
					$evaluasi_admin->setField("UKURAN", $insertLinkFilesSize);
					$evaluasi_admin->setField("TIPE", $insertLinkFilesExe);
					$evaluasi_admin->setField("PATH_FILE", $insertLinkFile);	
								
					$evaluasi_admin->insert();
					
				}
				else
				{
					
					$evaluasi_admin->setField("REKANAN_EVAL_ADMIN_ID", $id);
					$evaluasi_admin->setField("URAIAN", str_replace("'", "''", $reqEvaluasiAdmin[$i]));
		
					/* UPLOAD FILE */
					$insertLinkFile = "";
					$insertLinkFilesSize = "NULL";
					$insertLinkFilesExe = "";
					$cek = formatTextToDb($file->getFileNameArray('reqLinkFileDataAdministrasi', $i));
					if($cek != "")
					{
						$renameFile = $this->REKANAN_ID.date("dmY").'~'.formatTextToDb($file->getFileNameArray('reqLinkFileDataAdministrasi', $i));
						$varSource=$FILE_DIR_KUALIFIKASI.$reqLinkFileDataAdministrasiTemp;
						
						if($file->uploadToDirArray('reqLinkFileDataAdministrasi', $FILE_DIR_KUALIFIKASI, $renameFile, $i))
						{
							if($reqLinkFileDataAdministrasiTemp != ''){
								if($file->delete($varSource)){}
							}
							$insertLinkFile = $file->uploadedFileName;
							$insertLinkFilesSize = $file->uploadedSize;
							$insertLinkFilesExe = $file->uploadedExtension;
						}		
					}
					
					if($insertLinkFile == "")
						$evaluasi_admin->update();
					else
					{
						$evaluasi_admin->setField("UKURAN", $insertLinkFilesSize);
						$evaluasi_admin->setField("TIPE", $insertLinkFilesExe);
						$evaluasi_admin->setField("PATH_FILE", $insertLinkFile);
						$evaluasi_admin->updateFile();						
					}
					
					
				}
				unset($evaluasi_admin);
			}
			
			$evaluasi_pengalaman_rekanan= new RekananEvaluasiPengalaman();	
			$evaluasi_pengalaman_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$evaluasi_pengalaman_rekanan->deletePaketRekanan();
			for($i=0;$i<count($reqPengalamanId);$i++)
			{
				if($reqPengalamanId[$i] == "")
				{}
				else
				{
					$evaluasi_pengalaman_rekanan_insert= new RekananEvaluasiPengalaman();
					$evaluasi_pengalaman_rekanan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
					$evaluasi_pengalaman_rekanan_insert->setField("REKANAN_PENGALAMAN_ID", $reqPengalamanId[$i]);
					$evaluasi_pengalaman_rekanan_insert->insert();
					unset($evaluasi_pengalaman_rekanan_insert);
				}
			}
			
			/*$evaluasi_pengalaman_rekanan = new RekananEvaluasiPengalaman();
			$check = $evaluasi_pengalaman_rekanan->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
			if($check == 0)
			{
				$evaluasi_pengalaman_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
				$evaluasi_pengalaman_rekanan->setField("REKANAN_PENGALAMAN_ID", $reqPengalamanId);
				$evaluasi_pengalaman_rekanan->insert();	
			}
			else
			{
				$evaluasi_pengalaman_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);		
				$evaluasi_pengalaman_rekanan->setField("REKANAN_PENGALAMAN_ID", $reqPengalamanId);
				$evaluasi_pengalaman_rekanan->update();	
			}*/
		
			$evaluasi_keuangan_rekanan = new RekananEvaluasiKeuangan();
			$check = $evaluasi_keuangan_rekanan->getCountByParams(array("PAKET_REKANAN_ID" => $reqPaketRekananId));
			if($check == 0)
			{
				$evaluasi_keuangan_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
				$evaluasi_keuangan_rekanan->setField("PAKET_EVAL_KEUANGAN_ID", $reqPaketEvalKeuanganId);
				$evaluasi_keuangan_rekanan->setField("KUALIFIKASI", $reqKualifikasi);
				$evaluasi_keuangan_rekanan->setField("KB", $reqKB);
				$evaluasi_keuangan_rekanan->setField("FL", $reqFL);
				$evaluasi_keuangan_rekanan->setField("MK", $reqMK);
				$evaluasi_keuangan_rekanan->setField("FP", $reqFP);
				$evaluasi_keuangan_rekanan->setField("KK", $reqKK);
				$evaluasi_keuangan_rekanan->setField("NK", $reqNK);
				$evaluasi_keuangan_rekanan->setField("PROGRESS", $reqProgress);
				$evaluasi_keuangan_rekanan->setField("PRESTASI", $reqPrestasi);
				$evaluasi_keuangan_rekanan->setField("SKK", $reqSKK);
				$evaluasi_keuangan_rekanan->setField("REKENING_KORAN", $reqRekeningKoran);		
				$evaluasi_keuangan_rekanan->insert();	
			}
			else
			{
				$evaluasi_keuangan_rekanan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
				$evaluasi_keuangan_rekanan->setField("KUALIFIKASI", $reqKualifikasi);
				$evaluasi_keuangan_rekanan->setField("KB", $reqKB);
				$evaluasi_keuangan_rekanan->setField("FL", $reqFL);
				$evaluasi_keuangan_rekanan->setField("MK", $reqMK);
				$evaluasi_keuangan_rekanan->setField("FP", $reqFP);
				$evaluasi_keuangan_rekanan->setField("KK", $reqKK);
				$evaluasi_keuangan_rekanan->setField("NK", $reqNK);
				$evaluasi_keuangan_rekanan->setField("PROGRESS", $reqProgress);
				$evaluasi_keuangan_rekanan->setField("PRESTASI", $reqPrestasi);
				$evaluasi_keuangan_rekanan->setField("SKK", $reqSKK);
				$evaluasi_keuangan_rekanan->setField("REKENING_KORAN", $reqRekeningKoran);		
				$evaluasi_keuangan_rekanan->update();	
			}
			//echo $evaluasi_keuangan_rekanan->query;
			
			$evaluasi_personil = new RekananEvaluasiPersonil();	
			$evaluasi_personil->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$evaluasi_personil->delete();
			
			for($i=0;$i<count($reqPersonilId);$i++)
			{
				if($reqPersonilId[$i] == "")
				{}
				else
				{
					$evaluasi_personil_insert = new RekananEvaluasiPersonil();	
					$evaluasi_personil_insert->setField("PAKET_EVAL_PERSONIL_ID", $reqPaketEvalPersonilId[$i]);	
					$evaluasi_personil_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId);	
					$evaluasi_personil_insert->setField("REKANAN_TENAGA_AHLI_ID", $reqPersonilId[$i]);	
					$evaluasi_personil_insert->insert();
					//echo $evaluasi_personil_insert->query."<br/>";
					unset($evaluasi_personil_insert);
				}
			}
		
			$evaluasi_peralatan = new RekananEvaluasiPeralatan();	
			$evaluasi_peralatan->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$evaluasi_peralatan->delete();
			for($i=0;$i<count($reqPeralatanId);$i++)
			{
				if($reqPeralatanId[$i] == "")
				{}
				else
				{
					$evaluasi_peralatan_insert = new RekananEvaluasiPeralatan();	
					$evaluasi_peralatan_insert->setField("PAKET_EVAL_PERALATAN_DETIL_ID", $reqPaketEvalPeralatanDetilId[$i]);	
					$evaluasi_peralatan_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId);	
					$evaluasi_peralatan_insert->setField("REKANAN_PERALATAN_ID", $reqPeralatanId[$i]);	
					$evaluasi_peralatan_insert->insert();
					unset($evaluasi_peralatan_insert);
				}
			}
		
			$evaluasi_sertifikat_lain = new RekananEvaluasiSertifikatLain();	
			$evaluasi_sertifikat_lain->setField("PAKET_REKANAN_ID", $reqPaketRekananId);
			$evaluasi_sertifikat_lain->delete();
			for($i=0;$i<count($reqSertifikatLainId);$i++)
			{
				if($reqSertifikatLainId[$i] == "")
				{}
				else
				{
					$evaluasi_sertifikat_lain_insert = new RekananEvaluasiSertifikatLain();	
					$evaluasi_sertifikat_lain_insert->setField("PAKET_EVAL_SERTIFIKAT_LAIN_ID", $reqPaketEvalSertifikatLainId[$i]);	
					$evaluasi_sertifikat_lain_insert->setField("PAKET_REKANAN_ID", $reqPaketRekananId);	
					$evaluasi_sertifikat_lain_insert->setField("REKANAN_SERTIFIKAT_ID", $reqSertifikatLainId[$i]);	
					$evaluasi_sertifikat_lain_insert->insert();
					unset($evaluasi_sertifikat_lain_insert);
				}
			}
			echo "Data berhasil disimpan.";
		}
		
	}
	
	function publish_evaluasi_kualifikasi()
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->model("Paket");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");
		
		$paket->setField("PAKET_ID", $reqId);
		if($paket->publishBAKualifikasi())
		echo "Hasil evaluasi kualifikasi berhasil dipublish.";
		
	}
	
	function paket_lelang_batal()
	{
		$this->load->model("Paket");
		/* VARIABLES */
		$reqId = $this->input->post("reqId");
		$reqSubmit =  $this->input->post("reqSubmit");
		$reqAlasan =  $this->input->post("reqAlasan");
		
		$paket = new Paket();
		$paket->setField("PAKET_ID",$reqId);
		$paket->setField("ALASAN",$reqAlasan);
		$paket->updateAlasan();
			/*$paketInfo->getPaket($reqId);
			$reqPrGroupNumber = $paketInfo->pr_group_number;
			$this->load->model("SAP/SapPr");
			$sap_pr = new SapPr();
			$sap_pr->setField("FIELD", "PAKET_ID");
			$sap_pr->setField("FIELD_VALUE", "NULL");
			$sap_pr->setField("PR_GROUP_NUMBER", $reqPrGroupNumber);		
			$sap_pr->updateByField();*/
		
			echo "Data Berhasil Tersimpan";
		
	}
	
	function setPublishNegosiasi()
	{
		
		$this->load->library("kauth");  $userLogin = new kauth(); 
		$this->load->library("paketinfo"); $paketInfo = new paketinfo();
		$this->load->model("Paket");
		$this->load->model("PaketNegosiasiValidasi");
		$paket = new Paket();
		
		$reqId = $this->input->get("reqId");

		$paketInfo->getPaket($reqId);
		
		if($paketInfo->jenis_pengadaan == "LELANG")
		{
			/* VALIDASI PEMBUKAAN PENAWARAN */
			$paket_negosiasi_validasi = new PaketNegosiasiValidasi();
			$jumlahValidasi = $paket_negosiasi_validasi->getCountByParams(array("A.PAKET_ID" => $reqId));
			
			if($jumlahValidasi == 0)
			{
				$arrFinal = array("STATUS" => "Minimal terdapat 1(satu) validasi dari panitia.");			
				echo json_encode($arrFinal);		
				return;
			}
		}
				
		$paket->setField("PAKET_ID", $reqId);
		$paket->publishBANegosiasi();
		$arrFinal = array("STATUS" => "1");			
		echo json_encode($arrFinal);
		
	}
		
}
?>
