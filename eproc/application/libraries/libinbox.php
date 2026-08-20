<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */


class libinbox
{
    private $_CI; 

    function __construct()
    {
      $this->_CI =& get_instance();
      $this->_CI->load->library('kauth'); 
      $this->_CI->USER_LOGIN_ID   =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
      $this->_CI->USER_LOGIN      =  $this->_CI->kauth->getInstance()->getIdentity()->USER_LOGIN;
      $this->_CI->USER_NAMA       =  $this->_CI->kauth->getInstance()->getIdentity()->USER_NAMA;
      $this->_CI->USER_TYPE_ID    =  $this->_CI->kauth->getInstance()->getIdentity()->USER_TYPE_ID; 
      $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    } 

    function extractPenerima($penerima,$categoryid=null,$inboxid=null)
    {
      $this->_CI->load->model("Rekanan");
      $this->_CI->load->model("Inbox");
      $rekanan  = new Rekanan();
      $inbox    = new Inbox();
      $penerimaEx = explode(',', $penerima);

      $namaPenerima = "";
      foreach ($penerimaEx as $key => $value) {
        $rekanan->selectByParams(array('A.REKANAN_ID' => $value));
        $rekanan->firstRow();
        if ($inboxid) { 
          $inbox->selectByParams(array('A.INBOXCATEGORYID' => $categoryid, 'A.CREATED_BY' => $value, "A.PARENT" => $inboxid));
          $inbox->firstRow();
          // echo $inbox->query;
          if ($inbox->getField("CREATED_BY")) {
            $namaPenerima .= '<span class="badge badge-primary" style="margin-right:.2rem; padding:4px 10px"><i class="fa fa-check-square-o"></i> '.$rekanan->getField("NAMA").'</span>';
          } else {
            $namaPenerima .= '<span class="badge badge-danger" style="margin-right:.2rem; padding:4px 10px">'.$rekanan->getField("NAMA").'</span>';
          }

        } else {
          $namaPenerima .= '<span class="badge badge-danger" style="margin-right:.2rem; padding:4px 10px">'.$rekanan->getField("NAMA").'</span>';
        }
      }

      return $namaPenerima;
    }
  }
