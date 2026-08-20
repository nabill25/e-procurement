<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

 
$hook['post_controller'] = array(     
	'class' 	=> 'Db_log',             
	'function' 	=> 'logQueries',     
	'filename' 	=> 'db_log.php',    
	'filepath' 	=> 'hooks'         
	);
