<?php
namespace MBEI\Tags\Post;

use Elementor\Core\DynamicTags\Data_Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Post;
use MBEI\Traits\Fields\Video as VideoField;

class Video extends Data_Tag {
	use Base, Post, VideoField;

	public function get_name() {
		return 'meta-box-url';
	}
}