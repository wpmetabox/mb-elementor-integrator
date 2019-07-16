<?php
namespace MBEI\Tags\Archive;

use Elementor\Core\DynamicTags\Data_Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Archive;
use MBEI\Traits\Fields\Image as ImageField;

class Image extends Data_Tag {
	use Base, Archive, ImageField;

	public function get_name() {
		return 'meta-box-archive-image';
	}
}