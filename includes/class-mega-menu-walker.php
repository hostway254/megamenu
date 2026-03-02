<?php
/**
 * Custom Walker that renders mega-menu markup for designated top-level items.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IMM_Mega_Menu_Walker extends Walker_Nav_Menu {

    /**
     * IDs of top-level items that should render as mega menus.
     */
    private $mega_ids = array();

    /**
     * Track whether we are currently inside a mega item.
     */
    private $in_mega    = false;
    private $mega_depth = 0;

    public function __construct() {
        $saved = get_option( 'imm_mega_items', array() );
        $this->mega_ids = is_array( $saved ) ? array_map( 'intval', $saved ) : array();
    }

    /**
     * START LEVEL (<ul>)
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 && $this->in_mega ) {
            // Open mega dropdown and keep a valid list wrapper for child <li> items.
            $cols = intval( get_option( 'imm_columns', 5 ) );
            $output .= '<div class="imm-mega-dropdown" data-columns="' . $cols . '">';
            $output .= '<ul class="imm-mega-inner">';
            return;
        }

        // Inside mega → sub-lists become column lists
        if ( $this->in_mega ) {
            $indent  = str_repeat( "\t", $depth );
            $classes = array( 'imm-sub-list', 'imm-depth-' . $depth );
            $output .= "$indent<ul class=\"" . implode( ' ', $classes ) . "\">\n";
            return;
        }

        // Normal dropdown
        $indent  = str_repeat( "\t", $depth );
        $output .= "{$indent}<ul class=\"sub-menu\">\n";
    }

    /**
     * END LEVEL (</ul>)
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 && $this->in_mega ) {
            $output .= '</ul><!-- .imm-mega-inner -->';
            $output .= '</div><!-- .imm-mega-dropdown -->';
            return;
        }

        if ( $this->in_mega ) {
            $indent  = str_repeat( "\t", $depth );
            $output .= "$indent</ul>\n";
            return;
        }

        $indent  = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }

    /**
     * START ELEMENT (<li>)
     */
    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
        $item = $data_object;
        $indent = str_repeat( "\t", $depth );

        // Determine if this top-level item triggers mega
        if ( $depth === 0 ) {
            $this->in_mega = in_array( (int) $item->ID, $this->mega_ids, true );
        }

        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        if ( $depth === 0 && $this->in_mega ) {
            $classes[] = 'imm-has-mega';
        }

        if ( $this->in_mega && $depth === 1 ) {
            $classes[] = 'imm-mega-column';
        }

        if ( $this->in_mega && $depth >= 1 ) {
            $classes[] = 'imm-mega-item';
        }

        $class_names = implode( ' ', array_filter( $classes ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = ' id="menu-item-' . esc_attr( $item->ID ) . '"';

        $output .= $indent . '<li' . $id . $class_names . '>';

        // Build the <a>
        $atts = array(
            'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
            'target' => ! empty( $item->target ) ? $item->target : '',
            'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
            'href'   => ! empty( $item->url ) ? $item->url : '',
        );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $attributes .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
            }
        }

        // Headings for depth-1 items inside mega
        $link_class = '';
        if ( $this->in_mega && $depth === 1 ) {
            $link_class = ' class="imm-mega-heading"';
        } elseif ( $this->in_mega && $depth >= 2 ) {
            $link_class = ' class="imm-mega-link"';
        }

        $item_output  = isset( $args->before ) ? $args->before : '';
        $item_output .= '<a' . $attributes . $link_class . '>';
        $item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( isset( $args->link_after ) ? $args->link_after : '' );
        $item_output .= '</a>';
        $item_output .= isset( $args->after ) ? $args->after : '';

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * END ELEMENT (</li>)
     */
    public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
        $output .= "</li>\n";

        if ( $depth === 0 ) {
            $this->in_mega = false;
        }
    }
}