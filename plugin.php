<?php
/* 
* Plugin Name: Custom Post Type 
* Plugin URI:  phoenix.sheridanc.on.ca/~ccit3473
* Description: Plugin to create custom post type 
* Author: Briac Tier & Naima Nadeem 
* Version: 1.0 
* Author URI: phoenix.sheridanc.on.ca/~ccit3473
*/

// Enqueue Plugin Stylesheet
function plugin_enqueue_scripts (){
		wp_enqueue_style ('new-plugin', plugins_url ('new-plugin/css/new-style.css')); 
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

<?php

//Create the widget
class bt_my_plugin extends WP_Widget {
	//constructor
	public function __construct() {
		$widget_ops = array(
			'classname' => 'bt_my_plugin', 
			'description' => __( 'A widget that will display 5 posts from the "portfolio" post type in a set order, and will also display the featured image for each post.'
				)
			);
		// Adds a class to the widget and provides a description on the Widget page to describe what the widget does.
		parent::__construct('briac_widget', __('Briac Widget', 'bt'), $widget_ops);
	}

	/*
	*
	*this is what people will see (USER SIDE)
	*
	*/
	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$numberoflistings = $instance['numberoflistings'];
		echo $before_widget;
		if($title) {
			echo $before_title . $title . $after_title;
		}
		$this->getMyListings($numberoflistings);
		echo $after_widget;
	}

	function getMyListings($numberoflistings) {
		global $post;
		$listings = new WP_Query();
		$listings->query('post_type=Portfolio&showposts=3&order=desc' . $numberoflistings);
		if($listings->found_posts>0) {
			echo '<ul class="bt_widget">';
				while($listings->have_posts()) {
					$listings->the_post();
					$image = (has_post_thumbnail($post->ID)) ? get_the_post_thumbnail($post->ID) : '<div class="missingthumb"></div>';
					$listItem = '<li>' . $image;
					$listItem .= '<a href="' . get_permalink() . '">';
					$listItem .= get_the_title() . '</a>';
					$listItem .= '<span>' . get_the_excerpt() . '';
					$listItem .= '<a class="widgetmore" href="' . get_permalink() . '">';
					$listItem .= '<p>Learn More... </p>' . '</a></span></li>';
					echo $listItem;
				}
			echo '</ul>';
			wp_reset_postdata();
		}
	}

	/*This function creates the widget in the WordPress administration, 
	*
	*this is were you enter your data to be displayed on the the website 
	*
	*/
public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
			'title' => ''
			)
		);
		$title = strip_tags($instance['title']);
	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

	<?php }

	// Sanitizes, saves and submits the user-generated content.
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array(
			'title' => ''			)
		);
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}
}

//register widget
add_action('widgets_init', create_function('', 'return register_widget("bt_my_plugin");'));