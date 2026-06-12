<?php
/**
 * api_pengguna.php
 * Endpoint: Manajemen data Pengguna (hanya Admin).
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

requireApiRole('admin');

$pdo    = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query(
                "SELECT id, username, role, nama_lengkap, created_at FROM users ORDER BY created_at DESC"
            );
            sendSuccess($stmt->fetchAll(), 'Data Pengguna berhasil diambil');
        } catch (PDOException $e) {
            sendError('Gagal mengambil data pengguna: ' . $e->getMessage(), 500);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['username'], $input['password'])) {
            sendError('Username dan password wajib diisi');
        }
        try {
            $hash = password_hash($input['password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, password, role, nama_lengkap)
                 VALUES (:username, :password, :role, :nama_lengkap)"
            );
            $stmt->execute([
                ':username'    => $input['username'],
                ':password'    => $hash,
                ':role'        => $input['role'] ?? 'user',
                ':nama_lengkap'=> $input['nama_lengkap'] ?? null,
            ]);
            sendSuccess(['id' => $pdo->lastInsertId()], 'Pengguna berhasil dibuat', 201);
        } catch (PDOException $e) {
            sendError('Gagal membuat pengguna: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) sendError('ID pengguna wajib disertakan');
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendSuccess(null, 'Pengguna berhasil dihapus');
        } catch (PDOException $e) {
            sendError('Gagal menghapus pengguna: ' . $e->getMessage(), 500);
        }
        break;

    default:
        sendError('Method not allowed', 405);
}
