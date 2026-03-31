# Panduan Deploy Input IMEI ke Ubuntu Localhost

Dokumen ini berisi langkah-langkah lengkap untuk melakukan deployment aplikasi Input IMEI ke server Ubuntu (Localhost/WSL/VM) dan mengaksesnya menggunakan domain lokal.

## Prasyarat

- Sistem Operasi: Ubuntu 20.04/22.04/24.04 (bisa via VM, WSL, atau native install)
- Akses Root / Sudo
- Koneksi Internet untuk instalasi paket

---

## Langkah 1: Install LAMP Stack (Linux, Apache, MySQL, PHP)

Update repository dan install paket yang dibutuhkan. Aplikasi ini membutuhkan PHP 8.2 atau lebih baru.

```bash
sudo apt update
sudo apt install apache2 mysql-server php8.2 php8.2-mysql php8.2-intl php8.2-mbstring php8.2-xml php8.2-curl php8.2-gd php8.2-zip unzip git -y
```

Aktifkan modul Apache yang dibutuhkan CodeIgniter 4:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Langkah 2: Konfigurasi Database

Login ke MySQL sebagai root dan buat database serta user baru.

```bash
sudo mysql
```

Jalankan perintah SQL berikut di dalam prompt MySQL:

```sql
CREATE DATABASE input_imei_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'imei_user'@'localhost' IDENTIFIED BY 'password_aman_123';
GRANT ALL PRIVILEGES ON input_imei_db.* TO 'imei_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> **Catatan:** Ganti `'password_aman_123'` dengan password yang Anda inginkan.

## Langkah 3: Deploy Kode Aplikasi

Asumsikan kita meletakkan kode aplikasi di `/var/www/input-imei`.

    **Opsi A: Menggunakan Git (Recomendasi jika ada internet)**:
    ```bash
    cd /var/www
    sudo git clone https://github.com/username/repo-anda.git input-imei
    ```

    **Opsi B: Copy Manual dari Windows ke Ubuntu (WSL)**
    Jika Anda menggunakan WSL, Anda bisa mengakses file Windows dari Ubuntu.
    ```bash
    # 1. Masuk ke folder www di Ubuntu
    cd /var/www
    
    # 2. Buat folder project
    sudo mkdir input-imei
    
    # 3. Copy semua file dari folder Windows (Misal: D:\Project\input-imei)
    # Perhatikan: /mnt/d/ adalah drive D: di Windows
    sudo cp -r /mnt/d/Project/input-imei/* /var/www/input-imei/

    # ATAU jika project ada di C:\laragon\www\input-imei
    sudo cp -r /mnt/c/laragon/www/input-imei/* /var/www/input-imei/
    ```

    **Opsi C: Copy Manual Menggunakan Tools (WinSCP / FileZilla)**
    Jika Ubuntu Anda adalah VM atau Server terpisah:
    1.  Install **WinSCP** atau **FileZilla** di Windows.
    2.  Hubungkan ke IP Ubuntu (lihat IP dengan perintah `ip addr`).
    3.  Upload semua file dari folder project Windows ke folder `/home/username/input-imei` di Ubuntu.
    4.  Pindahkan ke `/var/www/`:
        ```bash
        sudo mv /home/username/input-imei /var/www/
        ```

2.  **Atur Izin (Permissions)**:
    Folder `writable` harus bisa ditulis oleh server.

    ```bash
    sudo chown -R www-data:www-data /var/www/input-imei
    sudo chmod -R 755 /var/www/input-imei
    sudo chmod -R 777 /var/www/input-imei/writable
    ```

3.  **Install Dependencies (Composer)**:
    Jika folder `vendor` belum ada, jalankan perintah berikut:
    ```bash
    cd /var/www/input-imei
    sudo composer install --no-dev --optimize-autoloader
    ```

## Langkah 4: Konfigurasi CodeIgniter (.env)

Masuk ke folder aplikasi dan setup konfigurasi.

```bash
cd /var/www/input-imei
sudo cp env .env
sudo nano .env
```

Edit bagian berikut di dalam file `.env`:

```ini
# Environment
CI_ENVIRONMENT = production

# App URL (sesuaikan dengan domain lokal yang diinginkan)
app.baseURL = 'http://input-imei.local/'

# Database
database.default.hostname = localhost
database.default.database = input_imei_db
database.default.username = imei_user
database.default.password = password_aman_123
database.default.DBDriver = MySQLi
```
Simpan file (Ctrl+O, Enter) dan keluar (Ctrl+X).

## Langkah 5: Jalankan Migrasi Database

Jalankan perintah Spark untuk membuat tabel-tabel database.

```bash
php spark migrate
```

## Langkah 6: Konfigurasi Virtual Host Apache

Buat file konfigurasi baru untuk situs ini.

```bash
sudo nano /etc/apache2/sites-available/input-imei.conf
```

Isi dengan konfigurasi berikut:

```apache
<VirtualHost *:80>
    ServerName input-imei.local
    ServerAdmin admin@input-imei.local
    DocumentRoot /var/www/input-imei/public

    <Directory /var/www/input-imei/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/input-imei-error.log
    CustomLog ${APACHE_LOG_DIR}/input-imei-access.log combined
</VirtualHost>
```

Simpan dan keluar. Kemudian aktifkan situs dan reload Apache.

```bash
sudo a2dissite 000-default.conf  # Opsional: disable default site
sudo a2ensite input-imei.conf
sudo systemctl reload apache2
```

## Langkah 7: Setup Domain Lokal (Agar bisa diakses via browser)

Supaya browser bisa mengenali `http://input-imei.local`, Anda perlu mengedit file `hosts`.

### A. Jika Mengakses dari Ubuntu Desktop Langsung
Buka file hosts:
```bash
sudo nano /etc/hosts
```
Tambahkan baris berikut di paling bawah:
```
127.0.0.1   input-imei.local
```

### B. Jika Mengakses dari Windows (karena Ubuntu ada di VM/WSL)
1.  Cari tahu IP Address Ubuntu:
    Di terminal Ubuntu ketik: `ip addr` atau `hostname -I`.
    Misal IP-nya adalah `192.168.1.15`.
2.  Buka Notepad sebagai **Administrator** di Windows.
3.  Buka file: `C:\Windows\System32\drivers\etc\hosts`.
4.  Tambahkan baris berikut di paling bawah:
    ```
    192.168.1.15   input-imei.local
    ```
    *(Ganti 192.168.1.15 dengan IP Ubuntu yang sebenarnya)*.

---

## Selesai!

Sekarang buka browser dan akses:
**http://input-imei.local**

Jika berhasil, Anda akan melihat halaman login atau dashboard aplikasi.
