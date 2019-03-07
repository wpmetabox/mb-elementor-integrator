<?php
use Elementor\Modules\DynamicTags\Module;

trait MBEI_Text {
	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
		];
	}

	public function render() {
		echo self::handle_get_value();
	}

	private function get_supported_fields() {
		return null;
	}
}