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

class movefile extends CI_Controller {

 function execute($value='amin')
 {
   // Folder sumber tempat file berada
    $sourceFolder = '/var/www/html/eproc/';
    $destinationFolder = '/var/www/html/eproc/uploads/';

    // Lokasi file .txt yang berisi daftar file yang sudah ada di database
    $txtFilePath = '/var/www/html/eproc/uploads/file.txt';

    // Membaca isi file .txt dan mengonversinya menjadi array
    $databaseFiles = file($txtFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Memeriksa apakah file berhasil dibaca
    if ($databaseFiles === false) {
        die("Gagal membaca file .txt.");
    }

    // Mengambil daftar file dari folder sumber
    $sourceFiles = array_diff(scandir($sourceFolder), array('..', '.'));
    // Memindahkan file yang tidak ada di dalam file .txt
    // foreach ($sourceFiles as $file) {
    //     if (!in_array($file, $databaseFiles)) {
    //         // Menentukan path sumber dan tujuan
    //         $sourceFilePath = $sourceFolder . $file;
    //         $destinationFilePath = $destinationFolder . $file;
    //
    //         // Memindahkan file ke folder tujuan
    //         if (rename($sourceFilePath, $destinationFilePath)) {
    //             echo "File '$file' berhasil dipindahkan.\n";
    //         } else {
    //             echo "Gagal memindahkan file '$file'.\n";
    //         }
    //     }
    // }
 }

}
?>
