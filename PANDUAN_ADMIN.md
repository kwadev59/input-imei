# Panduan Fitur Administrator Tambahan

Berikut adalah panduan penggunaan fitur baru untuk Administrator:

## 1. Import Data Karyawan (CSV)
Fitur ini digunakan untuk memasukkan banyak data karyawan sekaligus.

1.  Masuk ke menu **Data Karyawan**.
2.  Klik tombol **Import CSV**.
3.  Siapkan file CSV dengan format kolom:
    *   Kolom 1: **NIK** (Unik)
    *   Kolom 2: **Nama Lengkap**
    *   Kolom 3: **Jabatan** (misal: Pemanen, Perawatan)
    *   Kolom 4: **Afdeling** (misal: AFD-01, AFD-02)
    *   Kolom 5: **Status** (Opsional: Aktif/Resign)
4.  Upload file tersebut. Sistem akan otomatis update data jika NIK sudah ada, atau insert baru jika belum.

## 2. Edit & Mutasi Karyawan
Digunakan untuk mengubah data karyawan, memindahkan lokasi kerja (Mutasi), atau update status resign.

1.  Di menu **Data Karyawan**, cari nama karyawan.
2.  Klik tombol **Edit / Mutasi**.
3.  Anda dapat mengubah:
    *   **Jabatan**: Jika ada promosi/rotasi.
    *   **Afdeling**: Jika karyawan pindah lokasi (Mutasi).
    *   **Status Keaktifan**: Set ke **Resign/Non-Aktif** jika karyawan keluar.
4.  Karyawan yang diset **Non-Aktif** tidak akan muncul di form input Mandor.

## 3. Rekap Input (Laporan)
Halaman khusus untuk melihat seluruh history inputan mandor.

1.  Masuk ke menu **Rekap Input**.
2.  Gunakan **Filter** di bagian atas untuk menyaring data berdasarkan:
    *   **Afdeling**: Melihat inputan afdeling tertentu.
    *   **Status Gadget**: Melihat siapa saja yang "Ada Gadget" atau "Tidak Ada".
3.  Klik **Download Excel** untuk mengunduh laporan sesuai filter (saat ini download semua data).
