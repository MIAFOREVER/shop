<?php
/**
 * Plugin Name: Mia Jewelry Seed Tools
 * Description: WP-CLI setup command for the local jewelry WooCommerce demo.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('mia seed', function () {
        if (!class_exists('WooCommerce')) {
            WP_CLI::error('WooCommerce must be active before running mia seed.');
        }

        update_option('blogname', 'Mia Jewelry');
        update_option('blogdescription', 'Fine jewelry for everyday rituals');
        update_option('timezone_string', 'Asia/Shanghai');
        update_option('woocommerce_currency', 'USD');
        update_option('woocommerce_default_country', 'US:CA');
        update_option('woocommerce_store_address', '123 Atelier Lane');
        update_option('woocommerce_store_city', 'Los Angeles');
        update_option('woocommerce_store_postcode', '90015');
        update_option('woocommerce_allowed_countries', 'all');
        update_option('woocommerce_calc_taxes', 'no');
        update_option('woocommerce_enable_guest_checkout', 'yes');
        update_option('woocommerce_enable_checkout_login_reminder', 'no');
        update_option('woocommerce_ship_to_countries', 'all');
        update_option('woocommerce_catalog_columns', 3);
        update_option('woocommerce_catalog_rows', 3);

        if (class_exists('WC_Install')) {
            WC_Install::create_pages();
        }

        $shop_page_id = wc_get_page_id('shop');
        if ($shop_page_id > 0) {
            update_option('woocommerce_shop_page_id', $shop_page_id);
        }

        $categories = [
            'Rings' => 'Sculptural rings in gold vermeil and sterling silver.',
            'Necklaces' => 'Layerable chains, pendants, and pearl pieces.',
            'Earrings' => 'Everyday studs, hoops, and special occasion drops.',
        ];

        foreach ($categories as $name => $description) {
            if (!term_exists($name, 'product_cat')) {
                wp_insert_term($name, 'product_cat', ['description' => $description]);
            }
        }

        mia_seed_enable_checkout_basics();

        $products = [
            [
                'name' => 'Celeste Pearl Necklace',
                'sku' => 'MIA-NECK-001',
                'price' => '128',
                'category' => 'Necklaces',
                'image' => 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=1200&q=85',
                'short' => 'A luminous freshwater pearl pendant on a delicate gold vermeil chain.',
                'description' => 'Designed for soft shine from morning meetings to candlelit dinners. Finished with an adjustable clasp for effortless layering.',
            ],
            [
                'name' => 'Aurora Signet Ring',
                'sku' => 'MIA-RING-001',
                'price' => '96',
                'category' => 'Rings',
                'image' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?auto=format&fit=crop&w=1200&q=85',
                'short' => 'A polished signet ring with a low-profile silhouette and mirror finish.',
                'description' => 'A modern heirloom shape crafted for daily wear. Wear it solo or stack it with slim bands for contrast.',
            ],
            [
                'name' => 'Luna Drop Earrings',
                'sku' => 'MIA-EAR-001',
                'price' => '112',
                'category' => 'Earrings',
                'image' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=1200&q=85',
                'short' => 'Fluid drop earrings with a bright finish and lightweight movement.',
                'description' => 'Made to catch light without feeling heavy. A refined finishing touch for dresses, knits, and silk shirts.',
            ],
            [
                'name' => 'Solitaire Stacking Band',
                'sku' => 'MIA-RING-002',
                'price' => '74',
                'category' => 'Rings',
                'image' => 'https://images.unsplash.com/photo-1611652022419-a9419f74343d?auto=format&fit=crop&w=1200&q=85',
                'short' => 'A slim stacking band with a single crystal-set focal point.',
                'description' => 'Subtle sparkle with a clean profile. Sized for comfortable everyday stacking.',
            ],
        ];

        foreach ($products as $item) {
            $existing_id = wc_get_product_id_by_sku($item['sku']);
            $product = $existing_id ? wc_get_product($existing_id) : new WC_Product_Simple();

            $product->set_name($item['name']);
            $product->set_sku($item['sku']);
            $product->set_regular_price($item['price']);
            $product->set_price($item['price']);
            $product->set_short_description($item['short']);
            $product->set_description($item['description']);
            $product->set_manage_stock(true);
            $product->set_stock_quantity(24);
            $product->set_stock_status('instock');
            $product->set_catalog_visibility('visible');
            $product_id = $product->save();

            $term = term_exists($item['category'], 'product_cat');
            if ($term) {
                wp_set_object_terms($product_id, [(int) $term['term_id']], 'product_cat');
            }

            if (!has_post_thumbnail($product_id)) {
                $attachment_id = mia_seed_sideload_image($item['image'], $product_id, $item['name']);
                if ($attachment_id) {
                    set_post_thumbnail($product_id, $attachment_id);
                }
            }
        }

        update_option('woocommerce_coming_soon', 'no');
        update_option('woocommerce_onboarding_profile', [
            'completed' => true,
            'industry' => [['slug' => 'fashion-apparel-accessories']],
            'product_types' => ['physical'],
        ]);

        WP_CLI::success('Mia Jewelry demo store seeded.');
    });
}

function mia_seed_sideload_image($url, $post_id, $description = '')
{
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attachment_id = media_sideload_image($url, $post_id, $description, 'id');

    if (is_wp_error($attachment_id)) {
        WP_CLI::warning(sprintf('Could not sideload image for "%s": %s', $description, $attachment_id->get_error_message()));
        return 0;
    }

    return (int) $attachment_id;
}

function mia_seed_enable_checkout_basics()
{
    update_option('woocommerce_cod_settings', [
        'enabled' => 'yes',
        'title' => 'Pay after confirmation',
        'description' => 'Place the order now and receive payment instructions by email.',
        'instructions' => 'Thank you. We will confirm availability and send payment instructions shortly.',
        'enable_for_methods' => [],
        'enable_for_virtual' => 'yes',
    ]);

    $zone = new WC_Shipping_Zone(0);
    $flat_rate_instance_id = 0;

    foreach ($zone->get_shipping_methods() as $method) {
        if ($method->id === 'flat_rate') {
            $flat_rate_instance_id = $method->instance_id;
            break;
        }
    }

    if (!$flat_rate_instance_id) {
        $flat_rate_instance_id = $zone->add_shipping_method('flat_rate');
    }

    update_option('woocommerce_flat_rate_' . $flat_rate_instance_id . '_settings', [
        'title' => 'Standard shipping',
        'tax_status' => 'none',
        'cost' => '6.95',
        'type' => 'class',
    ]);
}
