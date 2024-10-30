<?php
/**
 * Plugin Name: CP Client Logo Slider
 * Plugin URI: https://wordpress.org/plugins/search/cp-client-logo-slider
 * Description: Company logo carousel, event carousel, partners, sponsors, website team carousel etc logos responsive carousel slider. its will be help to integrate with shortcode
 * Version: 1.0.0
 * Author: codepopular
 * Author URI: https://www.codepopular.com
 * Text Domain: ccls
 * License: GPLv2
 */

  if (!defined('ABSPATH')) die ('No direct access allowed');


  class Ccls{

  
// ---------- Autoload Action & Filter ----------------
   public function __construct() {	
		// Register client logo slider
		 add_action('init', array($this, 'ccls_register_post_type'));
		
		// Register client logo slider category
		 add_action('init', array($this, 'ccls_register_post_type_category'));

		 // Add meta box
		 add_action('add_meta_boxes' , array($this,'ccls_clientlogo_add_meta_box'));

          // Save Meta box info
		 add_action('save_post', array($this, 'ccls_clientlogo_meta_save'));
		
		// Add Coumn In List
		 add_action('manage_posts_columns', array($this, 'ccls_add_post_thumbnail_column'));
		
		// Add Coumn In List
		 add_action('manage_posts_custom_column', array($this, 'display_post_thunmail_col'), 5, 2);
		 // Activate plugin
		 register_activation_hook(__FILE__, array($this, 'ccls_initialize'));

		 // Activate plugin
		 add_action('admin_head', array($this, 'ccls_testimonials_dashboard_icon'));
		
		// Plugin shortcode
		 add_shortcode('codepopular_logo_slider', array($this, 'ccls_logo_carousel_callback'));

		 
		}





  // ---------- Register client logo slider ----------------	
	function ccls_register_post_type() {

		$labels = array(
			'name' => __('CP Client Slider', 'ccls'),
			'singular_name' => __('Client Logo', 'ccls'),
			'add_new' => _x('Add New Client Logo', 'Client Logo'),
			'add_new_item' => __('Add New Client Logo'),
			'edit_item' => __('Edit Client Logo'),
			'new_item' => __('New Client Logo'),
			'view_item' => __('View Client Logo'),
			'search_items' => __('Search Client Logo'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon' => 'dashicons-format-image',
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 99,
			'supports' => array( 'title', 'thumbnail' )
		  ); 

		register_post_type( 'ccls-client-slider' , $args );
	} 



	// ---------- Register client logo slider category ----------------	
	function ccls_register_post_type_category() {
		register_taxonomy(
			'carousel_cat',  
			'ccls-client-slider',                  
			array(
				'hierarchical'          => true,
				'label'                         => 'Category',  
				'query_var'             => true,
				'show_admin_column'			=> true,
				'rewrite'                       => array(
					'slug'                  => 'cp-logo-slider-category', 
					'with_front'    => true 
					)
				)
		);
	}




	// client logo Meta Box
	function ccls_clientlogo_add_meta_box(){
	// add meta Box
	 remove_meta_box( 'postimagediv', 'ccls-client-slider', 'side' );
	 add_meta_box('postimagediv', __('Client Logo'), 'post_thumbnail_meta_box', 'ccls-client-slider', 'normal', 'high');
	 add_meta_box('ccls_clientlogo_meta_id', __('Client Website Url'), array($this, 'ccls_meta_callback'), 'ccls-client-slider', 'normal', 'high');
	}




	// client logo Meta Box Call Back Funtion
	function ccls_meta_callback($post){

	    wp_nonce_field( basename( __FILE__ ), 'aft_nonce' );
	    $aft_stored_meta = get_post_meta( $post->ID );
	    ?>

	    <p>
	        <label for="ccls_clientlogo_meta_url" class="ccls_clientlogo_meta_url"><?php _e( 'Client Website Url', '' )?></label>
	        <input class="widefat" type="text" name="ccls_clientlogo_meta_url" id="ccls_clientlogo_meta_url" value="<?php if ( isset ( $aft_stored_meta['ccls_clientlogo_meta_url'] ) ) echo $aft_stored_meta['ccls_clientlogo_meta_url'][0]; ?>" /> <br>
			<em>(For Example: http://clients-website-url.com)</em>
	    </p>

	<?php

	}


	
	// ---------- Save Meta box info  ----------------	
	function ccls_clientlogo_meta_save( $post_id ) {

	    // Checks save status
	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	    $is_valid_nonce = ( isset( $_POST[ 'ccls_nonce' ] ) && wp_verify_nonce( $_POST[ 'ccls_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

	    // Exits script depending on save status
	    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
	        return;
	    }

	    // Checks for input and sanitizes/saves if needed
	    if( isset( $_POST[ 'ccls_clientlogo_meta_url' ] ) ) {
	        update_post_meta( $post_id, 'ccls_clientlogo_meta_url', esc_url_raw( $_POST[ 'ccls_clientlogo_meta_url' ] ) );
	    }

	}


	// ---------- Add The Column ----------------	
	function ccls_add_post_thumbnail_column($cols){
	  	
		global $post;
		$pst_type=$post->post_type;
			if( $pst_type == 'ccls-client-slider'){ 
			$cols['ccls_logo_thumb'] = __('Logo Image');
			$cols['ccls_client_url'] = __('Client Website Url');
			}
		return $cols;
	}


	
	// ---------- Grab featured-thumbnail size post thumbnail and display it ----------------	
	function display_post_thunmail_col($col, $id){
	  switch($col){
		case 'ccls_logo_thumb':
		  if( function_exists('the_post_thumbnail') ){
		
			$post_thumbnail_id = get_post_thumbnail_id($id);
			$post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');
			$post_thumbnail_img= $post_thumbnail_img[0];
			if($post_thumbnail_img !='')
			  echo '<img width="120" height="120" src="' . $post_thumbnail_img . '" />';
			else
			  echo 'No logo added.';	
		  }
		  else{
			echo 'No logo added.';
		  }	
		case 'ccls_client_url':
			if($col == 'ccls_client_url'){
				echo get_post_meta( $id, 'ccls_clientlogo_meta_url', true );;
			} 		   
		  break;
	 
	  }
	}


	// ----------  Activate plugin ----------------	
	function ccls_initialize(){
		$options_not_set = get_option('ccls_post_type_settings');
		if( $options_not_set ) return;
		
		$slider_settings = array( 'items' => 3, 'single_item' => false, 'slide_speed' => 500, 'pagination_speed' => 500, 'rewind_speed' => 500, 'auto_play' => true, 'stop_on_hover' => true, 'navigation' => false, 'pagination' => true, 'responsive' => true );
		update_option('ccls_slider_settings', $slider_settings);
	}

    // ----------  client logo admin style ----------------	
	function ccls_testimonials_dashboard_icon(){
		?>
		 <style>
		#toplevel_page_logo-client-carousel {
		 display:none; 
		}
		</style>
		<?php
	}



	// ----------  Plugin shortcode ----------------	
	function ccls_logo_carousel_callback( $atts ) {
		
		//include css and js start
		wp_enqueue_style( 'ccls-logo-slider', plugins_url('includes/client-carousel.css', __FILE__), array(), '1.0', 'all' );
		wp_enqueue_script( "ccls-logo-slider", plugins_url('includes/client-carousel.js', __FILE__ ), array('jquery') );
		$slider_settings = get_option('ccls_slider_settings');
		wp_localize_script( 'ccls-logo-slider', 'ccls', $slider_settings);
		//include css and js end
		ob_start();
		//echo $slider_settings;

	    $order_by='date';//default value

		$order= 'DESC';
		if($order_by == 'title'){	
			$order= 'ASC';
		}
		
		$category='default'; // default category
		$add_id='default';
		if( isset($atts['category']) and $atts['category'] !=''){
			$add_id=$atts['category']; //additional id 
		}
		
	    extract( shortcode_atts( array (
	        'type' => 'ccls-client-slider',
	        'category' => '',
	        'order' => $order,
	        'orderby' => $order_by,
	        'posts' => -1,
	    
	    ), $atts ) );
		
	    $options = array(
	        'post_type' => $type,
	        'order' => $order,
	        'orderby' => $orderby,
	        'posts_per_page' => $posts,
			'carousel_cat' => $category
	  		
	    );
	    $query = new WP_Query( $options );?>
	    <?php if ( $query->have_posts() ) { ?>
		<script>
		/*
	 *  Initialize the slider
	 */

			jQuery(document).ready(function($){ 
				jQuery("#ccls-logo-slider-<?php echo $add_id;?>").owlCarousel({
					items: 				Number(ccls.items),
					slideSpeed: 		Number(ccls.slide_speed),
					paginationSpeed: 	Number(ccls.pagination_speed),
					rewindSpeed: 		Number(ccls.rewind_speed),
					singleItem: 		Boolean('1' == ccls.single_item),
					autoPlay: 			Boolean('1' == ccls.auto_play),
					stopOnHover: 		Boolean('1' == ccls.stop_on_hover),
					navigation: 		Boolean('1' == ccls.navigation),
					pagination: 		Boolean('1' == ccls.pagination),
					responsive: 		Boolean('1' == ccls.responsive)
				});
			});
		</script>
		<div id="ccls-logo-slider-<?php echo $add_id;?>" class="owl-carousel">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		
		<div class="logo-container">
		<?php if(get_post_meta(get_the_ID(),'ccls_clientlogo_meta_url',true) != ''){
		 ?>
		 <a target="_blank" href="<?php echo get_post_meta(get_the_ID(),'ccls_clientlogo_meta_url',true);?>"><?php the_post_thumbnail('full'); ?></a>
	    <?php	 
		}else{ ?>
			<?php the_post_thumbnail('full'); ?>
		<?php }?>
					
		</div>
		<?php endwhile;
	      wp_reset_postdata(); ?>
		  </div>
		<?php 
		
		}else{
			
			_e('No Image is added','ccls');

		}
		
		return ob_get_clean();
	}



	function ccls_settings(){
			include('includes/cp-slider-settings.php');

		}

}



if(class_exists('Ccls')) {

	$Ccls = new Ccls();
	$Ccls->ccls_settings();

}

