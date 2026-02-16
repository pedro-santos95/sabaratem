<?php
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/app/helpers/functions.php';

$page_title = 'SabaraTem - Sobre nos';
$page_description = 'Conheca a SabaraTem e o nosso compromisso com o comercio local.';
require_once BASE_PATH . '/includes/header.php';
?>
<section class="hero">
  <div class="hero-inner">
    <div class="hero-content">
      <span class="hero-chip">Sobre nos</span>
      <h1>Conectando pessoas e negocios locais.</h1>
      <p>O SabaraTem e uma vitrine virtual criada para fortalecer o comercio local, aproximar clientes e lojistas e facilitar compras diretas via WhatsApp.</p>
    </div>
    <div class="hero-media">
      <img src="<?php echo e($asset_base); ?>/img/banner.png" alt="SabaraTem" loading="lazy" decoding="async">
    </div>
  </div>
</section>

<section class="section">
  <div class="section-title">
    <h2>Nossa missao</h2>
  </div>
  <p class="muted">Dar visibilidade a pequenos e medios negocios, criando um canal simples e direto entre lojistas e clientes.</p>
</section>

<section class="section">
  <div class="section-title">
    <h2>Como funciona</h2>
  </div>
  <ul class="muted">
    <li>O admin cadastra lojas e produtos.</li>
    <li>Os produtos aparecem na vitrine por categoria.</li>
    <li>O cliente entra em contato direto via WhatsApp.</li>
  </ul>
</section>

<section class="section">
  <div class="section-title">
    <h2>Contato</h2>
  </div>
  <p class="muted">Fale com a equipe pelo WhatsApp ou visite nossas redes sociais.</p>
</section>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
