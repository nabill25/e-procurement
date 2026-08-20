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

class master_backup_json extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}       
		
		/* GLOBAL VARIABLE */
		$this->ID = $this->kauth->getInstance()->getIdentity()->REKANAN_ID;   
	}	
	
	function backup() 
	{
		// Load the DB utility class
		// $this->load->dbutil();
		$this->load->dbutil();

		// $dbs = $this->dbutil->list_databases();
		// foreach ($dbs as $db)
		// {
		//         echo $db.'<br>';
		// }die();

		//create format
		$db_format=array('format'=>'sql','filename'=>'mybackup.sql');

		// Backup your entire database and assign it to a variable
		$backup =& $this->dbutil->backup($db_format); 

		// Load the file helper and write the file to your server
		$this->load->helper('file');
		write_file('/uploads/backup/mybackup.sql', $backup); 

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download('mybackup.sql', $backup);

		echo json_encode(array('data' => $save));
		// echo json_encode(array('data' => "Data Berhasil di backup"));
		// redirect(base_url().'main/index/master_backup');
	}
	
}
?>
