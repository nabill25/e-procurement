<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class libpaket
{
    private $_CI;

    function __construct()
    {
      $this->_CI =& get_instance();
      $this->_CI->load->library('session');
    }

    function detail($reqId)
    {
      include_once("functions/string.func.php");
      include_once("functions/date.func.php");
      include_once("functions/default.func.php");

      $this->_CI->load->model("Paket"); 
      $paket = new Paket();
      $paket->selectById($reqId); 
      $paket->firstRow(); 

      $html  = ''; 
      $html .= '<table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <td width="10%"><b>Kode RUP</b></td>
                      <td width="15%">'.$paket->getField('KODE_RUP').'</td>
                      <td width="10%"><b>Kode PR</b></td>
                      <td width="15%">'.$paket->getField('KODE_PR').'</td>
                    </tr>
                    <tr>
                      <td width="10%" colspan="1"><b>Nama Paket</b></td>
                      <td colspan="3">'.$paket->getField('NAMA').'</td>
                    </tr>
                    <tr>
                      <td width="10%"><b>Metode Pemilihan</b></td>
                      <td width="15%">'.$paket->getField('PAKET_METODE_LELANG').'</td>
                      <td width="10%"><b>Nama Jenis Pekerjaan</b></td>
                      <td width="15%">'.$paket->getField('PAKET_JENIS').'</td>
                    </tr>
                    <tr>
                      <td width="15%"><b>Nilai Pagu</b></td>
                      <td colspan="3">'.currencyToPage($paket->getField('NILAI')).'</td>
                  </tbody>
                </table>
              </div>';

      return $html;
    } 


}
