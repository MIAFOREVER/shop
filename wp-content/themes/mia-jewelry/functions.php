<?php

if (!defined('ABSPATH')) {
    exit;
}

function mia_jewelry_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus([
        'primary' => __('Primary menu', 'mia-jewelry'),
    ]);
}
add_action('after_setup_theme', 'mia_jewelry_setup');

function mia_jewelry_assets()
{
    wp_enqueue_style('mia-jewelry-style', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'mia_jewelry_assets');

function mia_jewelry_menu_fallback()
{
    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
    echo '<li><a href="' . esc_url($shop_url) . '">Shop</a></li>';
    echo '<li><a href="' . esc_url(home_url('/#collections')) . '">Collections</a></li>';
    echo '<li><a href="' . esc_url(home_url('/#craft')) . '">Craft</a></li>';
    echo '</ul>';
}

function mia_jewelry_cart_count()
{
    if (!function_exists('WC') || !WC()->cart) {
        return 0;
    }

    return WC()->cart->get_cart_contents_count();
}

function mia_jewelry_product_cat_url($category_name)
{
    if (!taxonomy_exists('product_cat') || !function_exists('wc_get_page_permalink')) {
        return home_url('/');
    }

    $url = get_term_link($category_name, 'product_cat');

    if (is_wp_error($url)) {
        return wc_get_page_permalink('shop');
    }

    return $url;
}

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start();
    ?>
    <a class="cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>">
        Cart <?php echo esc_html(mia_jewelry_cart_count()); ?>
    </a>
    <?php
    $fragments['a.cart-link'] = ob_get_clean();
    return $fragments;
});

add_action('after_switch_theme', function () {
    $menu_name = 'Mia Primary';
    $menu = wp_get_nav_menu_object($menu_name);

    if (!$menu) {
        $menu_id = wp_create_nav_menu($menu_name);
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => 'Home',
            'menu-item-url' => home_url('/'),
            'menu-item-status' => 'publish',
        ]);
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => 'Shop',
            'menu-item-url' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'),
            'menu-item-status' => 'publish',
        ]);
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => 'Collections',
            'menu-item-url' => home_url('/#collections'),
            'menu-item-status' => 'publish',
        ]);
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => 'Craft',
            'menu-item-url' => home_url('/#craft'),
            'menu-item-status' => 'publish',
        ]);
    } else {
        $menu_id = $menu->term_id;
    }

    set_theme_mod('nav_menu_locations', ['primary' => $menu_id]);
});
