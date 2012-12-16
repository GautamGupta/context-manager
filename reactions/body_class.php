<?php

// Add body class

class Context_Manager_Reaction_Body_Class extends Context_Manager_Reaction {

    function __construct( $plugin ) {
        add_filter( 'body_class', array( &$this, 'add_classes' ) );
        parent::__construct( $plugin, array( 'name' => 'Body Class' ) );
    }

    function form() {
        return array(
            'class' => array(
                'title' => __( 'Add this class to your theme&#39;s body tag:' , 'context-manager' ),
                'type' => 'text',
                'footer' => '
                    <p>' . sprintf( __( 'Your theme must be using %s.', 'context-manager' ), '<a href="http://codex.wordpress.org/Function_Reference/body_class" target="_blank"><code>body_class()</code></a>' ) . '</p>
                    <p>' . __( 'Separate multiple classes using spaces.', 'context-manager' ) . '</p>
                ',
            ),
        );
    }

    // On body_class
    function add_classes( $classes ) {

        if ( ! $context_rules = $this->get_rules() ) return $classes;
        foreach ( $context_rules as $context_rule ) {
            if ( ! $this->plugin->conditions_match( $context_rule ) ) continue;
            
            $classes[] = get_post_meta( $context_rule->ID, $this->field_prefix() . 'class' , true );
        }

        return $classes;
    }
}