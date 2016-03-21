<?php
/* 
* Plugin Name: Custom Post Type 
* Plugin URI:  phoenix.sheridanc.on.ca/~ccit3473
* Description: Plugin to create custom post type 
* Author: Briac Tier & Naima Nadeem 
* Version: 1.0 
* Author URI: phoenix.sheridanc.on.ca/~ccit34
*/

// Enqueue Plugin Stylesheet
function plugin_enqueue_scripts (){
		wp_enqueue_style ('newplugin', plugins_url ('new-plugins/css/style.css')); 
	} 
add_action( 'wp_enqueue_scripts','plugin_enqueue_scripts' );

/*
	register the custom post types
*/

function custom_post_type () { 
	$labels = array (
		'name' => 'Portfolio',
		'singular_name' => 'Portfolio',
		'add_new' => 'Add Portfolio Item',
		'all_items' => 'All Items',
		'edit_item' => 'Edit Item',
		'new_item' => 'New Item',
		'search_iterm' => 'Search Portfolio',
		'not_found' => 'No Item Found',
		'not_found_in_trash' => 'No Item Found in Trash',
		'parent_item_colon' => 'Parent Item'
	);

	// arguments for custom post types
	$args = array( 
		'labels' => $labels, 
		'public' => true,
		'has_archive' => true,
		'publicly_queryable' => true,
		'query_var' => true, 
		'rewrite' => true, 
		'capability_type' => 'post', 
		'hierarchical' => true, 
		'supports' => array( 
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'revisions',
			),
		'taxonomies' => array ('category', 'post_tag'),
		'exclude_from_search' => false, 
		);

	//register post types
	register_post_type('portfolio', $args);
}
add_action('init','custom_post_type')

?>