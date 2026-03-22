<?php /* section-checkout - Template Part */
if ( ma_get('hide_sec_checkout') ) return;

// ── Leer variables dinámicas del producto de WooCommerce ───────────────────
$post_id = get_the_ID();
$product = false;

if (function_exists('wc_get_product')) {
    $product = wc_get_product($post_id);
    // Si no es un producto (ej. estamos en la página frontal/funnel), jalamos el producto principal de la tienda
    if ( ! $product ) {
        $recent_products = wc_get_products( array( 'limit' => 1, 'return' => 'objects' ) );
        if ( !empty( $recent_products ) ) {
            $product = $recent_products[0];
            $post_id = $product->get_id();
        }
    }
}

$product_title = $product ? $product->get_name() : get_the_title($post_id);
if (!$product_title) $product_title = "Producto Principal";

$product_image = get_the_post_thumbnail_url($post_id, 'medium');
if (!$product_image) $product_image = ma_get('hero_image_url', '');

$wc_price = 49;
if ($product && !empty($product->get_price())) {
    $wc_price = (float) $product->get_price();
}

// Valores por defecto calculados algorítmicamente
$def_c1_sub = $wc_price;
$def_c1_dis = 'Q' . number_format($def_c1_sub, 2);

$def_c2_sub = round($wc_price * 2 * 0.9); // 10% off
$def_c2_dis = 'Q' . number_format($def_c2_sub, 2);

$def_c3_sub = round($wc_price * 3 * 0.8); // 20% off
$def_c3_dis = 'Q' . number_format($def_c3_sub, 2);

// Umbrales de envío gratis
$free_city  = (float) ma_get('ship_free_city', '250');
$free_depts = (float) ma_get('ship_free_depts', '300');


// ── Leer combos desde settings (con fallbacks algorítmicos) ───────────────
$combos_raw = [
    [
        'value'    => 'combo-1',
        'label'    => ma_get('combo1_label',    '1x producto'),
        'sublabel' => ma_get('combo1_sublabel', 'Ideal para probar'),
        'qty'      => ma_get('combo1_qty',      '1'),
        'subtotal' => ma_get('combo1_subtotal', (string)$def_c1_sub),
        'price'    => str_replace('$', 'Q', ma_get('combo1_price_display', $def_c1_dis)),
        'checked'  => true,
        'hide'     => strtoupper(ma_get('combo1_hide', '')) === 'SI',
    ],
    [
        'value'    => 'combo-2',
        'label'    => ma_get('combo2_label',    '2x combo ahorro'),
        'sublabel' => ma_get('combo2_sublabel', 'Más vendido · Ahorra 10%'),
        'qty'      => ma_get('combo2_qty',      '2'),
        'subtotal' => ma_get('combo2_subtotal', (string)$def_c2_sub),
        'price'    => str_replace('$', 'Q', ma_get('combo2_price_display', $def_c2_dis)),
        'checked'  => false,
        'hide'     => strtoupper(ma_get('combo2_hide', '')) === 'SI',
    ],
    [
        'value'    => 'combo-3',
        'label'    => ma_get('combo3_label',    '3x combo premium'),
        'sublabel' => ma_get('combo3_sublabel', 'Mejor relación precio · Ahorra 20%'),
        'qty'      => ma_get('combo3_qty',      '3'),
        'subtotal' => ma_get('combo3_subtotal', (string)$def_c3_sub),
        'price'    => str_replace('$', 'Q', ma_get('combo3_price_display', $def_c3_dis)),
        'checked'  => false,
        'hide'     => strtoupper(ma_get('combo3_hide', '')) === 'SI',
    ],
];
$combos = array_values(array_filter($combos_raw, fn($c) => !$c['hide']));

