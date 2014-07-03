<?php

add_filter( 'context_manager_reaction_menu_handlers', create_function( '$v', '$v[\'Context_Manager_Reaction_Menu_Handler_Hide_Item\'] = new Context_Manager_Reaction_Menu_Handler_Hide_Item(); return $v;' ) );

class Context_Manager_Reaction_Menu_Handler_Hide_Item extends Context_Manager_Reaction_Menu_Handler {

    function __construct() {
        $this->description = __( 'Hide menu item.', 'context-manager' );
    }

    function handler( $data, &$menu_reaction ) {
        parent::handler( $data, $menu_reaction );
        add_filter( 'wp_nav_menu_objects', array( $this, 'hide_item' ) );
    }

    function hide_item( $menu_items ) {

        // Store hidden items in an array
        $hidden_items = array();

        // Array keys of the menu items passed in are incremental so we need to traverse them to match the ID against the rule
        foreach ( $menu_items as $order => &$menu_item ) {

            // No context rule applied to this menu item
            if ( ! in_array( $menu_item->ID, $this->data[ $this->menu_reaction->field_prefix() . 'items' ] ) ) continue;

            // Remove if the condition evaluated to true
            unset( $menu_items[$order] );
            $hidden_items[] = $menu_item->ID;
        }

        return $menu_items;
    }
}
