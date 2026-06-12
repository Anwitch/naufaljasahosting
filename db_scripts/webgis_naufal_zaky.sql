-- ============================================================
-- WebGIS Pontianak - Database Lengkap
-- ============================================================
-- Import file ini jika ingin membuat ulang database dari awal.
-- Perhatian: tabel lama dengan nama yang sama akan dihapus.

-- Database selected via connection string

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ulasan_fasilitas;
DROP TABLE IF EXISTS laporan_warga;
DROP TABLE IF EXISTS warga_miskin;
DROP TABLE IF EXISTS kawasan_kumuh;
DROP TABLE IF EXISTS kavling;
DROP TABLE IF EXISTS jalan;
DROP TABLE IF EXISTS rumah_ibadah;
DROP TABLE IF EXISTS spbu;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. Users
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    nama_lengkap VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, password, role, nama_lengkap) VALUES
    (
        'admin',
        '$2y$12$oRvX1EtLHM/y8XtwlOy./Oi2UVpnHp98GvXqEuLCFimhr0doKus62',
        'admin',
        'Administrator WebGIS'
    ),
    (
        'pengguna',
        '$2y$12$djJkmhJBRCSCqqpAICIvGuLoVpICrDUS2IVECbZUodgR89VRTqdLW',
        'user',
        'Pengguna Publik'
    );

