<?php
namespace MBEI\Tags\Post;

use Elementor\Core\DynamicTags\Data_Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Post;
use MBEI\Traits\Fields\Image as ImageField;

class Image extends Data_Tag {
	use Base, Post, ImageField;

	public function get_name() {
		return 'meta-box-image';
	}
}