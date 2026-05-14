<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php bloginfo('name'); ?>">
            <span class="brand-mark">M</span>
            <span><?php bloginfo('name'); ?></span>
        </a>

        <nav aria-label="<?php esc_attr_e('Primary navigation', 'mia-jewelry'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'nav-menu',
                'fallback_cb' => 'mia_jewelry_menu_fallback',
            ]);
            ?>
        </nav>

        <div class="header-actions">
            <?php if (function_exists('wc_get_cart_url')) : ?>
                <a class="cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                    Cart <?php echo esc_html(mia_jewelry_cart_count()); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="site-main">

