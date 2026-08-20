<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

include_once('entity.php');

  class Pakettrans extends CI_Model{

	var $query;

	function __construct(){
	  parent::__construct();
	}

  function updateAlasanUlang()
	{
    // $this->db->trans_start(TRUE); // Query will be rolled back
    $this->db->trans_begin();

		// Tender Ulang
    // File tidak usah di copy karena jika update file tidak menghapus file yang lama
		// 1. Duplicate Permohonan Paket
		$this->setField("PERMOHONAN_PAKET_ID", $this->getNextId("PERMOHONAN_PAKET_ID","PERMOHONAN_PAKET"));
    $generatePermohonanID = $this->getField("PERMOHONAN_PAKET_ID");
    // echo $generatePermohonanID; die;
		$this->db->query("INSERT INTO permohonan_paket (permohonan_paket_id,user_login_id,unit_kerja_id,tanggal,nota_dinas,nama,keterangan,no_ppa,last_create_user,last_create_date,nilai,posting,posting_by,posting_date,pic,pic_by,pic_date,alasan_tolak,alasan_tolak_by,alasan_tolak_date,approval,pengadaanlangsung,permohonan_paket_analisa_id,tahun_anggaran,anggaran,jenis_barang_jasa,perkiraan_biaya_harga,waktu_pengguna_barangjasa,rencana_pengadaan,cara_pengadaan,budget_awal,budget_terpakai,budget_akhir,created_by,created_date,updated_by,updated_date,paket_id_ulang,permohonan_paket_id_parent
		)
		SELECT '".$generatePermohonanID."',A.user_login_id,
		A.unit_kerja_id,A.tanggal,A.nota_dinas,A.nama,A.keterangan,A.no_ppa,A.last_create_user,A.last_create_date,A.nilai,A.posting,A.posting_by,A.posting_date,A.pic,A.pic_by,A.pic_date,A.alasan_tolak,A.alasan_tolak_by,A.alasan_tolak_date,A.approval,A.pengadaanlangsung,A.permohonan_paket_analisa_id,A.tahun_anggaran,A.anggaran,A.jenis_barang_jasa,A.perkiraan_biaya_harga,A.waktu_pengguna_barangjasa,A.rencana_pengadaan,A.cara_pengadaan,A.budget_awal,A.budget_terpakai,A.budget_akhir,A.created_by,A.created_date,A.updated_by,A.updated_date,".$this->getField("PAKET_ID").",".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
		FROM permohonan_paket A
		WHERE A.permohonan_paket_id = ".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
		");
    $this->db->trans_complete();
    echo $this->db->trans_status().'---statusa';

    // echo "INSERT INTO permohonan_paket (permohonan_paket_id,user_login_id,unit_kerja_id,tanggal,nota_dinas,nama,keterangan,no_ppa,last_create_user,last_create_date,nilai,posting,posting_by,posting_date,pic,pic_by,pic_date,alasan_tolak,alasan_tolak_by,alasan_tolak_date,approval,pengadaanlangsung,permohonan_paket_analisa_id,tahun_anggaran,anggaran,jenis_barang_jasa,perkiraan_biaya_harga,waktu_pengguna_barangjasa,rencana_pengadaan,cara_pengadaan,budget_awal,budget_terpakai,budget_akhir,created_by,created_date,updated_by,updated_date,paket_id_ulang,permohonan_paket_id_parent
		// )
		// SELECT '".$generatePermohonanID."',A.user_login_id,
		// A.unit_kerja_id,A.tanggal,A.nota_dinas,A.nama,A.keterangan,A.no_ppa,A.last_create_user,A.last_create_date,A.nilai,A.posting,A.posting_by,A.posting_date,A.pic,A.pic_by,A.pic_date,A.alasan_tolak,A.alasan_tolak_by,A.alasan_tolak_date,A.approval,A.pengadaanlangsung,A.permohonan_paket_analisa_id,A.tahun_anggaran,A.anggaran,A.jenis_barang_jasa,A.perkiraan_biaya_harga,A.waktu_pengguna_barangjasa,A.rencana_pengadaan,A.cara_pengadaan,A.budget_awal,A.budget_terpakai,A.budget_akhir,A.created_by,A.created_date,A.updated_by,A.updated_date,".$this->getField("PAKET_ID").",".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
		// FROM permohonan_paket A
		// WHERE A.permohonan_paket_id = ".$this->getField("PERMOHONAN_PAKET_ID_OLD")."";

		// 2. Paket Penawaran
		// $this->setField("PAKET_PENAWARAN_ID", $this->getNextId("PAKET_PENAWARAN_ID","PAKET_PENAWARAN"));
		// $this->db->query("INSERT INTO PAKET_PENAWARAN (paket_penawaran_id,paket_id,item,total_cost,satuan,quantity,oe,delivery_date,last_create_user,last_create_date,lokasi,jumlah,boq,boq_kolom,pr_group_number,pr_number,service_line,item_group,item_number,item_parent,item_child,rekanan_id_pemenang,biaya_kirim,boq_file,permohonan_paket_id,updated_by,updated_date)
		// 		SELECT '".$this->getField("PAKET_PENAWARAN_ID")."', 0, A.item,A.total_cost,A.satuan,A.quantity,A.oe,A.delivery_date,A.last_create_user,A.last_create_date,A.lokasi,A.jumlah,A.boq,A.boq_kolom,A.pr_group_number,A.pr_number,A.service_line,A.item_group,A.item_number,A.item_parent,A.item_child,A.rekanan_id_pemenang,A.biaya_kirim,A.boq_file,'".$generatePermohonanID."',A.updated_by,A.updated_date
		// 		FROM paket_penawaran A
		// 		WHERE A.permohonan_paket_id = ".$this->getField("PERMOHONAN_PAKET_ID_OLD")."
    // ");
    //
		// // 3. COA
    // $this->load->model("PermohonanPaket");
    // $permohonan_paket_coa = new PermohonanPaket();
    // $permohonan_paket_coa->selectByParamsCoa(array("A.PERMOHONAN_PAKET_ID" => coalesce($this->getField("PERMOHONAN_PAKET_ID_OLD"), 0)));
    // while($permohonan_paket_coa->nextRow())
    // {
    //   $this->setField("COA_ID", $this->getNextId("COA_ID","PERMOHONAN_PAKET_COA"));
    //   $this->db->query("INSERT INTO PERMOHONAN_PAKET_COA (coa_id,permohonan_paket_id,nomor,keterangan,budget_awal,budget_terpakai,budget_akhir,created_by,created_date)
    //   SELECT '".$this->getField("COA_ID")."', ".$generatePermohonanID.", A.nomor, A.keterangan, A.budget_awal, A.budget_terpakai, A.budget_akhir, A.created_by, A.created_date
    //   FROM permohonan_paket_coa A
    //   WHERE A.coa_id = ".$permohonan_paket_coa->getField("COA_ID")."
    //   ");
    // }
    //
    //
		// // 4. Permohonan Paket File
    // $this->load->model("PermohonanPaketFile");
    // $permohonan_paket_file = new PermohonanPaketFile();
    // $permohonan_paket_file->selectByParams(array("A.PERMOHONAN_PAKET_ID" => coalesce($this->getField("PERMOHONAN_PAKET_ID_OLD"), 0)));
    // while($permohonan_paket_file->nextRow())
    // {
  	// 	$this->setField("PERMOHONAN_PAKET_FILE_ID", $this->getNextId("PERMOHONAN_PAKET_FILE_ID","PERMOHONAN_PAKET_FILE"));
  	// 	$this->db->query("INSERT INTO PERMOHONAN_PAKET_FILE (permohonan_paket_file_id,permohonan_paket_id,path_file,tipe,ukuran,judul,urut,paket_id,created_by,created_date)
  	// 			SELECT '".$this->getField("PERMOHONAN_PAKET_FILE_ID")."', ".$generatePermohonanID.", A.nomor, A.keterangan, A.budget_awal, A.budget_terpakai, A.budget_akhir, A.created_by, A.created_date
  	// 			FROM permohonan_paket_file A
  	// 			WHERE A.permohonan_paket_id = ".$permohonan_paket_file->getField("PERMOHONAN_PAKET_ID")."
  	// 	");
    // }
    //
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

  }
?>
