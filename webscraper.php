<?php
/**
 * Plugin Name: Web Scrapper
 * Plugin URI: https://faceScrapper Model.com
 * Description: This plugin scrap content from another website
 * Version: 3.7.6
 * Author: Kunal Malviya
 * Author URI: kunal.malviya351@gmail.com
 * Text Domain: webscrapper
 * Domain Path: /languages/
 * License: GPLv3
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

add_action( 'init', 'feeds_generator_init' );
function feeds_generator_init() {
	$labels = array(
		'name'               => _x( 'Scrapper Models', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'      => _x( 'Scrapper Model', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'          => _x( 'Scrapper Models', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'     => _x( 'Scrapper Model', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'            => _x( 'Add New', 'Scrapper Model', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Add New Scrapper Model', 'your-plugin-textdomain' ),
		'new_item'           => __( 'New Scrapper Model', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Edit Scrapper Model', 'your-plugin-textdomain' ),
		'view_item'          => __( 'View Scrapper Model', 'your-plugin-textdomain' ),
		'all_items'          => __( 'All Scrapper Models', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Search Scrapper Models', 'your-plugin-textdomain' ),
		'parent_item_colon'  => __( 'Parent Scrapper Models:', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No Scrapper Models found.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No Scrapper Models found in Trash.', 'your-plugin-textdomain' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Description.', 'your-plugin-textdomain' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'scrapper' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' )
	);

	register_post_type( 'feeds_generator', $args );
}

function adding_custom_meta_boxes( ) {
    add_meta_box(
        'my-meta-box',
        __( 'My Meta Box' ),
        'render_my_meta_box'
    );
}
add_action( 'add_meta_boxes_feeds_generator', 'adding_custom_meta_boxes', 10, 2 );

function render_my_meta_box() {
	//$urlToFetch = get_post_meta($post_id, 'url_for_meta');
	if( !empty($_GET['post']) ) {
		$urlToFetch = get_post_meta($_GET['post'], '_urlForFeeds', ARRAY_A);
		$itemWrapper = get_post_meta($_GET['post'], '_itemWrapper', ARRAY_A);
		$titleWrapper = get_post_meta($_GET['post'], '_titleWrapper', ARRAY_A);
		$descriptionWrapper = get_post_meta($_GET['post'], '_descriptionWrapper', ARRAY_A);
	}
	else {
		$urlToFetch = $itemWrapper = $titleWrapper = $descriptionWrapper = "";
	}
	// $siteHtml = wp_remote_get($urlToFetch);  
	// if(is_array($siteHtml) && $siteHtml['body']) {
	// 	$siteBody = $siteHtml['body'];
	// }
	echo '<div id="feedsGeneratorId">
		<form action="" method="post">			
			<ul>
		        <li>
		        	<label for="urlForFeeds">Url to Fetch<span> *</span>: </label>
		        	<input name="urlForFeeds" id="urlForFeeds" value="'.$urlToFetch.'" required/>
		        </li>
		        <li>
		        	<label for="itemWrapper">Item Wrapper<span> *</span>: </label>
		        	<input id="itemWrapper" name="itemWrapper" value="'.$itemWrapper.'" required/>
		        </li>
		        <li>
		        	<label for="titleWrapper">Title Wrapper<span> *</span>: </label>
		        	<input id="titleWrapper" name="titleWrapper" value="'.$titleWrapper.'" required/>
		        </li>
		        <li>
		        	<label for="descriptionWrapper">Description Wrapper<span> *</span>: </label>
		        	<input id="descriptionWrapper" name="descriptionWrapper" value="'.$descriptionWrapper.'" required/>
		        </li>
		    </ul>
		</form>		
	</div>';
}

function wporg_save_postdata($post_id) {    
    if( !empty($_POST['urlForFeeds']) && $post_id) {
	    update_post_meta($post_id, '_urlForFeeds', $_POST['urlForFeeds']);		
    }
    if( !empty($_POST['itemWrapper']) && $post_id) {
	    update_post_meta($post_id, '_itemWrapper', $_POST['itemWrapper']);		
    }
    if( !empty($_POST['urlForFeeds']) && $post_id) {
	    update_post_meta($post_id, '_titleWrapper', $_POST['titleWrapper']);		
    }
    if( !empty($_POST['urlForFeeds']) && $post_id) {
	    update_post_meta($post_id, '_descriptionWrapper', $_POST['descriptionWrapper']);		
    }
}
add_action('save_post', 'wporg_save_postdata');

/**
 * Checks to see if appropriate templates are present in active template directory.
 * Otherwises uses templates present in plugin's template directory.
 */
add_filter('template_include', 'wpse72544_set_template');
function wpse72544_set_template( $template ){

    /* 
     * Optional: Have a plug-in option to disable template handling
     * if( get_option('wpse72544_disable_template_handling') )
     *     return $template;
     */

    if(is_singular('feeds_generator') && 'single-feeds_generator.php' != $template ){
        //WordPress couldn't find an 'event' template. Use plug-in instead:        
        if( isset($_GET['visualEditor']) && $_GET['visualEditor'] == true ) {
        	$template = plugin_dir_path( __FILE__ ) . 'visualEditor.php';
        }
        else {
        	$template = plugin_dir_path( __FILE__ ) . 'rss.php';        	
        }
    }

    return $template;
}

// add_action('init', 'customRSS');
// function customRSS(){
//     add_feed('speakingTree', 'customRSSFunc');
// }

// function customRSSFunc(){
//     return plugin_dir_path( __FILE__ ) . 'rsss.php';
// }