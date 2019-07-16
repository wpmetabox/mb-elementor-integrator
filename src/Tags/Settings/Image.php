<?php
namespace MBEI\Tags\Settings;

use Elementor\Core\DynamicTags\Data_Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Settings;
use MBEI\Traits\Fields\Image as ImageField;

class Image extends Data_Tag {
	use Base, Settings, ImageField;

	public function get_name() {
		return 'meta-box-settings-image';
	}
}