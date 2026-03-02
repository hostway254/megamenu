<?php
/**
 * Plugin Name: Ideal Mega Menu
 * Plugin URI:  https://idealcontainers.co.ke
 * Description: A configurable mega menu for WordPress. Activate mega menu columns on specific top-level menu items via Appearance → Mega Menu.
 * Version:     1.0.0
 * Author:      Ideal Containers
 * Author URI:  https://idealcontainers.co.ke
 * License:     GPL-2.0+
 * Text Domain: ideal-mega-menu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'IMM_VERSION', '1.0.0' );
define( 'IMM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IMM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * ─── ACTIVATION: set sensible defaults ───
 */
register_activation_hook( __FILE__, function () {
    $defaults = array(
        'imm_enabled'           => '1',
        'imm_menu_location'     => 'primary',
        'imm_mega_items'        => array(),   // menu item IDs that trigger mega dropdown
        'imm_columns'           => 5,
        'imm_bg_color'          => '#ffffff',
        'imm_heading_color'     => '#222222',
        'imm_link_color'        => '#555555',
        'imm_link_hover_color'  => '#0073aa',
        'imm_separator_color'   => '#e5e5e5',
        'imm_shadow'            => '1',
        'imm_animation'         => 'fade',    // none | fade | slide
        'imm_animation_speed'   => 300,
        'imm_full_width'        => '1',
        'imm_max_width'         => '1200',
        'imm_heading_font_size' => '14',
        'imm_link_font_size'    => '13',
        'imm_column_padding'    => '20',
        'imm_border_radius'     => '4',
    );
    foreach ( $defaults as $key => $value ) {
        if ( false === get_option( $key ) ) {
            update_option( $key, $value );
        }
    }
});

/**
 * ─── LOAD ADMIN ───
 */
if ( is_admin() ) {
    require_once IMM_PLUGIN_DIR . 'admin/admin-settings.php';
}

/**
 * ─── LOAD WALKER ───
 */
require_once IMM_PLUGIN_DIR . 'includes/class-mega-menu-walker.php';

/**
 * ─── ENQUEUE FRONT-END ASSETS ───
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( get_option( 'imm_enabled' ) !== '1' ) {
        return;
    }
    wp_enqueue_style( 'imm-mega-menu', IMM_PLUGIN_URL . 'assets/css/mega-menu.css', array(), IMM_VERSION );
    wp_enqueue_script( 'imm-mega-menu', IMM_PLUGIN_URL . 'assets/js/mega-menu.js', array(), IMM_VERSION, true );

    // Pass dynamic CSS variables from admin options
    $custom_css = imm_build_dynamic_css();
    wp_add_inline_style( 'imm-mega-menu', $custom_css );

    // Pass JS config
    wp_localize_script( 'imm-mega-menu', 'immConfig', array(
        'animation'      => get_option( 'imm_animation', 'fade' ),
        'animationSpeed' => intval( get_option( 'imm_animation_speed', 300 ) ),
    ));
});

/**
 * Build dynamic CSS from saved options.
 */
function imm_build_dynamic_css() {
    $bg          = sanitize_hex_color( get_option( 'imm_bg_color', '#ffffff' ) );
    $heading     = sanitize_hex_color( get_option( 'imm_heading_color', '#222222' ) );
    $link        = sanitize_hex_color( get_option( 'imm_link_color', '#555555' ) );
    $link_hover  = sanitize_hex_color( get_option( 'imm_link_hover_color', '#0073aa' ) );
    $separator   = sanitize_hex_color( get_option( 'imm_separator_color', '#e5e5e5' ) );
    $shadow      = get_option( 'imm_shadow', '1' ) === '1' ? '0 8px 30px rgba(0,0,0,.12)' : 'none';
    $max_width   = intval( get_option( 'imm_max_width', 1200 ) );
    $full_width  = get_option( 'imm_full_width', '1' );
    $heading_fs  = intval( get_option( 'imm_heading_font_size', 14 ) );
    $link_fs     = intval( get_option( 'imm_link_font_size', 13 ) );
    $col_pad     = intval( get_option( 'imm_column_padding', 20 ) );
    $radius      = intval( get_option( 'imm_border_radius', 4 ) );
    $columns     = intval( get_option( 'imm_columns', 5 ) );

    $width_rule = $full_width === '1' ? 'width:100%;' : "width:auto; max-width:{$max_width}px;";

    return "
    :root {
        --imm-bg: {$bg};
        --imm-heading: {$heading};
        --imm-link: {$link};
        --imm-link-hover: {$link_hover};
        --imm-separator: {$separator};
        --imm-shadow: {$shadow};
        --imm-heading-fs: {$heading_fs}px;
        --imm-link-fs: {$link_fs}px;
        --imm-col-pad: {$col_pad}px;
        --imm-radius: {$radius}px;
        --imm-columns: {$columns};
    }
    .imm-mega-dropdown { {$width_rule} }
    ";
}

/**
 * ─── OVERRIDE NAV MENU ARGS ───
 * Inject our custom walker into the chosen menu location.
 */
add_filter( 'wp_nav_menu_args', function ( $args ) {
    if ( get_option( 'imm_enabled' ) !== '1' ) {
        return $args;
    }

    $target_location = get_option( 'imm_menu_location', 'primary' );

    if ( isset( $args['theme_location'] ) && $args['theme_location'] === $target_location ) {
        $args['walker']     = new IMM_Mega_Menu_Walker();
        $args['container']  = 'nav';
        $args['container_class'] = 'imm-mega-nav';
        $args['menu_class'] = 'imm-mega-menu-list';
    }
    return $args;
});

/**
 * ─── ADD BODY CLASS ───
 */
add_filter( 'body_class', function ( $classes ) {
    if ( get_option( 'imm_enabled' ) === '1' ) {
        $classes[] = 'imm-mega-menu-active';
    }
    return $classes;
});