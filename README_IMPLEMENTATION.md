# Panduan Implementasi Sistem Distribusi Gadget

Sistem telah diimplementasikan menggunakan CodeIgniter 4. Berikut adalah panduan singkat untuk menjalankan sistem.

## 1. Setup Database
Pastikan database telah dibuat dan di-seed (sudah dilakukan otomatis oleh script).
- Database: `db_gadget`
- Tabel: `users`, `karyawan`, `distribusi_gadget`

## 2. Akses Sistem
Buka browser dan akses:
`http://localhost/input-imei/public/`

## 3. Akun Login Default
Gunakan akun berikut untuk testing:

### Administrator
- **NPK**: `admin`
- **Password**: `admin123`
- **Akses**: Dashboard, Monitoring, Export Excel

### Mandor
- **NPK**: `12345`
- **Password**: `mandor123`
- **Akses**: Form Input Gadget
- **Afdeling**: AFD-01 (Hanya bisa melihat karyawan di afdeling ini)

## 4. Alur Pengujian
1. Login sebagai **Mandor** (`12345`).
2. Lakukan input untuk karyawan yang tersedia (misal: Budi Santoso, Slamet Riyadi).
3. Coba skenario "Ada Gadget" (input IMEI) dan "Tidak Ada Gadget" (pilih alasan).
4. Logout Mandor.
5. Login sebagai **Administrator** (`admin`).
6. Cek Dashboard, lihat statistik bertambah.
7. Coba tombol **Export Excel** untuk mengunduh laporan.

Selamat mencoba!
