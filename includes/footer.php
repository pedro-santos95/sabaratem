</main>
<div class="modal" id="checkout-modal" aria-hidden="true">
  <div class="modal-backdrop" data-modal-close></div>
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="checkout-title">
    <button class="modal-close" type="button" data-modal-close aria-label="Fechar">×</button>
    <h3 id="checkout-title">Dados para finalizar</h3>
    <form id="checkout-form">
      <label>
        Endere&ccedil;o
        <textarea name="address" rows="3" required></textarea>
      </label>
      <label>
        Contato
        <input type="text" name="contact" required>
      </label>
      <label>
        Forma de pagamento
        <select name="payment" required>
          <option value="">Selecione</option>
          <option value="PIX">PIX</option>
          <option value="Dinheiro">Dinheiro</option>
          <option value="Cart&atilde;o de cr&eacute;dito">Cart&atilde;o de cr&eacute;dito</option>
          <option value="Cart&atilde;o de d&eacute;bito">Cart&atilde;o de d&eacute;bito</option>
          <option value="Outro">Outro</option>
        </select>
      </label>
      <div class="modal-actions">
        <button class="btn-outline" type="button" data-modal-close>Cancelar</button>
        <button class="btn" type="submit">Enviar no WhatsApp</button>
      </div>
    </form>
    <p class="muted modal-hint">Seus dados ficam salvos neste navegador para facilitar as pr&oacute;ximas compras.</p>
  </div>
</div>
<footer class="site-footer">
  <div class="container">
    <p>SabaraTem - PartnerPlace local. Contato direto via WhatsApp.</p>
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





