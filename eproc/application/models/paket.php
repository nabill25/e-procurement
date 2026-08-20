<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  include_once('entity.php');

  class Paket extends Entity{

	var $query;

	function __construct(){
	  parent::__construct();
	}

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_ID", $this->getNextId("PAKET_ID","PAKET"));

		$str = "
			INSERT INTO PAKET (
			   PAKET_ID, PAKET_METODE_LELANG_ID, PAKET_METODE_KUALIFIKASI_ID,
			   PAKET_METODE_EVALUASI_ID, PAKET_JENIS_ID, USER_LOGIN_ID,
			   REKANAN_KUALIFIKASI_ID, NAMA, URAIAN,
			   LOKASI, ALAMAT, TELEPON,
			   EMAIL,
			   TANGGAL, PUBLISH_PAKET,
			   PUBLISH_PEMENANG, NILAI, NILAI_OWNER_ESTIMATE, UNIT_KERJA_ID, PERMOHONAN_PAKET_ID,
			   NILAI_MATA_UANG, SISTEM_SAMPUL, BAHASA, BIDDING_MENIT, BIDDING, BOBOT_TEKNIS, BOBOT_HARGA, PASSING_GRADE, MULTI_PEMENANG, CREATED_BY, CREATED_DATE, PAKET_UUID, MULTI_BIDANG_USAHA
			   )
  			 	VALUES (
				  ".$this->getField("PAKET_ID").",
  				  ".$this->getField("PAKET_METODE_LELANG_ID").",
   				  ".$this->getField("PAKET_METODE_KUALIFIKASI_ID").",
				  ".$this->getField("PAKET_METODE_EVALUASI_ID").",
				  ".$this->getField("PAKET_JENIS_ID").",
  				  ".$this->getField("USER_LOGIN_ID").",
   				  ".$this->getField("REKANAN_KUALIFIKASI_ID").",
   				  '".$this->getField("NAMA")."',
				  '".$this->getField("URAIAN")."',
   				  '".$this->getField("LOKASI")."',
                  '".$this->getField("ALAMAT")."',
   				  '".$this->getField("TELEPON")."',
				  '".$this->getField("EMAIL")."',
   				  CURRENT_DATE,
				  ".$this->getField("PUBLISH_PAKET").",
   				  ".$this->getField("PUBLISH_PEMENANG").",
				  ".$this->getField("NILAI").",
				  ".$this->getField("NILAI_OWNER_ESTIMATE").",
                  ".$this->getField('UNIT_KERJA_ID').",
                  ".$this->getField('PERMOHONAN_PAKET_ID').",
				  '".$this->getField('NILAI_MATA_UANG')."',
                  '".$this->getField('SISTEM_SAMPUL')."',
                  '".$this->getField('BAHASA')."',
				  ".$this->getField('BIDDING_MENIT').",
				  '".$this->getField('BIDDING')."',
				  '".$this->getField('BOBOT_TEKNIS')."',
				  '".$this->getField('BOBOT_HARGA')."',
				  '".$this->getField('PASSING_GRADE')."',
				  '".$this->getField('MULTI_PEMENANG')."',
				  ".$this->getField('CREATED_BY').",
				  CURRENT_TIMESTAMP,
				  uuid_generate_v4(),
				  '".$this->getField('MULTI_BIDANG_USAHA')."'
				)";

		// echo $str; die();
		$this->query = $str;
		$this->id = $this->getField("PAKET_ID");
		return $this->execQuery($str);
    }

	function update_dyna()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE")."
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }

	function update_set_null_syarat()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET SET
				  SYARAT_TEKNIS_TENAGA_AHLI = NULL,
				  SYARAT_TEKNIS_PERALATAN = NULL,
				  SYARAT_TEKNIS_SERTIFIKAT = NULL,
				  SYARAT_REKENING_KORAN = NULL,
				  SYARAT_KEUANGAN_SPT = NULL,
				  SYARAT_KEUANGAN_PPN = NULL,
				  SYARAT_KEUANGAN_PPH = NULL,
				  SYARAT_TEKNIS_SERTIFIKAT_INFO = NULL,
				  SYARAT_KEUANGAN_PKP = NULL,
				  SYARAT_ADM_KUALIFIKASI = NULL,
				  SYARAT_IJIN_SIUJK = NULL,
				  SYARAT_IJIN_SIUI = NULL,
				  SYARAT_IJIN_LAIN = NULL,
				  SYARAT_ADM_KUALIFIKASI_INFO = NULL,
				  SYARAT_NERACA = NULL,
				  SYARAT_SBU = NULL,
				  SYARAT_IJIN_SIUP = NULL
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		//echo $str;
		return $this->execQuery($str);
    }

	function updateNilaiOwner()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
				NILAI_OWNER_ESTIMATE        = '".$this->getField("NILAI_OWNER_ESTIMATE")."',
			  UPDATED_BY = ".$this->USER_LOGIN_ID.",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function updatePemenang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
				NILAI_NEGOSIASI        = '".$this->getField("NILAI_NEGOSIASI")."',
				TANGGAL_PENGUMUMAN_PEMENANG = ".$this->getField("TANGGAL_PENGUMUMAN_PEMENANG").",
				REKANAN_ID_PEMENANG        = '".$this->getField("REKANAN_ID_PEMENANG")."'
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
				//echo $str;exit;
		return $this->execQuery($str);
    }

	function publishBAPenawaran()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
			  PUBLISH_BA_PENAWARAN        = '1',
				PUBLISH_BA_PENAWARAN_TANGGAL        = CURRENT_TIMESTAMP,
				UPDATED_BY = ".$this->getField("CREATED_BY").",
				UPDATED_DATE = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function publishBALelangUlang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 CALL PROSES_LELANG_ULANG(".$this->getField("PAKET_ID").")
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function publishBAPenawaranUlang()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
				PUBLISH_BA_PENAWARAN        = '2',
				PUBLISH_BA_PENAWARAN_TANGGAL        = CURRENT_TIMESTAMP,
			  UPDATED_BY = ".$this->USER_LOGIN_ID.",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function publishBAPenawaran2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   PUBLISH_BA_PENAWARAN2        = '1',
					   PUBLISH_BA_PENAWARAN2_TANGGAL        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function publishSppbj()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
					   PUBLISH_SPPBJ        = '1',
					   PUBLISH_SPPBJ_TANGGAL        = CURRENT_DATE
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }


	function publishBAEvaluasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
		   	PUBLISH_BA_EVALSAMPUL1        = '1',
			  PUBLISH_BA_EVALSAMPUL1_TANGGAL        = CURRENT_TIMESTAMP,
			  UPDATED_BY = ".$this->USER_LOGIN_ID.",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
  }

  function publishBAEvaluasi2()
	{
		$str = "
				 UPDATE  PAKET
				SET
			  PUBLISH_BA_EVALSAMPUL2        = '1',
				PUBLISH_BA_EVALSAMPUL2_TANGGAL        = CURRENT_TIMESTAMP,
			  UPDATED_BY = ".$this->USER_LOGIN_ID.",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
  }

	function publishBANegosiasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
				PUBLISH_BA_NEGOSIASI        = '1',
			  UPDATED_BY = ".$this->USER_LOGIN_ID.",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

	function publishBAKualifikasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
				PUBLISH_BA_KUALIFIKASI        = '1',
			  UPDATED_BY = ".$this->USER_LOGIN_ID.",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;
		return $this->execQuery($str);
    }

  function publishEvaluasiKualifikasi()
	{
		$str = "
				 UPDATE  PAKET
				SET
		   	PUBLISH_EVALKUALIFIKASI        = '1',
			  PUBLISH_EVALKUALIFIKASI_TANGGAL        = CURRENT_TIMESTAMP,
			  UPDATED_BY = ".$this->USER_LOGIN_ID.",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				// echo $str;
				$this->query = $str;
		return $this->execQuery($str);
  }

    function updateByField()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET A SET
				  ".$this->getField("FIELD")." = ".$this->getField("FIELD_VALUE").",
					  UPDATED_BY = ".$this->USER_LOGIN_ID.",
					  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				// echo $str; die;
				$this->query = $str;
		return $this->execQuery($str);
    }

	function updateAlasan()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET A SET
				  ALASAN = '".$this->getField("ALASAN")."'
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;

		return $this->execQuery($str);
  }

  function updatePPK()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "UPDATE PAKET A SET
				  PPK = '".$this->getField("PPK")."'
				WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;

		return $this->execQuery($str);
  }

  function updateAlasanUlang()
	{
		// Tender Ulang
    // File tidak usah di copy karena jika update file tidak menghapus file yang lama
    // Update bisnis proses kalau Gagal balik ke perencanaan 17-06-2026
		// 1. Duplicate Permohonan Paket Analisa
		$this->setField("PERMOHONAN_PAKET_ANALISA_ID", $this->getNextId("PERMOHONAN_PAKET_ANALISA_ID","PERMOHONAN_PAKET_ANALISA"));
    $generatePermohonanAnalisaID = $this->getField("PERMOHONAN_PAKET_ANALISA_ID");

		$str1 = " INSERT INTO permohonan_paket_analisa
              (permohonan_paket_analisa_id,tahun_anggaran,created_by,created_date,posting,posting_by,posting_date,approval,publish,permohonan_paket_analisa_kategori_id,sumber_dana_keterangan
          		)
          		SELECT '".$generatePermohonanAnalisaID."',A.tahun_anggaran,A.created_by,A.created_date,A.posting,A.posting_by,A.posting_date,3,'0',1,A.sumber_dana_keterangan
          		FROM permohonan_paket_analisa A
          		WHERE A.permohonan_paket_analisa_id = ".$this->getField("PERMOHONAN_PAKET_ANALISA_ID_OLD")."
          		";
          		// echo $str1; die;
    if ($this->db->query($str1)) // 1. Insert to permohonan_paket_analisa
    {
			// 2. Duplicate Permohonan Paket
			$this->setField("PERMOHONAN_PAKET_ID", $this->getNextId("PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET"));
	    $generatePermohonanID = $this->getField("PERMOHONAN_PAKET_ID");
	    // echo $generatePermohonanID; die;
	    $str2 = " INSERT INTO permohonan_paket
	              (permohonan_paket_id,user_login_id,unit_kerja_id,nama,last_create_user,last_create_date,nilai,approval,permohonan_paket_analisa_id,tahun_anggaran,jenis_barang_jasa,perkiraan_biaya_harga,waktu_pengguna_barangjasa,rencana_pengadaan,created_by,created_date,paket_id_ulang,permohonan_paket_id_parent,kode_rup,kode_pr,sirup_id,nilai_rab_pr,nilai_hps_pr,kaji_ulang,nilai_mata_uang
	          		)
	          		SELECT '".$generatePermohonanID."',A.user_login_id,
	          		A.unit_kerja_id,A.nama,A.last_create_user,A.last_create_date,A.nilai,'0',".$generatePermohonanAnalisaID.",A.tahun_anggaran,A.jenis_barang_jasa,A.perkiraan_biaya_harga,A.waktu_pengguna_barangjasa,A.rencana_pengadaan,A.created_by,A.created_date,".$this->getField("PAKET_ID").",".$this->getField("PERMOHONAN_PAKET_ID_OLD").",A.kode_rup,A.kode_pr,A.sirup_id,A.nilai_rab_pr,A.nilai_hps_pr,'0',A.nilai_mata_uang
	          		FROM permohonan_paket A
	          		WHERE A.permohonan_paket_id = ".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
	          		";
	    if ($this->db->query($str2)) // 2. Insert to permohonan_paket
      {
      	// 3. Paket Penawaran
	      $this->setField("PAKET_PENAWARAN_ID", $this->getNextId("PAKET_PENAWARAN_ID","PAKET_PENAWARAN"));
	      $str3 = "INSERT INTO PAKET_PENAWARAN (paket_penawaran_id,paket_id,item,total_cost,satuan,quantity,oe,delivery_date,last_create_user,last_create_date,lokasi,jumlah,boq,boq_kolom,pr_group_number,pr_number,service_line,item_group,item_number,item_parent,item_child,rekanan_id_pemenang,biaya_kirim,boq_file,permohonan_paket_id,updated_by,updated_date)
	          		 SELECT '".$this->getField("PAKET_PENAWARAN_ID")."', 0, A.item,A.total_cost,A.satuan,A.quantity,A.oe,A.delivery_date,A.last_create_user,A.last_create_date,A.lokasi,A.jumlah,A.boq,A.boq_kolom,A.pr_group_number,A.pr_number,A.service_line,A.item_group,A.item_number,A.item_parent,A.item_child,A.rekanan_id_pemenang,A.biaya_kirim,A.boq_file,'".$generatePermohonanID."',A.updated_by,A.updated_date
	          		 FROM paket_penawaran A
	          		 WHERE A.permohonan_paket_id = ".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
	              ";
	      if ($this->db->query($str3)) // 3. Insert to paket_penawaran
	      {
	      	$countInsert3Gagal = 0;
         
	        if ($countInsert3Gagal == 0)  // Lanjut Jika tidak ada gagal insert
	        {
	          $countInsert4Gagal = 0;

	          if ($countInsert4Gagal == 0)  // Lanjut Jika tidak ada gagal insert
	          {
	            $this->db->query("UPDATE PAKET A SET
	        				  					  ALASAN_ULANG = '".$this->getField("ALASAN_ULANG")."'
	        										  WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
	                              ");
	            return true;
	          } else {
	          	$this->db->query("DELETE FROM permohonan_paket_analisa WHERE permohonan_paket_analisa_id = ".$generatePermohonanAnalisaID." ");
	            $this->db->query("DELETE FROM permohonan_paket WHERE permohonan_paket_id = ".$generatePermohonanID." ");
	            $this->db->query("DELETE FROM paket_penawaran WHERE permohonan_paket_id = ".$generatePermohonanID." ");
	            return false;
	            // echo 'gagal Permohonan Paket File';
	          }
	        } else {
	          return false;
	          // echo 'gagal COA';
	        }
	      } else {
	      	$this->db->query("DELETE FROM permohonan_paket_analisa WHERE permohonan_paket_analisa_id = ".$generatePermohonanAnalisaID." ");
          $this->db->query("DELETE FROM permohonan_paket WHERE permohonan_paket_id = ".$generatePermohonanID." ");
        	return false;
	      }

      } else { // 2. Insert to permohonan_paket
        // Hapus $str2
        $this->db->query("DELETE FROM permohonan_paket_analisa WHERE permohonan_paket_analisa_id = ".$generatePermohonanAnalisaID." ");
        return false;
        // echo 'gagal Insert to permohonan_paket';
      }
    } else 
    { // 1. Insert to permohonan_paket_analisa
      return false;
      // echo 'gagal Insert to permohonan_paket_analisa';
    }
		 
  }

  /**
   * OLD Update Kembalikan Gagal Paket ke Permohonan
  function updateAlasanUlang()
	{
		// Tender Ulang
    // File tidak usah di copy karena jika update file tidak menghapus file yang lama
		// 1. Duplicate Permohonan Paket
		$this->setField("PERMOHONAN_PAKET_ID", $this->getNextId("PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET"));
    $generatePermohonanID = $this->getField("PERMOHONAN_PAKET_ID");
    // echo $generatePermohonanID; die;
		$str1 = " INSERT INTO permohonan_paket
              (permohonan_paket_id,user_login_id,unit_kerja_id,tanggal,nota_dinas,nama,keterangan,no_ppa,last_create_user,last_create_date,nilai,posting,posting_by,posting_date,pic,pic_by,pic_date,alasan_tolak,alasan_tolak_by,alasan_tolak_date,approval,pengadaanlangsung,permohonan_paket_analisa_id,tahun_anggaran,anggaran,jenis_barang_jasa,perkiraan_biaya_harga,waktu_pengguna_barangjasa,rencana_pengadaan,cara_pengadaan,budget_awal,budget_terpakai,budget_akhir,created_by,created_date,updated_by,updated_date,paket_id_ulang,permohonan_paket_id_parent,kode_rup,kode_pr,strategi_pengadaan,sirup_id,nilai_rab_pr,nilai_hps_pr,paket_metode_lelang_id,kaji_ulang,kaji_ulang_end_date,tanggal_waktu_pelaksanaan,lokasi_pekerjaan,jenis_kontrak,kode_sirup_lkpp,nilai_mata_uang
          		)
          		SELECT '".$generatePermohonanID."',A.user_login_id,
          		A.unit_kerja_id,A.tanggal,A.nota_dinas,A.nama,A.keterangan,A.no_ppa,A.last_create_user,A.last_create_date,A.nilai,A.posting,A.posting_by,A.posting_date,A.pic,A.pic_by,A.pic_date,A.alasan_tolak,A.alasan_tolak_by,A.alasan_tolak_date,A.approval,A.pengadaanlangsung,A.permohonan_paket_analisa_id,A.tahun_anggaran,A.anggaran,A.jenis_barang_jasa,A.perkiraan_biaya_harga,A.waktu_pengguna_barangjasa,A.rencana_pengadaan,A.cara_pengadaan,A.budget_awal,A.budget_terpakai,A.budget_akhir,A.created_by,A.created_date,A.updated_by,A.updated_date,".$this->getField("PAKET_ID").",".$this->getField("PERMOHONAN_PAKET_ID_OLD").",A.kode_rup,A.kode_pr,A.strategi_pengadaan,A.sirup_id,A.nilai_rab_pr,A.nilai_hps_pr,A.paket_metode_lelang_id,A.kaji_ulang,A.kaji_ulang_end_date,A.tanggal_waktu_pelaksanaan,A.lokasi_pekerjaan,A.jenis_kontrak,A.kode_sirup_lkpp,A.nilai_mata_uang
          		FROM permohonan_paket A
          		WHERE A.permohonan_paket_id = ".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
          		";
    if ($this->db->query($str1)) // 1. Insert to permohonan_paket
    {
      // 2. Paket Penawaran
      $this->setField("PAKET_PENAWARAN_ID", $this->getNextId("PAKET_PENAWARAN_ID","PAKET_PENAWARAN"));
      $str2 = "INSERT INTO PAKET_PENAWARAN (paket_penawaran_id,paket_id,item,total_cost,satuan,quantity,oe,delivery_date,last_create_user,last_create_date,lokasi,jumlah,boq,boq_kolom,pr_group_number,pr_number,service_line,item_group,item_number,item_parent,item_child,rekanan_id_pemenang,biaya_kirim,boq_file,permohonan_paket_id,updated_by,updated_date)
          		 SELECT '".$this->getField("PAKET_PENAWARAN_ID")."', 0, A.item,A.total_cost,A.satuan,A.quantity,A.oe,A.delivery_date,A.last_create_user,A.last_create_date,A.lokasi,A.jumlah,A.boq,A.boq_kolom,A.pr_group_number,A.pr_number,A.service_line,A.item_group,A.item_number,A.item_parent,A.item_child,A.rekanan_id_pemenang,A.biaya_kirim,A.boq_file,'".$generatePermohonanID."',A.updated_by,A.updated_date
          		 FROM paket_penawaran A
          		 WHERE A.permohonan_paket_id = ".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
              ";
      if ($this->db->query($str2)) // 2. Insert to paket_penawaran
      {
        // 3. COA
        $countInsert3Gagal = 0;
        // $this->load->model("PermohonanPaket");
        // $permohonan_paket_coa = new PermohonanPaket();
        // $permohonan_paket_coa->selectByParamsCoa(array("A.PERMOHONAN_PAKET_ID" => coalesce($this->getField("PERMOHONAN_PAKET_ID_OLD"), 0)));
        // while($permohonan_paket_coa->nextRow())
        // {
        //   $this->setField("COA_ID", $this->getNextId("COA_ID","PERMOHONAN_PAKET_COA"));
        //   $str3 = "INSERT INTO PERMOHONAN_PAKET_COA (coa_id,permohonan_paket_id,nomor,keterangan,budget_awal,budget_terpakai,budget_akhir,created_by,created_date)
        //            SELECT '".$this->getField("COA_ID")."', ".$generatePermohonanID.", A.nomor, A.keterangan, A.budget_awal, A.budget_terpakai, A.budget_akhir, A.created_by, A.created_date
        //            FROM permohonan_paket_coa A
        //            WHERE A.coa_id = ".$permohonan_paket_coa->getField("COA_ID")."
        //            ";
        //   if ($this->db->query($str3)) // 3. Insert COA
        //   { continue;
        //   } else { // 3. COA
        //     // Hapus $str1 & $str2 & $str3
        //     $this->db->query("DELETE FROM permohonan_paket WHERE permohonan_paket_id = ".$generatePermohonanID." ");
        //     $this->db->query("DELETE FROM paket_penawaran WHERE permohonan_paket_id = ".$generatePermohonanID." ");
        //     $this->db->query("DELETE FROM permohonan_paket_coa WHERE permohonan_paket_id = ".$generatePermohonanID." ");
        //     // return false;
        //     // echo 'gagal COA';
        //     $countInsert3Gagal++;
        //   }

        // }

        if ($countInsert3Gagal == 0)  // Lanjut Jika tidak ada gagal insert
        {
          // 4. Permohonan Paket File
          $countInsert4Gagal = 0;
          // $this->load->model("PermohonanPaketFile");
          // $permohonan_paket_file = new PermohonanPaketFile();
          // $permohonan_paket_file->selectByParams(array("A.PERMOHONAN_PAKET_ID" => coalesce($this->getField("PERMOHONAN_PAKET_ID_OLD"), 0)));
          // while($permohonan_paket_file->nextRow())
          // {
          //   $this->setField("PERMOHONAN_PAKET_FILE_ID", $this->getNextId("PERMOHONAN_PAKET_FILE_ID","PERMOHONAN_PAKET_FILE"));
          //   $str4 = "INSERT INTO PERMOHONAN_PAKET_FILE (permohonan_paket_file_id,permohonan_paket_id,path_file,tipe,ukuran,judul,urut,paket_id,created_by,created_date)
          //   SELECT '".$this->getField("PERMOHONAN_PAKET_FILE_ID")."', ".$generatePermohonanID.", A.path_file, A.tipe, A.ukuran, A.judul, A.urut, null, A.created_by, A.created_date
          //   FROM permohonan_paket_file A
          //   WHERE A.permohonan_paket_file_id = ".$permohonan_paket_file->getField("PERMOHONAN_PAKET_FILE_ID")."
          //   ";
          //   // echo $str4;
          //   if ($this->db->query($str4)) // 4. Permohonan Paket File
          //   { continue;
          //   } else { // 4. Permohonan Paket File
          //     // Hapus $str1 & $str2 & $str3 & $str4
          //     $this->db->query("DELETE FROM permohonan_paket WHERE permohonan_paket_id = ".$generatePermohonanID." ");
          //     $this->db->query("DELETE FROM paket_penawaran WHERE permohonan_paket_id = ".$generatePermohonanID." ");
          //     $this->db->query("DELETE FROM permohonan_paket_coa WHERE permohonan_paket_id = ".$generatePermohonanID." ");
          //     $this->db->query("DELETE FROM permohonan_paket_file WHERE permohonan_paket_id = ".$generatePermohonanID." ");
          //     // return false;
          //     // echo 'gagal Permohonan Paket File';
          //     $countInsert4Gagal++;
          //   }
          // }

          if ($countInsert4Gagal == 0)  // Lanjut Jika tidak ada gagal insert
          {
            $this->db->query("UPDATE PAKET A SET
        				  					  ALASAN_ULANG = '".$this->getField("ALASAN_ULANG")."'
        										  WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
                              ");
            return true;
          } else {
          	$this->db->query("DELETE FROM permohonan_paket WHERE permohonan_paket_id = ".$generatePermohonanID." ");
            $this->db->query("DELETE FROM paket_penawaran WHERE permohonan_paket_id = ".$generatePermohonanID." ");
            return false;
            // echo 'gagal Permohonan Paket File';
          }
        } else {
          return false;
          // echo 'gagal COA';
        }


      } else { // 2. Insert to paket_penawaran
        // Hapus $str1
        $this->db->query("DELETE FROM permohonan_paket WHERE permohonan_paket_id = ".$generatePermohonanID." ");
        return false;
        // echo 'gagal Insert to paket_penawaran';
      }
    } else { // 1. Insert to permohonan_paket
      return false;
      // echo 'gagal Insert to permohonan_paket';
    }
    // die();

		// // 5. Update Paket
		// $this->db->query("UPDATE PAKET A SET
		// 		  					 ALASAN_ULANG = '".$this->getField("ALASAN_ULANG")."'
		// 								 WHERE PAKET_ID = ".$this->getField("PAKET_ID")."
    //                  ");
		// if ($this->db->trans_status() === FALSE)
		// {
    //   echo "false";
		//     $this->db->trans_rollback();
		//     return false;
		// }
		// else
		// {
    //   echo "true";
		//     $this->db->trans_commit();
		//     return true;
		// }
  }

  **/

    function update()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET
					   PAKET_METODE_LELANG_ID      =  ".$this->getField("PAKET_METODE_LELANG_ID").",
					   PAKET_METODE_KUALIFIKASI_ID = ".$this->getField("PAKET_METODE_KUALIFIKASI_ID").",
					   PAKET_METODE_EVALUASI_ID    = ".$this->getField("PAKET_METODE_EVALUASI_ID").",
					   PAKET_JENIS_ID              = ".$this->getField("PAKET_JENIS_ID").",
					   REKANAN_KUALIFIKASI_ID      =  ".$this->getField("REKANAN_KUALIFIKASI_ID").",
					   NAMA                        = '".$this->getField("NAMA")."',
					   URAIAN                      = '".$this->getField("URAIAN")."',
					   LOKASI                      = '".$this->getField("LOKASI")."',
					   ALAMAT                      = '".$this->getField("ALAMAT")."',
					   TELEPON                     = '".$this->getField("TELEPON")."',
					   EMAIL                       = '".$this->getField("EMAIL")."',
					   NILAI                       =   ".$this->getField("NILAI").",
					   NILAI_OWNER_ESTIMATE        =   ".$this->getField("NILAI_OWNER_ESTIMATE").",
					   PERMOHONAN_PAKET_ID		   = ".$this->getField("PERMOHONAN_PAKET_ID").",
					   NILAI_MATA_UANG			   = '".$this->getField('NILAI_MATA_UANG')."',
					   SISTEM_SAMPUL			   = '".$this->getField("SISTEM_SAMPUL")."',
					   BAHASA					   = '".$this->getField("BAHASA")."',
					   BIDDING_MENIT			   = ".$this->getField("BIDDING_MENIT").",
					   BIDDING					   = '".$this->getField("BIDDING")."',
					   BOBOT_TEKNIS					   = '".$this->getField("BOBOT_TEKNIS")."',
					   BOBOT_HARGA					   = '".$this->getField("BOBOT_HARGA")."',
					   PASSING_GRADE					   = '".$this->getField("PASSING_GRADE")."',
					   MULTI_PEMENANG					   = '".$this->getField("MULTI_PEMENANG")."',
					   UPDATED_BY					   = ".$this->getField("CREATED_BY").",
					   UPDATED_DATE 	= CURRENT_TIMESTAMP,
					   MULTI_BIDANG_USAHA					   = '".$this->getField("MULTI_BIDANG_USAHA")."'
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
    }

  function updateNegosiasi()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				UPDATE  PAKET
				SET  BIDDING_MENIT			   = ".$this->getField("BIDDING_MENIT").",
					   BIDDING					   = '".$this->getField("BIDDING")."',
					   MULTI_PEMENANG					   = '".$this->getField("MULTI_PEMENANG")."',
					   UPDATED_BY					   = ".$this->getField("CREATED_BY").",
					   UPDATED_DATE 	= CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM PAKET
                WHERE
                  PAKET_ID = ".$this->getField("PAKET_ID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

    /**
    * Cari record berdasarkan array parameter dan limit tampilan
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_METODE_EVALUASI_ID"=>"yyy")
    * @param int limit Jumlah maksimal record yang akan diambil
    * @param int from Awal record yang diambil
    * @return boolean True jika sukses, false jika tidak
    **/
    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT
					A.PAKET_ID, A.PAKET_UUID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID,
					   A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.USER_LOGIN_ID,
					   A.REKANAN_KUALIFIKASI_ID, A.NAMA, A.URAIAN,
					   A.LOKASI, A.ALAMAT, A.TELEPON,
					   A.FAX, A.EMAIL, A.SYARAT,
					   A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
					   A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, B.NILAI NILAI_PERMOHONAN,
					   A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, A.PERMOHONAN_PAKET_ID, B.NOTA_DINAS PERMOHONAN_NOTA_DINAS, B.NAMA PERMOHONAN, A.JENIS_PENGADAAN,
					   A.NILAI_NEGOSIASI, A.SISTEM_SAMPUL, A.BAHASA, A.SISTEM_HARGA, A.NILAI_MATA_UANG, A.SISTEM_PPN, A.BIDDING_MENIT, A.BIDDING, A.BIDDING_MULAI, A.BIDDING_MULAI - INTERVAL '10' MINUTE BIDDING_MULAI_SHOW, A.NEGOSIASI_MULAI,
                       A.BOBOT_TEKNIS, A.BOBOT_HARGA, A.PASSING_GRADE, A.PENAWARAN_HARGA_MAKSIMAL, A.PUBLISH_EVALKUALIFIKASI, A.MULTI_PEMENANG, A.PPK, B.KODE_PR, B.KODE_RUP, C.KODE_SA, A.MULTI_BIDANG_USAHA
					FROM PAKET A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
					LEFT JOIN IMPORT_SIRUP C ON B.SIRUP_ID = C.ID
				    WHERE A.PAKET_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY A.NAMA ASC";
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

   function selectByParamsReschedule($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT a.* FROM (
					SELECT a.paket_tahap_id, a.nama, a.paket_id, a.tanggal_awal, a.tanggal_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '1' order by aa.paket_tahap_id desc limit 1) as reschedule_1,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '1' order by aa.paket_tahap_id desc limit 1) as reschedule_1_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '1' order by aa.paket_tahap_id desc limit 1) as reschedule_1_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '2' order by aa.paket_tahap_id desc limit 1) as reschedule_2,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '2' order by aa.paket_tahap_id desc limit 1) as reschedule_2_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '2' order by aa.paket_tahap_id desc limit 1) as reschedule_2_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '3' order by aa.paket_tahap_id desc limit 1) as reschedule_3,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '3' order by aa.paket_tahap_id desc limit 1) as reschedule_3_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '3' order by aa.paket_tahap_id desc limit 1) as reschedule_3_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '4' order by aa.paket_tahap_id desc limit 1) as reschedule_4,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '4' order by aa.paket_tahap_id desc limit 1) as reschedule_4_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '4' order by aa.paket_tahap_id desc limit 1) as reschedule_4_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '5' order by aa.paket_tahap_id desc limit 1) as reschedule_5,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '5' order by aa.paket_tahap_id desc limit 1) as reschedule_5_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '5' order by aa.paket_tahap_id desc limit 1) as reschedule_5_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '6' order by aa.paket_tahap_id desc limit 1) as reschedule_6,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '6' order by aa.paket_tahap_id desc limit 1) as reschedule_6_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '6' order by aa.paket_tahap_id desc limit 1) as reschedule_6_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '7' order by aa.paket_tahap_id desc limit 1) as reschedule_7,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '7' order by aa.paket_tahap_id desc limit 1) as reschedule_7_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '7' order by aa.paket_tahap_id desc limit 1) as reschedule_7_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '8' order by aa.paket_tahap_id desc limit 1) as reschedule_8,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '8' order by aa.paket_tahap_id desc limit 1) as reschedule_8_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '8' order by aa.paket_tahap_id desc limit 1) as reschedule_8_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '9' order by aa.paket_tahap_id desc limit 1) as reschedule_9,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '9' order by aa.paket_tahap_id desc limit 1) as reschedule_9_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '9' order by aa.paket_tahap_id desc limit 1) as reschedule_9_akhir,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '10' order by aa.paket_tahap_id desc limit 1) as reschedule_10,
					(select aa.tanggal_awal from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '10' order by aa.paket_tahap_id desc limit 1) as reschedule_10_awal,
					(select aa.tanggal_akhir from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '10' order by aa.paket_tahap_id desc limit 1) as reschedule_10_akhir
					FROM paket_tahap a
				) a
					WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY PAKET_TAHAP_ID ASC";
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsTotalPerubahan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT RESCHEDULE_KE FROM PAKET_TAHAP_RESCHEDULE WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY RESCHEDULE_KE DESC LIMIT 1";
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsWithKatalog($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT
					A.PAKET_ID, A.PAKET_UUID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID,
					   A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.USER_LOGIN_ID,
					   A.REKANAN_KUALIFIKASI_ID, A.NAMA, A.URAIAN,
					   A.LOKASI, A.ALAMAT, A.TELEPON,
					   A.FAX, A.EMAIL, A.SYARAT,
					   A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
					   A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
					   A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, A.PERMOHONAN_PAKET_ID, B.NOTA_DINAS PERMOHONAN_NOTA_DINAS, B.NAMA PERMOHONAN, A.JENIS_PENGADAAN,
					   A.NILAI_NEGOSIASI, A.SISTEM_SAMPUL, A.BAHASA, A.SISTEM_HARGA, A.NILAI_MATA_UANG, A.SISTEM_PPN, A.BIDDING_MENIT, A.BIDDING,
                       A.BOBOT_TEKNIS, A.BOBOT_HARGA, A.PASSING_GRADE, A.ALASAN,
                       (SELECT V.STATUS FROM KATALOG_REKANAN V WHERE PAKET_ID=A.PAKET_ID LIMIT 1 ), A.PPK, C.NAMA METODE_LELANG, B.KODE_RUP, B.KODE_PR
					FROM PAKET A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
					LEFT JOIN PAKET_METODE_LELANG C ON A.PAKET_METODE_LELANG_ID=C.PAKET_METODE_LELANG_ID
				    WHERE A.PAKET_ID IS NOT NULL ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY A.PAKET_ID DESC";
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

    function selectByPaketRekananKeterangan($paket_id, $paket_rekanan_id, $rekanan_id, $urut_kualifikasi1, $urut_penawaran1)
	{
		$str = "
				SELECT KETERANGAN FROM
				(
				SELECT COALESCE(TANGGAL_AKHIR, TANGGAL_AWAL) TANGGAL_BATAS, CASE WHEN COALESCE((SELECT COUNT(*) FROM REKANAN_EVAL_ADMIN WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id."), 0) = 0 AND EXISTS(SELECT 1 FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID AND PAKET_METODE_KUALIFIKASI_ID = 1 AND JENIS_PENGADAAN = 'LELANG') THEN 'Anda gagal pada tahap kualifikasi karena tidak memasukkan data kualifikasi' END KETERANGAN
                                    FROM PAKET_TAHAP A WHERE PAKET_ID = ".$paket_id." AND TAMPILKAN = 1 AND URUT = ".$urut_kualifikasi1."
				UNION ALL
				SELECT COALESCE(TANGGAL_AKHIR, TANGGAL_AWAL) TANGGAL_BATAS, CASE WHEN COALESCE((SELECT COUNT(*)
                                FROM PAKET_DOKUMEN WHERE REKANAN_USER_ID = ".$rekanan_id." AND (JENIS_DOKUMEN = 'PENAWARAN' OR JENIS_DOKUMEN = 'PENAWARAN_HARGA')), 0) = 0 THEN 'Anda gagal pada tahap penawaran karena tidak memasukkan dokumen penawaran' END KETERANGAN FROM PAKET_TAHAP WHERE PAKET_ID = ".$paket_id." AND TAMPILKAN = 1 AND URUT = ".$urut_penawaran1."
				) A
				WHERE TANGGAL_BATAS < CURRENT_DATE AND KETERANGAN IS NOT NULL
	  ";
    // echo $str; die;
		//WHERE TO_DATE(TANGGAL_BATAS, 'yyyy/mm/dd hh:mi:ss') < TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss') AND KETERANGAN IS NOT NULL
		$this->query = $str;
	return $this->selectLimit($str, -1, -1);
    }

    function selectById($paket_Id)
	{
		$str = "
            SELECT
               A.SYARAT_TEKNIS_TENAGA_AHLI, A.SYARAT_TEKNIS_PERALATAN,
               A.SYARAT_TEKNIS_SERTIFIKAT, A.SYARAT_REKENING_KORAN_BULAN, A.SYARAT_REKENING_KORAN, A.SYARAT_KEUANGAN_SPT,
               A.SYARAT_KEUANGAN_PPN, A.SYARAT_KEUANGAN_PPH,
               A.SYARAT_KEUANGAN_PPN_BULAN, A.SYARAT_KEUANGAN_PPH_BULAN,
               A.SYARAT_TEKNIS_SERTIFIKAT_INFO,
               A.SYARAT_KEUANGAN_PKP, A.SYARAT_ADM_KUALIFIKASI,
               A.SYARAT_NERACA, A.SYARAT_SBU, A.PERMOHONAN_PAKET_ID,
               A.SYARAT_IJIN_SIUJK, A.SYARAT_IJIN_SIUI, A.SYARAT_IJIN_LAIN, A.SYARAT_ADM_KUALIFIKASI_INFO,
               A.PAKET_ID, A.PAKET_UUID, A.NAMA, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID,
               A.ALASAN, A.ALASAN_ULANG,
               A.BOBOT_TEKNIS, A.BOBOT_HARGA, A.PASSING_GRADE,
               B.NAMA PAKET_METODE_LELANG, C.NAMA PAKET_METODE_KUALIFIKASI,
               D.NAMA PAKET_METODE_EVALUASI, E.NAMA PAKET_JENIS, G.NAMA REKANAN_KUALIFIKASI, A.NILAI, A.NILAI_OWNER_ESTIMATE, A.PENAWARAN_HARGA_MAKSIMAL,A.TANGGAL, A.PASS_GRADE, A.LOKASI,
               COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) tanggal_tahap,
               COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pemasukan data kualifikasi'),A.TANGGAL) tanggal_pemasukan, --aim: INC0002723
               REKANAN_ID_PEMENANG, A.NILAI_NEGOSIASI, TO_CHAR(A.TANGGAL_PENGUMUMAN_PEMENANG, 'YYYY-MM-DD') TANGGAL_PENGUMUMAN_PEMENANG, REKANAN_ID_PENILAIAN, A.USER_LOGIN_ID,
               H.NAMA UNIT_KERJA, A.PUBLISH_PAKET,A.RESCHEDULE_KE,A.RESCHEDULE_1,A.RESCHEDULE_2,A.RESCHEDULE_3,A.RESCHEDULE_4,A.RESCHEDULE_5,A.RESCHEDULE_6,A.RESCHEDULE_7,A.RESCHEDULE_8,A.RESCHEDULE_9,A.RESCHEDULE_10, A.UNIT_KERJA_ID, TO_CHAR(A.PUBLISH_PAKET_TANGGAL, 'DD-MM-YYYY HH24:MI') PUBLISH_PAKET_TANGGAL, SYARAT_IJIN_SIUP, A.SYARAT_KEUANGAN_SPT_TAHUN,
               A.SYARAT_NERACA_TAHUN, A.JENIS_PENGADAAN, A.PUBLISH_BA_PENAWARAN, A.PUBLISH_BA_PENAWARAN_TANGGAL, A.PUBLISH_BA_KUALIFIKASI, A.PUBLISH_BA_NEGOSIASI, A.MULTI_PEMENANG,
               LPAD(CAST(A.PAKET_ID AS TEXT), 8, '0')  PR_GROUP_NUMBER, A.NILAI_MATA_UANG,
						   COALESCE(J.NAMA, F.USER_NAMA) USER_LOGIN, A.SISTEM_SAMPUL, A.PUBLISH_BA_EVALSAMPUL1, A.PUBLISH_BA_EVALSAMPUL2,
						   A.PUBLISH_BA_PENAWARAN2, A.BAHASA, A.SISTEM_HARGA, F.NIP NIP_PEMBUAT, PUBLISH_SPPBJ, PUBLISH_SPPBJ_TANGGAL, A.SISTEM_PPN, H.KODE_ENTITAS, A.BIDDING_MENIT, A.BIDDING, A.BIDDING_MULAI, K.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI, K.KODE_PR, K.KODE_RUP, A.MULTI_BIDANG_USAHA
          FROM  PAKET A
                  LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                  LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                  LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                  LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                  LEFT JOIN USER_LOGIN F ON A.USER_LOGIN_ID = F.USER_LOGIN_ID
                  LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                  LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                  LEFT JOIN SAP_PR I ON A.PAKET_ID = I.PAKET_ID
                  LEFT JOIN V_PEGAWAI_REVISI J ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = J.NIPP
                  LEFT JOIN PERMOHONAN_PAKET K ON A.PERMOHONAN_PAKET_ID=K.PERMOHONAN_PAKET_ID
          WHERE 1 = 1 AND
                    A.PAKET_ID = ".$paket_Id."
	  ";

		$this->query = $str;
		//echo $str;exit;
		return $this->select($str);
    }

  function selectByParamsMonitoring($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
        $str = "SELECT * from (
					SELECT
                       A.PAKET_ID, A.PAKET_UUID, K.AKTIF, K.STATUS, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                       A.PAKET_METODE_KUALIFIKASI_ID,A.PERMOHONAN_PAKET_ID, I.PERMOHONAN_PAKET_ANALISA_ID,
                       D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                       G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, AMBIL_PAKET_BIDANG_USAHA2(A.PAKET_ID) BIDANG_USAHA2, A.NAMA, A.URAIAN,
                       A.LOKASI, A.ALAMAT, A.TELEPON,
                       A.FAX, A.EMAIL, A.SYARAT,
                       A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, A.NILAI_MATA_UANG,
                       A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.UNIT_KERJA_ID ,H.NAMA UNIT_KERJA, A.USER_LOGIN_ID, A.ALASAN, A.ALASAN_ULANG, A.PAKET_METODE_LELANG_ID,
                       TO_CHAR(COALESCE((SELECT MAX(TANGGAL_AWAL) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID AND X.NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) , 'YYYY-MM-DD') TANGGAL_TAHAP,
                       COALESCE(I.USER_LOGIN_ID, 0) USER_LOGIN_ID_FUNGSIONAL, AMBIL_PAKET_BIDANG_USAHA_ID(A.PAKET_ID) BIDANG_USAHA_ID,
					   A.JENIS_PENGADAAN, REKANAN_ID_PEMENANG, NILAI_NEGOSIASI, A.PUBLISH_BA_PENAWARAN, A.PUBLISH_BA_PENAWARAN_TANGGAL, A.PUBLISH_BA_KUALIFIKASI,
                       J.PR_GROUP_NUMBER, A.SISTEM_SAMPUL, A.PUBLISH_BA_PENAWARAN2, A.PUBLISH_BA_EVALSAMPUL1, A.PUBLISH_BA_EVALSAMPUL2, A.MULTI_PEMENANG, A.BAHASA, A.SISTEM_HARGA,
					   A.PUBLISH_SPPBJ, A.PUBLISH_SPPBJ_TANGGAL,A.BIDDING_MENIT, A.BIDDING, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI, I.KODE_RUP, I.KODE_PR, A.PPK
                    FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON CAST (A.USER_LOGIN_ID AS TEXT) = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
														LEFT JOIN VIEW_PAKET_FILTER K ON A.PAKET_ID=K.PAKET_ID
                    WHERE 1 = 1
                    ) A where 1 = 1
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

		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC ";
    // echo $str; die();
		$this->query = $str;
        $rs = $this->selectLimit($str,$limit,$from);

		//	print_r($rs);
        return  $rs;
  }

  function selectByParamsMonitoring2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
	//THE NEW ONE, TANGGAL DI TAHAPAN LELANG IKUT DIQUERY SEBAGAI ACUAN PEMBUATAN LELANG
        $str = "SELECT * from (
					SELECT
                       A.PAKET_ID, A.PAKET_UUID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                       A.PAKET_METODE_KUALIFIKASI_ID,A.PERMOHONAN_PAKET_ID,
                       D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                       G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA2(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                       A.LOKASI, A.ALAMAT, A.TELEPON,
                       A.FAX, A.EMAIL, A.SYARAT,
                       A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI, A.NILAI_MATA_UANG,
                       A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.UNIT_KERJA_ID ,H.NAMA UNIT_KERJA, A.USER_LOGIN_ID, A.ALASAN, A.ALASAN_ULANG, A.PAKET_METODE_LELANG_ID,
                       TO_CHAR(COALESCE((SELECT MAX(TANGGAL_AWAL) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID AND X.NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) , 'YYYY-MM-DD') TANGGAL_TAHAP,
                       COALESCE(I.USER_LOGIN_ID, 0) USER_LOGIN_ID_FUNGSIONAL, AMBIL_PAKET_BIDANG_USAHA_ID(A.PAKET_ID) BIDANG_USAHA_ID,
					   A.JENIS_PENGADAAN, REKANAN_ID_PEMENANG, NILAI_NEGOSIASI, A.PUBLISH_BA_PENAWARAN, A.PUBLISH_BA_PENAWARAN_TANGGAL, A.PUBLISH_BA_KUALIFIKASI,
                       J.PR_GROUP_NUMBER, A.SISTEM_SAMPUL, A.PUBLISH_BA_PENAWARAN2, A.PUBLISH_BA_EVALSAMPUL1, A.PUBLISH_BA_EVALSAMPUL2, A.MULTI_PEMENANG, A.BAHASA, A.SISTEM_HARGA,
					   A.PUBLISH_SPPBJ, A.PUBLISH_SPPBJ_TANGGAL,A.BIDDING_MENIT, A.BIDDING, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI, A.PPK, A.MULTI_BIDANG_USAHA
                    FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON CAST (A.USER_LOGIN_ID AS TEXT) = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                    WHERE 1 = 1
                    ) A where 1 = 1
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

		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC ";
    // echo $str; die();
		$this->query = $str;
        $rs = $this->selectLimit($str,$limit,$from);

		//	print_r($rs);
        return  $rs;
  }

    function selectByParamsPaketRekanan($paramsArray=array(),$limit=-1,$from=-1, $rekanan_id='',$statement='')
	{
        $str = "
                SELECT * FROM
                (
                    SELECT
                       A.PAKET_ID, A.PAKET_UUID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                       D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                       G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                       A.LOKASI, A.ALAMAT, A.TELEPON,
                       A.FAX, A.EMAIL, A.SYARAT,
                       A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                       A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.ALASAN_ULANG, A.UNIT_KERJA_ID,
                       COALESCE((SELECT MAX(TO_CHAR(tanggal_awal, 'YYYY-MM-DD')) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),TO_CHAR(A.TANGGAL, 'YYYY-MM-DD')) tanggal_tahap,
                       A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI, A.MULTI_BIDANG_USAHA
                    FROM    PAKET A
                        LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                        LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                        LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                        LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                        LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                        LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                        LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                        LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                        LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                    WHERE
                          A.PUBLISH_PAKET = 1
                          or
                          EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.TANGGAL_UNDANG IS NOT NULL AND X.REKANAN_ID = '".$rekanan_id."')  ) A WHERE 1 = 1
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

		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC ";

		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);

		// $str = "
  //           SELECT * FROM
  //           (
  //               SELECT
  //                  A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
  //                  D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
  //                  G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
  //                  A.LOKASI, A.ALAMAT, A.TELEPON,
  //                  A.FAX, A.EMAIL, A.SYARAT,
  //                  A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
  //                  A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
  //                  A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
  //                  COALESCE((SELECT MAX(TO_CHAR(tanggal_awal, 'YYYY-MM-DD')) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),TO_CHAR(A.TANGGAL, 'YYYY-MM-DD')) tanggal_tahap,
  //                  A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA
  //               FROM    PAKET A
  //                   LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
  //                   LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
  //                   LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
  //                   LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
  //                   LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
  //                   LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
  //                   LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
  //                   LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
  //                   LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
  //               WHERE
  //                     A.PUBLISH_PAKET = 1
  //           UNION ALL  -- FOR YANG SUDAH TERUNDANG PADA PRAKUALIFIKASI
  //               SELECT
  //                  A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
  //                  D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
  //                  G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
  //                  A.LOKASI, A.ALAMAT, A.TELEPON,
  //                  A.FAX, A.EMAIL, A.SYARAT,
  //                  A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
  //                  A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
  //                  A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
  //                  COALESCE((SELECT MAX(TO_CHAR(tanggal_awal, 'YYYY-MM-DD')) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),TO_CHAR(A.TANGGAL, 'YYYY-MM-DD')) tanggal_tahap,
  //                  A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA
  //               FROM    PAKET A
  //                   LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
  //                   LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
  //                   LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
  //                   LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
  //                   LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
  //                   LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
  //                   LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
  //                   LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
  //                   LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
  //               WHERE
  //                     EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.TANGGAL_UNDANG IS NOT NULL AND X.REKANAN_ID = '".$rekanan_id."')
  //               ) A WHERE 1 = 1
  //   ";
    }

    function selectByParamsPaketRekananNonTender($paramsArray=array(),$limit=-1,$from=-1, $rekanan_id='',$statement='')
	{
        $str = "
                SELECT * FROM
                (
                    SELECT
                       A.PAKET_ID, A.PAKET_UUID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                       D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                       G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                       A.LOKASI, A.ALAMAT, A.TELEPON,
                       A.FAX, A.EMAIL, A.SYARAT,
                       A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                       A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.ALASAN_ULANG, A.UNIT_KERJA_ID,
                       COALESCE((SELECT MAX(TO_CHAR(tanggal_awal, 'YYYY-MM-DD')) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),TO_CHAR(A.TANGGAL, 'YYYY-MM-DD')) tanggal_tahap,
                       A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI, A.MULTI_BIDANG_USAHA
                    FROM    PAKET A
                        LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                        LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                        LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                        LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                        LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                        LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                        LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                        LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                        LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                    WHERE
                          A.PUBLISH_PAKET = 1
                          AND
                          EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.TANGGAL_UNDANG IS NOT NULL AND X.REKANAN_ID = '".$rekanan_id."')  ) A WHERE 1 = 1
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

		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectBidding($paket_Id)
	{
		// $str = "
  //                       SELECT
  //                          A.BIDDING_MENIT, A.BIDDING_MENIT_TAMBAHAN, TO_CHAR(A.BIDDING_MULAI, 'FMDD-MM-YYYY-HH24-MI-SS') BIDDING_MULAI
  //                           FROM    PAKET A
  //                       WHERE 1 = 1 AND
  //                                 A.PAKET_ID = ".$paket_Id."
	 //  ";

		$str = "
						SELECT A.bidding_menit, A.bidding_mulai bb, A.bidding_menit_tambahan, A.bidding_start,
						case when CURRENT_TIMESTAMP <= A.bidding_start THEN null
						else A.bidding_mulai END bidding_mulai
						FROM (
						SELECT A.BIDDING_MENIT, A.BIDDING_MENIT_TAMBAHAN, TO_CHAR(A.BIDDING_MULAI, 'FMDD-MM-YYYY-HH24-MI-SS') BIDDING_MULAI,
						A.BIDDING_MULAI - (A.bidding_menit ||' minutes')::interval bidding_start
						FROM    PAKET A
						WHERE 1 = 1 AND A.PAKET_ID = ".$paket_Id."
						) A";

		$this->query = $str;
		return $this->select($str);
    }

    function selectByParamsPaketFungsional($paramsArray=array(),$limit=-1,$from=-1, $user_id='',$statement='')
	{
            $str = "
                    SELECT DISTINCT * FROM
                    (
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           TO_CHAR(COALESCE((SELECT MAX(TANGGAL_AWAL) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID AND X.NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) , 'YYYY-MM-DD') TANGGAL_TAHAP,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA, I.TAHUN_ANGGARAN
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                        WHERE A.PUBLISH_PAKET = 1
                    UNION ALL
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           TO_CHAR(COALESCE((SELECT MAX(TANGGAL_AWAL) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID AND X.NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) , 'YYYY-MM-DD') TANGGAL_TAHAP,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA, I.TAHUN_ANGGARAN
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
							WHERE
                              EXISTS(SELECT 1 FROM PAKET_PIHAK_LAIN X WHERE X.PAKET_ID = A.PAKET_ID AND X.STATUS = 1 AND X.USER_LOGIN_ID = '".$user_id."')
                        ) A WHERE 1 = 1
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

		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";
		// echo $str; die;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    // ikn 20190902
    function getDashboard($unitkerja,$tahun)
	{
		if ($tahun == '' || $tahun == 'all') {
			if ($unitkerja == 'all') {
				$str = "
					SELECT a.month_angka, a.month_ina,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=1) Tender,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=3) Tender_Terbatas,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=8) Kompetisi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=10) Tender_Kualifikasi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=7) Tender_Cepat,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=2) Pengadaan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=5) Penunjukan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=6) Pembelian_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=9) Pembelian_Offline
					from (
					SELECT cast(a.month_angka as text), a.month_ina
					from month a
					order by a.month_id asc
					) a
		  		";
			} else {
				$str = "
					SELECT a.month_angka, a.month_ina,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=1 and z.unit_kerja_id='".$unitkerja."') Tender,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=3 and z.unit_kerja_id='".$unitkerja."') Tender_Terbatas,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=8 and z.unit_kerja_id='".$unitkerja."') Kompetisi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=10 and z.unit_kerja_id='".$unitkerja."') Tender_Kualifikasi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=7 and z.unit_kerja_id='".$unitkerja."') Tender_Cepat,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=2 and z.unit_kerja_id='".$unitkerja."') Pengadaan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=5 and z.unit_kerja_id='".$unitkerja."') Penunjukan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=6 and z.unit_kerja_id='".$unitkerja."') Pembelian_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=9 and z.unit_kerja_id='".$unitkerja."') Pembelian_Offline
					from (
					SELECT cast(a.month_angka as text), a.month_ina
					from month a
					order by a.month_id asc
					) a
		  		";
			}
		} else {
			if ($unitkerja == 'all') {
				$str = "
					SELECT a.month_angka, a.month_ina,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=1
					and z.tahun_anggaran = ".$tahun.") Tender,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=3
					and z.tahun_anggaran = ".$tahun.") Tender_Terbatas,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=8
					and z.tahun_anggaran = ".$tahun.") Kompetisi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=10
					and z.tahun_anggaran = ".$tahun.") Tender_Kualifikasi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=7
					and z.tahun_anggaran = ".$tahun.") Tender_Cepat,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=2
					and z.tahun_anggaran = ".$tahun.") Pengadaan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=5
					and z.tahun_anggaran = ".$tahun.") Penunjukan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=6
					and z.tahun_anggaran = ".$tahun.") Pembelian_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=9
					and z.tahun_anggaran = ".$tahun.") Pembelian_Offline
					from (
					SELECT cast(a.month_angka as text), a.month_ina
					from month a
					order by a.month_id asc
					) a
		  	";
			} else {
				$str = "
						SELECT a.month_angka, a.month_ina,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=1
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=3
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender_Terbatas,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=8
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Kompetisi,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=10
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender_Kualifikasi,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=7
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender_Cepat,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=2
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Pengadaan_Langsung,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=5
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Penunjukan_Langsung,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=6
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Pembelian_Langsung,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=9
						and z.tahun_anggaran = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Pembelian_Offline
						from (
						SELECT cast(a.month_angka as text), a.month_ina
						from month a
						order by a.month_id asc
						) a
			  	";
		 	}
		}
	  	$this->select($str);

		return $this->query = $str;
	}

	function getDashboardDetailPaket($metode_lelang,$tahun,$bulan)
	{
		if ($tahun == '' || $tahun == 'all') {
				$str = "
					SELECT a.* from view_paket_dashboard a
					WHERE a.paket_metode_lelang_id = '".$metode_lelang."'
		  		";
		} else {
				$str = "
					SELECT a.* from view_paket_dashboard a
					WHERE a.paket_metode_lelang_id = '".$metode_lelang."' and a.tahun_anggaran = '".$tahun."'
			  	";
		}

    if ($bulan) {
      $str .= " and a.month_ina = '".$bulan."'";
    }

  	$this->select($str);

		return $this->query = $str;
	}

	function getDashboardDetailPaket2($metode_lelang,$tahun,$bulan,$unitkerja)
	{
		if ($tahun == '' || $tahun == 'all') {
			$str = "
				SELECT a.* from view_paket_dashboard a
				WHERE a.unit_kerja_id='".$unitkerja."' and a.paket_metode_lelang_id = '".$metode_lelang."' and a.month_ina = '".$bulan."'
	  		";
		} else {
			$str = "
				SELECT a.* from view_paket_dashboard a
				WHERE a.unit_kerja_id='".$unitkerja."' and a.paket_metode_lelang_id = '".$metode_lelang."' and a.tahun_anggaran = '".$tahun."' and a.month_ina = '".$bulan."'
		  	";
		}

	  	$this->select($str);

		return $this->query = $str;
	}

	function getDashboardPie($unitkerja,$tahun=null)
	{
		if ($tahun == '' || $tahun == 'all') {
			if ($unitkerja == 'all') {
				$str = "SELECT paket_jenis_id_nama, count(paket_jenis_id) total
					FROM view_paket_dashboard
					GROUP BY paket_jenis_id_nama, paket_jenis_id
					ORDER BY paket_jenis_id ASC
					";
			} else {
				$str = "SELECT paket_jenis_id_nama, count(paket_jenis_id) total
					FROM view_paket_dashboard
					WHERE unit_kerja_id = '".$unitkerja."'
					GROUP BY paket_jenis_id_nama, paket_jenis_id
					ORDER BY paket_jenis_id ASC
					";
			}

		} else {
			if ($unitkerja == 'all') {
				$str = "SELECT paket_jenis_id_nama, count(paket_jenis_id) total
					FROM view_paket_dashboard
					WHERE tahun_anggaran = '".$tahun."'
					GROUP BY paket_jenis_id_nama, paket_jenis_id
					ORDER BY paket_jenis_id ASC
					";
			} else {
				$str = "SELECT paket_jenis_id_nama, count(paket_jenis_id) total
					FROM view_paket_dashboard
					WHERE tahun_anggaran = '".$tahun."' AND unit_kerja_id = '".$unitkerja."'
					GROUP BY paket_jenis_id_nama, paket_jenis_id
					ORDER BY paket_jenis_id ASC
					";
			}
		}
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardPieDetail($unitkerja,$paketjenis,$tahun)
	{
    $str = "
      SELECT a.* from view_paket_dashboard a
      WHERE a.paket_jenis_id = '".$paketjenis."'
      ";

    if ($unitkerja != 'all') {
      $str .= " and a.unit_kerja_id = '".$unitkerja."' ";
		}

		if ($tahun == '' || $tahun == 'all') {
		} else {
				$str .= " and a.tahun_anggaran = '".$tahun."' ";
		}

  	$this->select($str);

		return $this->query = $str;
	}

	function getDashboardBar2($unitkerja,$tahun=null)
	{
		if ($tahun == '' || $tahun == 'all') {
			if ($unitkerja == 'all') {
				$str = "SELECT a.user_login_id, b.user_jabatan,
						count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
						from
						(
							SELECT z.user_login_id, z.permohonan_paket_id,
							(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi
							from permohonan_paket z
						) a
						inner join user_login b on a.user_login_id=b.user_login_id
						group by a.user_login_id, b.user_jabatan
						";
			} else {
				$str = "SELECT a.user_login_id, b.user_jabatan,
						count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
						from
						(
							SELECT z.user_login_id, z.permohonan_paket_id,
							(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi
							from permohonan_paket z
						) a
						inner join user_login b on a.user_login_id=b.user_login_id
						where b.unit_kerja_id = '".$unitkerja."'
						group by a.user_login_id, b.user_jabatan
						";
			}
		} else {
			if ($unitkerja == 'all') {
				$str = "SELECT a.* from (
							SELECT a.user_login_id, b.user_jabatan, a.tahun_anggaran,
							count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
							from
							(
								SELECT z.user_login_id, z.permohonan_paket_id, z.tahun_anggaran,
								(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
								) total_realisasi
								from permohonan_paket z
							) a
							inner join user_login b on a.user_login_id=b.user_login_id
							group by a.user_login_id, b.user_jabatan, a.tahun_anggaran
						) a WHERE a.tahun_anggaran = '".$tahun."'
						";
			} else {
				$str = "SELECT a.* from (
							SELECT a.user_login_id, b.user_jabatan, a.tahun_anggaran,
							count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
							from
							(
								SELECT z.user_login_id, z.permohonan_paket_id, z.tahun_anggaran,
								(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
								) total_realisasi
								from permohonan_paket z
							) a
							inner join user_login b on a.user_login_id=b.user_login_id
							where b.unit_kerja_id = '".$unitkerja."'
							group by a.user_login_id, b.user_jabatan, a.tahun_anggaran
						) a WHERE a.tahun_anggaran = '".$tahun."'
						";
			}
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardBar2Detail($tahun=null,$user_login_id)
	{
		if ($tahun == '' || $tahun == 'all') {
			$str = "	SELECT A.* FROM (
						SELECT x.paket_id, z.user_login_id, z.permohonan_paket_id, z.nama, z.nilai, z.tahun_anggaran,
							(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi
							from permohonan_paket z
							left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
						) A
						WHERE A.user_login_id = '".$user_login_id."'
					";
		} else {
			$str = "SELECT A.* FROM (
					SELECT x.paket_id, z.user_login_id, z.permohonan_paket_id, z.nama, z.nilai, z.tahun_anggaran,
						(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
						) total_realisasi
						from permohonan_paket z
						left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
					) A
						WHERE A.user_login_id = '".$user_login_id."' AND A.tahun_anggaran = '".$tahun."'
					";
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardBar2Detail2($tahun=null,$user_login_id,$unitkerja=null)
	{
		if ($tahun == '' || $tahun == 'all') {
			if ($unitkerja == 'all') {
				$str = "	SELECT A.* FROM (
							SELECT x.paket_id, z.user_login_id, z.unit_kerja_id, z.permohonan_paket_id, z.nama, z.nilai, extract(year from z.tanggal) tanggal_permohoanan,
								(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
								) total_realisasi
								from permohonan_paket z
								left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
							) A
							WHERE A.user_login_id = '".$user_login_id."'
						";
			} else {
				$str = "	SELECT A.* FROM (
							SELECT x.paket_id, z.user_login_id, z.unit_kerja_id, z.permohonan_paket_id, z.nama, z.nilai, extract(year from z.tanggal) tanggal_permohoanan,
								(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
								) total_realisasi
								from permohonan_paket z
								left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
							) A
							WHERE A.user_login_id = '".$user_login_id."' AND A.unit_kerja_id = '".$unitkerja."'
							";
			}
		} else {
			if ($unitkerja == 'all') {
				$str = "SELECT A.* FROM (
						SELECT x.paket_id, z.user_login_id, z.unit_kerja_id, z.permohonan_paket_id, z.nama, z.nilai, extract(year from z.tanggal) tanggal_permohoanan,
							(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi
							from permohonan_paket z
							left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
						) A
							WHERE A.user_login_id = '".$user_login_id."' AND A.tanggal_permohoanan = '".$tahun."'
						";
			} else {
				$str = "SELECT A.* FROM (
						SELECT x.paket_id, z.user_login_id, z.unit_kerja_id, z.permohonan_paket_id, z.nama, z.nilai, extract(year from z.tanggal) tanggal_permohoanan,
							(SELECT '1' total from paket a where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi
							from permohonan_paket z
							left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
						) A
							WHERE A.user_login_id = '".$user_login_id."' AND A.tanggal_permohoanan = '".$tahun."' AND A.unit_kerja_id = '".$unitkerja."'
						";
			}
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardGauge($unitkerja,$tahun=null)
	{
		if ($tahun == '' || $tahun == 'all') {
			if ($unitkerja == 'all') {
				$str = "SELECT
						(select count(paket_id) total_paket from view_dashboard_paket_proses),
						(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where proses = '1')
						";
			} else {
				$str = "SELECT
					(select count(paket_id) total_paket from view_dashboard_paket_proses where unit_kerja_id = '".$unitkerja."'),
					(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where proses = '1' and unit_kerja_id = '".$unitkerja."')
					";
			}
		} else {
			if ($unitkerja == 'all') {
				$str = "SELECT
						(select count(paket_id) total_paket from view_dashboard_paket_proses where tahun_anggaran='".$tahun."'),
						(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where proses = '1' and tahun_anggaran='".$tahun."')
						";
			} else {
				$str = "SELECT
						(select count(paket_id) total_paket from view_dashboard_paket_proses where tahun_anggaran='".$tahun."' and unit_kerja_id = '".$unitkerja."'),
						(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where proses = '1' and tahun_anggaran='".$tahun."' and unit_kerja_id = '".$unitkerja."')
						";
			}
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardGaugeDetail($unitkerja,$tahun=null)
	{
    $str = "SELECT * from view_dashboard_paket_proses WHERE 1=1 ";

    if ($unitkerja != 'all') {
      $str .= " and a.unit_kerja_id = '".$unitkerja."' ";
		}

		if ($tahun == '' || $tahun == 'all') {
		} else {
			$str .= " and tahun_anggaran='".$tahun."'";
		}

    $str .= " order by proses desc";
// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

    function getCountByParamsPaketFungsional($paramsArray=array(),$user_id='',$statement='')
	{
            $str = "SELECT count(1) rowcount from (
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and X.NAMA = 'Pembuatan Paket Lelang' ),A.TANGGAL) tanggal_tahap,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                        WHERE A.PUBLISH_PAKET = 1
                    UNION ALL
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and X.NAMA = 'Pembuatan Paket Lelang' ),A.TANGGAL) tanggal_tahap,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
							WHERE
                              EXISTS(SELECT 1 FROM PAKET_PIHAK_LAIN X WHERE X.PAKET_ID = A.PAKET_ID AND X.STATUS = 1 AND X.USER_LOGIN_ID = '".$user_id."')
                        ) A WHERE 1 = 1
            ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ";

		$this->query = $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsPaketFungsional2($paramsArray=array(), $rekanan_id='', $statement='')
	{
    $str = "
            SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM
                (
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) tanggal_tahap,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                        WHERE
                              1=1

                ";
      while(list($key,$val)=each($paramsArray))
  		{
  			// $str .= " AND $key = '$val' ";
  			// ikn 20190218
  			$pecah = explode("||", $key);
  			if (count($pecah) > 1) {
  				$str .= "AND $pecah[0] $pecah[1] $val ";
  			} else {
  				$str .= " AND $key = '$val' ";
  			}
  		}
  		$str .= $statement;
  		$str .= ') A where 1 = 1';
      // echo $str; die();
  		$this->select($str);
  		$this->query = $str;
  		if($this->firstRow())
  			return $this->getField("ROWCOUNT");
  		else
  			return 0;
    }

	function selectByParamsMonitoringCetak($paramsArray=array(),$limit=-1,$from=-1, $statement='', $tahun='')
	{
		$str = "SELECT * FROM (
                            SELECT
                            A.PUBLISH_PAKET, A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_ID ID_PAKET,
                            B.NAMA METODE_LELANG, A.NAMA, E.NAMA PAKET_JENIS,
                            A.LOKASI, A.TANGGAL, A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                            A.NILAI_OWNER_ESTIMATE, A.USER_LOGIN_ID,
                            COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP WHERE PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' ),A.TANGGAL) tanggal_tahap
                            FROM
                                PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN REKANAN_KUALIFIKASI G ON  A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON  A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            WHERE 1=1
                          ) A
                          WHERE to_char(TANGGAL_TAHAP, 'YYYY') = '".$tahun."'
	  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsPaketAktif($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_ID, USER_LOGIN_ID, TANGGAL, KODE, NAMA, JUMLAH_PESERTA,
					   TAHAP, TO_CHAR(TANGGAL_AWAL, 'DD-MM-YYYY') TANGGAL_AWAL, TO_CHAR(TANGGAL_AKHIR, 'DD-MM-YYYY') TANGGAL_AKHIR
				  FROM PAKET_AKTIF A
				  WHERE 1 = 1
	  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY TANGGAL_AWAL DESC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsPaketSelesai($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT PAKET_ID, USER_LOGIN_ID, TANGGAL, KODE, NAMA, JUMLAH_PESERTA,
					   REKANAN, NILAI, NILAI_NEGOSIASI
				  FROM PAKET_SELESAI A
				WHERE 1 = 1
	  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY TANGGAL DESC";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParamsPaketAktif($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(*) AS ROWCOUNT
                 FROM PAKET_AKTIF A
				  WHERE 1 = 1 ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement;

		$this->select($str);
		$this->query = $str;
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

	 function getCountByParamsPaketSelesai($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(*) AS ROWCOUNT
                 FROM PAKET_SELESAI A
				  WHERE 1 = 1
	 		   ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement;

		$this->select($str);
		$this->query = $str;
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function selectByParamsPaketPekerjaanLaporan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
            $str = "
                    SELECT
                       A.NAMA NAMA_PEKERJAAN, A.LOKASI, TO_CHAR(A.TANGGAL, 'MM') BULAN, J.NOTA_DINAS NOTA_DINAS,
					   J.NO_PPA PPA, J.TANGGAL TANGGAL_PPA, J.TAHUN_ANGGARAN,
                        '' PO, F.NAMA PIC,
                       C.NAMA METODE_KUALIFIKASI, A.SISTEM_SAMPUL, D.NAMA METODE_EVALUASI, E.NAMA JENIS_PEKERJAAN, B.NAMA METODE_PEKERJAAN, G.NAMA KUALIFIKASI_USAHA,
											 CASE WHEN A.BIDDING = '1' THEN 'e-Reverse Auction'
											 ELSE 'Negosiasi' END SISTEM_NEGOSIASI, EF.USER_NAMA PIC_PAKET, J.POSTING_BY PENGGUNA,
                       '' KETERANGAN, '' DIREKTORAT, '' SUBDIT, '' NAMA_PEJABAT,
                       CASE WHEN NILAI_MATA_UANG = 'USD' THEN NULL ELSE A.NILAI_OWNER_ESTIMATE END NILAI_OE,
                       CASE WHEN NILAI_MATA_UANG = 'USD' THEN A.NILAI_OWNER_ESTIMATE END NILAI_OE_USD,
                       CASE WHEN NILAI_MATA_UANG = 'USD' THEN NULL
                       ELSE
                        (SELECT SUM(JUMLAH) FROM REKANAN_PAKET_PENAWARAN X INNER JOIN PAKET_REKANAN Y ON X.PAKET_REKANAN_ID = Y.PAKET_REKANAN_ID WHERE Y.PAKET_ID = A.PAKET_ID AND Y.REKANAN_ID = A.REKANAN_ID_PEMENANG)
                       END NILAI_PENAWARAN,
                       CASE WHEN NILAI_MATA_UANG = 'USD' THEN
                        (SELECT SUM(JUMLAH) FROM REKANAN_PAKET_PENAWARAN X INNER JOIN PAKET_REKANAN Y ON X.PAKET_REKANAN_ID = Y.PAKET_REKANAN_ID WHERE Y.PAKET_ID = A.PAKET_ID AND Y.REKANAN_ID = A.REKANAN_ID_PEMENANG)
                       END NILAI_PENAWARAN_USD,
                       CASE WHEN NILAI_MATA_UANG = 'USD' THEN NULL ELSE A.NILAI_NEGOSIASI END NILAI_NEGOSIASI,
                       CASE WHEN NILAI_MATA_UANG = 'USD' THEN A.NILAI_NEGOSIASI END NILAI_NEGOSIASI_USD,
                       '' EFISIENSI, ROUND((NILAI_NEGOSIASI / NILAI_OWNER_ESTIMATE) * 100, 2) PERSEN_OE,
                       (SELECT NAMA FROM REKANAN X WHERE X.REKANAN_ID = A.REKANAN_ID_PEMENANG) PELAKSANA, '' TANGGAL_NID, '' HUKUM, '' KETERANGAN2, J.KODE_RUP, J.KODE_PR
                    FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN USER_LOGIN EF ON A.USER_LOGIN_ID = EF.USER_LOGIN_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON EF.NIP = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET J ON A.PERMOHONAN_PAKET_ID = J.PERMOHONAN_PAKET_ID
                    WHERE 1 = 1
            ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$str .= $statement." ORDER BY A.TANGGAL ASC ";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

	function selectByParamsLike($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
				SELECT
					PAKET_ID, PAKET_METODE_LELANG_ID, PAKET_METODE_KUALIFIKASI_ID,
					   PAKET_METODE_EVALUASI_ID, PAKET_JENIS_ID, USER_LOGIN_ID,
					   REKANAN_KUALIFIKASI_ID, NAMA, URAIAN,
					   LOKASI, ALAMAT, TELEPON,
					   FAX, EMAIL, SYARAT,
					   TANGGAL, PUBLISH_PAKET, PUBLISH_PAKET_TANGGAL,
					   PUBLISH_PEMENANG, PUBLISH_PEMENANG_TANGGAL, NILAI,
					   NILAI_OWNER_ESTIMATE, PASS_GRADE
					FROM PAKET;
				    WHERE PAKET_ID IS NOT NULL";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key LIKE '%$val%' ";
		}

		$str .= $statement." ORDER BY NAMA ASC";
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }
    /**
    * Hitung jumlah record berdasarkan parameter (array).
    * @param array paramsArray Array of parameter. Contoh array("id"=>"xxx","PAKET_METODE_EVALUASI_ID"=>"yyy")
    * @return long Jumlah record yang sesuai kriteria
    **/
    function getPaketAktif($rekanan_id, $paket_id, $state='')
	{
		/*
		$str = "SELECT 1 ROWCOUNT
			  	FROM REKANAN_BIDANG_USAHA A
			 	WHERE REKANAN_ID = '".$rekanan_id."'
			   	AND EXISTS (SELECT 1
                FROM PAKET_BIDANG_USAHA X
                WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID AND PAKET_ID = '".$paket_id."') ".$state;

		$str = "SELECT 1 ROWCOUNT
			  	FROM REKANAN_BIDANG_USAHA A
			 	WHERE REKANAN_ID = '".$rekanan_id."' ".$state;

		//echo $str;
		$this->query = $str;
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else */

			return 1;
    }

    function getPaketPendaftaran($paket_id,$urut=null)
	{
		// kadang bisa ini DD/MM/YYYY HH24:MI:SS
		// kadang bisa ini yyyy/mm/dd hh:mi:ss
		//$str = "SELECT 1 ROWCOUNT FROM PAKET_TAHAP WHERE (TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss') BETWEEN TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') AND TO_DATE(TANGGAL_AKHIR, 'yyyy/mm/dd hh:mi:ss') OR TO_DATE(TANGGAL_AWAL, 'yyyy/mm/dd hh:mi:ss') = TO_DATE(CURRENT_DATE, 'yyyy/mm/dd hh:mi:ss'))AND URUT = 3 AND PAKET_ID = '".$paket_id."' ";
		// Revisi ikn 20190210
		$str = "SELECT 1 ROWCOUNT FROM PAKET_TAHAP WHERE (CURRENT_TIMESTAMP BETWEEN TANGGAL_AWAL
									AND COALESCE(TANGGAL_AKHIR, TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI'))) AND URUT = ".$urut." AND PAKET_ID = '".$paket_id."' ";
		// $str = "SELECT 1 ROWCOUNT FROM PAKET_TAHAP WHERE (CURRENT_TIMESTAMP BETWEEN TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 00:00', 'DDMMYYYY HH24:MI') AND COALESCE(TANGGAL_AKHIR, TO_TIMESTAMP(TO_CHAR(TANGGAL_AWAL, 'DDMMYYYY') || ' 23:59', 'DDMMYYYY HH24:MI')))  AND URUT = 3 AND PAKET_ID = '".$paket_id."' ";
		// echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
		{
			echo $this->errorMsg;
			return 0;
		}
    }

    function getPaketRekeningKoran($rekanan_id, $bulan)
	{
		$str = "select count(*) ROWCOUNT from (
                        select distinct bulan,tahun from
                        REKANAN_REKENING_KORAN A
                                WHERE REKANAN_ID = {$rekanan_id} AND CONCAT(BULAN,TAHUN) IN ({$bulan})
                        ) A ";

		$this->select($str);
		$this->query = $str;
		//echo $str;exit;
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

	function getPaketPajakRekanan($rekanan_id, $bulan, $tipe)
	{
		$str = "select count(*) ROWCOUNT from (
                            select distinct bulan,tahun from
                            REKANAN_PAJAK A
                            WHERE REKANAN_ID = {$rekanan_id}
                            AND CONCAT(BULAN,TAHUN) IN ({$bulan})
                            AND TIPE = '{$tipe}'
                            AND NOMOR IS NOT NULL
                        ) A ";

		$this->select($str);
//		echo $str;
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getPaketPajak($rekanan_id, $bulan, $tahun)
	{
		$str = "SELECT SUM(ROWCOUNT) ROWCOUNT FROM
				(
				SELECT 1 ROWCOUNT FROM REKANAN_PAJAK A
				WHERE REKANAN_ID = ".$rekanan_id." AND TIPE = 1 AND (TAHUN = ".$tahun." OR TAHUN = ".($tahun-1).")
				UNION ALL
				SELECT COUNT(REKANAN_PAJAK_ID) ROWCOUNT FROM REKANAN_PAJAK A
				WHERE REKANAN_ID = ".$rekanan_id." AND TIPE = 2 AND CONCAT(BULAN,TAHUN) IN(".$bulan.") AND NOMOR IS NOT NULL
				UNION ALL
				SELECT COUNT(REKANAN_PAJAK_ID) ROWCOUNT FROM REKANAN_PAJAK A
				WHERE REKANAN_ID = ".$rekanan_id." AND TIPE = 3 AND CONCAT(BULAN,TAHUN) IN(".$bulan.") AND NOMOR IS NOT NULL) A
              ";

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getPaketPengalaman($paket_id, $rekanan_id)
	{
		/*
		permintaan mas andri tidak perlu pengecekan bidang usaha
		$str = "SELECT COUNT(REKANAN_PENGALAMAN_BIDANG_ID) AS ROWCOUNT FROM REKANAN_PENGALAMAN_BIDANG A, REKANAN_PENGALAMAN B
				WHERE A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND REKANAN_ID = ".$rekanan_id."
					  AND EXISTS (SELECT 1
					  FROM PAKET_BIDANG_USAHA X
					  WHERE X.BIDANG_USAHA_ID = A.BIDANG_USAHA_ID AND PAKET_ID = ".$paket_id.")  ";
		*/
		$str = "SELECT COUNT(REKANAN_PENGALAMAN_BIDANG_ID) AS ROWCOUNT FROM REKANAN_PENGALAMAN_BIDANG A, REKANAN_PENGALAMAN B
				WHERE A.REKANAN_PENGALAMAN_ID = B.REKANAN_PENGALAMAN_ID AND REKANAN_ID = ".$rekanan_id." ";

		$this->select($str);

		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getPaketMengikuti($rekanan_id, $paket_id)
	{
		$str = "SELECT 1 ROWCOUNT
				  FROM PAKET_REKANAN A
				WHERE REKANAN_ID = '".$rekanan_id."' AND PAKET_ID = '".$paket_id."' AND A.TANGGAL_DAFTAR IS NOT NULL";
		// echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(*) AS ROWCOUNT
                        from (
                  SELECT A.* FROM (
                    SELECT
                    a.PAKET_ID,K.AKTIF, K.STATUS,A.PAKET_METODE_LELANG_ID,B.NAMA METODE_LELANG,A.NAMA,A.PUBLISH_PAKET,H.UNIT_KERJA_ID, A.USER_LOGIN_ID, I.USER_LOGIN_ID AS USER_PERENCANA,
                                        COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and X.NAMA = 'Pembuatan Paket Lelang' LIMIT 1 ),A.TANGGAL) tanggal_tahap,
										A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.LOKASI, A.TANGGAL, date_part('month'::text, A.tanggal) AS bulan, date_part('month'::text, I.tanggal) AS bulan_permohonan, date_part('year'::text, A.tanggal) AS TAHUN, date_part('year'::text, I.tanggal) AS TAHUN_PERMOHONAN, I.TAHUN_ANGGARAN,
			A.PERMOHONAN_PAKET_ID, I.KODE_RUP, I.KODE_PR, A.PPK
                    FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
														LEFT JOIN VIEW_PAKET_FILTER K ON A.PAKET_ID=K.PAKET_ID
                    ) A
                    WHERE 1 = 1 ";
		while(list($key,$val)=each($paramsArray))
		{
			// $str .= " AND $key = '$val' ";
			// ikn 20190218
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .= $statement;
		$str .= ') A where 1 = 1';

		$this->select($str);
		$this->query = $str;
		// echo $str; exit();
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getSumByParams($paramsArray=array(), $statement='')
	{
		$str = "select SUM(NILAI) AS ROWCOUNT
                        from ( SELECT
                      A.NILAI, A.PAKET_METODE_LELANG_ID, A.TANGGAL, A.UNIT_KERJA_ID, A.USER_LOGIN_ID, date_part('month'::text, A.tanggal) AS bulan, date_part('month'::text, I.tanggal) AS bulan_permohonan, date_part('year'::text, A.tanggal) AS TAHUN, date_part('year'::text, I.tanggal) AS TAHUN_PERMOHONAN, I.TAHUN_ANGGARAN
                    FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                    WHERE 1 = 1 ) A where 1 = 1";
		while(list($key,$val)=each($paramsArray))
		{
			// $str .= " AND $key = '$val' ";
			// ikn 20190218
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .= $statement;

		$this->select($str);
		$this->query = $str;
		// echo $str; exit();
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsPaketRekanan($paramsArray=array(), $rekanan_id='', $statement='')
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM
                (
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang' ),A.TANGGAL) tanggal_tahap,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                        WHERE
                              A.PUBLISH_PAKET = 1
                    UNION ALL
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) tanggal_tahap,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                        WHERE
                              EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.TANGGAL_UNDANG IS NOT NULL AND X.REKANAN_ID = '".$rekanan_id."')
                    ) A WHERE 1 = 1
                ";
		while(list($key,$val)=each($paramsArray))
		{
			// ikn 20190218
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}

                $str .= $statement;
//                $this->query = $str;
                // echo $str; die();
		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function getCountByParamsPaketRekanan2($paramsArray=array(), $rekanan_id='', $statement='')
	{
    $str = "
            SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM
                (
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) tanggal_tahap,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                        WHERE
                              EXISTS(SELECT 1 FROM PAKET_REKANAN X WHERE X.PAKET_ID = A.PAKET_ID AND X.TANGGAL_UNDANG IS NOT NULL AND X.REKANAN_ID = '".$rekanan_id."')

                ";
      while(list($key,$val)=each($paramsArray))
  		{
  			// $str .= " AND $key = '$val' ";
  			// ikn 20190218
  			$pecah = explode("||", $key);
  			if (count($pecah) > 1) {
  				$str .= "AND $pecah[0] $pecah[1] $val ";
  			} else {
  				$str .= " AND $key = '$val' ";
  			}
  		}
  		$str .= $statement;
  		$str .= ') A where 1 = 1';
      // echo $str; die();
  		$this->select($str);
  		$this->query = $str;
  		if($this->firstRow())
  			return $this->getField("ROWCOUNT");
  		else
  			return 0;
    }

    function getCountByParamsPaketRekanan3($paramsArray=array(), $rekanan_id='', $statement='')
	{
    $str = "
            SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM
                (
                        SELECT
                           A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                           D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                           G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                           A.LOKASI, A.ALAMAT, A.TELEPON,
                           A.FAX, A.EMAIL, A.SYARAT,
                           A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                           A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                           A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, H.NAMA UNIT_KERJA, A.ALASAN, A.UNIT_KERJA_ID,
                           COALESCE((SELECT MAX(tanggal_awal) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),A.TANGGAL) tanggal_tahap,
                           A.JENIS_PENGADAAN, PR_GROUP_NUMBER
                        FROM    PAKET A
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                        WHERE
                              1=1

                ";
      while(list($key,$val)=each($paramsArray))
  		{
  			// $str .= " AND $key = '$val' ";
  			// ikn 20190218
  			$pecah = explode("||", $key);
  			if (count($pecah) > 1) {
  				$str .= "AND $pecah[0] $pecah[1] $val ";
  			} else {
  				$str .= " AND $key = '$val' ";
  			}
  		}
  		$str .= $statement;
  		$str .= ') A where 1 = 1';
      // echo $str; die();
  		$this->select($str);
  		$this->query = $str;
  		if($this->firstRow())
  			return $this->getField("ROWCOUNT");
  		else
  			return 0;
    }

    function getCountByParamsMonitoring($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET A WHERE PAKET_ID IS NOT NULL ".$statement;
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

    function getPaketId($paramsArray=array(), $statement='')
	{
		$str = "SELECT PAKET_ID FROM PAKET A WHERE PAKET_ID IS NOT NULL ".$statement;
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->select($str);
		$this->query = $str;
		if($this->firstRow())
			return $this->getField("PAKET_ID");
		else
			return 0;
    }

    function getCountByParamsLike($paramsArray=array())
	{
		$str = "SELECT COUNT(PAKET_ID) AS ROWCOUNT FROM PAKET WHERE PAKET_ID IS NOT NULL ";
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

    function updateRescheduleAlasan1()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_1        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_1_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				// echo $str; die();
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan2()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_2        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_2_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan3()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_3        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_3_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan4()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_4        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_4_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan5()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_5        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_5_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan6()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_6        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_6_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

   function updateRescheduleAlasan7()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_7        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_7_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan8()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_8        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_8_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan9()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_9        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_9_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleAlasan10()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_10        = '".$this->getField("ALASAN")."',
					   RESCHEDULE_10_DATE   = NOW()
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function updateRescheduleKe()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "
				 UPDATE  PAKET
				SET
					   RESCHEDULE_KE        = ".$this->getField("RESCHEDULE_KE")."
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."

				";
				$this->query = $str;
		return $this->execQuery($str);
    }

    function reschedule()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_RESCHEDULE_ID", $this->getNextId("PAKET_RESCHEDULE_ID","PAKET_RESCHEDULE"));

		$str = "
				INSERT INTO PAKET_RESCHEDULE(
							PAKET_RESCHEDULE_ID, PAKET_TAHAP_ID, PAKET_ID,
							NAMA, URUT,
							TANGGAL_AWAL,
							TANGGAL_AKHIR,
							JAM_AWAL,
							JAM_AKHIR,
							TANGGAL_AWAL_BARU, TANGGAL_AKHIR_BARU, JAM_AWAL_BARU, JAM_AKHIR_BARU, RESCHEDULE_KE)
				VALUES ('".$this->getField("PAKET_RESCHEDULE_ID")."', '".$this->getField("PAKET_TAHAP_ID")."', '".$this->getField("PAKET_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("URUT")."',
					(SELECT TANGGAL_AWAL FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					(SELECT TANGGAL_AKHIR FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					(SELECT JAM_AWAL FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					(SELECT JAM_AKHIR FROM PAKET_TAHAP X WHERE X.PAKET_TAHAP_ID = '".$this->getField("PAKET_TAHAP_ID")."'),
					".$this->getField("TANGGAL_AWAL").", ".$this->getField("TANGGAL_AKHIR").", '".$this->getField("JAM_AWAL")."', '".$this->getField("JAM_AKHIR")."', '".$this->getField("RESCHEDULE_KE")."')
		";
		$this->query = $str;
		return $this->execQuery($str);
    }

  function reschedulebackup()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_RESCHEDULE_ID", $this->getNextId("PAKET_RESCHEDULE_ID","PAKET_RESCHEDULE"));

		$str = "
				INSERT INTO PAKET_RESCHEDULE(
							PAKET_RESCHEDULE_ID, PAKET_TAHAP_ID, PAKET_ID,
							NAMA, URUT,
							TANGGAL_AWAL,
							TANGGAL_AKHIR,
							JAM_AWAL,
							JAM_AKHIR,
							RESCHEDULE_KE)
				VALUES ('".$this->getField("PAKET_RESCHEDULE_ID")."', '".$this->getField("PAKET_TAHAP_ID")."', '".$this->getField("PAKET_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("URUT")."',
					'".$this->getField("TANGGAL_AWAL")."',
					'".$this->getField("TANGGAL_AKHIR")."',
					'".$this->getField("JAM_AWAL")."',
					'".$this->getField("JAM_AKHIR")."',
					'".$this->getField("RESCHEDULE_KE")."')
		";
		// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
  }

  function tahapReschedule()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("PAKET_TAHAP_ID", $this->getNextId("PAKET_TAHAP_ID","PAKET_TAHAP_RESCHEDULE"));

		$str = "
				INSERT INTO PAKET_TAHAP_RESCHEDULE(
							PAKET_TAHAP_ID, PAKET_ID,
							NAMA, HADIR, TAMPILKAN, URUT,
							TANGGAL_AWAL,
							TANGGAL_AKHIR,
							JAM_AWAL,
							JAM_AKHIR,
							RESCHEDULE_KE,
							UPDATED_BY,
							UPDATED_DATE)
				VALUES ('".$this->getField("PAKET_TAHAP_ID")."', '".$this->getField("PAKET_ID")."',
					'".$this->getField("NAMA")."', '".$this->getField("HADIR")."', '".$this->getField("TAMPILKAN")."', '".$this->getField("URUT")."',
					".$this->getField("TANGGAL_AWAL").",
					".$this->getField("TANGGAL_AKHIR").",
					'".$this->getField("JAM_AWAL")."',
					'".$this->getField("JAM_AKHIR")."',
					'".$this->getField("RESCHEDULE_KE")."',
					".$this->getField("CREATED_BY").",
					CURRENT_TIMESTAMP
					)
		";
		// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
  }

    function getUnitKerja($id)
	{
		$str = "SELECT NAMA, LOGO FROM UNIT_KERJA A WHERE UNIT_KERJA_ID = ".$id;
		$this->select($str);
		return $this->execQuery($str);
    }

    function updateNoBAHP()
	{
		$str = "
				UPDATE  PAKET
				SET
				NOMOR_BAHP        = '".$this->getField("NOMOR_BAHP")."',
				NOMOR_PAKET        = '".$this->getField("NOMOR_PAKET")."',
				UPDATED_BY        = ".$this->getField("USER_LOGIN_ID").",
			  UPDATED_DATE        = CURRENT_TIMESTAMP
				WHERE  PAKET_ID                    =  ".$this->getField("PAKET_ID")."
				";
				$this->query = $str;

		return $this->execQuery($str);
    }

    // Update 12.25
    function approvePaketPPK()
		{
			$str = "
					UPDATE  PAKET
					SET
					APPROVE_PPK = '".$this->getField("APPROVE_PPK")."',
				  APPROVE_BY = ".$this->USER_LOGIN_ID.",
				  APPROVE_DATE = CURRENT_TIMESTAMP
					WHERE  PAKET_ID =  ".$this->getField("PAKET_ID")."
					";
			// echo $str; die;
					$this->query = $str;
			return $this->execQuery($str);
	  }

	  // Update 1.26
    function approvePaketManager()
		{
			$str = "
					UPDATE  PAKET
					SET
					APPROVE_MANAGER = '".$this->getField("APPROVE_MANAGER")."',
				  APPROVE_MANAGER_BY = ".$this->USER_LOGIN_ID.",
				  APPROVE_MANAGER_DATE = CURRENT_TIMESTAMP
					WHERE  PAKET_ID =  ".$this->getField("PAKET_ID")."
					";
			// echo $str; die;
					$this->query = $str;
			return $this->execQuery($str);
	  }

	  function updatePICKontrak()
		{
			$str = "
					UPDATE  PAKET
					SET
					PIC_KONTRAK = '".$this->getField("PIC")."',
				  PIC_KONTRAK_BY = ".$this->USER_LOGIN_ID.",
				  PIC_KONTRAK_DATE = CURRENT_TIMESTAMP
					WHERE  PAKET_ID =  ".$this->getField("PAKET_ID")."
					";
			// echo $str; die;
					$this->query = $str;
			return $this->execQuery($str);
	  }

	  function updatePICKontrakBypass()
		{
			$str = "
					UPDATE  PAKET
					SET
					PIC_KONTRAK = '".$this->getField("PIC")."',
				  PIC_KONTRAK_BY = ".$this->USER_LOGIN_ID.",
				  PIC_KONTRAK_DATE = CURRENT_TIMESTAMP,
				  APPROVE_PPK = '1',
				  URAIAN = 'BYPASS',
				  APPROVE_DATE = CURRENT_TIMESTAMP
					WHERE  PAKET_ID =  ".$this->getField("PAKET_ID")."
					";
			// echo $str; die;
					$this->query = $str;
			return $this->execQuery($str);
	  }

  }
?>
