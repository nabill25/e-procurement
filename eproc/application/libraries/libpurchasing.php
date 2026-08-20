<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

class libpurchasing
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
    $this->_CI->LEGAL           =  $this->_CI->kauth->getInstance()->getIdentity()->LEGAL; 
    $this->_CI->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
  }   

  public function getTableFile($reqId,$statement=null)
  {

    $this->_CI->load->model("Purchasingfile");

    $purchasingfile = new Purchasingfile(); 
    $purchasingfile->selectByParams(array("A.PAKET_ID" => $reqId),-1,-1,$statement); 

    $html  =  '';
    $html .= '
      <thead>
        <tr class="backcolornew">
          <th class="text-center" width="10px">No<br>&nbsp;</th>
          <th>Nama Dokumen<br>&nbsp;</th>
          <th>Keterangan<br>&nbsp;</th>
          <th class="text-center" width="10px">Aksi<br>&nbsp;</th>
        </tr>       
      </thead>';
      $html .= '
      <tbody> ';
        $no=1;
        if ($purchasingfile->countRow() > 0) { 
          while($purchasingfile->nextRow())
          {  

            if ($purchasingfile->getField('file_nama_encrypt') != '' && file_exists($purchasingfile->getField('file_path').'/'.$purchasingfile->getField('file_nama_encrypt'))) {
              $linkDok = '<a href="'.$purchasingfile->getField('file_path').'/'.$purchasingfile->getField('file_nama_encrypt').'" target="_blank">'.$purchasingfile->getField('file_nama').'</a>';
            } else {
              $linkDok = $purchasingfile->getField('file_nama');
            } 

      $html .= '
            <tr>
              <td class="text-center">'.$no.'</td>
              <td>
                '.$linkDok.'<br>
                <span class="badge badge-primary">Jenis Dok: '.$purchasingfile->getField('file_jenis').'</span> <br>
                <small><i>Oleh: '.$purchasingfile->getField('created_by_str').'</i></small>
              </td>
              <td>'.$purchasingfile->getField('file_keterangan').'</td>  ';
      if ($purchasingfile->getField('created_by') == $this->_CI->USER_LOGIN_ID) {
      $html .= '<td class="text-center"><a onClick="deleteData(\'katalog_offline_json/deleteFile/\', '.$purchasingfile->getField("purchasingfileid").')"><span class="fa fa-trash btn-xs btn-danger" style="padding:5px; border-radius:4px"></span></a></td>';
      } else {
      $html .= '<td class="text-center">-</td>';
      }
      $html .= '
            </tr>
           ';
            $no++;
          }
        } else {
          // echo '<tr><td colspan="6">. : : Tidak ada dokumen : : .</td></tr>';
          $html .= '<tr><td></td><td>. : : Tidak ada dokumen : : .</td><td></td><td></td></tr>';
        } 
    $html .= '
      </tbody>';

    return $html;
  } 

}
