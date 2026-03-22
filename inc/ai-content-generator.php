<?php
/**
 * inc/ai-content-generator.php — Generador de Contenido con Gemini AI
 *
 * Integrado en Apariencia > ⚙ Mundo Accesorios como panel desplegable.
 *
 * 3 modos:
 *  1. Quick Generate   — nombre + descripción → todos los campos AIDA
 *  2. Research First   — foto del producto → Gemini Vision → copy + imágenes
 *  3. Por sección      — botón ✨ junto a cada campo del settings page
 *
 * Endpoints FastAPI (https://fastapi.modularis.pro):
 *  POST /api/gemini/generate-text       — gemini-2.0-flash (texto)
 *  POST /api/gemini/generate-image      — imagen-3.0 (imágenes)
 *  POST /api/gemini/marketing-content   — texto + imágenes combinado
 *
 * Dimensiones de imagen:
 *  Producto  (WC):  1000 × 1000 px, JPG/WebP, fondo blanco
 *  Lifestyle/Feed:  1000 × 1000 px cuadrada
 *  Stories/Reels:   1080 × 1920 px vertical
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── 1. Añadir sección AI al wp-admin menú y Personalizador ──────────────────── */

// Registrar página de menú bajo WMS/Theme
add_action('admin_menu', function() {
    add_submenu_page(
        'ma-wms',
        'Generador IA ✨',
        'Generador IA ✨',
        'manage_options',
        'ma-ai-generator',
        function() {
            echo '<div class="wrap"><h1 style="display:none;">Generador IA</h1>';
            ma_ai_render_panel();
            echo '</div>';
        });
});

// Hook para inyectar el panel nativamente en el Personalizador (Customizer) vía PHP
add_action( 'customize_register', function( $wp_customize ) {
    if ( ! current_user_can('manage_options') ) return;

    class MA_AI_Copilot_Control extends WP_Customize_Control {
        public $type = 'ma_ai_copilot';
        public function render_content() {
            ?>
            <style>
            /* Ajustes responsivos para el panel IA dentro de la sección nativa */
            #ma-ai-generator-panel { margin: 0; padding: 0; box-sizing: border-box; width: 100%; display: block !important; }
            #ma-ai-generator-panel .ma-ai-tab { padding: 6px 12px; font-size: 12px; flex: 1 1 auto; text-align: center; }
            #ma-ai-generator-panel .ai-resp-grid { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
            </style>
            <?php
            ma_ai_render_panel();
        }
    }

    $wp_customize->add_section( 'ma_ai_copilot_section', [
        'title'    => '✨ Asistente Copilot IA',
        'priority' => 1, // Hasta arriba
    ]);

    $wp_customize->add_setting( 'ma_ai_dummy_setting', [ 'default' => '' ] );

    $wp_customize->add_control( new MA_AI_Copilot_Control( $wp_customize, 'ma_ai_dummy_setting', [
        'section'  => 'ma_ai_copilot_section',
        'settings' => 'ma_ai_dummy_setting',
    ]) );
} );

// Inyectar script para página independiente admin
add_action( 'admin_footer', function() {
    $screen = get_current_screen();
    if ( ! $screen || strpos($screen->id, 'ma-ai-generator') === false ) return;
    ?>
    <style>
    /* Estilos para el panel IA en pantalla completa admin */
    #ma-ai-generator-panel .ai-resp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var panel = document.getElementById('ma-ai-generator-panel');
        if (panel) {
            panel.style.display = 'block';
            panel.style.margin = '20px auto';
            panel.style.maxWidth = '1000px';
        }
        var toggle = document.getElementById('ma-ai-toggle');
        if (toggle) toggle.click(); // Expandir
    });
    </script>
    <?php
} );

