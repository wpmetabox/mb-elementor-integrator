<?php
namespace MBEI\Tags\Settings;

use Elementor\Core\DynamicTags\Tag;
use MBEI\Traits\Base;
use MBEI\Traits\Settings;
use MBEI\Traits\Fields\Text as TextField;

class Text extends Tag {
	use Base, Settings, TextField;

	public function get_name() {
		return 'meta-box-settings-text';
	}
}