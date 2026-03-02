<?php
/**
 * Admin settings page: Appearance → Mega Menu  (v2 – ninja-254)
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ─── REGISTER MENU PAGE ─── */
add_action( 'admin_menu', function () {
    add_theme_page(
        __( 'Mega Menu Settings', 'ideal-mega-menu' ),
        __( 'Mega Menu', 'ideal-mega-menu' ),
        'manage_options',
        'imm-mega-menu',
        'imm_render_admin_page'
    );
} );

/* ─── ENQUEUE ADMIN ASSETS ─── */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook !== 'appearance_page_imm-mega-menu' ) { return; }
    wp_enqueue_style(  'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_style(  'imm-admin-css', IMM_PLUGIN_URL . 'assets/css/admin.css', array(), IMM_VERSION );
    wp_enqueue_script( 'imm-admin-js',  IMM_PLUGIN_URL . 'assets/js/admin.js',  array( 'jquery', 'wp-color-picker' ), IMM_VERSION, true );
} );

/* ─── REGISTER SETTINGS ─── */
add_action( 'admin_init', function () {
    $fields = array(
        'imm_enabled', 'imm_menu_location', 'imm_mega_items',
        'imm_max_rows_per_col',
        'imm_bg_color', 'imm_accent_color', 'imm_navbar_bg',
        'imm_toplevel_color', 'imm_toplevel_hover',
        'imm_heading_color', 'imm_link_color', 'imm_link_hover_color',
        'imm_separator_color', 'imm_footer_bg', 'imm_footer_link_color',
        'imm_shadow', 'imm_show_header_strip', 'imm_show_footer_strip',
        'imm_animation', 'imm_animation_speed',
        'imm_full_width', 'imm_max_width',
        'imm_heading_font_size', 'imm_link_font_size',
        'imm_column_padding', 'imm_border_radius',
        'imm_item_subtitles', 'imm_footer_links',
    );
    foreach ( $fields as $f ) {
        register_setting( 'imm_settings_group', $f );
    }
} );

/* ─── HELPERS ─── */
function imm_get_menu_locations() {
    $result = array();
    foreach ( get_registered_nav_menus() as $slug => $label ) {
        $result[ $slug ] = $label;
    }
    return $result;
}

function imm_get_toplevel_menu_items( $location = '' ) {
    if ( empty( $location ) ) { $location = get_option( 'imm_menu_location', 'primary' ); }
    $locs    = get_nav_menu_locations();
    if ( ! isset( $locs[ $location ] ) ) { return array(); }
    $items   = wp_get_nav_menu_items( $locs[ $location ] );
    if ( ! $items ) { return array(); }
    $top = array();
    foreach ( $items as $item ) {
        if ( (int) $item->menu_item_parent === 0 ) { $top[] = $item; }
    }
    return $top;
}

