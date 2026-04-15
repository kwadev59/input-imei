<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<style>

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11pt;
    margin:40px;
    color:#000;
}

/* HEADER */

.header{
    text-align:center;
    margin-bottom:10px;
}

.logo{
    width:120px;
    margin-bottom:5px;
}

.company{
    font-size:14pt;
    font-weight:bold;
}

.title{
    font-size:16pt;
    font-weight:bold;
    margin-top:10px;
}

.line{
    border-top:2px solid #000;
    margin-top:10px;
    margin-bottom:20px;
}

/* INFO */

.info-table{
    width:100%;
    margin-bottom:15px;
}

.info-table td{
    padding:4px;
}

/* DATA TABLE */

.data-table{
    width:100%;
    border-collapse:collapse;
}

.data-table th{
    border:1px solid #000;
    background:#f2f2f2;
    padding:6px;
    text-align:center;
    font-size:10pt;
}

.data-table td{
    border:1px solid #000;
    padding:6px;
    font-size:10pt;
}

.text-center{
    text-align:center;
}

/* SIGNATURE */

.signature{
    margin-top:60px;
    width:100%;
}

.signature td{
    text-align:center;
    width:50%;
}

.sign-space{
    height:80px;
}

.sign-name{
    font-weight:bold;
}

</style>

</head>

<body>

<div class="header">

    <!-- logo opsional -->
    <!-- <img src="logo.png" class="logo"> -->

    <div class="company">
        PT ASTRA AGRO LESTARI Tbk
    </div>

    <div class="title">
        BERITA ACARA PENGIRIMAN GADGET KE HEAD OFFICE <br>
        <P style="font-size:12pt; font-weight:bold;" margin-bottom:10px;>PT. BORNEO INDAH MARJAYA - PT. PALMA PLANTASINDO</P>
    </div>

</div>

<div class="line"></div>

<table class="info-table">

<tr>
<td width="20%"><b>No Register BAST</b></td>
<td width="2%">:</td>
<td width="30%"><b><?= esc($baste['no_baste']) ?></b></td>

<td width="20%"><b>Dibuat Pada</b></td>
<td width="2%">:</td>
<td><?= date('d F Y H:i', strtotime($baste['created_at'])) ?></td>
</tr>

<tr>
<td><b>Tanggal Kirim</b></td>
<td>:</td>
<td><?= date('d F Y', strtotime($baste['tanggal'])) ?></td>

<td><b>Total Gadget</b></td>
<td>:</td>
<td><?= count($items) ?> Unit</td>
</tr>

</table>


<table class="data-table">

<thead>
<tr>
<th width="4%">No</th>
<th width="14%">IMEI</th>
<th width="10%">PT</th>
<th width="6%">AFD</th>
<th width="11%">NPK</th>
<th width="18%">Nama Pengguna</th>
<th width="12%">Aplikasi</th>
<th width="25%">Kerusakan</th>
</tr>
</thead>

<tbody>

<?php if (empty($items)): ?>

<tr>
<td colspan="8" class="text-center">
Tidak ada data
</td>
</tr>

<?php else: ?>

<?php foreach ($items as $i => $item): ?>

<tr>
<td class="text-center"><?= $i+1 ?></td>
<td><b><?= esc($item['imei']) ?></b></td>
<td class="text-center"><?= esc($item['pt'] ?? '-') ?></td>
<td class="text-center"><?= esc($item['afd'] ?? '-') ?></td>
<td class="text-center"><?= esc($item['npk_pengguna'] ?? '-') ?></td>
<td><?= esc($item['nama_pengguna'] ?? '-') ?></td>
<td class="text-center"><?= esc($item['aplikasi'] ?? '-') ?></td>
<td><?= esc($item['kerusakan']) ?></td>
</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>



<table class="signature">

<tr>

<td>

Dibuat Oleh,

<div class="sign-space"></div>

<span class="sign-name">
<br>
<br>
<br>
<br>
<br>
( __________________________ )
</span>

<br>
Krani Technology

</td>


<td>

Diterima Oleh,

<div class="sign-space"></div>

<span class="sign-name">
<br>
<br>
<br>
<br>
<br>
( __________________________ )
</span>

<br>
Head Office - IT HO

</td>

</tr>

</table>


</body>
</html>