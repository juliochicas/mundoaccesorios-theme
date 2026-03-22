<?php /* section-pago — Pago, transportadoras y flujo de entrega */
if ( ma_get('hide_sec_pago') ) return;
$pago_title = ma_get('pago_title','Pago y envío');
$pago_intro = ma_get('pago_intro','Paga con tarjeta o contra entrega según tu zona.');
?>
<section id="pago" class="section wrap band alt">
  <h2><?= esc_html($pago_title) ?></h2>
  <p class="section-intro"><?= esc_html($pago_intro) ?></p>

  <!-- Métodos de pago -->
  <div class="pay-grid">
    <article class="tile pay-card">
      <div class="pay-head">
        <span class="pay-icon">
          <!-- Stripe wordmark color simplificado -->
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/>
          </svg>
        </span>
        <h3>Tarjeta — Stripe</h3>
      </div>
      <!-- Logos de tarjetas aceptadas (SVG inline) -->
      <div class="card-logos">
        <!-- Visa -->
        <svg viewBox="0 0 60 20" height="18" aria-label="Visa">
          <text x="2" y="15" font-family="Arial Black,sans-serif" font-size="15" font-weight="900" fill="#1a1f71">VISA</text>
        </svg>
        <!-- Mastercard -->
        <svg viewBox="0 0 38 24" width="38" height="24" aria-label="Mastercard">
          <circle cx="14" cy="12" r="10" fill="#eb001b"/>
          <circle cx="24" cy="12" r="10" fill="#f79e1b"/>
          <path d="M19 7.3a10 10 0 0 1 0 9.4A10 10 0 0 1 19 7.3z" fill="#ff5f00"/>
        </svg>
        <!-- Amex simplified -->
        <span style="font-size:10px;font-weight:700;letter-spacing:-.3px;color:#016fcf;border:1.5px solid #016fcf;padding:2px 5px;border-radius:3px">AMEX</span>
      </div>
      <p>Visa, Mastercard y pagos internacionales seguros.</p>
    </article>

    <article class="tile pay-card">
      <div class="pay-head">
        <span class="pay-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 12V8H4v12h8"/><path d="M16 19h6m-3-3v6"/><path d="M4 8l4-4 4 4 4-4"/>
          </svg>
        </span>
        <h3>Pago contra entrega</h3>
      </div>
      <p>Disponible en zonas habilitadas. Validamos dirección antes de despachar.</p>
    </article>
  </div>

  <!-- Portadoras con logo real -->
  <div class="carrier-row">
    <!-- Cargo Expreso CAEX -->
    <div class="carrier-chip" role="img" aria-label="Cargo Expreso">
      <svg viewBox="0 0 135 36" height="26" aria-hidden="true">
        <rect width="135" height="36" rx="6" fill="#003087"/>
        <text x="10" y="24" font-family="Arial,sans-serif" font-size="13" font-weight="700" fill="#fff" textLength="45" lengthAdjust="spacingAndGlyphs">CARGO</text>
        <text x="60" y="24" font-family="Arial,sans-serif" font-size="13" font-weight="400" fill="#f9c200" textLength="65" lengthAdjust="spacingAndGlyphs">EXPRESO</text>
      </svg>
    </div>
    <!-- Guatex -->
    <div class="carrier-chip" role="img" aria-label="Guatex">
      <svg viewBox="0 0 90 36" height="26" aria-hidden="true">
        <rect width="90" height="36" rx="6" fill="#e11d30"/>
        <text x="50%" y="24" text-anchor="middle" font-family="Arial,sans-serif" font-size="14" font-weight="900" fill="#fff" textLength="65" lengthAdjust="spacingAndGlyphs">GUATEX</text>
      </svg>
    </div>
    <!-- Forza Delivery -->
    <div class="carrier-chip" role="img" aria-label="Forza Delivery">
      <svg viewBox="0 0 135 36" height="26" aria-hidden="true">
        <rect width="135" height="36" rx="6" fill="#f97316"/>
        <text x="50%" y="24" text-anchor="middle" font-family="Arial,sans-serif" font-size="13" font-weight="800" fill="#fff" textLength="115" lengthAdjust="spacingAndGlyphs">FORZA DELIVERY</text>
      </svg>
    </div>
    <!-- Gintracom Logistic -->
    <div class="carrier-chip" role="img" aria-label="Gintracom Logistic">
      <svg viewBox="0 0 135 36" height="26" aria-hidden="true">
        <!-- Fondo vibrante Gintracom -->
        <rect width="135" height="36" rx="6" fill="#facc15"/>
        <!-- Cubo isométrico 3D (Isotipo) -->
        <path d="M12 18l6 3.5 6-3.5v-7l-6-3.5-6 3.5v7z" fill="#1e3a8a"/>
        <path d="M18 21.5v7m0-14v7l6-3.5m-12 0l6 3.5" stroke="#fff" stroke-width="1.5" fill="none"/>
        <!-- Tipografía bold y sólida -->
        <text x="32" y="23" font-family="Arial,sans-serif" font-size="12" font-weight="900" fill="#1e3a8a" letter-spacing="0.5">GINTRACOM</text>
      </svg>
    </div>
  </div>

  <!-- Trust badges con ícono SVG -->
  <div class="payment-badges">
    <span>
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      SSL activo
    </span>
    <span>
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Compra segura
    </span>
    <span>
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
      Soporte en español
    </span>
    <span>
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
      Datos protegidos
    </span>
  </div>

  <!-- Flujo de entrega -->
  <div class="order-flow">
    <article class="tile order-step">
      <p class="order-step-num">01</p>
      <h3>Compra confirmada</h3>
      <p>Realizas pago con tarjeta o solicitas contra entrega.</p>
      <div class="order-eta">2-5 min</div>
    </article>
    <article class="tile order-step">
      <p class="order-step-num">02</p>
      <h3>Validación y empaque</h3>
      <p>Confirmamos datos y preparamos tu pedido.</p>
      <div class="order-eta">Mismo día</div>
    </article>
    <article class="tile order-step">
      <p class="order-step-num">03</p>
      <h3>Despacho</h3>
      <p>Enviamos con transportadora y compartimos número de guía.</p>
      <div class="order-eta">24-72 horas</div>
    </article>
    <article class="tile order-step">
      <p class="order-step-num">04</p>
      <h3>Entrega y postventa</h3>
      <p>Recibes tu producto y te apoyamos en garantía o cambios.</p>
      <div class="order-eta">Según zona</div>
    </article>
  </div>
</section>
