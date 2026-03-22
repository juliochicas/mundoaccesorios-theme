<?php /* section-faq - Template Part */
if ( ma_get('hide_sec_faq') ) return;

$faqs = new WP_Query([
    'post_type'      => 'ma_faq',
    'posts_per_page' => 20,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);

// Fallback si el CPT está vacío
$default_faqs = [
    ['¿Cuándo recibo mi pedido?', 'Despachamos en 24 horas hábiles. En Ciudad de Guatemala y áreas metropolitanas lo recibes en 1-2 días. En departamentos entre 2-4 días hábiles según zona.'],
    ['¿Cómo sé que mi pedido fue confirmado?', 'Te contactamos por WhatsApp para confirmar tu pedido, validar datos de envío y darte un número de rastreo antes del despacho.'],
    ['¿Qué métodos de pago aceptan?', 'Tarjeta de crédito o débito (Visa, Mastercard, AMEX) a través de Stripe con conversión automática. También aceptamos pago contra entrega en zonas habilitadas.'],
    ['¿Tiene garantía?', 'Sí, 30 días de garantía por defecto de fábrica. Si tienes algún problema, te ayudamos por WhatsApp.'],
    ['¿Puedo devolver el producto?', 'Sí. Tienes 30 días para solicitar devolución si el producto no cumple tus expectativas. Consulta nuestra política de reembolsos para más detalles.'],
];

$faq_img = ma_get( 'faq_img', '' );
?>
<section id="faq" class="section wrap band">
    <h2>Preguntas frecuentes</h2>
    <div style="display:flex; flex-wrap:wrap; gap:32px; align-items:flex-start;">
        <div class="faq" style="flex:1; min-width:300px;">
        <?php if ( $faqs->have_posts() ) :
            while ( $faqs->have_posts() ) : $faqs->the_post(); ?>
          <details class="faq-item">
            <summary><?= esc_html( get_the_title() ) ?></summary>
            <p><?= wp_kses_post( get_the_content() ) ?></p>
          </details>
        <?php endwhile; wp_reset_postdata();
        else :
            foreach ( $default_faqs as [$q, $a] ) : ?>
          <details class="faq-item">
            <summary><?= esc_html($q) ?></summary>
            <p><?= esc_html($a) ?></p>
          </details>
        <?php endforeach; endif; ?>
        </div>
        <?php if ( $faq_img ) : ?>
        <div style="flex:1; min-width:300px; text-align:center; position:sticky; top:100px;">
            <img src="<?= esc_url($faq_img) ?>" alt="Guía de uso ilustrada" loading="lazy" style="width:100%; max-width:400px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1);" />
        </div>
        <?php endif; ?>
    </div>
</section>
