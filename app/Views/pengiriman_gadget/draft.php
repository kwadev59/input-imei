<?php $page_title = 'Draft Kirim Gadget'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'buat_pengiriman']) ?>

<style>
/* === DRAFT PAGE SPECIFIC STYLES === */
.step-badge {
    width: 28px; height: 28px; border-radius: 50%; display: inline-flex;
    align-items: center; justify-content: center; font-weight: 800;
    font-size: 0.75rem; flex-shrink: 0;
}
.step-badge.active  { background: var(--primary); color: #fff; box-shadow: 0 4px 10px rgba(99,102,241,0.4); }
.step-badge.pending { background: #e2e8f0; color: var(--text-muted); }
.step-connector { width: 2px; height: 20px; background: #e2e8f0; margin: 3px auto; }

/* Gadget Info Box */
#gadgetInfoBox {
    background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(59,130,246,0.06));
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 12px; padding: 14px 16px; margin-bottom: 16px;
}
#gadgetInfoBox table td { padding: 3px 4px; font-size: 0.82rem; }

/* Temp List */
#tempListWrap { min-height: 120px; }
.temp-item {
    display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9; animation: slideIn 0.25s ease;
    transition: background 0.2s;
}
.temp-item:last-child { border-bottom: none; }
.temp-item:hover { background: #f8fafc; }
@keyframes slideIn {
    from { opacity:0; transform: translateY(-6px); }
    to   { opacity:1; transform: translateY(0); }
}
.temp-item-num {
    width: 26px; height: 26px; border-radius: 8px;
    background: var(--primary-soft); color: var(--primary);
    font-weight: 800; font-size: 0.75rem; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; margin-top: 2px;
}
.temp-item-body { flex: 1; min-width: 0; }
.temp-item-imei { font-weight: 700; font-size: 0.9rem; color: var(--text-primary); }
.temp-item-info { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }
.temp-item-kerusakan {
    font-size: 0.8rem; color: #475569; margin-top: 6px;
    display: flex; align-items: flex-start; gap: 6px;
}
.no-temp-state {
    text-align: center; padding: 40px 20px;
    color: var(--text-muted);
}
.no-temp-state i { font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 10px; }

/* Editable kerusakan in temp list */
.edit-ker-btn {
    background: none; border: none; padding: 2px 6px; border-radius: 6px;
    color: var(--text-muted); cursor: pointer; transition: all 0.2s; font-size: 0.8rem;
}
.edit-ker-btn:hover { background: var(--primary-soft); color: var(--primary); }

/* Draft Tersimpan - inline edit */
.inline-edit-kerusakan {
    display: none; gap: 6px; align-items: center; margin-top: 6px;
}
.inline-edit-kerusakan.show { display: flex; }
.inline-edit-kerusakan textarea {
    flex: 1; border: 1px solid var(--primary); border-radius: 8px;
    font-size: 0.8rem; padding: 6px 10px; resize: vertical; min-height: 60px;
    font-family: inherit; outline: none;
}
.saving-spinner { display: none; }

/* Count badge */
.count-badge {
    background: var(--primary); color: #fff; border-radius: 20px;
    padding: 2px 8px; font-size: 0.7rem; font-weight: 700; margin-left: 6px;
}

/* Save bar */
#saveDraftBar {
    display: none; position: sticky; bottom: 0; left: 0; right: 0;
    z-index: 100; animation: slideUpBar 0.3s ease;
}
@keyframes slideUpBar {
    from { opacity:0; transform: translateY(10px); }
    to   { opacity:1; transform: translateY(0); }
}
.save-bar-inner {
    background: #fff; border: 1px solid var(--card-border);
    border-radius: 16px; padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    box-shadow: 0 -4px 24px rgba(0,0,0,0.08);
    flex-wrap: wrap;
}
.save-bar-info { font-size: 0.875rem; color: var(--text-secondary); font-weight: 500; }
.save-bar-info strong { color: var(--primary); }

/* Pulsing dot for unsaved */
.unsaved-dot {
    width: 8px; height: 8px; border-radius: 50%; background: var(--warning);
    display: inline-block; margin-right: 6px;
    animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.7); }
}

/* Section divider */
.section-gap { margin-top: 28px; }

/* Accordion style for saved drafts */
.saved-draft-row td { vertical-align: middle; }
.edit-saved-area { background: #f8fafc; }

/* ---- POS Print Button (browser, flow lama) ---- */
#btnPrintPOS {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff; border: none; border-radius: 8px;
    padding: 5px 12px; font-size: 0.78rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 5px;
    cursor: pointer; transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
#btnPrintPOS:hover { background: linear-gradient(135deg,#1e293b,#334155); transform: scale(1.03); }
#btnPrintPOS i { font-size: 0.9rem; }

/* ---- PRINT ONLINE Button (ESC/POS via server, flow baru) ---- */
#btnPrintOnline {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff; border: none; border-radius: 8px;
    padding: 5px 12px; font-size: 0.78rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 5px;
    cursor: pointer; transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(14,165,233,0.35);
}
#btnPrintOnline:hover { background: linear-gradient(135deg,#0284c7,#0369a1); transform: scale(1.03); }
#btnPrintOnline:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
#btnPrintOnline i { font-size: 0.9rem; }

