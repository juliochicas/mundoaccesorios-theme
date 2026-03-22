<?php
/**
 * inc/settings.php — Panel de ajustes del tema
 * Apariencia > ⚙ Mundo Accesorios
 */

// ── Helper global: leer setting con fallback ─────────────────────────────
if ( ! function_exists('ma_get') ) {
    function ma_get( string $key, string $default = '' ): string {
        // Fallback a Custom Meta (Prioridad 1 para Productos)
        if ( function_exists('is_singular') && is_singular('product') ) {
            $meta = get_post_meta( get_queried_object_id(), '_ma_' . $key, true );
            if ( $meta !== '' && $meta !== false ) {
                return (string) $meta;
            }
        }
        
        // Fallback a Theme Settings (Prioridad 2)
        $opts = get_option('ma_settings', []);
        return isset($opts[$key]) && $opts[$key] !== '' ? (string) $opts[$key] : $default;
    }
}

// ── Menú en el admin ─────────────────────────────────────────────────────
add_action( 'admin_menu', function() {
    add_theme_page(
        '⚙ Mundo Accesorios',
        '⚙ Mundo Accesorios',
        'manage_options',
        'ma-settings',
        'ma_settings_page'
    );
});

// ── Guardar settings ─────────────────────────────────────────────────────
add_action( 'admin_post_ma_save_settings', function() {
    if ( ! current_user_can('manage_options') ) wp_die('Sin permiso');
    check_admin_referer('ma_settings_nonce');

    $opts = [];
    $text_fields = [
        // SEO
        'seo_title','seo_description','seo_og_image','seo_whatsapp',
        // WHATSAPP
        'whatsapp_admin_phone',
        // STRIPE
        'stripe_payment_link',
        // TRACKING (Clarity, Meta Pixel, GA4, Webhook n8n)
        'clarity_project_id', 'meta_pixel_id', 'ga4_measurement_id',
        'n8n_webhook_url', 'n8n_webhook_secret',
        // CARGO EXPRESO FastAPI
        'caex_fastapi_url',
        // IA DIRECT KEY (Gemini Vision)
        'gemini_api_key',
        // OPERACIONES / WMS / KANBAN
        'op_business_name',       // Nombre del negocio
        'op_currency_symbol',     // Símbolo de moneda (Q, $, S/…)
        'op_risk_days',           // Días sin moverse → alerta riesgo
        'op_nit_api_url',         // URL endpoint validación NIT
        'op_nit_api_method',      // GET o POST
        // Mensajes WhatsApp por fase
        'op_wa_pendiente', 'op_wa_pagado', 'op_wa_transito', 'op_wa_entregado', 'op_wa_problema',
        // SLA por fase (horas)
        'op_sla_pendiente', 'op_sla_pagado', 'op_sla_transito', 'op_sla_entregado', 'op_sla_problema',
        // UltraMsg — Notificaciones push a operadores
        'ultramsg_instance_id',   // ID de instancia UltraMsg
        'ultramsg_token',         // Token de autenticación UltraMsg
        'ultramsg_notif_phone',   // Número que recibe alertas (con código país)
    ];

    foreach ( $text_fields as $f ) {
        $opts[$f] = sanitize_textarea_field( wp_unslash( $_POST[$f] ?? '' ) );
    }
    update_option( 'ma_settings', $opts );
    wp_redirect( add_query_arg(['page'=>'ma-settings','updated'=>1], admin_url('themes.php')) );
    exit;
});

