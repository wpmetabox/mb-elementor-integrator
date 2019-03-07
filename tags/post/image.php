<?php
use Elementor\Core\DynamicTags\Data_Tag;

class MBEI_Tag_Image extends Data_Tag {
	use MBEI_Base, MBEI_Post, MBEI_Image;

	public function get_name() {
		return 'meta-box-image';
	}
}