<?php
/**
 * api_statistik.php
 * Endpoint: Mengembalikan ringkasan statistik keseluruhan data WebGIS.
 */

require_once __DIR__ . '/../core_config/database.php';

header('Content-Type: application/json; charset=utf-8');

function sendSuccess($data = null, $message = 'Success', $code = 200) {
    http_response_code($code);
    echo json_encode(['status' => 'success', 'message' => $message, 'data' => $data]);
    exit();
}
function sendError($message = 'Error', $code = 400) {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit();
}

$pdo    = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') { sendError('Method not allowed', 405); }

try {
    $counts = [];

    $tables = [
        'spbu'          => 'total_spbu',
        'rumah_ibadah'  => 'total_rumah_ibadah',
        'jalan'         => 'total_jalan',
        'kavling'       => 'total_kavling',
        'kawasan_kumuh' => 'total_kawasan_kumuh',
        'warga_miskin'  => 'total_warga_miskin',
        'laporan_warga' => 'total_laporan',
        'users'         => 'total_users',
    ];

    foreach ($tables as $table => $key) {
        $row = $pdo->query("SELECT COUNT(*) AS cnt FROM `{$table}`")->fetch();
        $counts[$key] = (int) $row['cnt'];
    }

    // Additional SPBU breakdown
    $spbuStatus = $pdo->query(
        "SELECT SUM(buka_24_jam=1) AS buka_24_jam, SUM(buka_24_jam=0) AS tidak_24_jam FROM spbu"
    )->fetch();
    $counts['spbu_buka_24_jam']   = (int) $spbuStatus['buka_24_jam'];
    $counts['spbu_tidak_24_jam']  = (int) $spbuStatus['tidak_24_jam'];

    // Laporan by status
    $laporanStatus = $pdo->query(
        "SELECT status, COUNT(*) AS cnt FROM laporan_warga GROUP BY status"
    )->fetchAll();
    $counts['laporan_by_status'] = [];
    foreach ($laporanStatus as $row) {
        $counts['laporan_by_status'][$row['status']] = (int) $row['cnt'];
    }

    sendSuccess($counts, 'Statistik berhasil diambil');
} catch (PDOException $e) {
    sendError('Gagal mengambil statistik: ' . $e->getMessage(), 500);
}
