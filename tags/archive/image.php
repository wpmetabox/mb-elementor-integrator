<?php
use Elementor\Core\DynamicTags\Data_Tag;

class MBEI_Tag_Archive_Image extends Data_Tag {
	use MBEI_Object, MBEI_Archive, MBEI_Image;

	public function get_name() {
		return 'meta-box-archive-image';
	}
}