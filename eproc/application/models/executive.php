<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Executive extends Entity 
  { 

	var $query; 

  function __construct(){
  		parent::__construct();
	}
  function getReport()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$str = "select pp.tahun_anggaran as tahun,
	pp.kode_rup ,
	to_char(pp.created_date,'YYYY-mm-dd') as tanggal_usulan_unit_kerja , 
	pp.kode_pr as nomor_pr, 
	pp.nama as nama_paket, 
	pp.nilai as nilai_pagu_rup, 
	pp.nilai_rab_pr as nilai_rab ,
	pp.nilai_hps_pr  as nilai_hps,
	 (
        SELECT rj.created_date
        FROM rekam_jejak rj
        WHERE rj.paket_id = p.paket_id
          AND rj.posisi = 'Publish Penetapan Pemenang'
        ORDER BY rj.created_date DESC
        LIMIT 1
    ) AS tanggal_publish_penetapan_pemenang,
	(
        select crp.cr_nilai_kontrak 
        from contracting_rekanan_proses1 crp 
        where crp.paket_id =p.paket_id 
        ORDER BY crp.cr_created_date  DESC
        LIMIT 1
    ) AS nilai_kontrak,
    rjperencanaan_last.status_baru as perencanaan_new_status,
    rjpaket_last.status_baru as pengadaan_new_status,
    CASE 
        WHEN rjpaket_last.status_baru IS NOT NULL THEN rjpaket_last.status_baru
        ELSE rjperencanaan_last.status_baru
    END AS rekap_status,
    rjperencanaan_last.posisi  as status_perencanaan, rjperencanaan_last.user_nama as oleh1, rjperencanaan_last.user_nama || ' - ' || rjperencanaan_last.posisi as last_perencanaan,
    rjpaket_last.posisi as status_paket_pengadaan, rjpaket_last.user_nama as oleh2, rjpaket_last.user_nama || ' - ' || rjpaket_last.posisi as last_pengadaan
from permohonan_paket pp 
left join paket p on pp.permohonan_paket_id = p.permohonan_paket_id
LEFT JOIN LATERAL (
    SELECT
        rj.posisi,ul.user_nama ,aks.status_baru 
    FROM rekam_jejak rj
    left join user_login ul on rj.created_by =ul.user_login_id 
    left join akmal_konversi_status aks on aks.posisi =rj.posisi 
    WHERE rj.permohonan_paket_id  = pp.permohonan_paket_id
    ORDER BY rj.created_date DESC
    LIMIT 1
) rjperencanaan_last ON true
LEFT JOIN LATERAL (
    SELECT
        rj.posisi,ul.user_nama ,aks.status_baru 
    FROM rekam_jejak rj
    left join user_login ul on rj.created_by =ul.user_login_id
    left join akmal_konversi_status aks on aks.posisi =rj.posisi 
    WHERE rj.paket_id = p.paket_id
    ORDER BY rj.created_date DESC
    LIMIT 1
) rjpaket_last ON true"; 
				//echo $str; die;
				$query = $this->db->query($str);
				//var_dump($query->result_array());
				//die('stop');
		return $query->result_array();
  }
    
  } 
?>