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
$dataInbox->selectByParams(array("A.PARENT" => 0, "A.INBOXCATEGORYID" => '1', "A.CREATED_BY" => $this->USER_LOGIN_ID)); // 1:RFI 2:Survey 3:Complain

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
          <h4 class="card-title"> RFI <small>(Request For Information)</small></h4>
          <div class="heading-elements" id="tombol"> 
	  		<a class="btn btn-danger text-white" id="backPage" onclick="backP()" style="display: none"> <i class="fa fa-cross"></i> X </a> 
          </div>
        </div>
        <div class="row" id="header-inbox">
		    <div class="form-group col-md-12" style="padding: 0 2.4%;" id="sticker2">
		        <!-- <a id="btnBuat" onclick="createdR()" class="btn btn-primary text-white" style="width: 100%"><i class="fa fa-plus-circle"></i> Buat RFI (Request For Information)</a> -->
		        <a id="btnBuat" href="main/index/inbox_rfi_add" class="btn btn-primary text-white" style="width: 100%"><i class="fa fa-plus-circle"></i> Buat RFI (Request For Information)</a>
		    </div>
		    <div class="form-group col-md-12">
		        <div class="card-body area-datatable"> 
		          <div class="form-body">  
		            <section id="lists">
		                <div id="basic-list">
		                    <input type="text" class="search form-control round border-primary mb-1" placeholder="Cari Request For Information" />
		                    <div class="row">
		                        <div class="col-md-2 col-sm-12">
		                            <button class="sort btn btn-block btn-outline-warning btn-round mb-2" data-sort="name">Sort by Subject</button>
		                        </div> 
		                    </div>
		                    <ul class="list-group list" style="height: 700px; overflow-y: scroll;"> 
		                    	<?php 
		                    	$this->load->library("libinbox"); $libinbox = new libinbox(); 
		                    	while ($dataInbox->nextRow()) { 
		                    	$dateEx = explode(' ', $dataInbox->getField("CREATED_DATE"));
		                    	$dateEx1 = getFormattedDate($dateEx[0]);
		                    	$dateEx2 = $dateEx[1];
		                    	?>
		                    	<a onClick="openAdd('main/loadUrl/main/inbox_rfi_detail?id=<?=$dataInbox->getField("INBOXID")?>');" style="margin: 7px 5px"> 
			                        <li class="list-group-item" style="background-color: #fff">
			                            <h3 class="name">
			                            	<div class="pull-right">
			                            		<small style="font-size: 12px; top: 0px"> <span class="fa fa-clock-o"></span> <?= $dateEx1.' '.$dateEx2 ?></small>
			                            	</div>
		                        			<?=$dataInbox->getField("INBOX_SUBJECT")?><br>
		                        			<small style="font-size: 11px"><?= $libinbox->extractPenerima($dataInbox->getField("INBOX_TO"),$dataInbox->getField("INBOXCATEGORYID"),$dataInbox->getField("INBOXID")); ?></small> 
			                            </h3> 
			                        </li> 
		                    	</a> 
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
