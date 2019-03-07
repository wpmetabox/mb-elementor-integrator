<?php
use Elementor\Core\DynamicTags\Tag;

class MBEI_Tag_Archive_Text extends Tag {
	use MBEI_Object, MBEI_Archive, MBEI_Text;

	public function get_name() {
		return 'meta-box-archive-text';
	}
}