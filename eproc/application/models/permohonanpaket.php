<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class PermohonanPaket extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
	    parent::__construct();
	  }
    function PermohonanPaket()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ID", $this->getNextId("PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET"));

		$str = "
			INSERT INTO PERMOHONAN_PAKET (
			   PERMOHONAN_PAKET_ID, USER_LOGIN_ID, UNIT_KERJA_ID,
			   NOTA_DINAS, NAMA, KETERANGAN, NO_PPA, TANGGAL, LAST_CREATE_USER, NILAI, PENGADAANLANGSUNG,TAHUN_ANGGARAN,CREATED_BY,CREATED_DATE)

  			 	VALUES (
				  '".$this->getField("PERMOHONAN_PAKET_ID")."',
  				  '".$this->getField("USER_LOGIN_ID")."',
   				  '".$this->getField("UNIT_KERJA_ID")."',
				  '".$this->getField("NOTA_DINAS")."',
				  '".$this->getField("NAMA")."',
  				  '".$this->getField("KETERANGAN")."',
  				  '".$this->getField("NO_PPA")."',
  				  ".$this->getField("TANGGAL").",
				  '".$this->getField("LAST_CREATE_USER")."',
				  ".$this->getField("NILAI").",
				  ".$this->getField("PENGADAANLANGSUNG").",
				  '".$this->getField("TAHUN_ANGGARAN")."',
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)";
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_ID");
		return $this->execQuery($str);
  }

  function insertv2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ID", $this->getNextId("PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET"));

		$str = "

			INSERT INTO PERMOHONAN_PAKET (
			   PERMOHONAN_PAKET_ID, USER_LOGIN_ID, UNIT_KERJA_ID,
			   NAMA, LAST_CREATE_USER, NILAI, PENGADAANLANGSUNG,TAHUN_ANGGARAN,CREATED_BY,CREATED_DATE)

  			 	VALUES (
				  '".$this->getField("PERMOHONAN_PAKET_ID")."',
  				  '".$this->getField("USER_LOGIN_ID")."',
   				  '".$this->getField("UNIT_KERJA_ID")."',
				  '".$this->getField("NAMA")."',
				  '".$this->getField("LAST_CREATE_USER")."',
				  ".$this->getField("NILAI").",
				  ".$this->getField("PENGADAANLANGSUNG").",
				  '".$this->getField("TAHUN_ANGGARAN")."',
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)";
				echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_ID");
		return $this->execQuery($str);
  }

  function insertcoa()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("COA_ID", $this->getNextId("COA_ID","PERMOHONAN_PAKET_COA"));

		$str = "
			INSERT INTO PERMOHONAN_PAKET_COA (
			   COA_ID, PERMOHONAN_PAKET_ID, NOMOR,
			   KETERANGAN, BUDGET_AWAL, BUDGET_TERPAKAI, BUDGET_AKHIR, CREATED_BY, CREATED_DATE)

  			 	VALUES (
				  '".$this->getField("COA_ID")."',
  				  '".$this->getField("PERMOHONAN_PAKET_ID")."',
   				  '".$this->getField("NOMOR")."',
				  '".$this->getField("KETERANGAN")."',
				  ".$this->getField("BUDGET_AWAL").",
				  ".$this->getField("BUDGET_TERPAKAI").",
				  ".$this->getField("BUDGET_AKHIR").",
				  ".$this->getField("CREATED_BY").",
				  CURRENT_TIMESTAMP
				)";
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("COA_ID");
		return $this->execQuery($str);
  }

  function deleteCoa()
	{
		$str = "DELETE FROM PERMOHONAN_PAKET_COA
              WHERE  PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")."";
		$this->query = $str;
  	$this->execQuery($str);
    return true;
  }

    function updateIntegrasiAIM()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  AIM_BR2.PERMOHONAN_LELANG
				SET
					   PAKET_ID    = '".$this->getField("PAKET_ID")."'
				WHERE  PERMOHONAN_LELANG_ID   =  '".$this->getField("PERMOHONAN_LELANG_ID")."'

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		// $str = "
		// 		 UPDATE  PERMOHONAN_PAKET
		// 		SET
		// 			   NOTA_DINAS  = '".$this->getField("NOTA_DINAS")."',
		// 			   NAMA      = '".$this->getField("NAMA")."',
		// 			   KETERANGAN    = '".$this->getField("KETERANGAN")."',
		// 			   NO_PPA    = '".$this->getField("NO_PPA")."',
		// 			   TANGGAL    = ".$this->getField("TANGGAL").",
		// 			   NILAI    = ".$this->getField("NILAI").",
		// 			   PENGADAANLANGSUNG    = ".$this->getField("PENGADAANLANGSUNG").",
		// 			   BUDGET_AWAL    = ".$this->getField("BUDGET_AWAL").",
		// 			   BUDGET_TERPAKAI    = ".$this->getField("BUDGET_TERPAKAI").",
		// 			   BUDGET_AKHIR    = ".$this->getField("BUDGET_AKHIR").",
		// 			   TAHUN_ANGGARAN    = '".$this->getField("TAHUN_ANGGARAN")."',
		// 			   UPDATED_BY    = ".$this->getField("CREATED_BY").",
		// 			   UPDATED_DATE    = CURRENT_TIMESTAMP
		// 		WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."
		// 		";
		$str = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   NOTA_DINAS  = '".$this->getField("NOTA_DINAS")."',
					   NAMA      = '".$this->getField("NAMA")."',
					   KETERANGAN    = '".$this->getField("KETERANGAN")."',
					   NO_PPA    = '".$this->getField("NO_PPA")."',
					   TANGGAL    = ".$this->getField("TANGGAL").",
					   NILAI    = ".$this->getField("NILAI").",
					   PENGADAANLANGSUNG    = ".$this->getField("PENGADAANLANGSUNG").",
					   TAHUN_ANGGARAN    = '".$this->getField("TAHUN_ANGGARAN")."',
					   UPDATED_BY    = ".$this->getField("CREATED_BY").",
					   UPDATED_DATE    = CURRENT_TIMESTAMP
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."
				";
				//echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }

   function updatev2()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   NAMA      = '".$this->getField("NAMA")."',
					   NILAI    = ".$this->getField("NILAI").",
					   PENGADAANLANGSUNG    = ".$this->getField("PENGADAANLANGSUNG").",
					   TAHUN_ANGGARAN    = '".$this->getField("TAHUN_ANGGARAN")."',
					   UPDATED_BY    = ".$this->getField("CREATED_BY").",
					   UPDATED_DATE    = CURRENT_TIMESTAMP
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."
				";
				//echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }

  function updatePR()
	{
		$str = "
				UPDATE  PERMOHONAN_PAKET
				SET
				KODE_PR    = '".$this->getField("KODE_PR")."',
				POSTING  	= '".$this->getField("POSTING")."',
			  POSTING_BY      = '".$this->getField("POSTING_BY")."',
			  POSTING_DATE    = CURRENT_DATE,
			  ALASAN_TOLAK		= NULL
				WHERE  KODE_RUP   =  '".$this->getField("KODE_RUP")."'
				";
				//echo $str;
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updatePROne()
	{
		$str = "
				UPDATE  PERMOHONAN_PAKET
				SET
				KODE_PR    = '".$this->getField("KODE_PR")."',
			  UPDATED_BY      = ".$this->getField("UPDATED_BY").",
			  UPDATED_DATE    = CURRENT_DATE
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."
				";
				//echo $str;
				$this->query = $str;
		return $this->execQuery($str);
  }

	function posting_permohonan()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   POSTING  	= '".$this->getField("POSTING")."',
					   POSTING_BY      = '".$this->getField("POSTING_BY")."',
					   POSTING_DATE    = CURRENT_DATE,
					   ALASAN_TOLAK		= NULL
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

				";

				$this->query = $str;
		return $this->execQuery($str);
  }

  function approvePermohonan()
  {
    $str = "
         UPDATE  PERMOHONAN_PAKET_ANALISA
        SET
             UPDATED_BY  	= ".$this->getField("UPDATED_BY").",
             UPDATED_DATE = CURRENT_DATE,
             APPROVAL = '1'
        WHERE  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
        ";

    $this->query = $str;
    if($this->execQuery($str)) {
		   // PAKET_METODE_LELANG_ID      = ".$this->getField("PAKET_METODE_LELANG_ID")."
      $str2 = "
         UPDATE  PERMOHONAN_PAKET
        SET
             UPDATED_BY  	= '".$this->getField("UPDATED_BY")."',
             UPDATED_DATE = CURRENT_DATE,
             APPROVAL = '1',
             POSTING  	= '".$this->getField("POSTING")."',
		   POSTING_BY      = '".$this->getField("POSTING_BY")."',
		   POSTING_DATE    = CURRENT_DATE,
		   ALASAN_TOLAK		= NULL,
		   PENGADAANLANGSUNG      = '".$this->getField("PENGADAANLANGSUNG")."',
		   STRATEGI_PENGADAAN      = '".$this->getField("STRATEGI_PENGADAAN")."'
        WHERE  PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")."
        ";

        $this->query = $str2;
      return $this->execQuery($str2);
    } else {
      return false;
    }

  }

  function resendPermohonan()
  {
    $str = "
         UPDATE  PERMOHONAN_PAKET_ANALISA
        SET
             UPDATED_BY  	= ".$this->getField("UPDATED_BY").",
             UPDATED_DATE = CURRENT_DATE,
             APPROVAL = '3'
        WHERE  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
        ";

    $this->query = $str;
  	return $this->execQuery($str);
  }

  function updateApprovalAnalisa()
  {
    $str = "
         UPDATE  PERMOHONAN_PAKET_ANALISA
        SET
             UPDATED_BY  	= ".$this->getField("UPDATED_BY").",
             UPDATED_DATE = CURRENT_DATE,
             APPROVAL = '".$this->getField("APPROVAL")."'
        WHERE  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
        ";

    $this->query = $str;
  	return $this->execQuery($str);
  }

  function updateApprovalAnalisaWithNote()
  {
    $str = "
         UPDATE  PERMOHONAN_PAKET_ANALISA
        SET
             UPDATED_BY  	= ".$this->getField("UPDATED_BY").",
             UPDATED_DATE = CURRENT_DATE,
             APPROVAL = '".$this->getField("APPROVAL")."',
             NOTE_KASUBDIT = '".$this->getField("NOTE_KASUBDIT")."'
        WHERE  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
        ";

    $this->query = $str;
  	return $this->execQuery($str);
  }

  	function tetapkanmetode()
	{
				   // PAKET_METODE_LELANG_ID      = ".$this->getField("PAKET_METODE_LELANG_ID")."
		$str = "UPDATE  PERMOHONAN_PAKET
				SET
				   PENGADAANLANGSUNG      = '".$this->getField("PENGADAANLANGSUNG")."',
				   STRATEGI_PENGADAAN      = '".$this->getField("STRATEGI_PENGADAAN")."'
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."
				";

		$this->query = $str;

      if($this->execQuery($str)) {
        $str2 = "
             UPDATE  PERMOHONAN_PAKET_ANALISA
            SET
                 UPDATED_BY  	= '".$this->getField("UPDATED_BY")."',
                 UPDATED_DATE = CURRENT_DATE,
                 APPROVAL = '6'
            WHERE  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
            ";

            $this->query = $str2;
            // echo $str;exit;
        return $this->execQuery($str2);
			} else {
				return false;
			}

  	}

	function tunjuk_pic()
	{
		$str = "UPDATE  PERMOHONAN_PAKET
				SET
					   PIC  	= '".$this->getField("PIC")."',
					   PIC_BY      = '".$this->getField("PIC_BY")."', 
					   PIC_DATE    = CURRENT_DATE
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

				";

				$this->query = $str;

     	return $this->execQuery($str);

  	}

	function approval()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   APPROVAL  	= '1'
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

				";

				$this->query = $str;

		return $this->execQuery($str);
    }

	function kembali()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   ALASAN_TOLAK  	= '".$this->getField("ALASAN_TOLAK")."',
					   ALASAN_TOLAK_BY      = '".$this->getField("ALASAN_TOLAK_BY")."',
					   ALASAN_TOLAK_DATE    = CURRENT_DATE,
					   PIC    = NULL
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

				";

				$this->query = $str;

		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PERMOHONAN_PAKET
                WHERE
                  PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")."";

		$this->query = $str;
		//echo $str;exit;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PERMOHONAN_PAKET_METODE_EVALUASI_ID"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',$order="ORDER BY A.PERMOHONAN_PAKET_ID DESC")
	{
 		$str = "SELECT
					A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID, A.POSTING, A.PENGADAANLANGSUNG, A.TAHUN_ANGGARAN,
			   		A.NOTA_DINAS, A.NAMA, A.KETERANGAN, A.NO_PPA, A.TANGGAL, date_part('month'::text, A.TANGGAL) AS BULAN, date_part('year'::text, A.TANGGAL) AS TAHUN_PERMOHONAN, A.PENGADAANLANGSUNG, C.NAMA UNIT_KERJA, E.USER_NAMA USER_LOGIN, F.USER_TYPE_ID,
                    CASE WHEN D.PAKET_ID IS NULL THEN 0 ELSE 1 END STATUS,
					CASE WHEN (A.PIC IS NULL OR A.PIC IS NULL) THEN 0 ELSE 1 END STATUS_PIC,
					CASE WHEN A.POSTING = '1' THEN
					CASE WHEN D.PAKET_ID IS NULL THEN 'Paket belum diproses' ELSE '<a onclick=\"top.location.href = ''main/index/paket_detil/?eid=' || D.PAKET_ID || '&key=' || D.PAKET_UUID || '''\">Detil Paket</a>' END
					ELSE
						CASE WHEN COALESCE(NULLIF (A.ALASAN_TOLAK, ''), 'X') = 'X'
							THEN
								'Paket belum diposting'
						ELSE
								'Permohonan Paket Dikembalikan'
						END
					END
					STATUS_KETERANGAN,
					MD5(A.PERMOHONAN_PAKET_ID || 'EPROC') PERMOHONAN_PAKET_ID_ENCRYPT, A.NILAI, A.LAST_CREATE_USER, E.DEPARTMENT DEPARTEMEN,A.ALASAN_TOLAK, F.NIPP, F.NAMA NAMA_PIC, A.PIC, J.SK_PANITIA_ID,
						G.APPROVAL, G.PUBLISH, G.NAMA_KEBUTUHAN, G.PERMOHONAN_PAKET_ANALISA_ID, G.CREATED_BY,
						A.PERKIRAAN_BIAYA_HARGA, E.VP_PENGADAAN, E.ADMIN_RUP, A.BUDGET_AWAL, A.BUDGET_TERPAKAI, A.BUDGET_AKHIR, A.PAKET_ID_ULANG, A.PERMOHONAN_PAKET_ID_PARENT, A.KODE_RUP, A.KODE_PR, A.RENCANA_PENGADAAN, H.BOQ_FILE, A.SIRUP_ID, A.NILAI_RAB_PR, A.NILAI_HPS_PR, A.STRATEGI_PENGADAAN, G.NOTE_KASUBDIT, A.PAKET_METODE_LELANG_ID, A.KAJI_ULANG, A.TANGGAL_WAKTU_PELAKSANAAN, A.LOKASI_PEKERJAAN, A.JENIS_KONTRAK, A.KODE_SIRUP_LKPP, I.NAMA_JENIS_PEKERJAAN, A.PENGADAAN_BYPASS
					FROM PERMOHONAN_PAKET A
					LEFT JOIN USER_LOGIN E ON A.USER_LOGIN_ID = E.USER_LOGIN_ID 
			          LEFT JOIN UNIT_KERJA C ON A.UNIT_KERJA_ID = C.UNIT_KERJA_ID
			          LEFT JOIN PAKET D ON A.PERMOHONAN_PAKET_ID = D.PERMOHONAN_PAKET_ID
					LEFT JOIN V_OAUTH_USER F ON F.USER_LOGIN_ID = A.PIC
					LEFT JOIN PERMOHONAN_PAKET_ANALISA G ON A.PERMOHONAN_PAKET_ANALISA_ID=G.PERMOHONAN_PAKET_ANALISA_ID
					LEFT JOIN PAKET_PENAWARAN H ON A.PERMOHONAN_PAKET_ID = H.PERMOHONAN_PAKET_ID
					LEFT JOIN IMPORT_SIRUP I ON A.SIRUP_ID=I.ID
					LEFT JOIN PANITIA J ON F.NIPP=J.NIP
				    WHERE A.PERMOHONAN_PAKET_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;


		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsBypass($paramsArray=array(),$limit=-1,$from=-1, $statement='',$order="ORDER BY A.PERMOHONAN_PAKET_ID DESC")
	{
 		$str = "SELECT
					A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID, A.POSTING, A.PENGADAANLANGSUNG, A.TAHUN_ANGGARAN,
			   		A.NOTA_DINAS, A.NAMA, A.KETERANGAN, A.NO_PPA, A.TANGGAL, date_part('month'::text, A.TANGGAL) AS BULAN, date_part('year'::text, A.TANGGAL) AS TAHUN_PERMOHONAN, A.PENGADAANLANGSUNG, C.NAMA UNIT_KERJA, E.USER_NAMA USER_LOGIN, F.USER_TYPE_ID,
                    CASE WHEN D.PAKET_ID IS NULL THEN 0 ELSE 1 END STATUS,
					CASE WHEN (A.PIC IS NULL OR A.PIC IS NULL) THEN 0 ELSE 1 END STATUS_PIC,
					CASE WHEN A.POSTING = '1' THEN
					CASE WHEN D.PAKET_ID IS NULL THEN 'Paket belum diproses' ELSE '<a onclick=\"top.location.href = ''kontrak/index/paket_detil_bypass/?eid=' || D.PAKET_ID || '&key=' || D.PAKET_UUID || '''\">Detil Paket</a>' END
					ELSE
						CASE WHEN COALESCE(NULLIF (A.ALASAN_TOLAK, ''), 'X') = 'X'
							THEN
								'Paket belum diposting'
						ELSE
								'Permohonan Paket Dikembalikan'
						END
					END
					STATUS_KETERANGAN,
					MD5(A.PERMOHONAN_PAKET_ID || 'EPROC') PERMOHONAN_PAKET_ID_ENCRYPT, A.NILAI, A.LAST_CREATE_USER, E.DEPARTMENT DEPARTEMEN,A.ALASAN_TOLAK, F.NIPP, F.NAMA NAMA_PIC, A.PIC, J.SK_PANITIA_ID,
						G.APPROVAL, G.PUBLISH, G.NAMA_KEBUTUHAN, G.PERMOHONAN_PAKET_ANALISA_ID, G.CREATED_BY,
						A.PERKIRAAN_BIAYA_HARGA, E.VP_PENGADAAN, E.ADMIN_RUP, A.BUDGET_AWAL, A.BUDGET_TERPAKAI, A.BUDGET_AKHIR, A.PAKET_ID_ULANG, A.PERMOHONAN_PAKET_ID_PARENT, A.KODE_RUP, A.KODE_PR, A.RENCANA_PENGADAAN, H.BOQ_FILE, A.SIRUP_ID, A.NILAI_RAB_PR, A.NILAI_HPS_PR, A.STRATEGI_PENGADAAN, G.NOTE_KASUBDIT, A.PAKET_METODE_LELANG_ID, A.KAJI_ULANG, A.TANGGAL_WAKTU_PELAKSANAAN, A.LOKASI_PEKERJAAN, A.JENIS_KONTRAK, A.KODE_SIRUP_LKPP, I.NAMA_JENIS_PEKERJAAN, A.PENGADAAN_BYPASS, D.APPROVE_PPK
					FROM PERMOHONAN_PAKET A
					LEFT JOIN USER_LOGIN E ON A.USER_LOGIN_ID = E.USER_LOGIN_ID 
			          LEFT JOIN UNIT_KERJA C ON A.UNIT_KERJA_ID = C.UNIT_KERJA_ID
			          LEFT JOIN PAKET D ON A.PERMOHONAN_PAKET_ID = D.PERMOHONAN_PAKET_ID
					LEFT JOIN V_OAUTH_USER F ON F.USER_LOGIN_ID = A.PIC
					LEFT JOIN PERMOHONAN_PAKET_ANALISA G ON A.PERMOHONAN_PAKET_ANALISA_ID=G.PERMOHONAN_PAKET_ANALISA_ID
					LEFT JOIN PAKET_PENAWARAN H ON A.PERMOHONAN_PAKET_ID = H.PERMOHONAN_PAKET_ID
					LEFT JOIN IMPORT_SIRUP I ON A.SIRUP_ID=I.ID
					LEFT JOIN PANITIA J ON F.NIPP=J.NIP
				    WHERE A.PERMOHONAN_PAKET_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;


		return $this->selectLimit($str,$limit,$from);
  }

  function selectUpdateHPS($paramsArray=array(),$limit=-1,$from=-1, $statement='',$order="")
	{
 		$str = " SELECT a.permohonan_paket_id, a.kode_pr, a.nilai_hps_pr, a.nilai_rab_pr, b.approval
				FROM permohonan_paket a 
				JOIN permohonan_paket_analisa b on a.permohonan_paket_analisa_id=b.permohonan_paket_analisa_id
				WHERE b.approval != '1' AND b.approval != '6' and a.nilai_rab_pr is not null";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;


		return $this->selectLimit($str,$limit,$from);
  }

  function sumHpsByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='',$order="ORDER BY A.PERMOHONAN_PAKET_ID DESC")
	{
		$str = "SELECT SUM(A.NILAI) TOTAL_HPS FROM (
					SELECT
					A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID, A.POSTING, A.PENGADAANLANGSUNG, A.TAHUN_ANGGARAN,
			   		A.NOTA_DINAS, A.NAMA, A.KETERANGAN, A.NO_PPA, A.TANGGAL, date_part('month'::text, A.TANGGAL) AS BULAN, date_part('year'::text, A.TANGGAL) AS TAHUN_PERMOHONAN, A.PENGADAANLANGSUNG, C.NAMA UNIT_KERJA, E.USER_NAMA USER_LOGIN, F.USER_TYPE_ID,
                    CASE WHEN D.PAKET_ID IS NULL THEN 0 ELSE 1 END STATUS,
					CASE WHEN (A.PIC IS NULL OR A.PIC = '') THEN 0 ELSE 1 END STATUS_PIC,
					CASE WHEN A.POSTING = '1' THEN
					CASE WHEN D.PAKET_ID IS NULL THEN 'Paket belum diproses' ELSE '<a onclick=\"top.location.href = ''main/index/paket_detil/?reqId=' || D.PAKET_ID || '''\">Detil Paket</a>' END
					ELSE
						CASE WHEN COALESCE(NULLIF (A.ALASAN_TOLAK, ''), 'X') = 'X'
							THEN
								'Paket belum diposting'
						ELSE
								'Permohonan Paket Dikembalikan'
						END
					END
					STATUS_KETERANGAN,
					MD5(A.PERMOHONAN_PAKET_ID || 'EPROC') PERMOHONAN_PAKET_ID_ENCRYPT, A.NILAI, A.LAST_CREATE_USER, E.DEPARTMENT DEPARTEMEN,A.ALASAN_TOLAK, F.NIPP, F.NAMA NAMA_PIC,
						G.APPROVAL, G.PUBLISH, G.NAMA_KEBUTUHAN, G.PERMOHONAN_PAKET_ANALISA_ID, G.CREATED_BY,
						A.PERKIRAAN_BIAYA_HARGA, E.VP_PENGADAAN, E.ADMIN_RUP, A.BUDGET_AWAL, A.BUDGET_TERPAKAI, A.BUDGET_AKHIR, A.PAKET_ID_ULANG, A.PERMOHONAN_PAKET_ID_PARENT, A.KODE_RUP, A.KODE_PR, A.RENCANA_PENGADAAN, H.BOQ_FILE
					FROM PERMOHONAN_PAKET A
					LEFT JOIN USER_LOGIN E ON A.USER_LOGIN_ID = E.USER_LOGIN_ID
--           LEFT JOIN V_OAUTH_USER B ON E.NIP = B.NIPP
          LEFT JOIN UNIT_KERJA C ON A.UNIT_KERJA_ID = C.UNIT_KERJA_ID
          LEFT JOIN PAKET D ON A.PERMOHONAN_PAKET_ID = D.PERMOHONAN_PAKET_ID
					LEFT JOIN V_OAUTH_USER F ON F.NIPP = A.PIC
					LEFT JOIN PERMOHONAN_PAKET_ANALISA G ON A.PERMOHONAN_PAKET_ANALISA_ID=G.PERMOHONAN_PAKET_ANALISA_ID
					LEFT JOIN PAKET_PENAWARAN H ON A.PERMOHONAN_PAKET_ID = H.PERMOHONAN_PAKET_ID
				    WHERE A.PERMOHONAN_PAKET_ID IS NOT NULL ";

					while(list($key,$val) = each($paramsArray))
					{
						$str .= " AND $key = '$val' ";
					}

					$str .= $statement." ".$order;
		$str .= ' ) A';
		// echo $str; die();
		$this->query = $str;


		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsCoa($paramsArray=array(),$limit=-1,$from=-1, $statement='',$order="ORDER BY A.COA_ID ASC")
	{
		$str = "
					SELECT A.*
					FROM PERMOHONAN_PAKET_COA A
					JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
			    WHERE A.PERMOHONAN_PAKET_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

  function selectMonth($paramsArray=array(),$limit=-1,$from=-1, $statement='',$order="ORDER BY MONTH_ID ASC")
	{
		$str = "SELECT A.* FROM MONTH A";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

  function selectPermohonanForUpdateHPS($paramsArray=array(),$limit=-1,$from=-1, $statement='',$order="")
	{
		$str = "SELECT PERMOHONAN_PAKET, NAMA, SIRUP_ID, NILAI_RAB_PR, NILAI_HPS_PR
			   FROM PERMOHONAN_PAKET WHERE NILAI_RAB_PR IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ".$order;
		// echo $str; die();
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from);
  }

    function selectByParamsIntegrasiAIM($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					 SELECT PERMOHONAN_LELANG_ID PERMOHONAN_PAKET_ID, NULL USER_LOGIN_ID,  A.CABANG_ID UNIT_KERJA_ID, NOTA_DINAS, A.NAMA, A.KETERANGAN, NULL NO_PPA, A.TANGGAL,
                    B.NAMA UNIT_KERJA, NULL USER_LOGIN,  CASE WHEN PAKET_ID IS NULL THEN 0 ELSE 1 END STATUS,
                    CASE WHEN C.PAKET_ID IS NULL THEN 'Paket belum diproses' ELSE '<a onclick=\"top.location.href = ''main/?pg=ebc1536e4e96c8379aa257db020a8eef&reqId=' || C.PAKET_ID || '''\">Detil Paket</a>' END STATUS_KETERANGAN,
                     MD5(A.PERMOHONAN_LELANG_ID || 'EPROC') PERMOHONAN_PAKET_ID_ENCRYPT,
					 (SELECT SUM(NILAI_ANGGARAN) FROM AIM_BR2.REAL_SUB_PROGRAM_LELANG X WHERE X.PERMOHONAN_LELANG_ID = A.PERMOHONAN_LELANG_ID) NILAI
                    FROM AIM_BR2.PERMOHONAN_LELANG A
                    INNER JOIN AIM_BR2.CABANG B ON A.CABANG_ID = B.CABANG_ID
                    LEFT JOIN PAKET C ON A.PERMOHONAN_LELANG_ID = C.PERMOHONAN_PAKET_ID
					WHERE 1 = 1 AND A.EPROC = 'AUTO' ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;
		$str .= $statement." ORDER BY A.TANGGAL DESC";

		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsPermohonanLelang($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "  SELECT PERMOHONAN_PAKET_ID, PERMOHONAN_PAKET_ANALISA_ID, ALASAN_TOLAK, ALASAN_TOLAK_BY, ALASAN_TOLAK_DATE, PIC, USER_LOGIN_ID, A.UNIT_KERJA_ID, TANGGAL, NOTA_DINAS, PENGADAANLANGSUNG, A.TAHUN_ANGGARAN, A.NAMA, KETERANGAN, NO_PPA, LAST_CREATE_USER, LAST_CREATE_DATE, B.NAMA UNIT_KERJA, A.NILAI, A.BUDGET_AWAL, A.BUDGET_TERPAKAI, A.BUDGET_AKHIR, KODE_RUP, KODE_PR, SIRUP_ID, STRATEGI_PENGADAAN
				  FROM PERMOHONAN_PAKET A
				  LEFT JOIN UNIT_KERJA B ON A.UNIT_KERJA_ID = B.UNIT_KERJA_ID
				  WHERE 1=1";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; 
		$this->query = $str;
		$str .= $statement." ORDER BY A.TANGGAL DESC";

		return $this->selectLimit($str,$limit,$from);
    }

    function selectByParamsSimpleIntegrasi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT
                    A.PERMOHONAN_LELANG_ID PERMOHONAN_PAKET_ID, NULL USER_LOGIN_ID, A.CABANG_ID UNIT_KERJA_ID,
                       A.NOTA_DINAS, A.NAMA, A.KETERANGAN, NULL NO_PPA, A.TANGGAL
                    FROM AIM_BR2.PERMOHONAN_LELANG A
                    WHERE A.PERMOHONAN_LELANG_ID IS NOT NULL AND A.EPROC = 'AUTO' ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;

		$str .= $statement." ORDER BY A.PERMOHONAN_LELANG_ID DESC";


		return $this->selectLimit($str,$limit,$from);
    }


    function selectByParamsSimple($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT
					A.PERMOHONAN_PAKET_ID, A.USER_LOGIN_ID, A.UNIT_KERJA_ID,
			   		A.NOTA_DINAS, A.NAMA, A.KETERANGAN, A.NO_PPA, A.TANGGAL
					FROM PERMOHONAN_PAKET A
				    WHERE A.PERMOHONAN_PAKET_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$this->query = $str;

		$str .= $statement." ORDER BY A.PERMOHONAN_PAKET_ID DESC";


		return $this->selectLimit($str,$limit,$from);
    }


    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_ID) AS ROWCOUNT
				FROM PERMOHONAN_PAKET A
					LEFT JOIN USER_LOGIN E ON A.USER_LOGIN_ID = E.USER_LOGIN_ID
                    LEFT JOIN V_OAUTH_USER B ON E.NIP = B.NIPP
                    LEFT JOIN UNIT_KERJA C ON A.UNIT_KERJA_ID = C.UNIT_KERJA_ID
                    LEFT JOIN PAKET D ON A.PERMOHONAN_PAKET_ID = D.PERMOHONAN_PAKET_ID
					LEFT JOIN V_OAUTH_USER F ON F.USER_LOGIN_ID = A.PIC
					LEFT JOIN PERMOHONAN_PAKET_ANALISA G ON A.PERMOHONAN_PAKET_ANALISA_ID=G.PERMOHONAN_PAKET_ANALISA_ID
					LEFT JOIN PANITIA J ON F.NIPP=J.NIP
				    WHERE 1=1
					".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsIntegrasiAIM($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_LELANG_ID) AS ROWCOUNT
					FROM AIM_BR2.PERMOHONAN_LELANG A
                    INNER JOIN AIM_BR2.CABANG B ON A.CABANG_ID = B.CABANG_ID
                    LEFT JOIN PAKET C ON A.PERMOHONAN_LELANG_ID = C.PERMOHONAN_PAKET_ID
					WHERE 1 = 1 AND A.EPROC = 'AUTO' ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    /* ------------------------------------------------------------------
    	------------------------------------------------------------------
		------------------------------------------------------------------
    						Permohonan Paket Usulan
		------------------------------------------------------------------
		------------------------------------------------------------------
		------------------------------------------------------------------
    ------------------------------------------------------------------ */
    function insertAnalisa()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ANALISA_ID", $this->getNextId("PERMOHONAN_PAKET_ANALISA_ID","PERMOHONAN_PAKET_ANALISA"));

		$str = "
			INSERT INTO PERMOHONAN_PAKET_ANALISA (
			   PERMOHONAN_PAKET_ANALISA_ID, TAHUN_ANGGARAN, CREATED_BY, CREATED_DATE,PUBLISH,APPROVAL,POSTING,POSTING_BY,POSTING_DATE,PERMOHONAN_PAKET_ANALISA_KATEGORI_ID,SUMBER_DANA_KETERANGAN)
  			 	VALUES (
				  '".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."',
				  '".$this->getField("TAHUN_ANGGARAN")."',
          '".$this->getField("CREATED_BY")."',
          '".$this->getField("CREATED_DATE")."',
          '0',
				  '3',
				  '1',
          '".$this->getField("CREATED_BY")."',
          '".$this->getField("CREATED_DATE")."',
				  ".$this->getField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID").",
          '".$this->getField("SUMBER_DANA_KETERANGAN")."'
				)";
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_ANALISA_ID");
		return $this->execQuery($str);
    }

    function updateAnalisa()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		// $str = "
		// 		 UPDATE  PERMOHONAN_PAKET_ANALISA
		// 		SET
		// 			   TAHUN_ANGGARAN  = '".$this->getField("TAHUN_ANGGARAN")."',
		// 			   NAMA_KEBUTUHAN    = '".$this->getField("NAMA_KEBUTUHAN")."',
		// 			   ANALISA_KEBUTUHAN_ID    = '".$this->getField("ANALISA_KEBUTUHAN_ID")."',
		// 			   IDENTIFIKASI_RESIKO    = ".$this->getField("IDENTIFIKASI_RESIKO").",
		// 			   IDENTIFIKASI_RESIKO_KETERANGAN    = '".$this->getField("IDENTIFIKASI_RESIKO_KETERANGAN")."',
		// 			   UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
		// 			   UPDATED_DATE    = '".$this->getField("UPDATED_DATE")."',
		// 			   PERMOHONAN_PAKET_ANALISA_KATEGORI_ID    = ".$this->getField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID").",
		// 			   PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID    = ".$this->getField("PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID").",
		// 			   NOTE    = '".$this->getField("NOTE")."',
		// 			   SUMBER_DANA_KETERANGAN    = '".$this->getField("SUMBER_DANA_KETERANGAN")."'
		// 		WHERE  PERMOHONAN_PAKET_ANALISA_ID   =  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
		// 		";
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   TAHUN_ANGGARAN  = '".$this->getField("TAHUN_ANGGARAN")."',
					   UPDATED_BY    = '".$this->getField("UPDATED_BY")."',
					   UPDATED_DATE    = '".$this->getField("UPDATED_DATE")."',
					   PERMOHONAN_PAKET_ANALISA_KATEGORI_ID    = ".$this->getField("PERMOHONAN_PAKET_ANALISA_KATEGORI_ID").",
					   NOTE    = '".$this->getField("NOTE")."',
					   SUMBER_DANA_KETERANGAN    = '".$this->getField("SUMBER_DANA_KETERANGAN")."'
				WHERE  PERMOHONAN_PAKET_ANALISA_ID   =  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
				";
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
    }

    function insertPermohonan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ID", $this->getNextId("PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET"));

		$str = "
			INSERT INTO PERMOHONAN_PAKET (
			   PERMOHONAN_PAKET_ID, PERMOHONAN_PAKET_ANALISA_ID, NAMA, TAHUN_ANGGARAN,
			   JENIS_BARANG_JASA, PERKIRAAN_BIAYA_HARGA, NILAI, WAKTU_PENGGUNA_BARANGJASA, RENCANA_PENGADAAN, LAST_CREATE_USER, LAST_CREATE_DATE, USER_LOGIN_ID, UNIT_KERJA_ID,CREATED_BY,CREATED_DATE,SIRUP_ID,KODE_PR,KODE_RUP,NILAI_RAB_PR)

  			 	VALUES (
				  '".$this->getField("PERMOHONAN_PAKET_ID")."',
  				  '".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."',
  				  '".$this->getField("NAMA")."',
  				  '".$this->getField("TAHUN_ANGGARAN")."',
  				  '".$this->getField("JENIS_BARANG_JASA")."',
  				  '".$this->getField("PERKIRAAN_BIAYA_HARGA")."',
  				  '".$this->getField("NILAI")."',
  				  '".$this->getField("WAKTU_PENGGUNA_BARANGJASA")."',
  				  '".$this->getField("RENCANA_PENGADAAN")."',
  				  '".$this->getField("LAST_CREATE_USER")."',
  				  '".$this->getField("LAST_CREATE_DATE")."',
  				  '".$this->getField("USER_LOGIN_ID")."',
				  ".$this->getField("UNIT_KERJA_ID").",
            	  ".$this->getField("USER_LOGIN_ID").",
  				  CURRENT_TIMESTAMP,
		            ".$this->getField("SIRUP_ID").",
		            '".$this->getField("KODE_PR")."',
		            '".$this->getField("KODE_RUP")."',
		            ".$this->getField("NILAI_RAB_PR")."
				)";
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_ID");
		return $this->execQuery($str);
    }

  function updatePermohonan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   NAMA  = '".$this->getField("NAMA")."',
					   TAHUN_ANGGARAN      = '".$this->getField("TAHUN_ANGGARAN")."',
					   ANGGARAN    = '".$this->getField("ANGGARAN")."',
					   JENIS_BARANG_JASA    = '".$this->getField("JENIS_BARANG_JASA")."',
					   PERKIRAAN_BIAYA_HARGA    = '".$this->getField("PERKIRAAN_BIAYA_HARGA")."',
					   NILAI    = '".$this->getField("NILAI")."',
					   WAKTU_PENGGUNA_BARANGJASA    = ".$this->getField("WAKTU_PENGGUNA_BARANGJASA").",
					   RENCANA_PENGADAAN    = ".$this->getField("RENCANA_PENGADAAN").",
					   LAST_CREATE_USER    = '".$this->getField("LAST_CREATE_USER")."',
					   LAST_CREATE_DATE    = '".$this->getField("LAST_CREATE_DATE")."',
					   CARA_PENGADAAN    = '".$this->getField("CARA_PENGADAAN")."'
				WHERE  PERMOHONAN_PAKET_ANALISA_ID   =  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."

				";
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
  }

  function insertPermohonanAnggaran2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ANGGARAN2_ID", $this->getNextId("PERMOHONAN_PAKET_ANGGARAN2_ID","PERMOHONAN_PAKET_ANGGARAN2"));

		$str = "

			INSERT INTO PERMOHONAN_PAKET_ANGGARAN2 (
			   PERMOHONAN_PAKET_ANGGARAN2_ID, PERMOHONAN_PAKET_ID, INTEGRATION_IMPORT_RKA_BUDGET_ID, DEPARTMENT,
			   SEGMENT2_DESC, SEGMENT3_DESC, SEGMENT4_DESC, SEGMENT5_DESC, BUDGET_AMT, REMAIN_AMT, CREATED_BY, CREATED_DATE)

  			 	VALUES (
				  	'".$this->getField("PERMOHONAN_PAKET_ANGGARAN2_ID")."',
  				  ".$this->getField("PERMOHONAN_PAKET_ID").",
  				  ".$this->getField("INTEGRATION_IMPORT_RKA_BUDGET_ID").",
  				  '".$this->getField("DEPARTMENT")."',
  				  '".$this->getField("SEGMENT2_DESC")."',
  				  '".$this->getField("SEGMENT3_DESC")."',
  				  '".$this->getField("SEGMENT4_DESC")."',
  				  '".$this->getField("SEGMENT5_DESC")."',
  				  ".$this->getField("BUDGET_AMT").",
  				  ".$this->getField("REMAIN_AMT").",
  				  '".$this->getField("CREATED_BY")."',
				  	CURRENT_TIMESTAMP
				)";
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_ANGGARAN2_ID");
		return $this->execQuery($str);
  }

  function updatePermohonanAnggaran2()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANGGARAN2
				SET
					   INTEGRATION_IMPORT_RKA_BUDGET_ID  = ".$this->getField("INTEGRATION_IMPORT_RKA_BUDGET_ID").",
					   DEPARTMENT    = '".$this->getField("DEPARTMENT")."',
					   SEGMENT2_DESC    = '".$this->getField("SEGMENT2_DESC")."',
					   SEGMENT3_DESC    = '".$this->getField("SEGMENT3_DESC")."',
					   SEGMENT4_DESC    = '".$this->getField("SEGMENT4_DESC")."',
					   SEGMENT5_DESC    = '".$this->getField("SEGMENT5_DESC")."',
					   BUDGET_AMT    = ".$this->getField("BUDGET_AMT").",
					   REMAIN_AMT    = ".$this->getField("REMAIN_AMT").",
					   CREATED_DATE    = CURRENT_TIMESTAMP
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

				";
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
  }

  function insertPermohonanAnggaran()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PERMOHONAN_PAKET_ANGGARAN_ID", $this->getNextId("PERMOHONAN_PAKET_ANGGARAN_ID","PERMOHONAN_PAKET_ANGGARAN"));

		$str = "

			INSERT INTO PERMOHONAN_PAKET_ANGGARAN (
			   PERMOHONAN_PAKET_ANGGARAN_ID, PERMOHONAN_PAKET_ID, MATA_ANGGARAN, KEGIATAN,
			   SUMBER_DANA, BUDGET_REMAINING, DEPARTMENT, DEPARTMENT_CODE, KODE_MATA_ANGGARAN, KODE_KEGIATAN, TOTAL_BUDGET, TIPE_TRANSAKSI, CREATED_BY, CREATED_DATE)

  			 	VALUES (
				  	'".$this->getField("PERMOHONAN_PAKET_ANGGARAN_ID")."',
  				  ".$this->getField("PERMOHONAN_PAKET_ID").",
  				  '".$this->getField("MATA_ANGGARAN")."',
  				  '".$this->getField("KEGIATAN")."',
  				  '".$this->getField("SUMBER_DANA")."',
  				  ".$this->getField("BUDGET_REMAINING").",
  				  '".$this->getField("DEPARTMENT")."',
  				  '".$this->getField("DEPARTMENT_CODE")."',
  				  '".$this->getField("KODE_MATA_ANGGARAN")."',
  				  '".$this->getField("KODE_KEGIATAN")."',
  				  ".$this->getField("TOTAL_BUDGET").",
  				  '".$this->getField("TIPE_TRANSAKSI")."',
  				  '".$this->getField("CREATED_BY")."',
				  	CURRENT_TIMESTAMP
				)";
				// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PERMOHONAN_PAKET_ANGGARAN_ID");
		return $this->execQuery($str);
  }

 //  function updatePermohonanAnggaran()
	// {
	// 	/*Auto-generate primary key(s) by next max value (integer) */
	// 	$str = "
	// 			 UPDATE  PERMOHONAN_PAKET_ANGGARAN
	// 			SET
	// 				   MATA_ANGGARAN  = '".$this->getField("MATA_ANGGARAN")."',
	// 				   KEGIATAN      = '".$this->getField("KEGIATAN")."',
	// 				   SUMBER_DANA    = '".$this->getField("SUMBER_DANA")."',
	// 				   BUDGET_REMAINING    = ".$this->getField("BUDGET_REMAINING").",
	// 				   DEPARTMENT    = '".$this->getField("DEPARTMENT")."',
	// 				   DEPARTMENT_CODE    = '".$this->getField("DEPARTMENT_CODE")."',
	// 				   KODE_MATA_ANGGARAN    = '".$this->getField("KODE_MATA_ANGGARAN")."',
	// 				   KODE_KEGIATAN    = '".$this->getField("KODE_KEGIATAN")."',
	// 				   TOTAL_BUDGET    = ".$this->getField("TOTAL_BUDGET").",
	// 				   TIPE_TRANSAKSI    = '".$this->getField("TIPE_TRANSAKSI")."',
	// 				   CREATED_DATE    = CURRENT_TIMESTAMP
	// 			WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

	// 			";
	// 			// echo $str; die;
	// 			$this->query = $str;
	// 	return $this->execQuery($str);
 //  }

  function updatePermohonanPIC()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   USER_LOGIN_ID  = '".$this->getField("USER_LOGIN_ID")."',
					   LAST_CREATE_USER    = '".$this->getField("LAST_CREATE_USER")."',
					   LAST_CREATE_DATE    = '".$this->getField("LAST_CREATE_DATE")."'
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

				";
				//echo $str;
				$this->query = $str;
		return $this->execQuery($str);
  }

  function approveKajiulang()
	{
		$str = "	UPDATE  PERMOHONAN_PAKET
				SET
			   	KAJI_ULANG  = '".$this->getField("KAJI_ULANG")."',
			   	KAJI_ULANG_END_DATE    = CURRENT_TIMESTAMP,
			   	UPDATED_BY  	= '".$this->getField("CREATED_BY")."',
			   	UPDATED_DATE = CURRENT_DATE
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."

				";
				//echo $str;
				$this->query = $str;
		return $this->execQuery($str);
  }

  function updateHPS()
	{
		$str = "	UPDATE  PERMOHONAN_PAKET
				SET
			   	NILAI_HPS_PR  = ".$this->getField("NILAI_HPS_PR").",
			   	NILAI_RAB_PR  = ".$this->getField("NILAI_RAB_PR").",
			   	NILAI_MATA_UANG  = '".$this->getField("NILAI_MATA_UANG")."'
				WHERE  PERMOHONAN_PAKET_ID   =  ".$this->getField("PERMOHONAN_PAKET_ID")."
				";
				//echo $str;
				$this->query = $str;
		return $this->execQuery($str);
  }

  function selectByParamsUsulanView($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY PERMOHONAN_PAKET_ANALISA_ID ASC")
	{
		$str = "SELECT A.* FROM VIEW_ANALISA_DAN_PERMOHONAN_PAKET A WHERE 1=1 ";
		while(list($key,$val) = each($paramsArray))
		{
	    $pecah = explode("||", $key);
	    if (count($pecah) > 1) {
        $str .= "AND $pecah[0] $pecah[1] $val ";
	    } else {
        $str .= " AND $key = '$val' ";
	    }
		}

		$str .= $statement." ".$order;
		// echo $str; die;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
	}


    function selectByParamsUsulan($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID DESC")
	{
		$str = "SELECT A.* FROM
					(
					SELECT
					A.PERMOHONAN_PAKET_ANALISA_ID, I.USER_LOGIN_ID, A.TAHUN_ANGGARAN, A.NAMA_KEBUTUHAN, M.STATUS, M.PERMOHONAN_PAKET_ANALISA_MATRIX_ID AS STATUS_ID, A.POSTING, A.NOTE,
			   		A.ANALISA_KEBUTUHAN_ID, G.AK_NAMA, A.ANALISA_PASAR_ID, H.AP_NAMA, A.IDENTIFIKASI_RESIKO, A.IDENTIFIKASI_RESIKO_KETERANGAN, E.USER_NAMA PEMBUAT, EE.NAMA USER_TYPE_ID_STR, E.USER_JABATAN,  E.ADMIN_RUP, E.VP_PENGADAAN, E.DEPARTMENT, A.CREATED_BY, A.CREATED_DATE TANGGAL_BUAT, F.USER_NAMA PEJABAT_BERWENANG, A.ALASAN_TOLAK, A.ALASAN_TOLAK_BY, A.ALASAN_TOLAK_DATE, A.APPROVAL, A.PUBLISH, A.PUBLISH_DATE,
					MD5(A.PERMOHONAN_PAKET_ANALISA_ID || 'EPROC') PERMOHONAN_PAKET_ANALISA_ID_ENCRYPT,
					I.ANGGARAN, I.JENIS_BARANG_JASA, J.NAMA AS JENIS_BARANG_JASA_STR,
					I.PERKIRAAN_BIAYA_HARGA, I.WAKTU_PENGGUNA_BARANGJASA, I.RENCANA_PENGADAAN, I.CARA_PENGADAAN,	I.UNIT_KERJA_ID, I.PIC, UU.USER_NAMA PIC_NAMA, I.NILAI, I.KODE_RUP, I.KODE_PR, I.NO_PPA, I.TANGGAL,
					CASE WHEN I.CARA_PENGADAAN = '1' THEN 'Swakelola'
						 WHEN I.CARA_PENGADAAN = '2' THEN 'Penyedia'
						 WHEN I.CARA_PENGADAAN = '3' THEN 'Purchasing'
					ELSE I.CARA_PENGADAAN END CARA_PENGADAAN_STR, I.NAMA, A.PERMOHONAN_PAKET_ANALISA_KATEGORI_ID AS KATEGORI, K.NAMA KATEGORI_STR, A.PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID AS JENIS_BELANJA, L.NAMA JENIS_BELANJA_STR, I.PERMOHONAN_PAKET_ID,
					(SELECT USER_LOGIN_ID FROM USER_LOGIN WHERE USER_TYPE_ID=22 AND VALIDATOR_UNIT=1) AS VALIDATOR_1,
					(SELECT USER_LOGIN_ID FROM USER_LOGIN WHERE USER_TYPE_ID=22 AND VALIDATOR_UNIT=2) AS VALIDATOR_2,
					(SELECT USER_LOGIN_ID FROM USER_LOGIN WHERE USER_TYPE_ID=23 AND APPROVAL_UNIT=1) AS APPROVAL_1,
					(SELECT USER_LOGIN_ID FROM USER_LOGIN WHERE USER_TYPE_ID=23 AND APPROVAL_UNIT=2) AS APPROVAL_2,
					SUMBER_DANA_KETERANGAN, P.PAKET_ID
					FROM PERMOHONAN_PAKET_ANALISA A
					LEFT JOIN USER_LOGIN E ON A.CREATED_BY = E.USER_LOGIN_ID
					LEFT JOIN USER_TYPE EE ON E.USER_TYPE_ID=EE.USER_TYPE_ID
          LEFT JOIN V_OAUTH_USER B ON E.NIP = B.NIPP
          LEFT JOIN KOMODITAS C ON A.KOMODITAS_ID = C.KOMODITAS_ID
					LEFT JOIN USER_LOGIN F ON F.USER_LOGIN_ID = A.POSTING_BY
					LEFT JOIN ANALISA_KEBUTUHAN G ON A.ANALISA_KEBUTUHAN_ID = G.ANALISA_KEBUTUHAN_ID
					LEFT JOIN ANALISA_PASAR H ON A.ANALISA_PASAR_ID = H.ANALISA_PASAR_ID
					LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ANALISA_ID = I.PERMOHONAN_PAKET_ANALISA_ID
					LEFT JOIN PAKET_JENIS J ON I.JENIS_BARANG_JASA=J.PAKET_JENIS_ID
					LEFT JOIN PERMOHONAN_PAKET_ANALISA_KATEGORI K ON A.PERMOHONAN_PAKET_ANALISA_KATEGORI_ID = K.PERMOHONAN_PAKET_ANALISA_KATEGORI_ID
					LEFT JOIN PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA L ON A.PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID = L.PERMOHONAN_PAKET_ANALISA_JENIS_BELANJA_ID
					JOIN PERMOHONAN_PAKET_ANALISA_MATRIX M ON A.APPROVAL = M.PERMOHONAN_PAKET_ANALISA_MATRIX_ID
					LEFT JOIN PAKET P ON I.PERMOHONAN_PAKET_ID=P.PERMOHONAN_PAKET_ID
					LEFT JOIN USER_LOGIN UU ON I.PIC=UU.USER_LOGIN_ID
				    WHERE I.PERMOHONAN_PAKET_ANALISA_ID IS NOT NULL
				) A WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}

		$str .= $statement." ".$order;

		// $str .= $statement." ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID DESC";
		// echo $str; die;
		$this->query = $str;


		return $this->selectLimit($str,$limit,$from);
    }

  function selectByParamsAnggaran($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.PERMOHONAN_PAKET_ANGGARAN_ID DESC")
	{
		$str = "SELECT A.* FROM
					(
					SELECT A.PERMOHONAN_PAKET_ANGGARAN_ID, A.PERMOHONAN_PAKET_ID, A.MATA_ANGGARAN, A.KEGIATAN, A.SUMBER_DANA, A.BUDGET_REMAINING, A.DEPARTMENT, A.DEPARTMENT_CODE, A.KODE_MATA_ANGGARAN, A.KODE_KEGIATAN, A.TOTAL_BUDGET, A.TIPE_TRANSAKSI, A.CREATED_BY, A.CREATED_DATE
					FROM PERMOHONAN_PAKET_ANGGARAN A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
				) A WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}

		$str .= $statement." ".$order;

		// $str .= $statement." ORDER BY A.PERMOHONAN_PAKET_ANALISA_ID DESC";
		// echo $str; die;
		$this->query = $str;


		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsAnggaran2($paramsArray=array(),$limit=-1,$from=-1, $statement='', $order=" ORDER BY A.PERMOHONAN_PAKET_ANGGARAN2_ID DESC")
	{
		$str = "SELECT A.* FROM
					(
					SELECT A.*
					FROM PERMOHONAN_PAKET_ANGGARAN2 A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
				) A WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}

		$str .= $statement." ".$order;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

    function getCountByParamsUsulan($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.PERMOHONAN_PAKET_ANALISA_ID) AS ROWCOUNT FROM
				(
					SELECT A.* FROM
					(
						SELECT A.*, I.RENCANA_PENGADAAN, E.ADMIN_RUP, E.DEPARTMENT
						FROM PERMOHONAN_PAKET_ANALISA A
						LEFT JOIN USER_LOGIN E ON A.CREATED_BY = E.USER_LOGIN_ID
	                    LEFT JOIN V_OAUTH_USER B ON E.NIP = B.NIPP
	                    LEFT JOIN KOMODITAS C ON A.KOMODITAS_ID = C.KOMODITAS_ID
						LEFT JOIN USER_LOGIN F ON F.USER_LOGIN_ID = A.POSTING_BY
						LEFT JOIN ANALISA_KEBUTUHAN G ON A.ANALISA_KEBUTUHAN_ID = G.ANALISA_KEBUTUHAN_ID
						LEFT JOIN ANALISA_PASAR H ON A.ANALISA_PASAR_ID = H.ANALISA_PASAR_ID
						LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ANALISA_ID = I.PERMOHONAN_PAKET_ANALISA_ID
					    WHERE I.PERMOHONAN_PAKET_ANALISA_ID IS NOT NULL
					) A WHERE 1=1 ".$statement."
				) A WHERE 1=1
					";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

  function deleteAnalisa()
	{
				$str = "DELETE FROM PERMOHONAN_PAKET
                WHERE  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."";
				$this->query = $str;
        $this->execQuery($str);

        $str2 = "DELETE FROM PERMOHONAN_PAKET_ANALISA
                WHERE  PERMOHONAN_PAKET_ANALISA_ID = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."";
        $this->query = $str2;
        $this->execQuery($str2);

        $str3 = "DELETE FROM REKAM_JEJAK
                WHERE  PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")."";
        $this->query = $str3;
        $this->execQuery($str3);

        $str4 = "DELETE FROM PERMOHONAN_PAKET_ANGGARAN
                WHERE  PERMOHONAN_PAKET_ID = ".$this->getField("PERMOHONAN_PAKET_ID")."";
        $this->query = $str4;
        $this->execQuery($str4);

		//echo $str;exit;
        return true;
	}

    function posting_analisa()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   POSTING  	= '".$this->getField("POSTING")."',
					   POSTING_BY      = '".$this->getField("POSTING_BY")."',
					   APPROVAL      = '".$this->getField("APPROVAL")."',
					   POSTING_DATE    = CURRENT_DATE,
					   ALASAN_TOLAK		= NULL
				WHERE  PERMOHONAN_PAKET_ANALISA_ID   =  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
				";

				$this->query = $str;
			if($this->execQuery($str)) {
				$str2 = "
					 UPDATE  PERMOHONAN_PAKET
					SET
						   UPDATED_BY  	= '".$this->getField("POSTING_BY")."',
						   UPDATED_DATE = CURRENT_DATE,
						   KODE_RUP  	= '".$this->getField("KODE_RUP")."'
					WHERE  PERMOHONAN_PAKET_ANALISA_ID IN (".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").")
					";

					$this->query = $str2;
				return $this->execQuery($str2);
			} else {
				return false;
			}
				//echo $str;exit;
		// return $this->execQuery($str);
    }

	function updatePermohonanApprove()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   UPDATED_BY  	= '".$this->getField("UPDATED_BY")."',
					   UPDATED_DATE = CURRENT_DATE,
					   APPROVAL = '6'
				WHERE  PERMOHONAN_PAKET_ANALISA_ID IN (".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").")
				";

				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

  function updatePermohonanDraft()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   UPDATED_BY  	= '".$this->getField("UPDATED_BY")."',
					   UPDATED_DATE = CURRENT_DATE,
					   APPROVAL = '3'
				WHERE  PERMOHONAN_PAKET_ANALISA_ID IN (".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").")
				";

				$this->query = $str;
				// echo $str;exit;
		return $this->execQuery($str);
  }

  function updatePermohonanRUP()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   UPDATED_BY  	= '".$this->getField("UPDATED_BY")."',
					   UPDATED_DATE = CURRENT_DATE,
					   APPROVAL = '1'
				WHERE  PERMOHONAN_PAKET_ANALISA_ID IN (".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").")
				";

				$this->query = $str;
		if($this->execQuery($str)) {
			// $str2 = "
			// 	 UPDATE  PERMOHONAN_PAKET
			// 	SET
			// 		   UPDATED_BY  	= '".$this->getField("UPDATED_BY")."',
			// 		   UPDATED_DATE = CURRENT_DATE,
			// 		   APPROVAL = '1',
			// 		   KODE_RUP  	= '".$this->getField("KODE_RUP")."'
			// 	WHERE  PERMOHONAN_PAKET_ANALISA_ID IN (".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").")
			// 	";
			$str2 = "
				 UPDATE  PERMOHONAN_PAKET
				SET
					   UPDATED_BY  	= '".$this->getField("UPDATED_BY")."',
					   UPDATED_DATE = CURRENT_DATE,
					   APPROVAL = '1'
				WHERE  PERMOHONAN_PAKET_ANALISA_ID IN (".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").")
				";

				$this->query = $str2;
			return $this->execQuery($str2);
		} else {
			return false;
		}

  }

    function publish_permohonan()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   PUBLISH  	= '".$this->getField("PUBLISH")."',
					   PUBLISH_DATE    = CURRENT_DATE
				WHERE  PERMOHONAN_PAKET_ANALISA_ID   =  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
				";

				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
  }

    function publish_permohonan_multi()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   PUBLISH  	= '".$this->getField("PUBLISH")."',
					   PUBLISH_DATE    = CURRENT_DATE
				WHERE  PERMOHONAN_PAKET_ANALISA_ID   IN (".$this->getField("PERMOHONAN_PAKET_ANALISA_ID").")
				";

				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
  }

    function kembali_permohonan()
	{
		$str = "
				 UPDATE  PERMOHONAN_PAKET_ANALISA
				SET
					   ALASAN_TOLAK  	= '".$this->getField("ALASAN_TOLAK")."',
					   ALASAN_TOLAK_BY      = '".$this->getField("ALASAN_TOLAK_BY")."',
					   ALASAN_TOLAK_DATE    = CURRENT_DATE,
					   APPROVAL      = '".$this->getField("APPROVAL")."'
				WHERE  PERMOHONAN_PAKET_ANALISA_ID   =  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."

				";
		// echo $str;
				$this->query = $str;

		return $this->execQuery($str);
    }

    function updatePermohonanPerencana()
	{
		$str = " UPDATE  PERMOHONAN_PAKET
				SET
				   NAMA  	= '".$this->getField("NAMA")."',
				   TANGGAL_WAKTU_PELAKSANAAN      = '".$this->getField("TANGGAL_WAKTU_PELAKSANAAN")."',
				   LOKASI_PEKERJAAN      = '".$this->getField("LOKASI_PEKERJAAN")."',
				   JENIS_KONTRAK      = '".$this->getField("JENIS_KONTRAK")."',
				   PENGADAAN_BYPASS      = '".$this->getField("PENGADAAN_BYPASS")."',
				   UPDATED_BY      = ".$this->getField("UPDATED_BY").",
				   UPDATED_DATE    = CURRENT_DATE
				WHERE  PERMOHONAN_PAKET_ANALISA_ID   =  ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID")."
				";
		$this->query = $str;
		return $this->execQuery($str);
    }

    function getNextKode()
		{
			$str = "SELECT MAX(PERMOHONAN_PAKET_ID) + 1 ROWCOUNT FROM PERMOHONAN_PAKET";

			$this->select($str);
			if($this->firstRow())
				return $this->getField("ROWCOUNT");
			else
				return 0;
		}

  }
?>
