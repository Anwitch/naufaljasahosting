# 🗺️ WebGIS Smart City Pontianak — Portal & Aplikasi Spasial
**Tugas Akhir / UAS — Sistem Informasi Geografis (SIG)**

Selamat datang di repositori **WebGIS Smart City Pontianak**. Repositori ini merupakan wadah proyek akhir mata kuliah Sistem Informasi Geografis yang mengintegrasikan seluruh modul praktikum pembelajaran dari dasar hingga aplikasi final berbasis spasial.

---

## 👤 Identitas Mahasiswa
* **Nama**: Naufal Zaky Ramadhan
* **NIM**: D1041231071
* **Program Studi**: Rekayasa Sistem Komputer
* **Mata Kuliah**: Sistem Informasi Geografis (2025/2026)
* **Status Proyek**: Produksi / Selesai Deploy (Coolify & XAMPP)

---

## 📂 Struktur Repositori

```text
D1041231071_NaufalZakyR_UAS_WebGISProject/
├── portal.html            # Halaman Hub utama (Directory & Navigator Portal)
├── 01/                    # Modul Pertemuan 01 - Geometri Spasial Dasar
│   ├── backend/           # API lokal Modul 01
│   └── frontend/          # Tampilan Leaflet & Leaflet Draw (Geometri Dasar)
├── 02/                    # Modul Pertemuan 02 - Analisis Proksimitas Jarak
│   ├── backend/           # API lokal Modul 02
│   └── frontend/          # Tampilan Peta Haversine & Layer Tematik
├── 03/                    # Modul Pertemuan 03 - Analisis Spasial Lanjut
│   ├── backend/           # API lokal Modul 03
│   └── frontend/          # Tampilan Peta Choropleth (Point-in-Polygon)
├── webgis_app/            # APLIKASI UTAMA (UAS - Integrated Smart City WebGIS)
│   ├── core_config/       # Konfigurasi inti (Database, Session, Auth, Env Loader)
│   ├── endpoints/         # Handler API data spasial (GeoJSON CRUD & Analisis)
│   ├── api/               # API Router / Wrappers
│   ├── panel_admin/       # Dashboard & Manajemen Data Spasial Admin
│   ├── panel_user/        # Dashboard, Laporan Warga & Peta Interaktif User
│   └── public_assets/     # File statis (Aesthetic CSS global, JS Peta, & Ikon)
├── db_scripts/            # Script skema SQL & Seed Data awal
├── Dockerfile             # Konfigurasi containerization Apache + PHP 8.2 untuk Deploy
├── .env.example           # Contoh konfigurasi environment variable
└── README.md              # Dokumentasi teknis proyek
```

---

## 🌐 Fitur Utama Pada Portal (`portal.html`)

Halaman `portal.html` di root repositori bertindak sebagai **pintu gerbang interaktif (Dashboard Portal Hub)** yang menghubungkan seluruh progres pengerjaan praktikum dari awal semester hingga aplikasi final UAS. 

### Karakteristik & Fitur Portal:
* **Rich Modern UI**: Menggunakan skema desain *sleek dark mode* secara default, dengan dukungan *theme toggle* (Dark/Light) yang tersimpan secara lokal menggunakan `localStorage`.
* **Micro-Animations**: Efek hover interaktif, kartu portal dinamis dengan glassmorphic gradient, serta ambient glow orbs yang mempercantik estetika antarmuka.
* **Milestone Directory**:
  1. **Modul 01 (Geometri Dasar)**: Pembelajaran cara menggambar dan menyimpan tipe data spasial: Titik/Point (direpresentasikan sebagai SPBU), Garis/LineString (Jalan), dan Poligon/Polygon (Kavling Tanah) menggunakan plugin **Leaflet Draw** ke database MySQL.
  2. **Modul 02 (Haversine & Tematik)**: Pengenalan analisis jarak melengkung bumi (formula Haversine) secara real-time untuk mendeteksi sebaran Warga Miskin dalam radius bantuan Rumah Ibadah, serta visualisasi tematik sebaran sosial.
  3. **Modul 03 (Analisis Spasial - Point in Polygon)**: Implementasi algoritma spasial untuk mendeteksi titik Warga Miskin yang berada di dalam koordinat Poligon Kavling secara dinamis guna mewarnai kavling berdasarkan tingkat kepadatan warga (Choropleth Map).
  4. **Smart City WebGIS App (UAS)**: Integrasi penuh dari modul 01, 02, dan 03 ke dalam sebuah aplikasi web multi-peran dengan fitur pelaporan warga dan ulasan fasilitas kota.

