# Sistem Input Data Gadget & Distribusi Raincase

## Deskripsi Proyek
Sistem ini dirancang untuk mendukung proses distribusi gadget dan raincase kepada karyawan kebun secara cepat, terstruktur, dan terdokumentasi dengan baik. Aplikasi berjalan pada jaringan lokal kantor (offline-first) dan dapat diakses melalui PC, Laptop, maupun Smartphone mandor melalui WiFi internal.

## Fitur Utama
1.  **Manajemen Mandor:** Login cepat menggunakan NPK tanpa password (opsional) untuk mempermudah input di lapangan.
2.  **Input Data Gadget:** Pengumpulan data kepemilikan gadget karyawan sebagai syarat pembagian raincase.
3.  **Dashboard Admin:** Monitoring statistik distribusi, rekap afdeling, dan rekap MPP secara real-time.
4.  **Sistem Pengiriman (BASTE):** Manajemen pengiriman gadget lengkap dengan fitur cetak Berita Acara (PDF) dan antrian cetak otomatis.
5.  **API Print Queue:** Integrasi dengan Agent Python/Bash untuk pencetakan label/struk via printer ESC/POS secara otomatis.

## Tech Stack
- **Framework:** CodeIgniter 4 (PHP 8.2+)
- **Database:** MySQL / MariaDB
- **Web Server:** Nginx / Apache
- **Styling:** Vanilla CSS & Bootstrap 5 (Custom Admin Dashboard)

## Cara Instalasi
1.  Clone repositori ini: `git clone <url-repository>`
2.  Install dependensi composer: `composer install`
3.  Copy `.env.example` ke `.env` dan sesuaikan pengaturan database.
4.  Jalankan migrasi database: `php spark migrate`
5.  Akses aplikasi via browser di `http://localhost` (atau IP server lokal).

## Lisensi
Distributed under the MIT License. See `LICENSE` for more information.
