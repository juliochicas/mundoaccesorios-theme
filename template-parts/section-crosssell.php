<?php /* section-crosssell - Template Part */
if ( ma_get('hide_sec_crosssell') ) return;
$cs_title = ma_get('cs_title', 'Los clientes también llevan');

// Producto 1
$cs1_name    = ma_get('cs1_name',    '');
$cs1_price   = ma_get('cs1_price',   '');
$cs1_img     = ma_get('cs1_img',     '');
$cs1_btn     = ma_get('cs1_btn',     'Agregar');

// Producto 2
$cs2_name    = ma_get('cs2_name',    '');
$cs2_price   = ma_get('cs2_price',   '');
$cs2_img     = ma_get('cs2_img',     '');
$cs2_btn     = ma_get('cs2_btn',     'Agregar');

// Producto 3
$cs3_name    = ma_get('cs3_name',    '');
$cs3_price   = ma_get('cs3_price',   '');
$cs3_img     = ma_get('cs3_img',     '');
$cs3_btn     = ma_get('cs3_btn',     'Agregar');
?>
<section id="crosssell" class="section wrap band alt">
    <h2><?= esc_html($cs_title) ?></h2>
    <div class="cross-grid">
      <?php if ($cs1_name) : ?>
      <article class="tile cross-card">
        <?php if ($cs1_img) : ?><img src="<?= esc_url($cs1_img) ?>" alt="<?= esc_attr($cs1_name) ?>" loading="lazy" /><?php endif; ?>
        <h3><?= esc_html($cs1_name) ?></h3>
        <p class="price-mini">$<?= esc_html(number_format((float)$cs1_price,2)) ?></p>
        <button type="button" class="btn btn-primary"
          data-addon-id="addon-0"
          data-addon-name="<?= esc_attr($cs1_name) ?>"
          data-addon-price="<?= esc_attr($cs1_price) ?>"><?= esc_html($cs1_btn) ?></button>
      </article>
      <?php endif; ?>
      <?php if ($cs2_name) : ?>
      <article class="tile cross-card">
        <?php if ($cs2_img) : ?><img src="<?= esc_url($cs2_img) ?>" alt="<?= esc_attr($cs2_name) ?>" loading="lazy" /><?php endif; ?>
        <h3><?= esc_html($cs2_name) ?></h3>
        <p class="price-mini">$<?= esc_html(number_format((float)$cs2_price,2)) ?></p>
        <button type="button" class="btn btn-primary"
          data-addon-id="addon-1"
          data-addon-name="<?= esc_attr($cs2_name) ?>"
          data-addon-price="<?= esc_attr($cs2_price) ?>"><?= esc_html($cs2_btn) ?></button>
      </article>
      <?php endif; ?>
      <?php if ($cs3_name) : ?>
      <article class="tile cross-card">
        <?php if ($cs3_img) : ?><img src="<?= esc_url($cs3_img) ?>" alt="<?= esc_attr($cs3_name) ?>" loading="lazy" /><?php endif; ?>
        <h3><?= esc_html($cs3_name) ?></h3>
        <p class="price-mini">$<?= esc_html(number_format((float)$cs3_price,2)) ?></p>
        <button type="button" class="btn btn-primary"
          data-addon-id="addon-2"
          data-addon-name="<?= esc_attr($cs3_name) ?>"
          data-addon-price="<?= esc_attr($cs3_price) ?>"><?= esc_html($cs3_btn) ?></button>
      </article>
      <?php endif; ?>
    </div>
</section>
