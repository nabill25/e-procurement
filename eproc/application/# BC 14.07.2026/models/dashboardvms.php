<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Dashboardvms extends Entity
  {

		var $query;

    function __construct(){
  		parent::__construct();
		}

	  function selectByParams($table,$paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order='')
		{
			$str = "SELECT * FROM ".$table."
					WHERE 1 = 1
					";
			foreach ($paramsArray as $key => $val) {
			    $pecah = explode("||", $key);
			    if (count($pecah) > 1) {
			        $str .= "AND $pecah[0] $pecah[1] $val ";
			    } else {
			        $str .= " AND $key = '$val' ";
			    }
			}

			$this->query = $str;
			$str .= $statement." ".$order;
			// echo $str;
			return $this->selectLimit($str,$limit,$from);
	  }

	  function selectByParamsForVerifikasi()
		{
			$str = "SELECT 
					(select count(a.rekanan_id) total 
					from user_login a 
					inner join rekanan b on a.rekanan_id = b.rekanan_id 
					where a.user_type_id='6'),
					(select count(a.rekanan_id) melengkapi_berkas 
					from user_login a 
					inner join rekanan b on a.rekanan_id = b.rekanan_id 
					where a.user_type_id='6' and  a.user_status = '0' and b.status_validasi = '0' ),
					(select count(a.rekanan_id) kirim_berkas 
					from user_login a 
					inner join rekanan b on a.rekanan_id = b.rekanan_id 
					where a.user_type_id='6' and  a.user_status = '2' and b.status_validasi = '0' ),
					(select count(a.rekanan_id) proses_approval 
					from user_login a 
					inner join rekanan b on a.rekanan_id = b.rekanan_id 
					where a.user_type_id='6' and  a.user_status = '2' and b.status_validasi = '4' ),
					(select count(a.rekanan_id) berkas_ditolak 
					from user_login a 
					inner join rekanan b on a.rekanan_id = b.rekanan_id 
					where a.user_type_id='6' and  a.user_status = '2' and b.status_validasi = '10' ),
					(select count(a.rekanan_id) terverfikasi 
					from user_login a 
					inner join rekanan b on a.rekanan_id = b.rekanan_id 
					where a.user_type_id='6' and  a.user_status = '1' and b.status_validasi = '1' ),
					(select count(a.rekanan_id) revisi 
					from user_login a 
					inner join rekanan b on a.rekanan_id = b.rekanan_id 
					where a.user_type_id='6' and  a.user_status = '0' and b.status_validasi = '1' )
					";

					 
			$this->query = $str;
			$str .= $statement." ".$order;
			// echo $str;
			return $this->selectLimit($str,$limit,$from);
	  }

	  function selectByParamsKirimBerkasRevisi()
		{
			$str = "SELECT count(a.rekananid) total FROM ( 
						SELECT 
						    rekananid, 
						    (npwp_note IS NOT NULL AND npwp_note <> '' AND a.npwp ='0')::int +
						    (nib_note IS NOT NULL AND nib_note <> '' AND nib ='0')::int +
						    (akta_note IS NOT NULL AND akta_note <> '' AND akta ='0')::int +
						    (pengurus_note IS NOT NULL AND pengurus_note <> '' AND pengurus ='0')::int +
						    (saham_note IS NOT NULL AND saham_note <> '' AND saham ='0')::int +
						    (sbu_note IS NOT NULL AND sbu_note <> '' AND sbu ='0')::int +
						    (rekening_koran_note IS NOT NULL AND rekening_koran_note <> '' AND rekening_koran ='0')::int +
						    (neraca_note IS NOT NULL AND neraca_note <> '' AND neraca ='0')::int +
						    (pkp_note IS NOT NULL AND pkp_note <> '' AND a.pkp ='0')::int +
						    (spt_tahunan_note IS NOT NULL AND spt_tahunan_note <> '' AND spt_tahunan ='0')::int +
						    (pph_note IS NOT NULL AND pph_note <> '' AND pph ='0')::int +
						    (ppn_note IS NOT NULL AND ppn_note <> '' AND ppn ='0')::int +
						    (tenaga_ahli_note IS NOT NULL AND tenaga_ahli_note <> '' AND tenaga_ahli ='0')::int +
						    (pengalaman_note IS NOT NULL AND pengalaman_note <> '' AND pengalaman ='0')::int +
						    (peralatan_note IS NOT NULL AND peralatan_note <> '' AND peralatan ='0')::int +
						    (teknis_lain_note IS NOT NULL AND teknis_lain_note <> '' AND teknis_lain ='0')::int +
						    (cv_note IS NOT NULL AND cv_note <> '' AND cv_note ='0')::int +
						    (ktp_note IS NOT NULL AND ktp_note <> '' AND a.ktp ='0')::int 
						    AS jumlah_terisi
					FROM rekanan_checklist a
					join user_login b on a.rekananid = b.rekanan_id
					join rekanan c on b.rekanan_id = c.rekanan_id 
					WHERE b.user_status = '2' and c.status_validasi = '0'
					) a 
				WHERE a.jumlah_terisi > 0";

					 
			$this->query = $str;
			$str .= $statement." ".$order;
			// echo $str;
			return $this->selectLimit($str,$limit,$from);
	  }

	  function selectByPenilaian($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order='')
		{
			$str = "SELECT a.star, count(a.star) total
					from view_penilaian_rekanan_by_user a
					group by a.star
					";
			foreach ($paramsArray as $key => $val) {
			    $pecah = explode("||", $key);
			    if (count($pecah) > 1) {
			        $str .= "AND $pecah[0] $pecah[1] $val ";
			    } else {
			        $str .= " AND $key = '$val' ";
			    }
			}

			$this->query = $str;
			$str .= $statement." ".$order;

			return $this->selectLimit($str,$limit,$from);
	  }

	  function selectDash($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order='')
		{
			$str = "SELECT A.USER_LOGIN_ID, A.USER_LOGIN, A.USER_LAST_LOGIN, A.USER_STATUS, A.USER_AUTH, B.REKANAN_TIPE_ID, B.REKANAN_KUALIFIKASI_ID, B.KODE, CONCAT(C.NAMA,' ', B.NAMA) NAMA,
B.NPWP, B.ALAMAT, CONCAT(B.TELEPON_KODE,' ',B.TELEPON) TELEPON, B.EMAIL, B.ALAMAT_PUSAT,
B.EMAIL_PUSAT, B.STATUS_PERUSAHAAN, B.STATUS_VALIDASI, B.TANGGAL_DAFTAR, B.TANGGAL_VALIDASI,
B.PKP, B.PKP_TANGGAL, B.KOTA, B.KODEPOS, B.REGION_ID, B.BANK_ID, B.BANK_REKENING, B.BANK_PEMILIK, B.MATA_UANG_KODE,
B.WEBSITE, B.KONTAK_PERSON, B.KONTAK_PERSON_HP, B.USER_VALIDASI, B.BANK_CABANG, B.NOTE_1, B.NOTE_2, B.NOTE_3,
B.DATE_VALIDASI_1,B.DATE_VALIDASI_2,B.DATE_VALIDASI_3
							FROM user_login A
						  JOIN rekanan B on A.rekanan_id=B.rekanan_id
						  join rekanan_tipe C on B.rekanan_tipe_id=C.rekanan_tipe_id
						WHERE 1 = 1
					";
			foreach ($paramsArray as $key => $val) {
			    $pecah = explode("||", $key);
			    if (count($pecah) > 1) {
			        $str .= "AND $pecah[0] $pecah[1] $val ";
			    } else {
			        $str .= " AND $key = '$val' ";
			    }
			}

			$this->query = $str;
			$str .= $statement." ".$order;
			// echo $str;
			return $this->selectLimit($str,$limit,$from);
	  }

    function selectBlacklist($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order='')
		{
			$str = " SELECT B.* FROM blacklist A
              JOIN rekanan B on A.rekanan_id=B.rekanan_id
              WHERE CURRENT_DATE between A.tanggal_mulai AND A.tanggal_selesai
					";
			foreach ($paramsArray as $key => $val) {
			    $pecah = explode("||", $key);
			    if (count($pecah) > 1) {
			        $str .= "AND $pecah[0] $pecah[1] $val ";
			    } else {
			        $str .= " AND $key = '$val' ";
			    }
			}

			$this->query = $str;
			$str .= $statement." ".$order;
			// echo $str;
			return $this->selectLimit($str,$limit,$from);
	  }

	  function selectBlacklistHistory($paramsArray=array(),$limit=-1,$from=-1, $statement='',  $order='')
		{
			$str = " SELECT B.* FROM blacklist A
              JOIN rekanan B on A.rekanan_id=B.rekanan_id
              WHERE CURRENT_DATE > A.tanggal_selesai
					";
			foreach ($paramsArray as $key => $val) {
			    $pecah = explode("||", $key);
			    if (count($pecah) > 1) {
			        $str .= "AND $pecah[0] $pecah[1] $val ";
			    } else {
			        $str .= " AND $key = '$val' ";
			    }
			}

			$this->query = $str;
			$str .= $statement." ".$order;
			// echo $str;
			return $this->selectLimit($str,$limit,$from);
	  }

	}
?>
