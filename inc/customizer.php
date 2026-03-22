<?php
/**
 * inc/customizer.php — Personalizador del Tema Estilo Shopify
 */

function ma_customize_register( $wp_customize ) {

    if ( class_exists('WP_Customize_Control') && !class_exists('MA_Customize_Reset_Button_Control') ) {
        class MA_Customize_Reset_Button_Control extends WP_Customize_Control {
            public $type = 'reset_button';
            public function render_content() {
                $fields_json = isset($this->input_attrs['data-fields']) ? esc_attr($this->input_attrs['data-fields']) : '[]';
                ?>
                <button type="button" class="button button-secondary" data-fields="<?php echo $fields_json; ?>" style="width:100%; margin:15px 0 5px; background:#fff1f2; border-color:#fda4af; color:#be123c;" onclick="
                    let btn = this;
                    let fields = [];
                    try { fields = JSON.parse(btn.getAttribute('data-fields')); } catch(e){}
                    if (fields && fields.length > 0) {
                        fields.forEach(f => {
                            let id = 'ma_settings[' + f + ']';
                            if ( wp.customize(id) ) wp.customize(id).set('');
                        });
                        setTimeout(() => alert('¡Campos limpiados con éxito! \\n\\nLos valores ahora dependerán 100% de la configuración predeterminada o del producto específico. Haz clic en PUBLICAR.'), 100);
                    } else {
                        // Fallback heredado para Combos si no hay data-fields explícito
                        let base = ['combo1', 'combo2', 'combo3'];
                        let props = ['_label', '_sublabel', '_qty', '_subtotal', '_price_display', '_hide'];
                        base.forEach(f => {
                            props.forEach(p => {
                                let id = 'ma_settings[' + f + p + ']';
                                if ( wp.customize(id) ) wp.customize(id).set('');
                            });
                        });
                        setTimeout(() => alert('¡Combos limpiados con éxito! \\n\\nLos valores ahora dependerán 100% de WooCommerce de forma dinámica. Haz clic en PUBLICAR.'), 100);
                    }
                ">🗑️ Limpiar Esta Sección</button>
                <p class="description" style="margin-top:0;">Restaura los valores por defecto de la tienda vaciando las cajas locales.</p>
                <?php
            }
        }
    }

    // Crear Panel Principal
    $wp_customize->add_panel( 'ma_theme_options', [
        'title'       => '⚙️ Secciones del Tema',
        'description' => 'Edita las secciones estilo Shopify y previsualiza los cambios al instante.',
        'priority'    => 130,
    ] );

    // Definir la estructura de Secciones y Controles
    $sections = [
        'ma_sec_logo' => [
            'title' => '🎨 Identidad (Logo)',
            'fields' => [
                'logo_url'  => ['type' => 'image', 'label' => 'URL del logo (imagen)', 'selector' => '#site-logo img' ],
                'logo_text' => ['type' => 'text', 'label' => 'Texto del logo', 'selector' => '#site-logo span' ],
            ]
        ],
        'ma_sec_hero' => [
            'title' => '🦸‍♂️ Sección Hero',
            'fields' => [
                'hide_sec_hero' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'hero_image_url'      => ['type' => 'image', 'label' => 'Imagen Hero (URL)', 'selector' => '.hero-media img' ],
                'hero_kicker'         => ['type' => 'text', 'label' => 'Kicker (subtítulo pequeño)', 'selector' => '.hero-kicker' ],
                'hero_title'          => ['type' => 'textarea', 'label' => 'Título H1', 'selector' => '.hero-content h1' ],
                'hero_subtitle'       => ['type' => 'textarea', 'label' => 'Subtítulo / Lead', 'selector' => '.hero-content .lead' ],
                'hero_price_old'      => ['type' => 'text', 'label' => 'Precio anterior', 'selector' => '.hero-content .old' ],
                'hero_price_new'      => ['type' => 'text', 'label' => 'Precio actual', 'selector' => '.hero-content .new' ],
                'hero_discount_label' => ['type' => 'text', 'label' => 'Etiqueta descuento', 'selector' => '.hero-content .discount' ],
                'hero_btn_primary'    => ['type' => 'text', 'label' => 'Botón principal', 'selector' => '.hero-content .btn-primary' ],
                'hero_btn_secondary'  => ['type' => 'text', 'label' => 'Botón secundario', 'selector' => '.hero-content .btn-secondary' ],
                'reset_sec_hero' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_ticker' => [
            'title' => '📣 Ticker',
            'fields' => [
                'hide_sec_ticker' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'ticker_text' => ['type' => 'text', 'label' => 'Texto del ticker', 'selector' => '.ma-ticker p' ],
                'reset_sec_ticker' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_features' => [
            'title' => '✨ Características',
            'fields' => [
                'hide_sec_features' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'features_title' => ['type' => 'text', 'label' => 'Título sección', 'selector' => '#features h2' ],
                'feat1_title'    => ['type' => 'text', 'label' => 'Característica 1 — Título', 'selector' => '#features .feature-grid article:nth-child(1) h3' ],
                'feat1_desc'     => ['type' => 'textarea', 'label' => 'Característica 1 — Desc', 'selector' => '#features .feature-grid article:nth-child(1) p' ],
                'feat2_title'    => ['type' => 'text', 'label' => 'Característica 2 — Título', 'selector' => '#features .feature-grid article:nth-child(2) h3' ],
                'feat2_desc'     => ['type' => 'textarea', 'label' => 'Característica 2 — Desc', 'selector' => '#features .feature-grid article:nth-child(2) p' ],
                'feat3_title'    => ['type' => 'text', 'label' => 'Característica 3 — Título', 'selector' => '#features .feature-grid article:nth-child(3) h3' ],
                'feat3_desc'     => ['type' => 'textarea', 'label' => 'Característica 3 — Desc', 'selector' => '#features .feature-grid article:nth-child(3) p' ],
                'reset_sec_features' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_stats' => [
            'title' => '📊 Estadísticas',
            'fields' => [
                'hide_sec_stats' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'stats_title'   => ['type' => 'text', 'label' => 'Título', 'selector' => '#stats h2' ],
                'stats_intro'   => ['type' => 'textarea', 'label' => 'Subtítulo', 'selector' => '#stats p.lead' ],
                'stats_img'     => ['type' => 'image', 'label' => 'Imagen', 'selector' => '#stats img' ],
                'stats_img_alt' => ['type' => 'text', 'label' => 'Alt de imagen' ],
                'stats_items'   => ['type' => 'textarea', 'label' => 'Estadísticas (porcentaje|texto)', 'selector' => '#stats .stat-grid' ],
                'reset_sec_stats' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_comp' => [
            'title' => '⚖️ Comparativa',
            'fields' => [
                'hide_sec_comp' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'comp_title' => ['type' => 'text', 'label' => 'Título', 'selector' => '#comparacion h2' ],
                'comp_intro' => ['type' => 'textarea', 'label' => 'Subtítulo', 'selector' => '#comparacion p.section-intro' ],
                'comp_prod'  => ['type' => 'text', 'label' => 'Nombre nuestro producto', 'selector' => '#comparacion .comparative-table th:nth-child(2)' ],
                'comp_otros' => ['type' => 'text', 'label' => 'Nombre competencia', 'selector' => '#comparacion .comparative-table th:nth-child(3)' ],
                'comp_rows'  => ['type' => 'textarea', 'label' => 'Filas (característica|si|no)', 'selector' => '#comparacion .comparative-table tbody' ],
                'reset_sec_comp' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_reviews' => [
            'title' => '⭐ Testimonios',
            'fields' => [
                'hide_sec_reviews' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'stat1_num'   => ['type' => 'text', 'label' => 'Stat 1 — Número', 'selector' => '#reviews .review-stat:nth-child(1)' ],
                'stat1_label' => ['type' => 'text', 'label' => 'Stat 1 — Etiqueta', 'selector' => '#reviews .review-stat:nth-child(1) span' ],
                'stat2_num'   => ['type' => 'text', 'label' => 'Stat 2 — Número', 'selector' => '#reviews .review-stat:nth-child(2)' ],
                'stat2_label' => ['type' => 'text', 'label' => 'Stat 2 — Etiqueta', 'selector' => '#reviews .review-stat:nth-child(2) span' ],
                'stat3_num'   => ['type' => 'text', 'label' => 'Stat 3 — Número', 'selector' => '#reviews .review-stat:nth-child(3)' ],
                'stat3_label' => ['type' => 'text', 'label' => 'Stat 3 — Etiqueta', 'selector' => '#reviews .review-stat:nth-child(3) span' ],
                'reset_sec_reviews' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_demo' => [
            'title' => '🎥 Demo / Galería',
            'fields' => [
                'hide_sec_demo' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'demo_title'     => ['type' => 'text', 'label' => 'Título', 'selector' => '#demo h2' ],
                'demo_video_src' => ['type' => 'text', 'label' => 'URL video', 'selector' => '#demo video' ],
                'demo_poster'    => ['type' => 'image', 'label' => 'Poster video' ],
                'demo_gallery'   => ['type' => 'textarea', 'label' => 'Galería (tipo|url|alt)', 'selector' => '.gallery, .img-grid' ],
                'reset_sec_demo' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_ba' => [
            'title' => '📸 Antes y Después',
            'fields' => [
                'hide_sec_ba' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'ba_title'         => ['type' => 'text', 'label' => 'Título', 'selector' => '#before-after h2' ],
                'ba_intro'         => ['type' => 'textarea', 'label' => 'Subtítulo', 'selector' => '#before-after p.section-intro' ],
                'ba_label_antes'   => ['type' => 'text', 'label' => 'Etiqueta "Antes"', 'selector' => '#before-after .ba-before' ],
                'ba_label_despues' => ['type' => 'text', 'label' => 'Etiqueta "Después"', 'selector' => '#before-after .ba-after' ],
                'ba_img_antes'     => ['type' => 'image', 'label' => 'Subir: Foto "Antes"' ],
                'ba_img_despues'   => ['type' => 'image', 'label' => 'Subir: Foto "Después"' ],
                'ba_pairs'         => ['type' => 'textarea', 'label' => 'Pares avanzados (texto manual)', 'selector' => '#before-after .ba-grid' ],
                'reset_sec_ba' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_det' => [
            'title' => '📦 Detalles del producto',
            'fields' => [
                'hide_sec_det' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'det_title'        => ['type' => 'text', 'label' => 'Título sección', 'selector' => '#detalle h2' ],
                'det_espec'        => ['type' => 'text', 'label' => 'Título — Especificaciones', 'selector' => '#detalle article.detail-box:nth-child(1) h3' ],
                'det_specs'        => ['type' => 'textarea', 'label' => 'Especificaciones', 'selector' => '#detalle .spec-table' ],
                'det_inbox'        => ['type' => 'text', 'label' => 'Título "En la caja"', 'selector' => '#detalle article.detail-box:nth-child(2) h3:nth-of-type(1)' ],
                'det_inbox_items'  => ['type' => 'textarea', 'label' => 'Contenido del paquete', 'selector' => '#detalle article.detail-box:nth-child(2) ul.list:nth-of-type(1)' ],
                'det_compat'       => ['type' => 'text', 'label' => 'Título "Compatibilidad"', 'selector' => '#detalle article.detail-box:nth-child(2) h3:nth-of-type(2)' ],
                'det_compat_items' => ['type' => 'textarea', 'label' => 'Compatibilidad', 'selector' => '#detalle article.detail-box:nth-child(2) ul.list:nth-of-type(2)' ],
                'reset_sec_det' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_combos' => [
            'title' => '🛒 Combos / Paquetes',
            'fields' => [
                'hide_sec_combos' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'combo1_label'         => ['type' => 'text', 'label' => 'Combo 1: Etiqueta', 'selector' => '#comprar .combo-card:nth-child(1) .badge' ],
                'combo1_sublabel'      => ['type' => 'text', 'label' => 'Combo 1: Sub-etiqueta', 'selector' => '#comprar .combo-card:nth-child(1) h3' ],
                'combo1_qty'           => ['type' => 'text', 'label' => 'Combo 1: Cantidad' ],
                'combo1_subtotal'      => ['type' => 'text', 'label' => 'Combo 1: Subtotal' ],
                'combo1_price_display' => ['type' => 'text', 'label' => 'Combo 1: Precio visual', 'selector' => '#comprar .combo-card:nth-child(1) .price' ],
                
                'combo2_label'         => ['type' => 'text', 'label' => 'Combo 2: Etiqueta', 'selector' => '#comprar .combo-card:nth-child(2) .badge' ],
                'combo2_sublabel'      => ['type' => 'text', 'label' => 'Combo 2: Sub-etiqueta', 'selector' => '#comprar .combo-card:nth-child(2) h3' ],
                'combo2_qty'           => ['type' => 'text', 'label' => 'Combo 2: Cantidad' ],
                'combo2_subtotal'      => ['type' => 'text', 'label' => 'Combo 2: Subtotal' ],
                'combo2_price_display' => ['type' => 'text', 'label' => 'Combo 2: Precio visual', 'selector' => '#comprar .combo-card:nth-child(2) .price' ],
                
                'combo3_label'         => ['type' => 'text', 'label' => 'Combo 3: Etiqueta', 'selector' => '#comprar .combo-card:nth-child(3) .badge' ],
                'combo3_sublabel'      => ['type' => 'text', 'label' => 'Combo 3: Sub-etiqueta', 'selector' => '#comprar .combo-card:nth-child(3) h3' ],
                'combo3_qty'           => ['type' => 'text', 'label' => 'Combo 3: Cantidad' ],
                'combo3_subtotal'      => ['type' => 'text', 'label' => 'Combo 3: Subtotal' ],
                'combo3_price_display' => ['type' => 'text', 'label' => 'Combo 3: Precio visual', 'selector' => '#comprar .combo-card:nth-child(3) .price' ],
                
                'combo_reset_btn'      => ['type' => 'reset_button', 'label' => 'Limpiar Valores' ],
                'reset_sec_combos' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_shipping' => [
            'title' => '🚚 Zonas de Envío',
            'fields' => [
                'hide_sec_shipping' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'ship1_label' => ['type' => 'text', 'label' => 'Zona 1: Nombre', 'selector' => '#envio .bg-white:nth-child(1) h3' ],
                'ship1_eta'   => ['type' => 'text', 'label' => 'Zona 1: ETA', 'selector' => '#envio .bg-white:nth-child(1) p' ],
                'ship1_price' => ['type' => 'text', 'label' => 'Zona 1: Precio', 'selector' => '#envio .bg-white:nth-child(1) .text-2xl' ],
                
                'ship2_label' => ['type' => 'text', 'label' => 'Zona 2: Nombre', 'selector' => '#envio .bg-white:nth-child(2) h3' ],
                'ship2_eta'   => ['type' => 'text', 'label' => 'Zona 2: ETA', 'selector' => '#envio .bg-white:nth-child(2) p' ],
                'ship2_price' => ['type' => 'text', 'label' => 'Zona 2: Precio', 'selector' => '#envio .bg-white:nth-child(2) .text-2xl' ],
                
                'ship3_label' => ['type' => 'text', 'label' => 'Zona 3: Nombre', 'selector' => '#envio .bg-white:nth-child(3) h3' ],
                'ship3_eta'   => ['type' => 'text', 'label' => 'Zona 3: ETA', 'selector' => '#envio .bg-white:nth-child(3) p' ],
                'ship3_price' => ['type' => 'text', 'label' => 'Zona 3: Precio', 'selector' => '#envio .bg-white:nth-child(3) .text-2xl' ],
                'reset_sec_shipping' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_crosssell' => [
            'title' => '🔀 Cross-sell',
            'fields' => [
                'hide_sec_crosssell' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'cs_title'  => ['type' => 'text', 'label' => 'Título', 'selector' => '#crosssell h2' ],
                'cs1_name'  => ['type' => 'text', 'label' => 'Prod 1: Nombre', 'selector' => '#crosssell article:nth-child(1) h3' ],
                'cs1_price' => ['type' => 'text', 'label' => 'Prod 1: Precio', 'selector' => '#crosssell article:nth-child(1) p' ],
                'cs1_img'   => ['type' => 'image', 'label' => 'Prod 1: Img', 'selector' => '#crosssell article:nth-child(1) img' ],
                'cs1_btn'   => ['type' => 'text', 'label' => 'Prod 1: Botón', 'selector' => '#crosssell article:nth-child(1) a.btn' ],
                
                'cs2_name'  => ['type' => 'text', 'label' => 'Prod 2: Nombre', 'selector' => '#crosssell article:nth-child(2) h3' ],
                'cs2_price' => ['type' => 'text', 'label' => 'Prod 2: Precio', 'selector' => '#crosssell article:nth-child(2) p' ],
                'cs2_img'   => ['type' => 'image', 'label' => 'Prod 2: Img', 'selector' => '#crosssell article:nth-child(2) img' ],
                'cs2_btn'   => ['type' => 'text', 'label' => 'Prod 2: Botón', 'selector' => '#crosssell article:nth-child(2) a.btn' ],
                
                'cs3_name'  => ['type' => 'text', 'label' => 'Prod 3: Nombre', 'selector' => '#crosssell article:nth-child(3) h3' ],
                'cs3_price' => ['type' => 'text', 'label' => 'Prod 3: Precio', 'selector' => '#crosssell article:nth-child(3) p' ],
                'cs3_img'   => ['type' => 'image', 'label' => 'Prod 3: Img', 'selector' => '#crosssell article:nth-child(3) img' ],
                'cs3_btn'   => ['type' => 'text', 'label' => 'Prod 3: Botón', 'selector' => '#crosssell article:nth-child(3) a.btn' ],
                'reset_sec_crosssell' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_oferta' => [
            'title' => '🎯 Oferta Especial',
            'fields' => [
                'hide_sec_oferta' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'oferta_title' => ['type' => 'text', 'label' => 'Título', 'selector' => '#oferta h2' ],
                'oferta_items' => ['type' => 'textarea', 'label' => 'Beneficios', 'selector' => '#oferta ul' ],
                'oferta_flash' => ['type' => 'textarea', 'label' => 'Bono flash', 'selector' => '#oferta .flash-bonus, #oferta .bg-blue-800' ],
                'oferta_btn'   => ['type' => 'text', 'label' => 'Botón', 'selector' => '#oferta a.btn' ],
                'reset_sec_oferta' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
        'ma_sec_pago_cta' => [
            'title' => '💳 Checkout y CTA',
            'fields' => [
                'hide_sec_pago_cta' => ['type' => 'checkbox', 'label' => '❌ Ocultar esta sección' ],
                'pago_title' => ['type' => 'text', 'label' => 'Pago - Título', 'selector' => '#pago h2' ],
                'pago_intro' => ['type' => 'textarea', 'label' => 'Pago - Subtítulo', 'selector' => '#pago p.lead' ],
                'cta_title'  => ['type' => 'text', 'label' => 'CTA - Título', 'selector' => '#finalcta h2' ],
                'cta_desc'   => ['type' => 'textarea', 'label' => 'CTA - Descripción', 'selector' => '#finalcta p.lead' ],
                'cta_btn'    => ['type' => 'text', 'label' => 'CTA - Botón', 'selector' => '#finalcta a.btn' ],
                'stripe_payment_link' => ['type' => 'text', 'label' => 'Stripe Payment Link URL' ],
                'reset_sec_pago_cta' => ['type' => 'reset_button', 'label' => '🧹 Limpiar Valores de Sección' ],
            ]
        ],
    ];

    foreach ( $sections as $section_id => $section_args ) {
        // Enlazar al Panel
        $wp_customize->add_section( $section_id, [
            'title' => $section_args['title'],
            'panel' => 'ma_theme_options',
        ] );

        // Agregar Settings y Controls
        foreach ( $section_args['fields'] as $key => $field ) {
            // Guardamos usando el array global "ma_settings" existente
            $setting_id = "ma_settings[{$key}]";

            $wp_customize->add_setting( $setting_id, [
                // 'refresh' recarga la previsualización al instante
                'transport' => 'refresh',
                'type' => 'option',
            ] );

            if ( $field['type'] === 'image' ) {
                $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $setting_id, [
                    'label'   => $field['label'],
                    'section' => $section_id,
                    'settings'=> $setting_id,
                ] ) );
            } elseif ( $field['type'] === 'reset_button' ) {
                $keys = array_keys( $section_args['fields'] );
                $keys = array_diff( $keys, [ $key ] ); // exclude button itself
                $wp_customize->add_control( new MA_Customize_Reset_Button_Control( $wp_customize, $setting_id, [
                    'label'   => $field['label'],
                    'section' => $section_id,
                    'settings'=> $setting_id,
                    'input_attrs' => [
                        'data-fields' => json_encode(array_values($keys))
                    ]
                ] ) );
            } else {
                $wp_customize->add_control( $setting_id, [
                    'label'   => $field['label'],
                    'section' => $section_id,
                    'type'    => $field['type'],
                ] );
            }

            // Registrar Lápiz (Edit Shortcut) para TODOS los controles que tengan selector definido
            if ( !empty($field['selector']) ) {
                $wp_customize->selective_refresh->add_partial( $setting_id, [
                    'selector' => $field['selector'],
                    'render_callback' => '__return_false', // Refresh manejado globalmente
                ] );
            }
        }
    }

}
add_action( 'customize_register', 'ma_customize_register' );
