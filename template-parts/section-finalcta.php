<?php /* section-finalcta - Template Part */
if ( ma_get('hide_sec_pago_cta') ) return;
$cta_title = ma_get('cta_title', '¿Listo para hacer tu pedido?');
$cta_desc  = ma_get('cta_desc',  'Pago seguro, envío rastreable y soporte incluido.');
$cta_btn   = ma_get('cta_btn',   'Comprar ahora');
?>
<section id="finalcta" class="wrap">
    <div class="final-cta">
      <h2><?= esc_html($cta_title) ?></h2>
      <p><?= esc_html($cta_desc) ?></p>
      <a class="btn btn-primary" href="#checkout"><?= esc_html($cta_btn) ?></a>
    </div>
</section>
