<?php
/* FOOTER WIDGET *******************/
// Creating the widget 
class esh_footer_widget extends WP_Widget {

	var $context = 'Embassy Footer Widget';

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'esh_footer_widget', 

			// Widget name will appear in UI
			'Embassy Footer Widget', 

			// Widget description
			array( 'description' => 'Footer Text and Links' ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		extract($args);

        $pp_text   = icl_t($this->context, "Privacy Policy Link Text", $instance['pp_text']);
        $pp_link   = icl_t($this->context, "Privacy Policy Link URL", $instance['pp_link']);
        $ua_text   = icl_t($this->context, "User Agreement Link Text", $instance['ua_text']);
        $ua_link   = icl_t($this->context, "User Agreement Link URL", $instance['ua_link']);
        $address   = icl_t($this->context, "Address", $instance['address']);
        $ownership   = icl_t($this->context, "Ownership", $instance['ownership']);
        
        $tpl	= file_get_contents(ESHCP_PATH . "templates/footer_fine_print.html");

        // widget text string replacements
        $html = str_replace("##PRIVACY_TEXT##", $pp_text, $tpl);
        $html = str_replace("##PRIVACY_LINK##", $pp_link, $html);
        $html = str_replace("##UA_LINK##", $ua_link, $html);
        $html = str_replace("##UA_TEXT##", $ua_text, $html);
        $html = str_replace("##ADDRESS##", $address, $html);
        $html = str_replace("##OWNERSHIP##", $ownership, $html);
        $html = str_replace("##YEAR##", date('Y'), $html);

        echo $html;
        
	} // END widget front-end display function
			
	// Widget Backend 
	public function form( $instance ) {
        
		$pp_text   = esc_attr( isset( $instance['pp_text'] ) ? $instance['pp_text'] : '' );
        $pp_link   = esc_attr( isset( $instance['pp_link'] ) ? $instance['pp_link'] : '' );
        $ua_text   = esc_attr( isset( $instance['ua_text'] ) ? $instance['ua_text'] : '' );
        $ua_link   = esc_attr( isset( $instance['ua_link'] ) ? $instance['ua_link'] : '' );
        $address   = esc_attr( isset( $instance['address'] ) ? $instance['address'] : '' );
        $ownership   = esc_attr( isset( $instance['ownership'] ) ? $instance['ownership'] : '' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'pp_text' ); ?>">Privacy Policy Text</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'pp_text' ); ?>" name="<?php echo $this->get_field_name( 'pp_text' ); ?>" type="text" value="<?php echo $pp_text; ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'pp_link' ); ?>">Privacy Policy URL</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'pp_link' ); ?>" name="<?php echo $this->get_field_name( 'pp_link' ); ?>" type="text" value="<?php echo $pp_link; ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'ua_text' ); ?>">Usage Agreement Text</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ua_text' ); ?>" name="<?php echo $this->get_field_name( 'ua_text' ); ?>" type="text" value="<?php echo $ua_text; ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'ua_link' ); ?>">Usage Agreement URL</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ua_link' ); ?>" name="<?php echo $this->get_field_name( 'ua_link' ); ?>" type="text" value="<?php echo $ua_link; ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'address' ); ?>">Address Text</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" type="text" value="<?php echo $address; ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'ownership' ); ?>">Ownership Text</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'ownership' ); ?>" name="<?php echo $this->get_field_name( 'ownership' ); ?>" type="text" value="<?php echo $ownership; ?>" />
            </label>
        </p>

        <p>Remember to update String Translations for "<?php echo $this->context; ?>".</p>
        <?php
    
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
 
        $instance['pp_text']            = strip_tags( $new_instance['pp_text'] );
        $instance['pp_link']            = strip_tags( $new_instance['pp_link'] );
        $instance['ua_text']            = strip_tags( $new_instance['ua_text'] );
        $instance['ua_link']            = strip_tags( $new_instance['ua_link'] );
        $instance['address']            = strip_tags( $new_instance['address'] );
        $instance['ownership']            = strip_tags( $new_instance['ownership'] );

        // register strings for translation
        icl_register_string($this->context, "Privacy Policy Link Text", $instance['pp_text']);
        icl_register_string($this->context, "Privacy Policy Link URL", $instance['pp_link']);
        icl_register_string($this->context, "User Agreement Link Text", $instance['ua_text']);
        icl_register_string($this->context, "User Agreement Link URL", $instance['ua_link']);
        icl_register_string($this->context, "Address", $instance['address']);
        icl_register_string($this->context, "Ownership", $instance['ownership']);
 
        return $instance;
	}
} // Class esh_sb_widget ends here
/* ****************************** */



// Register and load the widget
function esh_load_footer_widget() {
	register_widget( 'esh_footer_widget' );
}

add_action( 'widgets_init', 'esh_load_footer_widget' );
?>