<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/pasinsjr
 * @since             1.0.0
 * @package           responsive-columns-slider
 *
 * @wordpress-plugin
 * Plugin Name:       responsive-columns-slider
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       get posts frome widget and display to slider
 * Version:           1.0.0
 * Author:            Pasin Sukjaimitr
 * Author URI:        https://github.com/pasinsjr
 * License:           Commercial
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!function_exists('get_the_post_thumbnail_url')) {
	function get_the_post_thumbnail_url($id = null) {
		global $post;
	    if (is_null($id)) {
	      $id = $post->ID;
	    }
	    
		if (empty($id)) return "";
		$post_thumbnail_id = get_post_thumbnail_id($id);
		if (empty($post_thumbnail_id)) return "";
		$post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
		return $post_thumbnail_url;
	}
}

if(!function_exists('get_dynamic_sidebar')){
	function get_dynamic_sidebar($index = 1) {
		$sidebar_contents = "";
		ob_start();
		dynamic_sidebar($index);
		$sidebar_contents = ob_get_clean();
		return $sidebar_contents;
	}
}

if (!function_exists('columns_slider_rp_load_wp_style')) {
	function columns_slider_rp_load_wp_style() {
		$url = plugin_dir_url( __FILE__ );
		wp_register_style( 'columns_slider_rp_style', $url . '/assets/stylesheets/main.css', false, '1.0.0' );
		wp_enqueue_style( 'columns_slider_rp_style' );
		wp_register_script( 'columns_slider_rp_script', $url . '/assets/js/main.js', array('jquery'), '1.0.0' );
		wp_enqueue_script( 'columns_slider_rp_script' );
	}
	add_action( 'wp_enqueue_scripts', 'columns_slider_rp_load_wp_style' );
}

if (!class_exists('ResponsivePostSliderWidget')) {
	class ResponsivePostSliderWidget extends WP_Widget {
		function __construct() {
			parent::__construct(
				'tkt_rp_widget',
				__( 'TKT Recommended Post', 'tktrp' ), // Name
				array( 'description' => __( 'TicketTail Recommended posts', 'tktrp' ), ) // Args
			);
		}

		private $color_list = array(
				'red' => '#ed1f24',
				'orange' => '#f37c20',
				'yellow' => '#febe10',
				'turq' => '#249da5',
				'maroon' => '#800000',
				'purple' => '#800080',
				'magenta' => '#FF00FF',
				'sea green' => '#4E8975',
				'royal blue' => '#2B60DE',
				'lime' => '#00FF00',
				'amber' => '#FFBF00',
				'yellow green' => '#52D017',
			);

		public function widget( $args, $instance ) {
			if ( ! empty( $instance['post_id'] ) ) {
				$id = $instance['post_id'];
				echo $args['before_widget'];
				$title = get_post_meta($id, 'override_title', true);
				$bg_color = get_post_meta($id, 'label_color', true);
				if (!empty($instance['title']))
					$title = $instance['title'];
				$label = get_post_meta($id, 'label_text', true);
				if (!empty($instance['label'])) {
					$label = $instance['label'];
					$bg_color = $instance['bg_color'];
				}
				?>
				<div class="col-sm-4 recommended-post-wrapper">
					<a href="<?php echo get_permalink( $id ) ?>" class="recommended-post">
						<div class="recommended-post-image-wrapper"><div class="recommended-post-image background-cover-image" style="background-image: url('<?php echo get_the_post_thumbnail_url($id)?>');"></div></div>
						<div class="recommended-post-title-wrapper"><p class="recommended-post-title"><span class="table-cell-max-height"><?php echo (empty($title)? get_the_title($id) : $title) ?></span></p></div>
						<?php if (!empty($label)): ?>
							<div class="recommended-post-label" style="background-color: <?php echo $bg_color; ?>;">
								<p><?php echo $label ?></p>
							</div>
						<?php endif ?>
					</a>
				</div>
				<?php
				echo $args['after_widget'];
			}
		}

		public function form( $instance ) {
			$post_id = !empty($instance['post_id']) ? $instance['post_id'] : "";
			$post = get_post($post_id);
			?>
			<?php if (!empty($post)): ?>
				<p>Current Post: <strong><?php echo "[".$post->ID."] ".$post->post_title; ?></strong></p>
			<?php endif ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'post_id' ); ?>">Post id:</label> 
				<input id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>" value="<?php echo $post_id ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">Override Title:</label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title'] ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'label' ); ?>">Label Text:</label>
				<input placeholder="DR.GAG" id="<?php echo $this->get_field_id( 'label' ); ?>" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label'] ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'bg_color' ); ?>">Background Color:</label>
				<select style="width: 200px;" id="<?php echo $this->get_field_id( 'bg_color' ); ?>" name="<?php echo $this->get_field_name( 'bg_color' ); ?>">
					<?php foreach($this->color_list as $name => $color_code): ?>
						<option value="<?php echo $color_code ?>" <?php if ($color_code == $instance['bg_color']) { echo "selected"; } ?>><?php echo $name ?></option>
					<?php endforeach ?>
				</select>
			</p>
			<?php 
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['post_id'] = (!empty($new_instance['post_id'])) ? strip_tags($new_instance['post_id']) : '';
			$instance['bg_color'] = (!empty($new_instance['bg_color']) ? $new_instance['bg_color'] : $this->color_list['red']);
			$instance['label'] = (!empty($new_instance['label']) ? $new_instance['label'] : '');
			$instance['title'] = (!empty($new_instance['title']) ? $new_instance['title'] : '');
			return $instance;
		}
	}
	function register_tkt_rp_widget() {
	    register_widget( 'ResponsivePostSliderWidget' );
	}
	add_action( 'widgets_init', 'register_tkt_rp_widget' );
}

if (!function_exists('responsive_columns_slider_func')) {
	function responsive_columns_slider_func($attrs) {
		$opt = shortcode_atts( array(
			'widget_id' => '',
		), $attrs, 'responsive-columns-slider' );
		
		$widget_id = $opt['widget_id'];
		if (empty($widget_id)) return "";

		$result = '<div class="row recommended-slider-wrapper">';
		$result .= get_dynamic_sidebar($widget_id);
		$result .= '</div>';

		return $result;
	}
	add_shortcode( 'responsive-columns-slider', 'responsive_columns_slider_func' );
}
