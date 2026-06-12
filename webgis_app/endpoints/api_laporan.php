<?php
/**
 * api_laporan.php
 * Endpoint: CRUD untuk Laporan Warga.
 */

require_once __DIR__ . '/../core_config/database.php';
require_once __DIR__ . '/../core_config/middleware_auth.php';
require_once __DIR__ . '/../core_config/session_mgr.php';

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

startAppSession();
$pdo    = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        requireApiLogin();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $role   = $_SESSION['role']    ?? 'user';

            if ($role === 'admin') {
                $stmt = $pdo->query(
                    "SELECT lw.id, lw.user_id, u.username, lw.kategori, lw.deskripsi,
                            lw.status, lw.created_at, ST_AsGeoJSON(lw.geometry) AS geojson
                     FROM laporan_warga lw
                     JOIN users u ON u.id = lw.user_id
                     ORDER BY lw.created_at DESC"
                );
            } else {
                $stmt = $pdo->prepare(
                    "SELECT id, user_id, kategori, deskripsi, status, created_at,
                            ST_AsGeoJSON(geometry) AS geojson
                     FROM laporan_warga WHERE user_id = :uid ORDER BY created_at DESC"
                );
                $stmt->execute([':uid' => $userId]);
            }

            $rows = $stmt->fetchAll();
            foreach ($rows as &$r) {
                $r['geojson'] = json_decode($r['geojson']);
            }
            sendSuccess($rows, 'Data Laporan berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil laporan: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        requireApiLogin();
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['kategori']) || !isset($input['deskripsi']) || !isset($input['geometry'])) {
            sendError('Kategori, deskripsi, dan geometri wajib diisi');
        }
        $userId = $_SESSION['user_id'];
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO laporan_warga (user_id, kategori, deskripsi, geometry)
                 VALUES (:user_id, :kategori, :deskripsi, ST_GeomFromGeoJSON(:geometry))"
            );
            $stmt->execute([
                ':user_id'  => $userId,
                ':kategori' => $input['kategori'],
                ':deskripsi'=> $input['deskripsi'],
                ':geometry' => json_encode($input['geometry']),
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Laporan berhasil dikirim', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan laporan: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        requireApiRole('admin');
        $input = json_decode(file_get_contents('php://input'), true);
        $id    = $_GET['id'] ?? null;
        if (!$id || !isset($input['status'])) sendError('ID dan status wajib disertakan');
        $allowed = ['menunggu', 'diproses', 'selesai', 'ditolak'];
        if (!in_array($input['status'], $allowed)) sendError('Status tidak valid');
        try {
            $stmt = $pdo->prepare("UPDATE laporan_warga SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $input['status'], ':id' => $id]);
            sendSuccess(null, 'Status laporan berhasil diperbarui');
        } catch (PDOException $e) {
            sendError('Gagal memperbarui laporan: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID laporan wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM laporan_warga WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Laporan berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus laporan: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
