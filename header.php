<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
<?php $theme_uri = get_template_directory_uri(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Figtree:wght@800;900&display=swap" />
  <link rel="icon" type="image/png" sizes="32x32" href="<?= $theme_uri ?>/assets/favicon-32.png" />
  <link rel="icon" type="image/png" sizes="16x16" href="<?= $theme_uri ?>/assets/favicon-16.png" />
  <link rel="apple-touch-icon" sizes="192x192" href="<?= $theme_uri ?>/assets/favicon-192.png" />
<?php
// ── SEO dinámico desde settings ──────────────────────────────────────────
$seo_title   = ma_get('seo_title',       'Masajeador Inteligente de Cuello | Alivio en 10 minutos | Mundo Accesorios GT');
$seo_desc    = ma_get('seo_description', 'Reduce tensión cervical y estrés diario con calor terapéutico y pulsos EMS. Envío rápido a toda Guatemala. Pago seguro con Stripe.');
$seo_image   = ma_get('seo_og_image',    ma_get('hero_image_url', 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=1200&q=80'));
$canonical   = esc_url( get_permalink() ?: home_url('/') );
$site_url    = esc_url( home_url() );
$price_new   = ma_get('hero_price_new', '$49.00');
$price_num   = preg_replace('/[^0-9.]/', '', $price_new) ?: '49';

// FAQs dinámicas para Schema (desde CPT ma_faq, con fallbacks)
$faqs_q = new WP_Query(['post_type'=>'ma_faq','posts_per_page'=>10,'post_status'=>'publish']);
$faq_schema = [];
if ( $faqs_q->have_posts() ) {
    while ( $faqs_q->have_posts() ) {
        $faqs_q->the_post();
        $faq_schema[] = [
            '@type' => 'Question',
            'name'  => get_the_title(),
            'acceptedAnswer' => ['@type'=>'Answer','text'=>wp_strip_all_tags(get_the_content())],
        ];
    }
    wp_reset_postdata();
} else {
    $faq_schema = [
        ['@type'=>'Question','name'=>'¿Cuándo recibo mi pedido?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Despachamos en 24 horas hábiles. En Ciudad de Guatemala 1-2 días. En departamentos 2-4 días hábiles.']],
        ['@type'=>'Question','name'=>'¿Cómo sé que mi pedido fue confirmado?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Te contactamos por WhatsApp para confirmar tu pedido y darte un número de rastreo antes del despacho.']],
        ['@type'=>'Question','name'=>'¿Qué métodos de pago aceptan?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Tarjeta de crédito o débito (Visa, Mastercard, AMEX) a través de Stripe. También aceptamos pago contra entrega en zonas habilitadas.']],
        ['@type'=>'Question','name'=>'¿Tiene garantía?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Sí, 30 días de garantía por defecto de fábrica.']],
        ['@type'=>'Question','name'=>'¿Puedo devolver el producto?','acceptedAnswer'=>['@type'=>'Answer','text'=>'Sí. Tienes 30 días para solicitar devolución.']],
    ];
}

// Schema Product dinámico
$product_schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Product',
    'name'     => 'Masajeador Inteligente de Cuello',
    'description' => $seo_desc,
    'image'    => [$seo_image],
    'brand'    => ['@type'=>'Brand','name'=>'Mundo Accesorios'],
    'offers'   => [
        '@type'         => 'Offer',
        'url'           => $canonical,
        'priceCurrency' => 'USD',
        'price'         => (float) $price_num,
        'availability'  => 'https://schema.org/InStock',
        'itemCondition' => 'https://schema.org/NewCondition',
        'seller'        => ['@type'=>'Organization','name'=>'Mundo Accesorios'],
    ],
    'aggregateRating' => ['@type'=>'AggregateRating','ratingValue'=>4.8,'reviewCount'=>126],
];
?>
  <title><?= esc_html($seo_title) ?></title>
  <meta name="description" content="<?= esc_attr($seo_desc) ?>" />
  <meta name="keywords" content="masajeador cervical Guatemala, alivio dolor cuello, EMS cuello, masajeador inteligente Guatemala" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="theme-color" content="#0B0B0C" />
  <meta name="color-scheme" content="dark light" />
  <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
  <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/apple-touch-icon.png" />
  <link rel="canonical" href="<?= $canonical ?>" />
  <meta property="og:type" content="product" />
  <meta property="og:locale" content="es_GT" />
  <meta property="og:site_name" content="Mundo Accesorios" />
  <meta property="og:title" content="<?= esc_attr($seo_title) ?>" />
  <meta property="og:description" content="<?= esc_attr($seo_desc) ?>" />
  <meta property="og:url" content="<?= $canonical ?>" />
  <meta property="og:image" content="<?= esc_attr($seo_image) ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= esc_attr($seo_title) ?>" />
  <meta name="twitter:description" content="<?= esc_attr($seo_desc) ?>" />
  <meta name="twitter:image" content="<?= esc_attr($seo_image) ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
  <link rel="preload" as="image" href="<?= esc_attr($seo_image) ?>" />
  <link rel="dns-prefetch" href="https://images.unsplash.com">
  <link rel="dns-prefetch" href="https://cdn.simpleicons.org">
  <link rel="dns-prefetch" href="https://fastapi.modularis.pro">
  <link rel="dns-prefetch" href="https://ipapi.co">
  <link rel="dns-prefetch" href="https://videos.pexels.com">
  <script type="application/ld+json"><?= wp_json_encode(['@context'=>'https://schema.org','@type'=>'Organization','name'=>'Mundo Accesorios','url'=>$site_url]) ?></script>
  <script type="application/ld+json"><?= wp_json_encode($product_schema) ?></script>
  <script type="application/ld+json"><?= wp_json_encode(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$faq_schema]) ?></script>
  <script>(function(){var s=localStorage.getItem('theme');if(s==='light')document.documentElement.classList.add('theme-light');else if(s==='dark')document.documentElement.classList.remove('theme-light');else if(window.matchMedia('(prefers-color-scheme:light)').matches)document.documentElement.classList.add('theme-light')})()</script>
  
