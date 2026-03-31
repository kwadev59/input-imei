# SISTEM INPUT DATA GADGET & DISTRIBUSI RAINCASE
## Aplikasi Web Lokal -- Operasional Kebun

---

## 1. Latar Belakang

Sistem ini dirancang untuk mendukung proses distribusi gadget dan raincase kepada karyawan kebun secara cepat, terstruktur, dan terdokumentasi dengan baik.

Aplikasi berjalan pada jaringan lokal kantor (tanpa internet) dan dapat diakses melalui:
- PC kantor
- Laptop administrator
- HP mandor melalui WiFi internal

---

## 2. Tujuan Sistem

Sistem digunakan untuk:
1. Pengumpulan data kepemilikan gadget karyawan.
2. Menjadi syarat pembagian tas gadget & raincase.
3. Menjadi database awal inventaris gadget perusahaan.
4. Menyediakan data audit distribusi dan operasional.

---

## 3. Arsitektur Sistem (Technical Stack)

### 3.1 Framework & Platform
Aplikasi dibangun menggunakan teknologi modern yang ringan dan cepat:
- **Framework Backend**: CodeIgniter 4 (PHP 7.4+ / 8.0+)
- **Database**: MySQL / MariaDB
- **Web Server**: Apache (via XAMPP / Laragon)
- **Frontend**: HTML5, CSS3, JavaScript (JQuery/Vanilla)

### 3.2 Kebutuhan Minimal Server
- RAM minimal 4 GB (Rekomendasi 8 GB)
- SSD minimal 256 GB
- Jaringan LAN stabil dengan Access Point untuk HP Mandor
- **Backup**: Auto-backup database (mysqldump) setiap hari

---

## 4. Peran Pengguna (User Role)

### 4.1 Mandor
- **Akses**: Terbatas (Input Only)
- **Fitur**:
  - Login menggunakan NPK
  - Input data pekerja
  - Mengisi status kepemilikan gadget

### 4.2 Administrator
- **Akses**: Penuh (Full Control)
- **Fitur**:
  - Verifikasi data masuk
  - Dashboard Monitoring (IMEI ganda, Progress per Afdeling)
  - Cetak Laporan (PDF & Excel)
  - Manajemen Master Data (Pekerja & Gadget)

---

## 5. Rancangan Database (Skema Awal)

Sistem menggunakan struktur database relasional:

### Tabel: `users` (Pengguna Sistem)
- `id` (PK)
- `npk` (Unique, Username Login)
- `nama_lengkap`
- `role` ('admin', 'mandor')
- `password_hash` (Bcrypt)
- `afdeling_id` (Relasi ke lokasi kerja)

### Tabel: `karyawan` (Data Master Pekerja)
- `id` (PK)
- `nik_karyawan`
- `nama`
- `jabatan` (Pemanen, Perawatan, dll)
- `afdeling`
- `status_aktif` (Aktif/Resign)

### Tabel: `distribusi_gadget` (Transaksi)
- `id` (PK)
- `karyawan_id` (FK)
- `status_gadget` ('Ada', 'Tidak Ada')
- `imei` (15 digit, Unique jika ada)
- `keterangan`
- `input_by` (FK User Mandor)
- `input_at` (Timestamp)
- `is_verified` (Boolean)

---

## 6. Alur Sistem (Flow Operasional)

### 6.1 Login Mandor
1. Mandor login menggunakan NPK.
2. Sistem memvalidasi NPK dan Password.
3. Sistem mendeteksi Afdeling & Tugas Mandor (Panen/Rawat).

### 6.2 Daftar Pekerja Otomatis
Setelah login, sistem menampilkan daftar pekerja yang **belum diinput** hari ini:
- Mandor Panen → Melihat daftar pemanen di afdelingnya.
- Mandor Rawat → Melihat daftar pekerja rawat di afdelingnya.

### 6.3 Input Status Gadget
Mandor memilih salah satu status untuk setiap pekerja:

**A. Karyawan Memiliki Gadget**
- Form Input IMEI muncul (Wajib 15 digit angka).
- **Validasi Live**: Sistem mengecek apakah IMEI sudah terdaftar di database.
  - *Warning* jika IMEI duplikat, namun data tetap bisa disimpan dengan flag 'Duplicate'.

**B. Karyawan Tidak Memiliki Gadget**
- Kolom IMEI disembunyikan.
- Wajib mengisi **Keterangan** (Contoh: "Rusak", "Hilang", "Belum Dapat").

### 6.4 Penyimpanan (Quick Submit)
- Target waktu input: < 5 detik per pekerja.
- Setelah simpan, otomatis lanjut ke nama pekerja berikutnya dalam antrian.

---

## 7. Dashboard & Reporting (Admin)

### 7.1 Dashboard
Menampilkan statistik realtime:
- Total Karyawan vs Total Sudah Input
- Progress per Afdeling (%)
- Daftar **IMEI Duplikat** (Perlu penanganan segera)
- Daftar Karyawan Tanpa Gadget

### 7.2 Laporan & Export
Fitur cetak dokumen untuk audit:
1. **Laporan Harian (PDF)**: Format resmi dengan tanda tangan, berisi daftar pekerja dan status gadget.
2. **Export Data (Excel)**: Untuk analisa lebih lanjut (Pivot Table, VLOOKUP).

---

## 8. Keamanan & Validasi (CI4 Implementation)

Untuk memastikan integritas data dan keamanan sistem lokal:

1. **Input Validation (CI4 Validation)**:
   - IMEI wajib numerik & 15 digit.
   - Mencegah SQL Injection pada input teks.
2. **Session Management**:
   - Auto-logout jika tidak aktif selama 30 menit.
3. **Password Security**:
   - Password disimpan menggunakan hashing modern (`password_hash`).
4. **CSRF Protection**:
   - Mengaktifkan fitur CSRF Token CodeIgniter untuk mencegah form submission ilegal.

---

## 9. Pengembangan Ke Depan

Data yang terkumpul dapat digunakan untuk:
- Tracking history penggunaan gadget per karyawan (siapa pakai HP apa sebelumnya).
- Integrasi dengan sistem Payroll (potongan jika gadget hilang).
- Analisa umur pakai gadget operasional.

---
*Dokumen ini diperbarui pada: 19 Februari 2026*
*Framework Target: CodeIgniter 4*
