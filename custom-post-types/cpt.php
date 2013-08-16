<?php

/* video custom post type & supporting taxonomies

Advanced Custom Post Types plugin used to add the following fields:
description
poster ( returns image url )
video_id
video_id_preview
video_id_no_music
in_mobile_app

See: http://www.advancedcustomfields.com/docs/getting-started/code-examples/

 */

function namespace_register_video_cpt(){
	$post_type_args = array(
		'public' 		=> true,
		'query_var' 	=> 'videos',
		'has_archive'	=> true,
		'rewrite'		=> 
		array (
			'slug' 			=> 'videos',
			'with_front' 	=> false
		),
		'taxonomies'	=> array('namespace_video_props', 'namespace_video_categories', 'namespace_video_instructors', 'namespace_video_durations' ),
		'supports' 		=> array('title', 'comments'),
		'labels'		=> 
			array(
				'name' 					=> 'Videos',
				'singular_name' 		=> 'Video'
				)	
	);
	register_post_type('namespace_video', $post_type_args);


	// props
	$prop_args = array(
		'labels'		=> 
			array(
				'name' 					=> 'Props',
				'singular_name' 		=> 'Prop'
				),
			'public'	=> false,
			'show_ui'	=> true,
			'query_var'	=> false,
			'rewrite'	=> false,
			'hierarchical' => true

	);
	register_taxonomy('namespace_video_props', 'namespace_video', $prop_args );
	register_taxonomy_for_object_type( 'namespace_video_props', 'namespace_video' );


	// categories
	$category_args = array(
		'labels'		=> 
			array(
				'name' 					=> 'Video Categories',
				'singular_name' 		=> 'Video Category'
				),
			'public'	=> false,
			'show_ui'	=> true,
			'query_var'	=> true,
			'rewrite'	=> false,
			'hierarchical' => true,
			'rewrite'	=> array( 'slug' => 'focus' ),
			'show_admin_column' => true,

	);
	register_taxonomy('namespace_video_categories', 'namespace_video', $category_args );
	register_taxonomy_for_object_type( 'namespace_video_categories', 'namespace_video' );

	// instructors
	$instructor_args = array(
		'labels'		=> 
			array(
				'name' 					=> 'Instructors',
				'singular_name' 		=> 'Instructor'
				),
			'public'	=> false,
			'show_ui'	=> true,
			'query_var'	=> false,
			'rewrite'	=> false,
			'hierarchical' => true

	);
	register_taxonomy('namespace_video_instructors', 'namespace_video', $instructor_args );
	register_taxonomy_for_object_type( 'namespace_video_instructors', 'namespace_video' );

	// Durations (used so we can do easy multiple taxonomy requests http://thereforei.am/2011/10/28/advanced-taxonomy-queries-with-pretty-urls/)
	$duration_args = array(
		'labels'		=> 
			array(
				'name' 					=> 'Durations',
				'singular_name' 		=> 'Duration',
				),
			'public'	=> false,
			'show_ui'	=> true,
			'show_tagcloud' => true,
			'query_var'	=> true,
			'rewrite'	=> false,
			'hierarchical' => true,
			'show_admin_column' => true,
			'rewrite'	=> array( 
				'slug' => 'duration',
				'with_front' => true,
				),

	);
	register_taxonomy('namespace_video_durations', 'namespace_video', $duration_args );
	register_taxonomy_for_object_type( 'namespace_video_durations', 'namespace_video' );


}
add_action('init', 'namespace_register_video_cpt');


/** DISPLAY ****/




// goal: show 3 videos in each category (which is a taxonomy: namespace_video_categories)
// http://codex.wordpress.org/Class_Reference/WP_Query
function namespace_list_videos_by_category(){
	global $post, $wp_query;
	// get all categories (taxonomy: namespace_video_categories)
	$categories = get_terms('namespace_video_categories');

	// loop thru
	$catTpl = file_get_contents("templates/video_cateogry.html", true);
	$returnHTML = "";
	foreach ($categories as $category) {
		$catHTML = $catTpl;
		// display category header
		$catHTML = str_replace("##CATEGORY_NAME##", $category->name, $catHTML);
		// view all in category links
		$catLink = get_post_type_archive_link( 'namespace_video' ) . "?namespace_video_categories=" . $category->slug; 
		$catHTML = str_replace("##CATEGORY_LINK##", $catLink, $catHTML);

		// display 3 most recent videos from this category
		$catVideoBoxHTML = "";
		$catArgs = array(
			'post_type' => 'namespace_video',
			'tax_query' => array(
				array(
					'taxonomy' => 'namespace_video_categories',
					'field' => 'id',
					'terms' => $category->term_id
				)
			),
			'posts_per_page' => 3
		);
		$catQ = new WP_Query( $catArgs );
		while ( $catQ->have_posts() ) {
			$catQ->the_post();
			ob_start();
			get_template_part( 'partials/content-namespace_video' );
			$videoBox = ob_get_clean();
			$catVideoBoxHTML .= $videoBox;
			
		}
		$catHTML = str_replace("##VIDEO_BOXES##", $catVideoBoxHTML, $catHTML);
		$returnHTML .= $catHTML;

		wp_reset_postdata();
		
	}

	// swap classes on the boxes
	$returnHTML = str_replace("gridItem", "col colspan4", $returnHTML);
	return ($returnHTML);

}