<?php wp_head(); ?>
<style id="critical-inline-css">
:root {
      --bg: <?= esc_attr(ma_get('color_bg', '#0B0B0C')) ?>;
      --surface: <?= esc_attr(ma_get('color_surface', '#141416')) ?>;
      --surface-2: <?= esc_attr(ma_get('color_surface2', '#1C1C1F')) ?>;
      --input-bg: <?= esc_attr(ma_get('color_input', '#222225')) ?>;
      --text: <?= esc_attr(ma_get('color_text', '#F5F5F5')) ?>;
      --muted: <?= esc_attr(ma_get('color_muted', '#A3A3A3')) ?>;
      --line: <?= esc_attr(ma_get('color_line', '#2A2D31')) ?>;
      --line-2: <?= esc_attr(ma_get('color_line2', '#3A3D42')) ?>;
      --green: <?= esc_attr(ma_get('color_primary', '#F97316')) ?>;
      --green-text: <?= esc_attr(ma_get('color_primary', '#F97316')) ?>;
      --purple: <?= esc_attr(ma_get('color_secondary', '#8B5CF6')) ?>;
      --success: #22C55E;
      --max: 1240px;
      --radius: 8px;
      --section: 34px;
      --ratio-hero-media: 4 / 5;
      --ratio-demo-media: 16 / 9;
      --ratio-gallery-media: 1 / 1;
      --ratio-cross-media: 16 / 10;
      --fit-hero-media: cover;
      --fit-demo-media: cover;
      --fit-gallery-media: cover;
      --fit-cross-media: cover;
      --pos-hero-media: center;
      --pos-demo-media: center;
      --pos-gallery-media: center;
      --pos-cross-media: center;
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      margin: 0;
      font-family: "Figtree", "Segoe UI", Arial, sans-serif;
      background: radial-gradient(1200px 600px at 15% -20%, rgba(139,92,246,.16), transparent 60%),
                  radial-gradient(900px 500px at 100% 0%, rgba(249,115,22,.12), transparent 55%),
                  var(--bg);
      color: var(--text);
      line-height: 1.5;
      overflow-x: hidden;
    }
    h1, h2, h3 { margin: 0; line-height: 1.1; letter-spacing: -.02em; }
    p { margin: 0; }
    a { color: inherit; text-decoration: none; }

    .wrap { width: min(100%, var(--max)); margin: 0 auto; padding: 0 16px; }
    .tile {
      background: linear-gradient(180deg, var(--surface-2), var(--surface));
      border: 1px solid var(--line-2);
      border-radius: var(--radius);
      box-shadow: 0 8px 22px rgba(0,0,0,.24);
    }

    .announcement {
      border-bottom: 1px solid var(--line);
      background: var(--bg);
      color: var(--text);
      text-align: center;
      padding: 10px 12px;
      font-size: 13px;
      font-weight: 600;
    }

    .nav-wrap {
      border-bottom: 1px solid var(--line);
      background: var(--bg);
      position: sticky;
      top: 0;
      z-index: 40;
    }

    .nav {
      min-height: 68px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
    }

    .logo {
      font-family: 'Figtree', 'Montserrat', 'Arial Black', sans-serif;
      font-size: 22px;
      font-weight: 900;
      letter-spacing: -.05em;
      text-transform: uppercase;
      white-space: nowrap;
      text-decoration: none;
      color: var(--text);
      display: inline-flex;
      align-items: center;
      gap: 0;
      flex-shrink: 0;
    }
    .logo-dot {
      display: inline-block;
      width: 7px;
      height: 7px;
      background: #E53935;
      margin: 0 0px;
      border-radius: 1px;
      vertical-align: middle;
      position: relative;
      top: -1px;
    }
    .logo-img {
      height: 30px;
      width: auto;
      max-width: 260px;
      display: block;
      object-fit: contain;
    }

    .nav-links {
      display: flex;
      gap: 16px;
      color: var(--muted);
      font-size: 14px;
      font-weight: 600;
    }
    .nav-links a { cursor: pointer; }
    a[href] { cursor: pointer; }
    .lang-switch {
      display: inline-flex;
      gap: 8px;
      align-items: center;
      font-size: 12px;
      font-weight: 800;
    }
    .lang-switch a {
      border: 1px solid var(--line);
      border-radius: 999px;
      padding: 6px 9px;
      color: var(--muted);
      background: var(--surface);
    }
    .lang-switch a.active {
      color: #000;
      background: var(--green);
      border-color: var(--green);
    }

    .hero {
      margin-top: var(--section);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      overflow: hidden;
      position: relative;
      min-height: 66vh;
      background: var(--bg);
    }

    .hero-media {
      position: absolute;
      inset: 0;
    }

    .hero-media img {
      width: 100%;
      height: 100%;
      object-fit: var(--fit-hero-media);
      object-position: var(--pos-hero-media);
      opacity: .8;
      display: block;
    }
    .hero-media .hero-main-video {
      width: 100%;
      height: 100%;
      object-fit: var(--fit-hero-media);
      object-position: var(--pos-hero-media);
      opacity: .88;
      display: block;
      background: var(--surface);
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(0,0,0,.75), rgba(0,0,0,.2));
    }

    .hero-content {
      position: relative;
      z-index: 2;
      width: min(100%, 640px);
      padding: 40px 34px;
      display: grid;
      gap: 12px;
    }

    .hero-kicker {
      color: var(--green-text);
      font-size: 12px;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .hero h1 {
      font-size: clamp(34px, 6vw, 68px);
      font-weight: 800;
    }

    .lead {
      color: var(--text);
      font-size: clamp(18px, 2.2vw, 22px);
      max-width: 56ch;
    }

    .urgency {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: linear-gradient(135deg, #F97316, #EA580C);
      border: none;
      color: var(--text);
      border-radius: 12px;
      padding: 10px 14px;
      font-size: 13px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
      width: fit-content;
      box-shadow: 0 4px 16px rgba(249,115,22,.35), 0 0 0 1px rgba(249,115,22,.15);
    }
    .urgency-label { opacity: 1; color: var(--text); }
    .urgency-time {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 96px;
      padding: 8px 12px;
      border-radius: 9px;
      border: none;
      background: rgba(0,0,0,.3);
      color: var(--text);
      font-size: 24px;
      line-height: 1;
      letter-spacing: .06em;
      font-weight: 900;
      font-variant-numeric: tabular-nums;
    }
    .urgency.critical {
      background: linear-gradient(135deg, #ef4444, #b91c1c);
      box-shadow: 0 4px 20px rgba(239,68,68,.4), 0 0 0 2px rgba(239,68,68,.2);
      animation: urgencyPulse 1s ease-in-out infinite alternate;
    }
    .addon-clock {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 78px;
      padding: 5px 8px;
      border-radius: 8px;
      border: 1px solid rgba(249,115,22,.6);
      background: rgba(249,115,22,.12);
      color: #ffe2cc;
      font-weight: 900;
      letter-spacing: .04em;
      font-variant-numeric: tabular-nums;
    }
    .addon-clock.critical {
      border-color: #ef4444;
      background: rgba(239,68,68,.18);
      color: #ffd6d6;
    }
    @keyframes urgencyPulse {
      from { transform: translateY(0); }
      to { transform: translateY(-1px); }
    }

    .price-row {
      display: flex;
      align-items: baseline;
      gap: 10px;
      margin-top: 2px;
    }

    .old { color: var(--muted); text-decoration: line-through; }
    .new { font-size: 46px; font-weight: 800; }
    .discount { color: var(--green-text); font-weight: 800; }

    .btn-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px; }

    .btn {
      min-height: 46px;
      padding: 12px 16px;
      border-radius: 6px;
      border: 1px solid transparent;
      font-weight: 800;
      font-size: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: background-color .16s ease, transform .16s ease;
      cursor: pointer;
    }

    .btn-primary {
      background: var(--green);
      color: #000;
    }

    .btn-primary:hover {
      background: var(--purple);
      color: var(--text);
      transform: translateY(-1px);
    }

    .btn-secondary {
      border-color: var(--line);
      background: var(--surface-2);
      color: var(--text);
    }
    .btn-success {
      background: var(--success);
      color: var(--text);
    }

    .trust-inline {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .trust-inline span {
      font-size: 12px;
      border: 1px solid var(--line);
      background: rgba(0,0,0,.45);
      border-radius: 999px;
      padding: 6px 10px;
      color: var(--muted);
      font-weight: 600;
    }

    .ticker {
      margin-top: var(--section);
      border: 1px solid var(--purple);
      background: var(--purple);
      border-radius: var(--radius);
      padding: 12px 14px;
      color: var(--text);
      font-size: 13px;
      font-weight: 700;
      text-align: center;
    }

    .section { margin-top: var(--section); }
    .band {
      border: 1px solid var(--line-2);
      border-radius: 12px;
      background: linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.0)), var(--bg);
      padding: 18px;
    }
    .band.alt {
      background: linear-gradient(180deg, rgba(139,92,246,.09), rgba(139,92,246,.02)), var(--surface);
    }

    .split {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .panel { padding: 18px; }

    .section h2 {
      font-size: clamp(28px, 4vw, 42px);
      margin-bottom: 12px;
      letter-spacing: -.025em;
    }

    .section-intro {
      color: var(--muted);
      font-size: 18px;
      margin-bottom: 14px;
      max-width: 780px;
    }
    .section-intro strong { color: var(--text); }

    .list {
      margin: 0;
      padding-left: 20px;
      display: grid;
      gap: 8px;
      color: var(--text);
      font-size: 17px;
    }

    .list li::marker { color: var(--green-text); }

    .feature-grid,
    .cross-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
    }
    .review-stats {
      margin-bottom: 12px;
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 8px;
    }
    .review-stat {
      border: 1px solid var(--line);
      border-radius: 8px;
      background: var(--surface);
      color: var(--text);
      font-size: 13px;
      font-weight: 700;
      text-align: center;
      padding: 10px 8px;
    }

    
    .comparison-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid var(--line); }
    .comparison-table { width: 100%; border-collapse: collapse; }
    .comparison-table thead th {
      background: var(--surface-2);
      padding: 14px 16px;
      font-size: 14px;
      font-weight: 700;
      text-align: center;
      color: var(--text);
      border-bottom: 2px solid var(--line);
    }
    .comparison-table thead th:first-child { text-align: left; width: 50%; }
    .comparison-table tbody td {
      padding: 14px 16px;
      border-bottom: 1px solid var(--line);
      text-align: center;
      font-size: 15px;
      color: var(--text);
    }
    .comparison-table tbody td:first-child { text-align: left; font-weight: 500; }
    .comparison-table tbody tr:last-child td { border-bottom: none; }
    .comparison-table tbody tr:nth-child(even) { background: rgba(255,255,255,.02); }
    .ct-check { color: #22C55E; }
    .ct-cross { color: #EF4444; }
    .ct-check svg, .ct-cross svg { width: 22px; height: 22px; vertical-align: middle; }

    
    .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: center; }
    .stats-image { border-radius: 12px; overflow: hidden; }
    .stats-image img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; display: block; }
    .stats-list { display: flex; flex-direction: column; gap: 8px; }
    .stat-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 14px;
      border: 1px solid var(--line);
      border-radius: 12px;
      background: var(--surface);
    }
    .stat-ring { width: 72px; height: 72px; position: relative; flex-shrink: 0; }
    .stat-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .stat-ring-bg { fill: none; stroke: var(--line); stroke-width: 7; }
    .stat-ring-fill { fill: none; stroke: var(--green); stroke-width: 7; stroke-linecap: round; transition: stroke-dasharray .6s ease; }
    .stat-value {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 17px;
      font-weight: 800;
      color: var(--text);
    }
    .stat-text { color: var(--text); font-size: 15px; line-height: 1.4; }

    
    .ba-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .ba-pair {
      background: var(--surface);
      border-radius: 12px;
      border: 1px solid var(--line);
      padding: 8px;
    }
    .ba-images { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
    .ba-figure { position: relative; border-radius: 8px; overflow: hidden; margin: 0; }
    .ba-figure img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }
    .ba-label {
      position: absolute;
      bottom: 8px;
      left: 8px;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
    .ba-before { background: rgba(239,68,68,.85); color: #fff; }
    .ba-after { background: rgba(34,197,94,.85); color: #fff; }
    .ba-caption { text-align: center; color: var(--muted); font-size: 14px; padding-top: 8px; margin: 0; }

    
    .quote-grid {
      columns: 3;
      column-gap: 12px;
    }
    .quote-card {
      break-inside: avoid;
      margin-bottom: 12px;
      padding: 16px;
    }
    .feature-tile,
    .cross-card {
      padding: 16px;
    }
    .verified-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      color: var(--green-text);
      font-size: 13px;
      font-weight: 500;
      margin-top: 2px;
    }
    .verified-badge svg { width: 14px; height: 14px; flex-shrink: 0; }
    .quote-head {
      display: grid;
      grid-template-columns: 46px 1fr;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }
    .quote-head img,
    .avatar-fallback {
      width: 46px;
      height: 46px;
      border-radius: 999px;
      object-fit: cover;
      border: 1px solid var(--line);
      background: var(--input-bg);
      color: var(--muted);
      display: grid;
      place-items: center;
      font-size: 10px;
      font-weight: 700;
      text-align: center;
      padding: 4px;
    }

    .quote { font-size: 19px; margin-bottom: 8px; }
    .author { color: var(--text); font-weight: 700; }
    .author span { color: var(--muted); margin-left: 6px; }
    .stars {
      margin: 2px 0 0;
      color: var(--green-text);
      font-size: 13px;
      letter-spacing: .1em;
      font-weight: 800;
    }

    .mini-label {
      color: var(--green-text);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    .mini-label-svg {
      font-size: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--surface);
      border: 1px solid var(--line);
    }
    .mini-label-svg svg { width: 22px; height: 22px; }

    .feature-tile h3 { font-size: 22px; margin-bottom: 8px; }
    .feature-tile p { color: var(--muted); }

    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .detail-box { padding: 18px; }

    .detail-box h3 { font-size: 21px; margin-bottom: 10px; }

    .spec-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .spec-table th,
    .spec-table td {
      border-top: 1px solid var(--line);
      padding: 10px 8px;
      text-align: left;
      vertical-align: top;
    }

    .spec-table th {
      width: 42%;
      color: var(--muted);
      font-weight: 700;
      background: var(--surface-2);
    }

    .demo-box {
      padding: 12px;
    }

    .demo-box img,
    .demo-box video {
      width: 100%;
      aspect-ratio: var(--ratio-demo-media);
      border-radius: 8px;
      border: 1px solid var(--line);
      max-height: 640px;
      object-fit: var(--fit-demo-media);
      object-position: var(--pos-demo-media);
      display: block;
      background: var(--surface);
    }

    .gallery-strip {
      margin-top: 10px;
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 8px;
    }

    .gallery-thumb {
      width: 100%;
      border: 1px solid var(--line);
      background: var(--surface);
      border-radius: 6px;
      padding: 0;
      overflow: hidden;
      cursor: pointer;
      transition: border-color .16s ease, transform .16s ease;
      position: relative;
    }
    .gallery-thumb:hover { border-color: var(--green); transform: translateY(-1px); }
    .gallery-thumb.active { border-color: var(--green); box-shadow: 0 0 0 1px var(--green); }
    .gallery-thumb .video-badge {
      position: absolute;
      top: 6px;
      right: 6px;
      font-size: 10px;
      font-weight: 800;
      border-radius: 999px;
      padding: 3px 6px;
      background: rgba(0,0,0,.72);
      border: 1px solid var(--line-2);
      color: var(--text);
      z-index: 2;
    }
    .gallery-thumb img,
    .gallery-thumb video {
      width: 100%;
      aspect-ratio: var(--ratio-gallery-media);
      object-fit: var(--fit-gallery-media);
      object-position: var(--pos-gallery-media);
      display: block;
      background: var(--surface);
    }

    .offer {
      padding: 22px;
      border: 1px solid var(--line);
      background: var(--surface);
      border-radius: var(--radius);
    }

    .offer .flash {
      margin: 12px 0;
      border: 1px dashed var(--line-2);
      background: var(--surface-2);
      border-radius: 8px;
      padding: 10px;
      color: var(--text);
      font-weight: 700;
    }

    .cross-card img {
      width: 100%;
      aspect-ratio: var(--ratio-cross-media);
      object-fit: var(--fit-cross-media);
      object-position: var(--pos-cross-media);
      border-radius: 6px;
      border: 1px solid var(--line);
      margin-bottom: 8px;
      background: var(--surface);
    }

    .cross-card h3 { margin-bottom: 6px; font-size: 19px; }
    .price-mini { color: var(--green-text); font-weight: 800; margin-bottom: 8px; }

    .faq {
      display: grid;
      gap: 8px;
    }

    .faq-item {
      border: 1px solid var(--line);
      border-radius: 8px;
      background: var(--surface);
      padding: 12px 14px;
    }

    .faq-item summary { font-weight: 800; cursor: pointer; }
    .faq-item p { color: var(--muted); margin-top: 8px; }

    .pay-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    .pay-card { padding: 16px; }
    .pay-head {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 8px;
    }
    .pay-head h3 { font-size: 20px; }
    .pay-card p { color: var(--muted); }

    .pay-icon {
      width: 36px;
      height: 36px;
      border-radius: 999px;
      border: 1px solid var(--line);
      display: grid;
      place-items: center;
      background: var(--surface-2);
      font-size: 18px;
      flex: 0 0 auto;
    }

    .pay-logo {
      width: 36px;
      height: 36px;
      border-radius: 999px;
      border: 1px solid var(--line);
      object-fit: cover;
      background: var(--surface);
      flex: 0 0 auto;
    }

    .carrier-row {
      margin-top: 14px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    /* Carrier logo container — limpio, sin pill de texto */
    .carrier-chip {
      display: inline-flex;
      align-items: center;
      opacity: .88;
      transition: opacity .15s;
    }
    .carrier-chip:hover { opacity: 1; }
    .carrier-chip img {
      height: 28px;
      width: auto;
      display: block;
    }

    /* Logos de tarjetas aceptadas (Visa, MC, Amex) */
    .card-logos {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      margin-bottom: 8px;
    }

    .payment-badges {
      margin-top: 12px;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }
    .payment-badges span {
      font-size: 12px;
      border: 1px solid var(--line);
      background: var(--surface);
      border-radius: 999px;
      padding: 5px 10px;
      color: var(--muted);
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .payment-badges span svg { color: var(--green-text); flex-shrink: 0; }
    .order-flow {
      margin-top: 12px;
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 10px;
    }
    .order-step { padding: 14px; }
    .order-step-num {
      color: var(--green-text);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
      margin: 0 0 6px;
    }
    .order-step h3 { margin: 0 0 6px; font-size: 18px; }
    .order-step p { color: var(--muted); font-size: 14px; }
    .order-eta {
      margin-top: 8px;
      display: inline-block;
      font-size: 11px;
      font-weight: 700;
      border: 1px solid var(--line);
      border-radius: 999px;
      padding: 4px 8px;
      color: var(--text);
    }
    .checkout-box {
      border: 1px solid var(--line-2);
      border-radius: 10px;
      background: var(--surface);
      padding: 18px;
      display: grid;
      gap: 14px;
    }
    .checkout-cart {
      border: 1px solid var(--line);
      border-radius: 10px;
      background: var(--surface);
      padding: 12px;
      display: grid;
      gap: 12px;
    }
    .cart-product {
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 10px;
      display: grid;
      grid-template-columns: 76px 1fr;
      gap: 10px;
      align-items: center;
    }
    .cart-product img {
      width: 76px;
      height: 76px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid var(--line);
      background: var(--surface);
    }
    .cart-product-name { margin: 0 0 4px; font-size: 20px; }
    .addons-heading { margin: 0 0 8px; font-size: 16px; font-weight: 600; }
    .qty-row {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 6px;
    }
    .qty-btn {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      border: 1px solid var(--line);
      background: var(--surface-2);
      color: var(--text);
      font-size: 20px;
      line-height: 1;
      cursor: pointer;
    }
    .qty-value { min-width: 26px; text-align: center; font-weight: 800; }
    .cart-totals {
      border: 1px solid var(--line);
      border-radius: 10px;
      background: var(--surface-2);
      padding: 12px;
      display: grid;
      gap: 8px;
    }
    .tier-note {
      margin: 2px 0 0;
      font-size: 12px;
      color: #bfeecf;
      font-weight: 700;
    }
    .combo-list {
      display: grid;
      gap: 8px;
    }
    .combo-option {
      border: 1px solid var(--line-2);
      border-radius: 10px;
      padding: 10px;
      background: var(--surface-2);
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: 10px;
      align-items: center;
      cursor: pointer;
    }
    .combo-label strong { font-size: 14px; }
    .combo-label small {
      display: block;
      color: var(--muted);
      font-size: 12px;
      margin-top: 2px;
    }
    .combo-price {
      color: var(--text);
      font-weight: 800;
      font-size: 16px;
      white-space: nowrap;
    }
    .total-row {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      font-size: 18px;
      color: var(--muted);
    }
    .total-row.strong {
      font-size: 32px;
      font-weight: 800;
      color: var(--text);
    }
    .addons-box {
      border: 1px solid var(--line-2);
      border-radius: 10px;
      background: var(--surface-2);
      padding: 10px;
      display: none;
    }
    .addons-box.show { display: block; }
    .addons-box h4 {
      margin: 0 0 8px;
      font-size: 14px;
      color: var(--text);
    }
    .addons-list {
      margin: 0;
      padding: 0;
      list-style: none;
      display: grid;
      gap: 6px;
      font-size: 13px;
      color: var(--muted);
    }
    .addons-list li {
      display: flex;
      justify-content: space-between;
      gap: 8px;
    }
    .shipping-methods {
      display: grid;
      gap: 8px;
    }
    .ship-option {
      border: 1px solid var(--line-2);
      border-radius: 10px;
      padding: 10px;
      background: var(--surface-2);
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: 10px;
      align-items: center;
      cursor: pointer;
      transition: border-color .16s ease, transform .16s ease;
    }
    .ship-option:hover {
      border-color: var(--green);
      transform: translateY(-1px);
    }
    .ship-option input { transform: scale(1.1); }
    .ship-option .eta { color: var(--muted); font-size: 12px; }
    .checkout-steps {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 8px;
    }
    .checkout-step {
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: 10px;
      background: var(--surface-2);
      font-size: 13px;
      font-weight: 700;
      color: var(--text);
      text-align: center;
    }
    .checkout-form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
    }
    .field {
      display: grid;
      gap: 6px;
    }
    .field label {
      font-size: 12px;
      font-weight: 700;
      color: var(--muted);
    }
    .field input, .field textarea {
      width: 100%;
      border: 1px solid var(--line-2);
      border-radius: 8px;
      background: var(--input-bg);
      color: var(--text);
      padding: 11px 12px;
      font: inherit;
    }
    .field select {
      width: 100%;
      border: 1px solid var(--line-2);
      border-radius: 8px;
      background: var(--input-bg);
      color: var(--text);
      padding: 11px 12px;
      font: inherit;
    }
    .field textarea { min-height: 90px; resize: vertical; }
    .field.full { grid-column: 1 / -1; }
    .nit-actions {
      display: flex;
      gap: 8px;
      margin-top: 2px;
      flex-wrap: wrap;
    }
    .btn-danger {
      background: #cc2327;
      color: var(--text);
    }
    .btn-success {
      background: #23b359;
      color: var(--text);
    }
    .nit-status {
      margin-top: 6px;
      font-size: 12px;
      font-weight: 700;
    }
    .nit-status.ok { color: #3ddc84; }
    .nit-status.err { color: #ff6b6b; }
    .nit-status.wait { color: #ffcf56; }
    .pay-choice-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }
    .pay-choice {
      border: 1px solid var(--line);
      border-radius: 8px;
      background: var(--surface-2);
      padding: 10px;
      display: flex;
      gap: 8px;
      align-items: center;
      font-size: 14px;
      font-weight: 700;
    }
    .addon-box {
      border: 1px dashed var(--line-2);
      border-radius: 8px;
      padding: 10px;
      background: var(--surface);
      color: var(--text);
      font-size: 13px;
      display: grid;
      gap: 8px;
    }
    .addon-box strong { color: var(--green-text); }
    .checkout-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .card-brands {
      margin-top: 6px;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }
    .brand-chip {
      border: 1px solid var(--line);
      background: var(--surface);
      border-radius: 8px;
      min-height: 38px;
      padding: 7px 11px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--text);
      font-size: 12px;
      font-weight: 700;
    }
    .brand-chip img {
      width: auto;
      height: 20px;
      max-width: 86px;
      object-fit: contain;
      display: block;
    }
    .hint {
      margin: 0;
      font-size: 12px;
      color: var(--muted);
    }
    .coverage-status {
      margin-top: 8px;
      display: none;
      border-radius: 8px;
      padding: 8px 10px;
      border: 1px solid #2e3340;
      background: #10141b;
      color: #cfd8e3;
      font-size: 12px;
      line-height: 1.35;
    }
    .coverage-status.show { display: block; }
    .coverage-status.warn {
      border-color: #7c5b18;
      background: #2a1f0b;
      color: #f5ddb0;
    }

    .final-cta {
      margin-top: var(--section);
      border: 1px solid var(--line);
      background: var(--surface);
      border-radius: var(--radius);
      padding: 24px;
      display: grid;
      gap: 12px;
      justify-items: start;
    }

    .final-cta p { color: var(--muted); font-size: 18px; }

    footer {
      margin-top: 34px;
      border-top: 1px solid var(--line);
      padding: 22px 0 28px;
      color: var(--muted);
      font-size: 14px;
      background: var(--bg);
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 16px;
      align-items: start;
    }

    .footer-links {
      display: flex;
      flex-wrap: wrap;
      gap: 10px 14px;
    }

    .footer-links a {
      color: var(--text);
      font-weight: 600;
      font-size: 14px;
    }

    .sticky-mobile {
      display: none;
      position: fixed;
      left: 10px;
      right: 10px;
      bottom: 10px;
      z-index: 50;
      border: 1px solid var(--line);
      border-radius: 8px;
      background: rgba(0,0,0,.95);
      padding: 9px;
      grid-template-columns: 1fr auto;
      gap: 10px;
      align-items: center;
    }

    .sticky-mobile small { display: block; color: var(--muted); font-size: 11px; }
    .sticky-mobile strong { font-size: 17px; }

    #addon-toast {
      position: fixed;
      bottom: 80px;
      left: 50%;
      transform: translateX(-50%) translateY(20px);
      background: var(--surface);
      color: var(--text);
      border: 1px solid var(--green);
      padding: 10px 22px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      z-index: 999;
      opacity: 0;
      transition: opacity .25s, transform .25s;
      pointer-events: none;
      box-shadow: 0 4px 20px rgba(0,0,0,.35);
      white-space: nowrap;
    }
    #addon-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .theme-light #addon-toast { box-shadow: 0 4px 20px rgba(0,0,0,.12); }

    @media (max-width: 1024px) {
      .hero-grid,
      .split,
      .detail-grid,
      .feature-grid,
      .cross-grid,
      .footer-grid {
        grid-template-columns: 1fr;
      }
	      .review-stats { grid-template-columns: 1fr; }
        .pay-grid { grid-template-columns: 1fr; }
        .order-flow { grid-template-columns: 1fr; }
        .checkout-steps { grid-template-columns: 1fr; }
        .checkout-form-grid { grid-template-columns: 1fr; }
        .pay-choice-row { grid-template-columns: 1fr; }
        .cart-product { grid-template-columns: 1fr; }
        .total-row.strong { font-size: 28px; }
        .quote-grid { columns: 2; }
        .stats-grid { grid-template-columns: 1fr; }
        .stats-image { max-height: 300px; }
        .ba-grid { grid-template-columns: 1fr; }

      .hero-media { position: static; }
      .hero-media img,
      .hero-media .hero-main-video {
        aspect-ratio: var(--ratio-hero-media);
        object-fit: var(--fit-hero-media);
        object-position: var(--pos-hero-media);
      }
    }

    @media (max-width: 720px) {
      :root { --section: 24px; }
      .wrap { padding: 0 12px; }
      .hero-content { padding: 20px 16px; }
      .hero h1 { font-size: 34px; }
      .new { font-size: 34px; }
      .nav-links { display: none; }
      .thumbs { grid-template-columns: repeat(3, minmax(0, 1fr)); }
      .gallery-strip { grid-template-columns: repeat(3, minmax(0, 1fr)); }
      .sticky-mobile { display: grid; }
      body { padding-bottom: 88px; }
      .quote-grid { columns: 1; }
      .ba-images { grid-template-columns: 1fr; }
    }

    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
      html { scroll-behavior: auto; }
    }

    
    html.theme-light {
      --bg: #FAFAF9;
      --surface: #FFFFFF;
      --surface-2: #F1F5F9;
      --input-bg: #F8FAFC;
      --text: #0F172A;
      --muted: #475569;
      --line: #E2E8F0;
      --line-2: #CBD5E1;
      --green: #EA580C;
      --green-text: #a83f09;
      --purple: #7C3AED;
      --success: #15803D;
    }
    html.theme-light body {
      background: radial-gradient(1200px 600px at 15% -20%, rgba(124,58,237,.08), transparent 60%),
                  radial-gradient(900px 500px at 100% 0%, rgba(234,88,12,.06), transparent 55%),
                  var(--bg);
    }
    .theme-light .announcement, .theme-light .nav-wrap, .theme-light footer { background: var(--surface); color: var(--text); }
    .theme-light .nav-links { color: var(--muted); }
    .theme-light .lang-switch a, .theme-light .theme-toggle { background: var(--surface); color: var(--text); border-color: var(--line); }
    .theme-light .tile, .theme-light .band, .theme-light .band.alt, .theme-light .offer, .theme-light .final-cta, .theme-light .checkout-box, .theme-light .checkout-cart, .theme-light .cart-totals, .theme-light .combo-option, .theme-light .ship-option, .theme-light .pay-choice, .theme-light .addon-box, .theme-light .faq-item, .theme-light .detail-box {
      background: var(--surface);
      color: var(--text);
      border-color: var(--line);
      box-shadow: 0 6px 18px rgba(15,23,42,.06);
    }
    .theme-light .section-intro, .theme-light .feature-tile p, .theme-light .pay-card p, .theme-light .order-step p, .theme-light .hint, .theme-light .faq-item p, .theme-light .author span, .theme-light .review-stat, .theme-light .total-row { color: var(--muted); }
    .theme-light .quote, .theme-light .feature-tile h3, .theme-light .pay-head h3, .theme-light .order-step h3, .theme-light .cart-product-name, .theme-light .addons-heading, .theme-light .total-row.strong, .theme-light .review-stat, .theme-light .author { color: var(--text); }
    .theme-light .field input, .theme-light .field textarea, .theme-light .field select {
      background: var(--input-bg);
      color: var(--text);
      border-color: var(--line);
    }
    .theme-light .brand-chip {
      background: var(--surface);
      border-color: var(--line);
      color: var(--text);
    }
    .theme-light .btn-secondary, .theme-light .qty-btn {
      background: var(--surface-2);
      color: var(--text);
      border-color: var(--line);
    }
    .theme-light .trust-inline span, .theme-light .payment-badges span, .theme-light .carrier-chip, .theme-light .order-eta, .theme-light .checkout-step {
      background: var(--surface-2);
      color: var(--text);
      border-color: var(--line);
    }
    .theme-light .coverage-status {
      background: #fff7ed;
      border-color: #fdba74;
      color: #7c2d12;
    }
    .theme-light .coverage-status.warn {
      background: #fef3c7;
      border-color: #f59e0b;
      color: #78350f;
    }
    
    .theme-light .addon-clock {
      background: rgba(249,115,22,.12);
      border-color: rgba(249,115,22,.5);
      color: #9a3412;
    }
    .theme-light .addon-clock.critical {
      background: rgba(239,68,68,.10);
      border-color: #ef4444;
      color: #991b1b;
    }
    
    .theme-light .tier-note { color: #15803d; }
    
    .theme-light .nit-status.ok { color: #15803d; }
    .theme-light .nit-status.err { color: #b91c1c; }
    .theme-light .nit-status.wait { color: #a16207; }
    
    .theme-light .btn-danger { background: #dc2626; color: #fff; }
    .theme-light .btn-success { background: #16a34a; color: #fff; }
    
    .theme-light .combo-option { background: var(--surface); border-color: var(--line); }
    .theme-light .combo-option input:checked + .combo-label { color: var(--text); }
    
    .theme-light .addon-box { background: var(--surface); border-color: var(--line); }
    .theme-light .checkout-box { background: var(--surface); border-color: var(--line); }
    
    .theme-light .urgency { color: #fff; }
    .theme-light .urgency-label { color: #fff; }
    .theme-light .urgency-time { color: #fff; background: rgba(0,0,0,.25); }
    
    .theme-light .discount { color: var(--green-text); }
    
    .theme-light .ship-option { background: var(--surface); border-color: var(--line); color: var(--text); }
    .theme-light .total-row strong { color: var(--text); }
    .theme-light .addons-list li { color: var(--text); }
    .theme-light .addons-list li strong { color: var(--text); }
    .theme-light .list li { color: var(--text); }
    .theme-light .field label { color: var(--muted); }
    .theme-light .review-stat { background: var(--surface-2); color: var(--text); }
    .theme-light .comparison-table thead th { background: var(--surface-2); }
    .theme-light .comparison-table tbody tr:nth-child(even) { background: var(--surface-2); }
    .theme-light .stat-item { background: var(--surface); border-color: var(--line); }
    .theme-light .ba-pair { background: var(--surface); border-color: var(--line); }
    .theme-light .pay-icon { background: var(--surface-2); border-color: var(--line); color: var(--text); }
    .theme-light .hero-overlay { background: linear-gradient(90deg, rgba(255,255,255,.85), rgba(255,255,255,.3)); }
    .theme-light .hero-content h1, .theme-light .hero-content .new, .theme-light .hero-content .old { color: var(--text); }
    .theme-light .hero-content p, .theme-light .hero-content .badge { color: var(--text); }
    .theme-light .sticky-mobile { background: rgba(255,255,255,.97); border-color: var(--line); box-shadow: 0 -4px 20px rgba(15,23,42,.08); }
    .theme-light .sticky-mobile small { color: var(--muted); }
    .theme-light .sticky-mobile strong { color: var(--text); }
    .theme-light .footer-links a { color: var(--muted); }
    .theme-light .ticker { border-color: var(--purple); color: #fff; }
    .theme-light .btn-primary:hover { color: #fff; }
    .theme-light .hero-media .hero-main-video { background: var(--surface-2); }
    .theme-light .cart-product img { background: var(--surface-2); }
    .theme-toggle { width: 36px; height: 36px; border-radius: 999px; border: 1px solid var(--line); background: var(--surface); color: var(--text); display: grid; place-items: center; cursor: pointer; transition: background .16s, color .16s; padding: 0; }
    .theme-toggle:hover { border-color: var(--green); }
    .theme-toggle svg { width: 18px; height: 18px; }
    .theme-toggle .icon-sun { display: none; }
    .theme-toggle .icon-moon { display: block; }
    .theme-light .theme-toggle .icon-sun { display: block; }
    .theme-light .theme-toggle .icon-moon { display: none; }

    
    :root {
      --font-body: "Figtree", "Segoe UI", Arial, sans-serif;
      --font-headings: "Figtree", "Segoe UI", Arial, sans-serif;
      --font-ui: "Figtree", "Segoe UI", Arial, sans-serif;
    }
    body { font-family: var(--font-body); }
    h1, h2, h3 { font-family: var(--font-headings); }
    .btn, .nav-links, .announcement, .hero-kicker, .urgency { font-family: var(--font-ui); }

    /* ── STICKY CTA BAR ─────────────────────────────────────────────────── */
    .sticky-cta {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      z-index: 50;
      background: rgba(15,23,42,.97);
      border-top: 1px solid var(--line);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 12px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      transform: translateY(110%);
      transition: transform .35s cubic-bezier(.22,1,.36,1);
      box-shadow: 0 -6px 32px rgba(0,0,0,.4);
    }
    .sticky-cta.visible { transform: translateY(0); }
    .sticky-cta-left { display: flex; flex-direction: column; gap: 1px; }
    .sticky-cta-left .cta-price { font-size: 22px; font-weight: 900; color: var(--orange); line-height: 1; }
    .sticky-cta-left .cta-old { font-size: 12px; color: var(--muted); text-decoration: line-through; }
    .sticky-cta-left .cta-badge {
      font-size: 11px; color: var(--green-text); font-weight: 700;
      background: rgba(20,255,120,.08); border: 1px solid rgba(20,255,120,.18);
      border-radius: 4px; padding: 1px 5px; display: inline-block;
    }
    .sticky-cta .btn-sticky {
      flex-shrink: 0; padding: 12px 22px; font-size: 15px; font-weight: 800;
      background: var(--orange); color: #fff; border: none;
      border-radius: var(--radius); cursor: pointer; text-decoration: none;
      display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;
      transition: background .15s, transform .12s;
    }
    .sticky-cta .btn-sticky:hover { background: #ea580c; transform: scale(1.02); }
    body.has-sticky-cta { padding-bottom: 80px; }
    .theme-light .sticky-cta { background: rgba(255,255,255,.97); border-color: var(--line); }

    /* ── REVIEWS TOGGLE ─────────────────────────────────────────────────── */
    .review-hidden { display: none !important; }
    .reviews-toggle-wrap { text-align: center; margin-top: 28px; }
    .btn-outline {
      background: transparent; border: 1.5px solid var(--line-2); color: var(--muted);
      padding: 10px 24px; border-radius: var(--radius); font-size: 14px;
      font-weight: 600; cursor: pointer; transition: border-color .15s, color .15s;
    }
    .btn-outline:hover { border-color: var(--orange); color: var(--orange); }
    #reviews-toggle-icon { display: inline-block; transition: transform .25s; margin-left: 4px; }
    #reviews-toggle-icon.open { transform: rotate(180deg); }
</style>
</head>
<body>
  

  <!-- ── STICKY CTA BAR (aparece al scrollar) ──────────────────────── -->
  <?php
  $hero_price_new = ma_get('hero_price_new', '$49.00');
  $hero_price_old = ma_get('hero_price_old', '$79.00');
  $hero_discount  = ma_get('hero_discount_label', 'Ahorra 38% hoy');
  ?>
  <div class="sticky-cta" id="sticky-cta" role="complementary" aria-label="Comprar ahora">
    <div class="sticky-cta-left">
      <span class="cta-price"><?= esc_html($hero_price_new) ?></span>
      <span class="cta-old"><?= esc_html($hero_price_old) ?></span>
      <span class="cta-badge"><?= esc_html($hero_discount) ?></span>
    </div>
    <a href="#checkout" class="btn-sticky" id="sticky-cta-btn">Quiero el mío →</a>
  </div>

  <div class="nav-wrap">
    <div class="wrap nav">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="Mundo Accesorios - Inicio">
<?php
$logo_url  = ma_get('logo_url', '');
$logo_text = ma_get('logo_text', 'MUNDO ACCESORIOS');
if ( $logo_url ) :
?>
        <img src="<?= esc_url($logo_url) ?>" alt="<?= esc_attr($logo_text) ?>" class="logo-img" />
<?php else :
    // Logo CSS — texto con cuadrito rojo entre las dos C
    $parts = explode('ACCESORIOS', strtoupper($logo_text), 2);
    if ( count($parts) === 2 ) {
        echo esc_html($parts[0]) . 'AC<span class="logo-dot"></span>CESORIOS';
    } else {
        echo esc_html($logo_text);
    }
endif;
?>
      </a>
      <nav class="nav-links">
        <a href="#interes">Beneficios</a>
        <a href="#detalle">Detalles</a>
        <a href="#pago">Pago y envio</a>
        <a href="#checkout">Checkout</a>
        <a href="#faq">FAQ</a>
      </nav>
      <button class="theme-toggle" id="theme-toggle" type="button" aria-label="Cambiar tema"><svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg><svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></button>
    </div>
  </div>

  