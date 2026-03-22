<?php /* section-ticker - Template Part */ ?>
if ( ma_get('hide_sec_ticker') ) return;
<?php
$ticker = ma_get('ticker_text', '🔥 Envío GRATIS a Ciudad de Guatemala hoy · Quedan pocas unidades');
if ( $ticker ) :
?>
<div class="wrap">
  <div class="ticker"><?php echo esc_html( $ticker ); ?></div>
</div>
<?php endif; ?>