/* ── 2. Render del panel ─────────────────────────────────────── */
function ma_ai_render_panel() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $fastapi_url = rtrim( ma_get( 'caex_fastapi_url', 'https://fastapi.modularis.pro' ), '/' );
    ?>
    <div id="ma-ai-generator-panel" style="display:none;margin:16px auto;max-width:800px;width:100%;box-sizing:border-box">
    <div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.05),0 2px 4px -1px rgba(0,0,0,0.03);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#111827">

        <!-- Header -->
        <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f3f4f6;padding-bottom:16px;margin-bottom:16px;flex-wrap:wrap;gap:12px;position:relative;z-index:1;">
            <div>
                <h2 style="margin:0;font-size:16px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    Asistente Copilot IA
                </h2>
                <p style="margin:4px 0 0;font-size:12px;color:#64748b;font-weight:500">Potenciado por Gemini 2.5 Flash e Imagen 4.0</p>
            </div>
            <button type="button" id="ma-ai-toggle"
                    style="background:#f8fafc;border:1px solid #e2e8f0;color:#334155;padding:6px 10px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;transition:0.2s">
                ▼ Contraer
            </button>
        </div>

        <!-- Contenido colapsable -->
        <div id="ma-ai-content" style="display:none">

            <!-- Tabs de modo -->
            <div style="display:flex;flex-wrap:wrap;background:#f1f5f9;padding:6px;border-radius:10px;margin-bottom:20px;gap:6px">
                <button type="button" class="ma-ai-tab ma-ai-tab-active" data-tab="quick"
                        style="background:#ffffff;color:#0f172a;border:none;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.1);transition:0.2s">
                    ⚡ Redacción Rápida
                </button>
                <button type="button" class="ma-ai-tab" data-tab="research"
                        style="background:transparent;color:#64748b;border:none;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:0.2s">
                    🔍 Analizar
                </button>
                <button type="button" class="ma-ai-tab" data-tab="images"
                        style="background:transparent;color:#64748b;border:none;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:0.2s">
                    🖼️ Imágenes
                </button>
                <button type="button" class="ma-ai-tab" data-tab="videos"
                        style="background:transparent;color:#64748b;border:none;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:0.2s">
                    🎥 Videos
                </button>
            </div>

            <!-- TAB: Quick Generate ──────────────────────────── -->
            <div id="ma-ai-tab-quick" class="ma-ai-tab-pane">
                <div class="ai-resp-grid">
                    <div style="flex:1">
                        <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px">Nombre del producto *</label>
                        <input type="text" id="ai_product_name" placeholder="Ej: Funda iPhone 15"
                               style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;background:#f8fafc;color:#0f172a;font-size:14px;box-sizing:border-box;transition:0.2s" onfocus="this.style.background='#fff';this.style.borderColor='#7c3aed'" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'" />
                    </div>
                    <div style="flex:1">
                        <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px">Precio (Q)</label>
                        <input type="text" id="ai_product_price" placeholder="Ej: 149"
                               style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;background:#f8fafc;color:#0f172a;font-size:14px;box-sizing:border-box;transition:0.2s" onfocus="this.style.background='#fff';this.style.borderColor='#7c3aed'" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'" />
                    </div>
                </div>
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px">Contexto o enfoque (opcional)</label>
                    <textarea id="ai_product_desc" rows="2" placeholder="Describe a quién va dirigido o enfoque persuasivo..."
                              style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;background:#f8fafc;color:#0f172a;font-size:14px;box-sizing:border-box;resize:vertical;transition:0.2s" onfocus="this.style.background='#fff';this.style.borderColor='#7c3aed'" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"></textarea>
                </div>
                <div style="margin-bottom:20px">
                    <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:8px">Bloques a generar</label>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;background:#f8fafc;padding:12px;border-radius:8px;border:1px solid #e2e8f0">
                        <?php
                        $sections = ['hero' => '🦸 Hero', 'ticker' => '📣 Ticker', 'features'=> '✨ Features', 'stats' => '📊 Stats', 'cta' => '🏁 Cierre', 'seo' => '🔍 Meta', 'oferta' => '🎯 Promos'];
                        foreach ( $sections as $key => $label ) :
                        ?>
                        <label style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#475569;cursor:pointer">
                            <input type="checkbox" class="ai-section-cb" value="<?= esc_attr( $key ) ?>" checked style="accent-color:#7c3aed;width:16px;height:16px" />
                            <?= esc_html( $label ) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="button" id="ma-ai-quick-generate"
                        style="background:linear-gradient(135deg, #0f172a, #334155);color:#ffffff;padding:12px 16px;border-radius:10px;border:none;font-size:15px;font-weight:700;letter-spacing:0.5px;width:100%;cursor:pointer;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);transition:transform 0.2s">
                    Escribir Landing Page ✨
                </button>
            </div>

            <!-- TAB: Research First ─────────────────────────── -->
            <div id="ma-ai-tab-research" class="ma-ai-tab-pane" style="display:none">
                <p style="font-size:13px;color:#4b5563;margin:0 0 16px">Sube la foto del producto para que Gemini Vision extraiga los beneficios clave y detecte tu público.</p>

                <!-- Paso 1 -->
                <div id="ma-research-step1">
                    <div id="ma-ai-dropzone" style="border:1.5px dashed #d1d5db;border-radius:8px;padding:32px 24px;text-align:center;cursor:pointer;background:#f9fafb">
                        <svg style="margin:0 auto 12px" width="28" height="28" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div style="font-size:14px;color:#374151;font-weight:500">Haz clic o arrastra la foto del producto</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:4px">JPG, PNG o WebP, max 5MB</div>
                        <input type="file" id="ai_product_photo" accept="image/*" style="display:none" />
                    </div>
                    <img id="ma-ai-preview-img" src="" style="display:none;width:100%;border-radius:8px;margin-top:16px;max-height:200px;object-fit:contain;background:#f3f4f6" />
                    <button type="button" id="ma-ai-analyze" style="display:none;background:#2563eb;color:#fff;padding:10px 16px;border-radius:8px;border:none;font-size:14px;font-weight:500;width:100%;margin-top:12px;cursor:pointer">
                        Analizar producto visualmente
                    </button>
                </div>

                <!-- Paso 2 -->
                <div id="ma-research-step2" style="display:none;margin-top:16px;border-top:1px solid #e2e8f0;padding-top:16px">
                    <label style="display:block;font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px">Contexto inferido (Puedes ajustarlo)</label>
                    <div class="ai-resp-grid">
                        <div style="flex:1">
                            <div style="font-size:12px;font-weight:600;color:#475569;margin-bottom:4px">Nombre Comercial</div>
                            <input id="ai_detected_name" type="text" style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;background:#f8fafc;font-size:14px;box-sizing:border-box;transition:0.2s" onfocus="this.style.background='#fff';this.style.borderColor='#7c3aed'" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'" />
                        </div>
                        <div style="flex:1">
                            <div style="font-size:12px;font-weight:600;color:#475569;margin-bottom:4px">Arquetipo / Público</div>
                            <input id="ai_detected_audience" type="text" style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;background:#f8fafc;font-size:14px;box-sizing:border-box;transition:0.2s" onfocus="this.style.background='#fff';this.style.borderColor='#7c3aed'" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'" />
                        </div>
                    </div>
                    <div>
                        <div style="font-size:12px;font-weight:600;color:#475569;margin-bottom:4px">Atributos y dolores detectados</div>
                        <textarea id="ai_detected_features" rows="3" style="width:100%;padding:10px 12px;border:1.5px solid #cbd5e1;border-radius:8px;background:#f8fafc;font-size:14px;box-sizing:border-box;resize:vertical;transition:0.2s" onfocus="this.style.background='#fff';this.style.borderColor='#7c3aed'" onblur="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"></textarea>
                    </div>
                    <button type="button" id="ma-ai-research-generate" style="background:linear-gradient(135deg, #0f172a, #334155);color:#ffffff;padding:12px 16px;border-radius:10px;border:none;font-size:15px;font-weight:700;letter-spacing:0.5px;width:100%;margin-top:16px;cursor:pointer;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);transition:transform 0.2s" onmouseover="this.style.transform='scale(0.98)'" onmouseout="this.style.transform='scale(1)'">
                        Basar mi Landing Page en esto ✨
                    </button>
                </div>
            </div>

            <!-- TAB: Generar Imágenes ────────────────────────── -->
            <div id="ma-ai-tab-images" class="ma-ai-tab-pane" style="display:none">
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px">Prompt visual o descripción fotográfica</label>
                    <textarea id="ai_img_desc" rows="2" placeholder="Primer plano de un masajeador elegante blanco sobre mesa de mármol negra con luz cinematográfica..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box"></textarea>
                </div>
                <div style="margin-bottom:20px">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px">Formato y uso previsto</label>
                    <div style="display:grid;grid-template-columns:1fr;gap:10px">
                        <?php
                        $img_types = [
                            'producto'   => [ '📦 Hero (Fondo Neutro)', '1:1 · Limpio y centrado', '1000x1000' ],
                            'lifestyle'  => [ '📸 Ambientes (Lifestyle)', '1:1 · Situación real cuadrada', '1000x1000' ],
                            'horizontal' => [ '🖥️ Banner Ancho (Desktop)', '16:9 · Ideal para cabeceras y cruces', '1920x1080' ],
                            'vertical'   => [ '📱 Vertical (Stories)', '9:16 · Formato de pauta móvil', '1080x1920' ],
                        ];
                        foreach ( $img_types as $key => [$label, $desc, $size] ) :
                        ?>
                        <label style="display:flex;align-items:center;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;cursor:pointer">
                            <input type="radio" name="ai_img_type" value="<?= esc_attr( $key ) ?>" <?= $key === 'producto' ? 'checked' : '' ?> data-size="<?= esc_attr( $size ) ?>" style="accent-color:#000;width:16px;height:16px;margin:0 12px 0 0" />
                            <div>
                                <div style="font-size:13px;font-weight:600;color:#111827"><?= esc_html( $label ) ?></div>
                                <div style="font-size:12px;color:#6b7280;margin-top:2px"><?= esc_html( $desc ) ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="button" id="ma-ai-gen-image" style="background:#0284c7;color:#fff;padding:10px 16px;border-radius:8px;border:none;font-size:14px;font-weight:500;width:100%;cursor:pointer">
                    🖼️ Pintar con Imagen 4.0
                </button>
                <div style="text-align:center;margin:12px 0;font-size:12px;color:#9ca3af;font-weight:600">O AUTOMATIZA TODA LA PÁGINA</div>
                <button type="button" id="ma-ai-gen-mediakit" style="background:linear-gradient(135deg, #10b981, #059669);color:#fff;padding:12px 16px;border-radius:8px;border:none;font-size:14px;font-weight:700;width:100%;cursor:pointer;box-shadow:0 4px 6px -1px rgba(16,185,129,0.3);transition:0.2s">
                    🪄 Generar Auto Media Kit (Hero, Banners y Lifestyle)
                </button>
                <div id="ma-ai-images-gallery" style="display:none;margin-top:20px">
                    <div style="font-size:13px;font-weight:500;color:#374151;margin-bottom:12px">Borradores generados:</div>
                    <div id="ma-ai-images-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px"></div>
                </div>
            </div>

            <!-- TAB: Video Veo ────────────────────────────── -->
            <div id="ma-ai-tab-videos" class="ma-ai-tab-pane" style="display:none">
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px">Descripción del escenario o animación para el video</label>
                    <textarea id="ai_vid_desc" rows="2" placeholder="Ej: Toma cinemática de dron girando alrededor del producto sobre una mesa de madera con iluminación LED..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;box-sizing:border-box"></textarea>
                </div>
                <button type="button" id="ma-ai-gen-video" style="background:linear-gradient(135deg, #ec4899, #be185d);color:#fff;padding:12px 16px;border-radius:8px;border:none;font-size:14px;font-weight:600;width:100%;cursor:pointer;box-shadow:0 4px 6px -1px rgba(236,72,153,0.3);transition:0.2s">
                    🎥 Iniciar Renderizado de Video (2-4 min)
                </button>
            </div>

            <!-- Feedback ─────────────────── -->
            <div id="ma-ai-progress" style="display:none;margin-top:16px;padding:16px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb">
                <div style="background:#e5e7eb;border-radius:20px;height:6px;overflow:hidden">
                    <div id="ma-ai-progress-bar" style="background:#000000;height:100%;border-radius:20px;width:0%;transition:width .4s"></div>
                </div>
                <div id="ma-ai-progress-label" style="font-size:12.5px;color:#4b5563;margin-top:10px;text-align:center;font-weight:500">Iniciando motor...</div>
            </div>

            <div id="ma-ai-result" style="display:none;margin-top:16px;border:1px solid #10b981;border-radius:8px;background:#ecfdf5;padding:16px">
                <div style="font-size:13px;font-weight:600;color:#047857;margin-bottom:8px;display:flex;align-items:center;gap:6px">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg> Contenido redactado
                </div>
                <div id="ma-ai-result-text" style="font-size:12.5px;color:#065f46;white-space:pre-wrap;line-height:1.5;max-height:180px;overflow-y:auto;padding:8px;background:rgba(255,255,255,.5);border-radius:6px;border:1px solid #34d399"></div>
                <button type="button" id="ma-ai-apply" style="background:#10b981;color:#fff;padding:8px 16px;border-radius:6px;border:none;font-size:13px;font-weight:600;cursor:pointer;margin-top:12px;width:100%;box-shadow:0 1px 2px rgba(16, 185, 129, 0.2)">
                    Inyectar directamente en esta página
                </button>
            </div>

            <div id="ma-ai-error" style="display:none;margin-top:16px;background:#fef2f2;border:1px solid #ef4444;border-radius:8px;padding:14px;color:#b91c1c;font-size:13px;font-weight:500"></div>

        </div>
    </div>
    </div>

    <script>
    (function($){
        var AJAX_URL  = '<?= admin_url( "admin-ajax.php" ) ?>';
        var NONCE     = '<?= wp_create_nonce( "ma_ai_action" ) ?>';
        var generated = {}; // almacena el último JSON generado

        // ── Toggle panel ───────────────────────────────────────
        $('#ma-ai-toggle').on('click', function() {
            var $c = $('#ma-ai-content');
            $c.toggle();
            $(this).text( $c.is(':visible') ? '▲ Contraer' : '▼ Expandir' );
        });

        // ── Tabs ───────────────────────────────────────────────
        $('.ma-ai-tab').on('click', function() {
            var tab = $(this).data('tab');
            $('.ma-ai-tab').css({ background: 'transparent', color: '#6b7280', boxShadow:'none' });
            $(this).css({ background: '#ffffff', color: '#111827', boxShadow:'0 1px 3px rgba(0,0,0,0.1)' });
            $('.ma-ai-tab-pane').hide();
            $('#ma-ai-tab-' + tab).show();
        });

        // ── Upload zone drag & drop ─────────────────────────────
        var photo64 = null;
        $('#ma-ai-dropzone').on('click', function(e) { 
            if (e.target.id === 'ai_product_photo') return; // Evitar loop infinito
            $('#ai_product_photo').trigger('click'); 
        })
            .on('dragover', function(e) { e.preventDefault(); $(this).css({borderColor:'#3b82f6', background:'#eff6ff'}); })
            .on('dragleave', function() { $(this).css({borderColor:'#d1d5db', background:'#f9fafb'}); })
            .on('drop', function(e) {
                e.preventDefault();
                $(this).css({borderColor:'#d1d5db', background:'#f9fafb'});
                handleFile( e.originalEvent.dataTransfer.files[0] );
            });
        $('#ai_product_photo').on('change', function() { handleFile( this.files[0] ); });

        function handleFile(file) {
            if (!file || !file.type.match('image.*')) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                photo64 = e.target.result.split(',')[1]; // base64 sin el prefijo
                $('#ma-ai-preview-img').attr('src', e.target.result).show();
                $('#ma-ai-analyze').show();
                $('#ma-ai-dropzone').css('border-color','#a78bfa');
            };
            reader.readAsDataURL(file);
        }

        // ── Progress helper ─────────────────────────────────────
        function progress(pct, label) {
            $('#ma-ai-progress').show();
            $('#ma-ai-progress-bar').css('width', pct + '%');
            $('#ma-ai-progress-label').text(label);
        }
        function resetUI() {
            $('#ma-ai-progress').hide();
            $('#ma-ai-result').hide();
            $('#ma-ai-error').hide();
        }

        // ── Quick Generate ─────────────────────────────────────
        $('#ma-ai-quick-generate').on('click', function() {
            var name = $('#ai_product_name').val().trim();
            if (!name) { alert('Ingresa el nombre del producto'); return; }
            resetUI();
            var sections = [];
            $('.ai-section-cb:checked').each(function() { sections.push($(this).val()); });
            progress(20, 'Conectando con Gemini...');
            $.ajax({
                url: AJAX_URL, method: 'POST',
                data: {
                    action: 'ma_ai_generate_all', _nonce: NONCE,
                    product_name: name,
                    product_price: $('#ai_product_price').val(),
                    product_desc: $('#ai_product_desc').val(),
                    sections: sections.join(',')
                },
                success: function(res) {
                    progress(100, 'Listo ✅');
                    if (res.success) { showResult(res.data); }
                    else { showError(res.data || 'Error al generar'); }
                },
                error: function() { showError('Error de conexión con el servidor'); }
            });
        });

        // ── Research First: Analizar foto ──────────────────────
        $('#ma-ai-analyze').on('click', function() {
            if (!photo64) return;
            resetUI();
            progress(30, 'Gemini Vision analizando imagen...');
            $.ajax({
                url: AJAX_URL, method: 'POST',
                data: { action: 'ma_ai_analyze_photo', _nonce: NONCE, photo_base64: photo64 },
                success: function(res) {
                    progress(100, 'Análisis completo ✅');
                    if (res.success && res.data) {
                        var d = res.data;
                        $('#ai_detected_name').val(d.nombre || d.product_name || '');
                        $('#ai_detected_audience').val(d.publico || d.audience || '');
                        $('#ai_detected_features').val(d.caracteristicas || d.features || '');
                        if (d.prompt_visual) {
                            var aud = d.publico || 'buyers';
                            $('#ai_img_desc').val(d.prompt_visual + '. Context: visually appealing to ' + aud + ' solving their pain points. High quality commercial photography, 8k, photorealistic.');
                        }
                        $('#ma-research-step2').show();
                        $('#ma-ai-progress').hide();
                    } else { showError(res.data || 'Error al analizar imagen'); }
                },
                error: function() { showError('Error de conexión'); }
            });
        });

        // ── Research First: Generar con contexto ───────────────
        $('#ma-ai-research-generate').on('click', function() {
            resetUI();
            progress(20, 'Generando copy completo...');
            $.ajax({
                url: AJAX_URL, method: 'POST',
                data: {
                    action: 'ma_ai_generate_all', _nonce: NONCE,
                    product_name: $('#ai_detected_name').val(),
                    product_desc: $('#ai_detected_features').val(),
                    audience: $('#ai_detected_audience').val(),
                    sections: 'hero,ticker,features,stats,cta,seo,oferta'
                },
                success: function(res) {
                    progress(100, 'Listo ✅');
                    if (res.success) { showResult(res.data); }
                    else { showError(res.data || 'Error'); }
                },
                error: function() { showError('Error de conexión'); }
            });
        });

        // ── Generar imagen ─────────────────────────────────────
        $('#ma-ai-gen-image').on('click', function() {
            var desc  = $('#ai_img_desc').val().trim();
            var type  = $('input[name="ai_img_type"]:checked').val();
            var size  = $('input[name="ai_img_type"]:checked').data('size');
            if (!desc) { alert('Describe el visual que quieres generar'); return; }
            resetUI();
            var $btn = $(this).text('Generando imagen...').prop('disabled', true);
            progress(40, 'Imagen 4.0 generando ' + size + '...');
            $.ajax({
                url: AJAX_URL, method: 'POST',
                data: { action: 'ma_ai_generate_image', _nonce: NONCE, description: desc, image_type: type, size: size, photo_base64: photo64 },
                success: function(res) {
                    progress(100, 'Imagen generada ✅');
                    if (res.success && res.data) {
                        var data = res.data;
                        var $grid = $('#ma-ai-images-grid').empty();
                        var images = data.images || (data.url ? [data] : []);
                        images.forEach(function(img) {
                            var url  = img.url || img;
                            var $wrap = $('<div>').css({ position:'relative', borderRadius:'8px', overflow:'hidden' });
                            var $img  = $('<img>').attr('src', url).css({ width:'100%', display:'block', cursor:'pointer', borderRadius:'8px' });
                            var $btn2 = $('<button>').text('Usar').css({
                                position:'absolute', bottom:'4px', left:'50%', transform:'translateX(-50%)',
                                background:'#7c3aed', color:'#fff', border:'none', borderRadius:'6px',
                                padding:'4px 10px', fontSize:'11px', fontWeight:'700', cursor:'pointer', whiteSpace:'nowrap'
                            }).on('click', function() {
                                navigator.clipboard.writeText(url).then(function() {
                                    alert('URL copiada. Pégala en el campo "Imagen Hero (URL)" del formulario.');
                                });
                            });
                            $wrap.append($img, $btn2);
                            $grid.append($wrap);
                        });
                        $('#ma-ai-images-gallery').show();
                        $('#ma-ai-progress').hide();
                    } else { showError(res.data || 'Error al generar imagen'); }
                    $btn.text('🖼️ Pintar con Imagen 4.0').prop('disabled', false);
                },
                error: function() {
                    showError('Error de conexión con FastAPI');
                    $btn.text('🖼️ Pintar con Imagen 4.0').prop('disabled', false);
                }
            });
        });

        // ── Auto Media Kit (Generación en Lote) ───────────────────
        $('#ma-ai-gen-mediakit').on('click', async function() {
            if (!photo64) { 
                alert('⚠️ Para extraer tu producto sin inventarlo, primero debes subir su foto real en la pestaña "🔍 Analizar".'); 
                return; 
            }
            var basePrompt = $('#ai_img_desc').val() || $('#ai_detected_features').val() || 'High quality commercial product shot';
            
            var kit = [
                { field: 'hero_image_url', type: 'producto', size: '1000x1000', label: 'Imagen Hero', suffix: 'Fondo blanco limpio iluminado estilo estudio Apple.' },
                { field: 'stats_img', type: 'horizontal', size: '1920x1080', label: 'Fondo Stats', suffix: 'Fotografía panorámica cinemática, entorno lifestyle elegante difuminado atrás.' },
                { field: 'cs1_img', type: 'lifestyle', size: '1000x1000', label: 'Cross-Sell 1 (Lifestyle)', suffix: 'Escena real en uso, composición lifestyle natural.' },
                { field: 'demo_poster', type: 'horizontal', size: '1920x1080', label: 'Banner Demo / Video', suffix: 'Visual impactante comercial, fondo atractivo oscuro.' }
            ];
            
            var $btn = $(this).prop('disabled', true);
            resetUI();
            $('#ma-ai-images-gallery').show();
            var $grid = $('#ma-ai-images-grid').empty();
            
            for (let i = 0; i < kit.length; i++) {
                var item = kit[i];
                $btn.text(`Generando ${item.label} (${i+1}/${kit.length})...`);
                progress((i+1)*25, `Imagen 4.0 pintando: ${item.label}...`);
                
                try {
                    var res = await $.ajax({
                        url: AJAX_URL, method: 'POST',
                        data: { 
                            action: 'ma_ai_generate_image', 
                            _nonce: NONCE, 
                            description: basePrompt + '. ' + item.suffix, 
                            image_type: item.type, 
                            size: item.size, 
                            photo_base64: photo64 
                        }
                    });
                    
                    if (res.success && res.data) {
                        var imgData = res.data.images ? res.data.images[0] : res.data;
                        var url = imgData.url || imgData;
                        
                        var $wrap = $('<div>').css({ position:'relative', borderRadius:'8px', overflow:'hidden', border:'2px solid #10b981' });
                        var $header = $('<div>').text(item.label).css({ background:'#10b981', color:'#fff', padding:'2px 6px', fontSize:'10px', fontWeight:'700', textAlign:'center' });
                        var $img  = $('<img>').attr('src', url).css({ width:'100%', display:'block', cursor:'pointer' });
                        
                        // Botón de asignación automática Customizer
                        let $btn2 = $('<button>').text('Asignar').css({
                            position:'absolute', bottom:'8px', left:'50%', transform:'translateX(-50%)',
                            background:'#0f172a', color:'#fff', border:'none', borderRadius:'6px',
                            padding:'6px 14px', fontSize:'12px', fontWeight:'700', cursor:'pointer', whiteSpace:'nowrap', boxShadow:'0 2px 4px rgba(0,0,0,0.3)'
                        });
                        
                        // Capturando el field en closure
                        (function(fieldTarget, buttonEl){
                            buttonEl.on('click', function() {
                                if (typeof wp !== 'undefined' && wp.customize && wp.customize('ma_settings['+fieldTarget+']')) {
                                    wp.customize('ma_settings['+fieldTarget+']').set(url);
                                    $(this).text('¡Aplicado! ✅').css('background', '#10b981');
                                } else {
                                    navigator.clipboard.writeText(url).then(function() {
                                        alert('Asignación automática solo funciona dentro del Personalizador. Se ha copiado la URL.');
                                    });
                                }
                            });
                        })(item.field, $btn2);
                        
                        $wrap.append($header, $img, $btn2);
                        $grid.append($wrap);
                        
                        // Guardar en el dict global por si usan el botón masivo final
                        generated[item.field] = url; 
                    }
                } catch(e) {
                    console.error('Error generando ' + item.label, e);
                }
            }
            
            $btn.html('🪄 Generar Auto Media Kit (Hero, Banners y Lifestyle)').prop('disabled', false);
            progress(100, 'Media Kit Generado Exitosamente 🎉');
            $('#ma-ai-result-text').append('\n[Imágenes del Media Kit listas para Aplicar]');
            $('#ma-ai-result').show();
        });

        // ── Mostrar resultado y botón Aplicar ──────────────────
        function showResult(data) {
            generated = (typeof data === 'object') ? data : {};
            var texto = typeof data === 'string' ? data
                : Object.entries(data).map(function(e) { return e[0] + ': ' + e[1]; }).join('\n');
            $('#ma-ai-result-text').text(texto);
            $('#ma-ai-result').show();
            $('#ma-ai-progress').hide();
        }
        function showError(msg) {
            $('#ma-ai-error').html('❌ ' + msg).show();
            $('#ma-ai-progress').hide();
        }

        // ── Aplicar textos a los campos ────────────────────────
        $('#ma-ai-apply').on('click', function() {
            var map = {
                hero_title:          'input[name="hero_title"]',
                hero_kicker:         'input[name="hero_kicker"]',
                hero_subtitle:       'textarea[name="hero_subtitle"]',
                hero_btn_primary:    'input[name="hero_btn_primary"]',
                hero_price_old:      'input[name="hero_price_old"]',
                hero_price_new:      'input[name="hero_price_new"]',
                hero_discount_label: 'input[name="hero_discount_label"]',
                ticker_text:         'input[name="ticker_text"]',
                features_title:      'input[name="features_title"]',
                feat1_title:         'input[name="feat1_title"]',
                feat1_desc:          'input[name="feat1_desc"]',
                feat2_title:         'input[name="feat2_title"]',
                feat2_desc:          'input[name="feat2_desc"]',
                feat3_title:         'input[name="feat3_title"]',
                feat3_desc:          'input[name="feat3_desc"]',
                cta_title:           'input[name="cta_title"]',
                cta_desc:            'input[name="cta_desc"]',
                cta_btn:             'input[name="cta_btn"]',
                oferta_title:        'input[name="oferta_title"]',
                oferta_flash:        'input[name="oferta_flash"]',
                seo_title:           'input[name="seo_title"]',
                seo_description:     'textarea[name="seo_description"]',
            };
            var applied = 0;
            Object.keys(map).forEach(function(key) {
                if (generated[key]) {
                    if (typeof wp !== 'undefined' && wp.customize) {
                        // Customizer mode
                        var settingName = 'ma_settings[' + key + ']';
                        if (wp.customize(settingName)) {
                            wp.customize(settingName).set(generated[key]);
                            applied++;
                        }
                    } else {
                        // Admin page mode
                        var $field = $(map[key]);
                        if ($field.length) { $field.val(generated[key]).trigger('change'); applied++; }
                    }
                }
            });
            if (applied > 0) {
                $('#ma-ai-result').css('border', '2px solid #86efac');
                if (typeof wp !== 'undefined' && wp.customize) {
                    $('#ma-ai-apply').text('✅ ' + applied + ' campos aplicados — Haz clic en Publicar arriba.');
                } else {
                    $('#ma-ai-apply').text('✅ ' + applied + ' campos aplicados — Guarda con el botón de abajo');
                    $('html,body').animate({ scrollTop: $('[name="hero_title"]').offset().top - 100 }, 600);
                }
            } else {
                alert('Los textos generados no pudieron mapearse a los campos. Verifica que estés dentro del Personalizador / Customizer.');
            }
        });

        // ── Lógica de Polling Asíncrono para Video (Google Veo) ──
        var videoPollInterval;
        $('#ma-ai-gen-video').on('click', function() {
            var desc = $('#ai_vid_desc').val().trim();
            var photoB64 = $('#ai_product_photo').prop('files')[0] ? $('#ma-ai-preview-img').attr('src') : '';
            if ( ! desc ) return alert('Describe el entorno del video.');
            if ( ! photoB64 ) return alert('Por favor, sube la foto del producto en el Paso 1 (Analizar) para inyectarlo en el video.');
            
            var $btn = $(this);
            $btn.prop('disabled', true).text('⏳ Iniciando motor Veo 3.1...');
            $('#ma-ai-result').hide();
            progress(5, 'Conectando con Google Cloud Veo...');

            $.post(AJAX_URL, {
                action: 'ma_ai_generate_video_start', _nonce: NONCE,
                description: desc, photo_base64: photoB64
            }).done(function(res) {
                if ( ! res.success ) {
                    $btn.prop('disabled', false).text('🎥 Iniciar Renderizado de Video (2-4 min)');
                    return showError( res.data );
                }
                var opId = res.data.operation_id;
                progress(10, 'Operación Asignada. Renderizando en los clusters de Google...');
                $btn.text('⏳ Render al 10%... (No cierres la pestaña)');

                // Iniciar Polling cada 12 segundos
                videoPollInterval = setInterval(function() {
                    $.post(AJAX_URL, {
                        action: 'ma_ai_generate_video_check', _nonce: NONCE, operation_id: opId
                    }).done(function(pollRes) {
                        if ( pollRes.data && pollRes.data.status === 'RUNNING' ) {
                            // Simulacion visual de progreso
                            var $pb = $('#ma-ai-progress-bar');
                            var cP = parseInt($pb[0].style.width) || 10;
                            var nextP = Math.min(cP + 4, 98);
                            progress(nextP, 'Renderizando cuadro por cuadro... ' + nextP + '%');
                            $btn.text('⏳ Render al ' + nextP + '%... (Puede tomar minutos)');
                        } else if ( pollRes.success && pollRes.data.url ) {
                            clearInterval(videoPollInterval);
                            $btn.prop('disabled', false).text('🎥 Iniciar Renderizado de Video (2-4 min)');
                            progress(100, 'Video Renderizado con Éxito 🎬');
                            
                            var videoUrl = pollRes.data.url;
                            $('#ma-ai-result-text').html(
                                '<div style="margin-top:10px"><video width="100%" controls autoplay loop muted style="border-radius:8px;border:1px solid #10b981"><source src="'+videoUrl+'" type="video/mp4"></video></div>' +
                                '<div style="margin-top:10px"><button type="button" id="ma-ai-apply-video" style="background:#10b981;color:#fff;padding:8px 16px;border-radius:6px;border:none;font-size:13px;font-weight:600;cursor:pointer;width:100%;box-shadow:0 2px 4px rgba(16,185,129,0.2)">Inyectar Video en la Página</button></div>'
                            );
                            $('#ma-ai-result').show();
                            
                            // Re-bind click event for dynamic button
                            $(document).off('click', '#ma-ai-apply-video').on('click', '#ma-ai-apply-video', function() {
                                if (typeof wp !== 'undefined' && wp.customize && wp.customize('ma_settings[demo_video_src]')) {
                                    wp.customize('ma_settings[demo_video_src]').set(videoUrl);
                                    $(this).text('¡Aplicado! ✅').css('background', '#059669');
                                    alert('¡Video de demostración asignado!');
                                } else {
                                    navigator.clipboard.writeText(videoUrl).then(function() { alert('URL de video copiada al portapapeles.'); });
                                }
                            });
                        } else if ( !pollRes.success ) {
                            clearInterval(videoPollInterval);
                            $btn.prop('disabled', false).text('🎥 Iniciar Renderizado de Video (2-4 min)');
                            showError(pollRes.data || 'Error desconocido verificando LRO');
                        }
                    }).fail(function(){ 
                        // Fail silencioso en polling, se reintenta
                    });
                }, 12000);
            }).fail(function() {
                $btn.prop('disabled', false).text('🎥 Iniciar Renderizado de Video (2-4 min)');
                showError('Error de red inicializando Veo.');
            });
        });

    })(jQuery);
    </script>
    <?php
}

