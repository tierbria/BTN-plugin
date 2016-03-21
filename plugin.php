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


//Create the widget
class bt_my_plugin extends WP_Widget {
	//constructor
	public function __construct() {
		$widget_ops = array('classname' => 'bt_my_plugin', 'description' => __( 'A plugin I made.') );
		// Adds a class to the widget and provides a description on the Widget page to describe what the widget does.
		parent::__construct('briac_widget', __('Briac Widget', 'bt'), $widget_ops);
	}
	public function widget( $args, $instance ) {
		$c = ! empty( $instance['count'] ) ? '1' : '0'; 
		//sets a variable for whether or not the 'Count' option is checked
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';
		// sets a variable for whether or not the 'Dropdown' option is checked
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Briac Widget', 'bt') : $instance['title'], $instance, $this->id_base); 
		// Determines if there's a user-provided title and if not, displays a default title.
		
		echo $args['before_widget']; // what's set up when you registered the sidebar
		
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

	if ( $d ) {
		//if the dropdown option is checked, gets a list of the archives and displays them by year in a dropdown list. 
		$dropdown_id = "{$this->id_base}-dropdown-{$this->number}";
?>
		<label class="screen-reader-text" for="<?php echo esc_attr( $dropdown_id ); ?>"><?php echo $title; ?></label>
		<select id="<?php echo esc_attr( $dropdown_id ); ?>" name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
			
		<?php	$dropdown_args = apply_filters( 'widget_archives_dropdown_args', array(
				'type'            => 'yearly',
				'format'          => 'option',
				'show_post_count' => $c // If post count checked, show the post count
			) );
		?>	
			<option value="<?php echo __( 'Select Year', 'bt' ); ?>"><?php echo __( 'Select Year', 'bt' ); ?></option>
			<?php wp_get_archives( $dropdown_args ); ?>
		</select>
<?php
	} else {
		// If (d) not selected, show this:
?>
		<ul>
		<?php 
			wp_get_archives( apply_filters( 'widget_briac_args', array(
			'type'            => 'yearly',
			'show_post_count' => $c
		) ) ); 
			// gets a list of the archives and displays them by year. If the Count option is checked, this gets shown.
		?>
		</ul>

<?php
		}
		
		echo $args['after_widget']; // what's set up when you registered the sidebar
	}
	//widget update
public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$title = strip_tags($instance['title']);
		$count = $instance['count'] ? 'checked="checked"' : '';
		$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p>
			<input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" /> <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as dropdown'); ?></label>
			<br/>
			<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
		</p>
<?php }
	
	// Sanitizes, saves and submits the user-generated content.
	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;

		return $instance;
	}
}

//register widget
add_action('widgets_init', create_function('', 'return register_widget("bt_my_plugin");'));

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