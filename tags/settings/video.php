<?php
use Elementor\Core\DynamicTags\Data_Tag;

class MBEI_Tag_Settings_Video extends Data_Tag {
	use MBEI_Base, MBEI_Settings, MBEI_Video;

	public function get_name() {
		return 'meta-box-settings-url';
	}
}