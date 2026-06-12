<?php
/**
 * auth_check.php — Middleware: pastikan user sudah login dan role sesuai.
 * Usage: require_once __DIR__ . '/../core_config/middleware_auth.php';
 *        requireRole('admin'); // atau 'user'
 */
require_once __DIR__ . '/session_mgr.php';

if (session_status() === PHP_SESSION_NONE) {
    startAppSession();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireLogin(?string $redirect = null): void {
    if (!isLoggedIn()) {
        $redirect = $redirect ?? app_url('login.php');
        header("Location: $redirect");
        exit;
    }
}

function requireRole(string $role, ?string $redirect = null): void {
    requireLogin($redirect);
    if ($_SESSION['role'] !== $role) {
        // Redirect ke dashboard yang sesuai role-nya
        if ($_SESSION['role'] === 'admin') {
            header('Location: ' . app_url('panel_admin/index.php'));
        } else {
            header('Location: ' . app_url('panel_user/index.php'));
        }
        exit;
    }
}

function currentUser(): array {
    return [
        'id'          => $_SESSION['user_id']    ?? null,
        'username'    => $_SESSION['username']   ?? '',
        'role'        => $_SESSION['role']        ?? '',
        'nama_lengkap'=> $_SESSION['nama_lengkap']?? '',
    ];
}

/**
 * API-safe auth helpers: return JSON 401/403 instead of redirecting.
 * Use these in endpoints called via fetch/AJAX.
 */
function requireApiLogin(): void {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Autentikasi diperlukan']);
        exit;
    }
}

function requireApiRole(string $role): void {
    requireApiLogin();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak: role tidak sesuai']);
        exit;
    }
}
