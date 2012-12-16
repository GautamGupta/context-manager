<?php

// Admin wrapper
class Context_Manager_Admin extends PB_Framework_Base {

    protected $plugin;

    // On plugins loaded
    function __construct( $plugin ) {

        // Inject the main plugin object
        $this->plugin = $plugin;

        add_action( 'add_meta_boxes_' . $this->plugin->post_type, array( &$this, 'register_meta_boxes' ) );

        add_action( 'admin_print_styles-post.php', array( &$this, 'styles' ) );
        add_action( 'admin_print_styles-post-new.php', array( &$this, 'styles' ) );

        add_action( 'admin_print_scripts-post.php', array( &$this, 'scripts' ) );
        add_action( 'admin_print_scripts-post-new.php', array( &$this, 'scripts' ) );

        add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );
    }

    // When the post type is registered
    function register_meta_boxes() {

        add_meta_box( 'context-manager-conditions', __( 'Conditions', 'context-manager' ), array( $this->plugin->meta_boxes['conditions'], 'display' ), $this->plugin->post_type, 'normal' );

        add_meta_box( 'context-manager-reactions', __( 'Reactions', 'context-manager' ), array( $this->plugin->meta_boxes['reactions'], 'display' ), $this->plugin->post_type, 'normal' );
    }

    // When Stylesheets are outputted on post.php
    function styles() {

        // Only for when we're editing this post type
        if ( ! isset( $GLOBALS['post_type_object'] ) || $GLOBALS['post_type_object']->name != $this->plugin->post_type ) return;

        foreach ( $this->plugin->meta_boxes as $meta_box ) $meta_box->styles();
    }

    // When JavaScript is outputted on post.php
    function scripts() {

        // Only for when we're editing this post type
        if ( ! isset( $GLOBALS['post_type_object'] ) || $GLOBALS['post_type_object']->name != $this->plugin->post_type ) return;

        foreach ( $this->plugin->meta_boxes as $meta_box ) $meta_box->scripts();

        // Disable autosave to prevent to unsaved form notice
        if ( wp_script_is( 'autosave', $list = 'queue' ) ) wp_dequeue_script( 'autosave' );
    }

    function post_updated_messages( $messages ) {
        global $post, $post_ID;

        $messages[ $this->plugin->post_type ] = array(
            0 => '',
            1 => __( 'Context rule updated.', 'context-manager' ),
            2 => __( 'Custom field updated.', 'context-manager' ),
            3 => __( 'Custom field deleted.', 'context-manager' ),
            4 => __( 'Context rule updated.', 'context-manager' ),
            5 => isset($_GET['revision']) ? sprintf( __( 'Context rule restored to revision from %s', 'context-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => __( 'Context rule published.', 'context-manager' ),
            7 => __( 'Context rule saved.', 'context-manager' ),
            8 => __( 'Context rule submitted.', 'context-manager' ),
            9 => __( 'Context rule scheduled for.', 'context-manager' ),
            10 => __( 'Context rule draft updated.', 'context-manager' ),
        );

        return $messages;
    }
}