<?php
/**
 * api_fasilitas.php
 * Endpoint: CRUD untuk layer Rumah Ibadah (Point).
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
                "SELECT id, nama, agama, radius_bantuan_meter, created_at,
                        ST_AsGeoJSON(geom) AS geojson FROM rumah_ibadah"
            );
            $features = [];
            while ($row = $stmt->fetch()) {
                $features[] = [
                    'type'       => 'Feature',
                    'geometry'   => json_decode($row['geojson']),
                    'properties' => [
                        'id'                   => $row['id'],
                        'nama'                 => $row['nama'],
                        'agama'                => $row['agama'],
                        'radius_bantuan_meter' => $row['radius_bantuan_meter'],
                        'created_at'           => $row['created_at'],
                    ],
                ];
            }
            sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'Data Rumah Ibadah berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil data Rumah Ibadah: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        requireApiRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['nama']) || !isset($input['agama']) || !isset($input['geometry'])) {
            sendError('Nama, agama, dan geometri wajib diisi');
        }
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO rumah_ibadah (nama, agama, radius_bantuan_meter, geom)
                 VALUES (:nama, :agama, :radius_bantuan_meter, ST_GeomFromGeoJSON(:geometry))"
            );
            $stmt->execute([
                ':nama'                 => $input['nama'],
                ':agama'                => $input['agama'],
                ':radius_bantuan_meter' => $input['radius_bantuan_meter'] ?? 1000,
                ':geometry'             => json_encode($input['geometry']),
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Data Rumah Ibadah berhasil disimpan', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan Rumah Ibadah: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID Rumah Ibadah wajib disertakan');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) sendError('Data tidak valid');
        try {
            $stmt = $pdo->prepare(
                "UPDATE rumah_ibadah SET nama = :nama, agama = :agama, radius_bantuan_meter = :radius_bantuan_meter WHERE id = :id"
            );
            $stmt->execute([
                ':nama'                 => $input['nama'],
                ':agama'                => $input['agama'],
                ':radius_bantuan_meter' => $input['radius_bantuan_meter'] ?? 1000,
                ':id'                   => $id,
            ]);
            sendSuccess(null, 'Data Rumah Ibadah berhasil diperbarui');
        } catch (PDOException $e) {
            sendError('Gagal memperbarui Rumah Ibadah: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID Rumah Ibadah wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM rumah_ibadah WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Data Rumah Ibadah berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus Rumah Ibadah: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