/* ─── RENDER PAGE ─── */
function imm_render_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }

    $locations    = imm_get_menu_locations();
    $cur_loc      = get_option( 'imm_menu_location', 'primary' );
    $top_items    = imm_get_toplevel_menu_items( $cur_loc );
    $mega_items   = (array) get_option( 'imm_mega_items', array() );
    $subtitles    = (array) get_option( 'imm_item_subtitles', array() );
    $footer_links = get_option( 'imm_footer_links', array() );
    if ( ! is_array( $footer_links ) ) { $footer_links = array(); }
    ?>
    <div class="wrap imm-admin-wrap">
        <h1>
            <span class="dashicons dashicons-screenoptions" style="font-size:26px;color:#E8500A;"></span>
            <?php _e( 'Mega Menu Settings', 'ideal-mega-menu' ); ?>
            <span style="font-size:12px;color:#999;font-weight:400;margin-left:8px;">v2.0 – ninja-254</span>
        </h1>
        <p class="description"><?php _e( 'Configure mega menus, colours, footer links and layout.', 'ideal-mega-menu' ); ?></p>

        <form method="post" action="options.php" id="imm-settings-form">
            <?php settings_fields( 'imm_settings_group' ); ?>

            <!-- TAB NAV -->
            <nav class="nav-tab-wrapper imm-tabs">
                <a href="#imm-tab-general"    class="nav-tab nav-tab-active"><?php _e( 'General',       'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-menus"      class="nav-tab"><?php _e( 'Menu Items',    'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-footer"     class="nav-tab"><?php _e( 'Footer Links',  'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-appearance" class="nav-tab"><?php _e( 'Appearance',    'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-layout"     class="nav-tab"><?php _e( 'Layout',        'ideal-mega-menu' ); ?></a>
                <a href="#imm-tab-animation"  class="nav-tab"><?php _e( 'Animation',     'ideal-mega-menu' ); ?></a>
            </nav>

            <!-- ══ TAB: GENERAL ══ -->
            <div id="imm-tab-general" class="imm-tab-content imm-tab-active">
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Enable Mega Menu', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_enabled" value="1" <?php checked( get_option( 'imm_enabled' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                            <p class="description"><?php _e( 'Turn the mega menu on / off globally.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Menu Location', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <select name="imm_menu_location" id="imm_menu_location">
                                <?php foreach ( $locations as $slug => $label ) : ?>
                                    <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $cur_loc, $slug ); ?>>
                                        <?php echo esc_html( $label ); ?> (<?php echo esc_html( $slug ); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Show Header Strip', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_show_header_strip" value="1" <?php checked( get_option( 'imm_show_header_strip', '1' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                            <p class="description"><?php _e( 'Show the title + subtitle strip at the top of every mega panel.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Show Footer Strip', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_show_footer_strip" value="1" <?php checked( get_option( 'imm_show_footer_strip', '1' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                            <p class="description"><?php _e( 'Show the quick-links footer strip at the bottom of every mega panel.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ══ TAB: MENU ITEMS ══ -->
            <div id="imm-tab-menus" class="imm-tab-content">
                <p class="description" style="margin-bottom:14px;">
                    <?php _e( 'Tick the top-level items that should display as a mega dropdown. Add an optional subtitle shown in the header strip.', 'ideal-mega-menu' ); ?>
                </p>
                <?php if ( empty( $top_items ) ) : ?>
                    <div class="notice notice-warning inline"><p><?php _e( 'No menu items found. Assign a menu to the selected location first.', 'ideal-mega-menu' ); ?></p></div>
                <?php else : ?>
                    <table class="widefat fixed striped imm-items-table">
                        <thead>
                            <tr>
                                <th style="width:50px;"><?php _e( 'Mega', 'ideal-mega-menu' ); ?></th>
                                <th><?php _e( 'Menu Item', 'ideal-mega-menu' ); ?></th>
                                <th><?php _e( 'Subtitle / Description (shown in header strip)', 'ideal-mega-menu' ); ?></th>
                                <th style="width:60px;"><?php _e( 'ID', 'ideal-mega-menu' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $top_items as $item ) :
                                $checked  = in_array( (int) $item->ID, array_map( 'intval', $mega_items ) );
                                $subtitle = isset( $subtitles[ $item->ID ] ) ? esc_attr( $subtitles[ $item->ID ] ) : '';
                            ?>
                            <tr>
                                <td><input type="checkbox" name="imm_mega_items[]" value="<?php echo esc_attr( $item->ID ); ?>" <?php checked( $checked ); ?>></td>
                                <td><strong><?php echo esc_html( $item->title ); ?></strong></td>
                                <td>
                                    <textarea
                                        class="imm-subtitle-field"
                                        rows="2"
                                        data-item-id="<?php echo esc_attr( $item->ID ); ?>"
                                        name="imm_item_subtitles[<?php echo esc_attr( $item->ID ); ?>]"
                                        placeholder="e.g. Browse our full range of containers, storage solutions and accessories"
                                    ><?php echo $subtitle; ?></textarea>
                                </td>
                                <td><code><?php echo esc_html( $item->ID ); ?></code></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- ══ TAB: FOOTER LINKS ══ -->
            <div id="imm-tab-footer" class="imm-tab-content">
                <p class="description" style="margin-bottom:14px;">
                    <?php _e( 'These links appear as buttons in the footer strip of every mega panel. Add, remove or reorder as needed.', 'ideal-mega-menu' ); ?>
                </p>
                <table class="imm-footer-links-table widefat">
                    <thead>
                        <tr>
                            <th><?php _e( 'Label', 'ideal-mega-menu' ); ?></th>
                            <th><?php _e( 'URL', 'ideal-mega-menu' ); ?></th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="imm-footer-links-tbody">
                        <?php foreach ( $footer_links as $idx => $fl ) :
                            $label = isset( $fl['label'] ) ? esc_attr( $fl['label'] ) : '';
                            $url   = isset( $fl['url'] )   ? esc_attr( $fl['url'] )   : '';
                        ?>
                        <tr>
                            <td><input type="text" name="imm_footer_links[<?php echo $idx; ?>][label]" value="<?php echo $label; ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. View All Products', 'ideal-mega-menu' ); ?>"></td>
                            <td><input type="text" name="imm_footer_links[<?php echo $idx; ?>][url]"   value="<?php echo $url; ?>"   class="regular-text" placeholder="https://"></td>
                            <td><button type="button" class="imm-footer-link-remove" title="Remove row">&times;</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" id="imm-add-footer-link">+ <?php _e( 'Add Footer Link', 'ideal-mega-menu' ); ?></button>
            </div>

            <!-- ══ TAB: APPEARANCE ══ -->
            <div id="imm-tab-appearance" class="imm-tab-content">
                <p class="imm-section-heading"><?php _e( 'Brand & Accent', 'ideal-mega-menu' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Accent / Brand Colour', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_accent_color" value="<?php echo esc_attr( get_option( 'imm_accent_color', '#E8500A' ) ); ?>" class="imm-color-picker">
                        <p class="description"><?php _e( 'Used for headings underline, footer buttons border, active states. Default: #E8500A', 'ideal-mega-menu' ); ?></p></td>
                    </tr>
                </table>

                <p class="imm-section-heading"><?php _e( 'Navbar', 'ideal-mega-menu' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Navbar Background', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_navbar_bg" value="<?php echo esc_attr( get_option( 'imm_navbar_bg', '#ffffff' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Top-Level Link Colour', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_toplevel_color" value="<?php echo esc_attr( get_option( 'imm_toplevel_color', '#222222' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Top-Level Link Hover Colour', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_toplevel_hover" value="<?php echo esc_attr( get_option( 'imm_toplevel_hover', '#E8500A' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                </table>

                <p class="imm-section-heading"><?php _e( 'Mega Panel', 'ideal-mega-menu' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Panel Background', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_bg_color" value="<?php echo esc_attr( get_option( 'imm_bg_color', '#ffffff' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Column Heading Colour', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_heading_color" value="<?php echo esc_attr( get_option( 'imm_heading_color', '#222222' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Link Colour', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_link_color" value="<?php echo esc_attr( get_option( 'imm_link_color', '#555555' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Link Hover Colour', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_link_hover_color" value="<?php echo esc_attr( get_option( 'imm_link_hover_color', '#E8500A' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Column Separator', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_separator_color" value="<?php echo esc_attr( get_option( 'imm_separator_color', '#e5e5e5' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Drop Shadow', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_shadow" value="1" <?php checked( get_option( 'imm_shadow', '1' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                        </td>
                    </tr>
                </table>

                <p class="imm-section-heading"><?php _e( 'Footer Strip', 'ideal-mega-menu' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Footer Strip Background', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_footer_bg" value="<?php echo esc_attr( get_option( 'imm_footer_bg', '#f5f5f5' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Footer Link Colour', 'ideal-mega-menu' ); ?></th>
                        <td><input type="text" name="imm_footer_link_color" value="<?php echo esc_attr( get_option( 'imm_footer_link_color', '#E8500A' ) ); ?>" class="imm-color-picker"></td>
                    </tr>
                </table>

                <p class="imm-section-heading"><?php _e( 'Typography', 'ideal-mega-menu' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Column Heading Font Size (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_heading_font_size" value="<?php echo esc_attr( get_option( 'imm_heading_font_size', 13 ) ); ?>" min="10" max="22" step="1"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Link Font Size (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_link_font_size" value="<?php echo esc_attr( get_option( 'imm_link_font_size', 12 ) ); ?>" min="9" max="18" step="1"></td>
                    </tr>
                </table>
            </div>

            <!-- ══ TAB: LAYOUT ══ -->
            <div id="imm-tab-layout" class="imm-tab-content">
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Max Items Per Column', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <input type="number" name="imm_max_rows_per_col" value="<?php echo esc_attr( get_option( 'imm_max_rows_per_col', 12 ) ); ?>" min="4" max="30" step="1">
                            <p class="description"><?php _e( 'When a Level-1 group + its children would exceed this row count, a new column is started. Smaller = more columns; larger = fewer, taller columns.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Full Width Panel', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <label class="imm-toggle">
                                <input type="checkbox" name="imm_full_width" value="1" <?php checked( get_option( 'imm_full_width', '1' ), '1' ); ?>>
                                <span class="imm-toggle-slider"></span>
                            </label>
                            <p class="description"><?php _e( 'Stretch mega panel to full viewport width.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Max Width (px)', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <input type="number" name="imm_max_width" value="<?php echo esc_attr( get_option( 'imm_max_width', 1200 ) ); ?>" min="600" max="1920" step="10">
                            <p class="description"><?php _e( 'Only applies when Full Width is off.', 'ideal-mega-menu' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Column Padding (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_column_padding" value="<?php echo esc_attr( get_option( 'imm_column_padding', 20 ) ); ?>" min="5" max="50" step="1"></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Border Radius (px)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_border_radius" value="<?php echo esc_attr( get_option( 'imm_border_radius', 0 ) ); ?>" min="0" max="20" step="1"></td>
                    </tr>
                </table>
            </div>

            <!-- ══ TAB: ANIMATION ══ -->
            <div id="imm-tab-animation" class="imm-tab-content">
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'Animation Type', 'ideal-mega-menu' ); ?></th>
                        <td>
                            <select name="imm_animation">
                                <option value="none"  <?php selected( get_option( 'imm_animation' ), 'none' ); ?>><?php _e( 'None', 'ideal-mega-menu' ); ?></option>
                                <option value="fade"  <?php selected( get_option( 'imm_animation' ), 'fade' ); ?>><?php _e( 'Fade In', 'ideal-mega-menu' ); ?></option>
                                <option value="slide" <?php selected( get_option( 'imm_animation' ), 'slide' ); ?>><?php _e( 'Slide Down', 'ideal-mega-menu' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Animation Speed (ms)', 'ideal-mega-menu' ); ?></th>
                        <td><input type="number" name="imm_animation_speed" value="<?php echo esc_attr( get_option( 'imm_animation_speed', 300 ) ); ?>" min="50" max="1000" step="50"></td>
                    </tr>
                </table>
            </div>

            <?php submit_button( __( 'Save Settings', 'ideal-mega-menu' ) ); ?>
        </form>
    </div>
    <?php
}