// ── Renderizar página ─────────────────────────────────────────────────────
function ma_settings_page() {
    if ( ! current_user_can('manage_options') ) return;
    $opts = get_option('ma_settings', []);
    $v = fn($k, $d='') => esc_attr($opts[$k] ?? $d);
    $ta = fn($k, $d='') => esc_textarea($opts[$k] ?? $d);
    $updated = isset($_GET['updated']);
    ?>
    <div class="wrap">
      <h1>⚙ Mundo Accesorios — Configuración del Tema</h1>
      <?php if ($updated): ?><div class="notice notice-success"><p>✅ Guardado correctamente.</p></div><?php endif; ?>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="ma_save_settings" />
        <?php wp_nonce_field('ma_settings_nonce'); ?>

        <!-- Las opciones visuales fueron movidas al Personalizador (Appearance > Customize) -->
        <div class="notice notice-info inline"><p>🎨 <strong>Secciones Visuales:</strong> Para editar la portada (Hero, Testimonios, Combos, etc.) ve a <strong><a href="<?php echo admin_url('customize.php'); ?>">Apariencia > Personalizar</a></strong> donde tendrás previsualización en vivo.</p></div>


        <!-- ── SEO ───────────────────────────────────────────────── -->
        <h2 class="title">🔍 SEO y Redes Sociales</h2><table class="form-table"><tbody>
          <tr><th>Meta título</th><td><input class="large-text" name="seo_title" value="<?= $v('seo_title') ?>" /></td></tr>
          <tr><th>Meta descripción</th><td><textarea class="large-text" name="seo_description" rows="2"><?= $ta('seo_description') ?></textarea></td></tr>
          <tr><th>OG Image (URL)</th><td><input class="large-text" name="seo_og_image" value="<?= $v('seo_og_image') ?>" /></td></tr>
          <tr><th>WhatsApp de soporte (sin +, ej: 50299887766)</th><td><input name="seo_whatsapp" value="<?= $v('seo_whatsapp') ?>" /></td></tr>
        </tbody></table>

        <!-- ── WHATSAPP NOTIFICACIONES ───────────────────────────────── -->
        <h2 class="title">📱 Notificaciones WhatsApp</h2><table class="form-table"><tbody>
          <tr><th>Teléfono del admin (con código país)</th><td><input name="whatsapp_admin_phone" value="<?= $v('whatsapp_admin_phone','502') ?>" /><p class="description">Número que recibirá la notificación de cada nuevo pedido. Ej: <strong>50250509136</strong> (502 + 8 dígitos). Dejar vacío para desactivar.</p></td></tr>
        </tbody></table>

        <!-- ── STRIPE / PAGO CON TARJETA ────────────────────────────── -->
        <h2 class="title">💳 Stripe — Pago con Tarjeta</h2><table class="form-table"><tbody>
          <tr>
            <th>Stripe Payment Link</th>
            <td>
              <input class="large-text" name="stripe_payment_link" value="<?= $v('stripe_payment_link') ?>" placeholder="https://buy.stripe.com/xxx..." />
              <p class="description">
                Crea un <strong>Payment Link</strong> en <a href="https://dashboard.stripe.com/payment-links" target="_blank">dashboard.stripe.com/payment-links</a> y pega la URL aquí.<br/>
                Cuando el cliente elige "Tarjeta (Stripe)", se crea la orden en WooCommerce y luego se redirige automáticamente a este link.<br/>
                <strong>Nota:</strong> Agrega <code>?client_reference_id={order_id}</code> al link para rastrear pedidos (el sistema lo hace automáticamente).
              </p>
            </td>
          </tr>
        </tbody></table>

        <?php submit_button('💾 Guardar todos los cambios'); ?>

        <!-- ── n8n WEBHOOKS ──────────────────────────────────────── -->
        <h2 class="title">🤖 n8n — Automatización (WA + Email)</h2>
        <p>Estos webhooks se disparan automáticamente desde WooCommerce. Cópialos desde <strong>n8n → Workflow → Webhook node → URL de prueba/producción</strong>.</p>
        <table class="form-table"><tbody>
          <tr>
            <th>Webhook: Pedido Nuevo</th>
            <td>
              <input class="large-text" name="n8n_webhook_pedido_nuevo" value="<?= $v('n8n_webhook_pedido_nuevo') ?>" placeholder="https://n8n.modularis.pro/webhook/ma-pedido-nuevo" />
              <p class="description">Se dispara cuando el pago es procesado. Envía confirmación por WhatsApp + Email.</p>
            </td>
          </tr>
          <tr>
            <th>Webhook: Carrito Abandonado</th>
            <td>
              <input class="large-text" name="n8n_webhook_carrito_abandonado" value="<?= $v('n8n_webhook_carrito_abandonado') ?>" placeholder="https://n8n.modularis.pro/webhook/ma-carrito-abandonado" />
              <p class="description">Se guarda cuando el cliente llena su teléfono en el checkout y no completa. Envía recordatorio WhatsApp.</p>
            </td>
          </tr>
          <tr>
            <th>Webhook: Solicitar Reseña (7 días)</th>
            <td>
              <input class="large-text" name="n8n_webhook_solicitar_resena" value="<?= $v('n8n_webhook_solicitar_resena') ?>" placeholder="https://n8n.modularis.pro/webhook/ma-solicitar-resena" />
              <p class="description">Se dispara automáticamente 7 días después de que el pedido se marca como completado. Envía solicitud de reseña por WA + Email.</p>
            </td>
          </tr>
        </tbody></table>

        <!-- ── INTELIGENCIA ARTIFICIAL ───────────────────────────────── -->
        <?php if ( current_user_can('manage_options') ): ?>
        <h2 class="title" style="margin-top:20px">✨ Inteligencia Artificial (Gemini)</h2>
        <table class="form-table"><tbody>
          <tr><th>🔒 Gemini API Key (Directa)</th>
            <td><input class="large-text" name="gemini_api_key" value="<?= $v('gemini_api_key') ?>" type="password" placeholder="AIzaSy..." />
                <p class="description">Para habilitar el <strong>análisis predictivo de imágenes</strong> (Vision), pega una API Key gratuita de <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>. De lo contrario, la IA solo servirá texto-a-texto mediante el servidor central.</p>
            </td></tr>
        </tbody></table>
        <?php endif; ?>

        <!-- ── TRACKING & ANALYTICS (100% Gratuito) ────────────── -->
        <h2 class="title">📊 Tracking & Analytics (100% Gratuito)</h2>
        <p>Configura los IDs de tus herramientas de tracking. Todo se inyecta automáticamente en el sitio. <strong>No necesitas tocar el código.</strong></p>
        <table class="form-table"><tbody>
          <tr>
            <th>🔥 Microsoft Clarity ID<br><small style="font-weight:400;color:#888">Heatmaps + Grabaciones gratis</small></th>
            <td>
              <input class="regular-text" name="clarity_project_id" value="<?= $v('clarity_project_id') ?>" placeholder="Ej: abc123xyz" />
              <p class="description">Crea tu proyecto en <a href="https://clarity.microsoft.com" target="_blank">clarity.microsoft.com</a> → copia el ID → pega aquí. <strong>Gratis e ilimitado.</strong> Verás mapas de calor, clicks y grabaciones de sesión.</p>
            </td>
          </tr>
          <tr>
            <th>🎯 Meta Pixel ID<br><small style="font-weight:400;color:#888">Facebook/Instagram Ads</small></th>
            <td>
              <input class="regular-text" name="meta_pixel_id" value="<?= $v('meta_pixel_id') ?>" placeholder="Ej: 1234567890123456" />
              <p class="description">Administrador de Meta → Eventos → Píxeles → copia el ID numérico. <strong>Gratis</strong> con tu cuenta Meta Business.</p>
            </td>
          </tr>
          <tr>
            <th>📈 Google Analytics 4 ID<br><small style="font-weight:400;color:#888">Tráfico + comportamiento</small></th>
            <td>
              <input class="regular-text" name="ga4_measurement_id" value="<?= $v('ga4_measurement_id') ?>" placeholder="Ej: G-XXXXXXXXXX" />
              <p class="description">Google Analytics → Admin → Flujos de datos → copia el Measurement ID (empieza con G-). <strong>Gratis.</strong></p>
            </td>
          </tr>
        </tbody></table>

        <?php submit_button('💾 Guardar todos los cambios'); ?>

        <!-- ══════════════════════════════════════════════════════════════
             OPERACIONES — WMS / KANBAN / NIT
             Roles: manage_woocommerce  → puede ver y editar operaciones
                    manage_options (admin) → además puede editar API keys
        ══════════════════════════════════════════════════════════════ -->
        <?php if ( current_user_can('manage_woocommerce') ): ?>
        <h2 class="title">🏭 Operaciones — WMS, Kanban &amp; NIT</h2>
        <p style="max-width:620px">Configura el sistema sin tocar código. Los campos marcados con 🔒 son técnicos y solo los puede editar el <strong>Administrador</strong>. El <em>Shop Manager</em> puede editar el resto.</p>
        <table class="form-table"><tbody>

          <tr>
            <th>Nombre del negocio</th>
            <td>
              <input class="regular-text" name="op_business_name"
                     value="<?= $v('op_business_name', get_bloginfo('name') ?: 'Mi Negocio') ?>" />
              <p class="description">Se inserta en los mensajes de WhatsApp en lugar de <code>{nombre_negocio}</code>. Cambiarlo es el primer paso al hacer onboarding con un nuevo cliente.</p>
            </td>
          </tr>

          <tr>
            <th>Símbolo de moneda</th>
            <td>
              <input name="op_currency_symbol" value="<?= $v('op_currency_symbol', 'Q') ?>" size="6" />
              <p class="description">Ej: <strong>Q</strong> (Quetzal), <strong>$</strong> (Dólar), <strong>S/</strong> (Sol). Se muestra en el Kanban, WMS e inventario.</p>
            </td>
          </tr>

          <tr>
            <th>Días para alerta de riesgo</th>
            <td>
              <input type="number" name="op_risk_days" value="<?= $v('op_risk_days', '3') ?>" size="5" min="1" max="30" />
              <p class="description">Un pedido se marca "en riesgo" si permanece en la misma fase este número de días o más. Recomendado: <strong>3</strong>.</p>
            </td>
          </tr>

          <?php if ( current_user_can('manage_options') ): ?>
          <tr>
            <th>🔒 URL API Validador NIT</th>
            <td>
              <input class="large-text" name="op_nit_api_url"
                     value="<?= $v('op_nit_api_url', 'https://fastapi.modularis.pro/api/fel/nit/') ?>" />
              <p class="description">Endpoint REST de validación NIT. El NIT se concatena al final: <code>{url}/{nit}</code>. Solo editable por Administrador.</p>
            </td>
          </tr>
          <tr>
            <th>🔒 Método HTTP del validador NIT</th>
            <td>
              <select name="op_nit_api_method">
                <?php $nm = $v('op_nit_api_method', 'GET'); ?>
                <option value="GET"  <?php selected($nm, 'GET');  ?>>GET  — NIT en URL</option>
                <option value="POST" <?php selected($nm, 'POST'); ?>>POST — NIT en body JSON</option>
              </select>
            </td>
          </tr>
          <?php endif; ?>

        </tbody></table>

        <h3 style="margin-top:20px">📱 Mensajes WhatsApp por Fase del Kanban</h3>
        <p style="max-width:620px">Personaliza el mensaje que se abre al presionar el botón 📱 WA en cada tarjeta del Kanban.<br>
           <strong>Tokens:</strong> <code>{nombre_negocio}</code> &nbsp;·&nbsp; <code>{id}</code> (ID pedido) &nbsp;·&nbsp; <code>{guia}</code> (nro. guía de envío)</p>
        <table class="form-table"><tbody>
        <?php
        $wa_cfg = [
            'op_wa_pendiente' => [ '🕐 Pendiente',
                '¡Hola! 👋 Te contactamos de *{nombre_negocio}*. Tu pedido #{id} está *pendiente de confirmación*. ¿Puedes verificar tu método de pago? Estamos aquí para ayudarte 🛍️' ],
            'op_wa_pagado'    => [ '💳 Pagado',
                '¡Hola! 😊 Tu pedido #{id} de *{nombre_negocio}* ya fue *confirmado y pagado* ✅. Estamos preparando tu envío. ¡Pronto lo recibirás! 🚀' ],
            'op_wa_transito'  => [ '🚚 En Tránsito',
                '📦 Tu pedido #{id} de *{nombre_negocio}* está *en camino* 🚚. Guía: *{guia}*. ¡Llegará muy pronto!' ],
            'op_wa_entregado' => [ '✅ Entregado',
                '✅ Tu pedido #{id} de *{nombre_negocio}* fue *entregado*. ¡Esperamos que lo disfrutes! Escríbenos si necesitas algo 💬' ],
            'op_wa_problema'  => [ '⚠️ Problema',
                '🔔 Hola, te contacta *{nombre_negocio}* sobre tu pedido #{id}. Tuvimos un inconveniente. ¿Puedes comunicarte con nosotros? 🙏' ],
        ];
        foreach ( $wa_cfg as $key => $cfg ):
            list($lbl, $def) = $cfg;
        ?>
        <tr>
          <th>Fase: <?php echo esc_html($lbl); ?></th>
          <td><textarea class="large-text" name="<?php echo esc_attr($key); ?>" rows="2"><?php echo esc_textarea( $opts[$key] ?? $def ); ?></textarea></td>
        </tr>
        <?php endforeach; ?>
        </tbody></table>

        <h3 style="margin-top:20px">⏱️ SLA — Alerta de Tiempo por Fase</h3>
        <p style="max-width:620px">Define el máximo de <strong>horas</strong> permitido en cada fase. Si se supera, se envía una alerta por WhatsApp (UltraMsg). <code>0</code> = sin límite.</p>
        <table class="form-table"><tbody>
        <?php
        $sla_cfg = [
            'op_sla_pendiente' => ['🕐 Pendiente',  '2'],
            'op_sla_pagado'    => ['💳 Pagado',      '4'],
            'op_sla_transito'  => ['🚚 En Tránsito', '72'],
            'op_sla_entregado' => ['✅ Entregado',   '0'],
            'op_sla_problema'  => ['⚠️ Problema',    '1'],
        ];
        foreach ($sla_cfg as $key => list($lbl, $def)): ?>
        <tr>
          <th><?php echo esc_html($lbl); ?></th>
          <td>
            <input type="number" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($opts[$key] ?? $def); ?>"
                   size="6" min="0" max="720" style="width:80px" />
            <span style="color:#888;margin-left:6px">horas (<code>0</code> = sin SLA)</span>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody></table>

        <?php if (current_user_can('manage_options')): ?>
        <h3 style="margin-top:20px">🔒 UltraMsg — Notificaciones Push a Operadores</h3>
        <p style="max-width:620px">Activa las alertas automáticas por WhatsApp cuando un pedido supera su SLA. Solo editable por Administrador.</p>
        <table class="form-table"><tbody>
          <tr>
            <th>🔒 Instance ID</th>
            <td>
              <input class="regular-text" name="ultramsg_instance_id" value="<?php echo esc_attr($opts['ultramsg_instance_id'] ?? ''); ?>" placeholder="instance12345" />
              <p class="description">En app.ultramsg.com → tu instancia → ID del panel.</p>
            </td>
          </tr>
          <tr>
            <th>🔒 Token</th>
            <td>
              <input class="regular-text" name="ultramsg_token" value="<?php echo esc_attr($opts['ultramsg_token'] ?? ''); ?>" placeholder="token_xxxxxxxxx" type="password" />
            </td>
          </tr>
          <tr>
            <th>🔒 Teléfono de alertas</th>
            <td>
              <input class="regular-text" name="ultramsg_notif_phone" value="<?php echo esc_attr($opts['ultramsg_notif_phone'] ?? ''); ?>" placeholder="50230000000" />
              <p class="description">Número con código de país, sin + ni espacios. Ej: <code>50230000000</code></p>
            </td>
          </tr>
        </tbody></table>
        <?php endif; // manage_options ?>

        <?php submit_button('💾 Guardar configuración operativa'); ?>
        <?php endif; // current_user_can('manage_woocommerce') ?>

      </form>
    </div>
    <?php
}
