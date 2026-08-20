<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Queryfree extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

  function Queryfree()
	{
      $this->Entity();
  }

  function selectByParams($query)
	{ 
		$this->query = $query;
		return $this->select($query);
  }
} 
?>
