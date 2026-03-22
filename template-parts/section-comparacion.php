<?php /* section-comparacion - Template Part */
if ( ma_get('hide_sec_comp') ) return;
$comp_title = ma_get('comp_title', 'Por qué Smart Neck Massager destaca frente a otros');
$comp_intro = ma_get('comp_intro', 'Comparación directa con alternativas del mercado');
$comp_prod  = ma_get('comp_prod',  'Smart Neck Massager');
$comp_otros = ma_get('comp_otros', 'Otros');

// Filas de comparación: característica|nuestro(si/no)|ellos(si/no)
// "si" = check verde, "no" = cruz roja
$comp_rows_raw = ma_get('comp_rows',
    "Tecnología EMS + Calor|si|no\n" .
    "Batería recargable 15 días|si|no\n" .
    "Diseño portátil y ligero|si|no\n" .
    "Garantía 30 días|si|si\n" .
    "Envío gratis a todo el país|si|no"
);
$comp_rows = array_filter(array_map('trim', explode("\n", $comp_rows_raw)));

$svg_check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
$svg_cross = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
?>
<section id="comparacion" class="section wrap band">
    <h2><?= esc_html($comp_title) ?></h2>
    <p class="section-intro"><?= esc_html($comp_intro) ?></p>
    <div class="comparison-wrap">
      <table class="comparison-table">
        <thead>
          <tr>
            <th></th>
            <th class="ct-product"><?= esc_html($comp_prod) ?></th>
            <th class="ct-others"><?= esc_html($comp_otros) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( $comp_rows as $row ) :
              $parts = explode('|', $row, 3);
              if ( count($parts) < 3 ) continue;
              $feat    = trim($parts[0]);
              $nuestro = strtolower(trim($parts[1])) === 'si';
              $ellos   = strtolower(trim($parts[2])) === 'si';
          ?>
          <tr>
            <td><?= esc_html($feat) ?></td>
            <td><?php if($nuestro): ?><span class="ct-check"><?= $svg_check ?></span><?php else: ?><span class="ct-cross"><?= $svg_cross ?></span><?php endif; ?></td>
            <td><?php if($ellos): ?><span class="ct-check"><?= $svg_check ?></span><?php else: ?><span class="ct-cross"><?= $svg_cross ?></span><?php endif; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</section>
