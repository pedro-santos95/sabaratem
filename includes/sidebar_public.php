<?php if (!empty($categorias)): ?>
<aside class="sidebar public-sidebar">
  <h3>Categorias</h3>
  <div class="cat-grid">
    <?php foreach ($categorias as $c): ?>
      <a href="index.php?categoria_id=<?php echo e($c['id']); ?>" class="cat-item <?php echo ($categoria_id == $c['id']) ? 'active' : ''; ?>">
        <span class="cat-icon"><?php echo e(mb_substr($c['nome'], 0, 1)); ?></span>
        <span class="cat-name"><?php echo e($c['nome']); ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</aside>
<?php endif; ?>
