<?php
$pdfUrl = base_url('uploads/ijin_usaha/'.$pdfNIB); // Ganti path PDF dengan file yang benar di server
//echo $pdfUrl;exit;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Gabungan Proses KBLI</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }
        .frame-layout {
            display: flex;
            gap: 16px;
            padding: 16px;
            box-sizing: border-box;
            height: 100vh;
        }
        .frame-layout iframe {
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 8px;
            min-width: 0;
            height: 100%;
        }
        .frame-layout iframe#leftFrame {
            max-width: 45%;
        }
    </style>
</head>
<body>
    <div style="padding: 16px; background: #f5f5f5; border-bottom: 1px solid #ddd;">
        <a href="<?= base_url('/penyedia') ?>" style="display: inline-block; padding: 10px 16px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 600;">
            &larr; Kembali
        </a>
    </div>
    <div class="frame-layout">
        <iframe id="leftFrame" src="<?= base_url('/proses-kbli/'.$penyedia['rekanan_id']) ?>"></iframe>
        <iframe src="<?=$pdfUrl ?>" type="application/pdf"></iframe>
    </div>
</body>
</html>
