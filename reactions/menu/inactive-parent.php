<?php

add_filter( 'context_manager_reaction_menu_handlers', create_function( '$v', '$v[\'Context_Manager_Reaction_Menu_Handler_Inactive_Parent\'] = new Context_Manager_Reaction_Menu_Handler_Inactive_Parent(); return $v;' ) );

class Context_Manager_Reaction_Menu_Handler_Inactive_Parent extends Context_Manager_Reaction_Menu_Handler {
    function __construct() {
        $this->description = __('Remove all active states.', 'context-manager');
    }

    function handler( $data, &$menu_reaction ) {
        parent::handler( $data, $menu_reaction );
        add_filter( 'wp_nav_menu_objects', array( $this, 'run' ) );
    }

    function run( $menu_items ) {

        foreach ( $menu_items as $order => &$menu_item ) {

            // No context rule applied to this menu item
            if ( ! in_array( $menu_item->ID, $this->data[ $this->menu_reaction->field_prefix() . 'items' ] ) ) continue;

            $classes_to_remove = array();
            if ( $classes_to_remove[] = array_search( 'current-menu-parent', $menu_item->classes ) || $classes_to_remove[] = array_search( 'current_page_parent', $menu_item->classes ) ) {

                // Search for other ancestor classes
                $classes_to_remove[] = array_search( 'current-menu-ancestor', $menu_item->classes );
                $classes_to_remove[] = array_search( 'current_page_ancestor', $menu_item->classes );

                foreach ( $classes_to_remove as $class_to_remove ) {
                    if ( is_int( $class_to_remove ) ) unset( $menu_item->classes[$class_to_remove] );
                }

                $menu_item->current_item_parent = false;
                $menu_item->current_item_ancestor = false;
            }
        }

        return $menu_items;
    }
}