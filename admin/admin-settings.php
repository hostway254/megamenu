<?php
/**
 * Admin settings page: Appearance → Mega Menu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ─── REGISTER ADMIN MENU ───
 */
add_action( 'admin_menu', function () {
    add_theme_page(
        __( 'Mega Menu Settings', 'ideal-mega-menu' ),
        __( 'Mega Menu', 'ideal-mega-menu' ),
        'manage_options',
        'imm-mega-menu',
        'imm_render_admin_page'
    );
});

/**
 * ─── ENQUEUE ADMIN ASSETS ───
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook !== 'appearance_page_imm-mega-menu' ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_style( 'imm-admin-css', IMM_PLUGIN_URL . 'assets/css/admin.css', array(), IMM_VERSION );
    wp_enqueue_script( 'imm-admin-js', IMM_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), IMM_VERSION, true );
});

/**
 * ─── REGISTER SETTINGS ───
 */
add_action( 'admin_init', function () {
    $fields = array(
        'imm_enabled',
        'imm_menu_location',
        'imm_mega_items',
        'imm_columns',
        'imm_bg_color',
        'imm_heading_color',
        'imm_link_color',
        'imm_link_hover_color',
        'imm_separator_color',
        'imm_shadow',
        'imm_animation',
        'imm_animation_speed',
        'imm_full_width',
        'imm_max_width',
        'imm_heading_font_size',
        'imm_link_font_size',
        'imm_column_padding',
        'imm_border_radius',
    );
    foreach ( $fields as $field ) {
        register_setting( 'imm_settings_group', $field );
    }
});

/**
 * Get all registered nav menu locations & their assigned menus.
 */
function imm_get_menu_locations() {
    $locations = get_registered_nav_menus();
    $assigned  = get_nav_menu_locations();
    $result    = array();
    foreach ( $locations as $slug => $label ) {
        $result[ $slug ] = $label;
    }
    return $result;
}

/**
 * Get top-level items from a specific menu location.
 */
function imm_get_toplevel_menu_items( $location = '' ) {
    if ( empty( $location ) ) {
        $location = get_option( 'imm_menu_location', 'primary' );
    }
    $locations = get_nav_menu_locations();
    if ( ! isset( $locations[ $location ] ) ) {
        return array();
    }
    $menu_id = $locations[ $location ];
    $items   = wp_get_nav_menu_items( $menu_id );
    if ( ! $items ) {
        return array();
    }
    $top = array();
    foreach ( $items as $item ) {
        if ( (int) $item->menu_item_parent === 0 ) {
            $top[] = $item;
        }
    }
    return $top;
}

/**
 * ─── RENDER ADMIN PAGE ───
 */
