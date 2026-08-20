<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Daftar KBLI</title>
        <style>
            body {
                margin: 0;
                padding: 16px;
                font-family: Arial, sans-serif;
                box-sizing: border-box;
            }
            .table-content {
                max-width: 100%;
            }
            .status {
                margin-bottom: 16px;
                padding: 10px;
                background: #eef6ff;
                border: 1px solid #b6d4ff;
                border-radius: 6px;
                color: #0a3d62;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                border: 1px solid #ddd;
                text-align: left;
            }
            th {
                background: #f5f5f5;
            }
            label {
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="table-content">
            <h3>Daftar KBLI</h3>
            <form method="post" action="<?= base_url('/proses-kbli/'.$penyedia['rekanan_id']) ?>">
                <table>
                    <thead>
                        <tr>
                            <th>Approve</th>
                            <th>KBLI</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach($kbli as $k): ?>
                        <tr>
                            <td><input type="checkbox" name="selected[]" <?php if ($k['validasi'] == 1) echo 'checked'; ?> value="<?= $k['bidang_usaha_id'] ?>" onchange="this.form.submit()"></td>
                            <td><?= $k['bidang_usaha_id'] ?></td>
                            <td><?= $k['nama_kbli'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </body>
    </html>