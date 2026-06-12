<?php
/**
 * api_kavling.php
 * Endpoint: CRUD untuk layer Kavling (Polygon).
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
                "SELECT id, nama_pemilik, status_kepemilikan, luas, created_at,
                        ST_AsGeoJSON(geom) AS geojson FROM kavling"
            );
            $features = [];
            while ($row = $stmt->fetch()) {
                $features[] = [
                    'type'       => 'Feature',
                    'geometry'   => json_decode($row['geojson']),
                    'properties' => [
                        'id'                   => $row['id'],
                        'nama_pemilik'          => $row['nama_pemilik'],
                        'status_kepemilikan'    => $row['status_kepemilikan'],
                        'luas'                 => $row['luas'],
                        'created_at'           => $row['created_at'],
                    ],
                ];
            }
            sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'Data Kavling berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil data Kavling: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        requireApiRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['nama_pemilik']) || !isset($input['geometry'])) {
            sendError('Nama pemilik dan geometri wajib diisi');
        }
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO kavling (nama_pemilik, status_kepemilikan, luas, geom)
                 VALUES (:nama_pemilik, :status_kepemilikan, :luas, ST_GeomFromGeoJSON(:geometry))"
            );
            $stmt->execute([
                ':nama_pemilik'       => $input['nama_pemilik'],
                ':status_kepemilikan' => $input['status_kepemilikan'] ?? 'SHM',
                ':luas'               => $input['luas'] ?? 0,
                ':geometry'           => json_encode($input['geometry']),
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Data Kavling berhasil disimpan', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan Kavling: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID Kavling wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM kavling WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Data Kavling berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus Kavling: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