---

## 🚀 Aplikasi Utama (`webgis_app` / Proyek Final UAS)

Aplikasi **Smart City WebGIS** merupakan hasil kompilasi dan penyempurnaan dari seluruh modul pembelajaran. Aplikasi ini ditujukan untuk memetakan sarana perkotaan, menganalisis daerah rentan sosial, serta membuka jalur interaksi dua arah antara masyarakat dan pemerintah daerah.

### 🛠️ Stack Teknologi & Arsitektur Spasial
* **Bahasa Pemrograman**: PHP Native (Terstruktur, kompatibel PHP 8.0 - 8.3)
* **Peta & Visualisasi**: Leaflet.js, Leaflet.Draw, FontAwesome, & Google Fonts (Inter, Space Grotesk)
* **Sistem Database**: MySQL / MariaDB dengan dukungan **Spatial Index** (`SPATIAL INDEX`) dan fungsi spasial OpenGIS (`ST_Contains`, `ST_Distance_Sphere`, `ST_GeomFromText`).
* **Security & Auth**: Proteksi Session aman, enkripsi password menggunakan `bcrypt` (`password_verify`), Prepared Statements untuk mencegah SQL Injection, dan middleware batasan hak akses peran.

---

### 🛡️ Fitur Bagian Admin (`panel_admin/`)

Panel khusus administrator perkotaan untuk mengelola seluruh data geospasial secara dinamis.

#### 1. Dashboard Analitik Spasial
* Menampilkan jumlah total objek spasial terdaftar (SPBU, Ruas Jalan, Rumah Ibadah, Kavling, Warga Miskin).
* **MySQL Spatial Counters**: Menghitung secara real-time jumlah **Kawasan Rawan** (Kawasan kumuh dengan lebih dari 3 warga miskin di dalamnya) dan jumlah **Blank Spot Bansos** (Warga miskin yang belum tercover bantuan radius rumah ibadah).
* Menampilkan daftar entitas terbaru yang baru saja didaftarkan oleh sistem atau dilaporkan oleh warga.

#### 2. Peta Interaktif Admin & Drawing Tools
* Integrasi **Leaflet.Draw** custom. Admin dapat menambah data langsung dengan menggambar di peta:
  * Menitikkan **SPBU** (Data Point + status operasional 24 jam).
  * Menitikkan **Rumah Ibadah** (Data Point + radius bantuan sosial).
  * Menitikkan **Warga Miskin** (Data Point + data ekonomi pendapatan/tanggungan).
  * Menarik garis **Jalur Jalan** (Data LineString + klasifikasi jalan).
  * Menggambar area **Kavling Tanah** (Data Polygon + pemilik & luas tanah).
  * Menggambar **Kawasan Kumuh** (Data Polygon).
* **Sinc-Data CRUD**: Klik objek apa saja di peta untuk melihat detail atribut, mengedit properti, mengubah letak koordinat, atau menghapusnya langsung dari peta.

#### 3. Data Tables Manager
* Modul CRUD berbasis tabel interaktif untuk seluruh 8 entitas data.
* Fitur pencarian instan tanpa reload, status badge dinamis, serta integrasi input data atribut spasial secara manual.
* **Moderasi Laporan Warga**: Admin dapat melihat keluhan lokasi dari warga, melihat titik masalah di peta, dan mengubah status laporan menjadi *Diproses*, *Selesai*, atau *Ditolak*.

---

### 👥 Fitur Bagian Pengguna (`panel_user/`)