function imm_render_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $locations     = imm_get_menu_locations();
    $cur_location  = get_option( 'imm_menu_location', 'primary' );
    $top_items     = imm_get_toplevel_menu_items( $cur_location );
    $mega_items    = get_option( 'imm_mega_items', array() );
    if ( ! is_array( $mega_items ) ) {
        $mega_items = array();
    }
    ?>
    <div class="wrap imm-admin-wrap">
        <h1><span class="dashicons dashicons-screenoptions" style="font-size:28px;margin-right:8px;"></span> <?php _e( 'Mega Menu Settings', 'ideal-mega-menu' ); ?></h1>
        <p class="description"><?php _e( 'Configure which menu items display as a mega dropdown and customise their appearance.', 'ideal-mega-menu' ); ?></p>

        <form method="post" action="options.php" id="imm-settings-form">
            <?php settings_fields( 'imm_settings_group' ); ?>

            <!-- ═══ TAB NAV ═══ -->
            <nav class="nav-tab-wrapper imm-tabs">
                <a href="#imm-tab-general" class="nav-tab nav-tab-active"><?php _e( 'General', 'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-menus" class="nav-tab"><?php _e( 'Menu Items', 'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-appearance" class="nav-tab"><?php _e( 'Appearance', 'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-layout" class="nav-tab"><?php _e( 'Layout', 'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-animation" class="nav-tab"><?php _e( 'Animation', 'ideal-mega-menu' ); ?></a>
            </nav>

            <!-- ═══ TAB: GENERAL ═══ -->
            <div id="imm-tab-general" class="imm-tab-content imm-tab-active">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Enable Mega Menu', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_enabled" value="1" <?php checked( get_option( 'imm_enabled' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                            <p class="description"><?php _e( 'Turn the mega menu on or off globally.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Menu Location', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <select name="imm_menu_location" id="imm_menu_location">
                                <?php foreach ( $locations as $slug => $label ) : ?>
                                    <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $cur_location, $slug ); ?>>
                                        <?php echo esc_html( $label ); ?> (<?php echo esc_html( $slug ); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e( 'Select which theme menu location should use the mega menu.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ═══ TAB: MENU ITEMS ═══ -->
            <div id="imm-tab-menus" class="imm-tab-content">
                <p class="description" style="margin-bottom:15px;">
                    <?php _e( 'Check the top-level menu items that should display their children as a mega dropdown. All other items will use the standard dropdown.', 'ideal-mega-menu' ); ?>
                </p>
                <?php if ( empty( $top_items ) ) : ?>
                    <div class="notice notice-warning inline">
                        <p><?php _e( 'No menu items found. Please assign a menu to the selected location in Appearance → Menus.', 'ideal-mega-menu' ); ?></p>
                    </div>
                <?php else : ?>
                    <table class="widefat fixed striped imm-items-table">
                        <thead>
                            <tr>
                                <th style="width:50px;"><?php _e( 'Mega', 'ideal-mega-menu' ); ?></th>
                                <th><?php _e( 'Menu Item', 'ideal-mega-menu' ); ?></th>
                                <th><?php _e( 'ID', 'ideal-mega-menu' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $top_items as $item ) : ?>
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               name="imm_mega_items[]"
                                               value="<?php echo esc_attr( $item->ID ); ?>"
                                               <?php checked( in_array( (int) $item->ID, array_map( 'intval', $mega_items ) ) ); ?>>
                                    </td>
                                    <td><strong><?php echo esc_html( $item->title ); ?></strong></td>
                                    <td><code><?php echo esc_html( $item->ID ); ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- ═══ TAB: APPEARANCE ═══ -->
            <div id="imm-tab-appearance" class="imm-tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Background Color', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_bg_color" value="<?php echo esc_attr( get_option( 'imm_bg_color', '#ffffff' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Heading Color', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_heading_color" value="<?php echo esc_attr( get_option( 'imm_heading_color', '#222222' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Link Color', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_link_color" value="<?php echo esc_attr( get_option( 'imm_link_color', '#555555' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Link Hover Color', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_link_hover_color" value="<?php echo esc_attr( get_option( 'imm_link_hover_color', '#0073aa' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Column Separator Color', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_separator_color" value="<?php echo esc_attr( get_option( 'imm_separator_color', '#e5e5e5' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Heading Font Size (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_heading_font_size" value="<?php echo esc_attr( get_option( 'imm_heading_font_size', 14 ) ); ?>" min="10" max="24" step="1"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Link Font Size (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_link_font_size" value="<?php echo esc_attr( get_option( 'imm_link_font_size', 13 ) ); ?>" min="10" max="20" step="1"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Drop Shadow', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_shadow" value="1" <?php checked( get_option( 'imm_shadow' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ═══ TAB: LAYOUT ═══ -->
            <div id="imm-tab-layout" class="imm-tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Number of Columns', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <input type="number" name="imm_columns" value="<?php echo esc_attr( get_option( 'imm_columns', 5 ) ); ?>" min="2" max="6" step="1">
                            <p class="description"><?php _e( 'Sub-items (depth-1) will wrap into this many columns.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Full Width', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_full_width" value="1" <?php checked( get_option( 'imm_full_width' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                            <p class="description"><?php _e( 'Stretch the mega dropdown to full viewport width.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Max Width (px)', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <input type="number" name="imm_max_width" value="<?php echo esc_attr( get_option( 'imm_max_width', 1200 ) ); ?>" min="600" max="1920" step="10">
                            <p class="description"><?php _e( 'Only applies when Full Width is off.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Column Padding (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_column_padding" value="<?php echo esc_attr( get_option( 'imm_column_padding', 20 ) ); ?>" min="5" max="50" step="1"></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Border Radius (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_border_radius" value="<?php echo esc_attr( get_option( 'imm_border_radius', 4 ) ); ?>" min="0" max="20" step="1"></td>
                    </tr>
                </table>
            </div>

            <!-- ═══ TAB: ANIMATION ═══ -->
            <div id="imm-tab-animation" class="imm-tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'Animation Type', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <select name="imm_animation">
                                <option value="none"  <?php selected( get_option( 'imm_animation' ), 'none' ); ?>>None</option>
                                <option value="fade"  <?php selected( get_option( 'imm_animation' ), 'fade' ); ?>>Fade In</option>
                                <option value="slide" <?php selected( get_option( 'imm_animation' ), 'slide' ); ?>>Slide Down</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e( 'Animation Speed (ms)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_animation_speed" value="<?php echo esc_attr( get_option( 'imm_animation_speed', 300 ) ); ?>" min="50" max="1000" step="50"></td>
                    </tr>
                </table>
            </div>

            <?php submit_button( __( 'Save Settings', 'ideal-mega-menu' ) ); ?>
        </form>
    </div>
    <?php
}