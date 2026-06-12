<?php
require_once __DIR__ . '/core_config/session_mgr.php';
startAppSession();

if (isset($_SESSION['user_id'])) {
    header($_SESSION['role'] === 'admin' ? 'Location: panel_admin/index.php' : 'Location: panel_user/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/core_config/database.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $pdo  = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['role']         = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            header($user['role'] === 'admin' ? 'Location: panel_admin/index.php' : 'Location: panel_user/index.php');
            exit;
        }

        $error = 'Username atau password salah. Silakan coba lagi.';
    } else {
        $error = 'Username dan password wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — WebGIS Smart City</title>
    <meta name="description" content="Masuk ke sistem WebGIS Smart City Pontianak.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ══ DESIGN TOKENS ══ */
        :root {
            --font-sans: 'Inter', sans-serif;
            --font-display: 'Space Grotesk', sans-serif;
            --ease: cubic-bezier(0.4, 0, 0.2, 1);
            --transition: all 0.25s var(--ease);
        }

        [data-theme="dark"] {
            --bg-page:      #0A0F1E;
            --bg-card:      #111827;
            --bg-input:     #1A2234;
            --border:       rgba(255,255,255,0.08);
            --border-focus: #6366F1;
            --text-primary: #F1F5F9;
            --text-secondary:#94A3B8;
            --text-muted:   #64748B;
            --primary:      #6366F1;
            --primary-dark: #4F46E5;
            --primary-glow: rgba(99,102,241,0.25);
            --accent:       #06B6D4;
            --error-bg:     rgba(244,63,94,0.12);
            --error-border: rgba(244,63,94,0.3);
            --error-text:   #FB7185;
            --role-border:  rgba(255,255,255,0.08);
            --role-active-bg: rgba(99,102,241,0.15);
            --role-active-border: #6366F1;
            --shadow-card:  0 8px 48px rgba(0,0,0,0.5);
            --dot-color:    rgba(255,255,255,0.03);
        }

        [data-theme="light"] {
            --bg-page:      #EEF2FF;
            --bg-card:      #FFFFFF;
            --bg-input:     #F8FAFF;
            --border:       #E2E8F0;
            --border-focus: #4F46E5;
            --text-primary: #0F172A;
            --text-secondary:#475569;
            --text-muted:   #94A3B8;
            --primary:      #4F46E5;
            --primary-dark: #3730A3;
            --primary-glow: rgba(79,70,229,0.12);
            --accent:       #0891B2;
            --error-bg:     #FFF1F2;
            --error-border: #FECDD3;
            --error-text:   #BE123C;
            --role-border:  #E2E8F0;
            --role-active-bg: #EEF2FF;
            --role-active-border: #4F46E5;
            --shadow-card:  0 8px 48px rgba(79,70,229,0.1);
            --dot-color:    rgba(79,70,229,0.04);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-sans);
            background: var(--bg-page);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            transition: background 0.3s var(--ease), color 0.3s var(--ease);
        }

        /* Dot grid */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, var(--dot-color) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* Ambient glow */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 { width: 400px; height: 400px; background: var(--primary-glow); top: -80px; right: -80px; opacity: 0.7; }
        .orb-2 { width: 300px; height: 300px; background: rgba(6,182,212,0.08); bottom: -60px; left: -60px; opacity: 0.6; }

        /* ── Top Bar ── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            width: 100%;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: transparent;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-back:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateX(-2px);
        }
        .btn-back i { font-size: 0.75rem; }

        .theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            transition: var(--transition);
        }
        .theme-toggle:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* ── Main Layout ── */
        .login-page {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            max-width: 960px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            animation: fadeIn 0.4s var(--ease) both;
        }

        /* ── Left panel (branding) ── */
        .login-left {
            background: linear-gradient(145deg, var(--primary) 0%, #4338CA 60%, #312E81 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            bottom: -80px;
            right: -80px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: -40px;
            left: -40px;
        }

        .left-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .brand-name {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
        }

        .left-body {
            position: relative;
            z-index: 1;
        }

        .left-title {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: white;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .left-desc {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.7);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.85);
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .left-footer {
            position: relative;
            z-index: 1;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
        }

        /* ── Right panel (form) ── */
        .login-right {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-title {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        /* ── Role selector ── */
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 28px;
        }

        .role-card { position: relative; cursor: pointer; }

        .role-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0; height: 0;
        }

        .role-card-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 14px 10px;
            border-radius: 12px;
            border: 1.5px solid var(--role-border);
            background: transparent;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .role-card input:checked + .role-card-label {
            border-color: var(--role-active-border);
            background: var(--role-active-bg);
        }

        .role-card-label:hover {
            border-color: var(--border-focus);
            background: var(--role-active-bg);
        }

        .role-card-icon {
            font-size: 1.3rem;
            line-height: 1;
        }

        .role-card input:checked + .role-card-label .role-card-icon {
            color: var(--primary);
        }

        .role-card-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .role-card input:checked + .role-card-label .role-card-name {
            color: var(--primary);
        }

        .role-card-desc {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        /* ── Error alert ── */
        .alert-error {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--error-text);
            font-size: 0.875rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s var(--ease);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* ── Form fields ── */
        .field-group {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .field-control {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 0.9rem;
            font-family: var(--font-sans);
            outline: none;
            transition: var(--transition);
        }

        .field-control:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .field-control:focus ~ .field-icon,
        .field-wrap:focus-within .field-icon {
            color: var(--primary);
        }

        .field-control::placeholder { color: var(--text-muted); }

        .btn-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .btn-eye:hover { color: var(--primary); }

        /* ── Submit button ── */
        .btn-login {
            width: 100%;
            padding: 13px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: var(--font-sans);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--transition);
            box-shadow: 0 4px 12px var(--primary-glow);
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px var(--primary-glow);
            filter: brightness(1.05);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ── Animations ── */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.97) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .login-container { grid-template-columns: 1fr; }
            .login-left { display: none; }
            .login-right { padding: 36px 28px; }
        }
    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <!-- Top bar -->
    <div class="topbar">
        <a href="../portal.html" class="btn-back" id="backToPortal">
            <i class="fas fa-chevron-left"></i>
            Kembali ke Portal
        </a>
        <button class="theme-toggle" id="themeToggle" title="Toggle tema" aria-label="Toggle dark/light mode">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>
    </div>

    <!-- Login page -->
    <main class="login-page">
        <div class="login-container">

            <!-- Left branding -->
            <div class="login-left">
                <div class="left-brand">
                    <div class="brand-icon"><i class="fas fa-map-marked-alt"></i></div>
                    <span class="brand-name">WebGIS Smart City</span>
                </div>

                <div class="left-body">
                    <h2 class="left-title">Selamat Datang di<br>Sistem Informasi Geografis</h2>
                    <p class="left-desc">Platform manajemen data spasial kawasan kota Pontianak dengan visualisasi peta interaktif real-time.</p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-map"></i></div>
                            Peta Interaktif Real-time
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                            Dashboard Analitik
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                            Autentikasi Multi-Peran
                        </div>
                    </div>
                </div>

                <div class="left-footer">
                    D1041231071 — Naufal Zaky Ramadhan &copy; 2026
                </div>
            </div>

            <!-- Right form -->
            <div class="login-right">
                <div class="form-header">
                    <h1 class="form-title">Masuk ke Sistem</h1>
                    <p class="form-subtitle">Pilih peran dan masukkan kredensial Anda.</p>
                </div>

                <!-- Role selector -->
                <div class="role-selector">
                    <label class="role-card">
                        <input type="radio" name="role_display" value="admin" checked>
                        <div class="role-card-label">
                            <div class="role-card-icon"><i class="fas fa-user-shield"></i></div>
                            <div class="role-card-name">Admin</div>
                            <div class="role-card-desc">Kelola semua data</div>
                        </div>
                    </label>
                    <label class="role-card">
                        <input type="radio" name="role_display" value="user">
                        <div class="role-card-label">
                            <div class="role-card-icon"><i class="fas fa-user"></i></div>
                            <div class="role-card-name">Pengguna</div>
                            <div class="role-card-desc">Lihat &amp; analisis peta</div>
                        </div>
                    </label>
                </div>

                <?php if ($error): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" autocomplete="on">
                    <div class="field-group">
                        <label class="field-label" for="usernameInput">Username</label>
                        <div class="field-wrap">
                            <i class="fas fa-user field-icon"></i>
                            <input type="text" name="username" id="usernameInput" class="field-control"
                                   placeholder="Masukkan username"
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                   required autocomplete="username">
                        </div>
                    </div>

                    <div class="field-group">
                        <label class="field-label" for="passwordInput">Password</label>
                        <div class="field-wrap">
                            <i class="fas fa-lock field-icon"></i>
                            <input type="password" name="password" id="passwordInput" class="field-control"
                                   placeholder="Masukkan password"
                                   required autocomplete="current-password">
                            <button type="button" class="btn-eye" onclick="togglePass()" aria-label="Toggle password visibility">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk ke Sistem
                    </button>
                </form>
            </div>

        </div>
    </main>

    <script>
        // ── Password toggle ──
        function togglePass() {
            const inp = document.getElementById('passwordInput');
            const ico = document.getElementById('eyeIcon');
            const isPass = inp.type === 'password';
            inp.type = isPass ? 'text' : 'password';
            ico.className = isPass ? 'fas fa-eye-slash' : 'fas fa-eye';
        }

        // ── Theme toggle ──
        const html = document.documentElement;
        const btn  = document.getElementById('themeToggle');
        const icon = document.getElementById('themeIcon');

        const saved = localStorage.getItem('portal-theme') || 'dark';
        setTheme(saved);

        btn.addEventListener('click', () => {
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(next);
            localStorage.setItem('portal-theme', next);
        });

        function setTheme(t) {
            html.setAttribute('data-theme', t);
            icon.className = t === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }

        // ── Loading state on submit ──
        document.querySelector('form').addEventListener('submit', function() {
            const loginBtn = document.getElementById('loginBtn');
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            loginBtn.disabled = true;
        });
    </script>
</body>
</html>
