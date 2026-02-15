<?php
require_once __DIR__ . '/../app/helpers/functions.php';
$page_title = $page_title ?? 'SabaraTem';
$page_description = $page_description ?? 'Vitrine virtual local - SabaraTem';
$search_query = $search_query ?? '';
$show_nav = $show_nav ?? true;

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
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($page_title); ?></title>
  <meta name="description" content="<?php echo e($page_description); ?>">
  <meta property="og:title" content="<?php echo e($page_title); ?>">
  <meta property="og:description" content="<?php echo e($page_description); ?>">
  <meta property="og:image" content="<?php echo e($page_og_image); ?>">
  <link rel="stylesheet" href="<?php echo e($asset_base); ?>/css/style.css?v=1">
</head>
<body data-public-base="<?php echo e($public_base); ?>" data-asset-base="<?php echo e($asset_base); ?>">
<header class="site-header">
  <div class="container header-row">
    <div class="brand">
      <span class="brand-icon">🔒</span>
      <div class="brand-text">
        <span class="brand-name">sabara<span>Tem</span></span>
        <span class="brand-tag">Vitrine local</span>
      </div>
    </div>

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
