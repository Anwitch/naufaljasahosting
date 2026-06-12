<?php
/**
 * api_spbu_nearby.php
 * Endpoint: Mencari SPBU terdekat dari titik koordinat pengguna.
 * Query params: lat, lng, radius (meter, default 5000)
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

$lat    = isset($_GET['lat'])    ? (float) $_GET['lat']    : null;
$lng    = isset($_GET['lng'])    ? (float) $_GET['lng']    : null;
$radius = isset($_GET['radius']) ? (int)   $_GET['radius'] : 5000;

if ($lat === null || $lng === null) { sendError('Parameter lat dan lng wajib disertakan'); }

try {
    $stmt = $pdo->prepare(
        "SELECT id, nama, deskripsi, buka_24_jam, created_at,
                ST_AsGeoJSON(geom) AS geojson,
                ST_Distance_Sphere(geom, ST_GeomFromText(:point)) AS jarak_meter
         FROM spbu
         WHERE ST_Distance_Sphere(geom, ST_GeomFromText(:point)) <= :radius
         ORDER BY jarak_meter ASC"
    );
    $stmt->execute([':point' => "POINT({$lng} {$lat})", ':radius' => $radius]);
    $features = [];
    while ($row = $stmt->fetch()) {
        $features[] = [
            'type'       => 'Feature',
            'geometry'   => json_decode($row['geojson']),
            'properties' => [
                'id'          => $row['id'],
                'nama'        => $row['nama'],
                'deskripsi'   => $row['deskripsi'],
                'buka_24_jam' => (bool) $row['buka_24_jam'],
                'jarak_meter' => round($row['jarak_meter'], 2),
                'jarak_km'    => round($row['jarak_meter'] / 1000, 2),
                'created_at'  => $row['created_at'],
            ],
        ];
    }
    sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'SPBU Terdekat berhasil diambil');
} catch (PDOException $e) {
    sendError('Gagal mengambil SPBU Terdekat: ' . $e->getMessage(), 500);
}