Panel publik yang diperuntukkan bagi warga kota Pontianak untuk memantau kota dan berpartisipasi aktif.

#### 1. Peta Interaktif User (Read-Only)
* Menampilkan seluruh layer spasial perkotaan yang dapat diaktifkan/dinonaktifkan melalui menu kontrol layer di sidebar.
* **Legenda Dinamis**: Panduan simbol warna pada peta (seperti SPBU 24 jam berwarna hijau, SPBU terbatas berwarna jingga, dsb).
* **Analisis Spasial Publik**: Visualisasi langsung peta Choropleth kepadatan dan sebaran Blank Spot bantuan sosial di wilayah sekitar.

#### 2. Alat Spasial "SPBU Terdekat" (Proximity Search)
* Pengguna dapat menekan tombol **SPBU Terdekat** pada peta.
* Sistem akan mendeteksi titik tengah koordinat layar peta pengguna (`map.getCenter()`) dan melakukan query spasial ke backend.
* Backend memproses pencarian menggunakan rumus **Haversine** (`ST_Distance_Sphere`) untuk mengurutkan stasiun pengisian bahan bakar terdekat lengkap dengan informasi jarak (meter/kilometer) dan durasi operasionalnya.

#### 3. Laporan Warga (Citizen Crowdsourcing)
* Pengguna dapat mengirimkan pengaduan masalah infrastruktur (misalnya: Jalan Rusak, Banjir, Lampu Jalan Mati, Fasilitas Terbengkalai).
* **Location Tagging**: Dilengkapi dengan mini-map Leaflet interaktif di dalam modal. Pengguna cukup mengklik lokasi kerusakan di peta untuk menangkap koordinat Latitude dan Longitude secara presisi.
* Pengguna dapat melihat daftar laporan miliknya beserta status perkembangannya (Menunggu, Diproses, Selesai). Laporan yang masih berstatus "Menunggu" dapat dibatalkan/dihapus oleh pengguna.

#### 4. Ulasan & Rating Fasilitas Kota
* Pengguna dapat memberikan rating (skala 1 - 5 bintang) dan ulasan tertulis pada fasilitas publik seperti SPBU dan Rumah Ibadah.
* Ulasan ini akan tampil pada popup informasi ketika fasilitas tersebut diklik di peta utama oleh pengguna lain.

---

### 📊 Detail Implementasi Query Spasial MySQL

Aplikasi ini menggunakan keunggulan MySQL Spatial Geometris untuk efisiensi kalkulasi data peta:

1. **Deteksi Kawasan Rawan (Kawasan Kumuh Padat Penduduk Miskin)**
   Menghitung jumlah warga miskin yang posisinya berada di dalam poligon kawasan kumuh menggunakan fungsi `ST_Contains`:
   ```sql
   SELECT k.id, k.nama_kawasan, COUNT(w.id) as jumlah_warga 
   FROM kawasan_kumuh k
   LEFT JOIN warga_miskin w ON ST_Contains(k.geom, w.geom)
   GROUP BY k.id HAVING jumlah_warga > 3;
   ```

2. **Deteksi Blank Spot Bantuan Sosial**
   Mendeteksi warga miskin yang lokasinya berada di luar radius pelayanan bantuan seluruh rumah ibadah di kota menggunakan `ST_Distance_Sphere`:
   ```sql
   SELECT w.id, w.nama_kk 
   FROM warga_miskin w
   WHERE NOT EXISTS (
       SELECT 1 FROM rumah_ibadah ri
       WHERE ST_Distance_Sphere(w.geom, ri.geom) <= ri.radius_bantuan_meter
   );
   ```

3. **Pencarian SPBU Terdekat (Proximity Query)**
   Mencari SPBU dengan radius terdekat dari koordinat input (`:user_lon`, `:user_lat`):
   ```sql
   SELECT nama, buka_24_jam, 
          ST_Distance_Sphere(geom, ST_GeomFromText('POINT(user_lon user_lat)', 4326)) AS jarak_meter
   FROM spbu
   ORDER BY jarak_meter ASC LIMIT 5;
   ```

---

## ⚙️ Cara Menjalankan Project

