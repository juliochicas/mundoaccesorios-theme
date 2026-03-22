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
        'hero_image_url'      => 'URL Imagen Hero (1000x1000)',
    ];

    $prices = [
        'hero_price_old'      => 'Precio Anterior (Tachado)',
        'hero_price_new'      => 'Precio Actual',
        'hero_discount_label' => 'Etiqueta Descuento (Ej: Ahorra 38%)',
        'ticker_text'         => 'Texto del Ticker de Urgencia',
    ];

    $features = [
        'feat1_title' => 'Beneficio 1 - Título', 'feat1_desc' => 'Beneficio 1 - Text',
        'feat2_title' => 'Beneficio 2 - Título', 'feat2_desc' => 'Beneficio 2 - Text',
        'feat3_title' => 'Beneficio 3 - Título', 'feat3_desc' => 'Beneficio 3 - Text',
    ];

    $assets = [
        'stats_img'   => 'URL Imagen Stats (Horizontal)',
        'cs1_img'     => 'URL Imagen Cross-Sell / Lifestyle',
        'demo_poster' => 'URL Imagen Banner / Demo',
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
        <?php foreach(array_merge($hero, $prices) as $key => $label): ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
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

    <div class="ma-lmg-title">🖼️ Imágenes y Multimedia (Generables con IA)</div>
    <div class="ma-lmg-row">
        <?php foreach($assets as $key => $label): ?>
        <div class="ma-lmg-col">
            <label><?php echo esc_html($label); ?></label>
            <input type="text" name="_ma_<?php echo $key; ?>" id="_ma_<?php echo $key; ?>" class="ma-ai-target-<?php echo $key; ?>" value="<?php echo esc_attr($get_val($key)); ?>">
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

    $fields = [
        'color_bg', 'color_surface', 'color_primary', 'color_secondary', 'color_text',
        'hero_kicker', 'hero_title', 'hero_subtitle', 'hero_btn_primary', 'hero_image_url',
        'hero_price_old', 'hero_price_new', 'hero_discount_label', 'ticker_text',
        'feat1_title', 'feat1_desc', 'feat2_title', 'feat2_desc', 'feat3_title', 'feat3_desc',
        'stats_img', 'cs1_img', 'demo_poster'
    ];

    foreach ( $fields as $field ) {
        if ( isset( $_POST['_ma_' . $field] ) ) {
            update_post_meta( $post_id, '_ma_' . $field, sanitize_text_field( $_POST['_ma_' . $field] ) );
        }
    }
}
