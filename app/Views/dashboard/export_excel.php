<?php
// Headers already sent in controller
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi Gadget</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>No</th>
                <th>Tanggal Input</th>
                <th>Site PT</th>
                <th>Afdeling</th>
                <th>Jabatan</th>
                <th>NIK Karyawan</th>
                <th>Nama Karyawan</th>
                <th>Status Gadget</th>
                <th>IMEI</th>
                <th>Keterangan</th>
                <th>Diinput Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach($laporan as $row): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['input_at'] ?></td>
                <td><?= $row['pt_site'] ?? '-' ?></td>
                <td><?= $row['afdeling'] ?></td>
                <td><?= $row['jabatan'] ?? '-' ?></td>
                <td><?= "'".$row['nik_karyawan'] ?></td>
                <td><?= $row['nama_karyawan'] ?></td>
                <td><?= $row['status_gadget'] ?></td>
                <td><?= "'".$row['imei'] ?></td>
                <td><?= $row['keterangan'] ?></td>
                <td><?= $row['nama_mandor'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
