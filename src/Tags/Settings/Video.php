<?php
namespace MBEI\Tags\Settings;

use Elementor\Core\DynamicTags\Data_Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Settings;
use MBEI\Traits\Fields\Video as VideoField;

class Video extends Data_Tag {
	use Base, Settings, VideoField;

	public function get_name() {
		return 'meta-box-settings-url';
	}
}