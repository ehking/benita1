</main>
<footer class="site-footer">
    <p>&copy; <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'three-d-accessory-store'); ?></p>
    <nav class="footer-navigation" aria-label="<?php esc_attr_e('Footer menu', 'three-d-accessory-store'); ?>">
        <?php
        wp_nav_menu([
            'theme_location' => 'footer',
            'menu_class'     => 'footer-menu',
            'container'      => false,
            'fallback_cb'    => '__return_false',
        ]);
        ?>
    </nav>
</footer>
<?php wp_footer(); ?>
</body>
</html>
