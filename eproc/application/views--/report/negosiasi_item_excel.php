<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
if($this->USER_TYPE_ID == "")
    redirect("main");

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=rekap_negosiasi_item.xls");

$reqId = $this->input->get("reqId");

$this->load->library("paketinfo"); $paketInfo = new paketinfo();
$this->load->library("libapiui"); $libapiui = new libapiui();
include_once("functions/string.func.php");
include_once("functions/default.func.php");
include_once("functions/date.func.php");
$this->load->model(array("PaketRekanan","Paket","PaketNegoisasi","RekananPaketPenawaran","Rekanan","PaketNegosiasiValidasi","Paketnegosiasiitem","PaketNegoisasi"));

$paket = new Paket();
$paket->selectByParamsMonitoring2(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
$paket->firstRow();
$reqPermohonanId = $paket->getField("PERMOHONAN_PAKET_ID");

$negosiasiitem = new Paketnegosiasiitem();
$negosiasiitem->selectByParams(array(), $dsplyRange, $dsplyStart, "AND PAKET_ID = ".$reqId."", $sOrder);
 
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<base href="<?=base_url()?>" />
	</head>
	<body>
    <table id="example" class="border-double table mb-0 table-bordered"> 
      <thead>
        <tr>
          <th style="width: 20%">Uraian</th>
          <th>Volume</th>
          <th>Durasi</th>
          <th>Harga Satuan</th>
          <th>Jumlah Harga Satuan</th>
          <th>Harga Penawaran</th>
          <th>Jumlah Harga Penawaran</th>
          <th>% HPS</th>
          <th>Harga Nego</th>
          <th>Jumlah Harga Nego</th>
          <th>% HPS</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no     =1;
        $total  = 0;
        $jumlahHarga = 0;
        $jumlahHargaPenawaran = 0;
        $jumlahHargaNego = 0;
        $html   = '';
        while ($negosiasiitem->nextRow()) {
          $jumlahHarga += $negosiasiitem->getField('JUMLAH_HARGA');
          $jumlahHargaPenawaran += $negosiasiitem->getField('JUMLAH_PENAWARAN');
          $jumlahHargaNego += $negosiasiitem->getField('JUMLAH_NEGOSIASI');
          $statusNego = $negosiasiitem->getField('STATUS_NEGO');
          if ($statusNego == '1' || $statusNego == '2' || $statusNego == '4' || $statusNego == '5') { $open = 'readonly'; $disabled = 'disabled'; } else { $open = ''; $disabled = ''; }
          $html .= '<tr>';
          $html .= '
            <td>'.$negosiasiitem->getField('URAIAN').'</td> 
            <td class="text-center">'.$negosiasiitem->getField('VOLUME').' '.$negosiasiitem->getField('SATUAN_VOLUME').'</td> 
            <td class="text-center">'.$negosiasiitem->getField('DURASI').' '.$negosiasiitem->getField('SATUAN_DURASI').'</td> 
            <td class="text-center">'.currencyToPage($negosiasiitem->getField('HARGA_SATUAN')).'</td> 
            <td class="text-center">'.currencyToPage($negosiasiitem->getField('JUMLAH_HARGA')).'</td> 
            <td class="text-center">'.currencyToPage($negosiasiitem->getField('NILAI_PENAWARAN')).'</td>
            <td class="text-center">'.currencyToPage($negosiasiitem->getField('JUMLAH_PENAWARAN')).'</td> 
            <td>'.$negosiasiitem->getField('PERSENTASE_PENAWARAN').'</td>';
          $html .= '<td>'.currencyToPage($negosiasiitem->getField("NILAI_NEGOSIASI")).'</td>';
          $html .= '<td>'.currencyToPage($negosiasiitem->getField('JUMLAH_NEGOSIASI')).'</td>';
          $html .= '<td>'.$negosiasiitem->getField('PERSENTASE_NEGOSIASI').'</td>'; 

          $html .= '</tr>';
          $no++;
        }
      echo $html;
      ?>
      </tbody>

      <?php
      if ($statusNego != '')
      { ?>
      <tfoot>
        <?php 
          $paketNegosiasi = new PaketNegoisasi();
          $paketNegosiasi->selectByParams(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
          $paketNegosiasi->firstRow();
         ?>
        <tr>
          <td class="text-center">
            <b>PPN</b> <?= currencyToPage($paketNegosiasi->getField('PPN')) ?: 0 ?> %
          </td>
          <td colspan="2"></td>
          <td></td>
          <td class="text-center"><?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_SATUAN')) ?: 0 ?></td>
          <td></td>
          <td class="text-center"><?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_PENAWARAN')) ?: 0 ?></td>
          <td></td>
          <td></td>
          <td class="text-center"><?= currencyToPage($paketNegosiasi->getField('PPN_JUMLAH_HARGA_NEGO')) ?: 0 ?></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td class="text-center"><b>TOTAL</b></td>
          <td colspan="2"></td>
          <td></td>
          <td class="text-center"> <?= currencyToPage($jumlahHarga + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_SATUAN')); ?></td>
          <td></td>
          <td class="text-center"><?= currencyToPage($jumlahHargaPenawaran + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_PENAWARAN')); ?></td>
          <td></td>
          <td></td>
          <td class="text-center"><?= currencyToPage($jumlahHargaNego + $paketNegosiasi->getField('PPN_JUMLAH_HARGA_NEGO')); ?></td>
          <td></td>
          <td></td>
        </tr>    
      </tfoot>
      <?php 
      } ?>
    </table>
	</body>
</html>
