<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

$active_group = 'default';
$active_record = TRUE;

$db['default'] = array(
	'hostname' => 'localhost',
	'username' => 'eproc_dbuser',
	'password' => '3p1R4@fZ5!k',
	'database' => 'eproc_migrasi', // eproc_dev || eproc_migrasi
	'dbdriver' => 'postgre',
	'port'   => 5432, # Add
	'dbprefix' => '',
	'pconnect' => TRUE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

/* End of file database.php */
/* Location: ./application/config/database.php */
