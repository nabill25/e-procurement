<?
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
$this->load->library("crfs_protect"); $csrf = new crfs_protect('_crfs_rr');
?>
<script type="text/javascript">
$(document).ready(function() {
	
	$(function(){
		$('#ff').form({
			url:'<?= base_url('rekanan_json/registrasi') ?>',
			onSubmit:function(){
				return $(this).form('validate');
			},
			success:function(data){
				arrData = data.split("-");
				
				if(arrData[0] == "0")
					$.messager.alert('Informasi',arrData[2],'info');				
				else
					document.location.href = 'login/action/?reqUser='+arrData[0]+'&reqPasswd='+arrData[1];
				/*if(arrData[0] == "0")
					$.messager.alert('Informasi',arrData[1],'info');				
				else
					document.location.href = 'app/index/registrasi_rekanan_informasi/?reqId='+arrData[0];*/
					
			}
		});
		
	});

	$(function(){
		$('input[name="reqStatus"]').on('change', function() {
			  var radioValue = $('input[name="reqStatus"]:checked').val();        
			  if(radioValue == "0")
			  {
				$( "input[name*='reqSuratKuasa']" ).prop("disabled", "disabled");  
				$( "input[name*='reqSuratKuasa']" ).val("");  
				$("#reqSuratKuasaTanggal").datebox({ disabled:true, required:false });
				$("#reqSuratKuasaNomor").validatebox({ required:false });
				$("#reqSuratKuasaNotaris").validatebox({ required:false });
			  } 
			  else
			  {
				$( "input[name*='reqSuratKuasa']" ).prop("disabled", "");  
				$("#reqSuratKuasaTanggal").datebox({ disabled:false, required:true });
				$("#reqSuratKuasaNomor").validatebox({ required:true });
				$("#reqSuratKuasaNotaris").validatebox({ required:true });
			  }
		});
		
	});	
		$("#chk_agreement").click(countChecked);
	
});

function countChecked() {
	  var n = $("#chk_agreement:checked").length;
	  //alert(n);
	  if(n){
		  $("#reqSubmit").show(0);
	  }else{
		  $("#reqSubmit").hide(0);
	  }
}
</script>

<section id="backColor">
  <div class="row"> 

    <div class="col-md-3 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;"> 
        <div class="card-header">
          <div class="alert alert-icon-right alert-info alert-dismissible" role="alert">
            <span class="alert-icon"><i class="fa fa-info"></i></span>
            <strong>Kategori.</strong>
          </div>
        </div>
        <div class="card-body"> 
          <div class="card-text">
            <ul class="list-style-square">
              <li>Facilisis in pretium nisl aliquet</li>
              <li>Nulla volutpat aliquam velit
                  <ul class="list-style-square">
                      <li>Phasellus iaculis neque</li>
                      <li>Ac tristique libero volutpat at</li>
                  </ul>
              </li>
              <li>Faucibus porta lacus fringilla vel</li>
              <li>Aenean sit amet erat nunc</li>
              <li>Facilisis in pretium nisl aliquet</li>
              <li>Nulla volutpat aliquam velit
                  <ul class="list-style-square">
                      <li>Phasellus iaculis neque</li>
                      <li>Ac tristique libero volutpat at</li>
                  </ul>
              </li>
              <li>Faucibus porta lacus fringilla vel</li>
              <li>Aenean sit amet erat nunc</li>
            </ul>
          </div>
        </div>
      </div>
    </div> 

    <div class="col-md-9 col-sm-12">
      <section id="basic-examples">
        <div class="row">
          <div class="col-12 mb-1">
            <!-- <h4 class="text-uppercase">Basic Examples</h4> -->
            <a href="app/index/katalog_add" class="btn btn-primary mr-1 text-white"> <i class="fa fa-cogs"></i> Kelola Katalog </a> 
          </div>
        </div>
        <div class="row match-height">
          <?php 
          for ($i=0; $i <20 ; $i++) { 
          ?>
          <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card" style="height: 441.5px;">
              <div class="card-content">
                <img class="card-img-top img-fluid" src="images/katalog/06.jpg" alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">
                    <small>22101528-ALB-004675362</small>
                    KOMATSU WA380-6
                  </h4>
                  <p class="card-text">Icing powder caramels macaroon. Toffee sugar plum brownie pastry gummies jelly.</p>
                </div>
              </div>
            </div>
          </div>
          <?php 
          } ?>
        </div> 
      </section>
    </div> 

  </div>  
</section> 