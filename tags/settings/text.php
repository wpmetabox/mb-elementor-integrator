<?php
use Elementor\Core\DynamicTags\Tag;

class MBEI_Tag_Settings_Text extends Tag {
	use MBEI_Base, MBEI_Settings, MBEI_Text;

	public function get_name() {
		return 'meta-box-settings-text';
	}
}