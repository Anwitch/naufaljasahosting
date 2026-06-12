<?php
/**
 * area_blank.php
 * Endpoint: Blank Spot — area tanpa sinyal seluler.
 * Menggunakan tabel kawasan_kumuh sebagai proxy (tidak ada tabel blank_spot tersendiri).
 * Mengembalikan FeatureCollection kosong jika tabel belum ada.
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
            // Check if blank_spot table exists, otherwise return empty collection
            $check = $pdo->query("SHOW TABLES LIKE 'blank_spot'")->rowCount();
            if ($check === 0) {
                sendSuccess(
                    ['type' => 'FeatureCollection', 'features' => []],
                    'Tabel blank_spot belum tersedia'
                );
            }
            $stmt = $pdo->query(
                "SELECT id, nama, keterangan, created_at, ST_AsGeoJSON(geom) AS geojson FROM blank_spot"
            );
            $features = [];
            while ($row = $stmt->fetch()) {
                $features[] = [
                    'type'       => 'Feature',
                    'geometry'   => json_decode($row['geojson']),
                    'properties' => [
                        'id'          => $row['id'],
                        'nama'        => $row['nama'],
                        'keterangan'  => $row['keterangan'],
                        'created_at'  => $row['created_at'],
                    ],
                ];
            }
            sendSuccess(['type' => 'FeatureCollection', 'features' => $features], 'Data Blank Spot berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil data Blank Spot: ' . $e->getMessage(), 500);
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
                "INSERT INTO blank_spot (nama, keterangan, geom)
                 VALUES (:nama, :keterangan, ST_GeomFromGeoJSON(:geometry))"
            );
            $stmt->execute([
                ':nama'       => $input['nama'],
                ':keterangan' => $input['keterangan'] ?? '',
                ':geometry'   => json_encode($input['geometry']),
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Data Blank Spot berhasil disimpan', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan Blank Spot: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID Blank Spot wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM blank_spot WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Data Blank Spot berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus Blank Spot: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