// Seguridad si el usuario ocultó todos (al menos mostramos el primero)
if (empty($combos)) {
    $combos = [ $combos_raw[0] ];
    $combos[0]['checked'] = true;
} else {
    // Asegurar que solo uno esté checked
    $has_checked = false;
    foreach ($combos as &$c) {
        if ($c['checked'] && !$has_checked) { $has_checked = true; }
        else { $c['checked'] = false; }
    }
    if (!$has_checked) { $combos[0]['checked'] = true; }
}

// ── Leer zonas de envío desde settings ────────────────────────────────────
$shipping_zones = [
    [
        'value' => 'city',
        'label' => ma_get('ship1_label', 'Ciudad de Guatemala'),
        'eta'   => ma_get('ship1_eta',   '24h'),
        'price' => ma_get('ship1_price', '20'),
        'free_threshold' => $free_city,
        'checked' => true,
    ],
    [
        'value' => 'departments',
        'label' => ma_get('ship2_label', 'Departamentos y Villa Nueva'),
        'eta'   => ma_get('ship2_eta',   '24-48h'),
        'price' => ma_get('ship2_price', '36'),
        'free_threshold' => $free_depts,
        'checked' => false,
    ],
    [
        'value' => 'express',
        'label' => ma_get('ship3_label', 'Capital express (1-3h)'),
        'eta'   => ma_get('ship3_eta',   '1-3h'),
        'price' => ma_get('ship3_price', '45'),
        'free_threshold' => 0, // Nunca gratis por default
        'checked' => false,
    ],
];

// ── Add-on ────────────────────────────────────────────────────────────────
$addon_label = ma_get('addon_label', 'Add-on: Cervical hot/cold gel pack');
$addon_price = ma_get('addon_price', '19');

