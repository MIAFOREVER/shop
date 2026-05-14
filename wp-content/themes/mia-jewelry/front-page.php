<?php get_header(); ?>

<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">Fine jewelry studio</p>
        <h1>Jewelry that keeps the light.</h1>
        <p>Modern pearls, sculptural rings, and luminous essentials designed for daily rituals and special evenings.</p>
        <div class="hero-actions">
            <?php if (function_exists('wc_get_page_permalink')) : ?>
                <a class="button" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Shop Jewelry</a>
            <?php endif; ?>
            <a class="button secondary" href="#collections">View Collections</a>
        </div>
    </div>
</section>

<section id="collections" class="section">
    <div class="section-heading">
        <h2>Signature collections</h2>
        <p>Soft shine, clean silhouettes, and pieces built to layer beautifully.</p>
    </div>

    <div class="category-grid">
        <a class="category-tile" href="<?php echo esc_url(mia_jewelry_product_cat_url('Rings')); ?>">
            <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?auto=format&fit=crop&w=900&q=82" alt="Gold ring on a soft surface">
            <span>Rings <b>Shop</b></span>
        </a>
        <a class="category-tile" href="<?php echo esc_url(mia_jewelry_product_cat_url('Necklaces')); ?>">
            <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=900&q=82" alt="Pearl necklace detail">
            <span>Necklaces <b>Shop</b></span>
        </a>
        <a class="category-tile" href="<?php echo esc_url(mia_jewelry_product_cat_url('Earrings')); ?>">
            <img src="https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=900&q=82" alt="Gold earrings close up">
            <span>Earrings <b>Shop</b></span>
        </a>
    </div>
</section>

<?php if (class_exists('WooCommerce')) : ?>
    <section class="section">
        <div class="section-heading">
            <h2>New arrivals</h2>
            <p>Ready-to-ship pieces selected for a polished everyday edit.</p>
        </div>
        <?php echo do_shortcode('[products limit="4" columns="4" orderby="date" order="DESC"]'); ?>
    </section>
<?php endif; ?>

<section id="craft" class="section">
    <div class="feature-band">
        <img src="https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?auto=format&fit=crop&w=1200&q=86" alt="Jewelry being arranged on a work table">
        <div class="feature-copy">
            <h2>Made to feel personal.</h2>
            <p>Each piece is chosen around proportion, finish, and wearability, so the store feels curated instead of crowded.</p>
            <?php if (function_exists('wc_get_page_permalink')) : ?>
                <a class="button" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Browse the shop</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="trust-row">
        <div class="trust-item">
            <strong>Secure checkout</strong>
            <p>WooCommerce-ready checkout flow for PayPal, Stripe, and other gateways.</p>
        </div>
        <div class="trust-item">
            <strong>Giftable packaging</strong>
            <p>Product copy and layout are prepared for jewelry gifting moments.</p>
        </div>
        <div class="trust-item">
            <strong>Easy to extend</strong>
            <p>Theme files stay small so product pages, SEO, and email flows can grow cleanly.</p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
