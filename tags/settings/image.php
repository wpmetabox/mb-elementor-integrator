<?php
use Elementor\Core\DynamicTags\Data_Tag;

class MBEI_Tag_Settings_Image extends Data_Tag {
	use MBEI_Object, MBEI_Settings, MBEI_Image;

	public function get_name() {
		return 'meta-box-settings-image';
	}
}