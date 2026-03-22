<?php
/**
 * Mundo Accesorios Theme functions and definitions
 */

if ( ! function_exists( 'mundoaccesorios_setup' ) ) :
	function mundoaccesorios_setup() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'customize-selective-refresh-widgets' ); // Activar lápices azules
		// Soporte para WooCommerce nativo
		add_theme_support( 'woocommerce' );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		) );
	}
endif;
add_action( 'after_setup_theme', 'mundoaccesorios_setup' );

// ── Core del Tema ──────────────────────────────────────────────────────────
require_once get_template_directory() . '/inc/settings.php'; // Página de ajustes del tema
require_once get_template_directory() . '/inc/customizer.php'; // Personalizador visual (Shopify-style)
require_once get_template_directory() . '/inc/cpt.php';      // Custom Post Types (reviews, FAQs)
require_once get_template_directory() . '/inc/api.php';      // REST API: /wp-json/ma/v1/checkout
require_once get_template_directory() . '/inc/feedback.php';           // Sistema de reseñas con links de estrellas
require_once get_template_directory() . '/inc/visitor-tracker.php';    // Tracking de visitantes por hora/día
require_once get_template_directory() . '/inc/n8n-dashboard.php';      // Dashboard de automatización n8n (backend)
require_once get_template_directory() . '/inc/analytics-dashboard.php'; // Dashboard Analytics tipo Shopify
require_once get_template_directory() . '/inc/packing-list.php';        // Packing list imprimible + NIT en pedido
require_once get_template_directory() . '/inc/order-tracking.php';      // Rastreo de guías multi-portadora (Forza/Cargo Expreso)
require_once get_template_directory() . '/inc/kanban-orders.php';        // Kanban board de pedidos por fases (drag-drop, WA, fotos)
require_once get_template_directory() . '/inc/wms-inventory.php';        // WMS: stock, recepción, put-away, movimientos
require_once get_template_directory() . '/inc/suppliers.php';             // 👥 Maestro de Proveedores (SAP Vendor Master)
require_once get_template_directory() . '/inc/wms-dashboard.php';        // WMS: dashboard KPIs + picking + reportes por fase
require_once get_template_directory() . '/inc/warehouse/loader.php';     // Módulo Multi-Almacén: CPTs, stock WAC, OC, SLA, UltraMsg
require_once get_template_directory() . '/inc/sales/loader.php';         // 📞 Módulo Televentas: POS Call Center y Comisiones
require_once get_template_directory() . '/inc/reports/loader.php';       // 📊 Módulo Business Intelligence: ERP y Dashboard Estadístico
require_once get_template_directory() . '/inc/erp-strictness.php';       // 🛡️ Candados Contables Antifraude (Nivel SAP/Odoo)
require_once get_template_directory() . '/inc/caex-metabox.php';          // 🚚 Cargo Expreso: cotizar, generar guía, rastrear
require_once get_template_directory() . '/inc/order-attribution.php';      // 📊 Fuente de pedidos: UTM tracking, Google, Facebook, Instagram
require_once get_template_directory() . '/inc/drop-tools.php';             // 🛒 Drop Tools: KPIs CAEX, calculadora precio, validador producto
require_once get_template_directory() . '/inc/forza-metabox.php';          // 🟠 Forza Delivery GT: guías, rastreo, regiones prioridad
require_once get_template_directory() . '/inc/order-costs.php';            // 💰 Costos por pedido: Costo proveedor + Pauta para Dashboard Financiero
require_once get_template_directory() . '/inc/ai-content-generator.php';   // 🤖 Generador IA: Gemini copy + imágenes
require_once get_template_directory() . '/inc/product-landing-meta.php';   // 🎨 Metabox: Landing por Producto
require_once get_template_directory() . '/inc/core-license.php';           // 🔒 Capa Anti-Piratería y Licenciamiento SaaS (FastAPI / HMAC)
require_once get_template_directory() . '/inc/nit-validator.php';        // Validador NIT en tiempo real via FastAPI SAT Guatemala
require_once get_template_directory() . '/inc/b2b-dropshipping.php';      // 🌐 Sync B2B (Shopify / Woo) Multi-tienda + WA Notifier