/* ── 3. AJAX Handlers ────────────────────────────────────────── */
function ma_ai_check_nonce() {
    if ( ! check_ajax_referer( 'ma_ai_action', '_nonce', false ) ) {
        wp_send_json_error( 'Nonce inválido', 403 );
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Sin permisos', 403 );
    }
}

function ma_ai_fastapi_url(): string {
    return rtrim( ma_get( 'caex_fastapi_url', 'https://fastapi.modularis.pro' ), '/' );
}

// POST: Analizar foto con Gemini Vision
add_action( 'wp_ajax_ma_ai_analyze_photo', function() {
    ma_ai_check_nonce();

    $photo_base64 = preg_replace('#[^A-Za-z0-9+/=]#', '', $_POST['photo_base64'] ?? '');
    if ( ! $photo_base64 ) wp_send_json_error( 'Imagen requerida' );

    $gemini_key = ma_get('gemini_api_key', '');
    if ( ! $gemini_key ) {
        wp_send_json_error( 'Para darte "super visión", necesito mi llave. Cópiala en Configuración ⚙️ > Inteligencia Artificial.' );
    }

    $prompt = 'Analiza este producto. Responde en JSON con estos campos exactos: ' .
              '"nombre" (nombre corto del producto real), "publico" (público objetivo en 1 frase y su dolor que resuelve), ' .
              '"caracteristicas" (3-5 beneficios clave separados por coma), ' .
              '"prompt_visual" (Describe FÍSICAMENTE el producto aislando su forma, color, textura y material en inglés a detalle estricto. Max 40 palabras. Servirá para regenerar el mismo producto en un modelo Text-to-Image). ' .
              'Solo el JSON, sin markdown.';

    $payload = [
        'contents' => [
            [
                'parts' => [
                    [ 'text' => $prompt ],
                    [ 'inlineData' => [
                        'mimeType' => 'image/jpeg',
                        'data'     => $photo_base64
                    ] ]
                ]
            ]
        ],
        'generationConfig' => [
            'responseMimeType' => 'application/json'
        ]
    ];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $gemini_key;

    $res = wp_remote_post( $url, [
        'timeout' => 45,
        'headers' => [ 'Content-Type' => 'application/json' ],
        'body'    => wp_json_encode( $payload ),
    ] );

    if ( is_wp_error( $res ) ) wp_send_json_error( $res->get_error_message() );
    $code = wp_remote_retrieve_response_code( $res );
    $body = json_decode( wp_remote_retrieve_body( $res ), true );
    
    // Capturar errores de Google
    if ( $code >= 400 ) {
        $msg = $body['error']['message'] ?? 'Error de API Google';
        wp_send_json_error( "Google AI rechazó la llave o la imagen: $msg" );
    }

    $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
    
    // Extraer solo la parte JSON (desde el primer { hasta el último })
    if (preg_match('/\{.*\}/s', $text, $matches)) {
        $text = $matches[0];
    }
    
    $parsed = json_decode( trim( $text ), true );

    wp_send_json_success( $parsed ?: [ 'nombre' => 'No detectado', 'publico' => '', 'caracteristicas' => $text ] );
} );

