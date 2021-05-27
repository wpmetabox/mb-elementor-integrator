<?php
namespace MBEI\Traits\Fields;

use Elementor\Modules\DynamicTags\Module;

trait Text {
	public function get_categories() {
		return [
			Module::TEXT_CATEGORY,
			Module::NUMBER_CATEGORY,
			Module::COLOR_CATEGORY,
		];
	}

	public function render() {
		$this->the_value();
	}
}