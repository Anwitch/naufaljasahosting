<?php
/**
 * perbaiki_srid.php
 * Utility: Perbaiki SRID semua geometri ke SRID 0 (tidak terdefinisi, kompatibel dengan MySQL).
 * Hanya untuk Admin / debugging.
 */

require_once __DIR__ . '/../core_config/database.php';
require_once __DIR__ . '/../core_config/middleware_auth.php';

header('Content-Type: application/json; charset=utf-8');

requireApiRole('admin');

$pdo = Database::getConnection();

try {
    $tables = ['spbu', 'rumah_ibadah', 'jalan', 'kavling', 'kawasan_kumuh', 'warga_miskin'];
    $updated = [];

    foreach ($tables as $table) {
        $affected = $pdo->exec(
            "UPDATE `{$table}` SET geom = ST_GeomFromWKB(ST_AsWKB(geom), 0) WHERE ST_SRID(geom) != 0"
        );
        $updated[$table] = ['rows_updated' => $affected];
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'SRID diperbaiki', 'data' => $updated]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
