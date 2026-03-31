<?php $page_title = 'Input Pengajuan'; $active_menu = 'create'; ?>
<?= view('partials/mandor_head', ['page_title' => $page_title]) ?>
<style>
    .status-card { 
        cursor: pointer; border: 2px solid var(--card-border); border-radius: 14px; 
        padding: 1.5rem 1rem; text-align: center; transition: all 0.25s; background: white;
        height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;
        box-shadow: var(--card-shadow);
    }
    .status-card:hover { border-color: var(--primary); background: var(--primary-soft); }
    .status-card.active.border-success { border-color: var(--success) !important; background: var(--success-soft) !important; }
    .status-card.active.border-danger { border-color: var(--danger) !important; background: var(--danger-soft) !important; }
    .status-icon { font-size: 2rem; display: block; margin-bottom: 0.5rem; }
    .fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
    .fade-enter-from, .fade-leave-to { opacity: 0; }
    [v-cloak] { display: none; }
    
    /* Checklist Items */
    .checklist-item {
        border: 1px solid var(--card-border) !important;
        border-radius: 12px !important;
        margin-bottom: 8px !important;
        transition: all 0.2s ease;
        border-left: 4px solid var(--warning) !important;
        background: var(--card-bg);
    }
    .checklist-item:hover, .checklist-item:active {
        background: #f8fafc;
        border-color: var(--primary) !important;
        border-left-color: var(--primary) !important;
    }
    
    .done-item {
        border: 1px solid var(--card-border) !important;
        border-radius: 12px !important;
        margin-bottom: 6px !important;
        border-left: 4px solid var(--success) !important;
        background: var(--card-bg);
    }

    /* FAB */
    .fab { 
        position: fixed; bottom: 20px; right: 20px; z-index: 1000; 
        width: 50px; height: 50px; border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        box-shadow: 0 4px 14px rgba(2,132,199,0.35); 
    }

    /* Search input in card header */
    .search-input-wrap {
        position: relative;
    }
    .search-input-wrap .bi-search {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.85rem;
    }
    .search-input-wrap input {
        padding-left: 36px;
        border-radius: 10px;
        border: 1px solid var(--card-border);
        background: #f8fafc;
    }
    .search-input-wrap input:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-soft);
    }

    /* Modal on fullscreen mobile */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    @media (max-width: 575.98px) {
        .modal-fullscreen-sm-down .modal-content {
            border-radius: 0;
        }
    }

    /* Alert in modal */
    .karyawan-info-bar {
        background: var(--primary-soft);
        border-radius: 12px;
        padding: 16px;
        border: 1px solid rgba(2,132,199,0.15);
    }

    /* Data Cocok Animations */
    @keyframes matchPulse {
        0% { transform: scale(0.95); opacity: 0; }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes matchPop {
        0% { transform: scale(0); }
        70% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }
</style>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<?= view('partials/sidebar_mandor', ['active_menu' => $active_menu]) ?>

<div class="content" id="app" v-cloak>
    
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Input Gadget Harian</h4>
            <div class="header-sub">
                Mandor <?= esc($mandor_type ?? 'Panen') ?> 
                · <?= esc(session()->get('afdeling_id')) ?> 
                <?php if(session()->get('pt_site')): ?>
                    · <?= esc(session()->get('pt_site')) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Section: BELUM INPUT -->
    <div class="data-card mb-4">
        <div class="data-card-header" style="position:sticky;top:0;z-index:5;background:var(--card-bg);">
            <h6 class="mb-0" style="color:var(--danger)">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> BELUM DIINPUT ({{ filteredPending.length }})
            </h6>
            <div class="search-input-wrap" style="max-width:260px;width:100%;">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control form-control-sm" v-model="searchQuery" placeholder="Cari Nama / NIK...">
            </div>
        </div>

        <div style="padding: 12px 16px;">
            <!-- Empty State -->
            <div v-if="filteredPending.length === 0" class="empty-state">
                <div v-if="searchQuery">
                    <div class="empty-state-icon"><i class="bi bi-search"></i></div>
                    <p>Tidak ditemukan karyawan dengan pencarian tersebut.</p>
                </div>
                <div v-else>
                    <div style="font-size: 3rem; margin-bottom: 12px;">🎉</div>
                    <p class="fw-bold" style="color:var(--success)">Luar biasa! Semua karyawan sudah diinput.</p>
                </div>
            </div>
            
            <!-- List Item -->
            <button v-for="k in filteredPending" :key="k.id" 
                    class="checklist-item d-flex justify-content-between align-items-center w-100 text-start p-3 border-0"
                    style="cursor:pointer;"
                    @click="openModal(k)">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:var(--warning-soft);color:var(--warning);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:1rem;">
                        {{ k.nama.charAt(0) }}
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:0.95rem;line-height:1.2;">{{ k.nama }}</div>
                        <div class="text-muted" style="font-size:0.78rem;margin-top:2px;">
                            <i class="bi bi-card-heading me-1"></i>{{ k.nik_karyawan }}
                            <span class="mx-1">·</span>
                            {{ k.jabatan }}
                        </div>
                    </div>
                </div>
                <div class="btn btn-sm btn-primary px-3 fw-bold" style="white-space:nowrap;border-radius:8px;">
                    Input <i class="bi bi-arrow-right ms-1"></i>
                </div>
            </button>
        </div>
    </div>

    <!-- Section: SUDAH SELESAI -->
    <div class="data-card mb-5" v-if="filteredDone.length > 0">
        <div class="data-card-header">
            <h6 class="mb-0" style="color:var(--success)">
                <i class="bi bi-check-circle-fill me-1"></i> SUDAH DIINPUT ({{ filteredDone.length }})
            </h6>
        </div>
        <div style="padding: 12px 16px;">
            <div v-for="k in filteredDone" :key="k.id" 
                 class="done-item d-flex justify-content-between align-items-center p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--success-soft);color:var(--success);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-check-lg" style="font-size:1.1rem"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-decoration-line-through" style="opacity:0.6">{{ k.nama }}</div>
                        <small class="d-block mt-1">
                            <span v-if="submissionMap[k.id] && submissionMap[k.id].status_pengajuan === 'Draft'" class="badge badge-soft-warning">DRAFT</span>
                            <span v-else class="badge badge-soft-success">TERKIRIM</span>
                        </small>
                    </div>
                </div>
                <div v-if="submissionMap[k.id] && submissionMap[k.id].input_by == currentUserId">
                    <a :href="'<?= base_url('input/edit/') ?>' + (submissionMap[k.id] ? submissionMap[k.id].submission_id : '')" class="btn btn-sm btn-outline-secondary px-3">
                        Edit
                    </a>
                </div>
                <div v-else class="text-end" style="max-width: 140px;">
                    <small class="text-muted d-block" style="font-size:0.75rem;line-height:1;">Diinput oleh:</small>
                    <span class="badge mt-1" style="background:var(--secondary);color:#fff;white-space:normal;text-align:right;">{{ submissionMap[k.id]?.nama_mandor || 'Orang Lain' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FAB - Scroll to top -->
    <a href="#" class="btn btn-primary fab d-md-none" @click.prevent="scrollToTop">
        <i class="bi bi-arrow-up text-white h4 mb-0"></i>
    </a>

    <!-- Modal Input Form -->
    <div class="modal fade" id="inputModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-phone me-2" style="color:var(--primary)"></i>Input Data Gadget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <form :action="formAction" method="post">
                        <?= csrf_field() ?>

                        <!-- No karyawan selected -->
                        <div class="mb-4 text-center py-4" v-if="!form.karyawan_id">
                             <div class="empty-state-icon" style="display:inline-flex"><i class="bi bi-person"></i></div>
                             <p class="text-muted">Silakan pilih karyawan dari daftar Checklist terlebih dahulu.</p>
                             <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Kembali ke Daftar</button>
                        </div>
                        
                        <!-- Selected Karyawan Display -->
                        <div v-if="form.karyawan_id">
                            <div class="karyawan-info-bar d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <div class="fw-bold" style="font-size:1.1rem;color:var(--text-primary)">{{ selectedKaryawanName }}</div>
                                    <small class="font-monospace text-muted">{{ selectedKaryawanNik }}</small>
                                </div>
                                <input type="hidden" name="karyawan_id" v-model="form.karyawan_id">
                                <button type="button" class="btn btn-sm btn-light fw-bold px-3" style="color:var(--primary)" @click="form.karyawan_id = ''" v-if="!editingId">Ganti</button>
                            </div>

                            <transition name="fade">
                                <div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-muted small text-uppercase mb-3">Status Gadget Karyawan</label>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="status-card h-100" :class="{ active: form.status_gadget === 'Ada', 'border-success': form.status_gadget === 'Ada' }" @click="form.status_gadget = 'Ada'">
                                                    <span class="status-icon">✅</span>
                                                    <span class="fw-bold" style="color:var(--success)">ADA GADGET</span>
                                                    <input type="radio" name="status_gadget" value="Ada" v-model="form.status_gadget" class="d-none">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="status-card h-100" :class="{ active: form.status_gadget === 'Tidak Ada', 'border-danger': form.status_gadget === 'Tidak Ada' }" @click="form.status_gadget = 'Tidak Ada'">
                                                    <span class="status-icon">❌</span>
                                                    <span class="fw-bold" style="color:var(--danger)">TIDAK ADA</span>
                                                    <input type="radio" name="status_gadget" value="Tidak Ada" v-model="form.status_gadget" class="d-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- IMEI Field -->
                                    <div v-if="form.status_gadget === 'Ada'" class="mb-4 p-3" style="background:var(--success-soft);border-radius:14px;border:1px solid rgba(16,185,129,0.2);">
                                        <label class="form-label fw-bold small mb-2" style="color:var(--success)">SCAN / KETIK NOMOR IMEI (15 DIGIT)</label>
                                        <div class="input-group input-group-lg" style="border-radius:12px;overflow:hidden;box-shadow:var(--card-shadow);">
                                            <span class="input-group-text bg-white border-0 ps-3" style="color:var(--success)"><i class="bi bi-upc-scan"></i></span>
                                            <input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="15"
                                                   class="form-control border-0 fw-bold font-monospace" 
                                                   name="imei" 
                                                   v-model="form.imei" 
                                                   @input="sanitizeImei"
                                                   placeholder="Contoh: 86512..." 
                                                   style="letter-spacing: 1px; border-radius: 0 12px 12px 0 !important;"
                                                   :class="{ 'is-invalid': errors.imei, 'is-valid': form.imei.length === 15 }">
                                        </div>
                                        <div class="d-flex justify-content-between mt-2 px-1">
                                            <small class="text-muted fst-italic" v-if="form.imei.length > 0 && form.imei.length < 15">Kurang {{ 15 - form.imei.length }} digit lagi</small>
                                            <div class="ms-auto fw-bold small" :class="form.imei.length === 15 ? 'text-success' : 'text-danger'">
                                                {{ form.imei.length }}/15 Digit
                                            </div>
                                        </div>
                                        
                                        <div v-if="imeiStatus === 'checking'" class="mt-3 small fw-bold text-center p-2" style="background:var(--primary-soft);border-radius:10px;color:var(--primary);">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Sedang memeriksa database...
                                        </div>
                                        
                                        <!-- DATA COCOK Banner -->
                                        <div v-if="imeiStatus === 'matched'" class="mt-3 p-3 text-center" style="background:linear-gradient(135deg, #ecfdf5, #d1fae5);border-radius:14px;border:2px solid #10b981;animation:matchPulse 0.5s ease;">
                                            <div style="width:48px;height:48px;border-radius:50%;background:#10b981;display:inline-flex;align-items:center;justify-content:center;margin-bottom:8px;animation:matchPop 0.4s ease 0.1s both;">
                                                <i class="bi bi-check-lg text-white" style="font-size:1.5rem;"></i>
                                            </div>
                                            <div class="fw-bold" style="font-size:1.1rem;color:#065f46;">DATA COCOK ✅</div>
                                            <div class="text-muted small mt-1" v-html="imeiMessage"></div>
                                            <div v-if="matchedGadget" class="d-flex justify-content-center gap-2 mt-2 flex-wrap">
                                                <span class="badge" style="background:rgba(16,185,129,0.15);color:#065f46;padding:5px 10px;border-radius:6px;">{{ matchedGadget.jenis_aset }}</span>
                                                <span class="badge" style="background:rgba(16,185,129,0.15);color:#065f46;padding:5px 10px;border-radius:6px;">{{ matchedGadget.status_desc }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Warning / Error / Valid Messages -->
                                        <div v-if="imeiMessage && imeiStatus !== 'matched'" class="alert mt-3 py-2 small d-flex align-items-start border-0"
                                             :class="{ 
                                                'alert-danger': imeiStatus === 'error', 
                                                'alert-warning': imeiStatus === 'warning', 
                                                'alert-success': imeiStatus === 'valid' 
                                             }" style="border-radius:10px;">
                                            <i class="bi me-2 fs-5" :class="{ 'bi-x-circle-fill': imeiStatus === 'error', 'bi-exclamation-triangle-fill': imeiStatus === 'warning', 'bi-check-circle-fill': imeiStatus === 'valid' }"></i>
                                            <div v-html="imeiMessage" class="mt-1"></div>
                                        </div>
                                    </div>

                                    <!-- Keterangan Field -->
                                    <div v-if="form.status_gadget === 'Tidak Ada'" class="mb-4 p-3" style="background:var(--danger-soft);border-radius:14px;border:1px solid rgba(239,68,68,0.15);">
                                        <label class="form-label fw-bold small mb-2" style="color:var(--danger)">ALASAN / KETERANGAN</label>
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <button type="button" class="btn btn-sm btn-outline-secondary bg-white border-0 shadow-sm" style="border-radius:8px;" v-for="r in reasons" :key="r" @click="form.keterangan = r">{{ r }}</button>
                                        </div>
                                        <textarea class="form-control border-0 shadow-sm p-3" name="keterangan" v-model="form.keterangan" rows="3" placeholder="Tulis alasan lain-lain disini..." style="border-radius:12px;"></textarea>
                                    </div>
                                    
                                    <div class="d-grid gap-3 pt-2">
                                        <button type="submit" name="action" value="submit_next" class="btn btn-primary py-3 fw-bold shadow-sm" :disabled="!isValid" style="font-size: 1.05rem; border-radius: 12px;">
                                            <i class="bi bi-check-lg me-1"></i> SIMPAN DATA
                                        </button>
                                        <button type="submit" name="action" value="draft" class="btn btn-light text-muted fw-bold border py-2" style="border-radius: 12px;">
                                            <i class="bi bi-save me-1"></i> Simpan Draft Saja
                                        </button>
                                    </div>
                                </div>
                            </transition>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?= view('partials/mandor_footer') ?>

<script>
    const { createApp, ref, computed, onMounted } = Vue;

    createApp({
        setup() {
            const existing = <?= json_encode($submission ?? null) ?>;
            const karyawanList = <?= json_encode($karyawan_list ?? []) ?>;
            const submissionMap = <?= json_encode($submission_map ?? new \stdClass()) ?>;
            const currentUserId = <?= json_encode(session()->get('id')) ?>;
            
            const searchQuery = ref('');
            const form = ref({ karyawan_id: '', status_gadget: '', imei: '', keterangan: '' });
            const errors = ref({});
            const selectedKaryawanName = ref('');
            const selectedKaryawanNik = ref('');
            const editingId = ref(null);
            
            const imeiStatus = ref(null);
            const imeiMessage = ref('');

            let modal = null;

            if(existing) {
                editingId.value = existing.id;
                form.value = { karyawan_id: existing.karyawan_id, status_gadget: existing.status_gadget, imei: existing.imei || '', keterangan: existing.keterangan || '' };
                const k = karyawanList.find(x => String(x.id) === String(existing.karyawan_id));
                if(k) { selectedKaryawanName.value = k.nama; selectedKaryawanNik.value = k.nik_karyawan; }
            }

            const formAction = computed(() => editingId.value ? '<?= base_url("input/update/") ?>' + editingId.value : '<?= base_url("input/store") ?>');

            const filteredPending = computed(() => {
                const q = searchQuery.value.toLowerCase();
                return karyawanList.filter(k => !submissionMap[k.id]).filter(k => k.nama.toLowerCase().includes(q) || k.nik_karyawan.includes(q));
            });
            
            const filteredDone = computed(() => {
                const q = searchQuery.value.toLowerCase();
                return karyawanList.filter(k => submissionMap[k.id]).filter(k => k.nama.toLowerCase().includes(q) || k.nik_karyawan.includes(q));
            });

            const reasons = ['Perangkat Rusak', 'Perangkat Hilang', 'Belum Dapat Jatah', 'Sedang Diperbaiki'];

            const checkImei = async (imei, kid) => {
                imeiStatus.value = 'checking';
                imeiMessage.value = '';
                
                const formData = new FormData();
                formData.append('imei', imei);
                formData.append('karyawan_id', kid);
                
                const csrfName = '<?= csrf_token() ?>';
                const csrfInput = document.querySelector('input[name="' + csrfName + '"]');
                if(csrfInput) formData.append(csrfName, csrfInput.value);

                try {
                    const res = await fetch('<?= base_url("input/check-imei") ?>', { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if(data.status === 'error') {
                        imeiStatus.value = 'error';
                        imeiMessage.value = data.message;
                        matchedGadget.value = null;
                    } else if (data.status === 'matched') {
                        imeiStatus.value = 'matched';
                        imeiMessage.value = data.message;
                        matchedGadget.value = data.gadget || null;
                    } else if (data.status === 'ok') {
                        if(data.warning) {
                            imeiStatus.value = 'warning';
                            imeiMessage.value = data.warning;
                        } else {
                            imeiStatus.value = 'valid';
                            imeiMessage.value = data.message;
                        }
                        matchedGadget.value = null;
                    } else {
                        imeiStatus.value = 'error';
                        matchedGadget.value = null;
                    }
                } catch (e) {
                    console.error(e);
                    imeiStatus.value = 'error';
                    imeiMessage.value = 'Gagal terhubung ke server.';
                    matchedGadget.value = null;
                }
            };

            const sanitizeImei = (e) => {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 15) val = val.slice(0, 15);
                form.value.imei = val;
                
                if(val.length === 15) {
                    checkImei(val, form.value.karyawan_id);
                } else {
                    imeiStatus.value = null;
                    imeiMessage.value = '';
                }
            };

            const matchedGadget = ref(null);

            const isValid = computed(() => {
                if (!form.value.karyawan_id || !form.value.status_gadget) return false;
                if (form.value.status_gadget === 'Ada') {
                    return form.value.imei.length === 15 && (imeiStatus.value === 'valid' || imeiStatus.value === 'matched' || imeiStatus.value === 'warning'); 
                }
                return form.value.keterangan.length > 3;
            });

            const openModal = (k) => {
                if(k && k.id){
                    form.value = { karyawan_id: k.id, status_gadget: '', imei: '', keterangan: '' };
                    selectedKaryawanName.value = k.nama;
                    selectedKaryawanNik.value = k.nik_karyawan;
                    editingId.value = null;
                    imeiStatus.value = null;
                    imeiMessage.value = '';
                    matchedGadget.value = null;
                }
                if(modal) modal.show();
            };

            const scrollToTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

            onMounted(() => {
                const el = document.getElementById('inputModal');
                if(el) { modal = new bootstrap.Modal(el); if(existing) modal.show(); }
            });

            return { 
                karyawanList, submissionMap, filteredPending, filteredDone, searchQuery, form, errors, reasons, isValid, 
                openModal, scrollToTop, formAction, selectedKaryawanName, selectedKaryawanNik, editingId, sanitizeImei,
                imeiStatus, imeiMessage, matchedGadget, currentUserId 
            };
        }
    }).mount('#app');
</script>
