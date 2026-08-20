<?php

  include_once('entity.php');

  class Rekanan extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    // function Rekanan()
    function __construct()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_ID", $this->getNextId("REKANAN_ID","REKANAN"));

		$str = "
					INSERT INTO  REKANAN (
					   REKANAN_ID, IJIN_USAHA_ID, REKANAN_TIPE_ID,
					   REKANAN_KUALIFIKASI_ID, KODE, NAMA,
					   NPWP, ALAMAT, TELEPON_KODE,
					   TELEPON, FAX_KODE, FAX,
					   EMAIL, STATUS_PERUSAHAAN, STATUS_VALIDASI,
					   STATUS_CP, TANGGAL_DAFTAR, TANGGAL_VALIDASI,
					   PKP, PKP_TANGGAL, KOTA,
					   KODEPOS, REGION_ID, BANK_ID,
					   PKP_FILE, NPWP_FILE, NAMA_FILE_PKP,
					   NAMA_FILE_NPWP, KONTAK_PERSON,
					   KONTAK_PERSON_HP, WEBSITE)
 			  	VALUES (
						  ".$this->getField("REKANAN_ID").",
						  ".$this->getField("IJIN_USAHA_ID").",
						  ".$this->getField("REKANAN_TIPE_ID").",
						  ".$this->getField("REKANAN_KUALIFIKASI_ID").",
						  '".$this->getField("KODE")."',
						  '".$this->getField("NAMA")."',
						  '".$this->getField("NPWP")."',
						  '".$this->getField("ALAMAT")."',
						  '".$this->getField("TELEPON_KODE")."',
						  '".$this->getField("TELEPON")."',
						  '".$this->getField("FAX_KODE")."',
						  '".$this->getField("FAX")."',
						  '".$this->getField("EMAIL")."',
						  ".$this->getField("STATUS_PERUSAHAAN").",
						  ".$this->getField("STATUS_VALIDASI").",
						  ".$this->getField("STATUS_CP").",
						  CURRENT_DATE,
						  NULL,
						  '".$this->getField("PKP")."',
						  ".$this->getField("PKP_TANGGAL").",
						  '".$this->getField("KOTA")."',
						  '".$this->getField("KODEPOS")."',
						  '".$this->getField("REGION_ID")."',
						  ".$this->getField("BANK_ID").",
						  '".$this->getField("PKP_FILE")."',
						  '".$this->getField("NPWP_FILE")."',
						  '".$this->getField("NAMA_FILE_PKP")."',
						  '".$this->getField("NAMA_FILE_NPWP")."',
						  '".$this->getField("KONTAK_PERSON")."',
						  '".$this->getField("KONTAK_PERSON_HP")."',
						  '".$this->getField("WEBSITE")."'
						)";
				//'".$this->getField("TANGGAL_DAFTAR")."'
						// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("REKANAN_ID");
		return $this->execQuery($str);
  }

    function insertrevisi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("REKANAN_ID", $this->getNextId("REKANAN_ID","REKANAN"));

		$str = "
					INSERT INTO  REKANAN (
					   REKANAN_ID, IJIN_USAHA_ID, REKANAN_KUALIFIKASI_ID, REKANAN_TIPE_ID,
					   KODE, NAMA, NPWP, EMAIL, STATUS_VALIDASI,
					   STATUS_CP, TANGGAL_DAFTAR, TANGGAL_VALIDASI)
 			  	VALUES (
						  ".$this->getField("REKANAN_ID").",
						  ".$this->getField("IJIN_USAHA_ID").",
						  ".$this->getField("REKANAN_KUALIFIKASI_ID").",
						  ".$this->getField("REKANAN_TIPE_ID").",
						  '".$this->getField("KODE")."',
						  '".$this->getField("NAMA")."',
						  '".$this->getField("NPWP")."',
						  '".$this->getField("EMAIL")."',
						  ".$this->getField("STATUS_VALIDASI").",
						  ".$this->getField("STATUS_CP").",
						  CURRENT_DATE,
						  NULL
						)";
				//'".$this->getField("TANGGAL_DAFTAR")."'
						// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("REKANAN_ID");
		return $this->execQuery($str);
  }

	function update_dyna()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE ".$this->getField("TABLE")." SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."'
				WHERE ".$this->getField("FIELD_KEY")." = '".$this->getField("FIELD_KEY_VALUE")."'
				";
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
  }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN A SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."'
				WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				$this->query = $str;

		return $this->execQuery($str);
    }
	function updateByFieldAkmal()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN A SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."'
				, UPDATED_DATE = '".$this->getField("UPDATED_DATE")."'
				WHERE REKANAN_ID = ".$this->getField("REKANAN_ID")."
				";
				$this->query = $str;
				//var_dump($this->execQuery($str));