/**
 * ── Indicador Visual de Dependencia API Externa (White-Label) ──────
 * Muestra un pequeño badge con animación CSS (pulso) para indicar módulos
 * que requieren de configuración o conexión a una API externa o SaaS.
 */
function ma_external_api_badge( $service_name, $tooltip = '' ) {
    $tooltip_html = $tooltip ? ' title="' . esc_attr($tooltip) . '"' : '';
    return '<span class="ma-api-indicator"'.$tooltip_html.' style="display:inline-flex;align-items:center;gap:4px;background:#eef2ff;color:#4f46e5;font-size:10px;font-weight:700;padding:2px 6px;border-radius:99px;border:1px solid #c7d2fe;cursor:help;vertical-align:middle;margin-left:6px;line-height:1;"><span style="display:inline-block;width:6px;height:6px;background:#4f46e5;border-radius:50%;animation:ma-api-pulse 2s infinite;"></span> ' . esc_html($service_name) . '</span>';
}
add_action('admin_head', function() {
    echo '<style>@keyframes ma-api-pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); } }</style>';
});

// Limpieza del dashboard (Eliminar noticias, blogs, etc.)
function mundoaccesorios_clean_dashboard() {
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // De un vistazo
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');    // Actividad
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');   // Borrador rápido
    remove_meta_box('dashboard_primary', 'dashboard', 'side');       // Noticias WP
}
add_action('wp_dashboard_setup', 'mundoaccesorios_clean_dashboard');

// Eliminar menús innecesarios para una tienda
function mundoaccesorios_remove_menus() {
    remove_menu_page('edit.php');                   // Entradas (Blog)
    remove_menu_page('edit-comments.php');          // Comentarios
}
add_action('admin_menu', 'mundoaccesorios_remove_menus');

/**
 * ── Reordenamiento del menú admin por prioridad de negocio ───────────
 *
 * Orden lógico para dropshipping:
 *   1. Operaciones diarias  → Escritorio, Pedidos, Drop Tools
 *   2. Inteligencia         → Analytics, Análisis, Kanban, WMS, Marketing
 *   3. Catálogo             → Productos, WooCommerce
 *   4. Contenido            → Páginas, Testimonios, FAQs, Medios
 *   5. Sistema              → Portadora exts, Apariencia, Plugins, Usuarios, Herramientas, Ajustes
 */
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', function( $menu_order ) {
    return [
        // ── 🏠 Inicio ──────────────────────────────────
        'index.php',                            // Escritorio
        'separator1',

        // ── 📦 OPERACIONES (uso diario) ────────────────
        'edit.php?post_type=shop_order',        // Pedidos
        'admin.php?page=ma-drop-tools',         // 🛒 Drop Tools

        'separator-last',

        // ── 📊 INTELIGENCIA DE NEGOCIO ─────────────────
        'admin.php?page=ma-analytics',          // 📈 Analytics
        'admin.php?page=ma-kanban',             // 🗂️ Kanban
        'admin.php?page=ma-wms',                // 🏭 WMS
        'admin.php?page=wc-admin&path=/analytics/overview', // Análisis WooCommerce
        'admin.php?page=ma-marketing',          // 📣 Marketing (si existe)

        'separator2',

        // ── 🛍️ CATÁLOGO ────────────────────────────────
        'edit.php?post_type=product',           // Productos
        'woocommerce',                          // WooCommerce (configuración)

        'separator3',

        // ── 📄 CONTENIDO ───────────────────────────────
        'edit.php?post_type=page',              // Páginas
        'edit.php?post_type=ma_testimonial',    // Testimonios
        'edit.php?post_type=ma_faq',            // FAQs
        'upload.php',                           // Medios
        'admin.php?page=dropify',               // Dropify

        // ── ⚙️ SISTEMA ─────────────────────────────────
        'themes.php',                           // Apariencia
        'plugins.php',                          // Plugins
        'users.php',                            // Usuarios
        'tools.php',                            // Herramientas
        'options-general.php',                  // Ajustes
        'separator-last',
    ];
});

