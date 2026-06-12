<?php
/**
 * api_spbu.php
 * Endpoint: CRUD untuk layer SPBU (Point).
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
                "SELECT id, nama, deskripsi, buka_24_jam, created_at,
                        ST_AsGeoJSON(geom) AS geojson FROM spbu"
            );
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
                        'created_at'  => $row['created_at'],
                    ],
                ];
            }
            sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'Data SPBU berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil data SPBU: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        requireApiRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['nama']) || !isset($input['geometry'])) {
            sendError('Nama dan geometri wajib diisi');
        }
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO spbu (nama, deskripsi, buka_24_jam, geom)
                 VALUES (:nama, :deskripsi, :buka_24_jam, ST_GeomFromGeoJSON(:geometry))"
            );
            $stmt->execute([
                ':nama'        => $input['nama'],
                ':deskripsi'   => $input['deskripsi'] ?? '',
                ':buka_24_jam' => isset($input['buka_24_jam']) && $input['buka_24_jam'] ? 1 : 0,
                ':geometry'    => json_encode($input['geometry']),
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Data SPBU berhasil disimpan', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan SPBU: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID SPBU wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM spbu WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Data SPBU berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus SPBU: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
