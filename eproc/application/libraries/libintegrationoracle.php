<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class libintegrationoracle
{
  private $_CI;

  function __construct()
  {
    $this->_CI =& get_instance();
    $this->_CI->load->library(array('kauth','Sftp'));
    $this->_CI->USER_LOGIN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
    $this->_CI->USER_LOGIN      =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN;
    $this->_CI->USER_NAMA       =  $this->_CI->kauth->getInstance()->getIdentity()->USER_NAMA;
    $this->_CI->USER_TYPE_ID    =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
    $this->_CI->LEGAL           =  $this->_CI->kauth->getInstance()->getIdentity()->LEGAL;
    $this->_CI->ID              =  $this->_CI->kauth->getInstance()->getIdentity()->REKANAN_ID;
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    $this->_CI->HOST = '82.180.152.126';
    $this->_CI->USER = 'u391423235';
    $this->_CI->PASS = 'GoLa1@123';
    $this->_CI->PORT = 65002;
    // $this->_CI->HOST = '10.224.11.26';
    // $this->_CI->USER = 'sftpprocure';
    // $this->_CI->PASS = 'Or4cl3erp2030!';
    // $this->_CI->PORT = 6567;
  }

  function getListPR()
  {
    $this->_CI->load->library('Sftp');
    $connected = $this->_CI->sftp->connect($this->_CI->HOST, $this->_CI->USER, $this->_CI->PASS, $this->_CI->PORT);
    if ($connected) {
        // echo "✅ Koneksi berhasil!<br>";
        $files = $this->_CI->sftp->listDir('public_html/titip/pr');
        // $files = $this->_CI->sftp->listDir('procurement/VMS/PR/UNPROCESS');

        // Filter file dengan ekstensi .xlsx saja
        if ($files) {
            $xlsxFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'xlsx';
            });

            // Reset index array
            $xlsxFiles = array_values($xlsxFiles);
        } else {
            $xlsxFiles = array();
        }


        return $xlsxFiles;
    } else {
        return "❌ Gagal konek ke SFTP";
    } 
  }

  function getListRKA()
  {
    $this->_CI->load->library('Sftp');
    $connected = $this->_CI->sftp->connect($this->_CI->HOST, $this->_CI->USER, $this->_CI->PASS, $this->_CI->PORT);
    if ($connected) {
        // echo "✅ Koneksi berhasil!<br>";
        $files = $this->_CI->sftp->listDir('public_html/titip/rka');
        // $files = $this->_CI->sftp->listDir('procurement/VMS/RKA/UNPROCESS');

        // Filter file dengan ekstensi .xlsx saja
        if ($files) {
            $xlsxFiles = array_filter($files, function($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'xlsx';
            });

            // Reset index array
            $xlsxFiles = array_values($xlsxFiles);
        } else {
            $xlsxFiles = array();
        }

        return $xlsxFiles;
    } else {
        return "❌ Gagal konek ke SFTP";
    } 
  }


}