/**
 * Limpieza Profunda UI: Desactivar "Atribución de Pedido" (Order Attribution)
 * Esta función anula el motor de UTMs y Referrals de WooCommerce 8.5+, lo cual
 * borra la misteriosa metabox vacía "Origen Desconocido" y ahorra valiosos recursos.
 */
add_filter('wc_order_attribution_tracking_enabled', '__return_false');

/**
 * CSS global admin: corrige desbordamiento de columnas en tablas WP admin
 * (incluye columnas del plugin Dropi que se muestran verticalmente).
 */
add_action( 'admin_head', function() {
    ?>
    <style id="ma-admin-global-css">
    /* Tablas admin: scroll horizontal en lugar de desbordamiento vertical */
    .wp-list-table-wrapper, .tablenav, .wrap > .wp-list-table { overflow-x: auto; }
    table.wp-list-table { table-layout: auto; min-width: 100%; }

    /* Columnas Dropi/Sync con texto vertical — forzar ancho y alineación */
    .wp-list-table .column-dropi_product_id,
    .wp-list-table .column-dropi_sync,
    .wp-list-table .column-dropi,
    .wp-list-table td[class*="dropi"],
    .wp-list-table th[class*="dropi"] {
        width: auto !important;
        min-width: 80px;
        max-width: 120px;
        white-space: normal;
        word-break: normal;
        overflow-wrap: anywhere;
        vertical-align: middle;
        text-align: center;
    }

    /* Acciones hover en filas (links debajo del nombre) */
    .wp-list-table .row-actions { white-space: normal; }

    /* WMS/Kanban/OC — evitar tablas desbordadas en pantallas < 900px */
    @media (max-width: 900px) {
        .wrap .widefat { table-layout: auto; }
        .wrap .widefat td, .wrap .widefat th { word-break: break-word; }
    }
    </style>
    <?php
} );

/**
 * ── Moneda principal: Quetzal Guatemalteco (GTQ) ────────────────
 *
 * Filtra WooCommerce para usar Q como moneda principal.
 * Compatible con plugins multi-moneda (WOOCS, Currency Switcher, etc.)
 * que manejan las conversiones sin afectar esta base.
 *
 * Para cambiar a multi-moneda sin romper nada, los plugins simplemente
 * añaden sus propios filtros con mayor prioridad.
 */
// ── Moneda base: Quetzal guatemalteco (GTQ) ──────────────────────────────
// Un único filtro por responsabilidad; sin placeholder vacío que cause conflicto.
add_filter( 'woocommerce_currency', function() { return 'GTQ'; }, 5 );
add_filter( 'woocommerce_currency_symbol', function( $symbol, $currency ) {
    return $currency === 'GTQ' ? 'Q' : $symbol;
}, 10, 2 );
// Forzar opciones de BD para garantizar coherencia incluso si la config en admin dice USD
add_filter( 'pre_option_woocommerce_currency',          function() { return 'GTQ'; } );
add_filter( 'pre_option_woocommerce_currency_pos',      function() { return 'left'; } );
add_filter( 'pre_option_woocommerce_price_decimal_sep',  function() { return '.'; } );
add_filter( 'pre_option_woocommerce_price_thousand_sep', function() { return ','; } );
add_filter( 'pre_option_woocommerce_price_num_decimals', function() { return '2'; } );

/**
 * Helper: símbolo de moneda activo de WooCommerce.
 * Usar SIEMPRE este helper en lugar de 'Q' literal en cualquier archivo del tema.
 * Ejemplo: echo ma_currency() . number_format($amount, 2);
 */
if ( ! function_exists('ma_currency') ) {
    function ma_currency(): string {
        return function_exists('get_woocommerce_currency_symbol')
            ? get_woocommerce_currency_symbol()
            : 'Q';
    }
}

