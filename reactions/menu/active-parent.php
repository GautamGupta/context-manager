<?php

add_filter( 'context_manager_reaction_menu_handlers', create_function( '$v', '$v[\'Context_Manager_Reaction_Menu_Handler_Active_Parent\'] = new Context_Manager_Reaction_Menu_Handler_Active_Parent(); return $v;' ) );

class Context_Manager_Reaction_Menu_Handler_Active_Parent extends Context_Manager_Reaction_Menu_Handler {

    function __construct() {
        $this->description = __( 'Emulate current page as a child but do not create a menu item.', 'context-manager' );
    }

    function handler( $data, &$menu_reaction ) {
        parent::handler( $data, $menu_reaction );
        add_filter( 'wp_nav_menu_objects', array( $this, 'active_parent' ) );
    }

    function active_parent( $menu_items ) {

        // Ancestory IDs
        $active_ancestor_item_ids = array();

        // Array keys of the menu items passed in are incremental so we need to traverse them to match the ID against the rule
        foreach ( $menu_items as $order => &$menu_item ) {

            // No context rule applied to this menu item
            if ( ! in_array( $menu_item->ID, $this->data[ $this->menu_reaction->field_prefix() . 'items' ] ) ) continue;

            // Copied from _wp_menu_item_classes_by_context() in wp-includes/nav-menu-template.php
            // Direct parent class
            if ( ! in_array( 'current-menu-parent', $menu_item->classes ) ) {
                $menu_item->classes[] = 'current-menu-parent';
                $menu_item->classes[] = 'current_page_parent';
                $menu_item->current_item_parent = true;

                $menu_item->classes[] = 'current-menu-ancestor';
                $menu_item->classes[] = 'current_page_ancestor';
                $menu_item->current_item_ancestor = true;
            }

            // Get ancestors
            $_anc_id = (int) $menu_item->db_id;
            while(
                ( $_anc_id = get_post_meta( $_anc_id, '_menu_item_menu_item_parent', true ) ) &&
                ! in_array( $_anc_id, $active_ancestor_item_ids )
            ) {
                $active_ancestor_item_ids[] = $_anc_id;
            }
        }

        // Loop through once more to setup all ancestor classes
        if ( ! empty( $active_ancestor_item_ids ) ) {
            $this->setup_ancestor_classes( $menu_items, $active_ancestor_item_ids );
        }

        return $menu_items;
    }

    protected function setup_ancestor_classes( &$menu_items, $active_ancestor_item_ids ) {
        $active_ancestor_item_ids = array_filter( array_unique( $active_ancestor_item_ids ) );
        foreach ( $menu_items as &$menu_item ) {
            if (
                in_array( intval( $menu_item->db_id ), $active_ancestor_item_ids ) &&
                ! in_array( 'current-menu-ancestor', $menu_item->classes )
            ) {
                $menu_item->classes[] = 'current-menu-ancestor';
                $menu_item->classes[] = 'current_page_ancestor';
                $menu_item->current_item_ancestor = true;
            }
        }
    }
}