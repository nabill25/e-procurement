<?php 
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */

$this->libsession->cekSession();    // block halaman khusus untuk masing-masing user
?>

<link href="<?= base_url() ?>lib/treeTable2/doc/stylesheets/master.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?= base_url() ?>lib/treeTable2/doc/javascripts/jquery.ui.js"></script>
<link href="<?= base_url() ?>lib/treeTable2/src/stylesheets/jquery.treeTable.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?= base_url() ?>lib/treeTable2/src/javascripts/jquery.treeTable.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$("#dnd-example").treeTable();
	$("#dnd-example .file, #dnd-example .folder").draggable({
		helper: "clone",
		opacity: .75,
		refreshPositions: true, // Performance?
		revert: "invalid",
		revertDuration: 300,
		scroll: true
	});
	
	$("#dnd-example .folder").each(function() {
		$($(this).parents("tr")[0]).droppable({
			accept: ".file, .folder",
			drop: function(e, ui) { 
			  $($(ui.draggable).parents("tr")[0]).appendBranchTo(this);
			},
			hoverClass: "accept",
			over: function(e, ui) {
				if(this.id != ui.draggable.parents("tr.parent")[0].id && !$(this).is(".expanded")) {
					$(this).expand();
				}
			}
		});
	});
	
	// Make visible that a row is clicked
	$("table#dnd-example tbody tr").mousedown(function() {
		$("tr.selected").removeClass("selected"); // Deselect currently selected rows
		$(this).addClass("selected");
	});
	
	// Make sure row is selected when span is clicked
	$("table#dnd-example tbody tr span").mousedown(function() {
		$($(this).parents("tr")[0]).trigger("mousedown");
	});
});


function reloadMonitoring()
{
	$('#treeSatker').treegrid('reload');	
}
</script>
<!-- END Plugin Code -->

<div class="row"> 
  <div class="col-md-12 col-sm-12">
    <div class="card">
      <div class="card-header card-head-inverse bg-primary">
        <h4 class="card-title text-white">Kategori</h4>
        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a> 
        <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <!-- <li><a data-action="close"><i class="ft-x"></i></a></li> -->
            </ul>
        </div>
      </div>
      <div class="card-content collapse show border-info border-darken-2">
        <div class="card-body area-datatable">
        <div id="idpencarian" class="area-pencarian">
        	<label>Pencarian <small>tekan enter</small> :  <input class="form-control" placeholder="" name="reqPencarian" aria-controls="example" type="search" style="width: 100%"></label>
		    	<div id="sticker" class="pull-right">
            <div class="area-menu-aksi" id="tombol">
            <span></span>
            </div>
	        </div>
        </div>
          <div class="table-responsive mt-1">
            <table class="table" id="treeSatker" class="easyui-treegrid" style="height: 350px" 
                    data-options="
                        url: 'katalog_kategori_json/json',
                        method: 'get',
                        idField: 'id',
                        treeField: 'text'">
                <thead>
                    <tr>
                        <th data-options="field:'aktif'" style="text-align: center">Aktif</th>
                        <th data-options="field:'text'">Nama</th>
                    </tr>
                </thead>
            </table> 
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>  

<div class="span12">
 <div class="card">
  <h3 class="card-heading simple">  </h3>
   <div class="card-body">
    <div class="control-group">
     <div class="controls">
        <form id="ff" class="easyui-form form-horizontal" method="post" data-options="novalidate:true" enctype="multipart/form-data"></form>  
     </div>
    </div>
   </div>
   <div class="card-actions">
   </div>
 </div> 
</div> 

<script>
$(document).ready( function () 
{
	$('input[name=reqPencarian]').change(function() {
		var value = this.value;
		$("html, body").animate({ scrollTop: 0 });
		var urlApp = 'katalog_kategori_json/json/?reqSearch='+ value;
		$('#treeSatker').treegrid(
		{
			url: urlApp
		});	
	});
	
	$('#treeSatker').treegrid({
		  onClickRow: function(param){
				$("#tombol span").remove();
				$("#tombol").html('<div><a class="<?= CLASS_BTN_PRIMARY ?>" onClick="openAdd(\'main/loadUrl/main/katalog_kategori_add?reqId=tambahParent\')" title="Tambah"><?= BTN_TAMBAH ?> Parent</a>&nbsp;&nbsp;<a class="<?= CLASS_BTN_SUCCESS ?>" onClick="openAdd(\'main/loadUrl/main/katalog_kategori_add?reqId=tambahChild\')" title="Tambah"><?= BTN_TAMBAH ?> Child</a>&nbsp;&nbsp;<a class="<?= CLASS_BTN_INFO ?>" onClick="openAdd(\'main/loadUrl/main/katalog_kategori_add/?reqId='+param.id+'\')" title="Edit"><?= BTN_UBAH ?></a>&nbsp;&nbsp;<a class="<?= CLASS_BTN_DANGER ?>"  onClick="deleteData(\'katalog_kategori_json/delete\','+"'"+param.id+"'"+')" title="Hapus"><?= BTN_HAPUS ?></a></div><hr>');		   
		  }
	});
}); 
</script>