// goal: show 3 videos in each duration (which is a taxonomy: namespace_video_categories)
// http://codex.wordpress.org/Class_Reference/WP_Query
function namespace_list_videos_by_duration(){
	global $post, $wp_query;
	// get all durations (taxonomy: namespace_video_durations)
	$durations = get_terms('namespace_video_durations');

	// loop thru
	$catTpl = file_get_contents("templates/video_cateogry.html", true);
	$returnHTML = "";
	foreach ($durations as $category) {
		$catHTML = $catTpl;
		// display category header
		$catHeader = $category->name . " minutes";
		$catHTML = str_replace("##CATEGORY_NAME##", $catHeader, $catHTML);
		// view all in category links
		$catLink = get_post_type_archive_link( 'namespace_video' ) . "?namespace_video_durations=" . $category->slug; 
		$catHTML = str_replace("##CATEGORY_LINK##", $catLink, $catHTML);

		// display 3 most recent videos from this category
		$catVideoBoxHTML = "";
		$catArgs = array(
			'post_type' => 'namespace_video',
			'tax_query' => array(
				array(
					'taxonomy' => 'namespace_video_durations',
					'field' => 'id',
					'terms' => $category->term_id
				)
			),
			'posts_per_page' => 3
		);
		$catQ = new WP_Query( $catArgs );
		while ( $catQ->have_posts() ) {
			$catQ->the_post();
			ob_start();
			get_template_part( 'partials/content-namespace_video' );
			$videoBox = ob_get_clean();
			$catVideoBoxHTML .= $videoBox;
			
		}
		$catHTML = str_replace("##VIDEO_BOXES##", $catVideoBoxHTML, $catHTML);
		$returnHTML .= $catHTML;

		wp_reset_postdata();
		
	}

	// swap classes on the boxes
	$returnHTML = str_replace("gridItem", "col colspan4", $returnHTML);
	return ($returnHTML);

}




// display filter terms on archive/search results page
function namespace_video_filter_list(){
	global $wp_query;
	//print_r($wp_query->query_vars['namespace_video_categories']);
	//print_r($wp_query->query_vars['namespace_video_durations']);

	// make arrays of query vars, filter empty string
	$categoryAr = array_filter(explode(',', $wp_query->query_vars['namespace_video_categories']));
	$durationAr = array_filter(explode(',', $wp_query->query_vars['namespace_video_durations']));

	// pull name for each category query var
	// replacing slugs with names
	// http://codex.wordpress.org/Function_Reference/get_term_by
	foreach ($categoryAr as &$category) {
		$termObj = get_term_by('slug', $category, 'namespace_video_categories');
		$category = $termObj->name;
	}

	// pull name for each duration query var
	// replacing slugs with names
	foreach($durationAr as &$duration){
		$termObj = get_term_by('slug', $duration, 'namespace_video_durations');
		// add "minutes" to each duration name
		$duration = $termObj->name . " minute workout";
	}	

	// join arrays
	$filterAr = array_merge($durationAr, $categoryAr);

	// make comma delimited string of all terms
	$filterList = implode(", ", $filterAr);
	return $filterList;
} // END namespace_video_filter_list()




// setup display values
// instantiate with post id:
// $video = new namespace_video_display($postID)

class namespace_video_display {


	function __construct($post_id) {
		$this->post_id = $post_id;
	}

	function get_post_id() {
		return $this->post_id;
	}

	/* video id ****/
	// this differs based on user's login status
	// logged out / unsubscribers get the preview id
	// logged in subscribers get the full video id

	// use this one in the template 
	function get_video_id(){
		if(!isset($this->video_id)) {
			$this->set_video_id();
		}
		return $this->video_id;
	} 

	function set_video_id(){
		if(is_barre3_subscriber()){
			$this->video_id = $this->get_full_video_id();
		} else {
			$this->video_id = $this->get_preview_video_id();
		}
	}

