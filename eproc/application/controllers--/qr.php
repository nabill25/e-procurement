<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

class qr extends CI_Controller {

	function __construct() {
		parent::__construct(); $this->db->query("SET TIME ZONE 'Asia/Jakarta'"); 		
		$this->db->query("alter session set nls_date_format='YYYY-MM-DD'"); 
	}
	
	public function index()
	{		
		$this->load->view('qr/index', $data);
	}

}