// POST: Generar todos los textos AIDA
add_action( 'wp_ajax_ma_ai_generate_all', function() {
    ma_ai_check_nonce();

    $product_name  = sanitize_text_field( $_POST['product_name']  ?? '' );
    $product_price = sanitize_text_field( $_POST['product_price'] ?? '' );
    $product_desc  = sanitize_textarea_field( $_POST['product_desc']  ?? '' );
    $audience      = sanitize_text_field( $_POST['audience']      ?? '' );
    $sections      = sanitize_text_field( $_POST['sections']      ?? 'hero,features,cta' );

    if ( ! $product_name ) wp_send_json_error( 'Nombre del producto requerido' );

    $price_text = $product_price ? "Precio: Q{$product_price}." : '';
    $audience_text = $audience ? "Público objetivo: {$audience}." : '';

    $prompt = "Eres un experto en copywriting de conversión para e-commerce en Guatemala.
Producto: {$product_name}. {$price_text} {$audience_text}
Descripción adicional: {$product_desc}

Genera copy AIDA de alta conversión en español (Guatemala). Responde SOLO en JSON válido con estos campos:
{
  \"hero_kicker\": \"subtítulo pequeño de 5-8 palabras que genera curiosidad\",
  \"hero_title\": \"título H1 principal de alto impacto (máx 10 palabras)\",
  \"hero_subtitle\": \"párrafo de 20-30 palabras que describe el problema que resuelve y para quién es\",
  \"hero_price_old\": \"precio anterior tachado (calcula +30% del precio actual)\",
  \"hero_price_new\": \"precio actual con Q\",
  \"hero_discount_label\": \"etiqueta de descuento (Ej: Ahorra 38% hoy)\",
  \"hero_btn_primary\": \"texto del botón principal (3-4 palabras con urgencia)\",
  \"ticker_text\": \"texto del ticker de urgencia con emojis (máx 80 chars)\",
  \"features_title\": \"título de la sección de características\",
  \"feat1_title\": \"característica 1, 2-3 palabras\",
  \"feat1_desc\": \"descripción 1, beneficio en 10-15 palabras\",
  \"feat2_title\": \"característica 2\",
  \"feat2_desc\": \"descripción 2\",
  \"feat3_title\": \"característica 3\",
  \"feat3_desc\": \"descripción 3\",
  \"oferta_title\": \"título de oferta especial\",
  \"oferta_flash\": \"texto de bono flash (urgencia)\",
  \"cta_title\": \"CTA final: pregunta que invita a comprar\",
  \"cta_desc\": \"descripción de garantías y seguridad (máx 15 palabras)\",
  \"cta_btn\": \"texto botón de compra final\",
  \"seo_title\": \"meta título SEO (máx 60 chars, incluye nombre + beneficio principal + Guatemala)\",
  \"seo_description\": \"meta descripción SEO (máx 155 chars, incluye CTA y palabra clave)\",
  \"product_long_html\": \"Redacción persuasiva completa en formato HTML nativo para la web. Usa etiquetas <h2>, <h3>, <ul>, <li>, <strong> y emojis abundantes para vender. Mínimo 100 palabras. Debe verse visualmente espectacular cuando se publique.\"
}
Solo JSON, sin comentarios ni markdown.";

    $gemini_key = ma_get('gemini_api_key', '');

    if ( $gemini_key ) {
        // Conexión Directa a Google (Mucho más rápida y JSON estricto)
        $payload = [
            'contents' => [
                [ 'parts' => [ [ 'text' => $prompt ] ] ]
            ],
            'generationConfig' => [ 'responseMimeType' => 'application/json' ]
        ];
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $gemini_key;
        
        $res = wp_remote_post( $url, [
            'timeout' => 45,
            'headers' => [ 'Content-Type' => 'application/json' ],
            'body'    => wp_json_encode( $payload ),
        ] );

        if ( is_wp_error( $res ) ) wp_send_json_error( $res->get_error_message() );
        $code = wp_remote_retrieve_response_code( $res );
        $body = json_decode( wp_remote_retrieve_body( $res ), true );
        
        if ( $code >= 400 ) wp_send_json_error( $body['error']['message'] ?? 'Error de API Google' );
        $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

    } else {
        // Fallback al Proxy Centralizado
        $payload = [
            'prompt' => $prompt,
            'model'  => 'gemini-1.5-flash',
            'format' => 'json',
        ];
        $res = wp_remote_post( ma_ai_fastapi_url() . '/api/gemini/generate-text', [
            'timeout' => 45,
            'headers' => [ 'Content-Type' => 'application/json' ],
            'body'    => wp_json_encode( $payload ),
        ] );

        if ( is_wp_error( $res ) ) wp_send_json_error( $res->get_error_message() );
        $code = wp_remote_retrieve_response_code( $res );
        $body = json_decode( wp_remote_retrieve_body( $res ), true );
        if ( $code >= 400 ) wp_send_json_error( $body['detail'] ?? 'Error del API Gemini' );

        $text = $body['text'] ?? $body['content'] ?? $body['response'] ?? '';
    }

    // Extraer solo la parte JSON (desde el primer { hasta el último })
    if (preg_match('/\{.*\}/s', $text, $matches)) {
        $text = $matches[0];
    }
    
    $parsed = json_decode( trim( $text ), true );

    if ( ! $parsed ) {
        wp_send_json_error( 'La IA devolvió un formato inesperado (' . substr($text,0,50) . '...). Intenta de nuevo.' );
    }

    wp_send_json_success( $parsed );
} );

