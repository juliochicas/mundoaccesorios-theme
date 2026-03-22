<?php /* section-demo - Template Part */
if ( ma_get('hide_sec_demo') ) return;
$demo_title      = ma_get('demo_title',      'Ve el masajeador de cuello en uso real');
$demo_video_src  = ma_get('demo_video_src',  '');
$demo_poster     = ma_get('demo_poster',     '');

// Galería: Prioridad a las imágenes individuales del producto
$demo_gallery = [];
for ($i=1; $i<=3; $i++) {
    $img_url = ma_get('demo_gallery_'.$i);
    if ($img_url) {
        $demo_gallery[] = "image|$img_url|Miniatura Galería $i";
    }
}

// Fallback al global/Customizer
$demo_gallery_raw = ma_get('demo_gallery', '');

if (empty($demo_gallery)) {
    $demo_gallery = array_filter(array_map('trim', explode("\n", $demo_gallery_raw)));
}
?>
<section id="demo" class="section wrap band">
    <h2><?= esc_html($demo_title) ?></h2>
    <div class="tile demo-box">
      <div id="demo-main-media">
        <video controls playsinline preload="metadata" poster="<?= esc_url($demo_poster) ?>">
          <source src="<?= esc_url($demo_video_src) ?>" type="video/mp4" />
        </video>
      </div>
      <div class="gallery-strip">
        <?php foreach ( $demo_gallery as $i => $row ) :
            $parts = explode('|', $row, 3);
            if ( count($parts) < 2 ) continue;
            $type = trim($parts[0]);
            $src  = trim($parts[1]);
            $alt  = isset($parts[2]) ? trim($parts[2]) : 'Vista adicional';
            $is_video = ($type === 'video');
            $active = ($i === 0) ? 'active' : '';
        ?>
        <button type="button" class="gallery-thumb <?= $active ?>"
          data-demo-src="<?= esc_url($src) ?>"
          data-demo-type="<?= esc_attr($type) ?>"
          data-demo-poster="<?= esc_url($demo_poster) ?>"
          data-demo-alt="<?= esc_attr($alt) ?>"
          aria-label="Ver media <?= $i+1 ?>">
          <?php if ( $is_video ) : ?>
            <span class="video-badge">VIDEO</span>
            <video preload="metadata" muted playsinline>
              <source src="<?= esc_url($src) ?>" type="video/mp4" />
            </video>
          <?php else : ?>
            <img src="<?= esc_url($src) ?>" alt="<?= esc_attr($alt) ?>" loading="lazy" />
          <?php endif; ?>
        </button>
        <?php endforeach; ?>
      </div>
    </div>
</section>
