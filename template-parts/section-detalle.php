<?php /* section-detalle - Template Part */
if ( ma_get('hide_sec_det') ) return;
$det_title   = ma_get('det_title',   'Detalles del producto');
$det_espec   = ma_get('det_espec',   'Especificaciones');
$det_inbox   = ma_get('det_inbox',   'Contenido del paquete');
$det_compat  = ma_get('det_compat',  'Compatibilidad');

// Especificaciones — cada fila es "etiqueta|valor"
$det_specs_raw = ma_get('det_specs',
    "Material|ABS + almohadilla de contacto suave\n" .
    "Batería|Recargable USB-C\n" .
    "Modos|Calor + EMS con niveles ajustables\n" .
    "Peso|Ligero para uso diario"
);
$det_specs = array_filter(array_map('trim', explode("\n", $det_specs_raw)));

// Contenido del paquete — una línea por ítem
$det_inbox_raw = ma_get('det_inbox_items',
    "1 masajeador inteligente\n1 cable de carga USB-C\n1 manual de uso rápido"
);
$det_inbox_items = array_filter(array_map('trim', explode("\n", $det_inbox_raw)));

// Compatibilidad — una línea por ítem
$det_compat_raw = ma_get('det_compat_items',
    "Uso recomendado para cuello y hombros\nIdeal para oficina, casa y viaje\nNo requiere app para funcionar"
);
$det_compat_items = array_filter(array_map('trim', explode("\n", $det_compat_raw)));
$detalle_img = ma_get('detalle_img', '');
?>
<section id="detalle" class="section wrap band">
    <h2><?= esc_html($det_title) ?></h2>
    <?php if ( $detalle_img ) : ?>
        <div style="text-align:center; max-width:900px; margin:0 auto 32px auto;">
            <img src="<?= esc_url($detalle_img) ?>" alt="Infografía del producto" loading="lazy" style="width:100%; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1);" />
        </div>
    <?php endif; ?>
    <div class="detail-grid">
      <article class="tile detail-box">
        <h3><?= esc_html($det_espec) ?></h3>
        <table class="spec-table">
          <tbody>
            <?php foreach ( $det_specs as $row ) :
                $parts = explode('|', $row, 2);
                if ( count($parts) < 2 ) continue;
            ?>
            <tr>
              <th><?= esc_html(trim($parts[0])) ?></th>
              <td><?= esc_html(trim($parts[1])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </article>
      <article class="tile detail-box">
        <h3><?= esc_html($det_inbox) ?></h3>
        <ul class="list">
          <?php foreach ( $det_inbox_items as $item ) : ?>
            <li><?= esc_html($item) ?></li>
          <?php endforeach; ?>
        </ul>
        <h3 style="margin-top:16px;"><?= esc_html($det_compat) ?></h3>
        <ul class="list">
          <?php foreach ( $det_compat_items as $item ) : ?>
            <li><?= esc_html($item) ?></li>
          <?php endforeach; ?>
        </ul>
      </article>
    </div>
</section>
