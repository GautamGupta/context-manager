<?php

// Add widgets to sidebars based on context

class Context_Manager_Reaction_Widgets extends Context_Manager_Reaction {

    function __construct( $plugin ) {
        add_action( 'wp', array( &$this, 'load' ) );

        parent::__construct( $plugin, array( 'name' => 'Widgets' ) );
    }

    function form( $preprocess = true ) {

        // Hack to stop wp_get_sidebars_widgets() getting in an infinite loop
        if ( $preprocess ) {
            // Map widgets and sidebars
            foreach( wp_get_sidebars_widgets() as $sidebar_id => $sidebar_widgets ) {

                // Skip sidebars with no active widgets or are no longer registered
                if (
                    ! $sidebar_widgets
                    || $sidebar_id == 'wp_inactive_widgets'
                    || substr( $sidebar_id, 0, 16 ) == 'orphaned_widgets'
                    || ! isset( $GLOBALS['wp_registered_sidebars'][ $sidebar_id ] )
                    || ! isset( $widgets[ $GLOBALS['wp_registered_sidebars'][ $sidebar_id ]['name'] ] )
                 ) continue;

                $widgets[ $GLOBALS['wp_registered_sidebars'][ $sidebar_id ]['name'] ] = array_combine(
                    $sidebar_widgets,
                    array_map(
                        create_function( '$v', 'return $GLOBALS[\'wp_registered_widgets\'][$v][\'name\'];' ),
                        $sidebar_widgets
                    )
                );
            }
        }
        return array(
            // Nav menus
            'widgets' => array(
                'title' => __( 'Hide the following widgets:' , 'context-manager' ),
                'type' => 'select',
                'value' => ! empty( $widgets ) ? $widgets : array(),
                'multiple' => true,
                'text' => __( 'Choose widgets' , 'context-manager' ),
                'extra' => array(
                    'class' => 'menu-rules-items-select',
                ),
            ),
        );
    }

    // On wp
    function load() {
        add_filter( 'sidebars_widgets', array( &$this, 'hide_widgets' ) );
    }

    // On sidebars_widgets
    function hide_widgets( $sidebars_widgets ) {

        if ( ! $context_rules = $this->get_rules() ) return $sidebars_widgets;
        foreach ( $context_rules as $context_rule ) {
            if ( ! $this->plugin->conditions_match( $context_rule ) ) continue;

            // Checks complete
            foreach ( $sidebars_widgets as $sidebar_id => $widgets_in_sidebar ) {
                foreach ( get_post_meta( $context_rule->ID, $this->field_prefix() . 'widgets' ) as $widget_to_hide ) { 
                    if ( $widget_id = array_search( $widget_to_hide, $widgets_in_sidebar ) ) unset( $sidebars_widgets[ $sidebar_id ][ $widget_id ] );
                }
            }

        }
        return $sidebars_widgets;
    }
}