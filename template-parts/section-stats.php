<?php /* section-stats - Template Part */
if ( ma_get('hide_sec_stats') ) return;
$stats_title = ma_get('stats_title', 'Resultados reportados por nuestros clientes');
$stats_intro = ma_get('stats_intro', 'Basado en encuestas de satisfacción post-compra');
$stats_img   = ma_get('stats_img',   '');
$stats_img_alt = ma_get('stats_img_alt', 'Resultados');

// Stats: porcentaje|texto — una por línea
$stats_raw = ma_get('stats_items', '');
$stats_items = $stats_raw ? array_filter(array_map('trim', explode("\n", $stats_raw))) : [];

// Radio del círculo SVG
$r = 54; $c = 2 * M_PI * $r; // circunferencia = 339.3
?>
<section id="stats" class="section wrap band alt">
    <h2><?= esc_html($stats_title) ?></h2>
    <p class="section-intro"><?= esc_html($stats_intro) ?></p>
    <div class="stats-grid">
      <?php if ($stats_img) : ?>
      <div class="stats-image">
        <img src="<?= esc_url($stats_img) ?>" alt="<?= esc_attr($stats_img_alt) ?>" loading="lazy" />
      </div>
      <?php endif; ?>
      <div class="stats-list">
        <?php foreach ( $stats_items as $row ) :
            $parts = explode('|', $row, 2);
            if ( count($parts) < 2 ) continue;
            $pct  = (int) trim($parts[0]);
            $text = trim($parts[1]);
            $dash = round(($pct / 100) * $c, 1) . ' ' . round($c, 1);
        ?>
        <div class="stat-item">
          <div class="stat-ring">
            <svg viewBox="0 0 120 120" aria-hidden="true">
              <circle cx="60" cy="60" r="<?= $r ?>" class="stat-ring-bg" />
              <circle cx="60" cy="60" r="<?= $r ?>" class="stat-ring-fill" stroke-dasharray="<?= $dash ?>" />
            </svg>
            <span class="stat-value"><?= $pct ?>%</span>
          </div>
          <p class="stat-text"><?= esc_html($text) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
</section>