-- ============================================================
-- 2. Data Spasial
-- ============================================================
CREATE TABLE spbu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    buka_24_jam TINYINT(1) NOT NULL DEFAULT 0,
    geom GEOMETRY NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SPATIAL INDEX idx_spbu_geom (geom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO spbu (nama, deskripsi, buka_24_jam, geom) VALUES
    (
        'SPBU COCO 61.781.01 Ahmad Yani',
        'Jl. Jend. Ahmad Yani No.3, Pontianak (Dekat Bundaran Digulis Untan)',
        1,
        ST_GeomFromText('POINT(109.344381 -0.056461)')
    ),
    (
        'SPBU 64.781.11 Imam Bonjol',
        'Jl. Imam Bonjol No.30, Pontianak Tenggara',
        1,
        ST_GeomFromText('POINT(109.349692 -0.038148)')
    ),
    (
        'SPBU 64.781.06 Kota Baru',
        'Jl. Prof. Dr. M. Yamin, Kota Baru, Pontianak Selatan',
        1,
        ST_GeomFromText('POINT(109.317589 -0.054378)')
    ),
    (
        'SPBU 64.781.01 Adi Sucipto',
        'Jl. Adi Sucipto, Pontianak Tenggara',
        0,
        ST_GeomFromText('POINT(109.362241 -0.061730)')
    ),
    (
        'SPBU 64.781.03 KH. Ahmad Dahlan',
        'Jl. KH. Ahmad Dahlan No.12, Pontianak Kota',
        0,
        ST_GeomFromText('POINT(109.333830 -0.033621)')
    ),
    (
        'SPBU 64.781.05 Tanjung Pura',
        'Jl. Tanjung Pura, Pontianak Selatan',
        1,
        ST_GeomFromText('POINT(109.336184 -0.026788)')
    ),
    (
        'SPBU 64.781.12 Gusti Hamzah',
        'Jl. Gusti Hamzah (Pancasila), Pontianak Kota',
        0,
        ST_GeomFromText('POINT(109.319760 -0.029415)')
    ),
    (
        'SPBU 64.781.14 Husein Hamzah',
        'Jl. Husein Hamzah (Pal 3), Pontianak Barat',
        0,
        ST_GeomFromText('POINT(109.294121 -0.027963)')
    ),
    (
        'SPBU 64.781.15 RE Martadinata',
        'Jl. RE Martadinata, Pontianak Barat',
        0,
        ST_GeomFromText('POINT(109.314227 -0.016335)')
    ),
    (
        'SPBU 63.781.02 28 Oktober',
        'Jl. 28 Oktober, Pontianak Utara',
        0,
        ST_GeomFromText('POINT(109.366405 0.007621)')
    ),
    (
        'SPBU 64.782.01 Khatulistiwa',
        'Jl. Khatulistiwa, Pontianak Utara',
        1,
        ST_GeomFromText('POINT(109.325983 0.035415)')
    ),
    (
        'SPBU OSO MT. Haryono',
        'Jl. Letjen MT Haryono, Pontianak Selatan',
        0,
        ST_GeomFromText('POINT(109.3367485 -0.0448924)')
    ),
    (
        'SPBU 64.781.18 Danau Sentarum',
        'Jl. Danau Sentarum, Pontianak Kota',
        0,
        ST_GeomFromText('POINT(109.311283 -0.046342)')
    ),
    (
        'SPBU 64.781.19 Serdam',
        'Jl. Sungai Raya Dalam, Pontianak Tenggara',
        0,
        ST_GeomFromText('POINT(109.358241 -0.076389)')
    ),
    (
        'SPBU 64.781.02 Hasanuddin',
        'Jl. Hasanuddin, Pontianak Barat',
        0,
        ST_GeomFromText('POINT(109.321873 -0.024502)')
    ),
    (
        'SPBU 64.781.21 Dr. Wahidin',
        'Jl. Dr. Wahidin S., Pontianak Kota',
        0,
        ST_GeomFromText('POINT(109.308210 -0.034502)')
    ),
    (
        'SPBU 64.781.17 Komyos Sudarso',
        'Jl. Komodor Yos Sudarso, Pontianak Barat',
        0,
        ST_GeomFromText('POINT(109.299100 -0.019500)')
    ),
    (
        'SPBU 64.781.19 HOS Cokroaminoto',
        'Jl. HOS Cokroaminoto, Pontianak Kota',
        1,
        ST_GeomFromText('POINT(109.337500 -0.031500)')
    );

CREATE TABLE rumah_ibadah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    agama VARCHAR(50) NOT NULL,
    radius_bantuan_meter INT NOT NULL DEFAULT 1000,
    geom GEOMETRY NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SPATIAL INDEX idx_rumah_ibadah_geom (geom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO rumah_ibadah (nama, agama, radius_bantuan_meter, geom) VALUES
    (
        'Masjid Raya Mujahidin',
        'Islam',
        1000,
        ST_GeomFromText('POINT(109.3377 -0.0414)')
    ),
    (
        'Masjid Jami Sultan Syarif Abdurrahman',
        'Islam',
        1000,
        ST_GeomFromText('POINT(109.3522 -0.0229)')
    ),
    (
        'Gereja Katedral Santo Yosef',
        'Katolik',
        1000,
        ST_GeomFromText('POINT(109.3384 -0.0274)')
    ),
    (
        'Gereja Kristen Immanuel (GPIB)',
        'Kristen',
        1000,
        ST_GeomFromText('POINT(109.3375 -0.0292)')
    ),
    (
        'Gereja GKKB Pontianak',
        'Kristen',
        1000,
        ST_GeomFromText('POINT(109.3386 -0.0336)')
    ),
    (
        'Vihara Bodhisatva Karaniya Metta',
        'Buddha',
        1000,
        ST_GeomFromText('POINT(109.3432 -0.0215)')
    ),
    (
        'Maha Vihara Maitreya',
        'Buddha',
        1000,
        ST_GeomFromText('POINT(109.3649 -0.0732)')
    ),
    (
        'Pura Giripati Mulawarman',
        'Hindu',
        1000,
        ST_GeomFromText('POINT(109.3802 -0.0663)')
    );

CREATE TABLE jalan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jenis_jalan VARCHAR(50) DEFAULT NULL,
    geom GEOMETRY NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SPATIAL INDEX idx_jalan_geom (geom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO jalan (nama, jenis_jalan, geom) VALUES
    (
        'Jalan Jenderal Ahmad Yani',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3621 -0.0768, 109.3444 -0.0565, 109.3360 -0.0450, 109.3330 -0.0330)')
    ),
    (
        'Jalan Gajah Mada',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3310 -0.0320, 109.3380 -0.0290, 109.3450 -0.0250)')
    ),
    (
        'Jalan Tanjung Pura',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3300 -0.0300, 109.3360 -0.0260, 109.3480 -0.0240, 109.3550 -0.0270)')
    ),
    (
        'Jalan Imam Bonjol',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3360 -0.0450, 109.3420 -0.0400, 109.3500 -0.0350, 109.3590 -0.0400)')
    ),
    (
        'Jalan Adi Sucipto',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3590 -0.0400, 109.3650 -0.0600, 109.3750 -0.0700)')
    ),
    (
        'Jalan Khatulistiwa',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3450 0.0050, 109.3350 0.0150, 109.3250 0.0350, 109.3150 0.0550)')
    ),
    (
        'Jalan Komodor Yos Sudarso',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3250 -0.0280, 109.3150 -0.0240, 109.3000 -0.0180, 109.2850 -0.0150)')
    ),
    (
        'Jalan Prof. M. Yamin',
        'Arteri Primer',
        ST_GeomFromText('LINESTRING(109.3245 -0.0487, 109.3170 -0.0540, 109.3050 -0.0620)')
    );

