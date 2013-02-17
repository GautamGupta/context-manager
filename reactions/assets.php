<?php

// Add body class

class Context_Manager_Reaction_Assets extends Context_Manager_Reaction {

    function __construct( $plugin ) {
        add_action( 'wp_enqueue_scripts', array( &$this, 'queue_assets' ), 9999 );
        parent::__construct( $plugin, array( 'name' => 'Stylesheets and JavaScript' ) );
    }

    function form( $preprocess = true ) {

        global $wp_styles, $wp_scripts;

        if ( $preprocess && isset( $wp_styles ) && isset( $wp_scripts ) ) {

            // We need to call wp_enqueue_scripts to get the queued frontend assets
            // This is reset once we have our data
            $preaction_styles = clone $wp_styles;
            $preaction_scripts = clone $wp_scripts;

            $this->stop_reaction = true;
            do_action( 'wp_enqueue_scripts' );
            $this->stop_reaction = false;

            foreach ( array( 'styles', 'scripts' ) as $asset_var_dropdown ) {
                $asset_var_obj = 'wp_' . $asset_var_dropdown;

                // No registered assets
                if ( ! ${$asset_var_obj}->registered ) continue;

                // Init vars
                $group_themes = array();
                $group_plugins = array();
                $group_core = array();

                foreach ( ${$asset_var_obj}->registered as $handle => $asset ) {

                    // Theme libraries first
                    $asset_group = 'group_';
                    if ( preg_match( '/' . preg_quote( get_theme_root_uri(), '/' ) . '/', $asset->src ) ) {
                        $asset_group .= 'themes';
                    }
                    // Plugin libraries
                    elseif ( preg_match( '/' . preg_quote( plugins_url(), '/' ) . '/', $asset->src ) ) {
                        $asset_group .= 'plugins';
                    }
                    // Must be a core library
                    else {
                        $asset_group .= 'core';
                    }

                    ${$asset_group}[ $handle ] = $handle . ' (' . $asset->src . ')';
                }

                // Re-order
                if ( ! empty( $group_themes ) ) ${$asset_var_dropdown}['Theme'] = $group_themes;
                if ( ! empty( $group_plugins ) ) ${$asset_var_dropdown}['Plugins'] = $group_plugins;
                if ( ! empty( $group_core ) ) ${$asset_var_dropdown}['WordPress Core'] = $group_core;
            }

            // Reset the asset objects
            $wp_styles = $preaction_styles;
            $wp_scripts = $preaction_scripts;
        }

        return array(
            'styles_include' => array(
                'title' => __( 'Include these stylesheets:' , 'context-manager' ),
                'type' => 'select',
                'value' => ! empty( $styles ) ? $styles : array(),
                'multiple' => true,
                'text' => __( 'Choose stylesheets' , 'context-manager' ),
            ),
            'styles_exclude' => array(
                'title' => __( 'Exclude these stylesheets:' , 'context-manager' ),
                'type' => 'select',
                'value' => ! empty( $styles ) ? $styles : array(),
                'multiple' => true,
                'text' => __( 'Choose stylesheets' , 'context-manager' ),
                'footer' => '<p>' . __( 'Note: These files will not be excluded if there are other stylesheets that require them as dependencies.', 'context-manager' ) . '</p>',
            ),
            'scripts_include' => array(
                'title' => __( 'Include these JavaScript libraries:' , 'context-manager' ),
                'type' => 'select',
                'value' => ! empty( $scripts ) ? $scripts : array(),
                'multiple' => true,
                'text' => __( 'Choose scripts' , 'context-manager' ),
            ),
            'scripts_exclude' => array(
                'title' => __( 'Exclude these JavaScript libraries:' , 'context-manager' ),
                'type' => 'select',
                'value' => ! empty( $scripts ) ? $scripts : array(),
                'multiple' => true,
                'text' => __( 'Choose scripts' , 'context-manager' ),
                'footer' => '<p>' . __( 'Note: These files will not be excluded if there are other scripts that require them as dependencies.', 'context-manager' ) . '</p>',
            ),
        );
    }

    // On wp_enqueue_scripts
    function queue_assets() {

        // Hack to stop the reaction from being triggered when we're displaying the form
        if ( $this->stop_reaction ) return;

        if ( ! $context_rules = $this->get_rules() ) return;
        foreach ( $context_rules as $context_rule ) {
            if ( ! $this->plugin->conditions_match( $context_rule ) ) continue;

            // Enqueue
            foreach ( get_post_meta( $context_rule->ID, $this->field_prefix() . 'styles_include' ) as $asset )  wp_enqueue_style( $asset );
            foreach ( get_post_meta( $context_rule->ID, $this->field_prefix() . 'scripts_include' ) as $asset ) wp_enqueue_script( $asset );

            // Dequeue
            foreach ( get_post_meta( $context_rule->ID, $this->field_prefix() . 'styles_exclude' ) as $asset )  wp_dequeue_style( $asset );
            foreach ( get_post_meta( $context_rule->ID, $this->field_prefix() . 'scripts_exclude' ) as $asset ) wp_dequeue_script( $asset );
        }
    }
}