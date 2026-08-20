<?php
// http://runnov.blogs.uny.ac.id/2015/09/25/script-auto-backup-database/comment-page-1/
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

include_once("functions/string.func.php");
include_once("functions/date.func.php");
include_once("functions/default.func.php");  

class Dbbackup extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'");
		$this->db->query("alter session set nls_date_format='YYYY-MM-DD'");
		$this->db->query("alter session set nls_numeric_characters='.,'"); 
		$this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$this->REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		$this->REMOTE_HOST = $_SERVER['REMOTE_HOST'];
		$this->REMOTE_PORT = $_SERVER['REMOTE_PORT'];
		$this->HTTP_ACCEPT = $_SERVER['HTTP_ACCEPT'];
		$this->REQUEST_URI = $_SERVER['REQUEST_URI'];
		$this->HTTP_HOST = $_SERVER['HTTP_HOST'];
		$this->SERVER_SOFTWARE = $_SERVER['SERVER_SOFTWARE'];
		$this->REQUEST_METHOD = $_SERVER['REQUEST_METHOD']; 
		$this->HTTP_TRY = $_SERVER['HTTP_TRY']; 
	}

	public function index()
	{  
		echo "Hai kaka, apa kabar?";
	}  

	public function backup($jenis,$date)
	{  
		if ($jenis == 'db') {
			$bc = $this->db();
		} else {
			$bc = $this->files();
		}

    	echo json_encode(array('respone' => $bc ));
	}  

	public function db() // udah oke
	{
		$this->load->dbutil();
		$this->load->helper('file');
		
		$db_name = 'backup-on-' . date("Y-m-d") . '.zip'; // file name
	 	$save  = 'backup/db/' . $db_name; // dir name backup output destination

		$config = array(
			'format'	=> 'zip',
			'filename'	=> 'database.sql'
		);

		if (file_exists($save)) {
			echo "Hari ini Database sudah dibackup";
		} else  {
			$backup =& $this->dbutil->backup($config);
			write_file($save, $backup);
			if (file_exists($save)) {
				return 'berhasil';
			} else {
				return 'gagal';
			}
		}
	}

	// backup files in directory
	function files()
	{
		$db_name = 'backup-file-on-' . date("Y-m-d") . ''; // file name
		$this->load->library('zip');
		$this->zip->read_dir('./application/controllers/');
		$this->zip->read_dir('./application/models/');
		$this->zip->read_dir('./application/views/');
	   	$this->zip->download(''.$db_name.'.zip');

		/*
		 $opt = array(
		   'src' => 'uploads', // dir name to backup
		   'dst' => 'backup/files' // dir name backup output destination
		 );
		 
		 // Codeigniter v3x
		 $this->load->library('RecurseZip_lib', $opt);
		 $download = $this->recursezip_lib->compress();
		 
		 // $zip    = $this->load->library('recurseZip_lib', $opt);     
		 // $download = $zip->compress();
		 // redirect(base_url($download));

		$backup =& $this->dbutil->backup($config);
		write_file($save, $backup);
		*/

		// $db_name = 'backup-file-on-' . date("Y-m-d") . '.zip'; // file name

		// $this->load->library('zip');
		// $this->zip->read_dir('js/');
	 //   	$this->zip->download($db_name); 
	 //   	return 'berhasil';
		 
	}

	// backup database.sql
	// public function db()
	// {
	// 	 $this->load->helper('url');
	// 	 $this->load->helper('file');
	// 	 $this->load->helper('download');
	// 	 $this->load->library('zip'); 

	// 	 $this->load->dbutil();
	// 	 $prefs = array(
	// 	   'format' => 'zip',
	// 	   'filename' => 'my_db_backup.sql'
	// 	 );
	// 	 $backup =& $this->dbutil->backup($prefs);
	// 	 $db_name = 'backup-on-' . date("Y-m-d-His") . '.zip'; // file name
	// 	 $save  = 'backup/db/' . $db_name; // dir name backup output destination

	// 	 write_file($save, $backup);
	// 	 // force_download($db_name, $backup);  
	// }

}
