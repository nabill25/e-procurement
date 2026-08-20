<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

function recordcoloring($i,$bright,$dark)
{
	if($i % 2 == 0)
		return $bright;
	else
		return $dark;
}

?>