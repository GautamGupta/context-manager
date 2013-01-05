<?php

require_once dirname( __FILE__ ) . '/../libs/pb-framework/meta-box2.php';
class Context_Manager_Meta_Box_Reactions extends PB_Meta_Box2 {

    // On init
    function __construct( $plugin ) {

        $this->plugin = $plugin;

        // Only setup metabox hooks we have reactions registered
        if ( ! $this->plugin->reactions ) return;

        parent::__construct();

        // Meta box only used on context_rules post type
        $this->post_type = $this->plugin->post_type;

        add_action( 'admin_init', array( &$this, 'setup_reactions' ) );

    }

    // On admin_init
    function setup_reactions() {
        
        // Display reactions
        foreach ( $this->plugin->reactions as $reaction_namespace => $reaction ) {
            if ( $reaction->form() ) {
                $this->prefix = $reaction->field_prefix();
                $this->add_field_group( $reaction_namespace, $reaction->form() );
            }
        }
    }

    // Display meta box
    function display( $post ) {

        if ( ! $this->plugin->reactions ) {
            echo '<h4>' . __( 'No reactions registered. Contact the site administrator.', 'context-manager' ) . '</h4>';
            return;
        }

        // Setup tabs and panels
        foreach ( $this->plugin->reactions as $reaction_namespace => $reaction ) {

            $panel_id = 'panel-rules-reactions-' . sanitize_title( $reaction->name );

            $panels[] = '
                <div id="' . $panel_id . '" class="pb-panel">
                    <h4>' . $reaction->name . '</h4>' .
                    $reaction->display_header() .
                    PB_Forms::table( $this->get_fields( $reaction_namespace ), get_post_custom( $post->ID ) ) .
                    $reaction->display_footer() . '
                </div>
            ';
        }

        // Display meta box
        echo '<div class="pb-panels">';
        echo implode( '', $panels );
        echo '</div>';
    }

    // Prefix fields with each reaction
    protected function field_prefix() {
        return $this->prefix;
    }
}