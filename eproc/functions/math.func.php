<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

function equalFloat($float1,$float2){
    return (abs($float1 - $float2) < 0.0001);
}

function div($p,$q){
    $rest=$p % $q;
    return (($p-$rest)/$q);
}
?>