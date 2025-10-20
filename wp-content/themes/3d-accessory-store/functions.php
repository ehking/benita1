<?php
/**
 * Theme bootstrap for 3D Accessory Store.
 */

declare(strict_types=1);

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    register_nav_menus([
        'primary' => __('Primary Menu', 'three-d-accessory-store'),
        'footer'  => __('Footer Menu', 'three-d-accessory-store'),
    ]);
});

add_action('wp_enqueue_scripts', function (): void {
    $theme_version = wp_get_theme()->get('Version');

    wp_enqueue_style('three-d-accessory-store-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', [], null);
    wp_enqueue_style('three-d-accessory-store-style', get_stylesheet_uri(), ['three-d-accessory-store-fonts'], $theme_version);
    wp_enqueue_style('three-d-accessory-store-customizer', get_theme_file_uri('assets/css/customizer.css'), ['three-d-accessory-store-style'], $theme_version);

    if (is_product()) {
        wp_enqueue_script('three-js', 'https://cdn.jsdelivr.net/npm/three@0.160.1/build/three.min.js', [], null, true);
        wp_enqueue_script('three-js-orbit', 'https://cdn.jsdelivr.net/npm/three@0.160.1/examples/js/controls/OrbitControls.js', ['three-js'], null, true);
        wp_enqueue_script('three-d-accessory-store-gltf', 'https://cdn.jsdelivr.net/npm/three@0.160.1/examples/js/loaders/GLTFLoader.js', ['three-js'], null, true);
        wp_enqueue_script('three-d-accessory-store-customizer', get_theme_file_uri('assets/js/product-customizer.js'), ['three-js', 'three-js-orbit', 'three-d-accessory-store-gltf'], $theme_version, true);

        wp_localize_script('three-d-accessory-store-customizer', 'ThreeDStoreCustomizer', [
            'ajax_url'         => admin_url('admin-ajax.php'),
            'nonce'            => wp_create_nonce('three_d_customizer'),
            'default_model'    => get_theme_file_uri('assets/models/sample-accessory.glb'),
            'text_font'        => get_theme_file_uri('assets/fonts/Roboto_Regular.json'),
            'color_label'      => __('Select finish color', 'three-d-accessory-store'),
            'pattern_label'    => __('Choose a pattern', 'three-d-accessory-store'),
            'engraving_label'  => __('Add engraving text', 'three-d-accessory-store'),
            'add_to_cart_text' => __('Update preview', 'three-d-accessory-store'),
        ]);
    }
});

/**
 * Inject the customizer interface into the single product summary.
 */
add_action('woocommerce_single_product_summary', function (): void {
    if (!is_product()) {
        return;
    }

    wc_get_template(
        'customizer-interface.php',
        [
            'color_options'   => three_d_accessory_store_get_attribute_terms('pa_color'),
            'pattern_options' => three_d_accessory_store_get_attribute_terms('pa_pattern'),
        ],
        '',
        get_theme_file_path('templates/')
    );
}, 25);

/**
 * Helper: Fetch WooCommerce product attribute terms if they exist.
 *
 * @param string $taxonomy
 * @return array<int, array<string, string>>
 */
function three_d_accessory_store_get_attribute_terms(string $taxonomy): array
{
    if (!taxonomy_exists($taxonomy)) {
        return [];
    }

    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms)) {
        return [];
    }

    return array_map(static function ($term): array {
        return [
            'slug' => $term->slug,
            'name' => $term->name,
        ];
    }, $terms);
}

/**
 * AJAX endpoint for exporting the configuration.
 * Stores selection in the WooCommerce cart item for fulfillment reference.
 */
add_action('wp_ajax_three_d_store_customize', 'three_d_accessory_store_handle_customization');
add_action('wp_ajax_nopriv_three_d_store_customize', 'three_d_accessory_store_handle_customization');

function three_d_accessory_store_handle_customization(): void
{
    check_ajax_referer('three_d_customizer', 'nonce');

    $configuration = [
        'color'     => sanitize_text_field($_POST['color'] ?? ''),
        'pattern'   => sanitize_text_field($_POST['pattern'] ?? ''),
        'engraving' => sanitize_text_field($_POST['engraving'] ?? ''),
    ];

    wp_send_json_success([
        'message' => __('Customization saved. Add the product to your cart to continue.', 'three-d-accessory-store'),
        'data'    => $configuration,
    ]);
}

/**
 * Persist customization data into the WooCommerce cart.
 */
add_filter('woocommerce_add_cart_item_data', function (array $cart_item_data, int $product_id, int $variation_id = 0): array {
    if (!isset($_POST['three_d_customization'])) {
        return $cart_item_data;
    }

    $payload = json_decode(stripslashes((string) $_POST['three_d_customization']), true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
        return $cart_item_data;
    }

    $cart_item_data['three_d_customization'] = array_filter([
        'color'     => sanitize_text_field($payload['color'] ?? ''),
        'pattern'   => sanitize_text_field($payload['pattern'] ?? ''),
        'engraving' => sanitize_text_field($payload['engraving'] ?? ''),
    ]);

    return $cart_item_data;
}, 10, 3);

add_filter('woocommerce_get_item_data', function (array $item_data, array $cart_item): array {
    if (!isset($cart_item['three_d_customization'])) {
        return $item_data;
    }

    foreach ($cart_item['three_d_customization'] as $key => $value) {
        $label = ucwords(str_replace('_', ' ', $key));
        $item_data[] = [
            'name'  => esc_html($label),
            'value' => esc_html($value),
        ];
    }

    return $item_data;
}, 10, 2);

add_action('woocommerce_order_item_meta_end', function ($item_id, $item, $order, $plain_text) {
    $data = $item->get_meta('three_d_customization');

    if (empty($data)) {
        return;
    }

    if ($plain_text) {
        echo "\n" . __('Customization:', 'three-d-accessory-store') . ' ' . wp_json_encode($data);
        return;
    }

    echo '<div class="three-d-customization"><strong>' . esc_html__('Customization', 'three-d-accessory-store') . ':</strong> ' . esc_html(wp_json_encode($data)) . '</div>';
}, 10, 4);

add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    if (isset($values['three_d_customization'])) {
        $item->add_meta_data('three_d_customization', $values['three_d_customization']);
    }
}, 10, 4);
