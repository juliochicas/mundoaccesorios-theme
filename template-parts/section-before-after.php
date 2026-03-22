<?php /* section-before-after - Template Part */
if ( ma_get('hide_sec_ba') ) return;
$ba_title = ma_get('ba_title', 'Resultados visibles en nuestros clientes');
$ba_intro = ma_get('ba_intro', 'Fotografías reales de usuarios después de usar el masajeador cervical');
$ba_img   = ma_get('ba_img', '');

// Prioridad a Pares de Antes/Después cargados individualmente en el Perfil de Producto
$ba_antes   = ma_get('ba_img_antes', '');
$ba_despues = ma_get('ba_img_despues', '');
$ba_caption = ma_get('ba_caption', '');

$ba_pairs = [];
if ($ba_antes && $ba_despues) {
    $ba_pairs[] = "{$ba_antes}|{$ba_despues}|{$ba_caption}";
}

// Fallback al global/Customizer si no hay campos de producto
if (empty($ba_pairs)) {
    $ba_pairs_raw = ma_get('ba_pairs', '');
    $ba_pairs = $ba_pairs_raw ? array_filter(array_map('trim', explode("\n", $ba_pairs_raw))) : [];
}

$label_antes  = ma_get('ba_label_antes',  'Antes');
$label_despues = ma_get('ba_label_despues', 'Después');
?>
<section id="before-after" class="section wrap band">
    <h2><?= esc_html($ba_title) ?></h2>
    <p class="section-intro"><?= esc_html($ba_intro) ?></p>
    <?php if ( $ba_img ) : ?>
        <div style="text-align:center; max-width:1000px; margin:0 auto;">
            <img src="<?= esc_url($ba_img) ?>" alt="Antes y Después" loading="lazy" style="width:100%; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1);" />
        </div>
    <?php elseif ( !empty($ba_pairs) ) : ?>
    <div class="ba-grid">
      <?php foreach ( $ba_pairs as $row ) :
          $parts = explode('|', $row, 3);
          if ( count($parts) < 2 ) continue;
          $img_before  = trim($parts[0]);
          $img_after   = trim($parts[1]);
          $caption     = isset($parts[2]) ? trim($parts[2]) : '';
      ?>
      <div class="ba-pair">
        <div class="ba-images">
          <figure class="ba-figure">
            <img src="<?= esc_url($img_before) ?>" alt="<?= esc_attr($label_antes) ?>" loading="lazy" />
            <span class="ba-label ba-before"><?= esc_html($label_antes) ?></span>
          </figure>
          <figure class="ba-figure">
            <img src="<?= esc_url($img_after) ?>" alt="<?= esc_attr($label_despues) ?>" loading="lazy" />
            <span class="ba-label ba-after"><?= esc_html($label_despues) ?></span>
          </figure>
        </div>
        <?php if ( $caption ) : ?>
        <p class="ba-caption"><?= esc_html($caption) ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
