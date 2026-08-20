<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 ?>
 
    <div class="card">
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body">
						<!--  FOR AUDIT-->
						<?php
            $reqId = httpFilterRequest("reqId");
            $this->load->model("Paket");
            $paket = new Paket();
            $paket->selectByParamsMonitoring(array(), -1, -1, " AND PAKET_ID = '".$reqId."' ");
            $paket->firstRow();
            $paket_metode_lelang_id = $paket->getField("PAKET_METODE_LELANG_ID");

						if($this->USER_TYPE_ID == 10 && $paket_metode_lelang_id != '6' && $paket_metode_lelang_id != '9')
						{ // bukan Purchasing/Pembelian Langsung ) // AUDIT // bukan Pembelian Offline
						?>
						<?php
						$this->load->model("Contractingrekanan");
						$spkpks = new Contractingrekanan();
						$spkpks->selectViewPKSSPK(array("A.PAKET_ID" => $reqId));
						$spkpks->firstRow();
						$reqRakananId = $spkpks->getField('REKANAN_ID') ?: '-';
						$reqPaketId = $spkpks->getField('PAKET_ID') ?: '-';
            if ($spkpks->countRow() > 0) {
              echo $this->libkontrak->getDokumenPendukung($reqPaketId,$reqRakananId);
            } else {
              echo '<p>Kontrak belum di buat</p>';
            }
            echo "<br>";
            echo '<a href="main/index/paket_detil?eid='.$reqId.'&key='.$paket->getField("PAKET_UUID").'" class="'.CLASS_BTN_DANGER.'"> '.BTN_KEMBALI.' </a>';
						} ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
