<?php /* section-hero - Template Part */ ?>
if ( ma_get('hide_sec_hero') ) return;
<section class="wrap">
    <div class="hero">
      <div class="hero-media">
        <img src="<?php echo esc_url( ma_get('hero_image_url','https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=1200&q=80') ); ?>" alt="Persona usando masajeador cervical" loading="eager" />
      </div>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="hero-kicker"><?php echo esc_html( ma_get('hero_kicker','Bienestar inteligente para rutinas exigentes') ); ?></div>
        <h1><?php echo esc_html( ma_get('hero_title','Alivia tension cervical en solo 10 minutos al dia') ); ?></h1>
        <p class="lead"><?php echo esc_html( ma_get('hero_subtitle','Pensado para personas con estres, mala postura y largas jornadas frente a pantalla.') ); ?></p>

        <div class="urgency"><span class="urgency-label">Oferta de hoy termina en</span> <span class="urgency-time" data-countdown-minutes="15">15:00</span></div>

        <p class="rating">4.8/5 de 126 clientes verificados</p>

        <div class="price-row">
          <span class="old"><?php echo esc_html( ma_get('hero_price_old','$79.00') ); ?></span>
          <span class="new"><?php echo esc_html( ma_get('hero_price_new','$49.00') ); ?></span>
        </div>
        <p class="discount"><?php echo esc_html( ma_get('hero_discount_label','Ahorra 38% hoy') ); ?></p>

        <div class="btn-row">
          <a class="btn btn-primary" href="#checkout"><?php echo esc_html( ma_get('hero_btn_primary','Comprar ahora') ); ?></a>
          <a class="btn btn-secondary" href="#interes"><?php echo esc_html( ma_get('hero_btn_secondary','Ver detalles') ); ?></a>
        </div>

        <div class="trust-inline">
          <span>Pago seguro</span><span>Envio rastreable</span><span>Garantia 30 dias</span>
        </div>
      </div>
    </div>
  </section>
