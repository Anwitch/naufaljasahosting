<?php
/**
 * api_warga_miskin.php
 * Endpoint: CRUD untuk layer Warga Miskin (Point).
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
                "SELECT id, nama_kk, penghasilan, jumlah_tanggungan, created_at,
                        ST_AsGeoJSON(geom) AS geojson FROM warga_miskin"
            );
            $features = [];
            while ($row = $stmt->fetch()) {
                $features[] = [
                    'type'       => 'Feature',
                    'geometry'   => json_decode($row['geojson']),
                    'properties' => [
                        'id'                 => $row['id'],
                        'nama_kk'            => $row['nama_kk'],
                        'penghasilan'        => $row['penghasilan'],
                        'jumlah_tanggungan'  => $row['jumlah_tanggungan'],
                        'created_at'         => $row['created_at'],
                    ],
                ];
            }
            sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'Data Warga Miskin berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil data Warga Miskin: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        requireApiRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['nama_kk']) || !isset($input['geometry'])) {
            sendError('Nama KK dan geometri wajib diisi');
        }
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO warga_miskin (nama_kk, penghasilan, jumlah_tanggungan, geom)
                 VALUES (:nama_kk, :penghasilan, :jumlah_tanggungan, ST_GeomFromGeoJSON(:geometry))"
            );
            $stmt->execute([
                ':nama_kk'           => $input['nama_kk'],
                ':penghasilan'       => $input['penghasilan'] ?? 0,
                ':jumlah_tanggungan' => $input['jumlah_tanggungan'] ?? 0,
                ':geometry'          => json_encode($input['geometry']),
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Data Warga Miskin berhasil disimpan', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan Warga Miskin: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID Warga Miskin wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM warga_miskin WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Data Warga Miskin berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus Warga Miskin: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
