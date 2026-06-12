# D1041231071 — WebGIS Smart City Project
**Naufal Zaky R | UAS WebGIS**

Aplikasi WebGIS berbasis PHP + MySQL untuk pemetaan data kemiskinan, fasilitas umum, laporan warga, dan analisis blank spot bantuan sosial di Kota Pontianak.

## Stack Teknologi

- PHP 8.2 Apache
- MySQL / MariaDB + PostGIS
- Leaflet.js (peta interaktif)
- PDO MySQL (koneksi database)

## Cara Deploy (Coolify)

1. Buat database MySQL/MariaDB di Coolify.
2. Import `db_scripts/webgis_naufal_zaky.sql` ke database tersebut.
3. Buat aplikasi baru dari repo ini.
4. Pilih build menggunakan `Dockerfile` di root repository.
5. Set environment variable:

```env
APP_BASE_PATH=
SESSION_SECURE=true
CORS_ALLOWED_ORIGIN=
DB_HOST=nama-service-database
DB_PORT=3306
DB_DATABASE=webgis_db
DB_USERNAME=user_database
DB_PASSWORD=password_database
```

Aplikasi final berjalan di `/webgis_app`. Gunakan `APP_BASE_PATH=/webgis_app` untuk deploy.

## Akun Awal

Setelah import SQL:

- Admin: `admin / admin123`
- Pengguna: `pengguna / user123`

> Ganti password akun demo setelah deploy jika aplikasi dibuka publik.

## Struktur Project

```
D1041231071_NaufalZakyR_UAS_WebGISProject/
├── beranda.html           ← Halaman pemilih progres
├── webgis_app/            ← Aplikasi final (main app)
│   ├── panel_admin/       ← Halaman & fitur admin
│   ├── panel_user/        ← Halaman & fitur pengguna
│   ├── endpoints/         ← API endpoint PHP
│   ├── core_config/       ← Konfigurasi (DB, session, auth)
│   └── public_assets/     ← CSS & JS (global, panel, map)
├── db_scripts/            ← SQL scripts database
│   ├── buat_tabel.sql
│   ├── data_awal_wilayah.sql
│   ├── data_awal_pengguna.sql
│   └── webgis_naufal_zaky.sql
├── version_01/            ← Progres pertemuan 1
├── version_02/            ← Progres pertemuan 2
├── version_03/            ← Progres pertemuan 3
├── legacy_version/        ← Versi lama (referensi)
├── Dockerfile             ← Docker image untuk Coolify
└── .env                   ← Konfigurasi environment
```

## Jalankan di XAMPP Lokal

Set konfigurasi di `.env`:

```env
APP_BASE_PATH=/D1041231071_NaufalZakyR_UAS_WebGISProject/webgis_app
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webgis_db
DB_USERNAME=root
DB_PASSWORD=
```

Import `db_scripts/webgis_naufal_zaky.sql` ke phpMyAdmin, lalu buka:
`http://localhost/D1041231071_NaufalZakyR_UAS_WebGISProject/webgis_app/login.php`
