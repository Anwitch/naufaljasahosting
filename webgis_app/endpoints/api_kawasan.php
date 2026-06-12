<?php
/**
 * api_kawasan.php
 * Endpoint: CRUD untuk layer Kawasan Kumuh (Polygon).
 */

require_once __DIR__ . '/../core_config/database.php';
require_once __DIR__ . '/../core_config/middleware_auth.php';

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

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query(
                "SELECT id, nama_kawasan, created_at, ST_AsGeoJSON(geom) AS geojson FROM kawasan_kumuh"
            );
            $features = [];
            while ($row = $stmt->fetch()) {
                $features[] = [
                    'type'       => 'Feature',
                    'geometry'   => json_decode($row['geojson']),
                    'properties' => [
                        'id'           => $row['id'],
                        'nama_kawasan' => $row['nama_kawasan'],
                        'created_at'   => $row['created_at'],
                    ],
                ];
            }
            sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'Data Kawasan Kumuh berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil data Kawasan Kumuh: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        requireApiRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['nama_kawasan']) || !isset($input['geometry'])) {
            sendError('Nama kawasan dan geometri wajib diisi');
        }
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO kawasan_kumuh (nama_kawasan, geom)
                 VALUES (:nama_kawasan, ST_GeomFromGeoJSON(:geometry))"
            );
            $stmt->execute([
                ':nama_kawasan' => $input['nama_kawasan'],
                ':geometry'     => json_encode($input['geometry']),
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Data Kawasan Kumuh berhasil disimpan', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan Kawasan Kumuh: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID Kawasan Kumuh wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM kawasan_kumuh WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Data Kawasan Kumuh berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus Kawasan Kumuh: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
