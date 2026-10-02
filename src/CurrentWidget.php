<?php
namespace MBEI;

class CurrentWidget {
	private static $name;

	public static function track() {
		// Must run before Elementor Pro Display Conditions ( same hook with priority 10 ) so dynamic tags resolve with the correct widget name.
		add_filter( 'elementor/widget/before_render_content', [ __CLASS__, 'save_widget_name' ], PHP_INT_MIN );
		add_action( 'elementor/frontend/before_render', [ __CLASS__, 'save_widget_name' ], PHP_INT_MIN );
	}

	public static function save_widget_name( $widget ) {
		self::$name = $widget->get_name();
	}

	public static function name() {
		return self::$name;
	}
}