// POST: Generar imagen con Imagen 4.0 (Directo)
add_action( 'wp_ajax_ma_ai_generate_image', function() {
    ma_ai_check_nonce();

    $gemini_key = ma_get('gemini_api_key', '');
    if ( ! $gemini_key ) {
        wp_send_json_error( 'Para generar fondos visuales, configura tu llave en Ajustes > Inteligencia Artificial.' );
    }

    $description = sanitize_textarea_field( $_POST['description'] ?? '' );
    $image_type  = sanitize_text_field( $_POST['image_type'] ?? 'producto' );
    $photo_b64   = $_POST['photo_base64'] ?? '';

    // 🔥 Auto-Enhancement: Expansión lógica de prompts cortos vía Gemini Text 🔥
    if ( strlen($description) < 30 ) {
        $contexto_base = $description ? $description : "Producto no especificado";
        $estilo_fotografico = ($image_type === 'producto') 
            ? "un set de estudio minimalista con fondo totalmente blanco y sombras suaves" 
            : "un entorno lifestyle espectacular, lujoso, cinemático o natural que aumente el deseo de compra";
            
        $expand_payload = [
            'contents' => [[ 'parts' => [[ 'text' => "Eres un Director de Arte de Apple. Tenemos el concepto básico de un producto: '$contexto_base'. Crea un PROMPT fotográfico visual en INGLÉS (max 30 palabras) definiendo $estilo_fotografico. Solo responde el prompt directo en inglés." ]] ]]
        ];
        $expand_res = wp_remote_post( "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $gemini_key, [
            'timeout' => 15, 'headers' => [ 'Content-Type' => 'application/json' ], 'body' => wp_json_encode( $expand_payload )
        ]);
        if ( ! is_wp_error($expand_res) && wp_remote_retrieve_response_code($expand_res) == 200 ) {
            $expand_body = json_decode( wp_remote_retrieve_body($expand_res), true );
            $expanded_text = $expand_body['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if ( !empty($expanded_text) ) {
                $description = trim(preg_replace('/[\n\r]+/', ' ', $expanded_text));
            }
        }
    }

    $modifiers = '';
    if ( $image_type === 'producto' )   { $modifiers = 'Plain white studio background, professional lighting.'; }
    if ( $image_type === 'lifestyle' )  { $modifiers = 'Real aesthetic environment, natural lighting, high quality lifestyle.'; }
    if ( $image_type === 'horizontal' ) { $modifiers = 'Wide panoramic layout for web banner, aesthetic balance.'; }
    if ( $image_type === 'vertical' )   { $modifiers = 'Vertical format for stories, visual appeal.'; }

    $final_prompt = "{$description}. {$modifiers} Hyper-realistic product photography, DSLR, 8k resolution.";

    $payload = [];
    $is_nano = false;

    if ( !empty($photo_b64) ) {
        $is_nano = true;
        // Identificar Mime Type
        $mime_type = 'image/jpeg';
        if (preg_match('#^data:(image/\w+);base64,#i', $photo_b64, $matches)) {
            $mime_type = $matches[1];
        }
        $photo_b64_clean = preg_replace('#^data:image/\w+;base64,#i', '', $photo_b64);
        
        $url = "https://generativelanguage.googleapis.com/v1beta/models/nano-banana-pro-preview:generateContent?key=" . $gemini_key;
        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [ 'text' => "INSTRUCCIÓN ESTRICTA: Escena: " . $final_prompt . " . Toma el producto de la imagen EXACTAMENTE como es (zero alteraciones, misma forma y color) y colócalo en ese escenario." ],
                        [ 'inlineData' => [ 'mimeType' => $mime_type, 'data' => $photo_b64_clean ] ]
                    ]
                ]
            ],
            'systemInstruction' => [
                'parts' => [[ 'text' => "You are an expert product photographer doing AI compositing. You MUST NOT hallucinate, mutate, or alter the product's physical shape, text, or logos. Maintain 100% strict fidelity to the input object. Output ONLY the generated image inlineData." ]]
            ],
            'generationConfig' => [ 'temperature' => 0.4 ]
        ];
    } else {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-fast-generate-001:predict?key=" . $gemini_key;
        $payload = [
            'instances'  => [[ 'prompt' => $final_prompt ]],
            'parameters' => [ 'sampleCount' => 1, 'aspectRatio' => $aspect ]
        ];
    }

    $res = wp_remote_post( $url, [
        'timeout' => 60,
        'headers' => [ 'Content-Type' => 'application/json' ],
        'body'    => wp_json_encode( $payload ),
    ] );

    if ( is_wp_error( $res ) ) wp_send_json_error( $res->get_error_message() );
    $code = wp_remote_retrieve_response_code( $res );
    $body = json_decode( wp_remote_retrieve_body( $res ), true );
    
    if ( $code >= 400 ) {
        $msg = $body['error']['message'] ?? 'Error de Modelo Google API';
        wp_send_json_error( $msg );
    }

    $b64 = '';
    if ( $is_nano ) {
        $parts = $body['candidates'][0]['content']['parts'] ?? [];
        foreach ($parts as $part) {
            if (isset($part['inlineData']['data'])) {
                $b64 = $part['inlineData']['data'];
                break;
            }
        }
    } else {
        $b64 = $body['predictions'][0]['bytesBase64Encoded'] ?? '';
    }

    if ( ! $b64 ) wp_send_json_error( 'El generador falló sin mensaje o no devolvió imagen válida.' );

    wp_send_json_success( [ 'url' => 'data:image/jpeg;base64,' . $b64 ] );
} );

