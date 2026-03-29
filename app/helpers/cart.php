<?php
require_once __DIR__ . '/functions.php';

function cart_get() {
    start_session();
    $carts = $_SESSION['carts'] ?? [];
    return is_array($carts) ? $carts : [];
}

function cart_save($carts) {
    start_session();
    $_SESSION['carts'] = $carts;
}

function cart_add_item($loja_id, $produto_id, $qty = 1) {
    $loja_id = (int)$loja_id;
    $produto_id = (int)$produto_id;
    $qty = (int)$qty;
    if ($loja_id <= 0 || $produto_id <= 0) {
        return;
    }
    if ($qty < 1) {
        $qty = 1;
    }
    $carts = cart_get();
    if (!isset($carts[$loja_id]) || !is_array($carts[$loja_id])) {
        $carts[$loja_id] = [];
    }
    if (!isset($carts[$loja_id][$produto_id])) {
        $carts[$loja_id][$produto_id] = 0;
    }
    $carts[$loja_id][$produto_id] += $qty;
    cart_save($carts);
}

function cart_set_item($loja_id, $produto_id, $qty) {
    $loja_id = (int)$loja_id;
    $produto_id = (int)$produto_id;
    $qty = (int)$qty;
    if ($loja_id <= 0 || $produto_id <= 0) {
        return;
    }
    $carts = cart_get();
    if ($qty <= 0) {
        if (isset($carts[$loja_id][$produto_id])) {
            unset($carts[$loja_id][$produto_id]);
        }
    } else {
        if (!isset($carts[$loja_id]) || !is_array($carts[$loja_id])) {
            $carts[$loja_id] = [];
        }
        $carts[$loja_id][$produto_id] = $qty;
    }
    if (isset($carts[$loja_id]) && empty($carts[$loja_id])) {
        unset($carts[$loja_id]);
    }
    cart_save($carts);
}

function cart_remove_item($loja_id, $produto_id) {
    cart_set_item($loja_id, $produto_id, 0);
}

function cart_clear_loja($loja_id) {
    $loja_id = (int)$loja_id;
    if ($loja_id <= 0) {
        return;
    }
    $carts = cart_get();
    if (isset($carts[$loja_id])) {
        unset($carts[$loja_id]);
    }
    cart_save($carts);
}

function cart_count_items() {
    $carts = cart_get();
    $count = 0;
    foreach ($carts as $items) {
        if (!is_array($items)) {
            continue;
        }
        foreach ($items as $qty) {
            $count += (int)$qty;
        }
    }
    return $count;
}

function cart_count_stores() {
    $carts = cart_get();
    return count($carts);
}

function cart_sanitize_redirect($url) {
    $url = trim((string)$url);
    if ($url === '') {
        return '';
    }
    if (preg_match('~^[a-z][a-z0-9+\-.]*://~i', $url)) {
        return '';
    }
    if (strpos($url, '//') === 0) {
        return '';
    }
    if ($url[0] !== '/') {
        return '';
    }
    return $url;
}
?>