	function get_full_video_id(){
		if (!isset($this->full_video_id)){
			$this->set_full_video_id();
		}
		return $this->full_video_id;
	}

	function set_full_video_id(){
		$this->full_video_id = get_field('video_id', $this->get_post_id());
	}

	function get_preview_video_id(){
		if (!isset($this->preview_video_id)){
			$this->set_preview_video_id();
		}
		return $this->preview_video_id;
	}

	function set_preview_video_id(){
		$this->preview_video_id = get_field('video_id_preview', $this->get_post_id());
	}






	/* description *****/
	function get_description(){
		if (!isset($this->description)){
			$this->set_description();
		}
		return $this->description;
	}

	// from acf http://www.advancedcustomfields.com/resources/functions/get_field/
	function set_description(){
		$this->description = get_field('description', $this->get_post_id());
	}



	/* 	props ****/

	// returns array of objects
	function get_props(){
		if (!isset($this->props)) {
			$this->set_props();
		}
		return $this->props;
	}

	function set_props(){
		$this->props = get_the_terms( $this->post_id, 'namespace_video_props');
	}

	// returns text list of comma separated props
	function get_prop_list(){
		if (!isset($this->prop_list)) {
			$this->set_prop_list();
		}
		return $this->prop_list; 
	}

	function set_prop_list() {
		$prop_ar = $this->get_props();
		$this->prop_list =   $this->make_comma_list($prop_ar);
	}

	/* 	focus / categories ****/

	// returns array of objects
	function get_categories(){
		if (!isset($this->categories)) {
			$this->set_categories();
		}
		return $this->categories;
	}

	function set_categories(){
		$this->categories = get_the_terms( $this->post_id, 'namespace_video_categories');
	}

	// returns text list of comma separated props
	function get_category_list(){
		if (!isset($this->category_list)) {
			$this->set_category_list();
		}
		return $this->category_list; 
	}

	function set_category_list() {
		$category_ar = $this->get_categories();
		$this->category_list =   $this->make_comma_list($category_ar);
	}


	/* 	instructors ****/

	// returns array of objects
	function get_instructors(){
		if (!isset($this->instructors)) {
			$this->set_instructors();
		}
		return $this->instructors;
	}

	function set_instructors(){
		$this->instructors = get_the_terms( $this->post_id, 'namespace_video_instructors');
	}

	// returns text list of comma separated instructor names
	function get_instructor_list(){
		if (!isset($this->instructor_list)) {
			$this->set_instructor_list();
		}
		return $this->instructor_list; 
	}

	function set_instructor_list() {
		$instructor_ar = $this->get_instructors();
		$this->instructor_list =  $this->make_comma_list($instructor_ar);
	}


	/* social share links **/
	function get_twitter_link(){
		if(!isset($this->twitter_link)){
			$this->set_twitter_link();
		}
		return $this->twitter_link;
	}

	function set_twitter_link(){
		$tpl = "https://twitter.com/intent/tweet?hashtags=barre3Online&url=##URL##";
		$full = str_replace("##URL##", $this->get_share_url(), $tpl);
		$this->twitter_link = $full;
	}

	function get_facebook_link(){
		if(!isset($this->facebook_link)){
			$this->set_facebook_link();
		}
		return $this->facebook_link;
	}


	function set_facebook_link(){
		$tpl = "https://www.facebook.com/dialog/feed?app_id=278733278909537&caption=&description=&link=##URL##&name=##NAME##&picture=##PICTURE##&redirect_uri=##URL##"; 
		$name = urlencode(get_the_title($this->get_post_id()));

		$full = str_replace("##URL##", $this->get_share_url(), $tpl);
		$full = str_replace("##NAME##", $name, $full);
		$full = str_replace("##PICTURE##", urlencode($this->get_poster_url()), $full);
		$this->facebook_link = $full;
	}

	function get_share_url(){
		if (!isset($this->share_url)){
			$this->set_share_url();
		}
		return $this->share_url;
	}

	function set_share_url(){
		$this->share_url = urlencode(get_permalink( $this->get_post_id()));
	}

	function get_poster_url(){
		if(!isset($this->poster_url)){
			$this->set_poster_url();
		}
		return $this->poster_url;
	}

	function set_poster_url(){
		$this->poster_url = get_field('poster', $this->get_post_id());
	}


	/* Utils ***/

	// turn an array of objects (specifically, taxonomy terms from get_the_terms() into a nice comma list)
	function make_comma_list($array){
		$html_list = "";
		foreach ($array as $item) {
			$html_list .= $item->name;
			$html_list .= ", ";
		}
		$html_list = rtrim($html_list, ", ");
		return $html_list;
	}


} // END namespace_video_display


?>