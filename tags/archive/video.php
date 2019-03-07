<?php
use Elementor\Core\DynamicTags\Data_Tag;

class MBEI_Tag_Archive_Video extends Data_Tag {
	use MBEI_Object, MBEI_Archive, MBEI_Video;

	public function get_name() {
		return 'meta-box-archive-url';
	}
}