/* ══════════════════════════════════════════════════════════════════════════
   PRODUCTO WooCommerce: Metabox "🤖 Generar con Gemini"
   Auto-rellena: Título, Descripción corta, Descripción larga, Costo proveedor
══════════════════════════════════════════════════════════════════════════ */

add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'ma-gemini-product',
        '🤖 Generar con Gemini AI' . ma_external_api_badge('Google Gemini API', 'Generación de contenido NLP/Imágenes'),
        'ma_gemini_product_metabox',
        'product',
        'side',
        'high'
    );
} );

function ma_gemini_product_metabox( $post ) {
    $fastapi_url = rtrim( ma_get( 'caex_fastapi_url', 'https://fastapi.modularis.pro' ), '/' );
    $nonce       = wp_create_nonce( 'ma_ai_action' );
    ?>
    <div id="ma-gem-box" style="font-family:-apple-system,BlinkMacSystemFont,sans-serif">

        <!-- Contexto adicional -->
        <div style="margin-bottom:10px">
            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:4px">
                Contexto adicional (opcional)
            </label>
            <textarea id="ma-gem-context" rows="2"
                      placeholder="Ej: Para personas con dolor cervical, precio competitivo..."
                      style="width:100%;padding:7px 9px;font-size:12px;border:1.5px solid #d1d5db;border-radius:7px;resize:vertical;box-sizing:border-box"></textarea>
        </div>

        <!-- Campos a generar -->
        <div style="margin-bottom:10px">
            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px">Generar:</div>
            <div style="display:flex;flex-direction:column;gap:4px">
                <?php
                $opts = [
                    'titulo'     => '📌 Título del producto',
                    'short_desc' => '📝 Descripción corta',
                    'long_desc'  => '📄 Descripción larga',
                    'seo'        => '🔍 SEO (meta título + descripción)',
                ];
                foreach ( $opts as $key => $label ) : ?>
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151;cursor:pointer">
                    <input type="checkbox" class="ma-gem-field" value="<?= esc_attr($key) ?>" checked
                           style="accent-color:#f97316;width:14px;height:14px">
                    <?= esc_html($label) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Botón generar -->
        <button type="button" id="ma-gem-generate"
                style="background:linear-gradient(135deg,#7c3aed,#f97316);color:#fff;width:100%;padding:9px 0;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;margin-bottom:8px">
            ✨ Generar con Gemini
        </button>

        <!-- Estado -->
        <div id="ma-gem-status" style="display:none;font-size:12px;text-align:center;color:#7c3aed;font-weight:600;margin-bottom:8px">
            ⏳ Generando...
        </div>

        <!-- Previsualización resultado -->
        <div id="ma-gem-preview" style="display:none;background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:10px;font-size:11px;margin-bottom:8px">
            <div style="font-weight:700;color:#16a34a;margin-bottom:6px">✅ Generado — revisa antes de aplicar:</div>
            <div id="ma-gem-preview-text" style="color:#374151;white-space:pre-wrap;max-height:180px;overflow-y:auto;line-height:1.5"></div>
        </div>

        <!-- Botón aplicar -->
        <button type="button" id="ma-gem-apply"
                style="display:none;background:#16a34a;color:#fff;width:100%;padding:8px 0;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer">
            ✅ Aplicar al producto
        </button>

        <!-- Error -->
        <div id="ma-gem-error" style="display:none;background:#fee2e2;border-radius:7px;padding:8px 10px;font-size:12px;color:#b91c1c;margin-top:6px"></div>
    </div>

    <script>
    (function($) {
        var AJAX_URL  = '<?= admin_url("admin-ajax.php") ?>';
        var NONCE     = '<?= $nonce ?>';
        var generated = {};

        $('#ma-gem-generate').on('click', function() {
            // Leer nombre del producto del título del editor WP
            var name = $('#title').val() || '<?= esc_js( $post->post_title ) ?>';
            // Precio del campo WooCommerce
            var price = $('#_regular_price').val() || '';

            if (!name.trim()) {
                alert('Escribe el nombre del producto primero');
                return;
            }

            $('#ma-gem-status').show();
            $('#ma-gem-preview').hide();
            $('#ma-gem-apply').hide();
            $('#ma-gem-error').hide();
            $(this).prop('disabled', true);

            var fields = [];
            $('.ma-gem-field:checked').each(function(){ fields.push($(this).val()); });

            $.ajax({
                url: AJAX_URL, method: 'POST',
                data: {
                    action: 'ma_ai_generate_all',
                    _nonce: NONCE,
                    product_name: name,
                    product_price: price,
                    product_desc: $('#ma-gem-context').val(),
                    sections: 'hero,features,cta,seo',
                    // Extra campos de producto
                    _gemini_fields: fields.join(',')
                },
                success: function(res) {
                    $('#ma-gem-status').hide();
                    $('#ma-gem-generate').prop('disabled', false);
                    if (res.success && res.data) {
                        generated = res.data;
                        var preview = '';
                        if (fields.indexOf('titulo') >= 0 && generated.hero_title) {
                            preview += '📌 TÍTULO:\n' + generated.hero_title + '\n\n';
                        }
                        if (fields.indexOf('short_desc') >= 0 && generated.hero_subtitle) {
                            preview += '📝 DESC. CORTA:\n' + generated.hero_subtitle + '\n\n';
                        }
                        if (fields.indexOf('long_desc') >= 0) {
                            var ldesc = generated.feat1_desc && generated.feat2_desc && generated.feat3_desc
                                ? generated.feat1_title + ': ' + generated.feat1_desc + '\n'
                                + generated.feat2_title + ': ' + generated.feat2_desc + '\n'
                                + generated.feat3_title + ': ' + generated.feat3_desc
                                : '';
                            if (ldesc) preview += '📄 DESC. LARGA:\n' + ldesc + '\n\n';
                        }
                        if (fields.indexOf('seo') >= 0) {
                            if (generated.seo_title)       preview += '🔍 SEO TÍTULO:\n' + generated.seo_title + '\n';
                            if (generated.seo_description) preview += '🔍 SEO META:\n' + generated.seo_description + '\n';
                        }
                        $('#ma-gem-preview-text').text(preview.trim());
                        $('#ma-gem-preview').show();
                        $('#ma-gem-apply').show();
                    } else {
                        $('#ma-gem-error').html('❌ ' + (res.data || 'Error al generar')).show();
                    }
                },
                error: function() {
                    $('#ma-gem-status').hide();
                    $('#ma-gem-generate').prop('disabled', false);
                    $('#ma-gem-error').html('❌ Error de conexión con Gemini').show();
                }
            });
        });

        $('#ma-gem-apply').on('click', function() {
            var fields = [];
            $('.ma-gem-field:checked').each(function(){ fields.push($(this).val()); });

            var applied = 0;

            // 1. Título
            if (fields.indexOf('titulo') >= 0 && generated.hero_title) {
                $('#title').val(generated.hero_title);
                applied++;
            }

            // 2. Descripción corta (excerpt)
            if (fields.indexOf('short_desc') >= 0 && generated.hero_subtitle) {
                // El excerpt en WP puede estar en un textarea o en TinyMCE
                if ($('#excerpt').length) {
                    $('#excerpt').val(generated.hero_subtitle);
                } else if (typeof tinyMCE !== 'undefined' && tinyMCE.get('excerpt')) {
                    tinyMCE.get('excerpt').setContent('<p>' + generated.hero_subtitle + '</p>');
                }
                applied++;
            }

            // 3. Descripción larga (content en WP editor)
            if (fields.indexOf('long_desc') >= 0) {
                var ldesc = generated.product_long_html || '';
                if (!ldesc && generated.feat1_title) {
                    ldesc += '<h2>✨ Beneficios Principales</h2>\n';
                    ldesc += '<h3>' + generated.feat1_title + '</h3><p>' + (generated.feat1_desc||'') + '</p>\n';
                    if (generated.feat2_title) ldesc += '<h3>' + generated.feat2_title + '</h3><p>' + (generated.feat2_desc||'') + '</p>\n';
                    if (generated.feat3_title) ldesc += '<h3>' + generated.feat3_title + '</h3><p>' + (generated.feat3_desc||'') + '</p>\n';
                    if (generated.cta_desc)    ldesc += '<p><strong>' + generated.cta_desc + '</strong></p>\n';
                }
                if (ldesc) {
                    if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
                        tinyMCE.get('content').setContent(ldesc);
                    } else if ($('#content').length) {
                        $('#content').val(ldesc);
                    }
                    applied++;
                }
            }

            // 4. SEO (si hay plugin) — Yoast o Rank Math
            if (fields.indexOf('seo') >= 0) {
                if (generated.seo_title && $('#yoast_wpseo_title').length) {
                    $('#yoast_wpseo_title').val(generated.seo_title); applied++;
                }
                if (generated.seo_description && $('#yoast_wpseo_metadesc').length) {
                    $('#yoast_wpseo_metadesc').val(generated.seo_description); applied++;
                }
                // Rank Math
                if (generated.seo_title && $('#rank-math-title').length) {
                    $('#rank-math-title').val(generated.seo_title); applied++;
                }
                if (generated.seo_description && $('#rank-math-description').length) {
                    $('#rank-math-description').val(generated.seo_description); applied++;
                }
            }

            if (applied > 0) {
                $(this).text('✅ Aplicado — recuerda guardar el producto');
                $(this).css('background', '#15803d');
                $('#ma-gem-preview').css('border-color', '#16a34a');
            } else {
                alert('No se pudo aplicar. Verifica que el editor esté cargado.');
            }
        });

    })(jQuery);
    </script>
    <?php
}

