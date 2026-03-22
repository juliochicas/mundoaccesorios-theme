<?php /* section-oferta - Template Part */
if ( ma_get('hide_sec_oferta') ) return;
$oferta_title   = ma_get('oferta_title',  '¡Oferta especial de lanzamiento!');
$oferta_flash   = ma_get('oferta_flash',  'Bono flash activo: soporte prioritario y despacho rápido.');
$oferta_btn     = ma_get('oferta_btn',    'Aprovechar oferta ahora');
$oferta_img     = ma_get('oferta_img',    '');

// Lista de beneficios — una línea por ítem
$oferta_items_raw = ma_get('oferta_items',
    "Descuento especial al llevar 2 o más unidades\n" .
    "Garantía de satisfacción de 30 días\n" .
    "Soporte y confirmación por WhatsApp"
);
$oferta_items = array_filter(array_map('trim', explode("\n", $oferta_items_raw)));
?>
<section id="oferta" class="section wrap">
    <div class="offer" style="display:flex; flex-wrap:wrap; gap:24px; align-items:center;">
      <div style="flex:1; min-width:300px;">
          <h2><?= esc_html($oferta_title) ?></h2>
          <ul class="list">
            <?php foreach ( $oferta_items as $item ) : ?>
              <li><?= esc_html($item) ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="flash"><?= esc_html($oferta_flash) ?></div>
          <a class="btn btn-primary" href="#checkout"><?= esc_html($oferta_btn) ?></a>
      </div>
      <?php if ( $oferta_img ) : ?>
      <div style="flex:1; min-width:300px; text-align:center;">
          <img src="<?= esc_url($oferta_img) ?>" alt="Oferta Limitada" loading="lazy" style="width:100%; max-width:500px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15);" />
      </div>
      <?php endif; ?>
    </div>
</section>
