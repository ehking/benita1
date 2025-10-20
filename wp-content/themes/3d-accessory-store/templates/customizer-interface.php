<?php
/**
 * Customizer interface embedded in WooCommerce single product view.
 *
 * @var array $color_options
 * @var array $pattern_options
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="customizer-panel" id="three-d-customizer" data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
    <h2 class="customizer-title"><?php esc_html_e('Design your accessory', 'three-d-accessory-store'); ?></h2>
    <p class="customizer-subtitle"><?php esc_html_e('Rotate the model, adjust finishes, and add custom engraving text.', 'three-d-accessory-store'); ?></p>

    <div class="customizer-viewer">
        <canvas id="three-d-preview" aria-label="<?php esc_attr_e('3D preview of the accessory', 'three-d-accessory-store'); ?>"></canvas>
    </div>

    <div class="customizer-controls">
        <?php if (!empty($color_options)) : ?>
            <fieldset class="control-group">
                <legend><?php echo esc_html__('Color', 'three-d-accessory-store'); ?></legend>
                <div class="swatch-grid" role="radiogroup">
                    <?php foreach ($color_options as $index => $option) : ?>
                        <label class="swatch">
                            <input type="radio" name="three_d_color" value="<?php echo esc_attr($option['slug']); ?>" <?php checked(0 === $index); ?>>
                            <span><?php echo esc_html($option['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endif; ?>

        <?php if (!empty($pattern_options)) : ?>
            <fieldset class="control-group">
                <legend><?php echo esc_html__('Pattern', 'three-d-accessory-store'); ?></legend>
                <div class="swatch-grid" role="radiogroup">
                    <?php foreach ($pattern_options as $index => $option) : ?>
                        <label class="swatch">
                            <input type="radio" name="three_d_pattern" value="<?php echo esc_attr($option['slug']); ?>" <?php checked(0 === $index); ?>>
                            <span><?php echo esc_html($option['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endif; ?>

        <fieldset class="control-group">
            <legend><?php echo esc_html__('Engraving', 'three-d-accessory-store'); ?></legend>
            <label for="three-d-engraving" class="screen-reader-text"><?php esc_html_e('Engraving text', 'three-d-accessory-store'); ?></label>
            <input id="three-d-engraving" type="text" name="three_d_engraving" maxlength="20" placeholder="<?php echo esc_attr__('Add up to 20 characters', 'three-d-accessory-store'); ?>">
        </fieldset>

        <button type="button" class="customizer-submit button alt" id="three-d-customizer-update">
            <?php esc_html_e('Update 3D Preview', 'three-d-accessory-store'); ?>
        </button>

        <input type="hidden" name="three_d_customization" id="three-d-customization-data" value="">
    </div>
</div>
