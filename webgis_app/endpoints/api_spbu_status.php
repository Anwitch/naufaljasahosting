<?php
/**
 * api_spbu_status.php
 * Endpoint: Mengambil ringkasan status SPBU (berapa 24 jam, berapa tidak).
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

if ($method !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    $stmt = $pdo->query(
        "SELECT
            COUNT(*) AS total,
            SUM(buka_24_jam = 1) AS buka_24_jam,
            SUM(buka_24_jam = 0) AS tidak_24_jam
         FROM spbu"
    );
    $summary = $stmt->fetch();
    sendSuccess($summary, 'Status SPBU berhasil diambil');
} catch (PDOException $e) {
    sendError('Gagal mengambil status SPBU: ' . $e->getMessage(), 500);
}
