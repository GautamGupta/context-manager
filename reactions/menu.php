<?php

class Context_Manager_Reaction_Menu extends Context_Manager_Reaction {

    function __construct( $plugin ) {

        // Menu reaction handlers
        foreach ( array( 'active-parent', 'inactive-parent', 'child-page' ) as $filename ) {
            require_once dirname( __FILE__ ) . '/menu/' . $filename . '.php';
        }

        // Where the magic happens
        add_action( 'wp', array( &$this, 'run' ) );

        parent::__construct( $plugin, array( 'name' => 'Menu' ) );
    }

    function form() {

        // Check if there are navigation menus defined
        $nav_menus = wp_get_nav_menus( array('orderby' => 'name') );
        if ( ! $nav_menus ) {
            $this->error = 'no_nav';
            return;
        }

        $nav_menu_dropdown_values = array();
        foreach ( $nav_menus as $nav_menu_obj ) {

            // Check the menu isn't empty
            if ( ! $nav_menu_items = wp_get_nav_menu_items( $nav_menu_obj->term_id ) ) continue;

            $nav_menu_dropdown_values[$nav_menu_obj->name] = array_combine(
                array_map( create_function( '$v', 'return $v->ID;' ), $nav_menu_items ),
                array_map( create_function( '$v', 'return empty( $v->menu_item_parent ) ? $v->title : "-- " . $v->title;' ), $nav_menu_items )
            );
        }

        // Menus are setup but don't contain any items
        if ( ! $nav_menu_dropdown_values ) {
            $this->error = 'no_items';
            return;
        }

        // No menu reaction rules enabled
        if ( ! $this->handlers ) {
            $this->error = 'no_handlers';
            return;
        }

        // Return fields
        return array(
            // Action
            'rules' => array(
                'title' => __( 'Apply this rule:' , 'context-manager' ),
                'type' => 'select',
                'value' => array_combine(
                    array_keys( $this->handlers ),
                    array_map( create_function( '$v', 'return $v->description;' ), $this->handlers )
                ),
            ),
            // Nav menus
            'items' => array(
                'title' => __( 'To these menu items:' , 'context-manager' ),
                'type' => 'select',
                'value' => $nav_menu_dropdown_values,
                'multiple' => true,
                'text' => __( 'Select menu items' , 'context-manager' ),
                'extra' => array(
                    'required' => 'required',
                ),
            ),
        );
    }

    // The footer runs after form() so use it to display errors
    function display_footer() {

        if ( ! $this->error ) return;

        // User need to create a menu
        if ( $this->error == 'no_nav' ) {
            return '<p class="error-message">' . sprintf( __( 'You haven\'t setup any WordPress custom menus. %sAdd some here%s', 'context-manager' ), '<a href="' . admin_url( 'nav-menus.php' ) . '">', '</a>' ) . '</p>';
        }

        // User needs to add menu items
        if ( $this->error == 'no_items' ) {
            return '<p class="error-message">' . sprintf( __( 'You haven\'t added any items to your WordPress custom menus. %sAdd some here%s', 'context-manager' ), '<a href="' . admin_url( 'nav-menus.php' ) . '">', '</a>' ) . '</p>';
        }

        // Site admin needs to enable handlers
        if ( $this->error == 'no_handlers' ) {
            return '<p class="error-message">' . __( 'No menu rules available. Contact your site administrator.', 'context-manager' ) . '</p>';
        }
    }

    // On wp
    function run() {
        if ( ! $context_rules = $this->get_rules() ) return;
        foreach ( $context_rules as $context_rule ) {

            if ( ! $this->plugin->conditions_match( $context_rule ) ) continue;

            // Apply rules
            $context_rule_data = get_post_custom( $context_rule->ID );
            foreach( $context_rule_data[ $this->field_prefix() . 'rules' ] as $applied_handler ) {

                // Check the rules handler still exists
                if ( ! $this->handlers[ $applied_handler ] ) continue;

                // Let the menu handler do the magic
                call_user_func_array( array( $this->handlers[ $applied_handler ], 'handler' ), array( $context_rule_data, &$this ) );
            }
        }
    }
}


// Base class for rules handlers
abstract class Context_Manager_Reaction_Menu_Handler extends PB_Framework_Base {

    // Context rule data
    protected $data;

    function handler( $data, &$menu_reaction ) {
        $this->data = $data;
        $this->menu_reaction = $menu_reaction;
    }
}