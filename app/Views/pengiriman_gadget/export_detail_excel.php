<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px 10px; font-size: 11pt; font-family: Arial, sans-serif; }
        th { background-color: #1a3c6e; color: #ffffff; font-weight: bold; text-align: center; }
        td { vertical-align: top; }
        .center { text-align: center; }
        .title { font-size: 14pt; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 10pt; color: #555; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="title">Detail History Pengiriman / BASTE</div>
    <div class="subtitle">Daftar lengkap IMEI gadget yang sudah dikirim — Diekspor pada: <?= date('d F Y, H:i') ?> WIB</div>
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>IMEI</th>
                <th>No BASTE</th>
                <th>Tanggal BASTE</th>
                <th>PT</th>
                <th>AFD</th>
                <th>NPK Pengguna</th>
                <th>Nama Pengguna</th>
                <th>Tipe Asset</th>
                <th>Aplikasi</th>
                <th>Kerusakan</th>
                <th>No Resi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($items)): ?>
                <?php foreach($items as $index => $item): ?>
                    <tr>
                        <td class="center"><?= $index + 1 ?></td>
                        <td><?= esc($item['imei']) ?></td>
                        <td><?= esc($item['no_baste']) ?></td>
                        <td class="center"><?= date('d-m-Y', strtotime($item['tanggal_baste'])) ?></td>
                        <td class="center"><?= esc($item['pt'] ?? '-') ?></td>
                        <td class="center"><?= esc($item['afd'] ?? '-') ?></td>
                        <td class="center"><?= esc($item['npk_pengguna'] ?? '-') ?></td>
                        <td><?= esc($item['nama_pengguna'] ?? '-') ?></td>
                        <td class="center"><?= esc($item['tipe_asset'] ?? '-') ?></td>
                        <td class="center"><?= esc($item['aplikasi'] ?? '-') ?></td>
                        <td><?= esc($item['kerusakan'] ?? '-') ?></td>
                        <td class="center"><?= esc($item['no_resi'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" class="center">Tidak ada data pengiriman.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