/**
 * ── Geolocalización sin MaxMind ───────────────────────────────────
 *
 * WooCommerce intenta usar MaxMind GeoIP para detectar la ubicación
 * del cliente automáticamente. Como somos una tienda 100% Guatemala,
 * usamos la dirección base de la tienda (GT) sin depender de ningún
 * servicio externo ni licencia de terceros.
 *
 * Esto elimina el aviso "No se ha configurado la geolocalización"
 * y evita llamadas externas en cada carga de página.
 *
 * El cliente puede corregir su país/estado en el checkout si difiere.
 */
// Forzar  ubicación base (tienda) en lugar de geolocalización IP
add_filter( 'pre_option_woocommerce_default_customer_address', function() {
    return 'base'; // 'base' = usa dirección de la tienda → Guatemala
} );

// Establecer país y estado base cuando se usa 'base'
add_filter( 'pre_option_woocommerce_default_country', function() {
    return 'GT:GT'; // Guatemala : Ciudad de Guatemala (departamento)
} );

// Eliminar el aviso de MaxMind/Geolocalización usando los hooks correctos de WC
add_action( 'admin_init', function() {
    // 1. Marcar el aviso como "visto/descartado" en la opción de WooCommerce
    $notices = get_option( 'woocommerce_admin_notices', [] );
    if ( in_array( 'maxmind_license_key', $notices, true ) ) {
        $notices = array_diff( $notices, [ 'maxmind_license_key' ] );
        update_option( 'woocommerce_admin_notices', $notices );
    }
    // 2. Suprimir el re-registro del notice en cada carga
    add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );
} );

// 3. Interceptar antes de que WC registre el aviso de geo en woocommerce_admin_notices
add_filter( 'woocommerce_admin_notices', function( $notices ) {
    return is_array( $notices )
        ? array_diff( $notices, [ 'maxmind_license_key', 'no_secure_connection', 'update' ] )
        : $notices;
} );

// 4. CSS fallback — ocultar el aviso de geo por su contenido (por si WC lo inyecta directamente)
add_action( 'admin_head', function() {
    echo '<style>.woocommerce-message:has(a[href*="maxmind"]),.woocommerce-BlueNotice:has(a[href*="maxmind"]){display:none!important}</style>';
} );

/**
 * ── Guía de dimensiones de medios ────────────────────────────────
 *
 * Muestra un panel compacto y descartable en la Biblioteca de Medios
 * y en el editor de productos de WooCommerce con las medidas exactas
 * recomendadas para imágenes, GIFs, videos y móvil.
 *
 * UX: el panel puede ocultarse con el botón ✕ y se recuerda con
 * localStorage por 7 días para no molestar al usuario.
 */
