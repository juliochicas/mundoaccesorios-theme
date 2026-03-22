<?php /* section-features - Template Part */
if ( ma_get('hide_sec_features') ) return;
$feat1_title = ma_get('feat1_title', 'Calor terapéutico');
$feat1_desc  = ma_get('feat1_desc',  'Ayuda a relajar musculatura profunda y mejorar confort.');
$feat2_title = ma_get('feat2_title', 'Pulsos EMS');
$feat2_desc  = ma_get('feat2_desc',  'Estimula zonas de tensión para alivio progresivo.');
$feat3_title = ma_get('feat3_title', 'Batería recargable');
$feat3_desc  = ma_get('feat3_desc',  'Uso diario sin cables con autonomía para varias sesiones.');
$features_title = ma_get('features_title', 'Por qué elegir el Masajeador Inteligente de Cuello');
$feat_img = ma_get('feat_img', '');
?>
<section id="features" class="section wrap band alt">
    <h2><?= esc_html($features_title) ?></h2>
    <?php if ( $feat_img ) : ?>
        <div style="text-align:center; max-width:800px; margin:0 auto 32px auto;">
            <img src="<?= esc_url($feat_img) ?>" alt="Especificaciones visuales" loading="lazy" style="width:100%; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1);" />
        </div>
    <?php endif; ?>
    <div class="feature-grid">
      <article class="tile feature-tile">
        <div class="mini-label mini-label-svg"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2c0 4-4 6-4 10a4 4 0 0 0 8 0c0-4-4-6-4-10z"/><path d="M12 18v4"/></svg></div>
        <h3><?= esc_html($feat1_title) ?></h3>
        <p><?= esc_html($feat1_desc) ?></p>
      </article>
      <article class="tile feature-tile">
        <div class="mini-label mini-label-svg"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>
        <h3><?= esc_html($feat2_title) ?></h3>
        <p><?= esc_html($feat2_desc) ?></p>
      </article>
      <article class="tile feature-tile">
        <div class="mini-label mini-label-svg"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="18" height="10" rx="2"/><path d="M22 11v2"/><path d="M6 11v2"/><path d="M10 11v2"/></svg></div>
        <h3><?= esc_html($feat3_title) ?></h3>
        <p><?= esc_html($feat3_desc) ?></p>
      </article>
    </div>
</section>
