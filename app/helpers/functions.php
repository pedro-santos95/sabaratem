<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function format_price($value) {
    return 'R$ ' . number_format((float)$value, 2, ',', '.');
}

function wa_link($phone, $text) {
    $digits = preg_replace('/\D+/', '', (string)$phone);
    if (strlen($digits) <= 11) {
        $digits = '55' . $digits;
    }
    return 'https://wa.me/' . $digits . '?text=' . urlencode($text);
}

function asset_url($path) {
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    if (preg_match('~^https?://~i', $path)) {
        return $path;
    }
    if (strpos($path, '../') === 0 || strpos($path, './') === 0) {
        return $path;
    }
    if (strpos($path, 'assets/') === 0) {
        global $asset_base;
        if (!empty($asset_base)) {
            return rtrim($asset_base, '/') . '/' . ltrim(substr($path, 7), '/');
        }
        return '../' . $path;
    }
    return $path;
}

function img_src($path) {
    if (!$path) {
        global $asset_base;
        if (!empty($asset_base)) {
            return rtrim($asset_base, '/') . '/img/placeholder.svg';
        }
        return '../assets/img/placeholder.svg';
    }
    return asset_url($path);
}

function asset_path($path) {
    return asset_url($path);
}

function start_session() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function is_admin_logged_in() {
    return !empty($_SESSION['admin_logged_in']);
}

function admin_login($id, $username) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $id;
    $_SESSION['admin_user'] = $username;
}

function admin_logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
?>
