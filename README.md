# 3D Accessory Store WordPress Theme

This repository contains a WooCommerce-ready WordPress theme tailored for 3D-printed accessory shops. It provides an interactive Three.js-based product customizer so customers can tweak finishes, patterns, and engravings in real time before placing an order.

## Features
- Responsive storefront with hero section and featured product grid.
- Three.js viewer embedded on single product pages for orbit/zoom interactions.
- Customization controls for colors, patterns, and engraving text with Ajax persistence.
- WooCommerce cart and order meta integration to forward customization details to fulfillment.

## Getting Started
1. Copy the theme folder to `wp-content/themes/` inside your WordPress installation.
2. Install and activate WooCommerce plus any Persian localization/payment plugins you require.
3. Activate the **3D Accessory Store** theme from the WordPress admin dashboard.
4. Create WooCommerce attributes named `pa_color` and `pa_pattern` with your desired options. These populate the swatches in the customizer interface.
5. Upload GLB/GLTF models for each product to `wp-content/themes/3d-accessory-store/assets/models/` and update the default model path if necessary.
6. (Optional) Use Advanced Custom Fields or product metadata to store model URLs per product and adjust `functions.php` to pull them dynamically.

## Customization Notes
- The default color hex values for common metallic finishes are defined in `assets/js/product-customizer.js`. Extend `defaultColorMap` to support additional finishes.
- Replace the placeholder font/model files in `assets/fonts` and `assets/models` with production-ready assets.
- The Ajax endpoint (`three_d_store_customize`) currently stores selections in the session and cart metadata. Extend it to generate STL files or trigger slicer workflows as needed.

## Development
- Styles live in `style.css` (global) and `assets/css/customizer.css` (product customizer UI).
- JavaScript powering the Three.js scene resides in `assets/js/product-customizer.js`.
- The WooCommerce customizer markup is rendered via `templates/customizer-interface.php`.

## License
Released under the GPL-2.0-or-later license.
