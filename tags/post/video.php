<?php
use Elementor\Core\DynamicTags\Data_Tag;

class MBEI_Tag_Video extends Data_Tag {
	use MBEI_Object, MBEI_Post, MBEI_Video;

	public function get_name() {
		return 'meta-box-url';
	}
}