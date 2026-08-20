<?php
$this->libsession->cekSession();
if ($this->USER_TYPE_ID == 27 && $this->LEVEL_PERENCANA != '2') { // Type khusu perencanan dan hanya untuk kasi
 redirect(base_url().'main/index/403');
}
?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script>
$(document).ready(function () {

    $('#tbl').DataTable({
        ajax: {
            url: "https://eproc.ui.ac.id/executive_json/contracting_paket",
            dataSrc: ''
        },
        columns: [
            { data: 'tahun' },
            { data: 'kode_rup' },
            { data: 'nomor_pr' },
            { data: 'nama_paket' },
			{
				data: 'nilai_pagu_rup',
				render: function (data) {
					if (!data) return '-';
					return 'Rp ' + Number(data).toLocaleString('id-ID');
				}
			},
            {
				data: 'nilai_rab',
				render: function (data) {
					if (!data) return '-';
					return 'Rp ' + Number(data).toLocaleString('id-ID');
				}
			},
            {
				data: 'nilai_hps',
				render: function (data) {
					if (!data) return '-';
					return 'Rp ' + Number(data).toLocaleString('id-ID');
				}
			},
            {
				data: 'nilai_kontrak',
				render: function (data) {
					if (!data) return '-';
					return 'Rp ' + Number(data).toLocaleString('id-ID');
				}
			},
            { data: 'rekap_status' }
        ]
    });

});
</script>

<style type="text/css">
  .card-text a {
    font-size: 11px;
  }
</style>

<section id="backColor">
  <div class="row">

    <div class="col-md-12 col-sm-12">
      <div class="card border-bottom-primary" style="zoom: 1;">
        <div class="card-header">
          <h4 class="card-title">Executive Report</h4>
          <div class="heading-elements" id="tombol">
            <span></span>
          </div>
        </div>
        <div class="card-body area-datatable">
          <div class="form-body">
            <div class="row">
              
            </div>
            <div>
              <table id="tbl" class="display" style="width:100%">
    <thead>
        <tr>
            <th style="width: 5%">Tahun</th>
            <th style="width: 10%">Kode RUP</th>
            <th style="width: 10%">No. PR</th>
            <th style="width: 20%">Nama Paket</th>
            <th>Nilai Pagu RUP</th>
            <th>Nilai RAB</th>
            <th>Nilai HPS</th>
            <th>Nilai Kontrak</th>
            <th>Status</th>
        </tr>
    </thead>
</table>



            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>