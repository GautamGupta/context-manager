<?php

require_once dirname( __FILE__ ) . '/../libs/pb-framework/meta-box2.php';
class Context_Manager_Meta_Box_Conditions extends PB_Meta_Box2 {

    // Setup meta box
    function __construct() {

        parent::__construct();

        // Condition fields
        $this->add_field_group( 'conditions', array(
            'exp' => array(
                'title' => __( 'When these conditions match:', 'context-manager' ),
                'type' => 'textarea',
				'extra' => array(
                    'class' => 'code',
                    'rows' => 5,
                    'cols' => 60,
                    'placeholder' => __( 'eg is_single()', 'context-manager' ),
                ),
                'description' => '
                    <p>' . sprintf( __( 'A full list of conditonal tags can be %sfound on the WordPress.org codex%s.', 'context-manager' ), '<a href="http://codex.wordpress.org/Conditional_Tags" target="_blank">', '</a>' ) . '</p>
                    <p>' . __( 'Do not include an if statement or a semicolon.', 'context-manager' ) . '</p>
                ',
                'footer' => '
                    <h4>' . __( 'Examples', 'context-manager' ) . '</h4>
                    <p>' . sprintf( __( '%sis_single()%s applies reactions when viewing a single post.', 'context-manager' ), '<code>', '</code>' ) . '</p>
                    <p>' . sprintf( __( '%sis_singular( \'product\' )%s applies reactions when viewing a single product.', 'context-manager' ), '<code>', '</code>' ) . '</p>
                    <p>' . sprintf( __( '%s( is_singular( \'book\' ) || is_singular( \'journal\' ) ) && has_tag( \'fiction\' )%s applies reactions when showing a single book or journal which is tagged as fiction', 'context-manager' ), '<code>', '</code>' ) . '</p>
                    <p>' . sprintf( __( '%sis_user_logged_in()%s applies reactions if the current user is logged in.', 'context-manager' ), '<code>', '</code>' ) . '</p>
                ',
            ),
        ) );
    }
}