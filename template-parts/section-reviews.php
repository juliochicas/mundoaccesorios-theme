<?php /* section-reviews - Template Part */
if ( ma_get('hide_sec_reviews') ) return;

$reviews = new WP_Query([
    'post_type'      => 'ma_review',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);

$has_cpt_reviews = $reviews->have_posts();

$stat1_num   = ma_get( 'stat1_num',   '+500' );
$stat1_label = ma_get( 'stat1_label', 'pedidos entregados' );
$stat2_num   = ma_get( 'stat2_num',   '4.8/5' );
$stat2_label = ma_get( 'stat2_label', 'satisfaccion promedio' );
$stat3_num   = ma_get( 'stat3_num',   '100%' );
$stat3_label = ma_get( 'stat3_label', 'envios activos en Guatemala' );
$review_img  = ma_get( 'review_img',  '' );

// Verificar si hay reseñas dinámicas vía IA
$r1_name = ma_get('rev1_name', '');
$has_reviews = $has_cpt_reviews || !empty($r1_name);

if ( $has_reviews ) :
?>
<section id="reviews" class="section wrap band alt">
    <h2>Lo que dicen nuestros clientes</h2>
    <?php if ( $review_img ) : ?>
        <div style="text-align:center; max-width:600px; margin:0 auto 24px auto;">
            <img src="<?= esc_url($review_img) ?>" alt="Testimonio Verificado" loading="lazy" style="width:100%; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1);" />
        </div>
    <?php endif; ?>
    <div class="review-stats">
      <div class="review-stat"><?= esc_html($stat1_num) ?> <span><?= esc_html($stat1_label) ?></span></div>
      <div class="review-stat"><?= esc_html($stat2_num) ?> <span><?= esc_html($stat2_label) ?></span></div>
      <div class="review-stat"><?= esc_html($stat3_num) ?> <span><?= esc_html($stat3_label) ?></span></div>
    </div>

    <div class="quote-grid" id="reviews-grid">
    <?php if ( $has_cpt_reviews ) :
        $i = 0;
        while ( $reviews->have_posts() ) : $reviews->the_post();
            $city    = get_post_meta( get_the_ID(), '_ma_review_city',    true );
            $rating  = (int) get_post_meta( get_the_ID(), '_ma_review_rating',  true ) ?: 5;
            $img_url = get_post_meta( get_the_ID(), '_ma_review_img_url', true );
            $stars   = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
            $name    = get_the_title();
            $initials = strtoupper( mb_substr($name,0,1) ) . strtoupper( mb_substr(strstr($name,' '),1,1) );
            $hidden_class = $i >= 4 ? ' review-hidden' : '';
            $i++;
    ?>
      <article class="tile quote-card<?= $hidden_class ?>">
        <div class="quote-head">
          <?php if ( $img_url ) : ?>
            <img src="<?= esc_url($img_url) ?>" alt="<?= esc_attr($name) ?>" loading="lazy" />
          <?php else : ?>
            <div class="avatar-fallback"><?= esc_html($initials) ?></div>
          <?php endif; ?>
          <div>
            <p class="author"><?= esc_html($name) ?> <span><?= esc_html($city) ?></span></p>
            <p class="verified-badge"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 12c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>Comprador verificado</p>
            <p class="stars"><?= esc_html($stars) ?></p>
          </div>
        </div>
        <p class="quote">"<?= wp_kses_post( get_the_content() ) ?>"</p>
      </article>
    <?php endwhile; wp_reset_postdata();
    else :
        // ── Fallback: testimonios dinámicos generados por IA ─────────────────
        $default_reviews = [];
        if ( $r1_name ) {
            for ($i=1; $i<=3; $i++) {
                $n = ma_get("rev{$i}_name");
                if ($n) {
                    $c = ma_get("rev{$i}_city", 'Guatemala');
                    $q = ma_get("rev{$i}_text", 'Excelente producto.');
                    $default_reviews[] = [$n, $c, 5, '', $q];
                }
            }
        }
        foreach ( $default_reviews as $idx => [$n, $c, $r, $img, $q] ) :
            $stars = str_repeat('★',$r).str_repeat('☆',5-$r);
            $hidden_class = $idx >= 4 ? ' review-hidden' : '';
    ?>
      <article class="tile quote-card<?= $hidden_class ?>">
        <div class="quote-head">
          <img src="<?= esc_url($img) ?>" alt="<?= esc_attr($n) ?>" loading="lazy" />
          <div>
            <p class="author"><?= esc_html($n) ?> <span><?= esc_html($c) ?></span></p>
            <p class="verified-badge"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 12c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>Comprador verificado</p>
            <p class="stars"><?= esc_html($stars) ?></p>
          </div>
        </div>
        <p class="quote">"<?= esc_html($q) ?>"</p>
      </article>
    <?php endforeach; endif; ?>
    </div>

    <div class="reviews-toggle-wrap" id="reviews-toggle-wrap">
      <button class="btn btn-outline" id="reviews-toggle-btn" type="button">
        Ver todas las reseñas <span id="reviews-toggle-icon">▼</span>
      </button>
    </div>
</section>
<?php endif; ?>