CREATE TABLE kavling (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemilik VARCHAR(100) NOT NULL,
    status_kepemilikan VARCHAR(50) NOT NULL,
    luas DECIMAL(10,2) DEFAULT NULL,
    geom GEOMETRY NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SPATIAL INDEX idx_kavling_geom (geom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO kavling (nama_pemilik, status_kepemilikan, luas, geom) VALUES
    (
        'PT. Mega Karya',
        'HGB',
        25000.00,
        ST_GeomFromText('POLYGON((109.333 -0.048, 109.338 -0.048, 109.338 -0.053, 109.333 -0.053, 109.333 -0.048))')
    ),
    (
        'Bapak Sudirman',
        'SHM',
        1500.00,
        ST_GeomFromText('POLYGON((109.340 -0.035, 109.341 -0.035, 109.341 -0.036, 109.340 -0.036, 109.340 -0.035))')
    );

CREATE TABLE kawasan_kumuh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kawasan VARCHAR(100) NOT NULL,
    geom GEOMETRY NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SPATIAL INDEX idx_kawasan_kumuh_geom (geom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO kawasan_kumuh (nama_kawasan, geom) VALUES
    (
        'Kawasan Rawan Bantaran Sungai',
        ST_GeomFromText('POLYGON((109.360 -0.050, 109.365 -0.050, 109.365 -0.055, 109.360 -0.055, 109.360 -0.050))')
    ),
    (
        'Kawasan Padat Parit Tokaya',
        ST_GeomFromText('POLYGON((109.342 -0.038, 109.346 -0.038, 109.346 -0.042, 109.342 -0.042, 109.342 -0.038))')
    );

CREATE TABLE warga_miskin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kk VARCHAR(100) NOT NULL,
    penghasilan DECIMAL(15,2) NOT NULL,
    jumlah_tanggungan INT NOT NULL,
    geom GEOMETRY NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SPATIAL INDEX idx_warga_miskin_geom (geom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO warga_miskin (nama_kk, penghasilan, jumlah_tanggungan, geom) VALUES
    (
        'Bpk. Budi (Terisolasi)',
        800000.00,
        4,
        ST_GeomFromText('POINT(109.361 -0.051)')
    ),
    (
        'Ibu Siti (Terisolasi)',
        600000.00,
        2,
        ST_GeomFromText('POINT(109.362 -0.052)')
    ),
    (
        'Bpk. Joko (Terisolasi)',
        1000000.00,
        5,
        ST_GeomFromText('POINT(109.363 -0.053)')
    ),
    (
        'Ibu Ani (Terisolasi)',
        700000.00,
        3,
        ST_GeomFromText('POINT(109.364 -0.054)')
    ),
    (
        'Bpk. Hasan',
        1200000.00,
        3,
        ST_GeomFromText('POINT(109.334 -0.050)')
    ),
    (
        'Mbah Warno',
        400000.00,
        1,
        ST_GeomFromText('POINT(109.336 -0.046)')
    ),
    (
        'Pak Junaidi',
        900000.00,
        4,
        ST_GeomFromText('POINT(109.344 -0.040)')
    );

-- ============================================================
-- 3. Data Interaksi Pengguna
-- ============================================================
CREATE TABLE laporan_warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT NOT NULL,
    geometry POINT NOT NULL,
    status ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'menunggu',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_laporan_user_id (user_id),
    SPATIAL INDEX idx_laporan_geometry (geometry),
    CONSTRAINT fk_laporan_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ulasan_fasilitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fasilitas_tipe ENUM('spbu', 'rumah_ibadah') NOT NULL,
    fasilitas_id INT NOT NULL,
    rating TINYINT NOT NULL,
    komentar TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ulasan_user_id (user_id),
    INDEX idx_ulasan_fasilitas (fasilitas_tipe, fasilitas_id),
    CONSTRAINT fk_ulasan_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT chk_ulasan_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
