<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PMAF Animated Form widget.
 *
 * @since 1.0.2
 */
class PMAF_Animated_Forms_Widget extends WP_Widget {

	/**
	 * Hold widget settings defaults, populated in constructor.
	 *
	 * @since 1.0.2
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor
	 *
	 * @since 1.0.2
	 */
	public function __construct() {

		// Widget defaults.
		$this->defaults = [
			'title'      => '',
			'form_id'    => '',
			'show_title' => false,
			'show_desc'  => false,
		];

		// Widget Slug.
		$widget_slug = 'pmaf-widget';

		// Widget basics.
		$widget_ops = [
			'classname'             => $widget_slug,
			'description'           => esc_html_x( 'Display a form.', 'Widget', 'animated-forms' ),
			'show_instance_in_rest' => true,
		];

		// Widget controls.
		$control_ops = [
			'id_base' => $widget_slug,
		];

		// Load widget.
		parent::__construct( $widget_slug, esc_html_x( 'PMAF Animated Form', 'Widget', 'animated-forms' ), $widget_ops, $control_ops );
	}

	/**
	 * Output the HTML for this widget.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme.
	 * @param array $instance An array of settings for this widget instance.
	 */
	public function widget( $args, $instance ) {

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Title.
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Form.
		if ( ! empty( $instance['form_id'] ) ) {
			echo do_shortcode( '[animated_form id="'. absint( $instance['form_id'] ) .'"]' );
		}

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Deal with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 *
	 * @since 1.0.2
	 *
	 * @param array $new_instance An array of new settings as submitted by the admin.
	 * @param array $old_instance An array of the previous settings.
	 *
	 * @return array The validated and (if necessary) amended settings
	 */
	public function update( $new_instance, $old_instance ) {

		$new_instance['title']      = wp_strip_all_tags( $new_instance['title'] );
		$new_instance['form_id']    = ! empty( $new_instance['form_id'] ) ? (int) $new_instance['form_id'] : 0;
		$new_instance['show_title'] = isset( $new_instance['show_title'] ) && $new_instance['show_title'] ? '1' : false;
		$new_instance['show_desc']  = isset( $new_instance['show_desc'] ) && $new_instance['show_desc'] ? '1' : false;

		return $new_instance;
	}

	/**
	 * Display the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @since 1.0.2
	 *
	 * @param array $instance An array of the current settings for this widget.
	 */
	public function form( $instance ) {

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php echo esc_html( _x( 'Title:', 'Widget', 'animated-forms' ) ); ?>
			</label>
			<input type="text"
			       id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'form_id' ) ); ?>">
				<?php echo esc_html( _x( 'Form:', 'Widget', 'animated-forms' ) ); ?>
			</label>
			<select class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( 'form_id' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'form_id' ) ); ?>">
				<?php
				$forms = pmaf_forms_data()->get_forms();

				if ( ! empty( $forms ) ) {
					echo '<option value="" selected disabled>' . esc_html_x( 'Select your form', 'Widget', 'animated-forms' ) . '</option>';

					foreach ( $forms as $form ) {
						echo '<option value="' . esc_attr( $form->ID ) . '" ' . selected( $instance['form_id'], $form->ID, false ) . '>' . esc_html( $form->post_title ? $form->post_title : 'Form #'. $form->ID ) . '</option>';
					}
				} else {
					echo '<option value="">' . esc_html_x( 'No forms', 'Widget', 'animated-forms' ) . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" <?php checked( '1', $instance['show_title'] ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>">
				<?php echo esc_html( _x( 'Display form name', 'Widget', 'animated-forms' ) ); ?>
			</label>
			<br>
			<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_desc' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'show_desc' ) ); ?>" <?php checked( '1', $instance['show_desc'] ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_desc' ) ); ?>">
				<?php echo esc_html( _x( 'Display form description', 'Widget', 'animated-forms' ) ); ?>
			</label>
		</p>
		<?php
	}
}

/**
 * Register PMAF Animated Form plugin widgets.
 */
function pmaf_register_widgets() {
	register_widget( 'PMAF_Animated_Forms_Widget' );
}

add_action( 'widgets_init', 'pmaf_register_widgets' );
