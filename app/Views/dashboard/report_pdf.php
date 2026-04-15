<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mandor - <?= esc($mandor['nama_lengkap']) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 20mm 15mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.5;
            background: #fff;
        }

        /* ======== PRINT CONTROLS ======== */
        .no-print {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .no-print .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .no-print .btn:active { transform: scale(0.96); }
        .no-print .btn-back { background: #475569; color: #fff; }
        .no-print .btn-back:hover { background: #64748b; }
        .no-print .btn-print { background: #3b82f6; color: #fff; }
        .no-print .btn-print:hover { background: #2563eb; }
        .no-print .hint { color: #94a3b8; font-size: 11px; margin-left: 8px; }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page-content { padding-top: 0 !important; }
        }

        @media screen {
            .page-content { padding-top: 70px; }
        }

        /* ======== PAGE CONTENT ======== */
        .page-content {
            max-width: 210mm;
            margin: 0 auto;
            padding-left: 10mm;
            padding-right: 10mm;
        }

        /* ======== HEADER ======== */
        .report-header {
            text-align: center;
            padding: 16px 0 12px;
            border-bottom: 3px double #1a1a1a;
            margin-bottom: 16px;
        }

        .report-header h1 {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .report-header h2 {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 2px;
        }

        .report-header .subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        /* ======== META INFO ======== */
        .meta-section {
            display: flex;
            gap: 24px;
            margin-bottom: 16px;
        }

        .meta-table {
            flex: 1;
        }

        .meta-table table {
            width: 100%;
            border: none;
        }

        .meta-table td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
            font-size: 11px;
        }

        .meta-table td.label {
            width: 100px;
            font-weight: 700;
            color: #374151;
        }

        .meta-table td.separator {
            width: 10px;
            text-align: center;
        }

        /* ======== SUMMARY BOXES ======== */
        .summary-row {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }

        .summary-box .summary-value {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
        }

        .summary-box .summary-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
        }

        .summary-box.total { border-color: #3b82f6; }
        .summary-box.total .summary-value { color: #1d4ed8; }
        .summary-box.ada { border-color: #10b981; }
        .summary-box.ada .summary-value { color: #059669; }
        .summary-box.tidak { border-color: #ef4444; }
        .summary-box.tidak .summary-value { color: #dc2626; }

        /* ======== DATA TABLE ======== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10.5px;
        }

        .data-table thead th {
            background: #1e293b;
            color: #fff;
            font-weight: 700;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 8px;
            text-align: left;
            border: 1px solid #1e293b;
        }

        .data-table thead th:first-child { text-align: center; }

        .data-table tbody td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) { background: #f8fafc; }
        .data-table tbody tr:hover { background: #f1f5f9; }

        .data-table .col-no { text-align: center; width: 30px; color: #6b7280; }
        .data-table .col-waktu { width: 75px; font-size: 10px; }
        .data-table .col-nama { width: auto; }
        .data-table .col-nik { width: 65px; font-family: 'Courier New', monospace; font-size: 10px; }
        .data-table .col-jabatan { width: 85px; font-size: 10px; }
        .data-table .col-status { width: 70px; text-align: center; font-weight: 700; }
        .data-table .col-detail { width: auto; font-size: 10px; }

        .status-ada { color: #059669; }
        .status-tidak { color: #dc2626; }

        .imei-code {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            background: #f1f5f9;
            padding: 1px 4px;
            border-radius: 3px;
            border: 1px solid #e2e8f0;
        }

        /* ======== VERIFIKASI BADGES ======== */
        .verif-badge {
            display: inline-block;
            font-size: 8.5px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .verif-cocok { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .verif-tidak { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .verif-notfound { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .verif-noowner { background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; }
        .verif-na { color: #9ca3af; }

        /* ======== SIGNATURE ======== */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border: none;
        }

        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 4px;
            font-size: 11px;
        }

        .signature-table .sig-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 60px;
        }

        .signature-table .sig-name {
            font-weight: 700;
            border-top: 1px solid #1a1a1a;
            display: inline-block;
            padding-top: 4px;
            min-width: 150px;
        }

        .signature-table .sig-role {
            font-size: 10px;
            color: #6b7280;
        }

        /* ======== FOOTER ======== */
        .report-footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #d1d5db;
            font-size: 9px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <!-- Print Controls (hidden on print) -->
    <div class="no-print">
        <a href="javascript:window.history.back()" class="btn btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak / Simpan PDF</button>
        
        <?php 
            $printable_count = 0;
            foreach($inputs as $r) { if($r['status_gadget'] == 'Ada' && !empty($r['imei'])) $printable_count++; }
        ?>
        
        <?php if($printable_count > 0): ?>
            <button id="btnPrintOnline" class="btn" style="background: #10b981; color: #fff;">
                <i class="bi bi-printer"></i> Print Semua Label (<?= $printable_count ?>)
            </button>
            <div id="printProgress" style="display:none; color: #10b981; font-weight: bold; margin-left: 10px;">
                Mengirim: <span id="currentPrint">0</span> / <?= $printable_count ?>
            </div>
        <?php endif; ?>

        <span class="hint">Gunakan opsi "Save as PDF" pada dialog print browser</span>
    </div>

    <!-- Load Bootstrap Icons if not available -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPrintOnline = document.getElementById('btnPrintOnline');
        
        // Auto-trigger if query param auto_print=1 is present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto_print') === '1') {
            setTimeout(() => {
                if (btnPrintOnline) btnPrintOnline.click();
            }, 500);
        }

        if (btnPrintOnline) {
            btnPrintOnline.addEventListener('click', async function() {
                if (!confirm('Kirim ' + <?= $printable_count ?> + ' label gadget ke antrian cetak online?')) return;

                const inputs = <?= json_encode($inputs) ?>;
                const progressDiv = document.getElementById('printProgress');
                const currentSpan = document.getElementById('currentPrint');
                
                btnPrintOnline.disabled = true;
                btnPrintOnline.style.opacity = '0.5';
                progressDiv.style.display = 'inline-block';
                
                let count = 0;
                for (const item of inputs) {
                    if (item.status_gadget === 'Ada' && item.imei) {
                        const payload = {
                            imei: item.imei,
                            nama_pengguna: item.nama_karyawan,
                            npk_pengguna: item.nik_karyawan,
                            aplikasi: item.aplikasi || '-',
                            kerusakan: 'Distribusi',
                            pt: item.pt_master || '-',
                            afd: item.afd_master || '-',
                            pos_title: item.pos_title_master || '-',
                            tipe_asset: item.tipe_asset_master || '-',
                            group_asset: item.group_asset_master || '-',
                            part_asset: item.part_asset_master || '-'
                        };

                        try {
                            const response = await fetch('<?= base_url("api/print") ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(payload)
                            });
                            const result = await response.json();
                            if (result.status === 'queued' || result.status === 'duplicate') {
                                count++;
                                currentSpan.textContent = count;
                            }
                        } catch (err) {
                            console.error('Print Error:', err);
                        }
                        
                        // Small delay to prevent overloading server
                        await new Promise(r => setTimeout(r, 200));
                    }
                }
                
                alert('Berhasil mengirim ' + count + ' job print ke antrian.');
                btnPrintOnline.disabled = false;
                btnPrintOnline.style.opacity = '1';
                progressDiv.style.display = 'none';
            });
        }
    });
    </script>

    <?php
        // Mapping pt_site ke nama PT
        $pt_map = [
            'BIM1' => 'PT BORNEO INDAH MARJAYA',
            'PPS1' => 'PT PALMA PLANTASINDO',
        ];
        $pt_site = $mandor['pt_site'] ?? '';
        $pt_nama = $pt_map[strtoupper($pt_site)] ?? $pt_site;

        // Hitung statistik
        $total = count($inputs);
        $ada = 0;
        $tidak = 0;
        $cocok = 0;
        $tidak_cocok = 0;
        foreach($inputs as $row) {
            if($row['status_gadget'] == 'Ada') $ada++;
            else $tidak++;
            if($row['verifikasi'] == 'cocok') $cocok++;
            if($row['verifikasi'] == 'tidak_cocok') $tidak_cocok++;
        }
    ?>

    <div class="page-content">

        <!-- Header -->
        <div class="report-header">
            <h1>Laporan Hasil Input Gadget</h1>
            <h2><?= esc($pt_nama) ?></h2>
            <div class="subtitle">Sistem Distribusi & Checklist Gadget Karyawan</div>
        </div>

        <!-- Meta Info -->
        <div class="meta-section">
            <div class="meta-table">
                <table>
                    <tr>
                        <td class="label">Nama Mandor</td>
                        <td class="separator">:</td>
                        <td><strong><?= esc($mandor['nama_lengkap']) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="label">NPK / ID</td>
                        <td class="separator">:</td>
                        <td><?= esc($mandor['npk'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Tipe Mandor</td>
                        <td class="separator">:</td>
                        <td><?= esc($mandor['tipe_mandor'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
            <div class="meta-table">
                <table>
                    <tr>
                        <td class="label">Afdeling</td>
                        <td class="separator">:</td>
                        <td><?= esc($mandor['afdeling_id']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">PT / Site</td>
                        <td class="separator">:</td>
                        <td><?= esc($pt_site) ?><?php if($pt_nama && $pt_nama !== $pt_site): ?> — <?= esc($pt_nama) ?><?php endif; ?></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Cetak</td>
                        <td class="separator">:</td>
                        <td><?= $generated_at ?></td>
                    </tr>
                </table>
            </div>
        </div>



        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="text-align:center;">No</th>
                    <th>Waktu Input</th>
                    <th>Nama Karyawan</th>
                    <th>NIK</th>
                    <th>Jabatan</th>
                    <th style="text-align:center;">Status</th>
                    <th>IMEI / Keterangan</th>
                    <th style="text-align:center;">Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($inputs)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:24px;color:#9ca3af;">Belum ada data input.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($inputs as $row): ?>
                    <tr>
                        <td class="col-no"><?= $no++ ?></td>
                        <td class="col-waktu">
                            <?= date('d/m/Y', strtotime($row['input_at'])) ?><br>
                            <span style="color:#6b7280;"><?= date('H:i', strtotime($row['input_at'])) ?></span>
                        </td>
                        <td class="col-nama"><strong><?= esc($row['nama_karyawan']) ?></strong></td>
                        <td class="col-nik"><?= esc($row['nik_karyawan']) ?></td>
                        <td class="col-jabatan"><?= esc($row['jabatan']) ?></td>
                        <td class="col-status">
                            <?php if($row['status_gadget'] == 'Ada'): ?>
                                <span class="status-ada">✓ Ada</span>
                            <?php else: ?>
                                <span class="status-tidak">✗ Tidak</span>
                            <?php endif; ?>
                        </td>
                        <td class="col-detail">
                            <?php if($row['status_gadget'] == 'Ada'): ?>
                                <span class="imei-code"><?= esc($row['imei']) ?></span>
                            <?php else: ?>
                                <span style="color:#6b7280;font-style:italic;"><?= esc($row['keterangan']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <?php if($row['verifikasi'] == 'cocok'): ?>
                                <span class="verif-badge verif-cocok">✓ Cocok</span>
                            <?php elseif($row['verifikasi'] == 'tidak_cocok'): ?>
                                <span class="verif-badge verif-tidak">✗ Tidak Cocok</span>
                                <?php if($row['pemilik_master']): ?>
                                    <br><span style="font-size:8px;color:#991b1b;">Master: <?= esc($row['pemilik_master']) ?></span>
                                <?php endif; ?>
                            <?php elseif($row['verifikasi'] == 'not_found'): ?>
                                <span class="verif-badge verif-notfound">! Tidak Terdaftar</span>
                            <?php elseif($row['verifikasi'] == 'no_owner'): ?>
                                <span class="verif-badge verif-noowner">— Tanpa Pemilik</span>
                            <?php else: ?>
                                <span class="verif-na">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total Row -->
        <?php if(!empty($inputs)): ?>
        <div style="font-size:10px;color:#6b7280;text-align:right;margin-bottom:4px;">
            Total: <strong><?= $total ?></strong> data
            &nbsp;|&nbsp; Ada Gadget: <strong style="color:#059669"><?= $ada ?></strong>
            &nbsp;|&nbsp; Tidak Ada: <strong style="color:#dc2626"><?= $tidak ?></strong>
            &nbsp;|&nbsp; IMEI Cocok: <strong style="color:#065f46"><?= $cocok ?></strong>
            <?php if($tidak_cocok > 0): ?>
                &nbsp;|&nbsp; Tidak Cocok: <strong style="color:#991b1b"><?= $tidak_cocok ?></strong>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td style="width:40%;">
                        <div class="sig-title">Diketahui Oleh,</div>
                        <br><br><br><br>
                        <div class="sig-name">( ............................. )</div>
                        <div class="sig-role">Asisten Afdeling</div>
                    </td>
                    <td style="width:20%;"></td>
                    <td style="width:40%;">
                        <div class="sig-title">Dibuat Oleh,</div>
                        <br><br><br><br>
                        <div class="sig-name">( <?= esc($mandor['nama_lengkap']) ?> )</div>
                        <div class="sig-role">Mandor <?= esc($mandor['tipe_mandor'] ?? '') ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="report-footer">
            <span><?= esc($pt_nama) ?></span>
            <span><?= date('d-m-Y H:i') ?> WIB</span>
        </div>

    </div>

</body>
</html>
