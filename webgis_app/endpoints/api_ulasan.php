<?php
/**
 * api_ulasan.php
 * Endpoint: CRUD untuk Ulasan Fasilitas (SPBU / Rumah Ibadah).
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
        $tipe = $_GET['tipe'] ?? null;
        $fid  = $_GET['fasilitas_id'] ?? null;
        try {
            if ($tipe && $fid) {
                $stmt = $pdo->prepare(
                    "SELECT uf.id, uf.user_id, u.username, uf.fasilitas_tipe,
                            uf.fasilitas_id, uf.rating, uf.komentar, uf.created_at
                     FROM ulasan_fasilitas uf
                     JOIN users u ON u.id = uf.user_id
                     WHERE uf.fasilitas_tipe = :tipe AND uf.fasilitas_id = :fid
                     ORDER BY uf.created_at DESC"
                );
                $stmt->execute([':tipe' => $tipe, ':fid' => $fid]);
            } else {
                $stmt = $pdo->query(
                    "SELECT uf.id, uf.user_id, u.username, uf.fasilitas_tipe,
                            uf.fasilitas_id, uf.rating, uf.komentar, uf.created_at
                     FROM ulasan_fasilitas uf
                     JOIN users u ON u.id = uf.user_id
                     ORDER BY uf.created_at DESC"
                );
            }
            sendSuccess($stmt->fetchAll(), 'Data Ulasan berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil ulasan: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        requireApiLogin();
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['fasilitas_tipe'], $input['fasilitas_id'], $input['rating'])) {
            sendError('fasilitas_tipe, fasilitas_id, dan rating wajib diisi');
        }
        $allowed = ['spbu', 'rumah_ibadah'];
        if (!in_array($input['fasilitas_tipe'], $allowed)) sendError('fasilitas_tipe tidak valid');
        if ($input['rating'] < 1 || $input['rating'] > 5) sendError('Rating harus antara 1-5');
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO ulasan_fasilitas (user_id, fasilitas_tipe, fasilitas_id, rating, komentar)
                 VALUES (:user_id, :fasilitas_tipe, :fasilitas_id, :rating, :komentar)"
            );
            $stmt->execute([
                ':user_id'       => $_SESSION['user_id'],
                ':fasilitas_tipe'=> $input['fasilitas_tipe'],
                ':fasilitas_id'  => $input['fasilitas_id'],
                ':rating'        => $input['rating'],
                ':komentar'      => $input['komentar'] ?? null,
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Ulasan berhasil disimpan', 201);
        } catch (PDOException $e) {
            sendError('Gagal menyimpan ulasan: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        requireApiRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID ulasan wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM ulasan_fasilitas WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Ulasan berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus ulasan: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