//die($str);
		return $this->execQuery($str);
    }

  function updateAllowUrl()
	{
		$str = "
				UPDATE  REKANAN_URL_VALIDASI_ALLOW
				SET
		    ALLOW_URL        = '".$this->getField("ALLOW_URL")."',
		    UPDATED_BY          	  = ".$this->getField("CREATED_BY").",
		    UPDATED_DATE          	  = CURRENT_TIMESTAMP
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'
			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

  function updateEmailKodeVerifikasi()
	{
		$str = "
				UPDATE  REKANAN
				SET
		    EMAIL_KODE_VERIFIKASI        = '".$this->getField("EMAIL_KODE_VERIFIKASI")."',
		    UPDATED_BY          	  = ".$this->getField("CREATED_BY").",
		    UPDATED_DATE          	  = CURRENT_TIMESTAMP
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'
			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

  function updateEmail()
	{
		$str = "
				UPDATE  REKANAN
				SET
		    EMAIL        = '".$this->getField("EMAIL")."',
		    EMAIL_KODE_VERIFIKASI   = '',
		    UPDATED_BY          	  = ".$this->getField("CREATED_BY").",
		    UPDATED_DATE          	  = CURRENT_TIMESTAMP
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."' AND EMAIL_KODE_VERIFIKASI = '".$this->getField("EMAIL_KODE_VERIFIKASI")."'
			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
//		$str = "
//
//				UPDATE  REKANAN
//				SET
//					   IJIN_USAHA_ID          = ".$this->getField("IJIN_USAHA_ID").",
//					   REKANAN_TIPE_ID        = ".$this->getField("REKANAN_TIPE_ID").",
//					   REKANAN_KUALIFIKASI_ID = ".$this->getField("REKANAN_KUALIFIKASI_ID").",
//					   KODE                   = '".$this->getField("KODE")."',
//					   NAMA                   = '".$this->getField("NAMA")."',
//					   NPWP                   = '".$this->getField("NPWP")."',
//					   ALAMAT                 = '".$this->getField("ALAMAT")."',
//					   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
//					   TELEPON                = '".$this->getField("TELEPON")."',
//					   FAX_KODE               = '".$this->getField("FAX_KODE")."',
//					   FAX                    = '".$this->getField("FAX")."',
//					   EMAIL                  = '".$this->getField("EMAIL")."',
//					   ALAMAT_PUSAT           = '".$this->getField("ALAMAT_PUSAT")."',
//					   TELEPON_KODE_PUSAT     = '".$this->getField("TELEPON_KODE_PUSAT")."',
//					   TELEPON_PUSAT          = '".$this->getField("TELEPON_PUSAT")."',
//					   FAX_KODE_PUSAT         = '".$this->getField("FAX_KODE_PUSAT")."',
//					   FAX_PUSAT              = '".$this->getField("FAX_PUSAT")."',
//					   EMAIL_PUSAT            = '".$this->getField("EMAIL_PUSAT")."',
//					   STATUS_PERUSAHAAN      = ".$this->getField("STATUS_PERUSAHAAN").",
//					   STATUS_VALIDASI        = ".$this->getField("STATUS_VALIDASI").",
//					   STATUS_CP              = ".$this->getField("STATUS_CP").",
//					   TANGGAL_DAFTAR         = '".$this->getField("TANGGAL_DAFTAR")."',
//					   TANGGAL_VALIDASI       = '".$this->getField("TANGGAL_VALIDASI")."',
//					   PKP                    = '".$this->getField("PKP")."',
//					   PKP_TANGGAL            = '".$this->getField("PKP_TANGGAL")."',
//					   KOTA                   = '".$this->getField("KOTA")."',
//					   SURAT_KUASA            = '".$this->getField("SURAT_KUASA")."'
//				WHERE  REKANAN_ID             = ".$this->getField("REKANAN_ID")."
//
//			 ";
		$str = "

				UPDATE  REKANAN
				SET
					   REKANAN_TIPE_ID        = '".$this->getField("REKANAN_TIPE_ID")."',
					   REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."',
					   NAMA                   = '".$this->getField("NAMA")."',
					   NPWP                   = '".$this->getField("NPWP")."',
					   ALAMAT                 = '".$this->getField("ALAMAT")."',
					   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
					   TELEPON                = '".$this->getField("TELEPON")."',
					   FAX_KODE               = '".$this->getField("FAX_KODE")."',
					   FAX                    = '".$this->getField("FAX")."',
					   EMAIL                  = '".$this->getField("EMAIL")."',
					   ALAMAT_PUSAT           = '".$this->getField("ALAMAT_PUSAT")."',
					   TELEPON_KODE_PUSAT     = '".$this->getField("TELEPON_KODE_PUSAT")."',
					   TELEPON_PUSAT          = '".$this->getField("TELEPON_PUSAT")."',
					   FAX_KODE_PUSAT         = '".$this->getField("FAX_KODE_PUSAT")."',
					   FAX_PUSAT              = '".$this->getField("FAX_PUSAT")."',
					   EMAIL_PUSAT            = '".$this->getField("EMAIL_PUSAT")."',
					   STATUS_PERUSAHAAN      = '".$this->getField("STATUS_PERUSAHAAN")."',
					   KOTA                   = '".$this->getField("KOTA")."',
					   KODEPOS                = '".$this->getField("KODEPOS")."',
					   REGION_ID              = '".$this->getField("REGION_ID")."',
					   WEBSITE                = '".$this->getField("WEBSITE")."',
					   KONTAK_PERSON_HP       = '".$this->getField("KONTAK_PERSON_HP")."',
					   KONTAK_PERSON          = '".$this->getField("KONTAK_PERSON")."',
					   BANK_ID                = '".$this->getField("BANK_ID")."',
					   BANK_REKENING          = '".$this->getField("BANK_REKENING")."',
					   BANK_PEMILIK           = '".$this->getField("BANK_PEMILIK")."',
					   INCOTERM_ID            = '".$this->getField("INCOTERM_ID")."',
					   INCOTERM2          	  = '".$this->getField("INCOTERM2")."',
					   PAYMENT_METHOD_ID	  = '".$this->getField("PAYMENT_METHOD_ID")."',
					   MATA_UANG_KODE		  = '".$this->getField("MATA_UANG_KODE")."'
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'

			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

    function updateprofile()
	{
		// $str = "

		// 		UPDATE  REKANAN
		// 		SET
		// 			   REKANAN_TIPE_ID        = '".$this->getField("REKANAN_TIPE_ID")."',
		// 			   REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."',
		// 			   NAMA                   = '".$this->getField("NAMA")."',
		// 			   NPWP                   = '".$this->getField("NPWP")."',
		// 			   NPWP_FILE              = '".$this->getField("NPWP_FILE")."',
		// 			   NAMA_FILE_NPWP         = '".$this->getField("NAMA_FILE_NPWP")."',
		// 			   PKP                    = '".$this->getField("PKP")."',
		// 			   PKP_FILE               = '".$this->getField("PKP_FILE")."',
		// 			   NAMA_FILE_PKP          = '".$this->getField("NAMA_FILE_PKP")."',
		// 			   PKP_TANGGAL            = ".$this->getField("PKP_TANGGAL").",
		// 			   ALAMAT                 = '".$this->getField("ALAMAT")."',
		// 			   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
		// 			   TELEPON                = '".$this->getField("TELEPON")."',
		// 			   FAX_KODE               = '".$this->getField("FAX_KODE")."',
		// 			   FAX                    = '".$this->getField("FAX")."',
		// 			   EMAIL                  = '".$this->getField("EMAIL")."',
		// 			   ALAMAT_PUSAT           = '".$this->getField("ALAMAT_PUSAT")."',
		// 			   TELEPON_KODE_PUSAT     = '".$this->getField("TELEPON_KODE_PUSAT")."',
		// 			   TELEPON_PUSAT          = '".$this->getField("TELEPON_PUSAT")."',
		// 			   FAX_KODE_PUSAT         = '".$this->getField("FAX_KODE_PUSAT")."',
		// 			   FAX_PUSAT              = '".$this->getField("FAX_PUSAT")."',
		// 			   EMAIL_PUSAT            = '".$this->getField("EMAIL_PUSAT")."',
		// 			   STATUS_PERUSAHAAN      = '".$this->getField("STATUS_PERUSAHAAN")."',
		// 			   KOTA                   = '".$this->getField("KOTA")."',
		// 			   KODEPOS                = '".$this->getField("KODEPOS")."',
		// 			   REGION_ID              = '".$this->getField("REGION_ID")."',
		// 			   WEBSITE                = '".$this->getField("WEBSITE")."',
		// 			   KONTAK_PERSON_HP       = '".$this->getField("KONTAK_PERSON_HP")."',
		// 			   KONTAK_PERSON          = '".$this->getField("KONTAK_PERSON")."',
		// 			   BANK_ID                = '".$this->getField("BANK_ID")."',
		// 			   BANK_REKENING          = '".$this->getField("BANK_REKENING")."',
		// 			   BANK_PEMILIK           = '".$this->getField("BANK_PEMILIK")."',
		// 			   PAYMENT_METHOD_ID	  = '".$this->getField("PAYMENT_METHOD_ID")."',
		// 			   MATA_UANG_KODE		  = '".$this->getField("MATA_UANG_KODE")."',
		// 			   BANK_CABANG		  = '".$this->getField("BANK_CABANG")."'
		// 		WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'

		// 	 ";
					   // REGION_ID              = '".$this->getField("REGION_ID")."',
		
		// $str = "
		// 		UPDATE  REKANAN
		// 		SET
		// 			   REKANAN_TIPE_ID        = '".$this->getField("REKANAN_TIPE_ID")."',
		// 			   REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."',
		// 			   NAMA                   = '".$this->getField("NAMA")."',
		// 			   NPWP                   = '".$this->getField("NPWP")."',
		// 			   NPWP_FILE              = '".$this->getField("NPWP_FILE")."',
		// 			   NAMA_FILE_NPWP         = '".$this->getField("NAMA_FILE_NPWP")."',
		// 			   PKP                    = '".$this->getField("PKP")."',
		// 			   PKP_FILE               = '".$this->getField("PKP_FILE")."',
		// 			   NAMA_FILE_PKP          = '".$this->getField("NAMA_FILE_PKP")."',
		// 			   PKP_TANGGAL            = ".$this->getField("PKP_TANGGAL").",
		// 			   ALAMAT                 = '".$this->getField("ALAMAT")."',
		// 			   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
		// 			   TELEPON                = '".$this->getField("TELEPON")."',
		// 			   FAX_KODE               = '".$this->getField("FAX_KODE")."',
		// 			   FAX                    = '".$this->getField("FAX")."',
		// 			   EMAIL                  = '".$this->getField("EMAIL")."',
		// 			   ALAMAT_PUSAT           = '".$this->getField("ALAMAT_PUSAT")."',
		// 			   TELEPON_KODE_PUSAT     = '".$this->getField("TELEPON_KODE_PUSAT")."',
		// 			   TELEPON_PUSAT          = '".$this->getField("TELEPON_PUSAT")."',
		// 			   FAX_KODE_PUSAT         = '".$this->getField("FAX_KODE_PUSAT")."',
		// 			   FAX_PUSAT              = '".$this->getField("FAX_PUSAT")."',
		// 			   EMAIL_PUSAT            = '".$this->getField("EMAIL_PUSAT")."',
		// 			   STATUS_PERUSAHAAN      = '".$this->getField("STATUS_PERUSAHAAN")."',
		// 			   KOTA                   = '".$this->getField("KOTA")."',
		// 			   KODEPOS                = '".$this->getField("KODEPOS")."',
		// 			   WEBSITE                = '".$this->getField("WEBSITE")."',
		// 			   KONTAK_PERSON_HP       = '".$this->getField("KONTAK_PERSON_HP")."',
		// 			   KONTAK_PERSON          = '".$this->getField("KONTAK_PERSON")."',
		// 			   BANK_ID                = '".$this->getField("BANK_ID")."',
		// 			   BANK_REKENING          = '".$this->getField("BANK_REKENING")."',
		// 			   BANK_PEMILIK           = '".$this->getField("BANK_PEMILIK")."',
		// 			   BANK_CABANG		  			= '".$this->getField("BANK_CABANG")."',
		// 			   STATUS_PKP		  				= '".$this->getField("STATUS_PKP")."',
		// 			   SKT_PKP_NOMOR		  		= '".$this->getField("SKT_PKP_NOMOR")."',
		// 			   SKT_PKP_FILE		  			= '".$this->getField("SKT_PKP_FILE")."',
		// 			   NAMA_SKT_PKP_FILE		  = '".$this->getField("NAMA_SKT_PKP_FILE")."',
		// 			   NON_PKP_FILE		  			= '".$this->getField("NON_PKP_FILE")."',
		// 			   NAMA_NON_PKP_FILE		  = '".$this->getField("NAMA_NON_PKP_FILE")."',
		// 			   COMPANY_PROFILE_FILE		= '".$this->getField("COMPANY_PROFILE_FILE")."',
		// 			   NAMAPROPINSI						= '".$this->getField("NAMAPROPINSI")."',
		// 			   NAMAKABKOTA						= '".$this->getField("NAMAKABKOT")."',
		// 			   NAMAKECAMATAN					= '".$this->getField("NAMAKECAMATAN")."',
		// 			   KELURAHAN							= '".$this->getField("KELURAHAN")."'
		// 		WHERE  REKANAN_ID           = '".$this->getField("REKANAN_ID")."'
		// 	 ";

		$str = "
				UPDATE  REKANAN
				SET
					   REKANAN_TIPE_ID        = '".$this->getField("REKANAN_TIPE_ID")."',
					   REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."',
					   NAMA                   = '".$this->getField("NAMA")."',
					   NPWP                   = '".$this->getField("NPWP")."',
					   NPWP_FILE              = '".$this->getField("NPWP_FILE")."',
					   NAMA_FILE_NPWP         = '".$this->getField("NAMA_FILE_NPWP")."', 
					   ALAMAT                 = '".$this->getField("ALAMAT")."',
					   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
					   TELEPON                = '".$this->getField("TELEPON")."',
					   FAX_KODE               = '".$this->getField("FAX_KODE")."',
					   FAX                    = '".$this->getField("FAX")."',
					   EMAIL                  = '".$this->getField("EMAIL")."',
					   ALAMAT_PUSAT           = '".$this->getField("ALAMAT_PUSAT")."',
					   TELEPON_KODE_PUSAT     = '".$this->getField("TELEPON_KODE_PUSAT")."',
					   TELEPON_PUSAT          = '".$this->getField("TELEPON_PUSAT")."',
					   FAX_KODE_PUSAT         = '".$this->getField("FAX_KODE_PUSAT")."',
					   FAX_PUSAT              = '".$this->getField("FAX_PUSAT")."',
					   EMAIL_PUSAT            = '".$this->getField("EMAIL_PUSAT")."',
					   STATUS_PERUSAHAAN      = '".$this->getField("STATUS_PERUSAHAAN")."',
					   KOTA                   = '".$this->getField("KOTA")."',
					   KODEPOS                = '".$this->getField("KODEPOS")."',
					   WEBSITE                = '".$this->getField("WEBSITE")."',
					   KONTAK_PERSON_HP       = '".$this->getField("KONTAK_PERSON_HP")."',
					   KONTAK_PERSON          = '".$this->getField("KONTAK_PERSON")."',
					   BANK_ID                = '".$this->getField("BANK_ID")."',
					   BANK_REKENING          = '".$this->getField("BANK_REKENING")."',
					   BANK_PEMILIK           = '".$this->getField("BANK_PEMILIK")."',
					   BANK_CABANG		  			= '".$this->getField("BANK_CABANG")."',
					   COMPANY_PROFILE_FILE		= '".$this->getField("COMPANY_PROFILE_FILE")."',
					   NAMAPROPINSI						= '".$this->getField("NAMAPROPINSI")."',
					   NAMAKABKOTA						= '".$this->getField("NAMAKABKOT")."',
					   NAMAKECAMATAN					= '".$this->getField("NAMAKECAMATAN")."',
					   KELURAHAN							= '".$this->getField("KELURAHAN")."',
					   UPDATED_BY             = ".$this->getField("CREATED_BY").",
					   UPDATED_DATE           = CURRENT_TIMESTAMP
				WHERE  REKANAN_ID           = '".$this->getField("REKANAN_ID")."'
			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
    }

    function updateprofileperorangan()
	{
		$str = "

				UPDATE  REKANAN
				SET
					   REKANAN_TIPE_ID        = '".$this->getField("REKANAN_TIPE_ID")."',
					   REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."',
					   NAMA                   = '".$this->getField("NAMA")."',
					   NPWP                   = '".$this->getField("NPWP")."',
					   NPWP_FILE              = '".$this->getField("NPWP_FILE")."',
					   NAMA_FILE_NPWP         = '".$this->getField("NAMA_FILE_NPWP")."',
					   KTP                    = '".$this->getField("KTP")."',
					   KTP_FILE               = '".$this->getField("KTP_FILE")."',
					   NAMA_FILE_KTP          = '".$this->getField("NAMA_FILE_KTP")."',
					   ALAMAT                 = '".$this->getField("ALAMAT")."',
					   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
					   TELEPON                = '".$this->getField("TELEPON")."',
					   FAX_KODE               = '".$this->getField("FAX_KODE")."',
					   FAX                    = '".$this->getField("FAX")."',
					   EMAIL                  = '".$this->getField("EMAIL")."',
					   ALAMAT_PUSAT           = '".$this->getField("ALAMAT_PUSAT")."',
					   TELEPON_KODE_PUSAT     = '".$this->getField("TELEPON_KODE_PUSAT")."',
					   TELEPON_PUSAT          = '".$this->getField("TELEPON_PUSAT")."',
					   FAX_KODE_PUSAT         = '".$this->getField("FAX_KODE_PUSAT")."',
					   FAX_PUSAT              = '".$this->getField("FAX_PUSAT")."',
					   EMAIL_PUSAT            = '".$this->getField("EMAIL_PUSAT")."',
					   KOTA                   = '".$this->getField("KOTA")."',
					   KODEPOS                = '".$this->getField("KODEPOS")."',
					   REGION_ID              = '".$this->getField("REGION_ID")."',
					   WEBSITE                = '".$this->getField("WEBSITE")."',
					   KONTAK_PERSON_HP       = '".$this->getField("KONTAK_PERSON_HP")."',
					   KONTAK_PERSON          = '".$this->getField("KONTAK_PERSON")."',
					   PKP                    = '".$this->getField("PKP")."',
					   PKP_FILE               = '".$this->getField("PKP_FILE")."',
					   NAMA_FILE_PKP          = '".$this->getField("NAMA_FILE_PKP")."',
					   PKP_TANGGAL          = ".$this->getField("PKP_TANGGAL")."
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'

			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
    }

    function updateprofileperorangan2()
	{
		$str = "

				UPDATE  REKANAN
				SET
					   REKANAN_TIPE_ID        = '".$this->getField("REKANAN_TIPE_ID")."',
					   REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."',
					   NAMA                   = '".$this->getField("NAMA")."',
					   NPWP                   = '".$this->getField("NPWP")."',
					   NPWP_FILE              = '".$this->getField("NPWP_FILE")."',
					   NAMA_FILE_NPWP         = '".$this->getField("NAMA_FILE_NPWP")."',
					   KTP                    = '".$this->getField("KTP")."',
					   KTP_FILE               = '".$this->getField("KTP_FILE")."',
					   NAMA_FILE_KTP          = '".$this->getField("NAMA_FILE_KTP")."',
					   ALAMAT                 = '".$this->getField("ALAMAT")."',
					   TELEPON_KODE           = '".$this->getField("TELEPON_KODE")."',
					   TELEPON                = '".$this->getField("TELEPON")."',
					   FAX_KODE               = '".$this->getField("FAX_KODE")."',
					   FAX                    = '".$this->getField("FAX")."',
					   EMAIL                  = '".$this->getField("EMAIL")."',
					   ALAMAT_PUSAT           = '".$this->getField("ALAMAT_PUSAT")."',
					   TELEPON_KODE_PUSAT     = '".$this->getField("TELEPON_KODE_PUSAT")."',
					   TELEPON_PUSAT          = '".$this->getField("TELEPON_PUSAT")."',
					   FAX_KODE_PUSAT         = '".$this->getField("FAX_KODE_PUSAT")."',
					   FAX_PUSAT              = '".$this->getField("FAX_PUSAT")."',
					   EMAIL_PUSAT            = '".$this->getField("EMAIL_PUSAT")."',
					   KOTA                   = '".$this->getField("KOTA")."',
					   KODEPOS                = '".$this->getField("KODEPOS")."',
					   WEBSITE                = '".$this->getField("WEBSITE")."',
					   KONTAK_PERSON_HP       = '".$this->getField("KONTAK_PERSON_HP")."',
					   KONTAK_PERSON          = '".$this->getField("KONTAK_PERSON")."',
					   BANK_ID                = '".$this->getField("BANK_ID")."',
					   BANK_REKENING          = '".$this->getField("BANK_REKENING")."',
					   BANK_PEMILIK           = '".$this->getField("BANK_PEMILIK")."',
					   BANK_CABANG		  			= '".$this->getField("BANK_CABANG")."',
					   NAMAPROPINSI						= '".$this->getField("NAMAPROPINSI")."',
					   NAMAKABKOTA						= '".$this->getField("NAMAKABKOT")."',
					   NAMAKECAMATAN					= '".$this->getField("NAMAKECAMATAN")."',
					   KELURAHAN							= '".$this->getField("KELURAHAN")."'
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'

			 ";
				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
    }

	function update_kualifikasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = " UPDATE  REKANAN
				 SET     REKANAN_KUALIFIKASI_ID = '".$this->getField("REKANAN_KUALIFIKASI_ID")."'
				 WHERE   REKANAN_ID             = '".$this->getField("REKANAN_ID")."'
			 ";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function update_cv()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = " UPDATE  REKANAN
				 SET     CV_FILE = '".$this->getField("CV_FILE")."',
				 		 NAMA_FILE_CV = '".$this->getField("NAMA_FILE_CV")."'
				 WHERE   REKANAN_ID             = '".$this->getField("REKANAN_ID")."'
			 ";
			 // echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
    }

	function update_pkp_delete()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  REKANAN
				SET    NPWP                   = '',
					   PKP                    = '',
					   PKP_TANGGAL            = NULL
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'

			 ";
				$this->query = $str;

		return $this->execQuery($str);
    }

    function update_pkp()
  	{
  		$str = "
  				UPDATE  REKANAN
  				SET
  					   PKP                    = '".$this->getField("PKP")."',
  					   PKP_TANGGAL            = ".$this->getField("PKP_TANGGAL").",
  					   NON_PKP_FILE          	= '".$this->getField("NON_PKP_FILE")."',
  					   NAMA_NON_PKP_FILE      = '".$this->getField("NAMA_NON_PKP_FILE")."',
  					   STATUS_PKP      				= '".$this->getField("STATUS_PKP")."',
  					   SKT_PKP_NOMOR          = '".$this->getField("SKT_PKP_NOMOR")."',
  					   SKT_PKP_FILE          	= '".$this->getField("SKT_PKP_FILE")."',
  					   NAMA_SKT_PKP_FILE      = '".$this->getField("NAMA_SKT_PKP_FILE")."',
  					   PKP_FILE          			= '".$this->getField("PKP_FILE")."',
  					   NAMA_FILE_PKP          = '".$this->getField("NAMA_FILE_PKP")."',
  					   UPDATED_BY             = ".$this->getField("CREATED_BY").",
  					   UPDATED_DATE           = CURRENT_TIMESTAMP
  				WHERE  REKANAN_ID           = '".$this->getField("REKANAN_ID")."'

  			 ";
  				$this->query = $str;
  		return $this->execQuery($str);
      }

	function updateAlasan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  REKANAN
				SET
					   ALASAN_HAPUS			  = '".$this->getField("ALASAN_HAPUS")."',
					   TANGGAL_HAPUS		  = CURRENT_DATE,
					   STATUS_VALIDASI 		  = 2
				WHERE  REKANAN_ID             = '".$this->getField("REKANAN_ID")."'

			 ";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM REKANAN
                WHERE
                  REKANAN_ID = ".$this->getField("REKANAN_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","IJIN_USAHA_ID"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.KODE DESC")
	{
		$str = "
			SELECT A.REKANAN_ID, U.USER_STATUS,
                   IJIN_USAHA_ID,
                   A.REKANAN_TIPE_ID,
                   A.REKANAN_KUALIFIKASI_ID,
                   B.NAMA REKANAN_KUALIFIKASI,
                   A.KODE,
                   A.NAMA REKANAN_NAMA,
                   C.NAMA || ' ' || A.NAMA NAMA,
                   A.NPWP,
                   A.ALAMAT,
                   A.TELEPON_KODE,
                   A.TELEPON,
                   A.TELEPON_KODE || TELEPON TELEPON_FULL,
                   A.FAX_KODE,
                   A.FAX,
				   A.FAX_KODE || FAX FAX_FULL,
                   A.EMAIL,
                   A.STATUS_VALIDASI,
                   A.USER_VALIDASI,
                   A.KTP,
                   A.KTP_FILE,
                   A.NAMA_FILE_KTP,
                   A.CV_FILE,
                   A.NAMA_FILE_CV,
                   A.BANK_CABANG,
                   A.STATUS_PKP,
                   A.SKT_PKP_NOMOR,
                   A.SKT_PKP_FILE,
                   A.NAMA_SKT_PKP_FILE,
                   ALAMAT_PUSAT,
                   TELEPON_KODE_PUSAT,
                   TELEPON_PUSAT,
                   FAX_KODE_PUSAT,
                   FAX_PUSAT,
                   EMAIL_PUSAT,
                   STATUS_PERUSAHAAN,
				   CASE WHEN STATUS_PERUSAHAAN = 0 THEN 'Pusat'
					WHEN STATUS_PERUSAHAAN = 1 THEN 'Cabang'
					WHEN STATUS_PERUSAHAAN = 2 THEN 'Join Operation'
				  ELSE 'Join Operation' END STATUS_CP,
                   TO_CHAR(TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR,
                   TO_CHAR(TANGGAL_VALIDASI, 'YYYY-MM-DD') TANGGAL_VALIDASI,
                   TANGGAL_VALIDASI TANGGAL_VALIDASI2,
                   STATUS_CP AS STATUS,
                   PKP,
                   TO_CHAR(PKP_TANGGAL, 'YYYY-MM-DD')PKP_TANGGAL,
                   KOTA,
                   SURAT_KUASA, SURAT_KUASA_TANGGAL, SURAT_KUASA_NOTARIS,
                   TO_CHAR(TANGGAL_HAPUS, 'YYYY-MM-DD')TANGGAL_HAPUS,
                   ALASAN_HAPUS,
                   WHATSAPP,
                   WHATSAPP_VALIDASI,
                   KODEPOS,
                   A.REGION_ID, A.BANK_ID, BANK_REKENING, BANK_PEMILIK, A.BANK_CABANG, A.NOTE_1, A.NOTE_2, A.NOTE_3, A.DATE_VALIDASI_1, A.DATE_VALIDASI_2, A.DATE_VALIDASI_3,
                   D.NAMA REGION, E.NAMA BANK, A.INCOTERM_ID, F.INCOTERM_ID || ' - ' || F.NAMA INCOTERM1, INCOTERM2,
                   A.PAYMENT_METHOD_ID, COALESCE(MATA_UANG_KODE, 'IDR') MATA_UANG_KODE,
                   G.NAMA PAYMENT_METHOD, A.SAP_KODE, A.PKP, TO_CHAR(A.PKP_TANGGAL, 'YYYY-MM-DD')PKP_TANGGAL, A.PKP_FILE, A.NPWP_FILE,
				   A.NAMA_FILE_PKP, A.NAMA_FILE_NPWP, C.NAMA NAMA_TIPE,
				CASE WHEN A.REKANAN_KUALIFIKASI_ID = 1 THEN 'Kecil'
			    		 WHEN A.REKANAN_KUALIFIKASI_ID = 2 THEN 'Non Kecil'
			     		 ELSE 'Kecil/Non Kecil'
					END KUALIFIKASI,  KONTAK_PERSON, KONTAK_PERSON_HP, WEBSITE, NON_PKP_FILE, NAMA_NON_PKP_FILE, COMPANY_PROFILE_FILE, NAMAPROPINSI, NAMAKABKOTA, NAMAKECAMATAN, KELURAHAN, KODEWILAYAH
              FROM REKANAN A
              LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
              LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
              LEFT JOIN REGION D ON A.REGION_ID = D.REGION_ID
              LEFT JOIN BANK E ON A.BANK_ID = E.BANK_ID
              LEFT JOIN INCOTERM F ON A.INCOTERM_ID = F.INCOTERM_ID
              LEFT JOIN PAYMENT_METHOD G ON A.PAYMENT_METHOD_ID = G.PAYMENT_METHOD_ID
              JOIN USER_LOGIN U ON A.REKANAN_ID=U.REKANAN_ID
             WHERE     A.REKANAN_ID IS NOT NULL
				";

        while(list($key,$val) = each($paramsArray))
    		{
    			// ikn 20190218
    		    $pecah = explode("||", $key);
    		    if (count($pecah) > 1) {
    		        $str .= "AND $pecah[0] $pecah[1] $val ";
    		    } else {
    		        $str .= " AND $key = '$val' ";
    		    }
    		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsURLValidasiAllow($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="")
  	{
  		$str = "
  			SELECT A.*
                FROM REKANAN_URL_VALIDASI_ALLOW A
               WHERE 1=1
  				";

  		while(list($key,$val) = each($paramsArray))
  		{
  			$str .= " AND $key = '$val' ";
  		}

  		$str .= $statement." ".$order;
  		 //echo $str; die();
  		$this->query = $str;
  		return $this->selectLimit($str,$limit,$from);
      }

  function selectByParams2($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.NAMA ASC")
	{
		$str = "
			SELECT A.REKANAN_ID,
                   IJIN_USAHA_ID,
                   A.REKANAN_TIPE_ID,
                   A.REKANAN_KUALIFIKASI_ID,
                   B.NAMA REKANAN_KUALIFIKASI,
                   A.KODE,
                   A.NAMA REKANAN_NAMA,
                   C.NAMA || ' ' || A.NAMA NAMA,
                   A.NPWP,
                   A.ALAMAT,
                   A.TELEPON_KODE,
                   A.TELEPON,
                   A.TELEPON_KODE || TELEPON TELEPON_FULL,
                   A.FAX_KODE,
                   A.FAX,
				   A.FAX_KODE || FAX FAX_FULL,
                   A.EMAIL,
                   A.STATUS_VALIDASI,
                   A.USER_VALIDASI,
                   H.USER_STATUS,
                   H.USER_AKTIF,
                   H.USER_LOGIN_ID,
                   A.KTP,
                   A.KTP_FILE,
                   A.NAMA_FILE_KTP,
                   A.CV_FILE,
                   A.NAMA_FILE_CV,
                   ALAMAT_PUSAT,
                   TELEPON_KODE_PUSAT,
                   TELEPON_PUSAT,
                   FAX_KODE_PUSAT,
                   FAX_PUSAT,
                   EMAIL_PUSAT,
                   STATUS_PERUSAHAAN,
                   STATUS_VALIDASI,
				   CASE WHEN STATUS_PERUSAHAAN = 0 THEN 'Pusat'
					WHEN STATUS_PERUSAHAAN = 1 THEN 'Cabang'
					WHEN STATUS_PERUSAHAAN = 2 THEN 'Join Operation'
				  ELSE 'Join Operation' END STATUS_CP,
                   TO_CHAR(TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR,
                   TO_CHAR(TANGGAL_VALIDASI, 'YYYY-MM-DD') TANGGAL_VALIDASI,
                   TANGGAL_VALIDASI TANGGAL_VALIDASI2,
                   STATUS_CP AS STATUS,
                   PKP,
                   TO_CHAR(PKP_TANGGAL, 'YYYY-MM-DD')PKP_TANGGAL,
                   KOTA,
                   SURAT_KUASA, SURAT_KUASA_TANGGAL, SURAT_KUASA_NOTARIS,
                   TO_CHAR(TANGGAL_HAPUS, 'YYYY-MM-DD')TANGGAL_HAPUS,
                   ALASAN_HAPUS,
                   WHATSAPP,
                   WHATSAPP_VALIDASI,
                   KODEPOS,
                   A.REGION_ID, A.BANK_ID, BANK_REKENING, BANK_PEMILIK,
                   D.NAMA REGION, E.NAMA BANK, A.INCOTERM_ID, F.INCOTERM_ID || ' - ' || F.NAMA INCOTERM1, INCOTERM2,
                   A.PAYMENT_METHOD_ID, COALESCE(MATA_UANG_KODE, 'IDR') MATA_UANG_KODE,
                   G.NAMA PAYMENT_METHOD, A.SAP_KODE, A.PKP, TO_CHAR(A.PKP_TANGGAL, 'YYYY-MM-DD')PKP_TANGGAL, A.PKP_FILE, A.NPWP_FILE,
				   A.NAMA_FILE_PKP, A.NAMA_FILE_NPWP, C.NAMA NAMA_TIPE,
				CASE WHEN A.REKANAN_KUALIFIKASI_ID = 1 THEN 'Kecil'
			    		 WHEN A.REKANAN_KUALIFIKASI_ID = 2 THEN 'Non Kecil'
			     		 ELSE 'Kecil/Non Kecil'
					END KUALIFIKASI,  KONTAK_PERSON, KONTAK_PERSON_HP, WEBSITE, COMPANY_PROFILE_FILE
              FROM REKANAN A
              LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
              LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
              LEFT JOIN REGION D ON A.REGION_ID = D.REGION_ID
              LEFT JOIN BANK E ON A.BANK_ID = E.BANK_ID
              LEFT JOIN INCOTERM F ON A.INCOTERM_ID = F.INCOTERM_ID
              LEFT JOIN PAYMENT_METHOD G ON A.PAYMENT_METHOD_ID = G.PAYMENT_METHOD_ID
              LEFT JOIN USER_LOGIN H ON A.REKANAN_ID = H.REKANAN_ID
             WHERE     A.REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

   function selectByParamsAll($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="ORDER BY A.NAMA ASC")
	{
		$str = "
			SELECT A.REKANAN_ID,
                   IJIN_USAHA_ID,
                   A.REKANAN_TIPE_ID,
                   A.REKANAN_KUALIFIKASI_ID,
                   B.NAMA REKANAN_KUALIFIKASI,
                   A.KODE,
                   A.NAMA REKANAN_NAMA,
                   C.NAMA || ' ' || A.NAMA NAMA,
                   A.NPWP,
                   A.ALAMAT,
                   A.TELEPON_KODE,
                   A.TELEPON,
                   A.TELEPON_KODE || TELEPON TELEPON_FULL,
                   A.FAX_KODE,
                   A.FAX,
				   A.FAX_KODE || FAX FAX_FULL,
                   A.EMAIL,
                   A.STATUS_VALIDASI,
                   A.USER_VALIDASI,
                   U.USER_STATUS,
                   U.USER_AKTIF,
                   U.USER_LOGIN_ID,
                   A.KTP,
                   A.KTP_FILE,
                   A.NAMA_FILE_KTP,
                   A.CV_FILE,
                   A.NAMA_FILE_CV,
				   A.UPDATED_DATE,
                   ALAMAT_PUSAT,
                   TELEPON_KODE_PUSAT,
                   TELEPON_PUSAT,
                   FAX_KODE_PUSAT,
                   FAX_PUSAT,
                   EMAIL_PUSAT,
                   STATUS_PERUSAHAAN,
                   STATUS_VALIDASI,
				   CASE WHEN STATUS_PERUSAHAAN = 0 THEN 'Pusat'
					WHEN STATUS_PERUSAHAAN = 1 THEN 'Cabang'
					WHEN STATUS_PERUSAHAAN = 2 THEN 'Join Operation'
				  ELSE 'Join Operation' END STATUS_CP,
                   TO_CHAR(TANGGAL_DAFTAR, 'YYYY-MM-DD') TANGGAL_DAFTAR,
                   TO_CHAR(TANGGAL_VALIDASI, 'YYYY-MM-DD') TANGGAL_VALIDASI,
                   TANGGAL_VALIDASI TANGGAL_VALIDASI2,
                   STATUS_CP AS STATUS,
                   PKP,
                   TO_CHAR(PKP_TANGGAL, 'YYYY-MM-DD')PKP_TANGGAL,
                   KOTA,
                   SURAT_KUASA, SURAT_KUASA_TANGGAL, SURAT_KUASA_NOTARIS,
                   TO_CHAR(TANGGAL_HAPUS, 'YYYY-MM-DD')TANGGAL_HAPUS,
                   ALASAN_HAPUS,
                   WHATSAPP,
                   WHATSAPP_VALIDASI,
                   KODEPOS,
                   A.REGION_ID, A.BANK_ID, BANK_REKENING, BANK_PEMILIK,
                   D.NAMA REGION, E.NAMA BANK, A.INCOTERM_ID, F.INCOTERM_ID || ' - ' || F.NAMA INCOTERM1, INCOTERM2,
                   A.PAYMENT_METHOD_ID, COALESCE(MATA_UANG_KODE, 'IDR') MATA_UANG_KODE,
                   G.NAMA PAYMENT_METHOD, A.SAP_KODE, A.PKP, TO_CHAR(A.PKP_TANGGAL, 'YYYY-MM-DD')PKP_TANGGAL, A.PKP_FILE, A.NPWP_FILE,
				   A.NAMA_FILE_PKP, A.NAMA_FILE_NPWP, C.NAMA NAMA_TIPE,
				CASE WHEN A.REKANAN_KUALIFIKASI_ID = 1 THEN 'Kecil'
			    		 WHEN A.REKANAN_KUALIFIKASI_ID = 2 THEN 'Non Kecil'
			     		 ELSE 'Kecil/Non Kecil'
					END KUALIFIKASI,  KONTAK_PERSON, KONTAK_PERSON_HP, WEBSITE, COMPANY_PROFILE_FILE
              FROM REKANAN A
              LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
              LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
              LEFT JOIN REGION D ON A.REGION_ID = D.REGION_ID
              LEFT JOIN BANK E ON A.BANK_ID = E.BANK_ID
              LEFT JOIN INCOTERM F ON A.INCOTERM_ID = F.INCOTERM_ID
              LEFT JOIN PAYMENT_METHOD G ON A.PAYMENT_METHOD_ID = G.PAYMENT_METHOD_ID
              LEFT JOIN USER_LOGIN U ON A.REKANAN_ID = U.REKANAN_ID
             WHERE     A.REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsInformasi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT REKANAN_ID,
				   IJIN_USAHA_ID,
				   A.REKANAN_TIPE_ID,
				   A.REKANAN_KUALIFIKASI_ID,
				   B.NAMA REKANAN_KUALIFIKASI,
				   A.KODE,
				   A.NAMA REKANAN_NAMA,
				   C.NAMA || ' ' || A.NAMA NAMA,
				   NPWP,
				   ALAMAT,
				   TELEPON_KODE,
				   TELEPON,
				   TELEPON_KODE || TELEPON TELEPON_FULL,
				   FAX_KODE,
				   FAX,
				   EMAIL,
				   ALAMAT_PUSAT,
				   TELEPON_KODE_PUSAT,
				   TELEPON_PUSAT,
				   FAX_KODE_PUSAT,
				   FAX_PUSAT,
				   EMAIL_PUSAT,
				   STATUS_PERUSAHAAN,
				   STATUS_VALIDASI,
				   DECODE (STATUS_PERUSAHAAN,  0, 'Pusat',  1, 'Cabang', 2, 'Join Operation') STATUS_CP,
				   TANGGAL_DAFTAR,
				   TANGGAL_VALIDASI,
				   STATUS_CP AS STATUS,
				   PKP,
				   TO_CHAR(PKP_TANGGAL, 'YYYY-MM-DD')PKP_TANGGAL,
				   KOTA,
				   SURAT_KUASA, SURAT_KUASA_TANGGAL, SURAT_KUASA_NOTARIS,
				   TANGGAL_HAPUS,
				   ALASAN_HAPUS,
                   WHATSAPP,
                   WHATSAPP_VALIDASI,
				   D.NAMA DIRUT,
				   D.JABATAN DIRUT_JABATAN,
				   A.KODEPOS,
				   A.SAP_KODE
			  FROM REKANAN A LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
										 LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
										 LEFT JOIN REKANAN_PENGURUS_DIRUT D ON A.REKANAN_ID = D.REKANAN_ID
			 WHERE     A.REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY A.NAMA ASC";
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsCari($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.REKANAN_ID,
				   IJIN_USAHA_ID,
				   A.REKANAN_TIPE_ID,
				   A.REKANAN_KUALIFIKASI_ID,
				   B.NAMA REKANAN_KUALIFIKASI,
				   A.KODE,
				   A.NAMA REKANAN_NAMA,
				   C.NAMA || ' ' || A.NAMA NAMA,
				   NPWP,
				   ALAMAT,
				   TELEPON_KODE,
				   TELEPON,
				   TELEPON_KODE || TELEPON TELEPON_FULL,
				   FAX_KODE,
				   FAX,
				   EMAIL,
				   ALAMAT_PUSAT,
				   TELEPON_KODE_PUSAT,
				   TELEPON_PUSAT,
				   FAX_KODE_PUSAT,
				   FAX_PUSAT,
				   EMAIL_PUSAT,
				   STATUS_PERUSAHAAN,
				   STATUS_VALIDASI,
				   CASE WHEN STATUS_PERUSAHAAN = 0 THEN 'Pusat'
						WHEN STATUS_PERUSAHAAN = 1 THEN 'Cabang'
				   ELSE 'Join Operation' END STATUS_CP,
				   TANGGAL_DAFTAR,
				   TANGGAL_VALIDASI,
				   STATUS_CP AS STATUS,
				   PKP,
				   PKP_TANGGAL,
				   KOTA,
				   SURAT_KUASA, SURAT_KUASA_TANGGAL, SURAT_KUASA_NOTARIS,
				   TANGGAL_HAPUS,
				   ALASAN_HAPUS,
                   WHATSAPP,
                   WHATSAPP_VALIDASI,
				   D.NAMA DIRUT,
				   D.JABATAN DIRUT_JABATAN,
				   A.KODEPOS,
				   A.SAP_KODE
			  FROM REKANAN A LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
										 LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
										 LEFT JOIN REKANAN_PENGURUS_DIRUT D ON A.REKANAN_ID = D.REKANAN_ID
			 WHERE     A.REKANAN_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY REKANAN_ID ASC";
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsSimple($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT A.REKANAN_ID, A.KODE, C.NAMA || ' ' || A.NAMA NAMA, A.EMAIL, D.JABATAN, D.NAMA NAMA_DIREKTUR, D.JABATAN JABATAN_PENGURUS, A.ALAMAT, A.TELEPON, A.REKANAN_TIPE_ID
			  FROM REKANAN A LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
			  LEFT JOIN REKANAN_PENGURUS_DIREKTUR D ON A.REKANAN_ID=D.REKANAN_ID
			 WHERE     A.REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY A.REKANAN_ID ASC";
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

  function selectByParamsRekananChecklist($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="")
	{
		$str = " SELECT A.* FROM REKANAN_CHECKLIST A WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsRekananChecklistCekKelengkapan($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order="")
	{
		$str = " SELECT 
    rekananid,

    -- jumlah field yang terisi
    (npwp_note IS NOT NULL AND npwp_note <> '' AND npwp ='0')::int +
    (nib_note IS NOT NULL AND nib_note <> '' AND nib ='0')::int +
    (akta_note IS NOT NULL AND akta_note <> '' AND akta ='0')::int +
    (pengurus_note IS NOT NULL AND pengurus_note <> '' AND pengurus ='0')::int +
    (saham_note IS NOT NULL AND saham_note <> '' AND saham ='0')::int +
    (sbu_note IS NOT NULL AND sbu_note <> '' AND sbu ='0')::int +
    (rekening_koran_note IS NOT NULL AND rekening_koran_note <> '' AND rekening_koran ='0')::int +
    (neraca_note IS NOT NULL AND neraca_note <> '' AND neraca ='0')::int +
    (pkp_note IS NOT NULL AND pkp_note <> '' AND pkp ='0')::int +
    (spt_tahunan_note IS NOT NULL AND spt_tahunan_note <> '' AND spt_tahunan ='0')::int +
    (pph_note IS NOT NULL AND pph_note <> '' AND pph ='0')::int +
    (ppn_note IS NOT NULL AND ppn_note <> '' AND ppn ='0')::int +
    (tenaga_ahli_note IS NOT NULL AND tenaga_ahli_note <> '' AND tenaga_ahli ='0')::int +
    (pengalaman_note IS NOT NULL AND pengalaman_note <> '' AND pengalaman ='0')::int +
    (peralatan_note IS NOT NULL AND peralatan_note <> '' AND peralatan ='0')::int +
    (teknis_lain_note IS NOT NULL AND teknis_lain_note <> '' AND teknis_lain ='0')::int +
    (cv_note IS NOT NULL AND cv_note <> '' AND cv_note ='0')::int +
    (ktp_note IS NOT NULL AND ktp_note <> '' AND ktp ='0')::int 
    AS jumlah_terisi,
    -- gabungan semua catatan + label field
    CONCAT_WS(', ',
        CASE WHEN npwp_note IS NOT NULL AND npwp_note <> '' THEN 'NPWP: ' || npwp_note END,
        CASE WHEN nib_note IS NOT NULL AND nib_note <> '' THEN 'NIB: ' || nib_note END,
        CASE WHEN akta_note IS NOT NULL AND akta_note <> '' THEN 'AKTA: ' || akta_note END,
        CASE WHEN pengurus_note IS NOT NULL AND pengurus_note <> '' THEN 'PENGURUS: ' || pengurus_note END,
        CASE WHEN saham_note IS NOT NULL AND saham_note <> '' THEN 'SAHAM: ' || saham_note END,
        CASE WHEN sbu_note IS NOT NULL AND sbu_note <> '' THEN 'SBU: ' || sbu_note END,
        CASE WHEN rekening_koran_note IS NOT NULL AND rekening_koran_note <> '' THEN 'REKENING: ' || rekening_koran_note END,
        CASE WHEN neraca_note IS NOT NULL AND neraca_note <> '' THEN 'NERACA: ' || neraca_note END,
        CASE WHEN pkp_note IS NOT NULL AND pkp_note <> '' THEN 'PKP: ' || pkp_note END,
        CASE WHEN spt_tahunan_note IS NOT NULL AND spt_tahunan_note <> '' THEN 'SPT: ' || spt_tahunan_note END,
        CASE WHEN pph_note IS NOT NULL AND pph_note <> '' THEN 'PPH: ' || pph_note END,
        CASE WHEN ppn_note IS NOT NULL AND ppn_note <> '' THEN 'PPN: ' || ppn_note END,
        CASE WHEN tenaga_ahli_note IS NOT NULL AND tenaga_ahli_note <> '' THEN 'TENAGA AHLI: ' || tenaga_ahli_note END,
        CASE WHEN pengalaman_note IS NOT NULL AND pengalaman_note <> '' THEN 'PENGALAMAN: ' || pengalaman_note END,
        CASE WHEN peralatan_note IS NOT NULL AND peralatan_note <> '' THEN 'PERALATAN: ' || peralatan_note END,
        CASE WHEN teknis_lain_note IS NOT NULL AND teknis_lain_note <> '' THEN 'TEKNIS LAIN: ' || teknis_lain_note END,
        CASE WHEN cv_note IS NOT NULL AND cv_note <> '' THEN 'CV: ' || cv_note END,
        CASE WHEN ktp_note IS NOT NULL AND ktp_note <> '' THEN 'KTP: ' || ktp_note END
    ) AS catatan_lengkap
		FROM rekanan_checklist WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function insertCheck()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("CHECKLISTID", $this->getNextId("CHECKLISTID","REKANAN_CHECKLIST"));

		$str = "
					INSERT INTO  REKANAN_CHECKLIST (
					   CHECKLISTID, REKANANID, NPWP, NIB, AKTA, PENGURUS, SAHAM, SBU, REKENING_KORAN, NERACA, PKP, SPT_TAHUNAN,
					    PPH, PPN, TENAGA_AHLI, PENGALAMAN, PERALATAN, TEKNIS_LAIN, PAKTA, CREATED_BY, CREATED_DATE)
 			  	VALUES (
						  ".$this->getField("CHECKLISTID").",
						  ".$this->getField("REKANANID").",
						  '0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0',
						  '".$this->getField("CREATED_BY")."',
						  CURRENT_TIMESTAMP
						)";
						// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
  }

  function updateCheck()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_CHECKLIST SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."'
				WHERE REKANANID = '".$this->getField("REKANANID")."'
				";
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
  }

  function updateCheck2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE REKANAN_CHECKLIST SET
				  ".$this->getField("FIELD")." = '".$this->getField("FIELD_VALUE")."',
          ".$this->getField("FIELD2")." = '".$this->getField("FIELD_VALUE2")."',
          UPDATED_BY = ".$this->getField("CREATED_BY").", UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE REKANANID = '".$this->getField("REKANANID")."'
				";
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
  }

	// function selectByParamsKonfirmasi($rekananId)
	// {
	// 	$str = "SELECT 'Landasan Hukum (Akta Pendirian)' NAMA, CASE WHEN (SELECT COUNT(1) FROM REKANAN_AKTA WHERE REKANAN_ID = '".$rekananId."' AND AKTA_TYPE_ID = 1) > 0 THEN 'centang' ELSE 'uncentang' END SIMBOL
	// 			UNION ALL
	// 			SELECT 'Pengesahan Badan Usaha', CASE WHEN (SELECT COUNT(1) FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = '".$rekananId."' AND SERTIFIKAT_TIPE = 'PENGESAHAN_BADAN_USAHA') > 0 THEN 'centang' ELSE 'uncentang' END SIMBOL
	// 			UNION ALL
	// 			SELECT 'Surat Domisili', CASE WHEN (SELECT COUNT(1) FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = '".$rekananId."' AND SERTIFIKAT_TIPE ='SURAT_DOMISILI') > 0 THEN 'centang' ELSE 'uncentang' END SIMBOL
	// 			UNION ALL
	// 			SELECT 'Tanda Daftar Perusahaan', CASE WHEN (SELECT COUNT(1) FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = '".$rekananId."' AND SERTIFIKAT_TIPE ='TANDA_DAFTAR_PERUSAHAAN') > 0 THEN 'centang' ELSE 'uncentang' END SIMBOL
	// 			UNION ALL
	// 			SELECT 'Ijin Usaha', CASE WHEN (SELECT COUNT(1) FROM REKANAN_IJIN_USAHA WHERE REKANAN_ID = '".$rekananId."' AND IJIN_USAHA_ID NOT IN(99)) > 0 THEN 'centang' ELSE 'uncentang' END SIMBOL
	// 			UNION ALL
	// 			SELECT 'Pimpinan Perusahaan', CASE WHEN (SELECT COUNT(1) FROM REKANAN_PENGURUS WHERE REKANAN_ID = '".$rekananId."') > 0 THEN 'centang' ELSE 'uncentang' END SIMBOL
	// 			UNION ALL
	// 			SELECT 'Kepemilikan Saham', CASE WHEN (SELECT COUNT(1) FROM REKANAN_SAHAM WHERE REKANAN_ID = '".$rekananId."') > 0 THEN 'centang' ELSE 'uncentang' END SIMBOL
	// 			";

	// 	return $this->selectLimit($str, -1, -1);
 //    }

    // ikn 14 November 2019
    function selectByParamsKonfirmasiDataAdmin($rekananId)
	{
		$str = "SELECT
						'NPWP' NAMA,
						'*' WAJIB,
						'npwp' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN WHERE REKANAN_ID = '".$rekananId."' AND NPWP IS NOT NULL AND NPWP != '' AND NAMA_FILE_NPWP IS NOT NULL ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
					UNION ALL
					SELECT
						'N I B',
						'*' WAJIB,
						'nib' FIELDNYA,
					CASE
							WHEN (
							SELECT COUNT
								( 1 )
							FROM
								REKANAN_IJIN_USAHA
							WHERE
								REKANAN_ID = '".$rekananId."'
								AND (IJIN_USAHA_ID = 1 OR IJIN_USAHA_ID = 2) ) > 0 THEN
								'centang' ELSE'uncentang'
							END SIMBOL
					UNION ALL
					SELECT
						'Landasan Hukum (Akta Pendirian/perubahan)' NAMA,
						'*' WAJIB,
						'akta' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN_AKTA WHERE REKANAN_ID = '".$rekananId."' AND AKTA_TYPE_ID = 1 ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
					UNION ALL
					SELECT
						'Pengurus Perusahaan',
						'*' WAJIB,
						'pengurus' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN_PENGURUS WHERE REKANAN_ID = '".$rekananId."' AND TIPE = '2') > 0 AND ( SELECT COUNT ( 1 ) FROM REKANAN_PENGURUS WHERE REKANAN_ID = '".$rekananId."' AND TIPE = '1') > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
					UNION ALL
					SELECT
						'Kepemilikan Saham',
						'*' WAJIB,
						'saham' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN_SAHAM WHERE REKANAN_ID = '".$rekananId."' ) > 0 THEN
						'centang' ELSE'uncentang'
						END SIMBOL
					UNION ALL
					SELECT
						'Sertifikat Badan Usaha',
						'' WAJIB,
						'sbu' FIELDNYA,
					CASE
							WHEN (
							SELECT COUNT
								( 1 )
							FROM
								REKANAN_IJIN_USAHA
							WHERE
								REKANAN_ID = '".$rekananId."'
								AND IJIN_USAHA_ID = 99) > 0 THEN
								'centang' ELSE'uncentang'
							END SIMBOL
				";
				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
    }

    function selectByParamsKonfirmasiDataKeuangan($rekananId)
	{
		$str = "SELECT
						'Rekening Koran <small>(Minimal 2 Bulan)</small>' NAMA,
						'' WAJIB,
						'rekening_koran' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( REKANAN_ID ) FROM REKANAN_REKENING_KORAN WHERE REKANAN_ID = '".$rekananId."' ) >= 2 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
				UNION
				SELECT
						'Neraca' NAMA,
						'*' WAJIB,
						'neraca' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( REKANAN_ID ) FROM REKANAN_NERACA WHERE REKANAN_ID = '".$rekananId."' ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
				";
				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
    }

     function selectByParamsKonfirmasiDataPerpajakan($rekananId)
	{
		$str = "SELECT
						'PKP / Non PKP' NAMA,
						'*' WAJIB,
						'pkp' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN WHERE REKANAN_ID = '".$rekananId."' AND PKP IS NOT NULL AND PKP != '' AND NAMA_FILE_PKP IS NOT NULL OR STATUS_PKP = '0' OR STATUS_PKP = '1' ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
					UNION ALL
					SELECT
						'SPT Tahunan',
						'*' WAJIB,
						'spt_tahunan' FIELDNYA,
					CASE
							WHEN (
							SELECT COUNT
								( 1 )
							FROM
								REKANAN_PAJAK
							WHERE
								REKANAN_ID = '".$rekananId."'
								AND TIPE = '1') > 0 THEN
								'centang' ELSE'uncentang'
							END SIMBOL
					UNION ALL
					SELECT
						'Laporan Pajak Bulanan (PPN)',
						'' WAJIB,
						'ppn' FIELDNYA,
					CASE
							WHEN (
							SELECT COUNT
								( 1 )
							FROM
								REKANAN_PAJAK
							WHERE
								REKANAN_ID = '".$rekananId."'
								AND TIPE = '3') > 0 THEN
								'centang' ELSE'uncentang'
							END SIMBOL
				";
				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
    }

  function selectByParamsKonfirmasiDataTeknis($rekananId)
	{
		$str = " SELECT
						'Tenaga Ahli Tetap' NAMA,
						'' WAJIB,
						'tenaga_ahli' FIELDNYA,
					CASE
							WHEN (
							SELECT COUNT
								( 1 )
							FROM
								REKANAN_TENAGA_AHLI
							WHERE
								REKANAN_ID = '".$rekananId."') > 0 THEN
								'centang' ELSE'uncentang'
							END SIMBOL
					UNION ALL
					SELECT
						'Pengalaman <small>(3 Tahun Terakhir)</small>' NAMA,
						'*' WAJIB,
						'pengalaman' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN_PENGALAMAN WHERE REKANAN_ID = '".$rekananId."') > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
					UNION ALL
					SELECT
						'Peralatan',
						'' WAJIB,
						'peralatan' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN_PERALATAN WHERE REKANAN_ID = '".$rekananId."' ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
						UNION ALL
					SELECT
						'Dokumen Teknis Perusahaan',
						'' WAJIB,
						'teknis_lain' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( REKANAN_ID ) FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = '".$rekananId."' ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
				";

				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
  }

  function selectByParamsKonfirmasiPaktaIntegritas($rekananId)
	{
		$str = " SELECT
						'Pakta Integritas' NAMA,
						'*' WAJIB,
						'pakta' FIELDNYA,
					CASE
							WHEN (
							SELECT COUNT
								( 1 )
							FROM
								REKANAN_PAKTA_INTEGRITAS
							WHERE
								REKANAN_ID = '".$rekananId."') > 0 THEN
								'centang' ELSE'uncentang'
							END SIMBOL
				";
				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
  }

    function selectByParamsKonfirmasiPerorangan($rekananId)
	{
		$str = "SELECT
						'NPWP & KTP' NAMA,
						'*' WAJIB,
	          'npwp' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN WHERE REKANAN_ID = '".$rekananId."' AND NPWP IS NOT NULL AND NPWP != '' AND NAMA_FILE_NPWP IS NOT NULL ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
          UNION ALL
          SELECT
            'N I B' NAMA,
            '*' WAJIB,
            'nib' FIELDNYA,
          CASE
              WHEN (
              SELECT COUNT
                ( 1 )
              FROM
                REKANAN_IJIN_USAHA
              WHERE
                REKANAN_ID = '".$rekananId."'
                AND (IJIN_USAHA_ID = 1 OR IJIN_USAHA_ID = 2) ) > 0 THEN
                'centang' ELSE'uncentang'
              END SIMBOL 
						UNION ALL
				SELECT
						'CV (DAFTAR RIWAYAT HIDUP)' NAMA,
						'*' WAJIB,
            'cv' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN WHERE REKANAN_ID = '".$rekananId."' AND CV_FILE IS NOT NULL AND CV_FILE != '' AND CV_FILE IS NOT NULL ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
				";
				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
    }

    function selectByParamsKonfirmasiPeroranganDataPerpajakan($rekananId)
	{
		$str = "SELECT
						'SPT Tahunan' NAMA,
						'*' WAJIB,
						'spt_tahunan' FIELDNYA,
					CASE
							WHEN (
							SELECT COUNT
								( 1 )
							FROM
								REKANAN_PAJAK
							WHERE
								REKANAN_ID = '".$rekananId."'
								AND TIPE = '1') > 0 THEN
								'centang' ELSE'uncentang'
							END SIMBOL
				";
				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
    }

    function selectByParamsKonfirmasiPeroranganDataTeknis($rekananId)
	{
		$str = "  SELECT
						'Pengalaman Keahlian' NAMA,
						'*' WAJIB,
						'pengalaman' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( 1 ) FROM REKANAN_PENGALAMAN WHERE REKANAN_ID = '".$rekananId."') > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
					UNION ALL
					SELECT
						'Sertifikat Keahlian',
						'*' WAJIB,
						'teknis_lain' FIELDNYA,
					CASE
							WHEN ( SELECT COUNT ( REKANAN_ID ) FROM REKANAN_SERTIFIKAT WHERE REKANAN_ID = '".$rekananId."' ) > 0 THEN
							'centang' ELSE'uncentang'
						END SIMBOL
				";
				// echo $str; die();
		return $this->selectLimit($str, -1, -1);
    }

	function selectByParamsRekanan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT REKANAN_ID, NAMA, JABATAN
			  FROM REKANAN_PENGURUS_DIREKTUR
			 WHERE  1=1
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY REKANAN_ID ASC";
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function checkNpwp($npwp)
    {
        $q = "select NPWP from REKANAN where REPLACE(NPWP,'.' ,'-') = REPLACE('".$npwp."','.','-') AND NOT COALESCE(STATUS_VALIDASI, 0) = 2 ";
        $this->query = $q;
        return $this->selectLimit($q);
    }

    function selectByParamsRekananPengurusDirut($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT A.REKANAN_ID, B.NAMA, B.JABATAN,
				   C.NAMA || ' ' || A.NAMA REKANAN,
				   ALAMAT,
				   CASE WHEN FAX IS NULL THEN TELEPON_KODE || TELEPON ELSE TELEPON_KODE || TELEPON || ' / ' || FAX_KODE || FAX  END TELEPON,
				   EMAIL
			  FROM REKANAN A LEFT JOIN REKANAN_PENGURUS_DIRUT B ON A.REKANAN_ID = B.REKANAN_ID
			  LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
             WHERE  A.REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY B.NAMA ASC";
		$this->query = $str;
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsJenisUsaha($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
			SELECT A.REKANAN_ID,
				   A.IJIN_USAHA_ID,
				   A.REKANAN_TIPE_ID,
				   A.REKANAN_KUALIFIKASI_ID,
				   B.NAMA REKANAN_KUALIFIKASI,
				   A.KODE,
				   A.NAMA REKANAN_NAMA,
				   C.NAMA || ' ' || A.NAMA NAMA,
				   NPWP,
				   ALAMAT,
				   TELEPON_KODE,
				   TELEPON,
				   TELEPON_KODE || TELEPON TELEPON_FULL,
				   FAX_KODE,
				   FAX,
				   EMAIL,
				   ALAMAT_PUSAT,
				   TELEPON_KODE_PUSAT,
				   TELEPON_PUSAT,
				   FAX_KODE_PUSAT,
                   FAX_PUSAT,
                   EMAIL_PUSAT,
                   STATUS_PERUSAHAAN,
                   STATUS_VALIDASI,
                   DECODE (STATUS_PERUSAHAAN,  0, 'Pusat',  1, 'Cabang', 2, 'Join Operation') STATUS_CP,
                   TANGGAL_DAFTAR,
                   TANGGAL_VALIDASI,
                   STATUS_CP AS STATUS,
                   PKP,
                   PKP_TANGGAL,
                   KOTA,
                   SURAT_KUASA, SURAT_KUASA_TANGGAL, SURAT_KUASA_NOTARIS,
                   TANGGAL_HAPUS,
                   ALASAN_HAPUS
              FROM REKANAN A LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
                                         LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
                                         LEFT JOIN REKANAN_BIDANG_USAHA D ON  A.REKANAN_ID = D.REKANAN_ID
             WHERE     A.REKANAN_ID IS NOT NULL
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY A.NAMA ASC";
		$this->query = $str;
		//echo $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsDaftarPenilaianRekanan($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order = " ORDER BY COALESCE(B.RATA_PENILAIAN, 0) DESC")
	{
		$str = "
			SELECT A.REKANAN_ID, KODE, NAMA, JUMLAH_PENILAIAN, RATA_PENILAIAN,
				CASE
					WHEN RATA_PENILAIAN BETWEEN 90 AND 100 THEN 'SANGAT BAIK'
					WHEN RATA_PENILAIAN BETWEEN 80 AND 90 THEN 'BAIK'
					WHEN RATA_PENILAIAN BETWEEN 70 AND 80 THEN 'CUKUP'
					WHEN RATA_PENILAIAN BETWEEN 50 AND 70 THEN 'KURANG'
					WHEN RATA_PENILAIAN BETWEEN 0 AND 50 THEN 'KURANG SEKALI'
				END NAMA_PENILAIAN
				FROM REKANAN A
				LEFT JOIN (SELECT REKANAN_ID_PEMENANG REKANAN_ID, COUNT(1) JUMLAH_PENILAIAN, ROUND(SUM(REKANAN_ID_PENILAIAN) / COUNT(1), 2) RATA_PENILAIAN FROM PAKET
				GROUP BY REKANAN_ID_PEMENANG) B ON A.REKANAN_ID = B.REKANAN_ID
				WHERE 1 = 1 AND NOT EXISTS(SELECT 1 FROM BLACKLIST X WHERE X.REKANAN_ID = A.REKANAN_ID AND CURRENT_DATE BETWEEN TANGGAL_MULAI AND TANGGAL_SELESAI)
				";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement."  ". $order;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT
				REKANAN_ID, IJIN_USAHA_ID, REKANAN_TIPE_ID,
				   REKANAN_KUALIFIKASI_ID, KODE, NAMA,
				   NPWP, ALAMAT, TELEPON_KODE,
				   TELEPON, FAX_KODE, FAX,
				   EMAIL, ALAMAT_PUSAT, TELEPON_KODE_PUSAT,
				   TELEPON_PUSAT, FAX_KODE_PUSAT, FAX_PUSAT,
				   EMAIL_PUSAT, STATUS_PERUSAHAAN, STATUS_VALIDASI,
				   STATUS_CP, TANGGAL_DAFTAR, TANGGAL_VALIDASI,
				   PKP, PKP_TANGGAL, KOTA,
				   SURAT_KUASA
				FROM  REKANAN
		        WHERE REKANAN_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->query = $str;
		$str .= $statement." ORDER BY NAMA ASC";
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","IJIN_USAHA_ID"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getCountByParams($paramsArray=array(), $stat='')
	{
		$str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT
				FROM REKANAN A
				LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
				LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
				LEFT JOIN REGION D ON A.REGION_ID = D.REGION_ID
				LEFT JOIN BANK E ON A.BANK_ID = E.BANK_ID
				LEFT JOIN INCOTERM F ON A.INCOTERM_ID = F.INCOTERM_ID
				LEFT JOIN PAYMENT_METHOD G ON A.PAYMENT_METHOD_ID = G.PAYMENT_METHOD_ID
				WHERE REKANAN_ID IS NOT NULL
				AND
					  -- A.REKANAN_KUALIFIKASI_ID =  B.REKANAN_KUALIFIKASI_ID AND
					  A.REKANAN_TIPE_ID =  C.REKANAN_TIPE_ID ".$stat;

		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsAll($paramsArray=array(), $stat='')
	{
		$str = "SELECT COUNT(A.REKANAN_ID) AS ROWCOUNT
				FROM REKANAN A
				LEFT JOIN REKANAN_KUALIFIKASI B ON  A.REKANAN_KUALIFIKASI_ID = B.REKANAN_KUALIFIKASI_ID
				LEFT JOIN REKANAN_TIPE C ON  A.REKANAN_TIPE_ID = C.REKANAN_TIPE_ID
				LEFT JOIN REGION D ON A.REGION_ID = D.REGION_ID
				LEFT JOIN BANK E ON A.BANK_ID = E.BANK_ID
				LEFT JOIN INCOTERM F ON A.INCOTERM_ID = F.INCOTERM_ID
				LEFT JOIN PAYMENT_METHOD G ON A.PAYMENT_METHOD_ID = G.PAYMENT_METHOD_ID
				LEFT JOIN USER_LOGIN U ON A.REKANAN_ID = U.REKANAN_ID
				WHERE A.REKANAN_ID IS NOT NULL
				AND
					  -- A.REKANAN_KUALIFIKASI_ID =  B.REKANAN_KUALIFIKASI_ID AND
					  A.REKANAN_TIPE_ID =  C.REKANAN_TIPE_ID ".$stat;

		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(REKANAN_ID) AS ROWCOUNT FROM REKANAN WHERE REKANAN_ID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

	function getNextKode()
	{
		//SELECT MAX(CAST(SUBSTR(KODE, 5, 6) AS INT)) + 1 ROWCOUNT FROM REKANAN
		$str = "SELECT MAX(REKANAN_ID) + 1 ROWCOUNT FROM REKANAN";

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
	}
  }
?>