/* ── 4. Generación Asíncrona Video (Google Veo) ──────────────── */
add_action( 'wp_ajax_ma_ai_generate_video_start', function() {
    ma_ai_check_nonce();
    $desc = sanitize_textarea_field( $_POST['description'] ?? '' );
    if ( ! $desc ) wp_send_json_error( 'Falta descripción para el video' );

    $photo_b64 = $_POST['photo_base64'] ?? '';
    if ( ! $photo_b64 ) wp_send_json_error( 'Falta foto de referencia del producto' );

    $mime_type = 'image/jpeg';
    if (preg_match('#^data:(image/\w+);base64,#i', $photo_b64, $matches)) {
        $mime_type = $matches[1];
    }
    $photo_b64_clean = preg_replace('#^data:image/\w+;base64,#i', '', $photo_b64);

    $gemini_key = ma_get('gemini_api_key', '');
    $url = "https://generativelanguage.googleapis.com/v1beta/models/veo-2.0-generate-001:predictLongRunning?key=" . $gemini_key;

    $payload = [
        'instances' => [
            [
                'prompt' => "INSTRUCCIÓN ESTRICTA: Eres un Renderizador Comercial de Producto. Escena: " . $desc . ". Debes incluir el producto de la imagen como sujeto central del video sin mutarlo o alterarlo drásticamente.",
                'image' => [
                    'bytesBase64Encoded' => $photo_b64_clean,
                    'mimeType' => $mime_type
                ]
            ]
        ],
        'parameters' => [
            'aspectRatio' => '16:9'
        ]
    ];

    $res = wp_remote_post( $url, [
        'timeout' => 30,
        'headers' => [ 'Content-Type' => 'application/json' ],
        'body'    => wp_json_encode( $payload ),
    ] );

    if ( is_wp_error($res) ) wp_send_json_error($res->get_error_message());
    $code = wp_remote_retrieve_response_code($res);
    $body = json_decode(wp_remote_retrieve_body($res), true);

    if ( $code >= 400 ) wp_send_json_error( $body['error']['message'] ?? 'Error iniciando motor Veo' );
    if ( empty($body['name']) ) wp_send_json_error( 'Google no devolvió Operation ID' );

    wp_send_json_success( [ 'operation_id' => $body['name'] ] );
});

