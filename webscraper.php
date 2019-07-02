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
    $screen = get_current_screen();
    add_meta_box(
        'my-meta-box-url',
        __( 'Enter url to Scrap' ),
        'render_my_meta_box1'
    );
    if( 'add' != $screen->action ) {
    	add_meta_box(
	        'my-meta-box-selectors',
	        __( 'Enter url to Scrap' ),
	        'render_my_meta_box'
	    );
    }    
}
add_action( 'add_meta_boxes_feeds_generator', 'adding_custom_meta_boxes', 10, 2 );

function render_my_meta_box1() {
	//$urlToFetch = get_post_meta($post_id, 'url_for_meta');
	if( !empty($_GET['post']) ) {
		$urlToFetch = get_post_meta($_GET['post'], '_urlForFeeds', ARRAY_A);		
	}
	else {
		$urlToFetch = $itemWrapper = $titleWrapper = $descriptionWrapper = "";
	}
	echo "<h3><p>Please add the url which you want to scrap and then hit publish button. After then you will get the link for Visual Editor and by using that editor you can do scrapping of any site.</p></h3>";
	// $siteHtml = wp_remote_get($urlToFetch);  
	// if(is_array($siteHtml) && $siteHtml['body']) {
	// 	$siteBody = $siteHtml['body'];
	// }
	echo '<div id="feedsGeneratorId1">			
		<form action="" method="post">			
			<table style="width:100%">
		        <tr>
		        	<td><label for="urlForFeeds">Url to Fetch<span> *</span>: </label></td>
		        	<td><input style="width:100%" name="urlForFeeds" id="urlForFeeds" value="'.$urlToFetch.'" required/></td>
		        </tr>		        
		    </table>
		</form>		
	</div>';
}

function render_my_meta_box() {
	//$urlToFetch = get_post_meta($post_id, 'url_for_meta');
	if( !empty($_GET['post']) ) {
		$urlToFetch = get_post_meta($_GET['post'], '_urlForFeeds', ARRAY_A);
		$itemWrapper = get_post_meta($_GET['post'], '_itemWrapper', ARRAY_A);
		$titleWrapper = get_post_meta($_GET['post'], '_titleWrapper', ARRAY_A);
		$descriptionWrapper = get_post_meta($_GET['post'], '_descriptionWrapper', ARRAY_A);
		echo "<h3><a target='_blank' href='".get_permalink($_GET['post'])."?visualEditor=true'>Click Here for VISUAL EDITOR</a></h3>";
		echo '<div id="feedsGeneratorId">			
			<form action="" method="post">
				<ul>		        
			        <li><b>If you have knowledge of css selector then you can directly put those selector in below Wrappers</b></li>
			        <li>
			        	<label for="itemWrapper">Item Wrapper: </label>
			        	<input id="itemWrapper" name="itemWrapper" value="'.$itemWrapper.'" />
			        </li>
			        <li>
			        	<label for="titleWrapper">Title Wrapper: </label>
			        	<input id="titleWrapper" name="titleWrapper" value="'.$titleWrapper.'" />
			        </li>
			        <li>
			        	<label for="descriptionWrapper">Description Wrapper: </label>
			        	<input id="descriptionWrapper" name="descriptionWrapper" value="'.$descriptionWrapper.'" />
			        </li>
			    </ul>
			</form>		
		</div>';
	}
	else {
		$urlToFetch = $itemWrapper = $titleWrapper = $descriptionWrapper = "";
	}
	// $siteHtml = wp_remote_get($urlToFetch);  
	// if(is_array($siteHtml) && $siteHtml['body']) {
	// 	$siteBody = $siteHtml['body'];
	// }	
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
        else if(isset($_GET['screen']) && $_GET['screen'] == 'output'){
        	$template = plugin_dir_path( __FILE__ ) . 'webscraper.output.php';        	
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

add_action('wp_ajax_wp_scrapper_update_selectors', 'wp_scrapper_update_selectors');
add_action('wp_ajax_nopriv_wp_scrapper_update_selectors', 'wp_scrapper_update_selectors');

function wp_scrapper_update_selectors() {	
	if( isset($_POST['postId']) ) {
		$post_id = $_POST['postId'];
		if( !empty($_POST['selectedWrapper']) ) {
			update_post_meta($post_id, '_itemWrapper', $_POST['selectedWrapper']);
		}
		if( !empty($_POST['selectedTitle']) ) {
			update_post_meta($post_id, '_titleWrapper', $_POST['selectedTitle']);
		}
		if( !empty($_POST['selectedDescription']) ) {
			update_post_meta($post_id, '_descriptionWrapper', $_POST['selectedDescription']);
		}
		echo "success";
	}
	else {
		echo "Post id is required";
	}
	wp_die();
}