<?php
/* 
* Plugin Name: BTM Events Plugin
* Plugin URI:  phoenix.sheridanc.on.ca/~ccit3473
* Description: Plugin to create custom post type 
* Author: Briac Tier & Naima Nadeem 
* Version: 1.0 
* Author URI: phoenix.sheridanc.on.ca/~ccit3473
*/

// Enqueue the plugin stylesheet.
function plugin_enqueue_scripts (){
		wp_enqueue_style ('new-plugin', plugins_url ('new-plugin/css/new-style.css')); 
	} 
add_action( 'wp_enqueue_scripts','plugin_enqueue_scripts' );

/*
* Register the custom 'Portfolio' post type.
*/

function custom_post_type () { 
	// An array of labels for the custom 'Porfolio' post type.
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

	// An array of arguments for custom 'Porfolio' post types.
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

	register_post_type('portfolio', $args);
}
add_action('init','custom_post_type');

/*
*
* Create the widget
*
*/

class bt_my_plugin extends WP_Widget {
	//constructor
	public function __construct() {
		$widget_ops = array(
			'classname' => 'bt_my_plugin', 
			'description' => __( 'Adds 3 posts from the portfolio post type in a descending order.'
				)
			);
		// A description on the Widget page to describe what the widget does.
		parent::__construct('briac_widget', __('Events Widget', 'bt'), $widget_ops);
	}

	/*
	*
	* Set up the user side of the widget. Display the widget title and 'Portoflio' posts.
	*
	*/
	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$btm = $instance['btm'];
		echo $before_widget;
		if($title) {
			echo $before_title . $title . $after_title;
		}
		$this->get_my_events($btm);
		echo $after_widget;
	}

	/*
	*
	* A custom query that returns the post's title and Learn More text as links to the rest of the content. 
	* The query also returns a thumbnail and the excerpt. There will be only 3 posts returned, they 
	* will be from the custom 'Portfolio' post type and they will appear in descending order.
	*
	*/
	function get_my_events($btm) {
		global $post;
		$events = new WP_Query();
		$events->query('post_type=Portfolio&showposts=3&order=desc' . $btm);
		if($events->found_posts>0) {
			echo '<ul class="bt_widget">';
				while($events->have_posts()) {
					// Will concatenate the variables and output the 'eventItem' string.
					$events->the_post();
					$image = (has_post_thumbnail($post->ID)) ? get_the_post_thumbnail($post->ID) : '<div class="missingthumbnail"></div>';
					$eventItem = '<li>' . $image;
					$eventItem .= '<a href="' . get_permalink() . '">';
					$eventItem .= get_the_title() . '</a>';
					$eventItem .= '<span>' . get_the_excerpt() . '';
					$eventItem .= '<a class="widgetmore" href="' . get_permalink() . '">';
					$eventItem .= '<p>Learn More... </p>' . '</a></span></li>';
					echo $eventItem;
				}
			echo '</ul>';
			wp_reset_postdata();
		}
	}

	/*
	*
	* Create the widget in the WordPress administration dashboard menu. 
	*
	*/
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
			'title' => ''
			)
		);
		$title = strip_tags($instance['title']);
	?>

		<!-- Creates a 'Title' label and an input for the user to enter a custom widget title. -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

	<?php }

	// Sanitize, save and submit the custom title created by the user.
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array(
			'title' => ''			)
		);
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}
}

/*
*
* Register the widget.
*
*/
add_action('widgets_init', create_function('', 'return register_widget("bt_my_plugin");'));


/*
*
* Adds the btn-breakthenews shortcode. This shortcode will display the latest post from the 'portfolio' custom post type.
*
*/
add_shortcode('btn-breakthenews', 'custom_post_portfolio_shortcode');

function custom_post_portfolio_shortcode() {
	// An array of arguments that the shortcode will look for when selecting a post from the custom post type.
	$args = array(
		// Only show posts with post type 'portfolio'.
		'post_type' => 'portfolio',
		// Only show 1 post from the post type.
		'showposts' => '1',
		// Show the posts that are returned in descending order.
		'order' => desc
		);
	$string = '';
	$query = new WP_Query($args);
	if($query->have_posts()) {
		$string .= '<ul class="bt_shortcode">';
		while($query->have_posts()) {
			// Will concatenate the variables and output the shortcode as a string.
			$query->the_post();
			$image = (has_post_thumbnail($post->ID)) ? get_the_post_thumbnail($post->ID) : '<div class="missingthumbnail"></div>';
			$string = '<li>' . $image;
			$string .= '<a href="' . get_permalink() . '">';
			$string .= get_the_title() . '</a>';
			$string .= '<a class="shortcodemore" href="' . get_permalink() . '">';
			$string .= '<p>Learn More... </p>' . '</a></li>';
		}
		$string .= '</ul>';
	}
	wp_reset_postdata();
	return $string;
}