### 💻 A. Menjalankan di XAMPP Lokal (Windows)

#### 1. Persiapan Direktori Proyek
Salin folder proyek ini ke direktori web root XAMPP Anda (biasanya `C:\xampp\htdocs\`). Pastikan nama foldernya sesuai:
`C:\xampp\htdocs\D1041231071_NaufalZakyR_UAS_WebGISProject`

#### 2. Konfigurasi Database
1. Aktifkan service **Apache** dan **MySQL** melalui XAMPP Control Panel.
2. Buka browser dan akses `http://localhost/phpmyadmin/`.
3. Buat database baru bernama `webgis_db`.
4. Import file SQL database awal yang terletak di `db_scripts/webgis_naufal_zaky.sql` ke dalam database `webgis_db`.
   > 💡 **Fitur Migrasi Otomatis**: Alternatif lain, Anda cukup membuat database kosong `webgis_db`. Saat aplikasi pertama kali dijalankan, sistem koneksi PDO akan mendeteksi database kosong dan secara otomatis mengimpor tabel serta data awal tanpa perlu manual phpMyAdmin!

#### 3. Pengaturan Environment (.env)
Buat file bernama `.env` di direktori utama proyek (sejajar dengan `portal.html`) dengan isi sebagai berikut:
```env
APP_BASE_PATH=/D1041231071_NaufalZakyR_UAS_WebGISProject/webgis_app
SESSION_SECURE=false
CORS_ALLOWED_ORIGIN=http://localhost

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webgis_db
DB_USERNAME=root
DB_PASSWORD=
```

#### 4. Mengakses Aplikasi
* Untuk membuka halaman **Portal Proyek**:
  `http://localhost/D1041231071_NaufalZakyR_UAS_WebGISProject/portal.html`
* Untuk langsung menuju **Aplikasi WebGIS Utama**:
  `http://localhost/D1041231071_NaufalZakyR_UAS_WebGISProject/webgis_app/`

---

### 🐳 B. Menjalankan Menggunakan Docker
Repositori ini telah dilengkapi dengan `Dockerfile` siap pakai untuk containerization.

1. Buka terminal di direktori proyek dan jalankan perintah build image:
   ```bash
   docker build -t webgis-smartcity .
   ```
2. Jalankan container dengan menghubungkannya ke container database Anda:
   ```bash
   docker run -d -p 8080:80 --name webgis-app webgis-smartcity
   ```
3. Akses aplikasi di `http://localhost:8080/`.

---

### ☁️ C. Deploying to Coolify (Production)
Jika Anda men-deploy proyek ini ke cloud server menggunakan platform **Coolify**:
1. Hubungkan akun GitHub/GitLab Anda ke Coolify.
2. Buat database MySQL / MariaDB resource di panel Coolify.
3. Tambahkan aplikasi baru dari repositori Git ini.
4. Set buildpack ke **Dockerfile** (Coolify akan mendeteksi `Dockerfile` di root secara otomatis).
5. Definisikan **Environment Variables** di panel aplikasi Coolify sesuai konfigurasi database yang dibuat:
   ```env
   APP_BASE_PATH=
   SESSION_SECURE=true
   DB_HOST=mysql-service-name
   DB_PORT=3306
   DB_DATABASE=webgis_db
   DB_USERNAME=coolify_user
   DB_PASSWORD=coolify_password
   ```

---

## 🔑 Kredensial Akun Pengujian (Demo)

Gunakan akun berikut untuk masuk ke sistem di halaman login (`webgis_app/login.php`):

| Peran (Role) | Username | Password | Deskripsi Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | Akses penuh edit spasial peta, CRUD tabel, dan moderasi laporan warga. |
| **Warga Publik** | `pengguna` | `user123` | Akses view peta interaktif, Proximity SPBU, kirim ulasan, dan input laporan warga. |

> [!WARNING]
> *Demi keamanan data publik, harap segera mengubah kata sandi default atau menghapus akun demo ini melalui menu **Manajemen Pengguna** setelah sistem di-deploy ke server produksi.*
