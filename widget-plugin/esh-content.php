<?php
/*
Plugin Name: Embassy Content Widgets
Description: Site specific content areas
*/

if ( !defined( 'DB_NAME' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

if ( !defined( 'ESHCP_PATH' ) )
	define( 'ESHCP_PATH', plugin_dir_path( __FILE__ ) );


require_once( ESHCP_PATH . "sidebar.php" );
require_once( ESHCP_PATH . "footer.php" );


?>