<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

 function setGrade($skor){
  if ($skor >= 4.51 and $skor < 5) {
    $gradeNya = 'Sangat Baik'; // A
  } elseif ($skor >= 3.51 and $skor < 4.5) {
    $gradeNya = 'Baik'; // B
  } elseif ($skor >= 2.51 and $skor < 3.5) {
    $gradeNya = 'Cukup'; // C
  } elseif ($skor >= 1.51 and $skor < 2.5) {
    $gradeNya = 'Buruk'; // D
  } else {
    $gradeNya = 'Sangat Buruk';
  } 
  return $gradeNya;
 }
?>