// ── Precio base (primer combo) ─────────────────────────────────────────────
$base_price_display = $combos[0]['price'];
$first_ship_price   = 'Q' . number_format((float)$shipping_zones[0]['price'], 2);
$initial_total      = 'Q' . number_format( (float)$combos[0]['subtotal'] + (float)$shipping_zones[0]['price'], 2 );
?>
<section id="checkout" class="section wrap band">
    <h2>Checkout de Un Paso</h2>
    <p class="section-intro">Completa tu pedido en 3 simples pasos.</p>
    <div class="checkout-box">
      <div class="checkout-steps">
        <div class="checkout-step">1. Datos de contacto y entrega</div>
        <div class="checkout-step">2. Método de pago</div>
        <div class="checkout-step">3. Confirmar pedido</div>
      </div>
      <div class="checkout-cart">
        <article class="cart-product">
          <?php if ($product_image): ?>
          <img src="<?= esc_url($product_image) ?>" alt="<?= esc_attr($product_title) ?>" loading="lazy" />
          <?php endif; ?>
          <div>
            <h3 class="cart-product-name"><?= esc_html($product_title) ?></h3>
            <p id="cart-unit-price"><?= esc_html($base_price_display) ?></p>
            <div class="qty-row">
              <button type="button" class="qty-btn" id="qty-minus" aria-label="Disminuir cantidad">−</button>
              <span class="qty-value" id="qty-value">1</span>
              <button type="button" class="qty-btn" id="qty-plus" aria-label="Aumentar cantidad">+</button>
            </div>
          </div>
        </article>
        <div class="cart-totals">
          <div class="total-row"><span>Subtotal</span><strong id="sum-subtotal"><?= esc_html($base_price_display) ?></strong></div>
          <p id="tier-note" class="tier-note"></p>
          <div class="addons-box" id="addons-box">
            <h3 class="addons-heading">Complementos agregados</h3>
            <ul id="addons-list" class="addons-list"></ul>
          </div>
          <div class="total-row"><span>Complementos</span><strong id="sum-addons">Q0.00</strong></div>
          <div class="total-row"><span>Envío</span><strong id="sum-shipping"><?= esc_html($first_ship_price) ?></strong></div>
          <div class="total-row strong"><span>Total</span><strong id="sum-total"><?= esc_html($initial_total) ?></strong></div>
        </div>
      </div>

      <!-- Combos -->
      <div>
        <h3 style="font-size:24px;margin:0 0 10px;">Construye tu paquete</h3>
        <div class="combo-list">
          <?php foreach ( $combos as $combo ) : ?>
          <label class="combo-option">
              <input type="radio" name="combo_offer"
                     value="<?= esc_attr($combo['value']) ?>"
                     data-qty="<?= esc_attr($combo['qty']) ?>"
                     data-subtotal="<?= esc_attr($combo['subtotal']) ?>"
                     <?= $combo['checked'] ? 'checked' : '' ?> />
              <span class="combo-label">
                <strong><?= esc_html($combo['label']) ?></strong>
                <small><?= esc_html($combo['sublabel']) ?></small>
              </span>
              <span class="combo-price"><?= esc_html($combo['price']) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Envíos -->
      <div>
        <h3 style="font-size:28px;margin:0 0 10px;">Método de envío</h3>
        <div class="shipping-methods">
          <?php foreach ( $shipping_zones as $zone ) : ?>
          <label class="ship-option">
            <input type="radio" name="shipping_method"
                   value="<?= esc_attr($zone['value']) ?>"
                   data-base-price="<?= esc_attr($zone['price']) ?>"
                   data-free-threshold="<?= esc_attr($zone['free_threshold']) ?>"
                   <?= $zone['checked'] ? 'checked' : '' ?> />
            <span><strong><?= esc_html($zone['label']) ?></strong><span class="eta"> · <?= esc_html($zone['eta']) ?></span></span>
            <strong class="ship-price-display">Q<?= esc_html(number_format((float)$zone['price'],2)) ?></strong>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Formulario -->
      <form id="quick-checkout-form" novalidate>
        <h3 style="font-size:32px;text-transform:uppercase;margin:4px 0 8px;">Ingresa tus datos completos</h3>
        <div class="checkout-form-grid">
          <div class="field"><label for="cf-name">Nombre completo *</label><input id="cf-name" name="name" required /></div>
          <div class="field"><label for="cf-phone">Teléfono alterno (casa/oficina)</label><input id="cf-phone" name="phone" /></div>
          <div class="field"><label for="cf-whatsapp">Número de WhatsApp *</label><input id="cf-whatsapp" name="whatsapp" required /></div>
          <div class="field"><label for="cf-country">País *</label><select id="cf-country" name="country" required><option value="">Detectando país...</option><option value="GT">Guatemala</option><option value="SV">El Salvador</option><option value="HN">Honduras</option><option value="US">Estados Unidos</option></select></div>
          <div class="field"><label for="cf-department">Departamento / Estado *</label><select id="cf-department" name="department" required><option value="">Selecciona uno</option><option value="Alta Verapaz">Alta Verapaz</option><option value="Baja Verapaz">Baja Verapaz</option><option value="Chimaltenango">Chimaltenango</option><option value="Chiquimula">Chiquimula</option><option value="El Progreso">El Progreso</option><option value="Escuintla">Escuintla</option><option value="Guatemala">Guatemala</option><option value="Huehuetenango">Huehuetenango</option><option value="Izabal">Izabal</option><option value="Jalapa">Jalapa</option><option value="Jutiapa">Jutiapa</option><option value="Peten">Petén</option><option value="Quetzaltenango">Quetzaltenango</option><option value="Quiche">Quiché</option><option value="Retalhuleu">Retalhuleu</option><option value="Sacatepequez">Sacatepéquez</option><option value="San Marcos">San Marcos</option><option value="Santa Rosa">Santa Rosa</option><option value="Solola">Sololá</option><option value="Suchitepequez">Suchitepéquez</option><option value="Totonicapan">Totonicapán</option><option value="Zacapa">Zacapa</option></select></div>
          <div class="field"><label for="cf-municipality">Municipio / Ciudad *</label><input id="cf-municipality" name="municipality" list="municipality-list" required /><datalist id="municipality-list"></datalist></div>
          <div class="field"><label for="cf-zone">Zona *</label><input id="cf-zone" name="zone" placeholder="Ejemplo: 1, 7, Mixco" required /></div>
          <div class="field"><label for="cf-area-colony">Colonia / Área *</label><input id="cf-area-colony" name="area_colony" list="area-colony-list" placeholder="Ejemplo: La Limonada" required /><datalist id="area-colony-list"></datalist></div>
          <div class="field"><label for="cf-reference">Referencia de entrega</label><input id="cf-reference" name="reference" placeholder="Ejemplo: Portón azul, casa #12" /></div>
          <div class="field"><label for="cf-nit">NIT *</label><input id="cf-nit" name="nit" placeholder="Ingresa NIT o CF" required /><div class="nit-actions"><button type="button" class="btn btn-danger" id="validate-nit-btn" aria-label="Validar NIT">Validar NIT</button><button type="button" class="btn btn-success" id="cf-btn" aria-label="Consumidor Final">CF</button></div><p id="nit-status" class="nit-status"></p></div>
          <div class="field"><label for="cf-email">Correo electrónico</label><input id="cf-email" name="email" type="email" /></div>
          <div class="field"><label for="cf-pay-method">2. Método de pago *</label><select id="cf-pay-method" name="pay_method" required><option value="">Selecciona método de pago</option><option value="card">Tarjeta (Crédito/Débito)</option><option value="cod">Pago contra entrega</option></select><p style="margin:8px 0 0;font-size:12px;font-weight:700;color:#d8d8d8;">Tarjetas y métodos aceptados</p><div class="card-brands"><span class="brand-chip" title="Visa" style="background:#ffffff;border-color:#cbd5e1;"><img src="https://cdn.simpleicons.org/visa/1A1F71" alt="Visa" loading="lazy" /></span><span class="brand-chip" title="Mastercard" style="background:#ffffff;border-color:#cbd5e1;"><img src="https://cdn.simpleicons.org/mastercard/EB001B" alt="Mastercard" loading="lazy" /></span><span class="brand-chip" title="American Express" style="background:#ffffff;border-color:#cbd5e1;"><img src="https://cdn.simpleicons.org/americanexpress/2E77BC" alt="American Express" loading="lazy" /></span><span class="brand-chip" title="Apple Pay" style="background:#ffffff;border-color:#cbd5e1;"><img src="https://cdn.simpleicons.org/applepay/000000" alt="Apple Pay" loading="lazy" /></span><span class="brand-chip" title="Stripe" style="background:#ffffff;border-color:#cbd5e1;"><img src="https://cdn.simpleicons.org/stripe/635BFF" alt="Stripe" loading="lazy" /></span></div></div>
          <div class="field full"><label for="cf-address">Dirección completa *</label><textarea id="cf-address" name="address" required></textarea></div>
        </div>

        <!-- Add-on -->
        <div class="addon-box" style="margin-top:10px;">
          <label>
            <input id="addon-check" type="checkbox"
                   data-addon-price="<?= esc_attr($addon_price) ?>"
                   data-addon-name="<?= esc_attr($addon_label) ?>" />
            <?= esc_html($addon_label) ?> (+Q<?= esc_html(number_format((float)$addon_price,2)) ?>)
          </label>
          <div>Combinación perfecta para tu rutina de recuperación cervical. <strong><span class="addon-clock" data-countdown-minutes="12">10:00</span></strong></div>
        </div>

        <p id="coverage-status" class="coverage-status" aria-live="polite"></p>
        <div class="checkout-actions" style="margin-top:12px;">
          <button class="btn btn-primary" type="submit">Proceder al pago seguro</button>
          <a id="cod-link" class="btn btn-success" href="#" style="display:none;">Enviar solicitud contra entrega</a>
        </div>
        <p class="hint">Si eliges pago contra entrega, te contactaremos para validar cobertura y programar la entrega.</p>
      </form>
    </div>
</section>