add_action( 'admin_footer', function() {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if ( ! $screen ) return;

    // Solo en: Biblioteca de Medios, edición de producto, nuevo producto
    $allowed = [ 'upload', 'product', 'post' ];
    if ( ! in_array( $screen->base, $allowed, true ) ) return;
    ?>
    <div id="ma-media-guide" style="
        position:fixed;bottom:20px;right:20px;z-index:99999;
        background:#0f172a;color:#f1f5f9;
        border-radius:12px;padding:16px 20px 14px;
        max-width:320px;width:calc(100vw - 40px);
        font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        font-size:13px;box-shadow:0 8px 32px rgba(0,0,0,.35);
        line-height:1.5;display:none">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <strong style="font-size:14px;color:#fff">📐 Guía de Medios</strong>
            <button onclick="maDismissGuide()" title="Cerrar"
                style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:18px;line-height:1;padding:0">✕</button>
        </div>

        <!-- IMÁGENES PRODUCTO -->
        <div style="margin-bottom:10px">
            <div style="font-size:11px;font-weight:700;color:#38bdf8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">📷 Imagen de Producto</div>
            <div style="color:#cbd5e1"><strong style="color:#fff">1000 × 1000 px</strong> — cuadrada (1:1)</div>
            <div style="color:#94a3b8;font-size:12px">JPG / WebP · máx 300 KB · fondo blanco</div>
        </div>

        <!-- GIF ANIMADO -->
        <div style="margin-bottom:10px">
            <div style="font-size:11px;font-weight:700;color:#a78bfa;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">🎞️ GIF Animado</div>
            <div style="color:#cbd5e1"><strong style="color:#fff">600 × 600 px</strong> — cuadrado (1:1)</div>
            <div style="color:#94a3b8;font-size:12px">GIF · máx 3 MB · 15-24 fps · 3-6 seg · sin fondo negro</div>
        </div>

        <!-- VIDEO -->
        <div style="margin-bottom:10px">
            <div style="font-size:11px;font-weight:700;color:#34d399;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">🎬 Video de Producto</div>
            <div style="color:#cbd5e1"><strong style="color:#fff">1080 × 1080 px</strong> — cuadrado (1:1)</div>
            <div style="color:#94a3b8;font-size:12px">MP4 (H.264) · máx 15 MB · 15-30 seg · mudo o con audio</div>
            <div style="color:#94a3b8;font-size:12px">También: <strong style="color:#e2e8f0">1080 × 1920 px</strong> (vertical 9:16 para Stories/Reels)</div>
        </div>

        <!-- MÓVIL -->
        <div style="background:#1e293b;border-radius:8px;padding:8px 10px;font-size:12px">
            <div style="color:#f59e0b;font-weight:700;margin-bottom:3px">📱 Consejo Móvil</div>
            <div style="color:#cbd5e1">Siempre <strong style="color:#fff">cuadrado 1:1</strong> — se ve perfecto en galería de producto y feed de redes. Usa fondos neutros (blanco/gris claro). Evita texto pequeño.</div>
        </div>
    </div>

    <script>
    (function(){
        var KEY = 'ma_guide_hidden';
        var el  = document.getElementById('ma-media-guide');
        if(!el) return;

        var hidden = localStorage.getItem(KEY);
        var ts     = hidden ? parseInt(hidden, 10) : 0;
        var week   = 7 * 24 * 3600 * 1000;

        if(!hidden || (Date.now() - ts > week)){
            el.style.display = 'block';
        }

        window.maDismissGuide = function(){
            el.style.transition = 'opacity .3s,transform .3s';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(10px)';
            setTimeout(function(){ el.style.display='none'; }, 300);
            localStorage.setItem(KEY, Date.now().toString());
        };

        // También aparece al abrir el modal de subida (para Uploader)
        document.addEventListener('click', function(e){
            if(e.target.classList.contains('browser-uploader') ||
               e.target.id === 'insert-media-button' ||
               (e.target.closest && e.target.closest('.upload-ui'))){
                el.style.display='block';
            }
        });
    })();
    </script>
    <?php
} );

/**
 * Enqueue scripts and styles.
 */
