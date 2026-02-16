<?php
require_once __DIR__ . '/../app/helpers/functions.php';
$page_title = $page_title ?? 'SabaraTem';
$page_description = $page_description ?? 'Vitrine virtual local - SabaraTem';
$search_query = $search_query ?? '';
$show_nav = $show_nav ?? true;
$page_robots = $page_robots ?? null;
$page_canonical = $page_canonical ?? null;

$doc_root = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$project_root = realpath(__DIR__ . '/..') ?: '';
$doc_root = rtrim(str_replace('\\', '/', $doc_root), '/');
$project_root = rtrim(str_replace('\\', '/', $project_root), '/');

$base = '';
if ($doc_root !== '' && $project_root !== '' && strpos($project_root, $doc_root) === 0) {
    $base = substr($project_root, strlen($doc_root));
    if ($base === false || $base === '/') {
        $base = '';
    }
}

$asset_base = ($base ? $base : '') . '/assets';
$public_base = $base;
$page_og_image = $page_og_image ?? ($asset_base . '/img/og.svg');

$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') === '443');
$scheme = $is_https ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$current_url = $scheme . '://' . $host . $request_uri;
$canonical_url = $page_canonical ?: $current_url;

$request_path = parse_url($request_uri, PHP_URL_PATH) ?: '';
$is_admin_path = strpos($request_path, '/admin') !== false;
if ($page_robots === null) {
    $page_robots = $is_admin_path ? 'noindex, nofollow' : 'index, follow';
}
if ($is_admin_path) {
    header('X-Robots-Tag: noindex, nofollow', true);
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($page_title); ?></title>
  <meta name="description" content="<?php echo e($page_description); ?>">
  <meta name="robots" content="<?php echo e($page_robots); ?>">
  <link rel="canonical" href="<?php echo e($canonical_url); ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo e($canonical_url); ?>">
  <meta property="og:title" content="<?php echo e($page_title); ?>">
  <meta property="og:description" content="<?php echo e($page_description); ?>">
  <meta property="og:image" content="<?php echo e($page_og_image); ?>">
  <meta property="og:locale" content="pt_BR">
  <meta property="og:site_name" content="SabaraTem">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo e($page_title); ?>">
  <meta name="twitter:description" content="<?php echo e($page_description); ?>">
  <meta name="twitter:image" content="<?php echo e($page_og_image); ?>">
  <link rel="stylesheet" href="<?php echo e($asset_base); ?>/css/style.css?v=2">
</head>
<body data-public-base="<?php echo e($public_base); ?>" data-asset-base="<?php echo e($asset_base); ?>">
<header class="site-header">
  <div class="container header-row">
    <a class="brand" href="<?php echo e($public_base); ?>/index.php" aria-label="Ir para a página inicial">
      <span class="brand-icon">🔒</span>
      <div class="brand-text">
        <span class="brand-name">sabara<span>Tem</span></span>
        <span class="brand-tag">Vitrine local</span>
      </div>
    </a>

    <div class="search">
      <form action="<?php echo e($public_base); ?>/index.php" method="get">
        <input id="search-input" type="text" name="q" placeholder="O que você está procurando em Sabará?" value="<?php echo e($search_query); ?>">
      </form>
      <div id="search-results" class="search-results"></div>
    </div>

    <?php if ($show_nav): ?>
      <nav class="nav">
        <a href="<?php echo e($public_base); ?>/lojistas.php">Lojistas</a>
        <a href="<?php echo e($public_base); ?>/sobre.php">Sobre</a>
        <a class="nav-cta" href="<?php echo e($base); ?>/admin/index.php">Anuncie aqui</a>
      </nav>
      <button class="menu-toggle" type="button" aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu">☰</button>
    <?php endif; ?>
  </div>
  <?php if ($show_nav): ?>
    <div id="mobile-menu" class="mobile-menu">
      <a href="<?php echo e($public_base); ?>/lojistas.php">Lojistas</a>
      <a href="<?php echo e($public_base); ?>/sobre.php">Sobre</a>
      <a href="<?php echo e($base); ?>/admin/index.php">Anuncie aqui</a>
    </div>
  <?php endif; ?>
</header>
<main class="container">
