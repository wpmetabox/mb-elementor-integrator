<?php
use Elementor\Core\DynamicTags\Tag;

class MBEI_Tag_Text extends Tag {
	use MBEI_Base, MBEI_Post, MBEI_Text;

	public function get_name() {
		return 'meta-box-text';
	}
}