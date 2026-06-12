<?php
/**
 * cek_srid.php
 * Utility: Cek SRID (Spatial Reference ID) dari semua kolom geometri.
 * Hanya untuk Admin / debugging.
 */

require_once __DIR__ . '/../core_config/database.php';
require_once __DIR__ . '/../core_config/middleware_auth.php';

header('Content-Type: application/json; charset=utf-8');

requireApiRole('admin');

$pdo = Database::getConnection();

try {
    $tables = ['spbu', 'rumah_ibadah', 'jalan', 'kavling', 'kawasan_kumuh', 'warga_miskin'];
    $results = [];

    foreach ($tables as $table) {
        $stmt = $pdo->query(
            "SELECT ST_SRID(geom) AS srid, COUNT(*) AS jumlah
             FROM `{$table}`
             GROUP BY ST_SRID(geom)"
        );
        $results[$table] = $stmt->fetchAll();
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $results]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
