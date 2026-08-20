<?php
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 *
 */

$this->libsession->cekSession();

$this->load->model("Inbox");
$dataInbox = new Inbox;
$dataInboxReplay = new Inbox;
$dataInbox->selectByParams(array("A.PARENT" => 0, "A.INBOXCATEGORYID" => '3')); // 1:RFI 2:Survey 3:Complain

 ?>
<script type="text/javascript">
// $(document).ready(function() {
 function createdR() {
  $('#header-inbox').hide();
  $('#lists').hide();
  $('#backPage').show();
 }

 function backP() {
  $('#header-inbox').show();
  $('#lists').show();
  $('#backPage').hide();
 }
// });
</script>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary">
        <div class="card-header">
          <h4 class="card-title"> Pertanyaan atau Pengajuan</h4>
          <div class="heading-elements" id="tombol">
	  		<a class="btn btn-danger text-white" id="backPage" onclick="backP()" style="display: none"> <i class="fa fa-cross"></i> X </a>
          </div>
        </div>
        <div class="row" id="header-inbox">
		    <div class="form-group col-md-12">
		        <div class="card-body area-datatable">
		          <div class="form-body">
		            <section id="lists">
		                <div id="basic-list">
		                    <input type="text" class="search form-control round border-primary mb-1" placeholder="Cari Pertanyaan . . ." />
		                    <div class="row">
		                        <div class="col-md-2 col-sm-12">
		                            <button class="sort btn btn-block btn-outline-warning btn-round mb-2" data-sort="name">Sort by Subject</button>
		                        </div>
		                    </div>
		                    <ul class="list-group list" style="height: 700px; overflow-y: scroll;">
		                    	<?php
		                    	$this->load->library("libinbox"); $libinbox = new libinbox();
		                    	while ($dataInbox->nextRow()) {
		                    		
		                    	$dataInboxReplay->selectByPenerima(array("A.PARENT" => $dataInbox->getField("INBOXID"), "A.INBOXCATEGORYID" => '3', "A.CREATED_BY" => $this->USER_LOGIN_ID), -1, -1); // 1:RFI 2:Survey 3:Complain
						              $dataInboxReplay->firstRow();
								          $cekDataReplay = $dataInboxReplay->getField("CREATED_BY");
		                    	$dateEx = explode(' ', $dataInbox->getField("CREATED_DATE"));
		                    	$dateEx1 = getFormattedDate($dateEx[0]);
		                    	$dateEx2 = $dateEx[1];
		                    	if (isset($cekDataReplay)) {
		                    		$background = '#f3f3f3';
		                    		$fontWeight = 'font-weight: normal';
                            $jawab = '<span class="badge badge-primary" style="margin-right:.2rem; padding:4px 10px;font-size: 11px"><i class="fa fa-check-square-o"></i> Sudah di jawab</span>';
		                    	} else {
		                    		$background = '#fff';
		                    		$fontWeight = 'font-weight: bold';
                            $jawab = '<span class="badge badge-danger" style="margin-right:.2rem; padding:4px 10px;font-size: 11px"><i class="fa fa-remove"></i> Belum di jawab</span>';
		                    	}

		                    	$kalimat = strip_tags($dataInbox->getField("INBOX_CONTENT"));
          								$jumlah = "60";
          								$content = implode(" ", array_slice(explode(" ", $kalimat), 0, $jumlah));
		                    	?>
			                        <li class="list-group-item" style="background-color: <?= $background ?>;">
			                            <h3 class="name">
			                            	<div class="pull-right">
			                            		<small style="font-size: 12px; top: 0px"> <span class="fa fa-clock-o"></span> <?= $dateEx1.' '.$dateEx2 ?></small><br>
                                      <small><?= $jawab ?></small>
			                            	</div>
		                        			<?=$dataInbox->getField("INBOX_SUBJECT")?><br>
		                        			<small style="font-size: 11px"><span class="badge badge-primary" style="margin-right:.2rem; padding:4px 10px"><?= $dataInbox->getField("INBOX_FROM"); ?></span></small> <br>
                        					<small style="font-size: 12px"><?= $content ?></small><br><br>
                        					<a onclick="openAdd('main/loadUrl/main/inbox_complain_detail?id=<?=$dataInbox->getField("INBOXID")?>')" class="icon-list iconsize" data-toggle="tooltip" data-placement="bottom" title="Detail"></a> &nbsp;
                    						  <a href="main/index/inbox_complain_add?id=<?=$dataInbox->getField("INBOXID")?>" class="icon-action-undo iconsize" data-toggle="tooltip" data-placement="bottom" title="Reply"></a>
			                            </h3>
			                        </li>
		                    	<?php
		                    	} ?>
		                    </ul>
		                </div>
					</section>
		          </div>
		        </div>
		    </div>
        </div>
      </div>
    </div>
  </div>
</section>
