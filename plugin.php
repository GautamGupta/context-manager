<?php
/*
Plugin Name: Context Manager
Description: Make your site react to users' context by changing your theme's CSS and JavaScript files, navigation menus, sidebars and the HTML body tag.
Author: Phill Brown
Author URI: http://pbweb.co.uk
Version: 1.2.0

Copyright 2012 Phill Brown (email: wp@pbweb.co.uk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Include PB Framework
require_once dirname( __FILE__ ) . '/libs/pb-framework/base.php';

class Context_Manager extends PB_Framework_Base {

    // On plugins_loaded
    function __construct() {

        // Default properties
        $this->post_type = 'context_rule';

        // Setup reactions
        require_once dirname( __FILE__ ) . '/reactions/base.php';
        foreach ( array( 'Menu', /*'Assets', */'Widgets', 'Body_Class' ) as $reaction ) {

            // Include reaction
            require_once dirname( __FILE__ ) . '/reactions/' . strtolower( $reaction ) . '.php';

            // Instantiate reactions
            $class_name = 'Context_Manager_Reaction_' . $reaction;
            $reactions[ $class_name ] = new $class_name( $this );
        }

        // Allow third-party plugins to add their own reactions using add_filter( 'menu_rules_reactions' )
        $this->reactions = $reactions;

        // Load admin
        if ( is_admin() ) {
            require_once dirname( __FILE__ ) . '/admin/admin.php';
            $admin = new Context_Manager_Admin( $this );
        }

        // Load common functionality
        add_action( 'init', array( &$this, 'init' ) );

        // Internationalise
        load_plugin_textdomain( 'menu_rules', false, basename( dirname( __FILE__ ) ) . '/languages' );

        // Meta box libraries must be included on plugins_loaded for scb_framework utilities
        require_once dirname( __FILE__ ) . '/admin/meta-box-conditions.php';
        require_once dirname( __FILE__ ) . '/admin/meta-box-reactions.php';

        parent::__construct();
    }

    // On init
    function init() {

        // Init meta box functionality
        $this->meta_boxes = array(
            'conditions' => new Context_Manager_Meta_Box_Conditions(),
            'reactions' => new Context_Manager_Meta_Box_Reactions( $this ),
        );

        // Setup the post type that stores context rules
        register_post_type( $this->post_type, array(
            'labels' => array(
                'name' => _x('Context Rules', 'post type general name', 'context-manager'),
                'singular_name' => _x('Context Rule', 'post type singular name', 'context-manager'),
                'add_new' => _x('Add New', 'Context Rule', 'context-manager'),
                'add_new_item' => __('Add New Context Rule', 'context-manager'),
                'edit_item' => __('Edit Context Rule', 'context-manager'),
                'new_item' => __('New Context Rule', 'context-manager'),
                'view_item' => __('View Context Rule', 'context-manager'),
                'search_items' => __('Search Context Rules', 'context-manager'),
                'not_found' =>  __('No Context Rules found', 'context-manager'),
                'not_found_in_trash' => __('No Context Rules found in Trash', 'context-manager'),
            ),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => 'themes.php',
            'capabilities' =>  array(
                'edit_posts' => 'edit_theme_options',
                'edit_others_posts' => 'edit_theme_options',
                'publish_posts' => 'edit_theme_options',
            ),
            'map_meta_cap' => true,
            'supports' => array( 'title' ),
        ) );
    }

    // Checking if conditions match
    function conditions_match( $context_rule ) {
        foreach ( $this->meta_boxes['conditions']->get_fields_flat() as $name => $field_def ) {
            $match = eval( 'return ' . get_post_meta( $context_rule->ID, $name, true ) . ';' );
        }

        return $match;
    }
}

// On plugins_loaded
new Context_Manager();