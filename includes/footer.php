</main>
<footer class="site-footer">
  <div class="container">
    <p>SabaraTem - Marketplace local. Contato direto via WhatsApp.</p>
  </div>
</footer>
<?php
$main_js_version = @filemtime(__DIR__ . '/../assets/js/main.js');
if ($main_js_version === false) {
  $main_js_version = time();
}
?>
<script src="<?php echo e($asset_base); ?>/js/main.js?v=<?php echo e($main_js_version); ?>"></script>
</body>
</html>



