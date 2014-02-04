<?php
/* SIDEBAR WIDGET : DEALS & SPECIALS ONLY *******************/
// Creating the widget 
class esh_sb_widget extends WP_Widget {
    
	var $context = 'Embassy Sidebar Widget';

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'esh_sb_widget', 

			// Widget name will appear in UI
			'Embassy Sidebar Widget', 

			// Widget description
			array( 'description' => 'Sidebar content for Deals & Specials' ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		extract($args);
 
        $headline 	= icl_t($this->context, "Headline", $instance['headline']);
        $blurb      = icl_t($this->context, "Blurb", $instance['blurb']);
        $link_text  = icl_t($this->context, "Link Text", $instance['link_text']);
        $link_url   = icl_t($this->context, "Link URL", $instance['link_url']);

        $tpl	= file_get_contents(ESHCP_PATH . "templates/ds_sidebar.html");
        $item_tpl = file_get_contents(ESHCP_PATH . "templates/ds_sidebar_item.html");

        $html = $tpl;

        // widget text string replacements
        $html = str_replace("##HEADER##", $headline, $html);
        $html = str_replace("##BLURB##", $blurb, $html);
        $html = str_replace("##LINK_URL##", $link_url, $html);
        $html = str_replace("##LINK_TEXT##", $link_text, $html);

        // image items are cpt esh_deal_sbwid_item under Deals & Specials in the admin 
        $item_args = array(
        	'post_type' => 'esh_deal_sbwid_item',
        	'orderby' => 'menu_order',
        	'order' => 'ASC',
        	'limit' => 4
        );
        $item_query = new WP_Query($item_args);
        //$item_query = new WP_Query('post_type=esh_deal_sbwid_item');
        $grid_html = "";
        if($item_query->have_posts()){
        	$i = 0; // counter for even/odd css classes
        	while($item_query->have_posts()){
        		$item_query->the_post();
        		$item_html = $item_tpl;
        		// string replacements per item
        		$item_html = str_replace("##CAPTION1##", get_the_title(), $item_html);
        		$item_html = str_replace("##IMG##", get_the_post_thumbnail(), $item_html);
        		$class = $i % 2 ? "last" : "first";
        		$item_html = str_replace("##CLASS##", $class, $item_html);

        		// add spacer div
        		if ($i == 1){
        			$item_html .= '<div style="height:15px;clear:both;"></div>';
        		}

        		// add this item to the list
        		$grid_html .= $item_html;
        		$i++;
        	}
        }
        wp_reset_postdata();

        // add grid items to the main content
        $html = str_replace("##GRID##", $grid_html, $html);

        echo $html;
	} // END widget front-end display function
			
	// Widget Backend 
	public function form( $instance ) {
		$headline   = esc_attr( isset( $instance['headline'] ) ? $instance['headline'] : '' );
        $blurb      = esc_attr( isset( $instance['blurb'] ) ? $instance['blurb'] : '' );
        $link_text	= esc_attr( isset( $instance['link_text'] ) ? $instance['link_text'] : '' );
        $link_url 	= esc_attr( isset( $instance['link_url'] ) ? $instance['link_url'] : '' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'headline' ); ?>"><?php _e( 'Headline:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'headline' ); ?>" name="<?php echo $this->get_field_name( 'headline' ); ?>" type="text" value="<?php echo $headline; ?>" />
            </label>
        </p>

        <p>Images & captions are under Deals & Specials.</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'blurb' ); ?>"><?php _e( 'Blurb:' ); ?>
                <textarea class="widefat" id="<?php echo $this->get_field_id( 'blurb' ); ?>" name="<?php echo $this->get_field_name( 'blurb' ); ?>"><?php echo $blurb; ?></textarea>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e( 'Link Text:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo $link_text; ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'link_url' ); ?>"><?php _e( 'Link URL:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'link_url' ); ?>" name="<?php echo $this->get_field_name( 'link_url' ); ?>" type="text" value="<?php echo $link_url; ?>" />
            </label>
        </p>
        <p>Remember to update String Translations for "<?php echo $this->context; ?>".</p>
        <?php
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
 
        $instance['headline']            = strip_tags( $new_instance['headline'] );
        $instance['blurb']               = strip_tags( $new_instance['blurb'] );
        $instance['link_text']               = strip_tags( $new_instance['link_text'] );
        $instance['link_url']               = strip_tags( $new_instance['link_url'] );

        // register strings for translation
        icl_register_string($this->context, "Headline", $instance['headline']);
        icl_register_string($this->context, "Blurb", $instance['blurb']);
        icl_register_string($this->context, "Link Text", $instance['link_text']);
        icl_register_string($this->context, "Link URL", $instance['link_url']);
 
        return $instance;
	}
} // Class esh_sb_widget ends here
/* ****************************** */

/// cpt for the sidebar items: images with captions
function esh_content_widgets_cpt(){
	$deal_sbwid_item_labels = array(
		'name' => _x('Sidebar Widget Items', 'custom post type generic name'),
		'singular_name' => _x('Sidebar Widget Item', 'individual custom post type name'),
		'add_new_item' => __('Add New Sidebar Widget Item'),
		'edit_item' => __('Edit Sidebar Widget Item'),
		'new_item' => __('New Sidebar Widget Item'),
		'search_items' => __('Search Sidebar Widget Items'),
		'not_found' => __('No Sidebar Widget Items Found'),  
		'not_found_in_trash' => __('No Sidebar Widget Items Found in Trash')
	);
	$deal_sbwid_item_args = array(
		'labels' => $deal_sbwid_item_labels,
		'public' => false,
		'show_ui' => true,
		'publicly_queryable' => false,
		'exclude_from_search' => true, 
		'show_in_nav_menus' => false,
		'has_archive' => false,
		'show_in_menu' => 'edit.php?post_type=esh_deal',
		'hierarichal' => false,
		'supports' => array('title','thumbnail','page-attributes')
	); 
	register_post_type('esh_deal_sbwid_item', $deal_sbwid_item_args);
}
	
add_action('init', 'esh_content_widgets_cpt');



// Register and load the widget
function esh_load_sidebar_widget() {
	register_widget( 'esh_sb_widget' );
}

add_action( 'widgets_init', 'esh_load_sidebar_widget' );
?>