add_action( 'wp_ajax_ma_ai_generate_video_check', function() {
    ma_ai_check_nonce();
    $op_id = sanitize_text_field( $_POST['operation_id'] ?? '' );
    if ( ! $op_id ) wp_send_json_error('ID de operación falta');

    $gemini_key = ma_get('gemini_api_key', '');
    // $op_id contiene ej: "models/veo-2.0-generate-001/operations/3wtkuln"
    $url = "https://generativelanguage.googleapis.com/v1beta/" . $op_id . "?key=" . $gemini_key;

    $res = wp_remote_get( $url, [ 'timeout' => 30 ] );
    if ( is_wp_error($res) ) wp_send_json_error($res->get_error_message());
    
    $body = json_decode(wp_remote_retrieve_body($res), true);

    if ( isset($body['error']) ) {
        wp_send_json_error( $body['error']['message'] ?? 'Error verificando operación.' );
    }

    if ( empty($body['done']) ) {
        wp_send_json_success( [ 'status' => 'RUNNING' ] );
    }

    // Video completado, encontrar los bytes.
    $video_b64 = '';
    if ( isset($body['response']['predictions'][0]['bytesBase64Encoded']) ) {
         $video_b64 = $body['response']['predictions'][0]['bytesBase64Encoded'];
    } elseif ( isset($body['response']['video']['bytesBase64Encoded']) ) {
         $video_b64 = $body['response']['video']['bytesBase64Encoded'];
    } elseif ( isset($body['response']['predictions'][0]['videoBytes']) ) {
         $video_b64 = $body['response']['predictions'][0]['videoBytes'];
    } elseif ( isset($body['metadata']['video']['bytesBase64Encoded']) ) {
         $video_b64 = $body['metadata']['video']['bytesBase64Encoded'];
    } else {
         // Fallback exploratorio
         $json_resp = json_encode($body['response'] ?? []);
         if (preg_match('/"([a-zA-Z0-9+\/]+={0,2})"/', $json_resp, $m) && strlen($m[1]) > 50000) {
             $video_b64 = $m[1];
         } else {
             wp_send_json_error('Video generado pero el formato interno es desconocido.');
         }
    }

    if ( empty($video_b64) ) wp_send_json_error('Bytes de video no localizados en la respuesta JSON.');

    // Save MP4
    $upload = wp_upload_bits('veo_demo_' . time() . '.mp4', null, base64_decode($video_b64));
    if ( $upload['error'] ) wp_send_json_error( $upload['error'] );

    wp_send_json_success( [ 'status' => 'COMPLETED', 'url' => $upload['url'] ] );
});
