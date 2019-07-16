<?php
namespace MBEI\Traits\Fields;

use Elementor\Modules\DynamicTags\Module;

trait Text {
	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
		];
	}

	public function render() {
		self::the_value();
	}

	private function get_supported_fields() {
		return null;
	}
}