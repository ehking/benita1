<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
    <div class="site-branding">
        <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
        <?php else : ?>
            <h1><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
        <?php endif; ?>
    </div>
    <nav class="primary-navigation" aria-label="<?php esc_attr_e('Primary menu', 'three-d-accessory-store'); ?>">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'menu_class'     => 'menu-items',
            'container'      => false,
            'fallback_cb'    => '__return_false',
        ]);
        ?>
    </nav>
</header>
<main class="site-content">
