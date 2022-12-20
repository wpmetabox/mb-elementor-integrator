<?php
namespace MBEI;

class CurrentWidget {
	private static $name;

	public static function track() {
		add_filter( 'elementor/widget/before_render_content', [ __CLASS__, 'save_widget_name' ] );
	}

	public static function save_widget_name( $widget ) {
		self::$name = $widget->get_name();
	}

	public static function name() {
		return self::$name;
	}
}