/* Status badge untuk online print di temp list */
.print-status-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.68rem; font-weight: 700; padding: 2px 8px;
    border-radius: 20px; margin-top: 4px;
}
.pst-queued     { background: #dbeafe; color: #1d4ed8; }
.pst-processing { background: #fef9c3; color: #92400e; }
.pst-done       { background: #dcfce7; color: #166534; }
.pst-failed     { background: #fee2e2; color: #991b1b; }
.pst-none       { background: #f1f5f9; color: #64748b; }

/* ---- POS Print Receipt (hidden, for print only) ---- */
#posReceipt {
    display: none;
}

/* === POS 80mm Print Media === */
@media print {
    body > *:not(#posPrintArea) { display: none !important; }
    #posPrintArea {
        display: block !important;
        width: 72mm !important; /* 80mm roll minus margins */
        margin: 0 !important;
        padding: 0 !important;
        font-family: 'Courier New', Courier, monospace;
        font-size: 9pt;
        color: #000;
        page-break-after: avoid;
    }
    #posPrintArea * { color: #000 !important; }
    .pos-logo   { text-align: center; font-weight: bold; font-size: 11pt; margin-bottom: 2mm; letter-spacing: 1px; }
    .pos-sub    { text-align: center; font-size: 7.5pt; margin-bottom: 1mm; }
    .pos-div    { border-top: 1px dashed #000; margin: 2mm 0; }
    .pos-title  { text-align: center; font-weight: bold; font-size: 10pt; margin: 1mm 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .pos-row    { display: flex; justify-content: space-between; font-size: 8.5pt; margin: 0.6mm 0; gap: 4px; }
    .pos-row .label { color: #555; flex-shrink: 0; white-space: nowrap; min-width: 22mm; }
    .pos-row .val   { font-weight: bold; text-align: right; word-break: break-all; }
    .pos-imei   { text-align: center; font-size: 12pt; font-weight: bold; letter-spacing: 2px; margin: 2mm 0; border: 1px solid #000; padding: 1mm 2mm; }
    .pos-foot   { text-align: center; font-size: 7pt; color: #666; margin-top: 2mm; }
    .pos-barcode-label { text-align: center; font-size: 7pt; }
    @page { size: 80mm auto; margin: 3mm 3mm; }
}
</style>

<div class="content">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-file-earmark-plus me-2"></i>Draft Kirim Gadget</h4>
            <div class="header-sub">Input unit yang dikirim sebagai draft sebelum dijadikan BASTE.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('pengiriman-gadget') ?>" class="btn btn-outline-secondary btn-action">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Flash notification area for JS-triggered messages -->
    <div id="jsAlert" class="d-none mb-3"></div>

    <!-- ====== ROW 1: Input + Daftar Sementara ====== -->
    <div class="row g-4">

        <!-- Form Input IMEI -->
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="data-card-header" style="background: linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border-radius: 16px 16px 0 0;">
                    <h5 class="mb-0" style="color:#fff;">
                        <i class="bi bi-upc-scan me-2"></i>Input IMEI
                    </h5>
                    <span class="badge" style="background:rgba(255,255,255,0.2); color:#fff; font-size:0.7rem;">
                        <i class="bi bi-1-circle me-1"></i>Langkah 1
                    </span>
                </div>
                <div class="card-body p-4">

                    <!-- IMEI Input -->
                    <div class="form-group mb-3">
                        <label for="imeiInput" class="form-label fw-bold">Scan / Input IMEI <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" id="imeiInput"
                                   placeholder="15 digit IMEI..." autofocus autocomplete="off"
                                   style="font-family: monospace; letter-spacing: 1px;">
                            <button class="btn btn-primary px-3" type="button" id="btnCheckImei">
                                <i class="bi bi-search"></i> Cek
                            </button>
                        </div>
                        <small id="imeiMsg" class="form-text mt-1 d-block"></small>
                    </div>

                    <!-- Gadget Info -->
                    <div id="gadgetInfoBox" class="d-none">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-phone-fill text-primary"></i>
                                <span class="fw-bold text-primary" style="font-size:0.85rem;">Data Master Gadget</span>
                                <span id="infoStatusBadge" class="badge"></span>
                            </div>
                            <!-- Tombol Print: dua opsi, flow lama dipertahankan -->
                            <div class="d-flex gap-1">
                                <!-- Flow LAMA: browser window.print() -->
                                <button type="button" id="btnPrintPOS" title="Print ke printer lokal (browser)" onclick="printPOS()">
                                    <i class="bi bi-printer-fill"></i> Print
                                </button>
                                <!-- Flow BARU: kirim ke server → ESC/POS agent -->
                                <button type="button" id="btnPrintOnline" title="Print Online via VPS Agent" onclick="sendToOnlinePrint()" disabled>
                                    <i class="bi bi-cloud-upload-fill"></i> Online
                                </button>
                            </div>
                        </div>
                        <table class="w-100">
                            <tr><td class="text-muted" style="width:40%">Aplikasi</td><td style="width:5%">:</td><td id="iAplikasi" class="fw-bold">-</td></tr>
                            <tr><td class="text-muted">PT / AFD</td><td>:</td><td id="iPtAfd" class="fw-bold">-</td></tr>
                            <tr><td class="text-muted">Pengguna</td><td>:</td><td id="iPengguna" class="fw-bold">-</td></tr>
                            <tr><td class="text-muted">Tipe Asset</td><td>:</td><td id="iTipeAsset" class="fw-bold">-</td></tr>
                            <tr><td class="text-muted">Group</td><td>:</td><td id="iGroupAsset" class="fw-bold">-</td></tr>
                            <tr><td class="text-muted">Part Asset</td><td>:</td><td id="iPartAsset" class="fw-bold">-</td></tr>
                            <tr><td class="text-muted">Jumlah</td><td>:</td><td id="iJumlah" class="fw-bold">-</td></tr>
                            <tr><td class="text-muted">Asal</td><td>:</td><td id="iAsal" class="fw-bold">-</td></tr>
                        </table>
                    </div>

                    <!-- Kerusakan Input -->
                    <div class="form-group mb-3" id="kerusakanGroup" style="display:none;">
                        <label for="kerusakanInput" class="form-label fw-bold">
                            Keterangan / Kerusakan <span class="text-danger">*</span>
                        </label>
                        <div class="mb-2 d-flex flex-wrap gap-1" id="shortcuts">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill scut">Mati Total</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill scut">Baterai Kembung</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill scut">Layar Rusak</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill scut">Port Charger Rusak</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill scut">GPS Tidak Normal</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill scut">Normal / Baik</button>
                        </div>
                        <textarea class="form-control" id="kerusakanInput" rows="3"
                            placeholder="Jelaskan kondisi atau kerusakan gadget..."></textarea>
                    </div>

                    <!-- Action Button -->
                    <button type="button" class="btn btn-success w-100 py-2 fw-bold" id="btnAddToList" disabled>
                        <i class="bi bi-floppy me-1"></i> Simpan
                    </button>
                    <div class="text-center mt-2">
                        <small class="text-muted">Setelah disimpan, data masuk ke <strong>Daftar Sementara</strong> dan struk akan dicetak.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Sementara (Client-Side) -->
        <div class="col-lg-8">
            <div class="data-card h-100 d-flex flex-column">
                <div class="data-card-header" style="position:sticky; top:0; z-index:10; background:#fff; border-radius: 16px 16px 0 0;">
                    <div>
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-list me-2"></i>Daftar Sementara
                            <span class="count-badge" id="tempListCount">0</span>
                        </h5>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:3px;">
                            <span class="unsaved-dot" id="unsavedDot" style="display:none;"></span>
                            <span id="unsavedLabel"></span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnClearAll" style="display:none;">
                            <i class="bi bi-trash me-1"></i>Bersihkan
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="btnSaveDraft" style="display:none;">
                            <i class="bi bi-floppy me-1"></i>Simpan Draft
                        </button>
                    </div>
                </div>

                <!-- Temporary list -->
                <div id="tempListWrap" class="flex-grow-1">
                    <div class="no-temp-state" id="noTempState">
                        <i class="bi bi-clipboard-plus"></i>
                        <p class="mb-0">Belum ada item.<br><small>Input IMEI di sebelah kiri, lalu klik <strong>Tambahkan ke Daftar</strong>.</small></p>
                    </div>
                    <div id="tempListBody"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Bar (Sticky) -->
    <div id="saveDraftBar" class="section-gap">
        <div class="save-bar-inner">
            <div class="save-bar-info">
                <span class="unsaved-dot"></span>
                Ada <strong id="barCount">0</strong> item belum disimpan ke draft.
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="barBtnClear">
                    <i class="bi bi-x-circle me-1"></i>Bersihkan
                </button>
                <button type="button" class="btn btn-primary" id="barBtnSave">
                    <i class="bi bi-floppy me-1"></i>Simpan Semua ke Draft
                </button>
            </div>
        </div>
    </div>

    <!-- ====== ROW 2: Daftar Draft Tersimpan ====== -->
    <div class="section-gap">
        <div class="data-card">
            <div class="data-card-header">
                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-list-task me-2"></i>Daftar Draft Tersimpan
                        <span class="badge badge-soft-primary ms-1"><?= count($drafts) ?> item</span>
                    </h5>
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:3px;">Item yang sudah tersimpan di server. Bisa diedit atau dihapus sebelum dijadikan BASTE.</div>
                </div>
                <?php if (!empty($drafts)): ?>
                <form action="<?= base_url('pengiriman-gadget/submit-baste') ?>" method="post"
                      onsubmit="return confirm('Jadikan semua draft ini sebagai BASTE?');">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check2-all me-1"></i>Jadikan BASTE
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <?php if (empty($drafts)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                    <h6>Belum Ada Draft Tersimpan</h6>
                    <p>Tambahkan item dari form di atas, lalu klik <strong>Simpan Draft</strong>.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTable">
                        <thead class="table-light">
                            <tr>
                                <th width="4%" class="text-center">No</th>
                                <th width="18%">IMEI</th>
                                <th width="30%">Info Gadget</th>
                                <th width="30%">Keterangan</th>
                                <th width="12%" class="text-center">Waktu</th>
                                <th width="6%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($drafts as $index => $row): ?>
                                <!-- Data Row -->
                                <tr class="saved-draft-row" id="sdr-<?= $row['id'] ?>">
                                    <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td>
                                        <span class="fw-bold font-monospace" style="font-size:0.82rem;"><?= esc($row['imei']) ?></span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="fw-bold"><i class="bi bi-person me-1 text-primary"></i><?= esc($row['nama_pengguna'] ?? '-') ?></div>
                                            <div class="text-muted"><i class="bi bi-hash me-1"></i><?= esc($row['npk_pengguna'] ?? '-') ?></div>
                                            <div class="mt-1">
                                                <span class="badge badge-soft-primary"><?= esc($row['aplikasi'] ?? '-') ?></span>
                                                <span class="badge badge-soft-info ms-1"><?= esc($row['pt'] ?? '-') ?> / <?= esc($row['afd'] ?? '-') ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Display mode -->
                                        <div id="ker-display-<?= $row['id'] ?>" class="d-flex align-items-start gap-1">
                                            <span class="text-secondary small flex-grow-1" id="ker-text-<?= $row['id'] ?>"><?= esc($row['kerusakan']) ?></span>
                                            <button class="edit-ker-btn" title="Edit keterangan"
                                                    onclick="showEditKer(<?= $row['id'] ?>, '<?= esc($row['kerusakan'], 'js') ?>')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </div>
                                        <!-- Edit mode (hidden inline) -->
                                        <div class="inline-edit-kerusakan" id="ker-edit-<?= $row['id'] ?>">
                                            <textarea rows="2" id="ker-textarea-<?= $row['id'] ?>"><?= esc($row['kerusakan']) ?></textarea>
                                            <div class="d-flex flex-column gap-1">
                                                <button class="btn btn-success btn-sm px-2 py-1" onclick="saveKer(<?= $row['id'] ?>)" title="Simpan">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="btn btn-secondary btn-sm px-2 py-1" onclick="cancelEditKer(<?= $row['id'] ?>)" title="Batal">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-soft-warning" style="font-size:0.7rem; white-space:nowrap;">
                                            <?= date('d/m H:i', strtotime($row['created_at'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('pengiriman-gadget/delete-draft/'.$row['id']) ?>"
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Hapus IMEI <?= esc($row['imei'], 'js') ?> dari draft?')"
                                           title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /content -->

<!-- Edit Modal for Temp List Item -->
<div class="modal fade" id="editTempModal" tabindex="-1" aria-labelledby="editTempModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="editTempModalLabel"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Keterangan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editTempIdx">
                <label class="form-label fw-bold" style="font-size:0.85rem;">IMEI</label>
                <p id="editTempImeiDisplay" class="fw-bold font-monospace mb-3 text-primary"></p>
                <label class="form-label fw-bold" style="font-size:0.85rem;">Keterangan / Kerusakan <span class="text-danger">*</span></label>
                <div class="mb-2 d-flex flex-wrap gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill modal-scut">Mati Total</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill modal-scut">Baterai Kembung</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill modal-scut">Layar Rusak</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill modal-scut">Port Charger Rusak</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill modal-scut">GPS Tidak Normal</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill modal-scut">Normal / Baik</button>
                </div>
                <textarea class="form-control" id="editTempKerusakan" rows="4"></textarea>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSaveEditTemp">
                    <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ====== HIDDEN POS PRINT AREA (80mm) ====== -->
<div id="posPrintArea">
    <div class="pos-logo">ASTRA AGRO</div>
    <div class="pos-sub">Sistem Distribusi Gadget</div>
    <div class="pos-div"></div>
    <div class="pos-title">LABEL GADGET</div>
    <div class="pos-div"></div>
    <!-- IMEI besar -->
    <div class="pos-imei" id="p-imei"></div>
    <div class="pos-div"></div>
    <!-- Data Gadget -->
    <div class="pos-row"><span class="label">Aplikasi</span><span class="val" id="p-aplikasi"></span></div>
    <div class="pos-row"><span class="label">PT / AFD</span><span class="val" id="p-ptafd"></span></div>
    <div class="pos-row"><span class="label">NPK Pengguna</span><span class="val" id="p-npk"></span></div>
    <div class="pos-row"><span class="label">Nama</span><span class="val" id="p-nama"></span></div>
    <div class="pos-row"><span class="label">Pos/Jabatan</span><span class="val" id="p-pos"></span></div>
    <div class="pos-div"></div>
    <div class="pos-row"><span class="label">Group Asset</span><span class="val" id="p-group"></span></div>
    <div class="pos-row"><span class="label">Tipe Asset</span><span class="val" id="p-tipe"></span></div>
    <div class="pos-row"><span class="label">Part Asset</span><span class="val" id="p-part"></span></div>
    <div class="pos-row"><span class="label">Jumlah</span><span class="val" id="p-jumlah"></span></div>
    <div class="pos-row"><span class="label">Asal</span><span class="val" id="p-asal"></span></div>
    <div class="pos-div"></div>
    <div class="pos-row"><span class="label">Status</span><span class="val" id="p-status"></span></div>
    <div class="pos-div"></div>
    <!-- Keterangan / Kerusakan -->
    <div class="pos-row" style="align-items: flex-start;">
        <span class="label">Keterangan</span>
        <span class="val" id="p-kerusakan" style="text-align: left; white-space: pre-wrap;"></span>
    </div>
    <div class="pos-div"></div>
    <div class="pos-foot" id="p-waktu"></div>
    <div class="pos-foot">--- Dicetak oleh Sistem Distribusi ---</div>
</div>

<script>
/* ==========================================
   DRAFT KIRIM GADGET - COMPLETE SCRIPT
   ========================================== */
document.addEventListener('DOMContentLoaded', function() {

    // -------- STATE --------
    let tempList    = []; // [{imei, kerusakan, gadgetData}]
    let currentIMEI = null;
    let currentGadget = null;

    // -------- DOM REFS --------
    const imeiInput      = document.getElementById('imeiInput');
    const btn_check      = document.getElementById('btnCheckImei');
    const imeiMsg        = document.getElementById('imeiMsg');
    const gadgetInfoBox  = document.getElementById('gadgetInfoBox');
    const kerusakanGrp   = document.getElementById('kerusakanGroup');
    const kerusakanInput = document.getElementById('kerusakanInput');
    const btn_add        = document.getElementById('btnAddToList');
    const tempListBody   = document.getElementById('tempListBody');
    const noTempState    = document.getElementById('noTempState');
    const tempListCount  = document.getElementById('tempListCount');
    const btn_save       = document.getElementById('btnSaveDraft');
    const btn_clear      = document.getElementById('btnClearAll');
    const saveDraftBar   = document.getElementById('saveDraftBar');
    const barBtnSave     = document.getElementById('barBtnSave');
    const barBtnClear    = document.getElementById('barBtnClear');
    const barCount       = document.getElementById('barCount');
    const unsavedDot     = document.getElementById('unsavedDot');
    const unsavedLabel   = document.getElementById('unsavedLabel');
    const jsAlert        = document.getElementById('jsAlert');

    // -------- CHECK IMEI --------
    btn_check.addEventListener('click', checkImei);
    imeiInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); checkImei(); }
    });

    function checkImei() {
        const val = imeiInput.value.trim();
        if (!val) { showMsg('Masukkan IMEI terlebih dahulu.', 'danger'); return; }

        // Check if already in temp list
        if (tempList.some(x => x.imei === val)) {
            showMsg('⚠ IMEI ini sudah ada di Daftar Sementara.', 'warning');
            return;
        }

        btn_check.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn_check.disabled = true;
        gadgetInfoBox.classList.add('d-none');
        kerusakanGrp.style.display = 'none';
        btn_add.disabled = true;
        currentIMEI = null;
        currentGadget = null;

        const fd = new FormData();
        fd.append('imei', val);

        fetch('<?= base_url("pengiriman-gadget/check-imei") ?>', {
            method: 'POST', body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            btn_check.innerHTML = '<i class="bi bi-search"></i> Cek';
            btn_check.disabled = false;

            if (data.status === 'success') {
                const g = data.data;
                currentIMEI   = val;
                // Aktifkan tombol PRINT ONLINE setelah IMEI valid ditemukan
                document.getElementById('btnPrintOnline').disabled = false;
                currentGadget = g;

                document.getElementById('iAplikasi').textContent  = g.aplikasi || '-';
                document.getElementById('iPtAfd').textContent     = `${g.pt || '-'} / ${g.afd || '-'}`;
                const npk = g.npk_pengguna ? ` (${g.npk_pengguna})` : '';
                document.getElementById('iPengguna').textContent  = `${g.nama_pengguna || '-'}${npk}`;
                document.getElementById('iTipeAsset').textContent = g.tipe_asset || '-';
                document.getElementById('iGroupAsset').textContent= g.group_asset || '-';
                document.getElementById('iPartAsset').textContent = g.part_asset || '-';
                document.getElementById('iJumlah').textContent    = g.jumlah || '-';
                document.getElementById('iAsal').textContent      = g.asal_desc || g.asal || '-';

                const badgeEl = document.getElementById('infoStatusBadge');
                const s = g.status_desc || '-';
                badgeEl.textContent = s;
                badgeEl.className = 'badge ms-auto ' + (s === 'NORMAL' ? 'badge-soft-success' : s === 'RUSAK' ? 'bg-danger text-white' : 'badge-soft-warning');

                gadgetInfoBox.classList.remove('d-none');
                kerusakanGrp.style.display = 'block';
                btn_add.disabled = false;
                showMsg('✔ IMEI ditemukan!', 'success');
                kerusakanInput.focus();
            } else {
                showMsg('✘ ' + data.message, 'danger');
                btn_add.disabled = true;
            }
        })
        .catch(() => {
            btn_check.innerHTML = '<i class="bi bi-search"></i> Cek';
            btn_check.disabled = false;
            showMsg('Terjadi kesalahan. Coba lagi.', 'danger');
        });
    }

    function showMsg(text, type) {
        imeiMsg.textContent = text;
        imeiMsg.className = `form-text mt-1 d-block text-${type} fw-bold`;
    }

    // -------- SHORTCUTS --------
    document.querySelectorAll('.scut').forEach(btn => {
        btn.addEventListener('click', function() {
            const cur = kerusakanInput.value.trim();
            const t   = this.textContent;
            kerusakanInput.value = cur ? (cur.includes(t) ? cur : cur + ', ' + t) : t;
            kerusakanInput.focus();
        });
    });

    document.querySelectorAll('.modal-scut').forEach(btn => {
        btn.addEventListener('click', function() {
            const ta  = document.getElementById('editTempKerusakan');
            const cur = ta.value.trim();
            const t   = this.textContent;
            ta.value = cur ? (cur.includes(t) ? cur : cur + ', ' + t) : t;
            ta.focus();
        });
    });

    // -------- ADD TO TEMP LIST --------
    btn_add.addEventListener('click', function() {
        if (!currentIMEI) return;
        const kerusakan = kerusakanInput.value.trim();
        if (!kerusakan) {
            kerusakanInput.classList.add('is-invalid');
            kerusakanInput.focus();
            setTimeout(() => kerusakanInput.classList.remove('is-invalid'), 2000);
            return;
        }

        // Buat item baru (tambah field printJobId untuk tracking status online print)
        const newItem = {
            imei: currentIMEI,
            kerusakan: kerusakan,
            gadgetData: { ...currentGadget },
            printJobId: null,       // akan diisi setelah sendToOnlinePrint()
            printStatus: 'none'    // none | queued | processing | done | failed
        };
        tempList.push(newItem);
        renderTempList();
        updateSaveBar();

        // ⚠️ FLOW LAMA: Hapus auto-print agar tidak terlalu agresif.
        // User bisa klik tombol Print atau Online secara manual.
        // Kalau ingin auto-print browser, uncomment baris di bawah:
        // printPOSForItem(newItem);

        // Reset form
        imeiInput.value = '';
        kerusakanInput.value = '';
        gadgetInfoBox.classList.add('d-none');
        kerusakanGrp.style.display = 'none';
        btn_add.disabled = true;
        document.getElementById('btnPrintOnline').disabled = true;
        imeiMsg.textContent = '';
        currentIMEI = null;
        currentGadget = null;
        imeiInput.focus();

        const idx = tempList.length - 1;
        showJsAlert(
            `<i class="bi bi-check-circle-fill me-2"></i>
             IMEI <strong>${tempList[idx].imei}</strong> ditambahkan.
             <button class="btn btn-sm btn-dark ms-2 py-0" onclick="printPOSForItem(tempList[${idx}])">
                 <i class="bi bi-printer me-1"></i>Print
             </button>
             <button class="btn btn-sm btn-info ms-1 py-0 text-white" onclick="sendToOnlinePrintIdx(${idx})">
                 <i class="bi bi-cloud-upload me-1"></i>Online
             </button>`,
            'success'
        );
    });

    // -------- RENDER TEMP LIST --------
    function renderTempList() {
        tempListCount.textContent = tempList.length;

        if (tempList.length === 0) {
            noTempState.style.display = 'block';
            tempListBody.innerHTML = '';
            btn_save.style.display = 'none';
            btn_clear.style.display = 'none';
            return;
        }

        noTempState.style.display = 'none';
        btn_save.style.display = '';
        btn_clear.style.display = '';

        tempListBody.innerHTML = tempList.map((item, idx) => {
            const g = item.gadgetData;
            const statusCls = (g.status_desc === 'NORMAL') ? 'badge-soft-success' : 'badge-soft-danger';
            return `
            <div class="temp-item" id="ti-${idx}">
                <div class="temp-item-num">${idx + 1}</div>
                <div class="temp-item-body">
                    <div class="temp-item-imei font-monospace">${item.imei}</div>
                    <div class="temp-item-info">
                        <i class="bi bi-person me-1"></i>${g.nama_pengguna || '-'}
                        <span class="mx-1">·</span>
                        <span class="badge ${statusCls}" style="font-size:0.65rem;">${g.status_desc || '-'}</span>
                        <span class="mx-1">·</span>${g.aplikasi || ''} / ${g.pt || ''}-${g.afd || ''}
                    </div>
                    <div class="temp-item-kerusakan">
                        <i class="bi bi-card-text text-muted mt-1"></i>
                        <span id="ker-tmp-${idx}" class="flex-grow-1">${escHtml(item.kerusakan)}</span>
                        <button class="edit-ker-btn" title="Edit" onclick="openEditTempModal(${idx})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </div>
                </div>
                <button class="btn btn-outline-danger btn-sm ms-auto flex-shrink-0" style="height:32px; align-self:center;" onclick="removeTempItem(${idx})" title="Hapus dari daftar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>`;
        }).join('');
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // -------- REMOVE TEMP ITEM --------
    window.removeTempItem = function(idx) {
        tempList.splice(idx, 1);
        renderTempList();
        updateSaveBar();
    };

    // -------- EDIT TEMP MODAL --------
    window.openEditTempModal = function(idx) {
        const item = tempList[idx];
        document.getElementById('editTempIdx').value         = idx;
        document.getElementById('editTempImeiDisplay').textContent = item.imei;
        document.getElementById('editTempKerusakan').value   = item.kerusakan;
        const m = new bootstrap.Modal(document.getElementById('editTempModal'));
        m.show();
    };

    document.getElementById('btnSaveEditTemp').addEventListener('click', function() {
        const idx      = parseInt(document.getElementById('editTempIdx').value);
        const newKer   = document.getElementById('editTempKerusakan').value.trim();
        if (!newKer) return;
        tempList[idx].kerusakan = newKer;
        renderTempList();
        bootstrap.Modal.getInstance(document.getElementById('editTempModal')).hide();
    });

    // -------- CLEAR ALL --------
    function clearAll() {
        if (tempList.length > 0 && !confirm('Hapus semua item dari daftar sementara?')) return;
        tempList = [];
        renderTempList();
        updateSaveBar();
    }
    btn_clear.addEventListener('click', clearAll);
    barBtnClear.addEventListener('click', clearAll);

    // -------- UPDATE SAVE BAR --------
    function updateSaveBar() {
        const n = tempList.length;
        barCount.textContent = n;

        if (n > 0) {
            saveDraftBar.style.display = 'block';
            unsavedDot.style.display   = 'inline-block';
            unsavedLabel.textContent   = `${n} item belum disimpan`;
        } else {
            saveDraftBar.style.display = 'none';
            unsavedDot.style.display   = 'none';
            unsavedLabel.textContent   = '';
        }
    }

    // -------- SAVE ALL TO DB --------
    function saveDraftBatch() {
        if (tempList.length === 0) return;

        const payload = tempList.map(x => ({ imei: x.imei, kerusakan: x.kerusakan }));
        const btn = barBtnSave;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
        btn.disabled  = true;
        btn_save.disabled = true;

        const fd = new FormData();
        fd.append('items', JSON.stringify(payload));

        fetch('<?= base_url("pengiriman-gadget/save-draft-batch") ?>', {
            method: 'POST', body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Simpan Semua ke Draft';
            btn.disabled  = false;
            btn_save.disabled = false;

            if (data.status === 'success') {
                showJsAlert(`<i class="bi bi-check-circle-fill me-2"></i>${data.message}`, 'success');
                tempList = [];
                renderTempList();
                updateSaveBar();
                // Reload saved draft section
                setTimeout(() => location.reload(), 1200);
            } else {
                showJsAlert(`<i class="bi bi-exclamation-triangle me-2"></i>${data.message}`, 'danger');
            }
        })
        .catch(() => {
            btn.innerHTML = '<i class="bi bi-floppy me-1"></i>Simpan Semua ke Draft';
            btn.disabled  = false;
            btn_save.disabled = false;
            showJsAlert('Terjadi kesalahan jaringan. Coba lagi.', 'danger');
        });
    }

    btn_save.addEventListener('click', saveDraftBatch);
    barBtnSave.addEventListener('click', saveDraftBatch);

    // -------- JS ALERT --------
    function showJsAlert(html, type) {
        jsAlert.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${html}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        jsAlert.classList.remove('d-none');
        setTimeout(() => {
            jsAlert.classList.add('d-none');
            jsAlert.innerHTML = '';
        }, 5000);
    }

    // -------- INLINE EDIT SAVED DRAFT (DB) --------
    window.showEditKer = function(id, current) {
        document.getElementById('ker-display-' + id).style.display = 'none';
        const editDiv = document.getElementById('ker-edit-' + id);
        editDiv.classList.add('show');
        document.getElementById('ker-textarea-' + id).value = current;
        document.getElementById('ker-textarea-' + id).focus();
    };

    window.cancelEditKer = function(id) {
        document.getElementById('ker-display-' + id).style.display = '';
        document.getElementById('ker-edit-' + id).classList.remove('show');
    };

    window.saveKer = function(id) {
        const newVal = document.getElementById('ker-textarea-' + id).value.trim();
        if (!newVal) { alert('Keterangan tidak boleh kosong.'); return; }

        const saveBtn = document.querySelector(`#ker-edit-${id} .btn-success`);
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        saveBtn.disabled  = true;

        const fd = new FormData();
        fd.append('kerusakan', newVal);

        fetch('<?= base_url("pengiriman-gadget/update-draft-kerusakan/") ?>' + id, {
            method: 'POST', body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            saveBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
            saveBtn.disabled  = false;

            if (data.status === 'success') {
                document.getElementById('ker-text-' + id).textContent = newVal;
                cancelEditKer(id);
                showJsAlert('<i class="bi bi-check-circle-fill me-2"></i>Keterangan berhasil diperbarui.', 'success');
            } else {
                showJsAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
            }
        })
        .catch(() => {
            saveBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
            saveBtn.disabled  = false;
            showJsAlert('Gagal. Coba lagi.', 'danger');
        });
    };

    // ====================================================
    // FLOW LAMA: Print POS via browser window.print()
    // (Tidak diubah, tetap berfungsi persis seperti semula)
    // ====================================================

    window.printPOS = function() {
        if (!currentGadget || !currentIMEI) {
            showJsAlert('<i class="bi bi-exclamation-triangle me-2"></i>Tidak ada data gadget untuk dicetak. Cek IMEI terlebih dahulu.', 'warning');
            return;
        }
        printPOSForItem({ imei: currentIMEI, gadgetData: currentGadget, kerusakan: kerusakanInput.value.trim() });
    };

    // Print for a specific item (dipanggil oleh tombol Print di alert atau tombol manual)
    window.printPOSForItem = function(item) {
        const g = item.gadgetData;
        const imei = item.imei;
        const kerusakan = item.kerusakan || '';

        // Format timestamp lokal
        const now = new Date();
        const pad = n => String(n).padStart(2,'0');
        const tgl = `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

        // Isi template print (#posPrintArea)
        document.getElementById('p-imei').textContent      = 'IMEI: ' + imei;
        document.getElementById('p-aplikasi').textContent  = g.aplikasi      || '-';
        document.getElementById('p-ptafd').textContent     = `${g.pt||'-'} / ${g.afd||'-'}`;
        document.getElementById('p-npk').textContent       = g.npk_pengguna  || '-';
        document.getElementById('p-nama').textContent      = g.nama_pengguna || '-';
        document.getElementById('p-pos').textContent       = g.pos_title      || '-';
        document.getElementById('p-group').textContent     = g.group_asset    || '-';
        document.getElementById('p-tipe').textContent      = g.tipe_asset     || '-';
        document.getElementById('p-part').textContent      = g.part_asset     || '-';
        document.getElementById('p-jumlah').textContent    = g.jumlah         || '-';
        document.getElementById('p-asal').textContent      = g.asal_desc || g.asal || '-';
        document.getElementById('p-status').textContent    = g.status_desc    || '-';
        document.getElementById('p-kerusakan').textContent = kerusakan        || '-';
        document.getElementById('p-waktu').textContent     = `Dicetak: ${tgl}`;

        // Trigger dialog print browser
        window.print();
    };

    // ====================================================
    // FLOW BARU: Print Online via server (ESC/POS Agent)
    // ====================================================

    // Dipanggil tombol manual di #gadgetInfoBox
    window.sendToOnlinePrint = function() {
        if (!currentGadget || !currentIMEI) {
            showJsAlert('<i class="bi bi-exclamation-triangle me-2"></i>Cek IMEI terlebih dahulu sebelum print online.', 'warning');
            return;
        }
        const item = {
            imei: currentIMEI,
            kerusakan: kerusakanInput.value.trim() || 'Tidak ada keterangan',
            gadgetData: { ...currentGadget }
        };
        _submitOnlinePrintJob(item);
    };

    // Dipanggil dari alert setelah item ditambahkan ke temp list (by index)
    window.sendToOnlinePrintIdx = function(idx) {
        if (idx < 0 || idx >= tempList.length) return;
        const item = tempList[idx];
        _submitOnlinePrintJob(item, idx);
    };

    // Fungsi inti: kirim data ke /api/print, update status di UI
    function _submitOnlinePrintJob(item, tempListIdx = -1) {
        const g = item.gadgetData;
        const btn = document.getElementById('btnPrintOnline');

        // Tandai tombol sedang loading
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }

        const payload = {
            imei:          item.imei,
            nama_pengguna: g.nama_pengguna  || '',
            npk_pengguna:  g.npk_pengguna   || '',
            aplikasi:      g.aplikasi        || '',
            pt:            g.pt              || '',
            afd:           g.afd             || '',
            pos_title:     g.pos_title       || '',
            group_asset:   g.group_asset     || '',
            tipe_asset:    g.tipe_asset      || '',
            part_asset:    g.part_asset      || '',
            jumlah:        g.jumlah          || '',
            asal_desc:     g.asal_desc || g.asal || '',
            status_desc:   g.status_desc     || '',
            kerusakan:     item.kerusakan    || '',
        };

        fetch('<?= base_url("api/print") ?>', {
            method:  'POST',
            headers: {
                'Content-Type':     'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(data => {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload-fill"></i> Online'; }

            if (data.status === 'queued') {
                showJsAlert(
                    `<i class="bi bi-check-circle-fill me-2"></i>
                     Job print <strong>#${data.job_id}</strong> masuk antrian. Akan dicetak oleh agent otomatis.`,
                    'success'
                );
                // Update status di tempList jika dipanggil dari sana
                if (tempListIdx >= 0 && tempList[tempListIdx]) {
                    tempList[tempListIdx].printJobId = data.job_id;
                    tempList[tempListIdx].printStatus = 'queued';
                    renderTempList();
                }
            } else if (data.status === 'duplicate') {
                showJsAlert(
                    `<i class="bi bi-exclamation-circle-fill me-2"></i>
                     IMEI ini sudah ada di antrian cetak (Job #${data.job_id}).`,
                    'warning'
                );
            } else {
                showJsAlert(
                    `<i class="bi bi-x-circle-fill me-2"></i>${data.message || 'Gagal mengirim ke antrian.'}`,
                    'danger'
                );
            }
        })
        .catch(err => {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload-fill"></i> Online'; }
            showJsAlert('<i class="bi bi-wifi-off me-2"></i>Gagal terhubung ke server. Periksa koneksi.', 'danger');
            console.error('[PrintOnline] Error:', err);
        });
    }

    // -------- INIT --------
    renderTempList();
    updateSaveBar();
    imeiInput.focus();

    // DataTable for saved drafts (if available)
    if (typeof $ !== 'undefined' && $.fn.DataTable && document.getElementById('dataTable')) {
        $('#dataTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            pageLength: 10,
            order: [[4, 'desc']]
        });
    }
});
</script>

<?= view('partials/admin_footer') ?>
