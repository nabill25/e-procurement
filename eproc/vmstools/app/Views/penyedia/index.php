<!-- CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- Table -->
<div class="card p-4" style="width:80%; margin: 0 auto;">
    <h3>Daftar Perusahaan</h3>
    <table id="tabelPerusahaan" class="display" style="width:100%">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Perusahaan</th>
                <th>email</th>
                <th>Diproses Oleh</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; 
            foreach($penyedia as $p): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $p['tipe'].". ".$p['nama']; ?></td>
            <td><?= $p['email']; ?></td>
            <td><?= $p['validate_by']; ?></td>
            <td><a href='<?= base_url('proses-penyedia/' . $p['rekanan_id']) ?>' class="btn btn-primary">Proses</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
            $('#tabelPerusahaan').DataTable({
            "processing": true,
            "pageLength": 20,
            "language": {
                "search": "Cari Perusahaan:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ perusahaan",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Lanjut",
                    "previous": "Kembali"
                }
            },
            // Opsional: Atur lebar kolom atau urutan
            "columnDefs": [
                { "width": "5%", "targets": 0 }
            ]
            });
    });
    </script>