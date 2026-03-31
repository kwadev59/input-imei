<?php
// Headers already sent in controller
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kepemilikan Gadget Pemanen & Pekerja Rawat</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th rowspan="2">Site/PT</th>
                <th rowspan="2">Afdeling</th>
                <th colspan="2">Pemanen</th>
                <th colspan="2">Pekerja Rawat</th>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <th>Ada Gadget</th>
                <th>Tidak Ada</th>
                <th>Ada Gadget</th>
                <th>Tidak Ada</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($gadget_stats)): ?>
                <tr><td colspan="6">Belum ada data.</td></tr>
            <?php else: ?>
                <?php foreach($gadget_stats as $pt => $ptData): ?>
                    <?php 
                        $afdelings = $ptData['afdelings'];
                        $ptCount = count($afdelings) + 1; 
                        $firstPt = true;
                    ?>
                    <?php foreach($afdelings as $afd => $stats): ?>
                        <?php 
                            $bgClass = '';
                            if ($stats['total_target'] > 0) {
                                if ($stats['total_input'] >= $stats['total_target']) {
                                    $bgClass = 'background-color: #d4edda;'; // Light green
                                } else {
                                    $bgClass = 'background-color: #ffeeba;'; // Light orange
                                }
                            }
                        ?>
                        <tr style="<?= $bgClass ?>">
                            <?php if($firstPt): ?>
                                <td rowspan="<?= $ptCount ?>" style="background-color: #ffffff;"><?= esc($pt) ?></td>
                                <?php $firstPt = false; ?>
                            <?php endif; ?>
                            <td><?= esc($afd) ?></td>
                            
                            <td><?= $stats['Pemanen']['ada'] ?></td>
                            <td><?= $stats['Pemanen']['tidak'] ?></td>
                            
                            <td><?= $stats['Pekerja Rawat']['ada'] ?></td>
                            <td><?= $stats['Pekerja Rawat']['tidak'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="background-color: #e2e3e5; font-weight: bold;">
                        <td>TOTAL <?= esc($pt) ?></td>
                        <td><?= $ptData['total_pt_ada_pemanen'] ?></td>
                        <td><?= $ptData['total_pt_tidak_pemanen'] ?></td>
                        <td><?= $ptData['total_pt_ada_rawat'] ?></td>
                        <td><?= $ptData['total_pt_tidak_rawat'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
