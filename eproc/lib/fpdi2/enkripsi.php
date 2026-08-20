<?
$src_file = 'CONVERT.pdf';
$new_file = str_replace(".pdf", "", $src_file).'_BARU.pdf';
$dest_file = str_replace(".pdf", "", $src_file).'_PROTECTED.pdf';

$dir = str_replace("\\", "/", getcwd())."/";

//echo $dir;
//echo "gswin64 -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$dir.$new_file." ".$dir.$src_file."";
//exit;

//echo shell_exec("gswin64 --version") ;
//exit;
shell_exec("gswin64 -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$dir.$new_file." ".$dir.$src_file."");


require_once('FPDI_Protection.php');

$pdf = new FPDI_Protection();
$pagecount = $pdf->setSourceFile($new_file);

for ($loop = 1; $loop <= $pagecount; $loop++) {
    $tplidx = $pdf->importPage($loop);
    $pdf->addPage();
    $pdf->useTemplate($tplidx);
}

//$pdf->SetProtection(\FPDI_Protection::FULL_PERMISSIONS);
$pdf->SetProtection(\FPDI_Protection::FULL_PERMISSIONS, '123456');
//$pdf->SetProtection(\FPDI_Protection::FULL_PERMISSIONS, '123456', 'ABCDEF');

$pdf->Output($dest_file, 'F');

?>