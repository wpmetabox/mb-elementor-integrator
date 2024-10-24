<?php
namespace MBEI\Tags\Archive;

use Elementor\Core\DynamicTags\Data_Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Archive;
use MBEI\Traits\Fields\Video as VideoField;

class Video extends Data_Tag {
	use Base;
	use Archive;
	use VideoField;

	public function get_name() {
		return 'meta-box-archive-url';
	}
}