function mundoaccesorios_scripts() {
	wp_enqueue_style( 'mundoaccesorios-style', get_stylesheet_uri(), array(), '1.0' );

	    wp_enqueue_script( 'mundoaccesorios-main', get_template_directory_uri() . '/assets/js/main.js', array(), '2.0.3', true );

    $product_price = 49;
    $product_id = 0;
    if ( class_exists('WooCommerce') ) {
        $recent_products = wc_get_products( array( 'limit' => 1, 'return' => 'objects' ) );
        if ( !empty( $recent_products ) ) {
            $product_price = $recent_products[0]->get_price();
            $product_id = $recent_products[0]->get_id();
        }
    }

    wp_localize_script( 'mundoaccesorios-main', 'mundoaccesorios_data', array(
        // REST API (reemplaza admin-ajax)
        'rest_url'             => rest_url( 'ma/v1/checkout' ),
        'rest_nit_url'         => rest_url( 'ma/v1/nit/' ),
        'nonce'                => wp_create_nonce( 'wp_rest' ),
        // Datos del producto
        'product_price'        => $product_price ? $product_price : 49,
        'product_id'           => $product_id,
        // Add-on nombre desde settings (para el payload)
        'addon_name'           => ma_get( 'addon_label', 'Compresa Gel Frio-Calor' ),
        // Pago con tarjeta — Stripe Payment Link (configurar en settings)
        'stripe_payment_link'  => ma_get( 'stripe_payment_link', '' ),
        // WhatsApp admin (para zonas restringidas y notificaciones)
        'whatsapp_admin'       => ma_get( 'whatsapp_admin_phone', '' ),
        // WhatsApp soporte para zonas restringidas (usa admin si no hay uno específico)
        'cod_whatsapp'         => ma_get( 'whatsapp_admin_phone', '50255551234' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'mundoaccesorios_scripts' );

// Inyeccion automatica de SEO Básico, OpenGraph y JSON-LD Schema (Marketing Skill)
function mundoaccesorios_seo_meta_tags() {
    if ( is_front_page() || is_home() ) {
        // Basicos y OpenGraph
        echo '<meta name="description" content="Alivia la tension cervical en solo 10 minutos al dia con nuestro Masajeador Inteligente. Ideal para estres y mala postura. ¡Envio a toda Guatemala!" />' . "\n";
        echo '<meta property="og:title" content="Mundo Accesorios | Especialistas en Dropshipping GT" />' . "\n";
        echo '<meta property="og:description" content="Productos innovadores con envío contra entrega a toda Guatemala. Compra en 1 clic." />' . "\n";
        echo '<meta property="og:type" content="website" />' . "\n";
        echo '<meta name="robots" content="index, follow" />' . "\n";

        // JSON-LD Product Schema Avanzado
        if ( class_exists('WooCommerce') ) {
            $recent_products = wc_get_products( array( 'limit' => 1 ) );
            if ( !empty( $recent_products ) ) {
                $product = $recent_products[0];
                $img_url = wp_get_attachment_url($product->get_image_id());
                
                $schema = array(
                    "@context" => "https://schema.org/",
                    "@type" => "Product",
                    "name" => $product->get_name(),
                    "image" => $img_url ? [$img_url] : [],
                    "description" => $product->get_short_description() ? wp_strip_all_tags($product->get_short_description()) : "Excelente producto con envio a toda Guatemala.",
                    "sku" => $product->get_sku() ? $product->get_sku() : "MA-" . $product->get_id(),
                    "offers" => array(
                        "@type" => "Offer",
                        "url" => get_site_url(),
                        "priceCurrency" => "GTQ",
                        "price" => $product->get_price() ?: '0.00',
                        "priceValidUntil" => date('Y-m-d', strtotime('+1 year')),
                        "availability" => $product->is_in_stock() ? "https://schema.org/InStock" : "https://schema.org/OutOfStock"
                    )
                );
                echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
            }
        }
    }
}
add_action( 'wp_head', 'mundoaccesorios_seo_meta_tags', 2 );

// ==========================================
// COLUMNAS PERSONALIZADAS EN BACKEND WOOCOMMERCE
// ==========================================

// 1. Agregar las columnas a la tabla
add_filter( 'manage_edit-shop_order_columns', 'mundo_custom_order_columns', 20 );
function mundo_custom_order_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;
        if ( 'order_status' === $column_name ) {
            $new_columns['order_whatsapp']  = 'WhatsApp';
            $new_columns['order_nit']       = 'NIT';
            $new_columns['order_reference'] = 'Referencia';
        }
    }
    return $new_columns;
}

// 2. Rellenar las columnas con la metadata
add_action( 'manage_shop_order_posts_custom_column', 'mundo_custom_order_column_content', 20, 2 );
function mundo_custom_order_column_content( $column, $post_id ) {
    $order = wc_get_order( $post_id );
    if ( ! $order ) return;

    if ( 'order_whatsapp' === $column ) {
        $wa = $order->get_meta( '_billing_whatsapp' );
        if ( $wa ) {
            $wa_clean = preg_replace('/[^0-9]/', '', $wa);
            echo '<a href="https://wa.me/502' . esc_attr( $wa_clean ) . '" target="_blank" style="color:#25D366;font-weight:bold;">💬 ' . esc_html( $wa ) . '</a>';
        } else {
            echo '<span style="color:#ccc;">-</span>';
        }
    }
    
    if ( 'order_nit' === $column ) {
        $nit = $order->get_meta( '_billing_nit' );
        if ( $nit ) {
            $is_cf = ( strtoupper(trim($nit)) === 'CF' );
            echo $is_cf ? '<span style="background:#e0e0e0;padding:2px 6px;border-radius:4px;font-size:11px;">CF</span>' : '<strong>' . esc_html( $nit ) . '</strong>';
        } else {
            echo '<span style="color:#ccc;">-</span>';
        }
    }

    if ( 'order_reference' === $column ) {
        $ref = $order->get_meta( '_shipping_reference' );
        echo $ref ? esc_html( $ref ) : '<span style="color:#ccc;">-</span>';
    }
}

// ==========================================
// FORZAR EMBUDO (FUNNEL) EN TODAS LAS URLS
// ==========================================
// Previene que WooCommerce muestre sus plantillas feas por defecto
// y fuerza a que cualquier ruta (Shop, Cart, Checkout) muestre nuestro funnel.
// add_filter( 'template_include', 'mundoaccesorios_force_pro_max_funnel', 999 );
/*
function mundoaccesorios_force_pro_max_funnel( $template ) {
    if ( ! is_admin() && ! wp_is_json_request() && ! wp_doing_ajax() ) {
        $funnel_template = get_template_directory() . '/front-page.php';
        if ( file_exists( $funnel_template ) ) {
            return $funnel_template;
        }
    }
    return $template;
}
*/


// FIX: Prevent Infinite 301 Canonical Redirect Loops
remove_filter('template_redirect', 'redirect_canonical');

// FIX: Disable WooCommerce Template Redirects that cause infinite loops on custom routing
remove_action('template_redirect', 'wc_template_redirect');

// ============================================================
// INTEGRACIÓN WOOCOMMERCE → n8n (Dual canal: WA + Email)
// Webhook URLs configurables desde Apariencia → ⚙ Mundo Accesorios
// Identificador: source = "mundoaccesorios"
// ============================================================

/**
 * Envía un webhook async a n8n con los datos del pedido.
 */
function ma_n8n_send( string $webhook_url, array $payload ): void {
    if ( empty( $webhook_url ) ) return;
    $payload['source'] = 'mundoaccesorios'; // Identificador de origen
    wp_remote_post( $webhook_url, [
        'method'      => 'POST',
        'timeout'     => 3,        // Async — no bloquea el flujo del usuario
        'blocking'    => false,
        'headers'     => [ 'Content-Type' => 'application/json' ],
        'body'        => wp_json_encode( $payload ),
        'sslverify'   => true,
    ] );
}

/**
 * Hook: Pedido nuevo (processing) → n8n: Bienvenida + Confirmación (WA + Email)
 */
add_action( 'woocommerce_order_status_processing', function( int $order_id ) {
    $wh = ma_get( 'n8n_webhook_pedido_nuevo', 'https://n8n.modularis.pro/webhook/ma-pedido-nuevo' );
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // Primer item del pedido como producto principal
    $items    = $order->get_items();
    $item     = reset( $items );
    $product  = $item ? $item->get_name() : 'Masajeador de Cuello';
    // WhatsApp: campo personalizado primero, sino billing_phone
    $wa_raw   = $order->get_meta( '_billing_whatsapp' ) ?: $order->get_billing_phone();
    $phone    = preg_replace( '/[^0-9]/', '', $wa_raw );

    ma_n8n_send( $wh, [
        'order_id'      => $order_id,
        'name'          => $order->get_billing_first_name(),
        'email'         => $order->get_billing_email(),
        'phone'         => $phone,
        'total'         => $order->get_total(),
        'product'       => $product,
        'billing_nit'   => $order->get_meta( '_billing_nit' ),
    ] );
}, 10, 1 );

/**
 * Hook: Pedido completado → programa solicitud de reseña en 7 días
 */
add_action( 'woocommerce_order_status_completed', function( int $order_id ) {
    $wh = ma_get( 'n8n_webhook_solicitar_resena', 'https://n8n.modularis.pro/webhook/ma-solicitar-resena' );
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    $wa_raw = $order->get_meta( '_billing_whatsapp' ) ?: $order->get_billing_phone();
    $phone  = preg_replace( '/[^0-9]/', '', $wa_raw );

    // Programar envío en 7 días (604800 segundos)
    if ( ! wp_next_scheduled( 'ma_send_review_request', [ $order_id ] ) ) {
        wp_schedule_single_event( time() + 604800, 'ma_send_review_request', [ $order_id ] );
    }

    // Guardar datos del pedido en meta para recuperarlos cuando se ejecute el cron
    $order->update_meta_data( '_ma_review_payload', wp_json_encode( [
        'order_id' => $order_id,
        'name'     => $order->get_billing_first_name(),
        'email'    => $order->get_billing_email(),
        'phone'    => $phone,
        'webhook'  => $wh,
    ] ) );
    $order->save();
}, 10, 1 );

// Cron event: ejecutar solicitud de reseña
add_action( 'ma_send_review_request', function( int $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;
    $payload_raw = $order->get_meta( '_ma_review_payload' );
    if ( ! $payload_raw ) return;
    $payload = json_decode( $payload_raw, true );
    $wh = $payload['webhook'] ?? ma_get( 'n8n_webhook_solicitar_resena', 'https://n8n.modularis.pro/webhook/ma-solicitar-resena' );
    ma_n8n_send( $wh, $payload );
}, 10, 1 );

/**
 * Hook: Carrito con datos guardados → n8n: Carrito Abandonado (WA + recordatorio)
 * Se activa cuando se guarda el teléfono en sesión (campo del checkout).
 * WordPress/WooCommerce no tiene carrito abandonado nativo, esto lo detecta
 * al guardar los campos del checkout (usuario escribe datos pero no completa).
 */
add_action( 'woocommerce_checkout_update_order_review', function( string $post_data_raw ) {
    // Solo disparar si hay sesión activa con teléfono
    parse_str( $post_data_raw, $post_data );
    $phone = preg_replace( '/[^0-9]/', '', $post_data['billing_phone'] ?? '' );
    $name  = sanitize_text_field( $post_data['billing_first_name'] ?? '' );
    $email = sanitize_email( $post_data['billing_email'] ?? '' );
    if ( ! $phone || strlen( $phone ) < 8 ) return;

    $wh = ma_get( 'n8n_webhook_carrito_abandonado', 'https://n8n.modularis.pro/webhook/ma-carrito-abandonado' );

    // Guardar en sesión para disparar si no completa en 2h
    WC()->session?->set( 'ma_cart_lead', [
        'phone'    => $phone,
        'name'     => $name,
        'email'    => $email,
        'time'     => time(),
        'webhook'  => $wh,
        'product'  => 'Masajeador de Cuello',
        'cart_url' => get_permalink( wc_get_page_id( 'shop' ) ) ?: home_url( '/shop/' ),
    ] );
}, 10, 1 );

/**
 * ── Limpieza de Interfaz UX (Menú de Administración) ────────────────
 * Ocultamos menús nativos innecesarios para evitar distracciones a los operadores,
 * ya que el ERP tiene sus propios dashboards integrados (Paso 1, 2, 3...).
 */
add_action( 'admin_menu', function() {
    // Solo si el usuario es un administrador de tienda o superior
    if ( current_user_can( 'manage_woocommerce' ) ) {
        remove_menu_page( 'wc-admin&path=/analytics/overview' ); // Analytics Nativo
        remove_menu_page( 'woocommerce-marketing' );            // Marketing Nativo
    }
}, 999 );
