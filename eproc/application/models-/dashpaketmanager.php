<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Dashpaketmanager extends Entity{

	var $query; 

	function __construct(){
	  parent::__construct();
	}
  
  function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT
					A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID,
					   A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.USER_LOGIN_ID,
					   A.REKANAN_KUALIFIKASI_ID, A.NAMA, A.URAIAN,
					   A.LOKASI, A.ALAMAT, A.TELEPON,
					   A.FAX, A.EMAIL, A.SYARAT,
					   A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
					   A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
					   A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, A.PERMOHONAN_PAKET_ID, B.NOTA_DINAS PERMOHONAN_NOTA_DINAS, B.NAMA PERMOHONAN, A.JENIS_PENGADAAN,
					   A.NILAI_NEGOSIASI, A.SISTEM_SAMPUL, A.BAHASA, A.SISTEM_HARGA, A.NILAI_MATA_UANG, A.SISTEM_PPN, A.BIDDING_MENIT, A.BIDDING,
                       A.BOBOT_TEKNIS, A.BOBOT_HARGA, A.PASSING_GRADE, A.PENAWARAN_HARGA_MAKSIMAL, A.PUBLISH_EVALKUALIFIKASI, A.MULTI_PEMENANG, A.PPK
					FROM PAKET A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
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
					SELECT a.paket_tahap_id, a.nama, a.paket_id,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '1' order by aa.paket_tahap_id desc limit 1) as reschedule_1,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '2' order by aa.paket_tahap_id desc limit 1) as reschedule_2,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '3' order by aa.paket_tahap_id desc limit 1) as reschedule_3,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '4' order by aa.paket_tahap_id desc limit 1) as reschedule_4,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '5' order by aa.paket_tahap_id desc limit 1) as reschedule_5,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '6' order by aa.paket_tahap_id desc limit 1) as reschedule_6,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '7' order by aa.paket_tahap_id desc limit 1) as reschedule_7,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '8' order by aa.paket_tahap_id desc limit 1) as reschedule_8,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '9' order by aa.paket_tahap_id desc limit 1) as reschedule_9,
					(select concat(aa.tanggal_awal, ' || ' , aa.tanggal_akhir) from paket_tahap_reschedule aa where a.nama=aa.nama and a.paket_id=aa.paket_id and reschedule_ke = '10' order by aa.paket_tahap_id desc limit 1) as reschedule_10
					FROM paket_tahap a 
				) a 
					WHERE 1=1";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement." ORDER BY PAKET_TAHAP_ID ASC";
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  }

  function selectByParamsWithKatalog($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "
					SELECT
					A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID,
					   A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.USER_LOGIN_ID,
					   A.REKANAN_KUALIFIKASI_ID, A.NAMA, A.URAIAN,
					   A.LOKASI, A.ALAMAT, A.TELEPON,
					   A.FAX, A.EMAIL, A.SYARAT,
					   A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
					   A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
					   A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE, A.PERMOHONAN_PAKET_ID, B.NOTA_DINAS PERMOHONAN_NOTA_DINAS, B.NAMA PERMOHONAN, A.JENIS_PENGADAAN,
					   A.NILAI_NEGOSIASI, A.SISTEM_SAMPUL, A.BAHASA, A.SISTEM_HARGA, A.NILAI_MATA_UANG, A.SISTEM_PPN, A.BIDDING_MENIT, A.BIDDING,
                       A.BOBOT_TEKNIS, A.BOBOT_HARGA, A.PASSING_GRADE, A.ALASAN,
                       (SELECT V.STATUS FROM KATALOG_REKANAN V WHERE PAKET_ID=A.PAKET_ID LIMIT 1 )
					FROM PAKET A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
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

  function selectByPaketRekananKeterangan($paket_id, $paket_rekanan_id, $rekanan_id, $urut_kualifikasi1, $urut_penawaran1)
	{
		$str = "
				SELECT KETERANGAN FROM
				(
				SELECT COALESCE(TANGGAL_AKHIR, TANGGAL_AWAL) TANGGAL_BATAS, CASE WHEN COALESCE((SELECT COUNT(*) FROM REKANAN_EVAL_ADMIN WHERE PAKET_REKANAN_ID = ".$paket_rekanan_id."), 0) = 0 AND EXISTS(SELECT 1 FROM PAKET X WHERE X.PAKET_ID = A.PAKET_ID AND PAKET_METODE_KUALIFIKASI_ID = 1 AND JENIS_PENGADAAN = 'LELANG') THEN 'Anda gagal pada tahap kualifikasi karena tidak memasukkan data kualifikasi' END KETERANGAN
                                    FROM PAKET_TAHAP A WHERE PAKET_ID = ".$paket_id." AND TAMPILKAN = 1 AND URUT = ".$urut_kualifikasi1."
				UNION ALL
				SELECT COALESCE(TANGGAL_AKHIR, TANGGAL_AWAL) TANGGAL_BATAS, CASE WHEN COALESCE((SELECT COUNT(*)
                                FROM PAKET_DOKUMEN WHERE REKANAN_USER_ID = ".$rekanan_id." AND JENIS_DOKUMEN = 'PENAWARAN'), 0) = 0 THEN 'Anda gagal pada tahap penawaran karena tidak memasukkan dokumen penawaran' END KETERANGAN FROM PAKET_TAHAP WHERE PAKET_ID = ".$paket_id." AND TAMPILKAN = 1 AND URUT = ".$urut_penawaran1."
				) A
				WHERE TANGGAL_BATAS < CURRENT_DATE AND KETERANGAN IS NOT NULL
	  ";

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
                           A.PAKET_ID, A.NAMA, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_KUALIFIKASI_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID,
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
						   A.PUBLISH_BA_PENAWARAN2, A.BAHASA, A.SISTEM_HARGA, F.NIP NIP_PEMBUAT, PUBLISH_SPPBJ, PUBLISH_SPPBJ_TANGGAL, A.SISTEM_PPN, H.KODE_ENTITAS, A.BIDDING_MENIT, A.BIDDING, A.BIDDING_MULAI, K.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI, K.KODE_PR, K.KODE_RUP 
                            FROM    PAKET A
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
	//THE NEW ONE, TANGGAL DI TAHAPAN LELANG IKUT DIQUERY SEBAGAI ACUAN PEMBUATAN LELANG
        $str = "SELECT * from (
					SELECT
                       A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                       A.PAKET_METODE_KUALIFIKASI_ID,A.PERMOHONAN_PAKET_ID,
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
					   A.PUBLISH_SPPBJ, A.PUBLISH_SPPBJ_TANGGAL,A.BIDDING_MENIT, A.BIDDING, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI, I.KODE_RUP, I.KODE_PR
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

  function selectByParamsMonitoring2($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
	//THE NEW ONE, TANGGAL DI TAHAPAN LELANG IKUT DIQUERY SEBAGAI ACUAN PEMBUATAN LELANG
        $str = "select * from (
					SELECT
                       A.PAKET_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
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
					   A.PUBLISH_SPPBJ, A.PUBLISH_SPPBJ_TANGGAL,A.BIDDING_MENIT, A.BIDDING, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI
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
                       A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                       D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                       G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                       A.LOKASI, A.ALAMAT, A.TELEPON,
                       A.FAX, A.EMAIL, A.SYARAT,
                       A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                       A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.ALASAN_ULANG, A.UNIT_KERJA_ID,
                       COALESCE((SELECT MAX(TO_CHAR(tanggal_awal, 'YYYY-MM-DD')) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),TO_CHAR(A.TANGGAL, 'YYYY-MM-DD')) tanggal_tahap,
                       A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI
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

		$str .= $statement." ORDER BY TANGGAL_TAHAP DESC,PAKET_ID DESC";
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
 
    }

    function selectByParamsPaketRekananNonTender($paramsArray=array(),$limit=-1,$from=-1, $rekanan_id='',$statement='')
	{
        $str = "
                SELECT * FROM
                (
                    SELECT
                       A.PAKET_ID, A.PAKET_METODE_LELANG_ID, A.PAKET_METODE_EVALUASI_ID, A.PAKET_JENIS_ID, A.REKANAN_KUALIFIKASI_ID, B.NAMA METODE_LELANG, C.NAMA METODE_KUALIFIKASI,
                       D.NAMA METODE_EVALUASI, E.NAMA PAKET_JENIS, F.NAMA USER_LOGIN,
                       G.NAMA REKANAN_KUALIFIKASI, AMBIL_PAKET_BIDANG_USAHA(A.PAKET_ID) BIDANG_USAHA, A.NAMA, A.URAIAN,
                       A.LOKASI, A.ALAMAT, A.TELEPON,
                       A.FAX, A.EMAIL, A.SYARAT,
                       A.TANGGAL, A.PUBLISH_PAKET, A.PUBLISH_PAKET_TANGGAL,
                       A.PUBLISH_PEMENANG, A.PUBLISH_PEMENANG_TANGGAL, A.NILAI,
                       A.NILAI_OWNER_ESTIMATE, A.PASS_GRADE,H.NAMA UNIT_KERJA, A.ALASAN, A.ALASAN_ULANG, A.UNIT_KERJA_ID,
                       COALESCE((SELECT MAX(TO_CHAR(tanggal_awal, 'YYYY-MM-DD')) FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and NAMA = 'Pembuatan Paket Lelang'),TO_CHAR(A.TANGGAL, 'YYYY-MM-DD')) tanggal_tahap,
                       A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.SISTEM_HARGA, I.TAHUN_ANGGARAN, A.PUBLISH_EVALKUALIFIKASI
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
		$str = "
                        SELECT
                           A.BIDDING_MENIT, A.BIDDING_MENIT_TAMBAHAN, TO_CHAR(A.BIDDING_MULAI, 'FMDD-MM-YYYY-HH24-MI-SS') BIDDING_MULAI
                            FROM    PAKET A
                        WHERE 1 = 1 AND
                                  A.PAKET_ID = ".$paket_Id."
	  ";

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
					and z.tahun_permohonan = ".$tahun.") Tender,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=3
					and z.tahun_permohonan = ".$tahun.") Tender_Terbatas,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=8
					and z.tahun_permohonan = ".$tahun.") Kompetisi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=10
					and z.tahun_permohonan = ".$tahun.") Tender_Kualifikasi,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=7
					and z.tahun_permohonan = ".$tahun.") Tender_Cepat,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=2
					and z.tahun_permohonan = ".$tahun.") Pengadaan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=5
					and z.tahun_permohonan = ".$tahun.") Penunjukan_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=6
					and z.tahun_permohonan = ".$tahun.") Pembelian_Langsung,
					( SELECT count(z.paket_id)
					from view_paket_dashboard z
					where
					z.bulan_permohonan_angka = a.month_angka
					and z.paket_metode_lelang_id=9
					and z.tahun_permohonan = ".$tahun.") Pembelian_Offline
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
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=3
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender_Terbatas,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=8
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Kompetisi,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=10
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender_Kualifikasi,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=7
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Tender_Cepat,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=2
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Pengadaan_Langsung,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=5
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Penunjukan_Langsung,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=6
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Pembelian_Langsung,
						( SELECT count(z.paket_id)
						from view_paket_dashboard z
						where
						z.bulan_permohonan_angka = a.month_angka
						and z.paket_metode_lelang_id=9
						and z.tahun_permohonan = ".$tahun." and z.unit_kerja_id='".$unitkerja."') Pembelian_Offline
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
					WHERE a.paket_metode_lelang_id = '".$metode_lelang."' and a.tahun_permohonan = '".$tahun."'
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
				WHERE a.unit_kerja_id='".$unitkerja."' and a.paket_metode_lelang_id = '".$metode_lelang."' and a.tahun_permohonan = '".$tahun."' and a.month_ina = '".$bulan."'
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
					WHERE tahun_permohonan = '".$tahun."'
					GROUP BY paket_jenis_id_nama, paket_jenis_id
					ORDER BY paket_jenis_id ASC
					";
			} else {
				$str = "SELECT paket_jenis_id_nama, count(paket_jenis_id) total
					FROM view_paket_dashboard
					WHERE tahun_permohonan = '".$tahun."' AND unit_kerja_id = '".$unitkerja."'
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
				$str .= " and a.tahun_permohonan = '".$tahun."' ";
		}

  	$this->select($str);

		return $this->query = $str;
	}

	// function getDashboardBar2($unitkerja,$tahun=null)
	// {
	// 	if ($tahun == '' || $tahun == 'all') {
	// 		if ($unitkerja == 'all') {
	// 			$str = "SELECT b.direktorat_id, c.nama,
	// 					count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
	// 					from
	// 					(
	// 						SELECT z.user_login_id, z.permohonan_paket_id, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
	// 						(SELECT '1' total from paket a 
	// 						left join paket x on a.permohonan_paket_id=x.permohonan_paket_id
	// 						left join view_dash_efesiensi n on x.paket_id = n.paket_id 
	// 						 where a.permohonan_paket_id=z.permohonan_paket_id 
	// 						 and n.harga_negosiasi is not null 
	// 						) total_realisasi
	// 						from permohonan_paket z
	// 					) a
	// 					inner join user_login b on a.user_login_id=b.user_login_id
	// 					inner join direktorat c on b.direktorat_id=c.direktorat_id
	// 					group by b.direktorat_id, c.nama 
	// 					";
	// 		} else {
	// 			$str = "SELECT b.direktorat_id, c.nama,
	// 					count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
	// 					from
	// 					(
	// 						SELECT z.user_login_id, z.permohonan_paket_id, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
	// 						(SELECT '1' total from paket a 
	// 						left join paket x on a.permohonan_paket_id=x.permohonan_paket_id
	// 						left join view_dash_efesiensi n on x.paket_id = n.paket_id 
	// 						 where a.permohonan_paket_id=z.permohonan_paket_id 
	// 						 and n.harga_negosiasi is not null 
	// 						) total_realisasi
	// 						from permohonan_paket z
	// 					) a
	// 					inner join user_login b on a.user_login_id=b.user_login_id
	// 					inner join direktorat c on b.direktorat_id=c.direktorat_id
	// 					where b.unit_kerja_id = '".$unitkerja."'
	// 					group by b.direktorat_id, c.nama  
	// 					";
	// 		}
	// 	} else {
	// 		if ($unitkerja == 'all') {
	// 			$str = "SELECT a.* from (
	// 						SELECT a.tanggal_permohoanan, b.direktorat_id, c.nama,
	// 						count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
	// 						from
	// 						(
	// 							SELECT z.user_login_id, z.permohonan_paket_id, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
	// 							(SELECT '1' total from paket a 
	// 							left join paket x on a.permohonan_paket_id=x.permohonan_paket_id
	// 							left join view_dash_efesiensi n on x.paket_id = n.paket_id 
	// 							 where a.permohonan_paket_id=z.permohonan_paket_id 
	// 							 and n.harga_negosiasi is not null 
	// 							) total_realisasi
	// 							from permohonan_paket z
	// 						) a
	// 						inner join user_login b on a.user_login_id=b.user_login_id
	// 						inner join direktorat c on b.direktorat_id=c.direktorat_id
	// 						group by b.direktorat_id, c.nama, a.tanggal_permohoanan
	// 					) a WHERE a.tanggal_permohoanan = '".$tahun."'
	// 					";
	// 		} else {
	// 			$str = "SELECT a.* from (
	// 						SELECT a.tanggal_permohoanan, b.direktorat_id, c.nama,
	// 						count(a.permohonan_paket_id) total_rencana, count(a.total_realisasi) total_realisasi
	// 						from
	// 						(
	// 							SELECT z.user_login_id, z.permohonan_paket_id, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
	// 							(SELECT '1' total from paket a 
	// 							left join paket x on a.permohonan_paket_id=x.permohonan_paket_id
	// 							left join view_dash_efesiensi n on x.paket_id = n.paket_id 
	// 							 where a.permohonan_paket_id=z.permohonan_paket_id 
	// 							 and n.harga_negosiasi is not null 
	// 							) total_realisasi
	// 							from permohonan_paket z
	// 						) a
	// 						inner join user_login b on a.user_login_id=b.user_login_id
	// 						inner join direktorat c on b.direktorat_id=c.direktorat_id
	// 						where b.unit_kerja_id = '".$unitkerja."'
	// 						group by b.direktorat_id, c.nama, a.tanggal_permohoanan
	// 					) a WHERE a.tanggal_permohoanan = '".$tahun."'
	// 					";
	// 		}
	// 	}
	// 	// echo $str;
	// 	$this->select($str);
	// 	return $this->query = $str;
	// }

	function getDashboardBar2($unitkerja,$vppengadaan,$tahun=null)
	{
		if ($tahun == '' || $tahun == 'all') {
			if ($unitkerja == 'all') {
				$str = "SELECT b.direktorat_id, c.nama,
						count(a.permohonan_paket_id) total_rencana, SUM ( A.total_realisasi::numeric ) total_realisasi 
						from
						(
							SELECT z.user_login_id, z.permohonan_paket_id, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
							(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
							left join view_dash_efesiensi n on a.paket_id = n.paket_id 
							 where a.permohonan_paket_id=z.permohonan_paket_id  
							) total_realisasi
							from permohonan_paket z
						) a
						inner join user_login b on a.user_login_id=b.user_login_id
						inner join direktorat c on b.direktorat_id=c.direktorat_id
						WHERE b.vp_pengadaan = ".$vppengadaan."
						group by b.direktorat_id, c.nama 
						";
			} else {
				$str = "SELECT b.direktorat_id, c.nama,
						count(a.permohonan_paket_id) total_rencana, SUM ( A.total_realisasi::numeric ) total_realisasi 
						from
						(
							SELECT z.user_login_id, z.permohonan_paket_id, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
							(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
							left join view_dash_efesiensi n on a.paket_id = n.paket_id 
							 where a.permohonan_paket_id=z.permohonan_paket_id  
							) total_realisasi
							from permohonan_paket z
						) a
						inner join user_login b on a.user_login_id=b.user_login_id
						inner join direktorat c on b.direktorat_id=c.direktorat_id
						where b.vp_pengadaan = ".$vppengadaan." AND b.unit_kerja_id = '".$unitkerja."'
						group by b.direktorat_id, c.nama  
						";
			}
		} else {
			if ($unitkerja == 'all') {
				$str = "SELECT a.* from (
							SELECT a.tahun_anggaran, b.direktorat_id, c.nama,
							count(a.permohonan_paket_id) total_rencana, SUM ( A.total_realisasi::numeric ) total_realisasi 
							from
							(
								SELECT z.user_login_id, z.permohonan_paket_id, z.tahun_anggaran,
								(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
								left join view_dash_efesiensi n on a.paket_id = n.paket_id 
								 where a.permohonan_paket_id=z.permohonan_paket_id  
								) total_realisasi
								from permohonan_paket z
							) a
							inner join user_login b on a.user_login_id=b.user_login_id
							inner join direktorat c on b.direktorat_id=c.direktorat_id
							WHERE b.vp_pengadaan = ".$vppengadaan."
							group by b.direktorat_id, c.nama, a.tahun_anggaran
						) a WHERE a.tahun_anggaran = '".$tahun."'
						";
			} else {
				$str = "SELECT a.* from (
							SELECT a.tahun_anggaran, b.direktorat_id, c.nama,
							count(a.permohonan_paket_id) total_rencana, SUM ( A.total_realisasi::numeric ) total_realisasi 
							from
							(
								SELECT z.user_login_id, z.permohonan_paket_id, z.tahun_anggaran,
								(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
								left join view_dash_efesiensi n on a.paket_id = n.paket_id 
								 where a.permohonan_paket_id=z.permohonan_paket_id  
								) total_realisasi
								from permohonan_paket z
							) a
							inner join user_login b on a.user_login_id=b.user_login_id
							inner join direktorat c on b.direktorat_id=c.direktorat_id
							where b.vp_pengadaan = ".$vppengadaan." AND b.unit_kerja_id = '".$unitkerja."'
							group by b.direktorat_id, c.nama, a.tahun_anggaran
						) a WHERE a.tahun_anggaran = '".$tahun."'
						";
			}
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardBar2Detail($tahun=null,$nama_direktorat,$vppengadaan)
	{
		if ($tahun == '' || $tahun == 'all') {
			$str = "	SELECT A.* FROM (
						SELECT x.paket_id, z.user_login_id, h.direktorat_id, g.nama nama_direktorat,h.department,h.vp_pengadaan,z.permohonan_paket_id, z.nama, z.nilai, z.perkiraan_biaya_harga, n.harga_negosiasi, 
							( 
								SELECT A.nilai_kontrak FROM (
									SELECT A.PAKET_ID, A.NAMA, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, SUM(C.CR_NILAI_KONTRAK) NILAI_KONTRAK, SUM(C.CR_SPPBJ_NILAI) NILAI_SPPBJ
									FROM PAKET A
									JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
									LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON B.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
									WHERE A.NAMA IS NOT NULL 
									GROUP BY A.PAKET_ID, A.NAMA, B.CONTRACTINGREKANANID
								) A where A.paket_id=x.paket_id 
							),extract(year from z.rencana_pengadaan) tanggal_permohoanan,
							(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
								LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
								where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi, x.paket_uuid
							from permohonan_paket z
							left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
							left join view_dash_efesiensi n on x.paket_id = n.paket_id
							JOIN user_login h ON z.user_login_id = h.user_login_id
							JOIN direktorat g ON h.direktorat_id = g.direktorat_id
						) A
						WHERE A.nama_direktorat = '".$nama_direktorat."' AND A.vp_pengadaan = ".$vppengadaan."
					";
		} else {
			$str = "SELECT A.* FROM (
					SELECT x.paket_id, z.user_login_id, h.direktorat_id, g.nama nama_direktorat, h.department, h.vp_pengadaan,z.permohonan_paket_id, z.nama, z.nilai, z.perkiraan_biaya_harga, n.harga_negosiasi, 
						( 
							SELECT A.nilai_kontrak FROM (
								SELECT A.PAKET_ID, A.NAMA, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, SUM(C.CR_NILAI_KONTRAK) NILAI_KONTRAK, SUM(C.CR_SPPBJ_NILAI) NILAI_SPPBJ
								FROM PAKET A
								JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
								LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON B.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
								WHERE A.NAMA IS NOT NULL 
								GROUP BY A.PAKET_ID, A.NAMA, B.CONTRACTINGREKANANID
							) A where A.paket_id=x.paket_id 
						),extract(year from z.rencana_pengadaan) tanggal_permohoanan, z.tahun_anggaran,
						(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
							LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
							where a.permohonan_paket_id=z.permohonan_paket_id
						) total_realisasi, x.paket_uuid
						from permohonan_paket z
						left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
						left join view_dash_efesiensi n on x.paket_id = n.paket_id
						JOIN user_login h ON z.user_login_id = h.user_login_id
						JOIN direktorat g ON h.direktorat_id = g.direktorat_id
					) A
						WHERE A.nama_direktorat = '".$nama_direktorat."' AND A.tahun_anggaran = '".$tahun."' AND A.vp_pengadaan = ".$vppengadaan."
					";
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardBar2DetailAll($tahun=null,$nama_direktorat)
	{
		if ($tahun == '' || $tahun == 'all') {
			$str = "	SELECT A.* FROM (
						SELECT x.paket_id, z.user_login_id, h.direktorat_id, g.nama nama_direktorat,h.department,h.vp_pengadaan,z.permohonan_paket_id, z.nama, z.nilai, z.perkiraan_biaya_harga, n.harga_negosiasi, 
							( 
								SELECT A.nilai_kontrak FROM (
									SELECT A.PAKET_ID, A.NAMA, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, SUM(C.CR_NILAI_KONTRAK) NILAI_KONTRAK, SUM(C.CR_SPPBJ_NILAI) NILAI_SPPBJ
									FROM PAKET A
									JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
									LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON B.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
									WHERE A.NAMA IS NOT NULL 
									GROUP BY A.PAKET_ID, A.NAMA, B.CONTRACTINGREKANANID
								) A where A.paket_id=x.paket_id 
							),extract(year from z.rencana_pengadaan) tanggal_permohoanan,
							(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
								LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
								where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi, x.paket_uuid
							from permohonan_paket z
							left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
							left join view_dash_efesiensi n on x.paket_id = n.paket_id
							JOIN user_login h ON z.user_login_id = h.user_login_id
							JOIN direktorat g ON h.direktorat_id = g.direktorat_id
						) A
						WHERE A.nama_direktorat = '".$nama_direktorat."'
					";
		} else {
			$str = "SELECT A.* FROM (
					SELECT x.paket_id, z.user_login_id, h.direktorat_id, g.nama nama_direktorat, h.department, h.vp_pengadaan,z.permohonan_paket_id, z.nama, z.nilai, z.perkiraan_biaya_harga, n.harga_negosiasi, 
						( 
							SELECT A.nilai_kontrak FROM (
								SELECT A.PAKET_ID, A.NAMA, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, SUM(C.CR_NILAI_KONTRAK) NILAI_KONTRAK, SUM(C.CR_SPPBJ_NILAI) NILAI_SPPBJ
								FROM PAKET A
								JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
								LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON B.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
								WHERE A.NAMA IS NOT NULL 
								GROUP BY A.PAKET_ID, A.NAMA, B.CONTRACTINGREKANANID
							) A where A.paket_id=x.paket_id 
						),extract(year from z.rencana_pengadaan) tanggal_permohoanan, z.tahun_anggaran,
						(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
							LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
							where a.permohonan_paket_id=z.permohonan_paket_id
						) total_realisasi, x.paket_uuid
						from permohonan_paket z
						left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
						left join view_dash_efesiensi n on x.paket_id = n.paket_id
						JOIN user_login h ON z.user_login_id = h.user_login_id
						JOIN direktorat g ON h.direktorat_id = g.direktorat_id
					) A
						WHERE A.nama_direktorat = '".$nama_direktorat."' AND A.tahun_anggaran = '".$tahun."'
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
								(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total from paket a 
									LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
									where a.permohonan_paket_id=z.permohonan_paket_id
								) total_realisasi
								from permohonan_paket z
								left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
							) A
							WHERE A.user_login_id = '".$user_login_id."'
						";
			} else {
				$str = "	SELECT A.* FROM (
							SELECT x.paket_id, z.user_login_id, z.unit_kerja_id, z.permohonan_paket_id, z.nama, z.nilai, extract(year from z.tanggal) tanggal_permohoanan,
								(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total 
								from paket a 
								LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
								where a.permohonan_paket_id=z.permohonan_paket_id
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
							(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total 
							from paket a 
							LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
							where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi
							from permohonan_paket z
							left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
						) A
							WHERE A.user_login_id = '".$user_login_id."' AND A.tanggal_permohoanan = '".$tahun."'
						";
			} else {
				$str = "SELECT A.* FROM (
						SELECT x.paket_id, z.user_login_id, z.unit_kerja_id, z.permohonan_paket_id, z.nama, z.nilai, extract(year from z.tanggal) tanggal_permohoanan,
							(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total 
							from paket a 
							LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
							where a.permohonan_paket_id=z.permohonan_paket_id
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

	function getDashboardBar2Detail3($tahun=null,$user_login_id)
	{
		if ($tahun == '' || $tahun == 'all') {
			$str = "	SELECT A.* FROM (
						SELECT x.paket_id, z.user_login_id, h.direktorat_id, g.nama nama_direktorat,z.permohonan_paket_id, z.nama, z.nilai, z.perkiraan_biaya_harga, n.harga_negosiasi, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
							(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total 
							from paket a 
							LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
							where a.permohonan_paket_id=z.permohonan_paket_id
							) total_realisasi
							from permohonan_paket z
							left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
							left join view_dash_efesiensi n on x.paket_id = n.paket_id
							JOIN user_login h ON z.user_login_id = h.user_login_id
							JOIN direktorat g ON h.direktorat_id = g.direktorat_id
						) A
						WHERE A.user_login_id = '".$user_login_id."'
					";
		} else {
			$str = "SELECT A.* FROM (
					SELECT x.paket_id, z.user_login_id, h.direktorat_id, g.nama nama_direktorat, z.permohonan_paket_id, z.nama, z.nilai, z.perkiraan_biaya_harga, n.harga_negosiasi, extract(year from z.rencana_pengadaan) tanggal_permohoanan,
						(SELECT case when a.alasan_ulang != '' OR a.alasan != '' then '0' when n.harga_negosiasi::text is null OR n.harga_negosiasi <= 0 then '0' else '1' end total 
						from paket a 
						LEFT JOIN view_dash_efesiensi n ON a.paket_id = n.paket_id 
						where a.permohonan_paket_id=z.permohonan_paket_id
						) total_realisasi
						from permohonan_paket z
						left join paket x on z.permohonan_paket_id=x.permohonan_paket_id
						left join view_dash_efesiensi n on x.paket_id = n.paket_id
						JOIN user_login h ON z.user_login_id = h.user_login_id
						JOIN direktorat g ON h.direktorat_id = g.direktorat_id
					) A
						WHERE A.user_login_id = '".$user_login_id."' AND A.tanggal_permohoanan = '".$tahun."'
					";
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardGauge($unitkerja,$vppengadaan,$tahun=null)
	{
		if ($tahun == '' || $tahun == 'all') {
			if ($unitkerja == 'all') {
				$str = "SELECT
						(select count(paket_id) total_paket from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."'),
						(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."' and proses = '1')
						";
			} else {
				$str = "SELECT
					(select count(paket_id) total_paket from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."' and unit_kerja_id = '".$unitkerja."'),
					(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."' and proses = '1' and unit_kerja_id = '".$unitkerja."')
					";
			}
		} else {
			if ($unitkerja == 'all') {
				$str = "SELECT
						(select count(paket_id) total_paket from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."' and tahun_anggaran='".$tahun."'),
						(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."' and proses = '1' and tahun_anggaran='".$tahun."')
						";
			} else {
				$str = "SELECT
						(select count(paket_id) total_paket from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."' and tahun_anggaran='".$tahun."' and unit_kerja_id = '".$unitkerja."'),
						(select count(paket_id) total_paket_proses from view_dashboard_paket_proses where vp_pengadaan = '".$vppengadaan."' and proses = '1' and tahun_anggaran='".$tahun."' and unit_kerja_id = '".$unitkerja."')
						";
			}
		}
		// echo $str;
		$this->select($str);
		return $this->query = $str;
	}

	function getDashboardGaugeDetail($unitkerja,$vppengadaan,$tahun=null)
	{
	    $str = "SELECT * from view_dashboard_paket_proses WHERE 1=1 ";

	    if ($unitkerja != 'all') {
	      $str .= " and a.unit_kerja_id = '".$unitkerja."' ";
			}

			if ($tahun == '' || $tahun == 'all') {
			} else {
				$str .= " and tahun_permohonan='".$tahun."'";
			}

	    $str .= " order by proses desc";
			// echo $str;
			$this->select($str);
			return $this->query = $str;
	}

	function getDashboardGaugeNewDetail($unitkerja,$vppengadaan,$tahun=null)
	{
	    $str = "SELECT * from view_dashboard_paket_proses WHERE vp_pengadaan = '".$vppengadaan."' ";

	    if ($unitkerja != 'all') {
	      $str .= " and unit_kerja_id = '".$unitkerja."' ";
			}

			if ($tahun == '' || $tahun == 'all') {
			} else {
				$str .= " and tahun_permohonan='".$tahun."'";
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
		$str = "select COUNT(*) AS ROWCOUNT
                        from (
                  SELECT A.* FROM (
                    SELECT
                    a.PAKET_ID,A.PAKET_METODE_LELANG_ID,B.NAMA METODE_LELANG,A.NAMA,A.PUBLISH_PAKET,H.UNIT_KERJA_ID, A.USER_LOGIN_ID, I.USER_LOGIN_ID AS USER_PERENCANA,
                                        COALESCE((SELECT tanggal_awal FROM PAKET_TAHAP X WHERE X.PAKET_ID = A.PAKET_ID and X.NAMA = 'Pembuatan Paket Lelang' LIMIT 1 ),A.TANGGAL) tanggal_tahap,
										A.JENIS_PENGADAAN, PR_GROUP_NUMBER, A.LOKASI, A.TANGGAL, date_part('month'::text, A.tanggal) AS bulan, date_part('month'::text, I.rencana_pengadaan) AS bulan_permohonan, date_part('year'::text, A.tanggal) AS TAHUN, date_part('year'::text, I.rencana_pengadaan) AS TAHUN_PERMOHONAN, I.TAHUN_ANGGARAN,
			A.PERMOHONAN_PAKET_ID, I.KODE_RUP, I.KODE_PR, A.PAKET_JENIS_ID, K.VP_PENGADAAN
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
                            LEFT JOIN VIEW_ANALISA_DAN_PERMOHONAN_PAKET K ON A.PAKET_ID = K.PAKET_ID
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
		$str = "SELECT SUM(NILAI) AS ROWCOUNT
                        from ( SELECT
                      A.NILAI, A.PAKET_METODE_LELANG_ID, A.TANGGAL, A.UNIT_KERJA_ID, A.USER_LOGIN_ID, date_part('month'::text, A.tanggal) AS bulan, date_part('month'::text, I.rencana_pengadaan) AS bulan_permohonan, date_part('year'::text, A.tanggal) AS TAHUN, date_part('year'::text, I.rencana_pengadaan) AS TAHUN_PERMOHONAN, I.TAHUN_ANGGARAN, K.VP_PENGADAAN
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
                            LEFT JOIN VIEW_ANALISA_DAN_PERMOHONAN_PAKET K ON A.PAKET_ID = K.PAKET_ID
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

    function getSumFinalByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(CR_NILAI_KONTRAK) AS ROWCOUNT
                        from ( SELECT
                      	X.CR_NILAI_KONTRAK, A.NILAI, A.PAKET_METODE_LELANG_ID, A.TANGGAL, A.UNIT_KERJA_ID, A.USER_LOGIN_ID, date_part('month'::text, A.tanggal) AS bulan, date_part('month'::text, I.rencana_pengadaan) AS bulan_permohonan, date_part('year'::text, A.tanggal) AS TAHUN, date_part('year'::text, I.rencana_pengadaan) AS TAHUN_PERMOHONAN, I.TAHUN_ANGGARAN, K.VP_PENGADAAN
                    FROM    PAKET A
                    		JOIN CONTRACTING_REKANAN Z ON A.PAKET_ID=Z.PAKET_ID 
							JOIN CONTRACTING_REKANAN_PROSES1 X ON Z.CONTRACTINGREKANANID=X.CONTRACTINGREKANANID
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                            LEFT JOIN VIEW_ANALISA_DAN_PERMOHONAN_PAKET K ON A.PAKET_ID = K.PAKET_ID
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

    function getSumFinalKatalogByParams($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(HARGA_NEGO) AS ROWCOUNT
                        from ( SELECT
                      	((Z.HARGA_NEGO * Z.QTY) + X.ONGKOS_KIRIM) HARGA_NEGO,A.NILAI, A.PAKET_METODE_LELANG_ID, A.TANGGAL, A.UNIT_KERJA_ID, A.USER_LOGIN_ID, date_part('month'::text, A.tanggal) AS bulan, date_part('month'::text, I.rencana_pengadaan) AS bulan_permohonan, date_part('year'::text, A.tanggal) AS TAHUN, date_part('year'::text, I.rencana_pengadaan) AS TAHUN_PERMOHONAN, I.TAHUN_ANGGARAN
                    FROM    PAKET A
                    		JOIN KATALOG_REKANAN Z ON A.PAKET_ID=Z.PAKET_ID  
							JOIN KATALOG_LOGISTIK X ON A.PAKET_ID=X.PAKET_ID
                            LEFT JOIN PAKET_METODE_LELANG B ON A.PAKET_METODE_LELANG_ID = B.PAKET_METODE_LELANG_ID
                            LEFT JOIN PAKET_METODE_KUALIFIKASI C ON A.PAKET_METODE_KUALIFIKASI_ID = C.PAKET_METODE_KUALIFIKASI_ID
                            LEFT JOIN PAKET_METODE_EVALUASI D ON A.PAKET_METODE_EVALUASI_ID = D.PAKET_METODE_EVALUASI_ID
                            LEFT JOIN PAKET_JENIS E ON A.PAKET_JENIS_ID = E.PAKET_JENIS_ID
                            LEFT JOIN V_PEGAWAI_REVISI F ON TO_CHAR(A.USER_LOGIN_ID, '999999999999999') = F.NIPP
                            LEFT JOIN REKANAN_KUALIFIKASI G ON A.REKANAN_KUALIFIKASI_ID = G.REKANAN_KUALIFIKASI_ID
                            LEFT JOIN UNIT_KERJA H ON A.UNIT_KERJA_ID = H.UNIT_KERJA_ID
                            LEFT JOIN PERMOHONAN_PAKET I ON A.PERMOHONAN_PAKET_ID = I.PERMOHONAN_PAKET_ID
                            LEFT JOIN SAP_PR J ON A.PAKET_ID = J.PAKET_ID
                    WHERE 1 = 1  ) A where 1 = 1";
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

    function getUnitKerja($id)
	{
		$str = "SELECT NAMA, LOGO FROM UNIT_KERJA A WHERE UNIT_KERJA_ID = ".$id;
		$this->select($str);
		return $this->execQuery($str);
    }

    function selectPermohonan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.*, B.NAMA, B.PERKIRAAN_BIAYA_HARGA, B.KODE_RUP, B.KODE_PR, C.DEPARTMENT, C.VP_PENGADAAN
				FROM PERMOHONAN_PAKET_ANALISA A 
				JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ANALISA_ID=B.PERMOHONAN_PAKET_ANALISA_ID
				LEFT JOIN USER_LOGIN C ON A.CREATED_BY = C.USER_LOGIN_ID
				WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  	}

  	function selectPermohonanSum($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(A.PERKIRAAN_BIAYA_HARGA) AS ROWCOUNT
                FROM ( SELECT B.PERKIRAAN_BIAYA_HARGA, C.VP_PENGADAAN FROM PERMOHONAN_PAKET_ANALISA A 
				LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ANALISA_ID=B.PERMOHONAN_PAKET_ANALISA_ID 
				LEFT JOIN USER_LOGIN C ON A.CREATED_BY = C.USER_LOGIN_ID
				WHERE 1=1 ";
		while(list($key,$val)=each($paramsArray))
		{
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .=  $statement."  ) A WHERE 1=1";

		$this->select($str);
		$this->query = $str;
		// echo $str; exit();
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function selectPersiapanSum($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(A.PERKIRAAN_BIAYA_HARGA) AS ROWCOUNT
                FROM ( SELECT B.PERKIRAAN_BIAYA_HARGA, C.VP_PENGADAAN, date_part( 'year' :: TEXT, B.rencana_pengadaan ) AS TAHUN_PERMOHONAN FROM PERMOHONAN_PAKET_ANALISA A 
				LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ANALISA_ID=B.PERMOHONAN_PAKET_ANALISA_ID 
				LEFT JOIN USER_LOGIN C ON A.CREATED_BY = C.USER_LOGIN_ID
				WHERE 1=1 AND B.KODE_PR IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .=  $statement."  ) A WHERE 1=1";

		$this->select($str);
		$this->query = $str;
		// echo $str; exit();
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function selectPemilihan($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.* FROM (
					SELECT A.*, D.VP_PENGADAAN, date_part('year'::text, A.tanggal) AS TAHUN, date_part( 'year' :: TEXT, B.rencana_pengadaan ) AS TAHUN_PERMOHONAN, B.TAHUN_ANGGARAN
					FROM PAKET A
					LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
					LEFT JOIN PERMOHONAN_PAKET_ANALISA C ON B.PERMOHONAN_PAKET_ANALISA_ID=C.PERMOHONAN_PAKET_ANALISA_ID
					LEFT JOIN USER_LOGIN D ON C.CREATED_BY = D.USER_LOGIN_ID 
					WHERE A.NAMA IS NOT NULL 
				) A WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  	}

  	function selectPemilihanSum($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(A.NILAI) AS ROWCOUNT
                FROM ( 
                SELECT A.* FROM (
                	SELECT A.*, D.VP_PENGADAAN, date_part('year'::text, A.tanggal) AS TAHUN, date_part( 'year' :: TEXT, B.rencana_pengadaan ) AS TAHUN_PERMOHONAN, B.TAHUN_ANGGARAN
                	FROM PAKET A
                	LEFT JOIN PERMOHONAN_PAKET B ON A.PERMOHONAN_PAKET_ID = B.PERMOHONAN_PAKET_ID
					LEFT JOIN PERMOHONAN_PAKET_ANALISA C ON B.PERMOHONAN_PAKET_ANALISA_ID=C.PERMOHONAN_PAKET_ANALISA_ID
					LEFT JOIN USER_LOGIN D ON C.CREATED_BY = D.USER_LOGIN_ID 
					WHERE A.NAMA IS NOT NULL 
				) A WHERE 1=1";
		while(list($key,$val)=each($paramsArray))
		{
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .=  $statement."  ) A WHERE 1=1";

		$this->select($str);
		$this->query = $str;
		// echo $str; exit();
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function selectPemilihanDivisi($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.* FROM (
					SELECT A.*, date_part('year'::text, A.tanggal) AS TAHUN, B.user_login_id pembuat
					FROM PAKET A
					LEFT JOIN permohonan_paket B ON A.permohonan_paket_id = B.permohonan_paket_id
					WHERE A.NAMA IS NOT NULL 
				) A WHERE 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement;
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  	}

  	function selectPemilihanDivisiSum($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(A.NILAI) AS ROWCOUNT
                FROM ( 
                SELECT A.* FROM (
                	SELECT A.*, date_part('year'::text, A.tanggal) AS TAHUN, B.user_login_id pembuat
                	FROM PAKET A
                	LEFT JOIN permohonan_paket B ON A.permohonan_paket_id = B.permohonan_paket_id
					WHERE A.NAMA IS NOT NULL 
				) A WHERE 1=1";
		while(list($key,$val)=each($paramsArray))
		{
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .=  $statement."  ) A WHERE 1=1";

		$this->select($str);
		$this->query = $str;
		// echo $str; exit();
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function selectKontrakProses($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = " SELECT A.*, 
				(SELECT SUM(Z.CR_NILAI_KONTRAK) FROM CONTRACTING_REKANAN_PROSES1 Z WHERE A.CONTRACTINGREKANANID=Z.CONTRACTINGREKANANID ) AS CR_NILAI_KONTRAK FROM (
						SELECT A.NAMA, D.VP_PENGADAAN, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, date_part( 'year' :: TEXT, BB.rencana_pengadaan ) AS TAHUN_PERMOHONAN, BB.TAHUN_ANGGARAN
						FROM PAKET A
						JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
						LEFT JOIN PERMOHONAN_PAKET BB ON A.PERMOHONAN_PAKET_ID = BB.PERMOHONAN_PAKET_ID
						LEFT JOIN PERMOHONAN_PAKET_ANALISA C ON BB.PERMOHONAN_PAKET_ANALISA_ID=C.PERMOHONAN_PAKET_ANALISA_ID
						LEFT JOIN USER_LOGIN D ON C.CREATED_BY = D.USER_LOGIN_ID 
						WHERE A.NAMA IS NOT NULL 
					) A WHERE 1=1
				 ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement.' GROUP BY A.NAMA, A.TAHUN, A.CONTRACTINGREKANANID, A.VP_PENGADAAN, A.TAHUN_ANGGARAN, A.TAHUN_PERMOHONAN ';
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  	}

  	function selectKontrakProsesSum($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(A.NILAI_KONTRAK) AS ROWCOUNT
                FROM ( 
	                SELECT A.* FROM (
						SELECT A.PAKET_ID, A.NAMA, D.VP_PENGADAAN, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, SUM(C.CR_NILAI_KONTRAK) NILAI_KONTRAK, SUM(C.CR_SPPBJ_NILAI) NILAI_SPPBJ, date_part( 'year' :: TEXT, BB.rencana_pengadaan ) AS TAHUN_PERMOHONAN, BB.TAHUN_ANGGARAN
						FROM PAKET A
						JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
						LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON B.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
						LEFT JOIN PERMOHONAN_PAKET BB ON A.PERMOHONAN_PAKET_ID = BB.PERMOHONAN_PAKET_ID
						LEFT JOIN PERMOHONAN_PAKET_ANALISA CC ON BB.PERMOHONAN_PAKET_ANALISA_ID=CC.PERMOHONAN_PAKET_ANALISA_ID
						LEFT JOIN USER_LOGIN D ON CC.CREATED_BY = D.USER_LOGIN_ID 
						WHERE A.NAMA IS NOT NULL 
						GROUP BY A.PAKET_ID, A.NAMA, B.CONTRACTINGREKANANID, D.VP_PENGADAAN, BB.RENCANA_PENGADAAN, BB.TAHUN_ANGGARAN
					) A 
					WHERE 1=1
					";
		while(list($key,$val)=each($paramsArray))
		{
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .=  $statement."  ) A WHERE 1=1";

		$this->select($str);
		$this->query = $str;
		// echo $str; 
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function selectKontrakSelesai($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.*, 
				(SELECT SUM(Z.CR_NILAI_KONTRAK) FROM CONTRACTING_REKANAN_PROSES1 Z WHERE A.CONTRACTINGREKANANID=Z.CONTRACTINGREKANANID ) AS CR_NILAI_KONTRAK FROM (
						SELECT A.NAMA, D.VP_PENGADAAN, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, date_part( 'year' :: TEXT, BB.rencana_pengadaan ) AS TAHUN_PERMOHONAN, BB.TAHUN_ANGGARAN
						FROM PAKET A
						JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
						LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON B.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
						LEFT JOIN PERMOHONAN_PAKET BB ON A.PERMOHONAN_PAKET_ID = BB.PERMOHONAN_PAKET_ID
						LEFT JOIN PERMOHONAN_PAKET_ANALISA CC ON BB.PERMOHONAN_PAKET_ANALISA_ID=CC.PERMOHONAN_PAKET_ANALISA_ID
						LEFT JOIN USER_LOGIN D ON CC.CREATED_BY = D.USER_LOGIN_ID 
						WHERE A.NAMA IS NOT NULL AND B.CONTRACTINGPROSESID >= '5'
					) A WHERE 1=1
					  ";

		while(list($key,$val) = each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		$str .= $statement.' GROUP BY A.NAMA, A.TAHUN, A.CONTRACTINGREKANANID, A.VP_PENGADAAN, A.TAHUN_PERMOHONAN, A.TAHUN_ANGGARAN';
		// echo $str; die();
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
  	} 

  	function selectKontrakSelesaiSum($paramsArray=array(), $statement='')
	{
		$str = "SELECT SUM(A.NILAI_KONTRAK) AS ROWCOUNT
                FROM ( 
	                SELECT A.* FROM (
						SELECT A.PAKET_ID, A.NAMA, D.VP_PENGADAAN, date_part('year'::text, A.tanggal) AS TAHUN, B.CONTRACTINGREKANANID, SUM(C.CR_NILAI_KONTRAK) NILAI_KONTRAK, SUM(C.CR_SPPBJ_NILAI) NILAI_SPPBJ, date_part( 'year' :: TEXT, BB.rencana_pengadaan ) AS TAHUN_PERMOHONAN, BB.TAHUN_ANGGARAN
						FROM PAKET A
						JOIN CONTRACTING_REKANAN B ON A.PAKET_ID=B.PAKET_ID
						LEFT JOIN CONTRACTING_REKANAN_PROSES1 C ON B.CONTRACTINGREKANANID=C.CONTRACTINGREKANANID
						LEFT JOIN PERMOHONAN_PAKET BB ON A.PERMOHONAN_PAKET_ID = BB.PERMOHONAN_PAKET_ID
						LEFT JOIN PERMOHONAN_PAKET_ANALISA CC ON BB.PERMOHONAN_PAKET_ANALISA_ID=CC.PERMOHONAN_PAKET_ANALISA_ID
						LEFT JOIN USER_LOGIN D ON CC.CREATED_BY = D.USER_LOGIN_ID 
						WHERE A.NAMA IS NOT NULL AND B.CONTRACTINGPROSESID >= '5'
						GROUP BY A.PAKET_ID, A.NAMA, B.CONTRACTINGREKANANID, D.VP_PENGADAAN, BB.RENCANA_PENGADAAN, BB.TAHUN_ANGGARAN
					) A 
					WHERE 1=1
					";
		while(list($key,$val)=each($paramsArray))
		{
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .=  $statement."  ) A WHERE 1=1";

		$this->select($str);
		$this->query = $str;
		// echo $str; 
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }

    function selectTerKontrak($paramsArray=array(), $limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.* FROM (
				SELECT a.*, c.nama || '. ' || b.nama penyedia 
				FROM (
					select a.cr_nilai_kontrak,a.rekanan_id,b.nama,date_part('year'::text, a.cr_sppbj_tanggal) AS TAHUN
					from contracting_rekanan_proses1 a 
					JOIN paket b on a.paket_id=b.paket_id
				) a
				JOIN rekanan b ON a.rekanan_id=b.rekanan_id
				JOIN rekanan_tipe c on b.rekanan_tipe_id=c.rekanan_tipe_id
					";
		while(list($key,$val)=each($paramsArray))
		{
			$pecah = explode("||", $key);
			if (count($pecah) > 1) {
				$str .= "AND $pecah[0] $pecah[1] $val ";
			} else {
				$str .= " AND $key = '$val' ";
			}
		}
		$str .=  $statement."  ) A WHERE 1=1";

		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

  }
?>
