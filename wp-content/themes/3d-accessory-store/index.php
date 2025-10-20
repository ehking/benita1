<?php get_header(); ?>
<section class="hero">
    <h2><?php esc_html_e('Customize Your 3D-Printed Accessories', 'three-d-accessory-store'); ?></h2>
    <p><?php esc_html_e('Explore premium-quality pieces, personalize colors and patterns, and see your design come to life in 3D before ordering.', 'three-d-accessory-store'); ?></p>
    <a class="cta" href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"><?php esc_html_e('Shop the Collection', 'three-d-accessory-store'); ?></a>
</section>

<section class="featured-products">
    <?php
    $args  = [
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'meta_query'     => WC()->query->get_meta_query(),
        'tax_query'      => WC()->query->get_tax_query(),
    ];
    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) :
            $query->the_post();
            global $product;
            ?>
            <article <?php post_class('product-card'); ?>>
                <a href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) :
                        the_post_thumbnail('large');
                    else : ?>
                        <img src="<?php echo esc_url(wc_placeholder_img_src('large')); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <h3><?php the_title(); ?></h3>
                    <span class="price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                    <a class="cta" href="<?php the_permalink(); ?>"><?php esc_html_e('Personalize', 'three-d-accessory-store'); ?></a>
                </div>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>' . esc_html__('No products found. Start adding your 3D accessories!', 'three-d-accessory-store') . '</p>';
    endif;
    ?>
</section>
<?php get_footer(); ?>
