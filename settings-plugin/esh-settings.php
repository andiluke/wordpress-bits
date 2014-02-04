<?php
/*
Plugin Name: ESH Settings
Description: Site specific settings

http://ottopress.com/2009/wordpress-settings-api-tutorial/
http://kovshenin.com/2012/the-wordpress-settings-api/
*/

if ( !defined( 'DB_NAME' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

// Add settings page to admin menu
function esh_settings_admin_item(){
	add_options_page( "ESH Settings", "ESH Settings", 'manage_options', 'esh_settings_plugin', 'esh_settings_admin_page' );
}
// END esh_settings_admin_item

add_action('admin_menu', 'esh_settings_admin_item');


// define page content
function esh_settings_admin_page() {
	?>
	<div>
	<h2>Embassy Suites Waikiki Booking &amp; Tracking Settings</h2>	
	<form action="options.php" method="post">
	<?php settings_fields('esh_settings_plugin'); ?>
	<?php do_settings_sections('esh_settings_plugin'); ?>
	 <?php submit_button(); ?>
	</form></div>
	 
	<?php
}


function esh_settings_plugin_admin_init(){

	// params: option group, option name, sanitize callback
	register_setting( 'esh_settings_plugin', 'esh_settings_plugin', 'esh_settings_validate_options' );

	// get any saved settings to pass values to the form input creation function
	$settings = get_option('esh_settings_plugin');

	// contains lang array
	global $eshVars;
	foreach ($eshVars['languages'] as $lang_array) {
		$lang_name = $lang_array['translated_name'];
		$lang_code = $lang_array['language_code'];
		$section_name = "esh_settings_" . $lang_code;
		$section_display_title = $lang_name . " : " . strtoupper($lang_code);

		// create a section for each language
		// add_settings_section( $id, $title, $callback, $page )     
		add_settings_section( 
			$section_name, // id
			$section_display_title, // title 
			'esh_settings_section_text',  // callback function for text
			'esh_settings_plugin' // page
		);

		// add fields for each language
		// add_settings_field( $id, $title, $callback, $page, $section, $args );
		// The html input field's name attribute must match $option_name in register_setting(), and value can be filled using get_option().
		// http://codex.wordpress.org/Function_Reference/add_settings_field
		add_settings_field( 
			'esh_settings_booking_link' . $lang_code, 
			'Booking URL', 
			'esh_settings_text_input', 
			'esh_settings_plugin', 
			$section_name, 
			array(
	    		'name' => 'esh_settings_plugin['.$lang_code.'][booking_link]',
	    		'value' => $settings[$lang_code]['booking_link'],
			) 
		); 
		add_settings_field( 
			'esh_settings_booking_widget_url' . $lang_code, 
			'Booking Widget Form Submission URL', 
			'esh_settings_text_input', 
			'esh_settings_plugin', 
			$section_name, 
			array(
	    		'name' => 'esh_settings_plugin['.$lang_code.'][booking_widget_url]',
	    		'value' => $settings[$lang_code]['booking_widget_url'],
			) 
		);
		add_settings_field( 
			'esh_settings_omniture_id_live' . $lang_code, 
			'Live Omniture ID', 
			'esh_settings_text_input', 
			'esh_settings_plugin', 
			$section_name, 
			array(
	    		'name' => 'esh_settings_plugin['.$lang_code.'][omniture_id_live]',
	    		'value' => $settings[$lang_code]['omniture_id_live'],
			) 
		);
		add_settings_field( 
			'esh_settings_omniture_id_dev' . $lang_code, 
			'Dev Omniture ID', 
			'esh_settings_text_input', 
			'esh_settings_plugin', 
			$section_name, 
			array(
	    		'name' => 'esh_settings_plugin['.$lang_code.'][omniture_id_dev]',
	    		'value' => $settings[$lang_code]['omniture_id_dev'],
			) 
		);

	}
	// end of lang loop
}

add_action('admin_init', 'esh_settings_plugin_admin_init');

function esh_settings_section_text(){
	// don't really need any text entered here.
}

function esh_settings_text_input( $args ) {
	$name = esc_attr( $args['name'] );
    $value = esc_attr( $args['value'] );
    echo "<input type='text' name='$name' value='$value' />";
}

function esh_settings_validate_options($input) {
	
	$options = get_option('esh_settings_plugin');

	foreach ($input as $lang => $opt_array) {
		// validate booking url
		$opt_array['booking_link'] = trim($opt_array['booking_link']);
		if (esh_settings_validate_url($opt_array['booking_link'])){
			$options[$lang]['booking_link'] = $opt_array['booking_link'];
		} else {
			add_settings_error( 'esh_settings_plugin', 'invalid-url', 'You have entered an invalid Booking URL for ' . strtoupper($lang) );
		}

		// validate booking widget url
		$opt_array['booking_widget_url'] = trim($opt_array['booking_widget_url']);
		if (esh_settings_validate_url($opt_array['booking_widget_url'])){
			$options[$lang]['booking_widget_url'] = $opt_array['booking_widget_url'];
		} else {
			add_settings_error( 'esh_settings_plugin', 'invalid-url', 'You have entered an invalid Booking Widget URL for ' . strtoupper($lang) );
		}

		// sanitize omniture ids
		$options[$lang]['omniture_id_live'] = sanitize_text_field(trim($opt_array['omniture_id_live']));
		$options[$lang]['omniture_id_dev'] = sanitize_text_field(trim($opt_array['omniture_id_dev']));  
		
	} // end language loop

	return $options;
}

// takes a string, returns true if its a valid url, false if not
function esh_settings_validate_url($input_url){
	$flags = 0;
	if(!in_array(parse_url($input_url, PHP_URL_SCHEME),array('http','https'))){
		$flags++;
	}
	if(!filter_var($input_url, FILTER_VALIDATE_URL)){
		$flags++;
	}
	$test = $flags == 0 ? true : false;
	return $test;
}

?>
