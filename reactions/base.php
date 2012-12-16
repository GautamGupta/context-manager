<?php

abstract class Context_Manager_Reaction extends PB_Framework_Base {

    // Setup all hooks in the constructor.
    // Usually there'll be other methods in the class that hook to actions and filters
    // @param main plugin object
    // On plugins_loaded
    function __construct( $plugin, $args ) {
        $this->plugin = $plugin;
        $this->name = ! empty( $args['name'] ) ? $args['name'] : get_class( $this );
    }

    // Display the form in the reactions meta box
    abstract function form();

    // Extra display hooks above and below the main form. Needs to return output, not echo
    function display_header() {}
    function display_footer() {}

    // Get context rules with reaction applied
    protected function get_rules() {

        // No form registered
        if ( ! $this->form( false ) ) return;

        // Setup meta query conditions
        $meta_queries = array();
        foreach( $this->form( false ) as $field_name => $field_data ) {
            $meta_queries[] = array(
                'key' => $this->field_prefix() . $field_name,
                'value' => '',
                'compare' => '!=',
            );
        }
        
        // Get context rules
        return get_posts( array(
            'post_type' => $this->plugin->post_type,
            'numberposts' => -1,
            'meta_query' => array_merge( array( 'relation' => 'OR' ), $meta_queries ),
        ) );
    }

    function field_prefix() {
        return strtolower( get_class( $this ) ) . '_';
    }
}