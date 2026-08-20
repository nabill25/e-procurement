<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->load->library("kauth");  $userLogin = new kauth(); 
$this->load->model("Rekanan");
include_once("functions/date.func.php");
include_once("functions/default.func.php");
require_once "WEB-INF/base-main/excel/class.writeexcel_workbookbig.inc.php";
require_once "WEB-INF/base-main/excel/class.writeexcel_worksheet.inc.php";

/* create objects */
$rekanan = new Rekanan();

/* LOGIN CHECK */

set_time_limit(300);
ini_set("memory_limit","500M");
ini_set('max_execution_time', 520);

$statement = " AND NVL(STATUS_VALIDASI, 0) = 0 AND TANGGAL_HAPUS IS NULL ";
$rekanan->selectByParams(array(),-1,-1,$statement);
//echo $rekanan->query;

$fname = tempnam("/tmp", "cetak_daftar_rekanan_belum_valid.xls");
$workbook = & new writeexcel_workbookbig($fname);
$worksheet = &$workbook->addworksheet();

//$worksheet->set_column(kolom ke, sampai kolom ke, lebar kolom);
$worksheet->set_column(0, 0, 3.43);
$worksheet->set_column(1, 1, 11.43);
$worksheet->set_column(2, 2, 22.71);
$worksheet->set_column(3, 3, 28.71);
$worksheet->set_column(4, 4, 13.71);

$heading =& $workbook->addformat(array(align => 'center', bold => 1 ,font => 'Arial Narrow'));
$text_format =& $workbook->addformat(array(font => 'Arial Narrow'));
$text_format_center =& $workbook->addformat(array(font => 'Arial Narrow', align => 'center'));

$tanggal =& $workbook->addformat(array(num_format => ' dd mmmm yyy'));
$align =& $workbook->addformat();
$align->set_align('left');
$uang =& $workbook->addformat(array(num_format => '#,##0.00'));

$worksheet->write(0, 0, "No", $heading);
$worksheet->write(0, 1, "No Registrasi", $heading);
$worksheet->write(0, 2, "Nama",  $heading);
$worksheet->write(0, 3, "Alamat",  $heading);
$worksheet->write(0, 4, "Tanggal Daftar", $heading);

$row = 1;
while($rekanan->nextRow())
{
	$worksheet->write($row, 0, $row, $text_format_center);	
	$worksheet->write($row, 1, $rekanan->getField('KODE'), $text_format_center);
	$worksheet->write($row, 2, $rekanan->getField('NAMA'), $text_format);
	$worksheet->write($row, 3, $rekanan->getField('ALAMAT'), $text_format);
	$worksheet->write($row, 4, getFormattedDate($rekanan->getField('TANGGAL_DAFTAR')), $text_format_center);
	$row++;
}

$workbook->close();

header("Content-Type: application/x-msexcel; name=\"cetak_daftar_rekanan_belum_valid.xls\"");
header("Content-Disposition: inline; filename=\"cetak_daftar_rekanan_belum_valid.xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);
?>