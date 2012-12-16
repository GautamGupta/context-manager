<?php

// Takes care of creating, updating and deleting database tables

class scbTable {
	protected $name;
	protected $columns;
	protected $upgrade_method;

	function __construct( $name, $file, $columns, $upgrade_method = 'dbDelta' ) {
		$this->name = $name;
		$this->columns = $columns;
		$this->upgrade_method = $upgrade_method;

		scb_register_table( $name );

		if ( $file ) {
			scbUtil::add_activation_hook( $file, array( $this, 'install' ) );
			scbUtil::add_uninstall_hook( $file, array( $this, 'uninstall' ) );
		}
	}

	function install() {
		scb_install_table( $this->name, $this->columns, $this->upgrade_method );
	}

	function uninstall() {
		scb_uninstall_table( $this->name );
	}
}


function scb_register_table( $name ) {
	global $wpdb;

	$wpdb->tables[] = $name;
	$wpdb->$name = $wpdb->prefix . $name;
}

function scb_install_table( $name, $columns, $upgrade_method = 'dbDelta' ) {
	global $wpdb;

	$full_table_name = $wpdb->$name;

	$charset_collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}

	if ( 'dbDelta' == $upgrade_method ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( "CREATE TABLE $full_table_name ( $columns ) $charset_collate" );
		return;
	}

	if ( 'delete_first' == $upgrade_method )
		$wpdb->query( "DROP TABLE IF EXISTS $full_table_name;" );

	$wpdb->query( "CREATE TABLE IF NOT EXISTS $full_table_name ( $columns ) $charset_collate;" );
}

function scb_uninstall_table( $name ) {
	global $wpdb;

	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->$name );
}

