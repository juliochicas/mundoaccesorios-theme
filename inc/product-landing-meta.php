<?php
/**
 * inc/product-landing-meta.php
 * Agrega un Metabox en WooCommerce para que cada Producto tenga su propia Landing Page + Colores
 */

add_action( 'add_meta_boxes', 'ma_register_landing_metabox' );
function ma_register_landing_metabox() {
    add_meta_box(
        'ma-producto-landing',
        '🎨 Diseño de Landing Page (Exclusivo de este Producto)',
        'ma_render_landing_metabox',
        'product',
        'normal',
        'high'
    );
}

function ma_render_landing_metabox( $post ) {
    wp_nonce_field( 'ma_landing_meta_nonce', 'ma_landing_meta_nonce_field' );
    
    // Función helper para no repetir html
    $get_val = fn($key) => get_post_meta( $post->ID, '_ma_' . $key, true );
    
    // Array de campos: Clave => Etiqueta
    $colors = [
        'color_bg'       => ['Fondo General', '#0B0B0C'],
        'color_surface'  => ['Superficies (Cards)', '#141416'],
        'color_primary'  => ['Color Principal (Botones/Acentos)', '#F97316'],
        'color_secondary'=> ['Color Secundario (Decoración)', '#8B5CF6'],
        'color_text'     => ['Color Texto Principal', '#F5F5F5'],
    ];

    $hero = [
        'hero_kicker'         => 'Hero Subtítulo Pequeño (Kicker)',
        'hero_title'          => 'Hero Título Principal (H1)',
        'hero_subtitle'       => 'Hero Párrafo (Lead)',
        'hero_btn_primary'    => 'Botón Principal',
        'hero_image_url'      => ['URL Imagen Hero (1000x1000)', '💡 Sugerido: Imagen Hero (Fondo Neutro) o Lifestyle'],
    ];

    $prices = [
        'hero_price_old'      => 'Precio Anterior (Tachado)',
        'hero_price_new'      => 'Precio Actual',
        'hero_discount_label' => 'Etiqueta Descuento (Ej: Ahorra 38%)',
        'ticker_text'         => 'Texto del Ticker de Urgencia',
    ];

    $extra_titles = [
        'demo_title' => 'Título Sección Demo/Video',
        'features_title' => 'Título Sección Beneficios',
        'ba_title' => 'Título Sección Antes/Después',
        'ba_intro' => 'Subtítulo Antes/Después',
        'ba_label_antes' => 'Etiqueta "Antes" (Sobre foto)',
        'ba_label_despues' => 'Etiqueta "Después" (Sobre foto)',
        'oferta_title' => 'Título Sección Oferta',
        'oferta_flash' => 'Texto Flash (Cintillo Urgencia)',
        'oferta_btn' => 'Texto Botón Oferta',
        'det_title' => 'Título Sección Detalles',
        'det_espec' => 'Subtítulo Especificaciones',
        'det_inbox' => 'Subtítulo Contenido Caja',
        'det_compat' => 'Subtítulo Compatibilidad',
    ];

    $features = [
        'feat1_title' => 'Beneficio 1 - Título', 'feat1_desc' => 'Beneficio 1 - Text',
        'feat2_title' => 'Beneficio 2 - Título', 'feat2_desc' => 'Beneficio 2 - Text',
        'feat3_title' => 'Beneficio 3 - Título', 'feat3_desc' => 'Beneficio 3 - Text',
    ];

    $combos_meta = [
        'combo1_label' => 'Combo 1: Nombre (Ej: 1x Producto)',
        'combo1_sublabel' => 'Combo 1: Subtítulo',
        'combo1_qty' => 'Combo 1: Cantidad Numérica (Ej: 1)',
        'combo1_subtotal' => 'Combo 1: Precio Base (Matemático, Ej: 49)',
        'combo1_price_display' => 'Combo 1: Precio a Mostrar (Ej: $49.00)',
        'combo1_hide' => 'Ocultar Combo 1 (Escribe "SI")',
        
        'combo2_label' => 'Combo 2: Nombre (Ej: 2x Promo)',
        'combo2_sublabel' => 'Combo 2: Subtítulo',
        'combo2_qty' => 'Combo 2: Cantidad Numérica (Ej: 2)',
        'combo2_subtotal' => 'Combo 2: Precio Base (Matemático)',
        'combo2_price_display' => 'Combo 2: Precio a Mostrar',
        'combo2_hide' => 'Ocultar Combo 2 (Escribe "SI")',
        
        'combo3_label' => 'Combo 3: Nombre',
        'combo3_sublabel' => 'Combo 3: Subtítulo',
        'combo3_qty' => 'Combo 3: Cantidad Numérica (Ej: 3)',
        'combo3_subtotal' => 'Combo 3: Precio Base (Matemático)',
        'combo3_price_display' => 'Combo 3: Precio a Mostrar',
        'combo3_hide' => 'Ocultar Combo 3 (Escribe "SI")',
    ];

    $reviews_meta = [
        'rev1_name' => 'Testimonio 1: Nombre', 'rev1_city' => 'Testimonio 1: Ciudad', 'rev1_text' => 'Testimonio 1: Reseña',
        'rev2_name' => 'Testimonio 2: Nombre', 'rev2_city' => 'Testimonio 2: Ciudad', 'rev2_text' => 'Testimonio 2: Reseña',
        'rev3_name' => 'Testimonio 3: Nombre', 'rev3_city' => 'Testimonio 3: Ciudad', 'rev3_text' => 'Testimonio 3: Reseña',
    ];

    $shipping_meta = [
        'ship1_label' => 'Envío 1: Ciudad (Ej: Guatemala City)', 'ship1_eta' => 'Envío 1: Tiempo', 'ship1_price' => 'Envío 1: Precio Base (Q)',
        'ship2_label' => 'Envío 2: Deptos (Ej: Departamentos)', 'ship2_eta' => 'Envío 2: Tiempo', 'ship2_price' => 'Envío 2: Precio Base (Q)',
        'ship3_label' => 'Envío 3: Express (Ej: Capital)', 'ship3_eta' => 'Envío 3: Tiempo', 'ship3_price' => 'Envío 3: Precio Base (Q)',
        'ship_free_city' => 'Monto Envío Gratis Ciudad (Ej: 250)', 'ship_free_depts' => 'Monto Envío Gratis Deptos (Ej: 300)',
    ];

    $addon_meta = [
        'addon_label' => 'Upsell/Complemento (Nombre)', 'addon_price' => 'Upsell/Complemento (Precio Q)',
    ];

    $assets = [
        'demo_video_src'=> ['URL Video Demo (Opcional)', '💡 Sugerido: Enlace a MP4 o YouTube'],
        'stats_img'   => ['URL Imagen Stats / Resultados', '💡 Sugerido: Ángulo 8 (Banner) o 2 (Lifestyle)'],
        'cs1_img'     => ['URL Imagen Cross-Sell / Lifestyle', '💡 Sugerido: Ángulo 2 (Lifestyle)'],
        'demo_poster' => ['URL Imagen Banner / Demo Tech', '💡 Sugerido: Ángulo 4 (Características)'],
        'demo_gallery_1' => ['URL Miniatura Galería 1', '💡 Sugerido: Ángulo 2 (Lifestyle)'],
        'demo_gallery_2' => ['URL Miniatura Galería 2', '💡 Sugerido: Ángulo 2 (Lifestyle)'],
        'demo_gallery_3' => ['URL Miniatura Galería 3', '💡 Sugerido: Ángulo 2 (Lifestyle)'],
        'ba_img'         => ['URL Imagen Antes vs Después (Foto Única Dividida)', '💡 Sugerido: Ángulo 3 (Antes vs Después)'],
        'ba_img_antes'   => ['[Opcional] URL Foto Individual "Antes"', '💡 Rellena esto y "Después" si prefieres formato de 2 cajones'],
        'ba_img_despues' => ['[Opcional] URL Foto Individual "Después"', '💡 Rellena esto si prefieres formato de 2 cajones (en vez de la Foto Única)'],
        'ba_caption'     => ['[Opcional] Texto descriptivo Antes/Después', 'Ej: Postura tensa → Alivio cervical'],
        'feat_img'       => ['URL Imagen Gráfico de Beneficios', '💡 Sugerido: Ángulo 1 (Gráfico Beneficios)'],
        'detalle_img'    => ['URL Imagen Infografía Completa', '💡 Sugerido: Ángulo 5 (Infografía Detalle)'],
        'review_img'  => ['URL Imagen Social Proof / Testimonio', '💡 Sugerido: Ángulo 6 (Testimonio Visual)'],
        'oferta_img'  => ['URL Imagen Retargeting / Urgencia', '💡 Sugerido: Ángulo 7 (Retargeting)'],
        'faq_img'     => ['URL Imagen Guía de Uso (How-To)', '💡 Sugerido: Ángulo 9 (Guía de Uso)'],
    ];

    $textareas = [
        'oferta_items' => ['Beneficios de Oferta (1 por línea)', "Descuento especial...\nGarantía de 30 días..."],
        'det_specs' => ['Especificaciones (Etiqueta|Valor por línea)', "Material|ABS\nBatería|Recargable"],
        'det_inbox_items' => ['Contenido de la Caja (1 por línea)', "1 Masajeador\n1 Cable USB"],
        'det_compat_items' => ['Compatibilidad / Usos (1 por línea)', "Cuello y hombros\nOficina y viaje"],
    ];

    ?>
    <style>
        .ma-lmg-row { display:flex; flex-wrap:wrap; gap:15px; margin-bottom:15px; }
        .ma-lmg-col { flex: 1; min-width:250px; }
        .ma-lmg-col label { display:block; font-weight:600; margin-bottom:5px; font-size:12px; }
        .ma-lmg-col input[type=text] { width:100%; }
        .ma-lmg-title { font-size:14px; border-bottom:1px solid #ddd; padding-bottom:5px; margin:20px 0 10px; font-weight:700; }
        .ma-lmg-color { display:flex; align-items:center; gap:8px; }
        .ma-lmg-color input[type=color] { height:30px; width:40px; padding:0; border:none; }
    </style>

    <p style="color:#666; font-size:13px;">Llena estos campos para crear una Landing Page única para este producto. Si dejas un campo vacío, usará el formato predeterminado del tema global. <strong>Genera fotos y textos mágicamente con el panel de la derecha (IA)</strong>.</p>
    
    <div class="ma-lmg-title">🎨 Paleta de Colores Exclusiva</div>
    <div class="ma-lmg-row">
        <?php foreach($colors as $key => [$label, $default]): $val = $get_val($key); ?>
        <div class="ma-lmg-col ma-lmg-color">
            <input type="color" id="ma_<?php echo $key; ?>_color" value="<?php echo esc_attr($val ?: $default); ?>" onchange="document.getElementById('ma_<?php echo $key; ?>').value = this.value">
            <div>
                <label><?php echo esc_html($label); ?></label>
                <input type="text" name="_ma_<?php echo $key; ?>" id="ma_<?php echo $key; ?>" value="<?php echo esc_attr($val); ?>" placeholder="<?php echo esc_attr($default); ?>" style="width:100px;">
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">📦 Textos Pricipales (Hero & Precios)</div>
    <div class="ma-lmg-row">
        <?php foreach(array_merge($hero, $prices) as $key => $data): 
             $label = is_array($data) ? $data[0] : $data;
             $hint  = is_array($data) ? $data[1] : '';
        ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?>
                <?php if($hint): ?><br><span style="font-size:11px; font-weight:normal; color:#059669;"><?php echo esc_html($hint); ?></span><?php endif; ?>
            </label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">🏷️ Títulos y Etiquetas de las Secciones (Opcional, sobrescribe globales)</div>
    <div class="ma-lmg-row">
        <?php foreach($extra_titles as $key => $label): ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">⚙️ Beneficios y Características</div>
    <div class="ma-lmg-row">
        <?php foreach($features as $key => $label): ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">🛒 Combos / Paquetes (Checkout)</div>
    <div class="ma-lmg-row">
        <?php foreach($combos_meta as $key => $label): ?>
        <div class="ma-lmg-col" style="min-width: 200px;">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">💬 Testimonios Dinámicos (Generables con IA)</div>
    <div class="ma-lmg-row">
        <?php foreach($reviews_meta as $key => $label): ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">🚚 Tarifas de Envío y Complementos Extras</div>
    <div class="ma-lmg-row">
        <?php foreach($shipping_meta as $key => $label): ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>
    <div class="ma-lmg-row">
        <?php foreach($addon_meta as $key => $label): ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">🖼️ Imágenes y Multimedia (Generables con IA)</div>
    <div class="ma-lmg-row">
        <?php foreach($assets as $key => $data): 
             $label = is_array($data) ? $data[0] : $data;
             $hint  = is_array($data) ? $data[1] : '';
        ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?>
                <?php if($hint): ?><br><span style="font-size:11px; font-weight:normal; color:#059669;"><?php echo esc_html($hint); ?></span><?php endif; ?>
            </label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ma-lmg-title">📝 Listas y Especificaciones Técnicas (Una por línea)</div>
    <div class="ma-lmg-row">
        <?php foreach($textareas as $key => $data): 
             $label = is_array($data) ? $data[0] : $data;
             $hint  = is_array($data) ? $data[1] : '';
        ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <textarea name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" rows="5" placeholder="<?php echo esc_attr($hint); ?>" style="width:100%;resize:vertical;"><?php echo esc_textarea($get_val($key)); ?></textarea>
        </div>
        <?php endforeach; ?>
    </div>
    <script>
        // Actualizar el color picker si el usuario escribe en el input texto
        document.querySelectorAll('.ma-lmg-color input[type="text"]').forEach(input => {
            input.addEventListener('input', (e) => {
                let colorPicker = document.getElementById(e.target.id + '_color');
                if (colorPicker && /^#[0-9A-F]{6}$/i.test(e.target.value)) {
                    colorPicker.value = e.target.value;
                }
            });
        });
    </script>
    <?php
}

add_action( 'save_post_product', 'ma_save_landing_metabox' );
function ma_save_landing_metabox( $post_id ) {
    if ( ! isset( $_POST['ma_landing_meta_nonce_field'] ) || ! wp_verify_nonce( $_POST['ma_landing_meta_nonce_field'], 'ma_landing_meta_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $all_fields = [
        'color_bg', 'color_surface', 'color_primary', 'color_secondary', 'color_text',
        'hero_kicker', 'hero_title', 'hero_subtitle', 'hero_btn_primary', 'hero_image_url',
        'hero_price_old', 'hero_price_new', 'hero_discount_label', 'ticker_text',
        'demo_title', 'features_title', 'ba_title', 'ba_intro', 'ba_label_antes', 'ba_label_despues',
        'oferta_title', 'oferta_flash', 'oferta_btn', 'det_title', 'det_espec', 'det_inbox', 'det_compat',
        'feat1_title', 'feat1_desc', 'feat2_title', 'feat2_desc', 'feat3_title', 'feat3_desc',
        'combo1_label', 'combo1_sublabel', 'combo1_qty', 'combo1_subtotal', 'combo1_price_display', 'combo1_hide',
        'combo2_label', 'combo2_sublabel', 'combo2_qty', 'combo2_subtotal', 'combo2_price_display', 'combo2_hide',
        'combo3_label', 'combo3_sublabel', 'combo3_qty', 'combo3_subtotal', 'combo3_price_display', 'combo3_hide',
        'rev1_name', 'rev1_city', 'rev1_text', 'rev2_name', 'rev2_city', 'rev2_text', 'rev3_name', 'rev3_city', 'rev3_text',
        'ship1_label', 'ship1_eta', 'ship1_price', 'ship2_label', 'ship2_eta', 'ship2_price', 'ship3_label', 'ship3_eta', 'ship3_price',
        'ship_free_city', 'ship_free_depts',
        'addon_label', 'addon_price',
        'demo_video_src','stats_img', 'cs1_img', 'demo_poster', 'demo_gallery_1', 'demo_gallery_2', 'demo_gallery_3', 
        'ba_img', 'ba_img_antes', 'ba_img_despues', 'ba_caption', 'feat_img', 'detalle_img', 'review_img', 'oferta_img', 'faq_img',
        'oferta_items', 'det_specs', 'det_inbox_items', 'det_compat_items'
    ];

    foreach ( $all_fields as $field ) {
        if ( isset( $_POST['_ma_' . $field] ) ) {
            // Especial para textareas
            if ( in_array($field, ['oferta_items', 'det_specs', 'det_inbox_items', 'det_compat_items']) ) {
                update_post_meta( $post_id, '_ma_' . $field, sanitize_textarea_field( $_POST['_ma_' . $field] ) );
            } else {
                update_post_meta( $post_id, '_ma_' . $field, sanitize_text_field( $_POST['_ma_' . $field] ) );
            }
        }
    }
}
